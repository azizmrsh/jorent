<?php

// فحص صحة ملفات PHP في نظام الدفعات
echo "🔍 فحص صحة ملفات نظام الدفعات\n";
echo "================================\n\n";

$files = [
    'app/Models/Payment.php',
    'app/Filament/Resources/PaymentResource.php',
    'app/Filament/Resources/TenantResource/RelationManagers/PaymentsRelationManager.php',
    'database/migrations/2025_05_23_100540_create_payments_table.php'
];

foreach ($files as $file) {
    echo "📁 فحص ملف: $file\n";
    
    if (!file_exists($file)) {
        echo "   ❌ الملف غير موجود\n\n";
        continue;
    }
    
    // فحص صحة PHP syntax
    $output = [];
    $return_var = 0;
    exec("php -l \"$file\" 2>&1", $output, $return_var);
    
    if ($return_var === 0) {
        echo "   ✅ صحة PHP: ممتاز\n";
    } else {
        echo "   ❌ خطأ في PHP:\n";
        foreach ($output as $line) {
            echo "      $line\n";
        }
    }
    
    // فحص حجم الملف
    $size = filesize($file);
    echo "   📊 حجم الملف: " . number_format($size) . " بايت\n";
    
    echo "\n";
}

echo "✅ انتهى فحص الملفات\n";
