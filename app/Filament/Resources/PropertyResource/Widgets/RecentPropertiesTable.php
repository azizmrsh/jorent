<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class RecentPropertiesTable extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    
    protected function getHeading(): string
    {
        return '🏗️ Recent Properties & Activities';
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Property::with(['units', 'units.contracts'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-building-office-2')
                    ->iconColor('primary'),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'apartment' => 'info',
                        'villa' => 'success',
                        'commercial' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(function (Property $record): string {
                        return $record->address;
                    }),
                    
                Tables\Columns\TextColumn::make('units_count')
                    ->label('Units')
                    ->getStateUsing(fn (Property $record): int => $record->units->count())
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('occupied_units')
                    ->label('Occupied')
                    ->getStateUsing(function (Property $record): string {
                        $total = $record->units->count();
                        $occupied = $record->units->filter(function ($unit) {
                            return $unit->contracts->where('status', 'active')->count() > 0;
                        })->count();
                        return $occupied . '/' . $total;
                    })
                    ->badge()
                    ->color(function (Property $record): string {
                        $total = $record->units->count();
                        $occupied = $record->units->filter(function ($unit) {
                            return $unit->contracts->where('status', 'active')->count() > 0;
                        })->count();
                        $rate = $total > 0 ? ($occupied / $total) * 100 : 0;
                        return $rate > 80 ? 'success' : ($rate < 50 ? 'danger' : 'warning');
                    }),
                    
                Tables\Columns\TextColumn::make('monthly_income')
                    ->label('Monthly Income')
                    ->getStateUsing(function (Property $record): string {
                        $income = $record->units->sum(function ($unit) {
                            return $unit->contracts->where('status', 'active')->sum('rent_amount');
                        });
                        return '$' . number_format($income);
                    })
                    ->color('success')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Property $record): string {
                        $total = $record->units->count();
                        $occupied = $record->units->filter(function ($unit) {
                            return $unit->contracts->where('status', 'active')->count() > 0;
                        })->count();
                        
                        if ($total == 0) return 'No Units';
                        if ($occupied == $total) return 'Fully Occupied';
                        if ($occupied == 0) return 'Vacant';
                        return 'Partially Occupied';
                    })
                    ->badge()
                    ->color(function (Property $record): string {
                        $total = $record->units->count();
                        $occupied = $record->units->filter(function ($unit) {
                            return $unit->contracts->where('status', 'active')->count() > 0;
                        })->count();
                        
                        if ($total == 0) return 'gray';
                        if ($occupied == $total) return 'success';
                        if ($occupied == 0) return 'danger';
                        return 'warning';
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated(false);
    }
}
