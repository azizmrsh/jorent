<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Contract1;
use App\Models\Tenant;

echo "🔍 اختبار نظام الدفعات\n";
echo "====================\n\n";

try {
    // Test Payment model instantiation
    echo "1. اختبار إنشاء نموذج Payment... ";
    $payment = new Payment();
    echo "✅ نجح\n";
    
    // Test fillable attributes
    echo "2. اختبار الحقول المسموح تعبئتها... ";
    $fillable = $payment->getFillable();
    $expectedFields = ['contract_id', 'amount', 'payment_date', 'payment_method', 'reference_number', 'notes'];
    $missingFields = array_diff($expectedFields, $fillable);
    
    if (empty($missingFields)) {
        echo "✅ جميع الحقول موجودة\n";
    } else {
        echo "❌ حقول مفقودة: " . implode(', ', $missingFields) . "\n";
    }
    
    // Test table connection
    echo "3. اختبار الاتصال بجدول payments... ";
    $count = Payment::count();
    echo "✅ متصل (إجمالي الدفعات: $count)\n";
    
    // Test relationships
    echo "4. اختبار العلاقات... ";
    
    // Check if we have any payments to test relationships
    $firstPayment = Payment::first();
    if ($firstPayment) {
        echo "\n   - علاقة العقد: ";
        $contract = $firstPayment->contract;
        echo $contract ? "✅ تعمل" : "❌ لا تعمل";
        
        if ($contract) {
            echo "\n   - علاقة المستأجر عبر العقد: ";
            $tenant = $firstPayment->tenant;
            echo $tenant ? "✅ تعمل" : "❌ لا تعمل";
        }
        echo "\n";
    } else {
        echo "⚠️ لا توجد دفعات لاختبار العلاقات\n";
    }
    
    // Test Contract1 has payments relationship
    echo "5. اختبار علاقة العقود بالدفعات... ";
    $contractsWithPayments = Contract1::has('payments')->count();
    echo "✅ يعمل (عقود بدفعات: $contractsWithPayments)\n";
    
    // Test Tenant has payments relationship
    echo "6. اختبار علاقة المستأجرين بالدفعات... ";
    $tenantsWithPayments = Tenant::has('payments')->count();
    echo "✅ يعمل (مستأجرين بدفعات: $tenantsWithPayments)\n";
    
    echo "\n✅ اختبار نظام الدفعات مكتمل بنجاح!\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "في الملف: " . $e->getFile() . " السطر: " . $e->getLine() . "\n";
}
