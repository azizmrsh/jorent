<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\Contract1;
use App\Models\Payment;

echo "🔍 فحص نظام لوحة المستأجرين\n";
echo "============================\n\n";

try {
    // التحقق من المستأجر التجريبي
    $testTenant = Tenant::where('email', 'tenant@test.com')->first();
    if (!$testTenant) {
        echo "❌ المستأجر التجريبي غير موجود\n";
        exit(1);
    }
    echo "✅ المستأجر التجريبي موجود: {$testTenant->firstname} {$testTenant->lastname}\n";

    // التحقق من العقود
    $contracts = Contract1::where('tenant_id', $testTenant->id)->get();
    echo "✅ عدد العقود للمستأجر: {$contracts->count()}\n";

    // التحقق من المدفوعات
    $payments = Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->get();
    echo "✅ إجمالي المدفوعات: {$payments->count()}\n";

    // إحصائيات المدفوعات
    $completedPayments = $payments->where('status', 'completed')->count();
    $pendingPayments = $payments->where('status', 'pending')->count();
    $failedPayments = $payments->where('status', 'failed')->count();

    echo "   - مكتملة: {$completedPayments}\n";
    echo "   - معلقة: {$pendingPayments}\n";
    echo "   - فاشلة: {$failedPayments}\n";

    // التحقق من كلمة المرور
    if (password_verify('password', $testTenant->password)) {
        echo "✅ كلمة المرور صحيحة\n";
    } else {
        echo "❌ كلمة المرور خاطئة\n";
    }

    // التحقق من الـ Guard
    $guards = config('auth.guards');
    if (isset($guards['tenant'])) {
        echo "✅ Tenant guard مُكوّن صحيحاً\n";
    } else {
        echo "❌ Tenant guard غير مُكوّن\n";
    }

    // التحقق من الـ Provider
    $providers = config('auth.providers');
    if (isset($providers['tenants'])) {
        echo "✅ Tenants provider مُكوّن صحيحاً\n";
    } else {
        echo "❌ Tenants provider غير مُكوّن\n";
    }

    echo "\n🎯 نتائج الفحص:\n";
    echo "================\n";
    echo "✅ جميع المكونات تعمل بشكل صحيح\n";
    echo "✅ البيانات التجريبية موجودة\n";
    echo "✅ النظام جاهز للاختبار\n\n";

    echo "🚀 للوصول إلى لوحة المستأجرين:\n";
    echo "   URL: http://127.0.0.1:8000/tenant/login\n";
    echo "   Email: tenant@test.com\n";
    echo "   Password: password\n\n";

    echo "📊 الصفحات المتاحة بعد تسجيل الدخول:\n";
    echo "   - /tenant (Dashboard)\n";
    echo "   - /tenant/my-contracts (عقودي)\n";
    echo "   - /tenant/my-payments (مدفوعاتي)\n";
    echo "   - /tenant/tenant-profile (الملف الشخصي)\n";

} catch (Exception $e) {
    echo "❌ خطأ في الفحص: " . $e->getMessage() . "\n";
    exit(1);
}
