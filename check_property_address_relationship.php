<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== تحقق من علاقة Property-Address ===\n";

echo "إجمالي العقارات: " . \App\Models\Property::count() . "\n";
echo "العقارات التي لها address_id: " . \App\Models\Property::whereNotNull('address_id')->count() . "\n";

echo "\nإجمالي العناوين: " . \App\Models\Address::count() . "\n";
echo "العناوين التي لها property_id: " . \App\Models\Address::whereNotNull('property_id')->count() . "\n";

echo "\n=== أمثلة على العقارات الأولى ===\n";
$properties = \App\Models\Property::with('address')->limit(5)->get();

foreach ($properties as $property) {
    $hasAddress = $property->address ? 'نعم' : 'لا';
    echo "عقار: {$property->name}\n";
    echo "  - address_id: {$property->address_id}\n";
    echo "  - له عنوان: {$hasAddress}\n";
    if ($property->address) {
        echo "  - عنوان: {$property->address->city}, {$property->address->district}\n";
    }
    echo "\n";
}

echo "=== العقارات التي لا تملك عناوين ===\n";
$propertiesWithoutAddress = \App\Models\Property::whereDoesntHave('address')->count();
echo "عدد العقارات بدون عناوين: {$propertiesWithoutAddress}\n";

if ($propertiesWithoutAddress > 0) {
    echo "\nأمثلة على العقارات بدون عناوين:\n";
    $examples = \App\Models\Property::whereDoesntHave('address')->limit(3)->get();
    foreach ($examples as $property) {
        echo "- {$property->name} (ID: {$property->id}, address_id: {$property->address_id})\n";
    }
}

echo "\n=== العناوين غير المرتبطة ===\n";
$orphanAddresses = \App\Models\Address::whereNull('property_id')->count();
echo "عدد العناوين غير المرتبطة بعقارات: {$orphanAddresses}\n";

if ($orphanAddresses > 0) {
    echo "\nأمثلة على العناوين غير المرتبطة:\n";
    $examples = \App\Models\Address::whereNull('property_id')->limit(3)->get();
    foreach ($examples as $address) {
        echo "- {$address->city}, {$address->district} (ID: {$address->id})\n";
    }
}

echo "\n=== تحليل المشكلة ===\n";
$totalProperties = \App\Models\Property::count();
$propertiesWithAddressId = \App\Models\Property::whereNotNull('address_id')->count();
$propertiesWithLinkedAddress = \App\Models\Property::whereHas('address')->count();

echo "العقارات التي لها address_id: {$propertiesWithAddressId}/{$totalProperties}\n";
echo "العقارات التي لها عنوان مرتبط فعلياً: {$propertiesWithLinkedAddress}/{$totalProperties}\n";

if ($propertiesWithAddressId != $propertiesWithLinkedAddress) {
    echo "⚠️ هناك مشكلة: بعض العقارات لها address_id ولكن العنوان غير موجود!\n";
    
    // البحث عن العقارات التي لها address_id ولكن العنوان غير موجود
    $brokenLinks = \App\Models\Property::whereNotNull('address_id')
        ->whereDoesntHave('address')->count();
    echo "عدد العقارات بروابط معطلة: {$brokenLinks}\n";
}

echo "\n=== انتهاء التحقق ===\n";