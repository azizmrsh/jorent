<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use App\Models\Unit;

class TestWidgetsData extends Command
{
    protected $signature = 'widgets:test';
    protected $description = 'اختبار بيانات الويدجتس الجديدة';

    public function handle()
    {
        $this->info('🏢 اختبار بيانات الويدجتس الجديدة');
        $this->line(str_repeat('=', 50));

        // 1. إجمالي العقارات
        $totalProperties = Property::count();
        $recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
        $thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

        $this->info('📊 ويدجت إجمالي العقارات:');
        $this->line("   - إجمالي العقارات: " . number_format($totalProperties));
        $this->line("   - العقارات الجديدة (30 يوم): " . number_format($recentProperties));
        $this->line("   - معدل النمو الشهري: {$thisMonthGrowth}%");
        $this->line('');

        // 2. أنواع العقارات
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();

        $buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
        $villaPercentage = $totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0;
        $housePercentage = $totalProperties > 0 ? round(($houseCount / $totalProperties) * 100, 1) : 0;
        $warehousePercentage = $totalProperties > 0 ? round(($warehouseCount / $totalProperties) * 100, 1) : 0;

        $this->info('🏘️ ويدجت أنواع العقارات:');
        $this->line("   - المباني: " . number_format($buildingCount) . " ({$buildingPercentage}%)");
        $this->line("   - الفيلات: " . number_format($villaCount) . " ({$villaPercentage}%)");
        $this->line("   - المنازل: " . number_format($houseCount) . " ({$housePercentage}%)");
        $this->line("   - المستودعات: " . number_format($warehouseCount) . " ({$warehousePercentage}%)");
        $this->line('');

        // 3. أنواع الاستخدام
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();

        $residentialPercentage = $totalProperties > 0 ? round(($residentialCount / $totalProperties) * 100, 1) : 0;
        $commercialPercentage = $totalProperties > 0 ? round(($commercialCount / $totalProperties) * 100, 1) : 0;
        $industrialPercentage = $totalProperties > 0 ? round(($industrialCount / $totalProperties) * 100, 1) : 0;

        $this->info('🏢 ويدجت أنواع الاستخدام:');
        $this->line("   - السكني: " . number_format($residentialCount) . " ({$residentialPercentage}%)");
        $this->line("   - التجاري: " . number_format($commercialCount) . " ({$commercialPercentage}%)");
        $this->line("   - الصناعي: " . number_format($industrialCount) . " ({$industrialPercentage}%)");
        $this->line('');

        // 4. الوحدات
        $totalUnits = Unit::count();
        $propertiesWithUnits = Property::has('units')->count();
        $averageUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;
        $propertiesWithUnitsPercentage = $totalProperties > 0 ? round(($propertiesWithUnits / $totalProperties) * 100, 1) : 0;

        $this->info('🔢 ويدجت عداد الوحدات:');
        $this->line("   - إجمالي الوحدات: " . number_format($totalUnits));
        $this->line("   - متوسط الوحدات لكل عقار: {$averageUnitsPerProperty}");
        $this->line("   - العقارات بوحدات: " . number_format($propertiesWithUnits) . " ({$propertiesWithUnitsPercentage}%)");
        $this->line('');

        $this->info('✅ تم اختبار جميع الويدجتس بنجاح!');
        $this->line('🌐 يمكنك الآن زيارة: http://127.0.0.1:8000/admin/properties');

        return Command::SUCCESS;
    }
}
