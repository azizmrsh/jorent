<?php

echo "🚀 إنشاء مستأجر تجريبي للاختبار\n";
echo "================================\n\n";

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

try {
    // إنشاء مستأجر تجريبي
    $tenant = Tenant::create([
        'firstname' => 'محمد',
        'midname' => 'أحمد',
        'lastname' => 'الاختبار',
        'email' => 'tenant@test.com',
        'password' => Hash::make('password123'),
        'phone' => '+96279123456',
        'address' => 'عمان - الجاردنز',
        'birth_date' => '1990-01-01',
        'nationality' => 'أردني',
        'status' => 'active',
        'document_type' => 'id',
        'document_number' => '1234567890',
        'hired_date' => now(),
        'hired_by' => 'النظام',
        'email_verified_at' => now(),
    ]);
    
    echo "✅ تم إنشاء المستأجر التجريبي بنجاح!\n";
    echo "📧 البريد الإلكتروني: tenant@test.com\n";
    echo "🔑 كلمة المرور: password123\n";
    echo "🔗 الرابط: " . config('app.url') . "/tenant\n\n";
    
    echo "📋 معرف المستأجر: {$tenant->id}\n";
    echo "👤 الاسم: {$tenant->firstname} {$tenant->lastname}\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ في إنشاء المستأجر: " . $e->getMessage() . "\n";
}

echo "\n🎯 الآن يمكنك تسجيل الدخول إلى panel المستأجرين على:\n";
echo config('app.url') . "/tenant\n";
