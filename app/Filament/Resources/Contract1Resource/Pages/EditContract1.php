<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContract1 extends EditRecord
{
    protected static string $resource = Contract1Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
