<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class RecentPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 52; // ترتيب منخفض لإظهاره في أسفل الصفحة
    protected int|string|array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['contract', 'contract.tenant'])
                    ->latest('payment_date')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('contract.tenant.firstname')
                    ->label('المستأجر')
                    ->formatStateUsing(fn ($record) => 
                        $record->contract && $record->contract->tenant ? 
                        "{$record->contract->tenant->firstname} {$record->contract->tenant->lastname}" : 
                        'غير متوفر'
                    )
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->heading('آخر المدفوعات')
            ->description('آخر 8 مدفوعات مسجلة في النظام');
    }
}
