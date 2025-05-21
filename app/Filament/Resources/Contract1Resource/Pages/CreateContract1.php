<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContract1 extends CreateRecord
{
    protected static string $resource = Contract1Resource::class;
}
