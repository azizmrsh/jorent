<?php

echo "=== اختبار التوقيع للعقد رقم 34 ===\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

// البحث عن ملفات التوقيع في المجلد المحلي
$signatureDir = 'public/uploads/contracts/signatures';
$contractId = 34;

echo "1. فحص مجلد التوقيعات:\n";
echo str_repeat("-", 50) . "\n";

if (is_dir($signatureDir)) {
    echo "✓ مجلد التوقيعات موجود: $signatureDir\n";
    
    // البحث عن ملفات التوقيع للعقد 34
    $files = glob($signatureDir . "/*contract_{$contractId}_*");
    $files = array_merge($files, glob($signatureDir . "/*{$contractId}*"));
    
    if (empty($files)) {
        echo "⚠️ لم يتم العثور على ملفات توقيع للعقد $contractId\n";
        
        // عرض جميع الملفات الموجودة
        $allFiles = glob($signatureDir . "/*");
        if (!empty($allFiles)) {
            echo "\nالملفات الموجودة في مجلد التوقيعات:\n";
            foreach ($allFiles as $file) {
                if (is_file($file)) {
                    $size = filesize($file);
                    $modified = date('Y-m-d H:i:s', filemtime($file));
                    echo "  - " . basename($file) . " (الحجم: $size بايت، آخر تعديل: $modified)\n";
                }
            }
        } else {
            echo "  مجلد التوقيعات فارغ\n";
        }
    } else {
        echo "✓ تم العثور على ملفات التوقيع للعقد $contractId:\n";
        foreach ($files as $file) {
            $size = filesize($file);
            $modified = date('Y-m-d H:i:s', filemtime($file));
            echo "  - " . basename($file) . " (الحجم: $size بايت، آخر تعديل: $modified)\n";
            
            // التحقق من نوع الملف
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            echo "    نوع الملف: $extension\n";
            
            // إذا كان الملف صورة، التحقق من صحته
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                $imageInfo = getimagesize($file);
                if ($imageInfo) {
                    echo "    أبعاد الصورة: {$imageInfo[0]}x{$imageInfo[1]} بكسل\n";
                } else {
                    echo "    ⚠️ ملف الصورة قد يكون تالفاً\n";
                }
            }
        }
    }
} else {
    echo "✗ مجلد التوقيعات غير موجود: $signatureDir\n";
}

echo "\n2. اختبار إنشاء توقيع جديد:\n";
echo str_repeat("-", 50) . "\n";

// إنشاء ملف توقيع تجريبي
$testSignatureFile = $signatureDir . "/test_signature_contract_{$contractId}_" . time() . ".txt";

if (is_dir($signatureDir) && is_writable($signatureDir)) {
    $signatureData = "توقيع تجريبي للعقد رقم $contractId\nالتاريخ: " . date('Y-m-d H:i:s') . "\nتم إنشاؤه بواسطة اختبار النظام";
    
    if (file_put_contents($testSignatureFile, $signatureData) !== false) {
        echo "✓ تم إنشاء ملف توقيع تجريبي بنجاح\n";
        echo "  المسار: " . basename($testSignatureFile) . "\n";
        
        // التحقق من إمكانية قراءة الملف
        if (file_get_contents($testSignatureFile) === $signatureData) {
            echo "✓ تم التحقق من إمكانية قراءة الملف\n";
        } else {
            echo "✗ خطأ في قراءة الملف\n";
        }
        
        // حذف الملف التجريبي
        unlink($testSignatureFile);
        echo "✓ تم حذف الملف التجريبي\n";
    } else {
        echo "✗ فشل في إنشاء ملف توقيع تجريبي\n";
    }
} else {
    echo "✗ مجلد التوقيعات غير قابل للكتابة\n";
}

echo "\n3. فحص إعدادات Laravel للملفات:\n";
echo str_repeat("-", 50) . "\n";

// فحص إعدادات Laravel
if (file_exists('config/filesystems.php')) {
    echo "✓ ملف إعدادات النظام موجود\n";
    
    $config = file_get_contents('config/filesystems.php');
    if (strpos($config, "'uploads'") !== false) {
        echo "✓ إعدادات uploads موجودة\n";
    } else {
        echo "⚠️ إعدادات uploads غير موجودة\n";
    }
} else {
    echo "✗ ملف إعدادات النظام غير موجود\n";
}

echo "\n=== خلاصة الاختبار ===\n";
echo "المجلد: $signatureDir\n";
echo "العقد المطلوب: $contractId\n";
echo "التاريخ: " . date('Y-m-d H:i:s') . "\n";
echo "\nالخطوات التالية:\n";
echo "- تحقق من وجود ملفات التوقيع في مجلد uploads\n";
echo "- اختبر رفع التوقيع من خلال واجهة الإدارة\n";
echo "- تأكد من حفظ التوقيع في قاعدة البيانات\n";

?>
