<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Unit;

class WidgetsDataTest extends TestCase
{
    public function test_property_counts()
    {
        // اختبار إجمالي العقارات
        $totalProperties = Property::count();
        echo "إجمالي العقارات: " . $totalProperties . "\n";
        
        // اختبار أنواع العقارات
        $buildingCount = Property::where('type1', 'building')->count();
        $villaCount = Property::where('type1', 'villa')->count();
        $houseCount = Property::where('type1', 'house')->count();
        $warehouseCount = Property::where('type1', 'warehouse')->count();
        
        echo "المباني: " . $buildingCount . "\n";
        echo "الفيلات: " . $villaCount . "\n";
        echo "المنازل: " . $houseCount . "\n";
        echo "المستودعات: " . $warehouseCount . "\n";
        
        // اختبار أنواع الاستخدام
        $residentialCount = Property::where('type2', 'residential')->count();
        $commercialCount = Property::where('type2', 'commercial')->count();
        $industrialCount = Property::where('type2', 'industrial')->count();
        
        echo "السكني: " . $residentialCount . "\n";
        echo "التجاري: " . $commercialCount . "\n";
        echo "الصناعي: " . $industrialCount . "\n";
        
        // اختبار الوحدات
        $totalUnits = Unit::count();
        $propertiesWithUnits = Property::has('units')->count();
        $averageUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;
        
        echo "إجمالي الوحدات: " . $totalUnits . "\n";
        echo "العقارات مع وحدات: " . $propertiesWithUnits . "\n";
        echo "متوسط الوحدات لكل عقار: " . $averageUnitsPerProperty . "\n";
        
        $this->assertTrue(true);
    }
}
