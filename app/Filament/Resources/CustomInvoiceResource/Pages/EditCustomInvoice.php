<?php

namespace App\Filament\Resources\CustomInvoiceResource\Pages;

use App\Filament\Resources\CustomInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomInvoice extends EditRecord
{
    protected static string $resource = CustomInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
