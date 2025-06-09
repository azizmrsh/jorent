<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== اختبار آمن للعلاقة Property-Address ===\n";

try {
    echo "تنظيف البيانات بطريقة آمنة...\n";
    
    // تعطيل فحص المفاتيح الخارجية مؤقتاً
    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // حذف البيانات بالترتيب الصحيح (من الأطفال إلى الوالدين)
    \App\Models\Payment::query()->delete();
    echo "- تم حذف المدفوعات\n";
    
    \App\Models\Contract1::query()->delete();
    echo "- تم حذف العقود\n";
    
    \App\Models\Unit::query()->delete();
    echo "- تم حذف الوحدات\n";
    
    \App\Models\Property::query()->delete();
    echo "- تم حذف العقارات\n";
    
    \App\Models\Address::query()->delete();
    echo "- تم حذف العناوين\n";
    
    \App\Models\Tenant::query()->delete();
    echo "- تم حذف المستأجرين\n";
    
    \App\Models\Acc::query()->delete();
    echo "- تم حذف الحسابات\n";
    
    // إعادة تفعيل فحص المفاتيح الخارجية
    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\nإعادة تعيين auto increment...\n";
    \DB::statement('ALTER TABLE addresses AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE properties AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE units AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE contract1s AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE payments AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE tenants AUTO_INCREMENT = 1');
    \DB::statement('ALTER TABLE accs AUTO_INCREMENT = 1');
    
    echo "\nبدء عملية الـ seeding الجديدة...\n";
    
    // تشغيل السيدرز بالترتيب الصحيح
    echo "1. إنشاء حسابات الإدارة...\n";
    \Artisan::call('db:seed', ['--class' => 'AccSeeder']);
    echo "   تم إنشاء " . \App\Models\Acc::count() . " حساب\n";
    
    echo "\n2. إنشاء العقارات مع العناوين...\n";
    \Artisan::call('db:seed', ['--class' => 'PropertySeeder']);
    echo "   تم إنشاء " . \App\Models\Property::count() . " عقار\n";
    echo "   تم إنشاء " . \App\Models\Address::count() . " عنوان\n";
    
    echo "\n3. إنشاء المستأجرين...\n";
    \Artisan::call('db:seed', ['--class' => 'TenantSeeder']);
    echo "   تم إنشاء " . \App\Models\Tenant::count() . " مستأجر\n";
    
    echo "\n4. إنشاء الوحدات...\n";
    \Artisan::call('db:seed', ['--class' => 'UnitSeeder']);
    echo "   تم إنشاء " . \App\Models\Unit::count() . " وحدة\n";
    
    echo "\n5. إنشاء العقود...\n";
    \Artisan::call('db:seed', ['--class' => 'Contract1Seeder']);
    echo "   تم إنشاء " . \App\Models\Contract1::count() . " عقد\n";
    
    echo "\n6. إنشاء المدفوعات...\n";
    \Artisan::call('db:seed', ['--class' => 'PaymentSeeder']);
    echo "   تم إنشاء " . \App\Models\Payment::count() . " دفعة\n";
    
    echo "\n=== نتائج الاختبار ===\n";
    
    // فحص العلاقة Property-Address
    $totalProperties = \App\Models\Property::count();
    $propertiesWithAddress = \App\Models\Property::whereHas('address')->count();
    $totalAddresses = \App\Models\Address::count();
    
    echo "إجمالي العقارات: {$totalProperties}\n";
    echo "العقارات التي لها عناوين: {$propertiesWithAddress}\n";
    echo "إجمالي العناوين: {$totalAddresses}\n";
    
    if ($totalProperties == $propertiesWithAddress && $totalProperties == $totalAddresses) {
        echo "✅ نجح! كل عقار له عنوان واحد بالضبط\n";
    } else {
        echo "⚠️ تحذير: عدم تطابق في العلاقات\n";
    }
    
    // فحص عينة من العقارات
    echo "\n=== عينة من العقارات والعناوين ===\n";
    $sampleProperties = \App\Models\Property::with('address')->limit(5)->get();
    
    foreach ($sampleProperties as $property) {
        echo "عقار: {$property->name}\n";
        echo "  - له عنوان: " . ($property->address ? 'نعم' : 'لا') . "\n";
        if ($property->address) {
            echo "  - العنوان: {$property->address->city}, {$property->address->district}\n";
        }
        echo "\n";
    }
    
    // فحص الوحدات
    $totalUnits = \App\Models\Unit::count();
    $unitsWithProperties = \App\Models\Unit::whereHas('property')->count();
    echo "=== فحص الوحدات ===\n";
    echo "إجمالي الوحدات: {$totalUnits}\n";
    echo "الوحدات المرتبطة بعقارات: {$unitsWithProperties}\n";
    
    // فحص العقود
    $totalContracts = \App\Models\Contract1::count();
    $contractsWithAllRelations = \App\Models\Contract1::whereHas('property')
        ->whereHas('tenant')
        ->whereHas('unit')
        ->count();
    echo "\n=== فحص العقود ===\n";
    echo "إجمالي العقود: {$totalContracts}\n";
    echo "العقود مع جميع العلاقات: {$contractsWithAllRelations}\n";
    
    // فحص المدفوعات
    $totalPayments = \App\Models\Payment::count();
    $paymentsWithContracts = \App\Models\Payment::whereHas('contract')->count();
    echo "\n=== فحص المدفوعات ===\n";
    echo "إجمالي المدفوعات: {$totalPayments}\n";
    echo "المدفوعات المرتبطة بعقود: {$paymentsWithContracts}\n";
    
    echo "\n✅ تم الانتهاء من الاختبار بنجاح!\n";
    
} catch (\Exception $e) {
    echo "\n❌ حدث خطأ: " . $e->getMessage() . "\n";
    echo "التفاصيل: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    // إعادة تفعيل فحص المفاتيح الخارجية في حالة الخطأ
    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
}

echo "\n=== انتهاء الاختبار ===\n";
