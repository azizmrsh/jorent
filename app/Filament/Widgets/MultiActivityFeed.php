<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use App\Models\Contract1;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Property;

class MultiActivityFeed extends Widget
{
    protected static string $view = 'filament.widgets.multi-activity-feed';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'activities' => $this->getRecentActivities(),
        ];
    }

    protected function getRecentActivities()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Get recent contracts
        $contracts = Contract1::with(['tenant', 'property', 'unit'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => "contract_{$contract->id}",
                    'type' => 'contract',
                    'icon' => 'heroicon-m-document-plus',
                    'color' => 'success',
                    'title' => 'New Contract',
                    'description' => "Contract #{$contract->id} - {$contract->tenant?->firstname} {$contract->tenant?->lastname}",
                    'subtitle' => $contract->property?->name . ($contract->unit ? " ({$contract->unit->name})" : ''),
                    'amount' => $contract->rent_amount,
                    'status' => $contract->status ?? 'active',
                    'date' => $contract->created_at,
                ];
            });        // Get recent payments
        $payments = Payment::with(['contract.tenant', 'contract.property'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => "payment_{$payment->id}",
                    'type' => 'payment',
                    'icon' => 'heroicon-m-currency-dollar',
                    'color' => 'info',
                    'title' => 'Payment Received',
                    'description' => "Payment #{$payment->id} via {$payment->payment_method}",
                    'subtitle' => $payment->contract?->tenant?->firstname . ' ' . $payment->contract?->tenant?->lastname,
                    'amount' => $payment->amount,
                    'status' => $payment->payment_status ?? 'completed',
                    'date' => $payment->created_at,
                ];
            });

        // Get recent tenants
        $tenants = Tenant::where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($tenant) {
                return [
                    'id' => "tenant_{$tenant->id}",
                    'type' => 'tenant',
                    'icon' => 'heroicon-m-user-plus',
                    'color' => 'warning',
                    'title' => 'New Tenant',
                    'description' => "{$tenant->firstname} {$tenant->lastname}",
                    'subtitle' => 'Registered in system',
                    'amount' => null,
                    'status' => 'active',
                    'date' => $tenant->created_at,
                ];
            });

        // Get recent properties
        $properties = Property::where('created_at', '>=', $thirtyDaysAgo)
            ->get()
            ->map(function ($property) {
                return [
                    'id' => "property_{$property->id}",
                    'type' => 'property',
                    'icon' => 'heroicon-m-building-office',
                    'color' => 'primary',
                    'title' => 'New Property',
                    'description' => $property->name,
                    'subtitle' => $property->location ?? 'Location not specified',
                    'amount' => null,
                    'status' => $property->status ?? 'active',
                    'date' => $property->created_at,
                ];
            });

        // Merge and sort all activities
        return collect()
            ->merge($contracts)
            ->merge($payments)
            ->merge($tenants)
            ->merge($properties)
            ->sortByDesc('date')
            ->take(15)
            ->values();
    }
}
