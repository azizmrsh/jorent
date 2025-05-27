<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use App\Models\Unit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SystemAlertsTable extends BaseWidget
{
    protected ?string $heading = '⚠️ Expiring Contracts (Next 30 Days)';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contract1::query()
                    ->with(['tenant', 'unit.property'])
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays(30))
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.property.name')
                    ->label('Property')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Expiry Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn ($record) => now()->diffInDays($record->end_date) <= 7 ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('days_left')
                    ->label('Days Left')
                    ->getStateUsing(fn ($record) => now()->diffInDays($record->end_date) . ' days')
                    ->badge()
                    ->color(fn ($record) => now()->diffInDays($record->end_date) <= 7 ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label('Monthly Rent')
                    ->money('JOD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->getStateUsing(fn ($record) => now()->diffInDays($record->end_date) <= 7 ? 'High' : 'Medium')
                    ->badge()
                    ->color(fn ($record) => now()->diffInDays($record->end_date) <= 7 ? 'danger' : 'warning'),
            ])
            ->defaultSort('end_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priority')
                    ->options([
                        'high' => 'High (≤7 days)',
                        'medium' => 'Medium (8-30 days)',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'high') {
                            return $query->where('end_date', '<=', now()->addDays(7));
                        } elseif ($data['value'] === 'medium') {
                            return $query->where('end_date', '>', now()->addDays(7));
                        }
                        return $query;
                    }),
            ]);
    }

    /**
     * Update every 5 minutes for real-time alerts
     */
    protected static ?string $pollingInterval = '5m';
}
