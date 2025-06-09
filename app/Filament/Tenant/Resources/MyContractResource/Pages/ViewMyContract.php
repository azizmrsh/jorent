<?php

namespace App\Filament\Tenant\Resources\MyContractResource\Pages;

use App\Filament\Tenant\Resources\MyContractResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyContract extends ViewRecord
{
    protected static string $resource = MyContractResource::class;

    protected static ?string $title = 'تفاصيل العقد';
}
