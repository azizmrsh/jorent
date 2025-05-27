<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use Filament\Widgets\Widget;

class QuickStatsSection extends Widget
{
    protected static string $view = 'filament.widgets.quick-stats-section';
    
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
}
