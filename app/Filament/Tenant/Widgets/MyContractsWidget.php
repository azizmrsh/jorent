<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Contract1;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MyContractsWidget extends BaseWidget
{
    protected static ?string $heading = 'عقودي الحالية';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contract1::query()
                    ->where('tenant_id', auth('tenant')->id())
                    ->where('status', 'active')
                    ->with(['unit.property'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unit.property.name')
                    ->label('اسم العقار')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unit.unit_number')
                    ->label('رقم الوحدة')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('monthly_rent')
                    ->label('الإيجار الشهري')
                    ->money('SAR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'pending' => 'معلق',
                        'terminated' => 'منتهي',
                        default => $state,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Contract1 $record): string => route('filament.tenant.resources.my-contracts.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('لا توجد عقود نشطة')
            ->emptyStateDescription('لم يتم العثور على أي عقود نشطة حالياً')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
