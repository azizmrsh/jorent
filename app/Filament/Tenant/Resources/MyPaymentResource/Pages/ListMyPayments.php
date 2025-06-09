<?php

namespace App\Filament\Tenant\Resources\MyPaymentResource\Pages;

use App\Filament\Tenant\Resources\MyPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListMyPayments extends ListRecords
{
    protected static string $resource = MyPaymentResource::class;

    protected static ?string $title = 'مدفوعاتي';
}
