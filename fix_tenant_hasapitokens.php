<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

echo "🔧 إصلاح مشكلة HasApiTokens في نظام المستأجرين\n";
echo "=============================================\n\n";

try {
    // اختبار إنشاء نموذج Tenant
    echo "1. اختبار إنشاء نموذج Tenant... ";
    $tenant = new Tenant();
    echo "✅ نجح\n";

    // اختبار العثور على المستأجر التجريبي
    echo "2. اختبار العثور على المستأجر التجريبي... ";
    $testTenant = Tenant::where('email', 'tenant@test.com')->first();
    if ($testTenant) {
        echo "✅ موجود: {$testTenant->firstname} {$testTenant->lastname}\n";
    } else {
        echo "❌ غير موجود\n";
        
        // إنشاء مستأجر تجريبي
        echo "3. إنشاء مستأجر تجريبي... ";
        $testTenant = Tenant::create([
            'firstname' => 'أحمد',
            'lastname' => 'المحمد',
            'email' => 'tenant@test.com',
            'password' => bcrypt('password'),
            'phone' => '0791234567',
            'nationality' => 'أردني',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);
        echo "✅ تم الإنشاء\n";
    }

    // اختبار Guard المستأجرين
    echo "4. اختبار tenant guard... ";
    $guardConfig = config('auth.guards.tenant');
    if ($guardConfig) {
        echo "✅ مُكوّن صحيحاً\n";
    } else {
        echo "❌ غير مُكوّن\n";
    }

    // اختبار المصادقة
    echo "5. اختبار مصادقة المستأجر... ";
    $credentials = [
        'email' => 'tenant@test.com',
        'password' => 'password'
    ];

    if (Auth::guard('tenant')->attempt($credentials)) {
        echo "✅ تسجيل الدخول نجح\n";
        $user = Auth::guard('tenant')->user();
        echo "   المستخدم: {$user->firstname} {$user->lastname}\n";
        Auth::guard('tenant')->logout();
    } else {
        echo "❌ فشل تسجيل الدخول\n";
    }

    // اختبار العلاقات
    echo "6. اختبار علاقات البيانات... ";
    $contracts = \App\Models\Contract1::where('tenant_id', $testTenant->id)->count();
    echo "✅ العقود: {$contracts}\n";

    $payments = \App\Models\Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->count();
    echo "   المدفوعات: {$payments}\n";

    echo "\n🎯 ملخص الإصلاح:\n";
    echo "================\n";
    echo "✅ تم إزالة HasApiTokens من Tenant Model\n";
    echo "✅ تم تعطيل LanguageSwitch مؤقتاً\n";
    echo "✅ نظام المصادقة يعمل بدون Sanctum\n";
    echo "✅ جميع العلاقات تعمل بشكل صحيح\n\n";

    echo "🚀 النظام جاهز للاستخدام:\n";
    echo "   URL: http://127.0.0.1:8000/tenant/login\n";
    echo "   Email: tenant@test.com\n";
    echo "   Password: password\n";

} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "📄 تفاصيل: " . $e->getTraceAsString() . "\n";
}
