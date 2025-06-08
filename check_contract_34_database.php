<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract1;

echo "=== فحص العقد رقم 34 في قاعدة البيانات ===\n";
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
        echo "- توقيع المستأجر: " . ($contract->tenant_signature ?? 'غير موجود') . "\n";
        echo "- توقيع المؤجر: " . ($contract->landlord_signature ?? 'غير موجود') . "\n";
        echo "- تاريخ الإنشاء: " . $contract->created_at . "\n";
        echo "- تاريخ آخر تحديث: " . $contract->updated_at . "\n\n";
        
        // فحص ملفات التوقيع
        if ($contract->tenant_signature) {
            $tenantSigPath = public_path('uploads/contracts/signatures/' . $contract->tenant_signature);
            if (file_exists($tenantSigPath)) {
                echo "✓ ملف توقيع المستأجر موجود: " . $contract->tenant_signature . "\n";
                echo "  الحجم: " . number_format(filesize($tenantSigPath)) . " بايت\n";
            } else {
                echo "✗ ملف توقيع المستأجر مفقود: " . $contract->tenant_signature . "\n";
            }
        }
        
        if ($contract->landlord_signature) {
            $landlordSigPath = public_path('uploads/contracts/signatures/' . $contract->landlord_signature);
            if (file_exists($landlordSigPath)) {
                echo "✓ ملف توقيع المؤجر موجود: " . $contract->landlord_signature . "\n";
                echo "  الحجم: " . number_format(filesize($landlordSigPath)) . " بايت\n";
            } else {
                echo "✗ ملف توقيع المؤجر مفقود: " . $contract->landlord_signature . "\n";
            }
        }
        
    } else {
        echo "✗ لم يتم العثور على العقد رقم 34\n";
        
        // البحث عن أحدث العقود
        echo "\nالبحث عن أحدث العقود:\n";
        $recentContracts = Contract1::orderBy('id', 'desc')->take(5)->get();
        
        if ($recentContracts->count() > 0) {
            echo "أحدث 5 عقود:\n";
            foreach ($recentContracts as $c) {
                echo "- العقد رقم: {$c->id}, المستأجر: {$c->tenant_id}, المؤجر: {$c->landlord_id}\n";
            }
        } else {
            echo "لا توجد عقود في النظام\n";
        }
    }

} catch (Exception $e) {
    echo "✗ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى الفحص ===\n";

?>
