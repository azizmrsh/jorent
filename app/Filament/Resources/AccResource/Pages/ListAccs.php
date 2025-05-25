<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;

class ListAccs extends ListRecords
{
    protected string $tableLayout = 'grid';
    
    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
        ];
    }

    protected function toggleTableLayout(): void
    {
        $layout = $this->tableLayout;
        $this->tableLayout = $layout === 'grid' ? 'list' : 'grid';
    }


}
