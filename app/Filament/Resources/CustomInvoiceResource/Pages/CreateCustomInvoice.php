<?php

namespace App\Filament\Resources\CustomInvoiceResource\Pages;

use App\Filament\Resources\CustomInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomInvoice extends CreateRecord
{
    protected static string $resource = CustomInvoiceResource::class;
}
