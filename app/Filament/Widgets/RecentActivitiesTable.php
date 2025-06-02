<?php

namespace App\Filament\Widgets;

use App\Models\Contract1;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Property;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RecentActivitiesTable extends BaseWidget
{
    protected static ?string $heading = '🕐 Recent System Activities';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Activity Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New Contract' => 'success',
                        'Payment Received' => 'info',
                        'New Tenant' => 'warning',
                        'New Property' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'New Contract' => 'heroicon-m-document-plus',
                        'Payment Received' => 'heroicon-m-banknotes',
                        'New Tenant' => 'heroicon-m-user-plus',
                        'New Property' => 'heroicon-m-building-office-2',
                        default => 'heroicon-m-bell',
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('JOD')
                    ->placeholder('—')
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->date->format('F j, Y \a\t g:i A')),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Active' => 'success',
                        'Completed' => 'info',
                        'Pending' => 'warning',
                        'Inactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
      protected function getTableQuery(): Builder
    {
        // استخدام العقود الحديثة كنقطة بداية
        return Contract1::query()
            ->with(['tenant', 'property', 'unit'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->latest('created_at');
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->label('Activity Type')
                ->formatStateUsing(fn () => 'New Contract')
                ->badge()
                ->color('success')
                ->icon('heroicon-m-document-plus'),
                
            Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->getStateUsing(function ($record) {
                    return "Contract #{$record->id} - {$record->tenant?->firstname} {$record->tenant?->lastname} for {$record->property?->name} ({$record->unit?->name})";
                })
                ->searchable()
                ->wrap(),
                
            Tables\Columns\TextColumn::make('rent_amount')
                ->label('Amount')
                ->money('JOD')
                ->alignEnd(),
                
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->dateTime('M j, Y g:i A')
                ->sortable()
                ->since()
                ->tooltip(fn ($record) => $record->created_at->format('F j, Y \a\t g:i A')),
                
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                    default => 'gray',
                }),
        ];
    }
}
