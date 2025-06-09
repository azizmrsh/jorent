<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = 'آخر المدفوعات';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->whereHas('contract', function ($query) {
                        $query->where('tenant_id', auth('tenant')->id());
                    })
                    ->with(['contract.unit.property'])
                    ->latest()
                    ->limit(5)
            )
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
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Payment $record): string => route('filament.tenant.resources.my-payments.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('لا توجد مدفوعات')
            ->emptyStateDescription('لم يتم العثور على أي مدفوعات حالياً')
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
