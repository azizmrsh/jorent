<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use App\Traits\FileUploadTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Notifications\Notification;

class TenantResource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
        protected static ?string $navigationGroup = 'Real Estate Management';

    public static function getNavigationLabel(): string
    {
        return __('general.Tenants');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Tenant');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Tenants');
    }
    protected static ?string $slug = 'tenants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Personal Information
                Forms\Components\Fieldset::make(__('general.Personal Information'))
                    ->schema([
                        Forms\Components\TextInput::make('firstname')
                            ->required()
                            ->label(__('general.First Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('midname')
                            ->label(__('general.Middle Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lastname')
                            ->label(__('general.Last Name'))
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('general.Birth Date')),
                        Forms\Components\TextInput::make('nationality')
                            ->label(__('general.Nationality'))
                            ->maxLength(255),
                    ]),

                // Contact Information
                Forms\Components\Fieldset::make(__('general.Contact Information'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('general.Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rule('email')
                            ->validationMessages([
                                'email' => __('general.Please enter a valid email address'),
                                'required' => __('general.The email field is required'),
                            ]),
                      Forms\Components\TextInput::make('password')
                            ->required()
                            ->label(__('general.Password'))
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn (string $context) => in_array($context, ['create', 'edit'])),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('general.Phone'))
                            ->maxLength(255),                        Forms\Components\TextInput::make('address')
                            ->label(__('general.Address'))
                            ->maxLength(255),
                    ]),

                // Profile Information
                Forms\Components\Fieldset::make(__('general.Profile Information'))
                    ->schema([
                        self::profilePhotoUpload(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->label(__('general.Status'))
                            ->options([
                                'active' => __('general.Active'),
                                'unactive' => __('general.Unactive'),
                            ])
                            ->default('unactive'),
                    ]),

                // Document Information
                Forms\Components\Fieldset::make(__('general.Document Information'))
                    ->schema([
                        Forms\Components\Select::make('document_type')
                            ->label(__('general.Document Type'))
                            ->options([
                                'passport' => __('general.Passport'),
                                'id_card' => __('general.ID Card'),
                                'driver_license' => __('general.Driver License'),
                                'residency_permit' => __('general.Residency Permit'),
                                'other' => __('general.Other'),
                            ])
                            ->default('passport'),
                        Forms\Components\TextInput::make('document_number')     
                            ->label(__('general.Document Number'))
                            ->maxLength(255),
                        self::documentPhotoUpload(),
                    ]),

                // Employment Information
                Forms\Components\Fieldset::make(__('general.Tented by'))
                    ->schema([
                        Forms\Components\DatePicker::make('hired_date')
                            ->default(now())
                            ->label(__('general.Hired Date'))
                            ->disabled(),
                        Forms\Components\TextInput::make('hired_by')
                            ->default(optional(\Illuminate\Support\Facades\Auth::user())->name)
                            ->label(__('general.Hired By'))
                            ->maxLength(255)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('general.ID'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label(__('general.Photo'))
                    ->circular()
                    ->size(40)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('firstname')
                    ->label(__('general.First Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('midname')
                    ->label(__('general.Middle Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('lastname')
                    ->label(__('general.Last Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label(__('general.Email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('general.Email Verified'))
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->tooltip(function ($record) {
                        return $record->email_verified_at 
                            ? __('general.Email verified on') . ' ' . $record->email_verified_at->format('Y-m-d H:i')
                            : __('general.Email not verified');
                    }),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('general.Phone Number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('nationality')
                    ->label(__('general.Nationality'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('general.Birth Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('address')
                    ->label(__('general.Address'))
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'unactive' => __('general.Unactive'),
                        'pending' => __('general.Pending'),
                        default => $state,
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('document_type')
                    ->label(__('general.Document Type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'passport' => 'primary',
                        'id_card' => 'success',
                        'driver_license' => 'warning',
                        'residency_permit' => 'info',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'passport' => __('general.Passport'),
                        'id_card' => __('general.ID Card'),
                        'driver_license' => __('general.Driver License'),
                        'residency_permit' => __('general.Residency Permit'),
                        'other' => __('general.Other'),
                        default => $state,
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('document_number')
                    ->label(__('general.Document Number'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label(__('general.Hired Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label(__('general.Hired By'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'pending' => __('general.Pending'),
                    ]),
                    
                Tables\Filters\SelectFilter::make('document_type')
                    ->label(__('general.Document Type'))
                    ->options([
                        'passport' => __('general.Passport'),
                        'id_card' => __('general.ID Card'),
                        'driver_license' => __('general.Driver License'),
                        'residency_permit' => __('general.Residency Permit'),
                        'other' => __('general.Other'),
                    ]),
                    
                Tables\Filters\Filter::make('nationality')
                    ->label(__('general.Nationality'))
                    ->form([
                        Forms\Components\TextInput::make('nationality')
                            ->label(__('general.Nationality')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['nationality'], fn ($query, $nationality) => 
                            $query->where('nationality', 'like', "%{$nationality}%"));
                    }),
                    
                Tables\Filters\Filter::make('birth_date_range')
                    ->label(__('general.Birth Date Range'))
                    ->form([
                        Forms\Components\DatePicker::make('birth_from')
                            ->label(__('general.From Date')),
                        Forms\Components\DatePicker::make('birth_until')
                            ->label(__('general.To Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['birth_from'], fn ($query, $date) => $query->whereDate('birth_date', '>=', $date))
                            ->when($data['birth_until'], fn ($query, $date) => $query->whereDate('birth_date', '<=', $date));
                    }),
                    
                Tables\Filters\Filter::make('hired_date_range')
                    ->label(__('general.Hiring Date Range'))
                    ->form([
                        Forms\Components\DatePicker::make('hired_from')
                            ->label(__('general.From Date')),
                        Forms\Components\DatePicker::make('hired_until')
                            ->label(__('general.To Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['hired_from'], fn ($query, $date) => $query->whereDate('hired_date', '>=', $date))
                            ->when($data['hired_until'], fn ($query, $date) => $query->whereDate('hired_date', '<=', $date));
                    }),
                    
                Tables\Filters\Filter::make('has_email')
                    ->label(__('general.Has Email'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email')->where('email', '!=', '')),
                    
                // 📧 Email verification filter
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label(__('general.Email Verification Status'))
                    ->trueLabel(__('general.Verified'))
                    ->falseLabel(__('general.Not Verified'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                Tables\Filters\Filter::make('has_phone')
                    ->label(__('general.Has Phone'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('phone')->where('phone', '!=', '')),
                    
                Tables\Filters\Filter::make('has_profile_photo')
                    ->label(__('general.Has Profile Photo'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('profile_photo')),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export Data'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // 📧 Resend email verification link
                Tables\Actions\Action::make('resend_verification')
                    ->label(__('general.Resend Verification Email'))
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn ($record) => $record->email && !$record->email_verified_at)
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Resend Email Verification Link'))
                    ->modalDescription(__('general.Are you sure you want to resend the verification link?'))
                    ->modalSubmitActionLabel(__('general.Send'))
                    ->modalCancelActionLabel(__('general.Cancel'))
                    ->action(function ($record) {
                        if ($record->email) {
                            try {
                                $record->sendEmailVerificationNotification();
                                
                                Notification::make()
                                    ->title(__('general.Verification Link Sent Successfully'))
                                    ->body(__('general.Verification link sent to') . " {$record->email}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('general.Failed to Send Verification Link'))
                                    ->body(__('general.Error occurred') . ": " . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title(__('general.No Email Address'))
                                ->body(__('general.Tenant does not have an email address'))
                                ->warning()
                                ->send();
                        }
                    }),

                // ✅ Manually verify email
                Tables\Actions\Action::make('mark_verified')
                    ->label(__('general.Mark Email as Verified'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->email && !$record->email_verified_at)
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Manually Verify Email'))
                    ->modalDescription(__('general.Are you sure you want to manually verify the email?'))
                    ->modalSubmitActionLabel(__('general.Verify'))
                    ->modalCancelActionLabel(__('general.Cancel'))
                    ->action(function ($record) {
                        $record->update([
                            'email_verified_at' => now(),
                        ]);
                        
                        Notification::make()
                            ->title(__('general.Email Verified Successfully'))
                            ->body(__('general.Email verified for') . " {$record->firstname} {$record->lastname}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // ✅ Bulk verify email for selected tenants
                    Tables\Actions\BulkAction::make('bulk_verify_email')
                        ->label(__('general.Bulk Verify Email'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading(__('general.Bulk Verify Selected Tenants'))
                        ->modalDescription(__('general.Are you sure you want to verify email for all selected tenants?'))
                        ->modalSubmitActionLabel(__('general.Verify All'))
                        ->modalCancelActionLabel(__('general.Cancel'))
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->email && !$record->email_verified_at) {
                                    $record->update([
                                        'email_verified_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title(__('general.Email Verified Successfully'))
                                ->body(__('general.Email verification confirmed for tenants', ['count' => $count]))
                                ->success()
                                ->send();
                        }),
                    
                    FilamentExportBulkAction::make('export')
                        ->label(__('general.Export Selected')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContractsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}