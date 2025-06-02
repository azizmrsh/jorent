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
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AllRecentActivitiesTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    protected function getHeading(): string
    {
        return '📊 All Recent System Activities';
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('activity_type')
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
                        'Payment Received' => 'heroicon-m-currency-dollar',
                        'New Tenant' => 'heroicon-m-user-plus',
                        'New Property' => 'heroicon-m-building-office',
                        default => 'heroicon-m-clock',
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('JOD')
                    ->alignEnd()
                    ->placeholder('—'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'success',
                        'paid' => 'success',
                        'pending' => 'warning',
                        'unpaid' => 'warning',
                        'inactive' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime('M j, g:i A')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => Carbon::parse($record->date)->format('F j, Y \a\t g:i A')),
            ])
            ->defaultSort('date', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10)
            ->striped();
    }

    protected function getTableQuery(): Builder
    {
        // This is a workaround for Filament Table Widget limitations
        // We'll return a simple Contract query and override the data in getTableRecords
        return Contract1::query()->whereRaw('1 = 0'); // Return empty query
    }

    public function getTableRecords(): Collection
    {
        return $this->getAllRecentActivities();
    }

    protected function getAllRecentActivities(): Collection
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Get recent contracts
        $recentContracts = Contract1::with(['tenant', 'property', 'unit'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($contract) {
                return (object) [
                    'id' => "contract_{$contract->id}",
                    'activity_type' => 'New Contract',
                    'description' => "Contract #{$contract->id} - {$contract->tenant?->firstname} {$contract->tenant?->lastname} for {$contract->property?->name}" . 
                                   ($contract->unit ? " ({$contract->unit->name})" : ''),
                    'amount' => $contract->rent_amount,
                    'status' => $contract->status ?? 'active',
                    'date' => $contract->created_at,
                ];
            });

        // Get recent payments
        $recentPayments = Payment::with(['contract1.tenant', 'contract1.property'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'id' => "payment_{$payment->id}",
                    'activity_type' => 'Payment Received',
                    'description' => "Payment #{$payment->id} from {$payment->contract1?->tenant?->firstname} {$payment->contract1?->tenant?->lastname}" .
                                   " via {$payment->payment_method}",
                    'amount' => $payment->amount,
                    'status' => $payment->payment_status ?? 'completed',
                    'date' => $payment->created_at,
                ];
            });

        // Get recent tenants
        $recentTenants = Tenant::where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($tenant) {
                return (object) [
                    'id' => "tenant_{$tenant->id}",
                    'activity_type' => 'New Tenant',
                    'description' => "New tenant registered - {$tenant->firstname} {$tenant->lastname}",
                    'amount' => null,
                    'status' => 'active',
                    'date' => $tenant->created_at,
                ];
            });

        // Get recent properties
        $recentProperties = Property::where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($property) {
                return (object) [
                    'id' => "property_{$property->id}",
                    'activity_type' => 'New Property',
                    'description' => "New property added - {$property->name} in {$property->location}",
                    'amount' => null,
                    'status' => $property->status ?? 'active',
                    'date' => $property->created_at,
                ];
            });

        // Merge all activities and sort by date
        return collect()
            ->merge($recentContracts)
            ->merge($recentPayments)
            ->merge($recentTenants)
            ->merge($recentProperties)
            ->sortByDesc('date')
            ->take(50)
            ->values(); // Reset array keys
    }
}
