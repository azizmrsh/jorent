<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract1;
use Illuminate\Support\Facades\DB;

echo "=== فحص شامل نهائي لنظام التوقيع ===\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. فحص جميع العقود للبحث عن أي توقيعات
    echo "1. البحث في جميع العقود عن التوقيعات:\n";
    
    $allContracts = Contract1::all();
    $contractsWithSignatures = [];
    
    foreach ($allContracts as $contract) {
        $hasTenantSig = !empty($contract->tenant_signature_path);
        $hasLandlordSig = !empty($contract->landlord_signature_path);
        
        if ($hasTenantSig || $hasLandlordSig) {
            $contractsWithSignatures[] = [
                'id' => $contract->id,
                'tenant_sig' => $contract->tenant_signature_path,
                'landlord_sig' => $contract->landlord_signature_path,
                'updated_at' => $contract->updated_at
            ];
        }
    }
    
    if (count($contractsWithSignatures) > 0) {
        echo "   ✓ تم العثور على " . count($contractsWithSignatures) . " عقد بتوقيعات:\n";
        foreach ($contractsWithSignatures as $contract) {
            echo "   - العقد #{$contract['id']}:\n";
            echo "     توقيع المستأجر: " . ($contract['tenant_sig'] ?: 'غير موجود') . "\n";
            echo "     توقيع المؤجر: " . ($contract['landlord_sig'] ?: 'غير موجود') . "\n";
            echo "     آخر تحديث: " . $contract['updated_at'] . "\n";
        }
    } else {
        echo "   ⚠️ لم يتم العثور على أي عقود بتوقيعات\n";
    }

    // 2. فحص البيانات الخام في قاعدة البيانات
    echo "\n2. فحص البيانات الخام في قاعدة البيانات:\n";
    
    $rawSignatures = DB::table('contract1s')
        ->select('id', 'tenant_signature_path', 'landlord_signature_path', 'updated_at')
        ->whereNotNull('tenant_signature_path')
        ->orWhereNotNull('landlord_signature_path')
        ->get();
    
    if ($rawSignatures->count() > 0) {
        echo "   ✓ البيانات الخام تؤكد وجود " . $rawSignatures->count() . " عقد بتوقيعات\n";
    } else {
        echo "   ⚠️ لا توجد بيانات توقيعات في قاعدة البيانات\n";
    }

    // 3. فحص ملفات التوقيع ومحاولة ربطها
    echo "\n3. تحليل ملفات التوقيع الموجودة:\n";
    
    $signaturesDir = public_path('uploads/contracts/signatures');
    $files = scandir($signaturesDir);
    $signatureFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'png';
    });
    
    echo "   الملفات الموجودة (" . count($signatureFiles) . "):\n";
    foreach ($signatureFiles as $file) {
        $filePath = $signaturesDir . '/' . $file;
        $fileDate = date('Y-m-d H:i:s', filemtime($filePath));
        echo "   - {$file} (تاريخ الإنشاء: {$fileDate})\n";
        
        // البحث عن هذا الملف في قاعدة البيانات
        $relatedPath = 'contracts/signatures/' . $file;
        $relatedContracts = DB::table('contract1s')
            ->where('tenant_signature_path', $relatedPath)
            ->orWhere('landlord_signature_path', $relatedPath)
            ->get();
        
        if ($relatedContracts->count() > 0) {
            echo "     ✓ مربوط بالعقد: " . $relatedContracts->first()->id . "\n";
        } else {
            echo "     ⚠️ غير مربوط بأي عقد\n";
        }
    }

    // 4. ملخص الحالة النهائية
    echo "\n4. ملخص الحالة النهائية:\n";
    echo "   - إجمالي العقود: " . $allContracts->count() . "\n";
    echo "   - عقود بتوقيعات: " . count($contractsWithSignatures) . "\n";
    echo "   - ملفات التوقيع: " . count($signatureFiles) . "\n";
    echo "   - حالة النظام: " . (count($contractsWithSignatures) > 0 ? "يعمل ✅" : "جاهز للاختبار ⏳") . "\n";

    // 5. توصيات
    echo "\n5. التوصيات:\n";
    if (count($contractsWithSignatures) == 0 && count($signatureFiles) > 0) {
        echo "   🔄 يُنصح بإنشاء عقد جديد لاختبار النظام المُصلح\n";
        echo "   📝 الملفات الموجودة قد تكون من قبل الإصلاح\n";
    } elseif (count($contractsWithSignatures) > 0) {
        echo "   ✅ النظام يعمل بشكل صحيح\n";
        echo "   🎯 يمكن المتابعة باستخدام النظام\n";
    } else {
        echo "   🆕 النظام جاهز لأول استخدام\n";
    }

} catch (Exception $e) {
    echo "✗ خطأ: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الفحص الشامل النهائي ===\n";

?>
