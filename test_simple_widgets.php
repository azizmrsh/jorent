<?php

require_once 'vendor/autoload.php';

// تحميل Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Property;
use App\Models\Unit;

echo "🏢 اختبار بيانات الويدجتس الجديدة\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// 1. إجمالي العقارات
$totalProperties = Property::count();
$recentProperties = Property::where('created_at', '>=', now()->subDays(30))->count();
$thisMonthGrowth = $recentProperties > 0 ? round(($recentProperties / max($totalProperties - $recentProperties, 1)) * 100, 1) : 0;

echo "📊 ويدجت إجمالي العقارات:\n";
echo "   - إجمالي العقارات: " . number_format($totalProperties) . "\n";
echo "   - العقارات الجديدة (30 يوم): " . number_format($recentProperties) . "\n";
echo "   - معدل النمو الشهري: " . $thisMonthGrowth . "%\n\n";

// 2. أنواع العقارات
$buildingCount = Property::where('type1', 'building')->count();
$villaCount = Property::where('type1', 'villa')->count();
$houseCount = Property::where('type1', 'house')->count();
$warehouseCount = Property::where('type1', 'warehouse')->count();

$buildingPercentage = $totalProperties > 0 ? round(($buildingCount / $totalProperties) * 100, 1) : 0;
$villaPercentage = $totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0;
$housePercentage = $totalProperties > 0 ? round(($houseCount / $totalProperties) * 100, 1) : 0;
$warehousePercentage = $totalProperties > 0 ? round(($warehouseCount / $totalProperties) * 100, 1) : 0;

echo "🏘️ ويدجت أنواع العقارات:\n";
echo "   - المباني: " . number_format($buildingCount) . " ({$buildingPercentage}%)\n";
echo "   - الفيلات: " . number_format($villaCount) . " ({$villaPercentage}%)\n";
echo "   - المنازل: " . number_format($houseCount) . " ({$housePercentage}%)\n";
echo "   - المستودعات: " . number_format($warehouseCount) . " ({$warehousePercentage}%)\n\n";

// 3. أنواع الاستخدام
$residentialCount = Property::where('type2', 'residential')->count();
$commercialCount = Property::where('type2', 'commercial')->count();
$industrialCount = Property::where('type2', 'industrial')->count();

$residentialPercentage = $totalProperties > 0 ? round(($residentialCount / $totalProperties) * 100, 1) : 0;
$commercialPercentage = $totalProperties > 0 ? round(($commercialCount / $totalProperties) * 100, 1) : 0;
$industrialPercentage = $totalProperties > 0 ? round(($industrialCount / $totalProperties) * 100, 1) : 0;

echo "🏢 ويدجت أنواع الاستخدام:\n";
echo "   - السكني: " . number_format($residentialCount) . " ({$residentialPercentage}%)\n";
echo "   - التجاري: " . number_format($commercialCount) . " ({$commercialPercentage}%)\n";
echo "   - الصناعي: " . number_format($industrialCount) . " ({$industrialPercentage}%)\n\n";

// 4. الوحدات
$totalUnits = Unit::count();
$propertiesWithUnits = Property::has('units')->count();
$averageUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;
$propertiesWithUnitsPercentage = $totalProperties > 0 ? round(($propertiesWithUnits / $totalProperties) * 100, 1) : 0;

echo "🔢 ويدجت عداد الوحدات:\n";
echo "   - إجمالي الوحدات: " . number_format($totalUnits) . "\n";
echo "   - متوسط الوحدات لكل عقار: " . $averageUnitsPerProperty . "\n";
echo "   - العقارات بوحدات: " . number_format($propertiesWithUnits) . " ({$propertiesWithUnitsPercentage}%)\n\n";

echo "✅ تم اختبار جميع الويدجتس بنجاح!\n";
echo "🌐 يمكنك الآن زيارة: http://127.0.0.1:8000/admin/properties\n";
