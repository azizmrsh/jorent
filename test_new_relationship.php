<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار العلاقة الجديدة ===\n";

// تنظيف البيانات أولاً (ما عدا المستخدمين)
echo "تنظيف البيانات...\n";
\App\Models\Payment::truncate();
\App\Models\Contract1::truncate();
\App\Models\Unit::truncate();
\App\Models\Property::truncate();
\App\Models\Address::truncate();
\App\Models\Tenant::truncate();
\App\Models\Acc::truncate();

echo "إعادة إنشاء البيانات...\n";

// إنشاء Accounts أولاً
\App\Models\Acc::factory(50)->create();
echo "✅ تم إنشاء 50 حساب\n";

// إنشاء العناوين
\App\Models\Address::factory(50)->create();
echo "✅ تم إنشاء 50 عنوان\n";

// إنشاء العقارات (ستستخدم العناوين الموجودة)
\App\Models\Property::factory(50)->create();
echo "✅ تم إنشاء 50 عقار\n";

// التحقق من النتيجة
echo "\n=== النتائج ===\n";
echo "إجمالي العقارات: " . \App\Models\Property::count() . "\n";
echo "إجمالي العناوين: " . \App\Models\Address::count() . "\n";
echo "العقارات التي لها address_id: " . \App\Models\Property::whereNotNull('address_id')->count() . "\n";
echo "العقارات التي لها عنوان مرتبط: " . \App\Models\Property::whereHas('address')->count() . "\n";
echo "العناوين المرتبطة بعقارات: " . \App\Models\Address::whereNotNull('property_id')->count() . "\n";

// عرض أمثلة
echo "\n=== أمثلة ===\n";
$properties = \App\Models\Property::with('address')->limit(3)->get();
foreach ($properties as $property) {
    $hasAddress = $property->address ? 'نعم' : 'لا';
    echo "عقار: {$property->name}\n";
    echo "  - address_id: {$property->address_id}\n";
    echo "  - له عنوان: {$hasAddress}\n";
    if ($property->address) {
        echo "  - عنوان: {$property->address->city}, {$property->address->district}\n";
        echo "  - العنوان مرتبط بعقار ID: {$property->address->property_id}\n";
    }
    echo "\n";
}

echo "=== انتهاء الاختبار ===\n";
