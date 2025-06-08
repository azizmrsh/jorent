<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract1;

echo "=== اختبار إصلاح نظام التوقيع ===\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // البحث عن العقد رقم 34
    $contract = Contract1::find(34);
    
    if ($contract) {
        echo "✓ تم العثور على العقد رقم 34\n";
        echo "تفاصيل العقد:\n";
        echo "- رقم العقد: " . $contract->id . "\n";
        echo "- رقم المستأجر: " . ($contract->tenant_id ?? 'غير محدد') . "\n";
        echo "- رقم المؤجر: " . ($contract->landlord_id ?? 'غير محدد') . "\n";
        echo "- مسار توقيع المستأجر: " . ($contract->tenant_signature_path ?? 'غير موجود') . "\n";
        echo "- مسار توقيع المؤجر: " . ($contract->landlord_signature_path ?? 'غير موجود') . "\n\n";
        
        // فحص ملفات التوقيع
        echo "=== فحص ملفات التوقيع ===\n";
        
        if ($contract->tenant_signature_path) {
            $tenantSigPath = public_path('uploads/' . $contract->tenant_signature_path);
            if (file_exists($tenantSigPath)) {
                echo "✓ ملف توقيع المستأجر موجود: " . $contract->tenant_signature_path . "\n";
                echo "  المسار الكامل: " . $tenantSigPath . "\n";
                echo "  الحجم: " . number_format(filesize($tenantSigPath)) . " بايت\n";
            } else {
                echo "✗ ملف توقيع المستأجر مفقود: " . $contract->tenant_signature_path . "\n";
                echo "  المسار المتوقع: " . $tenantSigPath . "\n";
            }
        } else {
            echo "- لا يوجد توقيع للمستأجر\n";
        }
        
        if ($contract->landlord_signature_path) {
            $landlordSigPath = public_path('uploads/' . $contract->landlord_signature_path);
            if (file_exists($landlordSigPath)) {
                echo "✓ ملف توقيع المؤجر موجود: " . $contract->landlord_signature_path . "\n";
                echo "  المسار الكامل: " . $landlordSigPath . "\n";
                echo "  الحجم: " . number_format(filesize($landlordSigPath)) . " بايت\n";
            } else {
                echo "✗ ملف توقيع المؤجر مفقود: " . $contract->landlord_signature_path . "\n";
                echo "  المسار المتوقع: " . $landlordSigPath . "\n";
            }
        } else {
            echo "- لا يوجد توقيع للمؤجر\n";
        }
        
        // فحص جميع ملفات التوقيع الموجودة
        echo "\n=== فحص جميع ملفات التوقيع الموجودة ===\n";
        $signaturesDir = public_path('uploads/contracts/signatures');
        if (is_dir($signaturesDir)) {
            $files = scandir($signaturesDir);
            $signatureFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'png';
            });
            
            if (count($signatureFiles) > 0) {
                echo "الملفات الموجودة في مجلد التوقيعات:\n";
                foreach ($signatureFiles as $file) {
                    $fullPath = $signaturesDir . '/' . $file;
                    $size = filesize($fullPath);
                    $created = date('Y-m-d H:i:s', filemtime($fullPath));
                    echo "- {$file} (الحجم: " . number_format($size) . " بايت، تاريخ الإنشاء: {$created})\n";
                }
            } else {
                echo "لا توجد ملفات توقيع في المجلد\n";
            }
        } else {
            echo "✗ مجلد التوقيعات غير موجود: {$signaturesDir}\n";
        }
        
    } else {
        echo "✗ لم يتم العثور على العقد رقم 34\n";
        
        // البحث عن أحدث العقود
        echo "\nالبحث عن أحدث العقود:\n";
        $recentContracts = Contract1::orderBy('id', 'desc')->take(5)->get();
        
        if ($recentContracts->count() > 0) {
            echo "أحدث 5 عقود:\n";
            foreach ($recentContracts as $c) {
                echo "- العقد رقم: {$c->id}, المستأجر: {$c->tenant_id}, التوقيعات: ";
                $sigs = [];
                if ($c->tenant_signature_path) $sigs[] = "مستأجر";
                if ($c->landlord_signature_path) $sigs[] = "مؤجر";
                echo count($sigs) > 0 ? implode(', ', $sigs) : "لا توجد";
                echo "\n";
            }
        } else {
            echo "لا توجد عقود في النظام\n";
        }
    }

} catch (Exception $e) {
    echo "✗ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
    echo "تفاصيل الخطأ: " . $e->getTraceAsString() . "\n";
}

echo "\n=== تم انتهاء الاختبار ===\n";

?>
