<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract1;
use Illuminate\Support\Facades\Schema;

echo "=== فحص نظام توقيع الشاهدين ===\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. فحص وجود حقول توقيع الشاهدين في قاعدة البيانات
    echo "1. فحص حقول قاعدة البيانات:\n";
    
    $witness1Column = Schema::hasColumn('contract1s', 'witness1_signature_path');
    $witness2Column = Schema::hasColumn('contract1s', 'witness2_signature_path');
    
    echo "   - حقل witness1_signature_path: " . ($witness1Column ? '✅ موجود' : '❌ مفقود') . "\n";
    echo "   - حقل witness2_signature_path: " . ($witness2Column ? '✅ موجود' : '❌ مفقود') . "\n";
    
    // 2. فحص مجلد حفظ التوقيعات
    echo "\n2. فحص مجلد حفظ التوقيعات:\n";
    $signaturesDir = public_path('uploads/contracts/signatures');
    
    if (is_dir($signaturesDir)) {
        echo "   ✅ مجلد التوقيعات موجود: {$signaturesDir}\n";
        
        // فحص صلاحيات الكتابة
        if (is_writable($signaturesDir)) {
            echo "   ✅ صلاحيات الكتابة متوفرة\n";
        } else {
            echo "   ❌ صلاحيات الكتابة غير متوفرة\n";
        }
        
        // عرض الملفات الموجودة
        $files = scandir($signaturesDir);
        $signatureFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'png';
        });
        
        echo "   📁 عدد ملفات التوقيع الموجودة: " . count($signatureFiles) . "\n";
        
    } else {
        echo "   ❌ مجلد التوقيعات غير موجود\n";
    }

    // 3. اختبار إنشاء ملف توقيع تجريبي
    echo "\n3. اختبار إنشاء ملف توقيع تجريبي:\n";
    
    try {
        // إنشاء صورة تجريبية بسيطة (بيانات PNG صغيرة)
        $testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAHlkSOIeAAAAABJRU5ErkJggg==');
        
        $testFileName = 'contracts/signatures/test_witness_' . time() . '.png';
        $testFilePath = public_path('uploads/' . $testFileName);
        
        // محاولة كتابة الملف
        if (file_put_contents($testFilePath, $testImageData)) {
            echo "   ✅ تم إنشاء ملف التوقيع التجريبي بنجاح\n";
            echo "   📄 المسار: {$testFileName}\n";
            
            // فحص وجود الملف
            if (file_exists($testFilePath)) {
                echo "   ✅ تم التحقق من وجود الملف\n";
                $fileSize = filesize($testFilePath);
                echo "   📊 حجم الملف: {$fileSize} بايت\n";
                
                // حذف الملف التجريبي
                if (unlink($testFilePath)) {
                    echo "   🗑️ تم حذف الملف التجريبي بنجاح\n";
                }
            }
        } else {
            echo "   ❌ فشل في إنشاء ملف التوقيع التجريبي\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ خطأ في اختبار الملف: " . $e->getMessage() . "\n";
    }

    // 4. فحص نموذج Contract1
    echo "\n4. فحص نموذج Contract1:\n";
    
    try {
        $contract = new Contract1();
        $fillable = $contract->getFillable();
        
        $hasWitness1 = in_array('witness1_signature_path', $fillable);
        $hasWitness2 = in_array('witness2_signature_path', $fillable);
        
        echo "   - witness1_signature_path في fillable: " . ($hasWitness1 ? '✅' : '❌') . "\n";
        echo "   - witness2_signature_path في fillable: " . ($hasWitness2 ? '✅' : '❌') . "\n";
        
    } catch (Exception $e) {
        echo "   ❌ خطأ في فحص النموذج: " . $e->getMessage() . "\n";
    }

    // 5. ملخص الحالة
    echo "\n5. ملخص الحالة:\n";
    
    $allGood = $witness1Column && $witness2Column && is_dir($signaturesDir) && is_writable($signaturesDir);
    
    if ($allGood) {
        echo "   🎉 نظام توقيع الشاهدين جاهز بالكامل!\n";
        echo "   📝 يمكن الآن:\n";
        echo "      - إضافة توقيعات الشاهدين في العقود\n";
        echo "      - حفظ التوقيعات في نفس مجلد باقي التوقيعات\n";
        echo "      - عرض التوقيعات في ملف PDF\n";
    } else {
        echo "   ⚠️ هناك مشاكل تحتاج إصلاح:\n";
        if (!$witness1Column) echo "      - حقل witness1_signature_path مفقود\n";
        if (!$witness2Column) echo "      - حقل witness2_signature_path مفقود\n";
        if (!is_dir($signaturesDir)) echo "      - مجلد التوقيعات مفقود\n";
        if (!is_writable($signaturesDir)) echo "      - صلاحيات الكتابة مفقودة\n";
    }

} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "\n";
}

echo "\n=== انتهى فحص نظام توقيع الشاهدين ===\n";

?>
