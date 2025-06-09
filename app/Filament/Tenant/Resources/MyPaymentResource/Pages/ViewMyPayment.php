<?php

namespace App\Filament\Tenant\Resources\MyPaymentResource\Pages;

use App\Filament\Tenant\Resources\MyPaymentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyPayment extends ViewRecord
{
    protected static string $resource = MyPaymentResource::class;

    protected static ?string $title = 'تفاصيل الدفعة';
}
