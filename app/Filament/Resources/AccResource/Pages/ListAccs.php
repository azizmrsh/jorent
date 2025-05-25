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
    use HasToggleableTable;

    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('toggleLayout')
                ->label('↔ Toggle Layout')
                ->icon('heroicon-o-view-columns')
                ->color('gray')
                ->action(fn () => $this->toggleTableLayout()),
        ];
    }

    protected function toggleTableLayout(): void
    {
        $layout = $this->tableLayout;
        $this->tableLayout = $layout === 'grid' ? 'list' : 'grid';
    }

    public static function getSeparatorBlock(string $label = ''): Stack
    {
        return Stack::make([
            TextColumn::make('label')
                ->label('')
                ->default('────────────────────────────── ' . $label)
                ->color('gray')
                ->size('sm'),
        ]);
    }
}
