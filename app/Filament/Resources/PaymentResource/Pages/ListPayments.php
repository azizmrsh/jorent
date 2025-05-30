<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PaymentResource\Widgets\CurrentUserPaymentStatsWidget;
use App\Filament\Resources\PaymentResource\Widgets\TotalPaymentsWidget;
use App\Filament\Resources\PaymentResource\Widgets\PaymentStatusStatsWidget;
use App\Filament\Resources\PaymentResource\Widgets\PaymentMethodStatsWidget;
use App\Filament\Resources\PaymentResource\Widgets\CurrencyStatsWidget;
use App\Filament\Resources\PaymentResource\Widgets\MonthlyPaymentTrendsWidget;
use App\Filament\Resources\PaymentResource\Widgets\PaymentCollectionRateWidget;
use App\Filament\Resources\PaymentResource\Widgets\AveragePaymentValueWidget;
use App\Filament\Resources\PaymentResource\Widgets\BankTransferStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CurrentUserPaymentStatsWidget::class,
            TotalPaymentsWidget::class,
            PaymentStatusStatsWidget::class,
            PaymentMethodStatsWidget::class,
            CurrencyStatsWidget::class,
            MonthlyPaymentTrendsWidget::class,
            PaymentCollectionRateWidget::class,
            AveragePaymentValueWidget::class,
            BankTransferStatsWidget::class,
        ];
    }
}
