<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Payments';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '30s';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::with(['contract.tenant', 'contract.property', 'contract.unit'])
                    ->latest('payment_date')
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('contract.tenant.firstname')
                    ->label('Tenant')
                    ->formatStateUsing(function ($record) {
                        if ($record->contract && $record->contract->tenant) {
                            return $record->contract->tenant->firstname . ' ' . $record->contract->tenant->lastname;
                        }
                        return 'No Tenant';
                    })
                    ->searchable(['firstname', 'lastname']),
                    
                Tables\Columns\TextColumn::make('contract.property.name')
                    ->label('Property')
                    ->searchable()
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('contract.unit.name')
                    ->label('Unit')
                    ->badge()
                    ->color('secondary'),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('JOD')
                    ->alignEnd()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'info',
                        'wallet' => 'warning', 
                        'cliq' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => '💵 Cash',
                        'bank_transfer' => '🏦 Bank',
                        'wallet' => '📱 Wallet',
                        'cliq' => '⚡ CliQ',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Payment $record): string => route('filament.admin.resources.payments.edit', $record))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('No Recent Payments')
            ->emptyStateDescription('Payments will appear here once they are created.')
            ->emptyStateIcon('heroicon-o-credit-card');
    }
}
