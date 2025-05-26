<?php

// إنشاء بيانات تجريبية للعقارات الجديدة
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\Address;
use App\Models\Acc;

echo "🏠 إنشاء بيانات تجريبية للعقارات مع الميزات الجديدة...\n\n";

// التأكد من وجود بعض الحسابات
$acc = Acc::first();
if (!$acc) {
    echo "❌ لا توجد حسابات في النظام. يرجى إنشاء حساب أولاً.\n";
    exit;
}

echo "✅ تم العثور على حساب: {$acc->firstname}\n";

// إنشاء عناوين تجريبية
$addresses = [];

// عنوان 1
$address1 = Address::create([
    'country' => 'Jordan',
    'governorate' => 'Amman',
    'city' => 'Amman',
    'district' => 'Abdoun',
    'building_number' => '123',
    'plot_number' => '456',
    'basin_number' => '789',
    'property_number' => '101',
    'street_name' => 'King Abdullah Street'
]);
$addresses[] = $address1;

// عنوان 2
$address2 = Address::create([
    'country' => 'Jordan',
    'governorate' => 'Amman',
    'city' => 'Amman',
    'district' => 'Jabal Al Hussein',
    'building_number' => '456',
    'plot_number' => '789',
    'basin_number' => '123',
    'property_number' => '202',
    'street_name' => 'Rainbow Street'
]);
$addresses[] = $address2;

// عنوان 3
$address3 = Address::create([
    'country' => 'Jordan',
    'governorate' => 'Zarqa',
    'city' => 'Zarqa',
    'district' => 'Central',
    'building_number' => '789',
    'plot_number' => '123',
    'basin_number' => '456',
    'property_number' => '303',
    'street_name' => 'Main Street'
]);
$addresses[] = $address3;

echo "✅ تم إنشاء 3 عناوين\n";

// إنشاء عقارات متنوعة
$properties = [
    [
        'name' => 'Luxury Villa in Abdoun',
        'description' => 'Beautiful 4-bedroom villa with swimming pool and garden',
        'type1' => 'villa',
        'type2' => 'residential',
        'price' => 450000.00,
        'floors_count' => 3,
        'floor_area' => 200.00,
        'total_area' => 600.00,
        'is_for_sale' => true,
        'is_for_rent' => false,
        'birth_date' => '2020-01-15',
        'address_id' => $addresses[0]->id,
        'acc_id' => $acc->id
    ],
    [
        'name' => 'Modern Apartment Downtown',
        'description' => 'Spacious 2-bedroom apartment in the heart of Amman',
        'type1' => 'building',
        'type2' => 'residential',
        'price' => 1200.00, // للإيجار شهريًا
        'floors_count' => 1,
        'floor_area' => 120.00,
        'total_area' => 120.00,
        'is_for_sale' => false,
        'is_for_rent' => true,
        'birth_date' => '2018-06-20',
        'address_id' => $addresses[1]->id,
        'acc_id' => $acc->id
    ],
    [
        'name' => 'Commercial Warehouse',
        'description' => 'Large warehouse suitable for storage and distribution',
        'type1' => 'warehouse',
        'type2' => 'commercial',
        'price' => 180000.00,
        'floors_count' => 1,
        'floor_area' => 800.00,
        'total_area' => 800.00,
        'is_for_sale' => true,
        'is_for_rent' => true, // متاح للبيع والإيجار
        'birth_date' => '2015-03-10',
        'address_id' => $addresses[2]->id,
        'acc_id' => $acc->id
    ],
    [
        'name' => 'Family House with Garden',
        'description' => 'Cozy 3-bedroom house perfect for families',
        'type1' => 'house',
        'type2' => 'residential',
        'price' => 220000.00,
        'floors_count' => 2,
        'floor_area' => 150.00,
        'total_area' => 300.00,
        'is_for_sale' => true,
        'is_for_rent' => false,
        'birth_date' => '2019-11-05',
        'address_id' => $addresses[0]->id, // نفس العنوان لاختبار التجميع
        'acc_id' => $acc->id
    ],
    [
        'name' => 'Office Building',
        'description' => 'Modern office building with elevator and parking',
        'type1' => 'building',
        'type2' => 'commercial',
        'price' => 750000.00,
        'floors_count' => 8,
        'floor_area' => 200.00,
        'total_area' => 1600.00,
        'is_for_sale' => true,
        'is_for_rent' => false,
        'birth_date' => '2021-08-15',
        'address_id' => $addresses[1]->id,
        'acc_id' => $acc->id
    ]
];

foreach ($properties as $index => $propertyData) {
    $property = Property::create($propertyData);
    echo "✅ تم إنشاء العقار: {$property->name} (ID: {$property->id})\n";
}

echo "\n🎉 تم إنشاء " . count($properties) . " عقارات تجريبية بنجاح!\n";
echo "📊 الآن يمكنك اختبار:\n";
echo "   - الويدجت الإحصائية\n";
echo "   - عرض الشبكة (Grid View)\n";
echo "   - عرض الجدول (Table View)\n";
echo "   - الفلاتر الجديدة\n";
echo "   - التصدير\n\n";

// عرض ملخص الإحصائيات
$totalProperties = Property::count();
$buildingsCount = Property::where('type1', 'building')->count();
$residentialCount = Property::where('type2', 'residential')->count();
$forSaleCount = Property::where('is_for_sale', true)->count();
$forRentCount = Property::where('is_for_rent', true)->count();

echo "📈 الإحصائيات الحالية:\n";
echo "   - إجمالي العقارات: {$totalProperties}\n";
echo "   - المباني: {$buildingsCount}\n";
echo "   - العقارات السكنية: {$residentialCount}\n";
echo "   - للبيع: {$forSaleCount}\n";
echo "   - للإيجار: {$forRentCount}\n";
