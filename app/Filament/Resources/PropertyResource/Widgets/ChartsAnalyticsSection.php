<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use Filament\Widgets\Widget;

class ChartsAnalyticsSection extends Widget
{
    protected static string $view = 'filament.widgets.charts-analytics-section';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
}
