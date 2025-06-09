<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Payment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\MyPaymentResource\Pages;

class MyPaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationLabel = 'مدفوعاتي';
    
    protected static ?string $modelLabel = 'دفعة';
    
    protected static ?string $pluralModelLabel = 'المدفوعات';
    
    protected static ?string $navigationGroup = 'المدفوعات';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('contract', function ($query) {
                $query->where('tenant_id', auth('tenant')->id());
            })
            ->with(['contract.unit.property']);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الدفعة')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('payment_date')
                                    ->label('تاريخ الدفعة')
                                    ->date('Y-m-d'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label('المبلغ')
                                    ->money('SAR'),
                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label('طريقة الدفع')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'cash' => 'نقداً',
                                        'bank_transfer' => 'تحويل بنكي',
                                        'check' => 'شيك',
                                        'credit_card' => 'بطاقة ائتمان',
                                        default => $state,
                                    }),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('حالة الدفعة')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'completed' => 'مكتملة',
                                        'pending' => 'معلقة',
                                        'failed' => 'فاشلة',
                                        default => $state,
                                    }),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('معلومات العقد والعقار')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('contract.contract_number')
                                    ->label('رقم العقد'),
                                Infolists\Components\TextEntry::make('contract.unit.property.name')
                                    ->label('اسم العقار'),
                                Infolists\Components\TextEntry::make('contract.unit.unit_number')
                                    ->label('رقم الوحدة'),
                                Infolists\Components\TextEntry::make('contract.monthly_rent')
                                    ->label('الإيجار الشهري')
                                    ->money('SAR'),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('ملاحظات')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('الملاحظات')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفعة')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان',
                        default => $state,
                    }),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'مكتملة',
                        'pending' => 'معلقة',
                        'failed' => 'فاشلة',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('contract.unit.property.name')
                    ->label('العقار')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.unit.unit_number')
                    ->label('الوحدة')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.contract_number')
                    ->label('رقم العقد')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الدفعة')
                    ->options([
                        'completed' => 'مكتملة',
                        'pending' => 'معلقة',
                        'failed' => 'فاشلة',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض التفاصيل'),
            ])
            ->bulkActions([
                // لا توجد إجراءات جماعية للمستأجرين
            ])
            ->emptyStateHeading('لا توجد مدفوعات')
            ->emptyStateDescription('لم يتم العثور على أي مدفوعات مرتبطة بحسابك')
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyPayments::route('/'),
            'view' => Pages\ViewMyPayment::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // المستأجرون لا يمكنهم إنشاء مدفوعات
    }

    public static function canEdit($record): bool
    {
        return false; // المستأجرون لا يمكنهم تعديل المدفوعات
    }

    public static function canDelete($record): bool
    {
        return false; // المستأجرون لا يمكنهم حذف المدفوعات
    }
}
