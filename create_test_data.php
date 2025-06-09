<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

echo "🌱 إنشاء بيانات تجريبية للمستأجرين\n";
echo "====================================\n\n";

try {
    // إنشاء مستأجر تجريبي إضافي إذا لم يكن موجوداً
    $testTenant = Tenant::firstOrCreate(
        ['email' => 'tenant@test.com'],
        [
            'firstname' => 'أحمد',
            'lastname' => 'المحمد',
            'email' => 'tenant@test.com',
            'password' => Hash::make('password'),
            'phone' => '0791234567',
            'nationality' => 'أردني',
            'birthdate' => '1990-01-01',
            'status' => 'active',
        ]
    );
    echo "✅ تم إنشاء/التحقق من المستأجر التجريبي: {$testTenant->firstname} {$testTenant->lastname}\n";

    // التحقق من وجود عقارات ووحدات
    $property = Property::first();
    if (!$property) {
        echo "❌ لا توجد عقارات في النظام. يرجى إنشاء عقار أولاً.\n";
        exit(1);
    }

    $unit = Unit::where('property_id', $property->id)->first();
    if (!$unit) {
        echo "❌ لا توجد وحدات للعقار. يرجى إنشاء وحدة أولاً.\n";
        exit(1);
    }

    echo "✅ تم العثور على العقار: {$property->name}\n";
    echo "✅ تم العثور على الوحدة: {$unit->name}\n";

    // إنشاء عقد تجريبي
    $contract = Contract1::firstOrCreate(
        [
            'tenant_id' => $testTenant->id,
            'unit_id' => $unit->id,
        ],
        [
            'landlord_name' => 'محمد السالم',
            'property_id' => $property->id,
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->addMonths(9),
            'due_date' => Carbon::now()->day(1)->addMonth(),
            'rent_amount' => $unit->rental_price ?? 500,
            'status' => 'active',
            'hired_date' => Carbon::now()->subMonths(3),
            'hired_by' => 'المدير العام',
        ]
    );
    echo "✅ تم إنشاء/التحقق من العقد رقم: {$contract->id}\n";

    // إنشاء مدفوعات تجريبية
    $paymentMethods = ['cash', 'bank_transfer', 'wallet', 'cliq'];
    $paymentStatuses = ['completed', 'pending', 'failed'];
    
    // مدفوعات الأشهر الماضية
    for ($i = 3; $i >= 1; $i--) {
        $paymentDate = Carbon::now()->subMonths($i)->day(5);
        
        Payment::firstOrCreate(
            [
                'contract_id' => $contract->id,
                'payment_date' => $paymentDate,
            ],
            [
                'amount' => $contract->rent_amount,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payer_name' => $testTenant->firstname . ' ' . $testTenant->lastname,
                'receiver_name' => 'مكتب الإدارة',
                'bank_name' => 'البنك الأهلي الأردني',
                'transfer_reference' => 'REF' . random_int(100000, 999999),
                'status' => 'completed',
                'notes' => 'إيجار شهر ' . $paymentDate->format('Y-m'),
            ]
        );
    }

    // مدفوعة هذا الشهر (معلقة)
    Payment::firstOrCreate(
        [
            'contract_id' => $contract->id,
            'payment_date' => Carbon::now()->day(5),
        ],
        [
            'amount' => $contract->rent_amount,
            'payment_method' => 'bank_transfer',
            'payer_name' => $testTenant->firstname . ' ' . $testTenant->lastname,
            'receiver_name' => 'مكتب الإدارة',
            'bank_name' => 'البنك الأهلي الأردني',
            'transfer_reference' => 'REF' . random_int(100000, 999999),
            'status' => 'pending',
            'notes' => 'إيجار شهر ' . Carbon::now()->format('Y-m'),
        ]
    );

    // مدفوعة الشهر القادم (مستحقة)
    Payment::firstOrCreate(
        [
            'contract_id' => $contract->id,
            'payment_date' => Carbon::now()->addMonth()->day(5),
        ],
        [
            'amount' => $contract->rent_amount,
            'payment_method' => 'cash',
            'payer_name' => $testTenant->firstname . ' ' . $testTenant->lastname,
            'receiver_name' => 'مكتب الإدارة',
            'status' => 'pending',
            'notes' => 'إيجار شهر ' . Carbon::now()->addMonth()->format('Y-m'),
        ]
    );

    echo "✅ تم إنشاء المدفوعات التجريبية\n\n";

    // إحصائيات
    $totalPayments = Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->count();

    $completedPayments = Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->where('status', 'completed')->count();

    $pendingPayments = Payment::whereHas('contract', function ($query) use ($testTenant) {
        $query->where('tenant_id', $testTenant->id);
    })->where('status', 'pending')->count();

    echo "📊 الإحصائيات:\n";
    echo "   - إجمالي العقود: 1\n";
    echo "   - إجمالي المدفوعات: {$totalPayments}\n";
    echo "   - المدفوعات المكتملة: {$completedPayments}\n";
    echo "   - المدفوعات المعلقة: {$pendingPayments}\n\n";

    echo "🎯 يمكنك الآن تسجيل الدخول إلى لوحة المستأجرين:\n";
    echo "   URL: http://127.0.0.1:8000/tenant\n";
    echo "   Email: tenant@test.com\n";
    echo "   Password: password\n\n";

    echo "✅ تم إنشاء جميع البيانات التجريبية بنجاح!\n";

} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "📄 تفاصيل: " . $e->getTraceAsString() . "\n";
    exit(1);
}
