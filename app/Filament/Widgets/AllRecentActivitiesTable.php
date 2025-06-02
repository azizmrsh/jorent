<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AllRecentActivitiesTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
      protected function getHeading(): string
    {
        return '📋 Recent Contract Activities';
    }public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Activity Type')
                    ->getStateUsing(fn () => 'New Contract')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-document-plus'),
                    
                Tables\Columns\TextColumn::make('tenant_name')
                    ->label('Tenant')
                    ->getStateUsing(function ($record) {
                        return $record->tenant ? "{$record->tenant->firstname} {$record->tenant->lastname}" : 'N/A';
                    })
                    ->searchable(['tenant.firstname', 'tenant.lastname'])
                    ->icon('heroicon-m-user'),
                    
                Tables\Columns\TextColumn::make('property_details')
                    ->label('Property Details')
                    ->getStateUsing(function ($record) {
                        $property = $record->property ? $record->property->name : 'N/A';
                        $unit = $record->unit ? " - Unit: {$record->unit->name}" : '';
                        $location = $record->property && $record->property->location ? " ({$record->property->location})" : '';
                        return $property . $unit . $location;
                    })
                    ->wrap()
                    ->limit(50)
                    ->icon('heroicon-m-building-office'),
                    
                Tables\Columns\TextColumn::make('rent_amount')
                    ->label('Amount')
                    ->money('JOD')
                    ->alignEnd()
                    ->icon('heroicon-m-currency-dollar'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'inactive' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('F j, Y \a\t g:i A')),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10)
            ->striped();
    }    protected function getTableQuery(): Builder
    {
        // Get the most recent contracts as the base query
        // We'll combine with other activities in the table rendering
        return Contract1::query()
            ->with(['tenant', 'property', 'unit'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->latest('created_at')
            ->limit(25); // Limit for performance
    }
}
