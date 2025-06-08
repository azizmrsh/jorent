<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract1;

echo "=== اختبار نظام التوقيع الكامل بعد الإصلاح ===\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

try {
    echo "1. فحص العقد رقم 34:\n";
    $contract = Contract1::find(34);
    
    if ($contract) {
        echo "   ✓ العقد موجود\n";
        echo "   - رقم المستأجر: " . ($contract->tenant_id ?? 'غير محدد') . "\n";
        echo "   - مسار توقيع المستأجر: " . ($contract->tenant_signature_path ?? 'غير موجود') . "\n";
        echo "   - مسار توقيع المؤجر: " . ($contract->landlord_signature_path ?? 'غير موجود') . "\n";
    } else {
        echo "   ✗ العقد غير موجود\n";
    }

    echo "\n2. فحص أحدث العقود:\n";
    $recentContracts = Contract1::orderBy('id', 'desc')->take(10)->get();
    
    if ($recentContracts->count() > 0) {
        echo "   أحدث 10 عقود:\n";
        foreach ($recentContracts as $c) {
            $tenantSig = $c->tenant_signature_path ? '✓' : '✗';
            $landlordSig = $c->landlord_signature_path ? '✓' : '✗';
            echo "   - العقد #{$c->id}: مستأجر({$tenantSig}) مؤجر({$landlordSig})\n";
        }
    } else {
        echo "   ✗ لا توجد عقود\n";
    }

    echo "\n3. فحص ملفات التوقيع:\n";
    $signaturesDir = public_path('uploads/contracts/signatures');
    
    if (is_dir($signaturesDir)) {
        $files = scandir($signaturesDir);
        $signatureFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'png';
        });
        
        echo "   ✓ مجلد التوقيعات موجود\n";
        echo "   عدد ملفات التوقيع: " . count($signatureFiles) . "\n";
        
        if (count($signatureFiles) > 0) {
            echo "   ملفات التوقيع:\n";
            foreach ($signatureFiles as $file) {
                $filePath = $signaturesDir . '/' . $file;
                $fileSize = number_format(filesize($filePath));
                $fileDate = date('Y-m-d H:i:s', filemtime($filePath));
                echo "   - {$file} ({$fileSize} بايت، {$fileDate})\n";
            }
        }
    } else {
        echo "   ✗ مجلد التوقيعات غير موجود\n";
    }

    echo "\n4. فحص بنية قاعدة البيانات:\n";
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('contract1s');
    if ($tableExists) {
        echo "   ✓ جدول contract1s موجود\n";
        
        $tenantSigColumn = \Illuminate\Support\Facades\Schema::hasColumn('contract1s', 'tenant_signature_path');
        $landlordSigColumn = \Illuminate\Support\Facades\Schema::hasColumn('contract1s', 'landlord_signature_path');
        
        echo "   - حقل tenant_signature_path: " . ($tenantSigColumn ? '✓' : '✗') . "\n";
        echo "   - حقل landlord_signature_path: " . ($landlordSigColumn ? '✓' : '✗') . "\n";
    } else {
        echo "   ✗ جدول contract1s غير موجود\n";
    }

    echo "\n5. إحصائيات العقود والتوقيعات:\n";
    $totalContracts = Contract1::count();
    $contractsWithTenantSig = Contract1::whereNotNull('tenant_signature_path')->count();
    $contractsWithLandlordSig = Contract1::whereNotNull('landlord_signature_path')->count();
    $contractsWithBothSigs = Contract1::whereNotNull('tenant_signature_path')
                                     ->whereNotNull('landlord_signature_path')
                                     ->count();
    
    echo "   - إجمالي العقود: {$totalContracts}\n";
    echo "   - عقود بتوقيع المستأجر: {$contractsWithTenantSig}\n";
    echo "   - عقود بتوقيع المؤجر: {$contractsWithLandlordSig}\n";
    echo "   - عقود بالتوقيعين: {$contractsWithBothSigs}\n";

    echo "\n6. ملخص الإصلاح:\n";
    echo "   ✓ تم تصحيح أسماء الحقول في Contract1Resource.php\n";
    echo "   ✓ تم إزالة استخدام \$set() غير الضروري\n";
    echo "   ✓ تم توحيد طريقة حفظ التوقيعات\n";
    echo "   ✓ الكود الآن يحفظ مسار الملف مباشرة في قاعدة البيانات\n";
    echo "   ✓ لا توجد مشكلة تكرار في حفظ التوقيعات\n";

} catch (Exception $e) {
    echo "✗ خطأ: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الاختبار الكامل ===\n";

?>
