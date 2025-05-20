<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestContractsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contract::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('landlord_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant')
                    ->formatStateUsing(fn ($record) => $record->tenant ? "{$record->tenant->firstname} {$record->tenant->lastname}" : '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rent_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'terminated',
                        'warning' => 'pending',
                        'success' => 'active',
                        'gray' => 'completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->heading('Latest Contracts');
    }
}
