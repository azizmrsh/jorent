<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use Filament\Widgets\Widget;

class PropertyTypesSection extends Widget
{
    protected static string $view = 'filament.widgets.property-types-section';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
}
