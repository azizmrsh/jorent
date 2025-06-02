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

    protected static ?string $navigationLabel = 'Tenants';
    protected static ?string $label = 'Tenant';
    protected static ?string $pluralLabel = 'Tenants';
    protected static ?string $slug = 'tenants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Personal Information
                Forms\Components\Fieldset::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('firstname')
                            ->required()
                            ->label('First Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('midname')
                            ->label('Middle Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lastname')
                            ->label('Last Name')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Birth Date'),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Nationality')
                            ->maxLength(255),
                    ]),

                // Contact Information
                Forms\Components\Fieldset::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rule('email')
                            ->validationMessages([
                                'email' => 'Please enter a valid email address.',
                                'required' => 'The email field is required.',
                            ]),
                      Forms\Components\TextInput::make('password')
                            ->required()
                            ->label('Password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn (string $context) => in_array($context, ['create', 'edit'])),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255),
                    ]),

                // Profile Information
                Forms\Components\Fieldset::make('Profile Information')
                    ->schema([
                        self::profilePhotoUpload(),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'unactive' => 'Unactive',
                            ])
                            ->default('unactive'),
                    ]),

                // Document Information
                Forms\Components\Fieldset::make('Document Information')
                    ->schema([
                        Forms\Components\Select::make('document_type')
                            ->label('Document Type')
                            ->options([
                                'passport' => 'Passport',
                                'id_card' => 'ID Card',
                                'driver_license' => 'Driver License',
                                'residency_permit' => 'Residency Permit',
                                'other' => 'Other',
                            ])
                            ->default('passport'),
                        Forms\Components\TextInput::make('document_number')     
                            ->label('Document Number')
                            ->maxLength(255),
                        self::documentPhotoUpload(),
                    ]),

                // Employment Information
                Forms\Components\Fieldset::make('Tented by')
                    ->schema([
                        Forms\Components\DatePicker::make('hired_date')
                            ->default(now())
                            ->label('Hired Date')
                            ->disabled(),
                        Forms\Components\TextInput::make('hired_by')
                            ->default(optional(\Illuminate\Support\Facades\Auth::user())->name)
                            ->label('Tented by')
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
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('الصورة')
                    ->circular()
                    ->size(40)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('firstname')
                    ->label('الاسم الأول')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('midname')
                    ->label('الاسم الأوسط')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('lastname')
                    ->label('الاسم الأخير')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('✅ تم تأكيد البريد')
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->tooltip(function ($record) {
                        return $record->email_verified_at 
                            ? 'تم التأكيد في ' . $record->email_verified_at->format('Y-m-d H:i')
                            : 'البريد الإلكتروني غير مؤكد';
                    }),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('nationality')
                    ->label('الجنسية')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('address')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('document_type')
                    ->label('نوع الوثيقة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'passport' => 'primary',
                        'id_card' => 'success',
                        'driver_license' => 'warning',
                        'residency_permit' => 'info',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('document_number')
                    ->label('رقم الوثيقة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('تاريخ التوظيف')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('تم التوظيف بواسطة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'pending' => 'في الانتظار',
                    ]),
                    
                Tables\Filters\SelectFilter::make('document_type')
                    ->label('نوع الوثيقة')
                    ->options([
                        'passport' => 'جواز سفر',
                        'id_card' => 'بطاقة هوية',
                        'driver_license' => 'رخصة قيادة',
                        'residency_permit' => 'إقامة',
                        'other' => 'أخرى',
                    ]),
                    
                Tables\Filters\Filter::make('nationality')
                    ->label('الجنسية')
                    ->form([
                        Forms\Components\TextInput::make('nationality')
                            ->label('الجنسية'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['nationality'], fn ($query, $nationality) => 
                            $query->where('nationality', 'like', "%{$nationality}%"));
                    }),
                    
                Tables\Filters\Filter::make('birth_date_range')
                    ->label('نطاق تاريخ الميلاد')
                    ->form([
                        Forms\Components\DatePicker::make('birth_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('birth_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['birth_from'], fn ($query, $date) => $query->whereDate('birth_date', '>=', $date))
                            ->when($data['birth_until'], fn ($query, $date) => $query->whereDate('birth_date', '<=', $date));
                    }),
                    
                Tables\Filters\Filter::make('hired_date_range')
                    ->label('نطاق تاريخ التوظيف')
                    ->form([
                        Forms\Components\DatePicker::make('hired_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('hired_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['hired_from'], fn ($query, $date) => $query->whereDate('hired_date', '>=', $date))
                            ->when($data['hired_until'], fn ($query, $date) => $query->whereDate('hired_date', '<=', $date));
                    }),
                    
                Tables\Filters\Filter::make('has_email')
                    ->label('لديه بريد إلكتروني')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email')->where('email', '!=', '')),
                    
                // 📧 فلتر التحقق من البريد الإلكتروني
                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label('📧 تأكيد البريد الإلكتروني')
                    ->trueLabel('✅ مؤكد')
                    ->falseLabel('❌ غير مؤكد')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                Tables\Filters\Filter::make('has_phone')
                    ->label('لديه رقم هاتف')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('phone')->where('phone', '!=', '')),
                    
                Tables\Filters\Filter::make('has_profile_photo')
                    ->label('لديه صورة شخصية')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('profile_photo')),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // 📧 إعادة إرسال رابط التحقق من البريد الإلكتروني
                Tables\Actions\Action::make('resend_verification')
                    ->label('📧 إعادة إرسال التحقق')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn ($record) => $record->email && !$record->email_verified_at)
                    ->requiresConfirmation()
                    ->modalHeading('إعادة إرسال رابط التحقق من البريد الإلكتروني')
                    ->modalDescription('هل أنت متأكد من أنك تريد إعادة إرسال رابط التحقق من البريد الإلكتروني؟')
                    ->modalSubmitActionLabel('إرسال')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function ($record) {
                        if ($record->email) {
                            try {
                                $record->sendEmailVerificationNotification();
                                
                                Notification::make()
                                    ->title('تم إرسال رابط التحقق بنجاح')
                                    ->body("تم إرسال رابط التحقق إلى {$record->email}")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('فشل في إرسال رابط التحقق')
                                    ->body("حدث خطأ: " . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('لا يوجد بريد إلكتروني')
                                ->body('المستأجر لا يمتلك بريد إلكتروني')
                                ->warning()
                                ->send();
                        }
                    }),

                // ✅ تأكيد البريد الإلكتروني يدوياً
                Tables\Actions\Action::make('mark_verified')
                    ->label('✅ تأكيد البريد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->email && !$record->email_verified_at)
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد البريد الإلكتروني يدوياً')
                    ->modalDescription('هل أنت متأكد من أنك تريد تأكيد البريد الإلكتروني يدوياً؟')
                    ->modalSubmitActionLabel('تأكيد')
                    ->modalCancelActionLabel('إلغاء')
                    ->action(function ($record) {
                        $record->update([
                            'email_verified_at' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('تم تأكيد البريد الإلكتروني بنجاح')
                            ->body("تم تأكيد البريد الإلكتروني لـ {$record->firstname} {$record->lastname}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // ✅ تأكيد البريد الإلكتروني للمحددين
                    Tables\Actions\BulkAction::make('bulk_verify_email')
                        ->label('✅ تأكيد البريد للمحددين')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('تأكيد البريد الإلكتروني للمستأجرين المحددين')
                        ->modalDescription('هل أنت متأكد من أنك تريد تأكيد البريد الإلكتروني لجميع المستأجرين المحددين؟')
                        ->modalSubmitActionLabel('تأكيد الكل')
                        ->modalCancelActionLabel('إلغاء')
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
                                ->title('تم تأكيد البريد الإلكتروني بنجاح')
                                ->body("تم تأكيد البريد الإلكتروني لـ {$count} مستأجر")
                                ->success()
                                ->send();
                        }),
                    
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد'),
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