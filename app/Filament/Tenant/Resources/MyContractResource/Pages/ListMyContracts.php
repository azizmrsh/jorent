<?php

namespace App\Filament\Tenant\Resources\MyContractResource\Pages;

use App\Filament\Tenant\Resources\MyContractResource;
use Filament\Resources\Pages\ListRecords;

class ListMyContracts extends ListRecords
{
    protected static string $resource = MyContractResource::class;

    protected static ?string $title = 'عقودي';
}
