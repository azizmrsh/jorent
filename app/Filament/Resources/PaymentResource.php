<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $label = 'Payment';
    protected static ?string $pluralLabel = 'Payments';
    protected static ?string $slug = 'payments';
    protected static ?string $recordTitleAttribute = 'id';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الدفعة الأساسية')
                    ->schema([
                        Forms\Components\Select::make('contract_id')
                            ->relationship('contract', 'id')
                            ->label('العقد')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "عقد #{$record->id} - {$record->tenant->firstname} {$record->tenant->lastname}"
                            ),
                            
                        Forms\Components\TextInput::make('payment_number')
                            ->label('رقم الدفعة')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => \App\Models\Payment::generatePaymentNumber())
                            ->disabled()
                            ->dehydrated(),
                            
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0.01),
                            
                        Forms\Components\Select::make('currency')
                            ->label('العملة')
                            ->options([
                                'JOD' => 'دينار أردني (JOD)',
                                'USD' => 'دولار أمريكي (USD)',
                                'EUR' => 'يورو (EUR)',
                                'SAR' => 'ريال سعودي (SAR)',
                                'AED' => 'درهم إماراتي (AED)',
                            ])
                            ->required()
                            ->default('JOD'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('معلومات الأطراف')
                    ->schema([
                        Forms\Components\TextInput::make('payer_name')
                            ->label('اسم الدافع')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('receiver_name')
                            ->label('اسم المستلم')
                            ->required()
                            ->maxLength(255)
                            ->default(function () {
                                $user = auth()->user();
                                if ($user) {
                                    // إنشاء الاسم الكامل من الحقول المتاحة
                                    $nameParts = array_filter([
                                        $user->name,
                                        $user->midname,
                                        $user->lastname
                                    ]);
                                    
                                    return implode(' ', $nameParts) ?: 'غير محدد';
                                }
                                return null;
                            })
                            ->placeholder(function () {
                                $user = auth()->user();
                                if ($user) {
                                    $nameParts = array_filter([
                                        $user->name,
                                        $user->midname,
                                        $user->lastname
                                    ]);
                                    
                                    return 'مثال: ' . implode(' ', $nameParts);
                                }
                                return 'سيتم ملء الاسم تلقائياً';
                            })
                            ->helperText('يتم ملء هذا الحقل تلقائياً بإسم المستخدم الحالي، يمكن تعديله')
                            ->suffixIcon('heroicon-m-user'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('تفاصيل الدفع')
                    ->schema([
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('تاريخ الدفع')
                            ->required()
                            ->default(now()),
                            
                        Forms\Components\Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options([
                                'cash' => 'نقداً',
                                'bank_transfer' => 'تحويل بنكي',
                                'wallet' => 'محفظة إلكترونية',
                                'cliq' => 'كليك',
                            ])
                            ->required()
                            ->default('cash')
                            ->reactive(),
                            
                        Forms\Components\Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'completed' => 'مكتمل',
                                'failed' => 'فاشل',
                                'cancelled' => 'ملغي',
                            ])
                            ->required()
                            ->default('completed'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('تفاصيل البنك')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->label('اسم البنك')
                            ->maxLength(255)
                            ->placeholder('مطلوب للتحويلات البنكية'),
                            
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('رقم المعاملة البنكية')
                            ->maxLength(255)
                            ->placeholder('رقم المعاملة أو الإيصال البنكي'),
                            
                        Forms\Components\TextInput::make('reference_number')
                            ->label('الرقم المرجعي')
                            ->maxLength(255)
                            ->placeholder('اختياري - رقم الإيصال أو المرجع'),
                    ])
                    ->columns(3)
                    ->visible(fn (callable $get) => in_array($get('payment_method'), ['bank_transfer', 'wallet', 'cliq'])),
                    
                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات إضافية')
                            ->maxLength(65535)
                            ->placeholder('أضف أي ملاحظات إضافية حول هذه الدفعة')
                            ->columnSpanFull(),
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
                    
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('رقم الدفعة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الدفعة!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.id')
                    ->label('رقم العقد')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.tenant.firstname')
                    ->label('المستأجر')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($record) => 
                        $record->contract->tenant->firstname . ' ' . $record->contract->tenant->lastname
                    )
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.unit.name')
                    ->label('الوحدة')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payer_name')
                    ->label('اسم الدافع')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('اسم المستلم')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money(fn ($record) => $record->currency ?? 'JOD')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('currency')
                    ->label('العملة')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'JOD' => 'دينار أردني',
                        'USD' => 'دولار أمريكي',
                        'EUR' => 'يورو',
                        'SAR' => 'ريال سعودي',
                        'AED' => 'درهم إماراتي',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'primary',
                        'wallet' => 'warning',
                        'cliq' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'wallet' => 'محفظة إلكترونية',
                        'cliq' => 'كليك',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'مكتمل',
                        'pending' => 'قيد الانتظار',
                        'failed' => 'فاشل',
                        'cancelled' => 'ملغي',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('اسم البنك')
                    ->searchable()
                    ->limit(20)
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('رقم المعاملة')
                    ->searchable()
                    ->limit(15)
                    ->placeholder('غير محدد')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('الرقم المرجعي')
                    ->searchable()
                    ->limit(20)
                    ->placeholder('لا يوجد')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(30)
                    ->placeholder('لا توجد ملاحظات')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contract_id')
                    ->label('العقد')
                    ->relationship('contract', 'id')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('currency')
                    ->label('العملة')
                    ->options([
                        'JOD' => 'دينار أردني',
                        'USD' => 'دولار أمريكي',
                        'EUR' => 'يورو',
                        'SAR' => 'ريال سعودي',
                        'AED' => 'درهم إماراتي',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('حالة الدفع')
                    ->options([
                        'completed' => 'مكتمل',
                        'pending' => 'قيد الانتظار',
                        'failed' => 'فاشل',
                        'cancelled' => 'ملغي',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'wallet' => 'محفظة إلكترونية',
                        'cliq' => 'كليك',
                    ]),
                    
                Tables\Filters\Filter::make('amount_range')
                    ->label('نطاق المبلغ')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('من (ريال)')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('إلى (ريال)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['amount_from'], fn ($query, $amount) => $query->where('amount', '>=', $amount))
                            ->when($data['amount_to'], fn ($query, $amount) => $query->where('amount', '<=', $amount));
                    }),
                    
                Tables\Filters\Filter::make('payment_date_range')
                    ->label('نطاق تاريخ الدفع')
                    ->form([
                        Forms\Components\DatePicker::make('payment_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('payment_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['payment_from'], fn ($query, $date) => $query->whereDate('payment_date', '>=', $date))
                            ->when($data['payment_until'], fn ($query, $date) => $query->whereDate('payment_date', '<=', $date));
                    }),
                    
                Tables\Filters\Filter::make('has_reference')
                    ->label('لديه رقم مرجعي')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('reference_number')->where('reference_number', '!=', '')),
                    
                Tables\Filters\Filter::make('this_month')
                    ->label('هذا الشهر')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)),
                    
                Tables\Filters\Filter::make('today')
                    ->label('اليوم')
                    ->query(fn (Builder $query): Builder => $query->whereDate('payment_date', now())),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد'),
                ]),
            ])
            ->defaultSort('payment_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
