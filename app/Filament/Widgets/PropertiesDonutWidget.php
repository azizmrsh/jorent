<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PropertiesDonutWidget extends ChartWidget
{
    protected static ?string $heading = 'العقارات والوحدات';
    protected static ?int $sort = 6; // ترتيب أعلى لإظهاره في الأعلى
    protected int|string|array $columnSpan = "full";

    protected function getData(): array
    {
        // إحصاء الوحدات لكل عقار
        $propertiesWithUnits = Property::withCount('units')->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'عدد الوحدات',
                    'data' => $propertiesWithUnits->pluck('units_count')->toArray(),
                    'backgroundColor' => $this->generateColors(count($propertiesWithUnits)),
                ],
            ],
            'labels' => $propertiesWithUnits->pluck('name')->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    // توليد ألوان متنوعة لمخطط الدائرة
    protected function generateColors(int $count): array
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 137) % 360; // ضمان توزيع متجانس للألوان
            $colors[] = "hsla({$hue}, 70%, 60%, 0.7)";
        }
        return $colors;
    }
}
