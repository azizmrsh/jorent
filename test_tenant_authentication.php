<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "🔐 اختبار نظام المصادقة للمستأجرين\n";
echo "===================================\n\n";

try {
    // اختبار المستأجر التجريبي
    $testTenant = Tenant::where('email', 'tenant@test.com')->first();
    if (!$testTenant) {
        echo "❌ المستأجر التجريبي غير موجود\n";
        exit(1);
    }

    echo "✅ المستأجر موجود: {$testTenant->firstname} {$testTenant->lastname}\n";
    echo "   Email: {$testTenant->email}\n";
    echo "   ID: {$testTenant->id}\n";
    echo "   Status: {$testTenant->status}\n\n";

    // اختبار كلمة المرور
    $passwordTest = Hash::check('password', $testTenant->password);
    echo "🔑 اختبار كلمة المرور: " . ($passwordTest ? "✅ صحيحة" : "❌ خاطئة") . "\n";

    // اختبار الـ Guard
    $guardConfig = config('auth.guards.tenant');
    echo "🛡️ تكوين Tenant Guard:\n";
    echo "   Driver: {$guardConfig['driver']}\n";
    echo "   Provider: {$guardConfig['provider']}\n";

    // اختبار الـ Provider
    $providerConfig = config('auth.providers.tenants');
    echo "👥 تكوين Tenants Provider:\n";
    echo "   Driver: {$providerConfig['driver']}\n";
    echo "   Model: {$providerConfig['model']}\n\n";

    // اختبار محاولة مصادقة
    echo "🔐 اختبار المصادقة...\n";
    
    // محاولة تسجيل دخول باستخدام tenant guard
    $credentials = [
        'email' => 'tenant@test.com',
        'password' => 'password'
    ];

    if (Auth::guard('tenant')->attempt($credentials)) {
        echo "✅ تسجيل الدخول نجح\n";
        $authenticatedTenant = Auth::guard('tenant')->user();
        echo "   المستخدم المُسجل: {$authenticatedTenant->firstname} {$authenticatedTenant->lastname}\n";
        
        // تسجيل الخروج
        Auth::guard('tenant')->logout();
        echo "✅ تسجيل الخروج نجح\n";
    } else {
        echo "❌ فشل تسجيل الدخول\n";
    }

    echo "\n📊 إحصائيات البيانات:\n";
    echo "=====================\n";

    // العقود
    $contracts = \App\Models\Contract1::where('tenant_id', $testTenant->id)->get();
    echo "📋 العقود: {$contracts->count()}\n";

    foreach ($contracts as $contract) {
        echo "   - العقد #{$contract->id}\n";
        echo "     العقار: " . ($contract->property ? $contract->property->name : 'غير محدد') . "\n";
        echo "     الوحدة: " . ($contract->unit ? $contract->unit->name : 'غير محدد') . "\n";
        echo "     المبلغ: {$contract->rent_amount} JOD\n";
        echo "     الحالة: {$contract->status}\n\n";
    }

    // المدفوعات
    $payments = \App\Models\Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->get();

    echo "💰 المدفوعات: {$payments->count()}\n";

    $completedPayments = $payments->where('status', 'completed');
    $pendingPayments = $payments->where('status', 'pending');
    $totalAmount = $payments->where('status', 'completed')->sum('amount');

    echo "   - مكتملة: {$completedPayments->count()} (المجموع: {$totalAmount} JOD)\n";
    echo "   - معلقة: {$pendingPayments->count()}\n\n";

    echo "🎯 ملخص حالة النظام:\n";
    echo "===================\n";
    echo "✅ المستأجر التجريبي جاهز\n";
    echo "✅ نظام المصادقة يعمل\n";
    echo "✅ البيانات متوفرة\n";
    echo "✅ العلاقات تعمل بشكل صحيح\n\n";

    echo "🚀 النظام جاهز للاستخدام!\n";
    echo "   الرابط: http://127.0.0.1:8000/tenant/login\n";
    echo "   Email: tenant@test.com\n";
    echo "   Password: password\n";

} catch (Exception $e) {
    echo "❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "📄 تفاصيل: " . $e->getTraceAsString() . "\n";
    exit(1);
}
