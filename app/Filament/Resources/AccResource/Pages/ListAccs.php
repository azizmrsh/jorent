<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use App\Filament\Resources\AccResource\Widgets\AccStatsOverview;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
// تم تعليق Plugin مؤقتاً لحل مشكلة array_merge()
// use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;

class ListAccs extends ListRecords
{
    // تم تعليق tableLayout مؤقتاً لحل مشكلة array_merge()
    // protected string $tableLayout = 'grid';
    
    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccStatsOverview::class,
        ];
    }

}
