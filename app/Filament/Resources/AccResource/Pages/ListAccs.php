<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Filament\Tables\Actions\Action;

class ListAccs extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Toggle View')
                ->label('↔ Toggle Layout')
                ->icon('heroicon-o-view-columns')
                ->color('gray')
                ->action(fn () => $this->toggleTableLayout())
                ->visible(fn () => true),
        ];
    }

    protected function toggleTableLayout(): void
    {
        $layout = $this->getTableLayout();
        $this->setTableLayout($layout === 'grid' ? 'list' : 'grid');
    }
}
