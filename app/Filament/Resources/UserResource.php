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
    protected static ?string $navigationLabel = 'Managers';
    protected static ?string $label = 'Manager';
    protected static ?string $pluralLabel = 'Managers';
    protected static ?string $slug = 'managers';

    public static function form(Form $form): Form
    {
        return $form 
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('First Name')->maxLength(255),
                Forms\Components\TextInput::make('midname')->required()->label('Middle Name')->maxLength(255),
                Forms\Components\TextInput::make('lastname')->required()->label('Last Name')->maxLength(255),
                Forms\Components\TextInput::make('role')->required()->label('Role')->maxLength(255)->default('user'),
                Forms\Components\TextInput::make('status')->required()->label('Status')->maxLength(255)->default('active'),
                Forms\Components\TextInput::make('email')->required()->label('Email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->label('Phone')->tel()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->label('Address')->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')->label('Birth Date'),
                self::profilePhotoUpload(),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->label('Password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->confirmed()
                    ->dehydrated(fn ($state) => ! blank($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->required()
                    ->label('Password Confirmation')
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
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('👤 First Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('midname')
                    ->label('👤 Middle Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('👤 Last Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('🏷️ Role')
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
                    ->label('✅ Status')
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
                    ->label('📧 Email')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->limit(50),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('✅ Email Verified')
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->tooltip(function ($record) {
                        return $record->email_verified_at 
                            ? 'Verified on ' . $record->email_verified_at->format('Y-m-d H:i')
                            : 'Email not verified';
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('📞 Phone')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->placeholder('No phone'),
                Tables\Columns\TextColumn::make('address')
                    ->label('📍 Address')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->limit(50)
                    ->placeholder('No address'),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('🎂 Birth Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('No birth date'),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('🖼️ Profile Photo')
                    ->circular()
                    ->size(40)
                    ->toggleable()
                    ->defaultImageUrl('data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="#e5e7eb"><circle cx="50" cy="50" r="50"/><circle cx="50" cy="35" r="15" fill="#9ca3af"/><ellipse cx="50" cy="75" rx="20" ry="15" fill="#9ca3af"/></svg>')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('📅 Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🔍 فلتر النص الموحد
                Tables\Filters\Filter::make('search')
                    ->form([
                        Forms\Components\TextInput::make('search')
                            ->label('🔍 Search')
                            ->placeholder('Search by name, email, phone...')
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
                    ->label('🔍 Global Search'),

                // 👤 فلتر الأدوار
                Tables\Filters\SelectFilter::make('role')
                    ->label('👤 Role')
                    ->options([
                        'admin' => '👑 Admin',
                        'manager' => '🏢 Manager',
                        'user' => '👤 User',
                        'owner' => '🏠 Owner',
                    ])
                    ->multiple()
                    ->placeholder('Select roles'),

                // ✅ فلتر الحالة
                Tables\Filters\SelectFilter::make('status')
                    ->label('✅ Status')
                    ->options([
                        'active' => '✅ Active',
                        'inactive' => '❌ Inactive',
                    ])
                    ->placeholder('Select status'),

                // 📧 فلتر التحقق من البريد الإلكتروني
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label('📧 Email Verified')
                    ->trueLabel('✅ Verified')
                    ->falseLabel('❌ Not Verified')
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
                                    ->label('Birth Date From')
                                    ->placeholder('From date'),
                                Forms\Components\DatePicker::make('birth_to')
                                    ->label('Birth Date To')
                                    ->placeholder('To date'),
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
                    ->label('📅 Birth Date Range'),

                // 📞 فلتر وجود الهاتف
                Tables\Filters\TernaryFilter::make('has_phone')
                    ->label('📞 Has Phone')
                    ->trueLabel('With Phone')
                    ->falseLabel('Without Phone')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('phone')->where('phone', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('phone')->orWhere('phone', '=', ''),
                    ),

                // 🖼️ فلتر وجود الصورة الشخصية
                Tables\Filters\TernaryFilter::make('has_profile_photo')
                    ->label('🖼️ Has Profile Photo')
                    ->trueLabel('With Photo')
                    ->falseLabel('Without Photo')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('profile_photo')->where('profile_photo', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('profile_photo')->orWhere('profile_photo', '=', ''),
                    ),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export')
                    ->fileName('users-export')
                    ->defaultFormat('xlsx')
                    
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resend_verification')
                    ->label('Resend Verification')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->sendEmailVerificationNotification();
                        Notification::make()
                            ->title('Verification email sent')
                            ->body('Email verification has been sent to ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Resend Email Verification')
                    ->modalDescription('Are you sure you want to resend the email verification to this user?')
                    ->modalSubmitActionLabel('Send Email'),
                Tables\Actions\Action::make('mark_verified')
                    ->label('Mark as Verified')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->update(['email_verified_at' => now()]);
                        Notification::make()
                            ->title('Email marked as verified')
                            ->body('Email has been manually verified for ' . $record->email)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mark Email as Verified')
                    ->modalDescription('Are you sure you want to manually mark this email as verified?')
                    ->modalSubmitActionLabel('Mark Verified'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('resend_verification_bulk')
                        ->label('Resend Verification to Selected')
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
                                ->title('Verification emails sent')
                                ->body("Email verification sent to {$unverifiedCount} unverified users")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Resend Email Verification')
                        ->modalDescription('Are you sure you want to resend email verification to all selected unverified users?')
                        ->modalSubmitActionLabel('Send Emails'),
                    Tables\Actions\BulkAction::make('mark_verified_bulk')
                        ->label('Mark Selected as Verified')
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
                                ->title('Emails marked as verified')
                                ->body("{$updatedCount} users have been manually verified")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Mark Emails as Verified')
                        ->modalDescription('Are you sure you want to manually mark selected emails as verified?')
                        ->modalSubmitActionLabel('Mark Verified'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('Export Selected')
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
