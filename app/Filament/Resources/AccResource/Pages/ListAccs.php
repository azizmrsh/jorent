<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListAccs extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
