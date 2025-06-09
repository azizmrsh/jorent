<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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

class UserResource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $navigationLabel = null;
    protected static ?string $label = null;
    protected static ?string $pluralLabel = null;
    
    public static function getNavigationLabel(): string
    {
        return __('general.Managers');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Manager');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Managers');
    }
    protected static ?string $slug = 'managers';

    public static function form(Form $form): Form
    {
        return $form 
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label(__('general.First Name'))->maxLength(255),
                Forms\Components\TextInput::make('midname')->required()->label(__('general.Middle Name'))->maxLength(255),
                Forms\Components\TextInput::make('lastname')->required()->label(__('general.Last Name'))->maxLength(255),
                Forms\Components\TextInput::make('role')->required()->label(__('general.Role'))->maxLength(255)->default('user'),
                Forms\Components\TextInput::make('status')->required()->label(__('general.Status'))->maxLength(255)->default('active'),
                Forms\Components\TextInput::make('email')->required()->label(__('general.Email'))->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->label(__('general.Phone'))->tel()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->label(__('general.Address'))->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')->label(__('general.Birth Date')),
                self::profilePhotoUpload(),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->label(__('general.Password'))
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->confirmed()
                    ->dehydrated(fn ($state) => ! blank($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->required()
                    ->label(__('general.Confirm Password'))
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => ! blank($state)),
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
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('general.First Name'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('midname')
                    ->label(__('general.Middle Name'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label(__('general.Last Name'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('general.Role'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'owner' => 'success',
                        'user' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('general.Email'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage(__('general.Email copied!'))
                    ->limit(50),
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
                            ? __('general.Verified on') . ' ' . $record->email_verified_at->format('Y-m-d H:i')
                            : __('general.Email not verified');
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('general.Phone'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage(__('general.Phone copied!'))
                    ->placeholder(__('general.No phone')),
                Tables\Columns\TextColumn::make('address')
                    ->label(__('general.Address'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->limit(50)
                    ->placeholder(__('general.No address')),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('general.Birth Date'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('general.No birth date')),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label(__('general.Profile Photo'))
                    ->circular()
                    ->size(40)
                    ->toggleable()
                    ->defaultImageUrl('data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="#e5e7eb"><circle cx="50" cy="50" r="50"/><circle cx="50" cy="35" r="15" fill="#9ca3af"/><ellipse cx="50" cy="75" rx="20" ry="15" fill="#9ca3af"/></svg>')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🔍 فلتر النص الموحد
                Tables\Filters\Filter::make('search')
                    ->form([
                        Forms\Components\TextInput::make('search')
                            ->label(__('general.Search'))
                            ->placeholder(__('general.Search by name, email, phone...'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['search'],
                            fn (Builder $query, $search): Builder => $query->where(function (Builder $query) use ($search) {
                                $query->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('midname', 'like', '%' . $search . '%')
                                    ->orWhere('lastname', 'like', '%' . $search . '%')
                                    ->orWhere('email', 'like', '%' . $search . '%')
                                    ->orWhere('phone', 'like', '%' . $search . '%');
                            })
                        );
                    })
                    ->label(__('general.Global Search')),

                // 👤 فلتر الأدوار
                Tables\Filters\SelectFilter::make('role')
                    ->label(__('general.Role'))
                    ->options([
                        'admin' => __('general.Admin'),
                        'manager' => __('general.Manager'),
                        'user' => __('general.User'),
                        'owner' => __('general.Owner'),
                    ])
                    ->multiple()
                    ->placeholder(__('general.Select roles')),

                // ✅ فلتر الحالة
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                    ])
                    ->placeholder(__('general.Select status')),

                // 📧 فلتر التحقق من البريد الإلكتروني
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label(__('general.Email Verified'))
                    ->trueLabel(__('general.Verified'))
                    ->falseLabel(__('general.Not Verified'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),

                // 📅 فلتر تاريخ الميلاد
                Tables\Filters\Filter::make('birth_date_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('birth_from')
                                    ->label(__('general.Birth Date From'))
                                    ->placeholder(__('general.From date')),
                                Forms\Components\DatePicker::make('birth_to')
                                    ->label(__('general.Birth Date To'))
                                    ->placeholder(__('general.To date')),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['birth_from'],
                                fn (Builder $query, $date): Builder => $query->where('birth_date', '>=', $date)
                            )
                            ->when(
                                $data['birth_to'],
                                fn (Builder $query, $date): Builder => $query->where('birth_date', '<=', $date)
                            );
                    })
                    ->label(__('general.Birth Date Range')),

                // 📞 فلتر وجود الهاتف
                Tables\Filters\TernaryFilter::make('has_phone')
                    ->label(__('general.Has Phone'))
                    ->trueLabel(__('general.With Phone'))
                    ->falseLabel(__('general.Without Phone'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('phone')->where('phone', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('phone')->orWhere('phone', '=', ''),
                    ),

                // 🖼️ فلتر وجود الصورة الشخصية
                Tables\Filters\TernaryFilter::make('has_profile_photo')
                    ->label(__('general.Has Profile Photo'))
                    ->trueLabel(__('general.With Photo'))
                    ->falseLabel(__('general.Without Photo'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('profile_photo')->where('profile_photo', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('profile_photo')->orWhere('profile_photo', '=', ''),
                    ),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export'))
                    ->fileName('users-export')
                    ->defaultFormat('xlsx')
                    
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('general.View')),
                Tables\Actions\EditAction::make()->label(__('general.Edit')),
                Tables\Actions\Action::make('resend_verification')
                    ->label(__('general.Resend Verification'))
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->sendEmailVerificationNotification();
                        Notification::make()
                            ->title(__('general.Verification email sent'))
                            ->body(__('general.Verification link sent to') . ' ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Resend Email Verification Link'))
                    ->modalDescription(__('general.Are you sure you want to resend the verification link?'))
                    ->modalSubmitActionLabel(__('general.Send')),
                Tables\Actions\Action::make('mark_verified')
                    ->label(__('general.Mark as Verified'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);
                        Notification::make()
                            ->title(__('general.Email Verified Successfully'))
                            ->body(__('general.Email verified for') . ' ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Manually Verify Email'))
                    ->modalDescription(__('general.Are you sure you want to manually verify the email?'))
                    ->modalSubmitActionLabel(__('general.Verify')),
                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('general.Delete Selected')),
                    Tables\Actions\BulkAction::make('resend_verification_bulk')
                        ->label(__('general.Resend Verification to Selected'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(function ($records) {
                            $unverifiedCount = 0;
                            foreach ($records as $record) {
                                if (is_null($record->email_verified_at)) {
                                    $record->sendEmailVerificationNotification();
                                    $unverifiedCount++;
                                }
                            }
                            Notification::make()
                                ->title(__('general.Verification Link Sent Successfully'))
                                ->body(__('general.Email verification confirmed for tenants', ['count' => $unverifiedCount]))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('general.Resend Email Verification Link'))
                        ->modalDescription(__('general.Are you sure you want to resend email verification to all selected unverified users?'))
                        ->modalSubmitActionLabel(__('general.Send')),
                    Tables\Actions\BulkAction::make('mark_verified_bulk')
                        ->label(__('general.Mark Selected as Verified'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $updatedCount = 0;
                            foreach ($records as $record) {
                                if (is_null($record->email_verified_at)) {
                                    $record->update(['email_verified_at' => now()]);
                                    $updatedCount++;
                                }
                            }
                            Notification::make()
                                ->title(__('general.Email Verified Successfully'))
                                ->body(__('general.Email verification confirmed for tenants', ['count' => $updatedCount]))
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading(__('general.Manually Verify Email'))
                        ->modalDescription(__('general.Are you sure you want to manually mark selected emails as verified?'))
                        ->modalSubmitActionLabel(__('general.Verify All')),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
                        ->fileName('users-selected')
                        ->defaultFormat('pdf')
                        
                        ->disablePreview(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
