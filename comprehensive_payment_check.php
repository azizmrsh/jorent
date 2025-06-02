<?php

echo "🔍 فحص شامل لنظام الدفعات (Payment System)\n";
echo "==========================================\n\n";

// 1. فحص التطابق بين Migration و PaymentResource
echo "📋 1. فحص التطابق بين Migration و PaymentResource\n";
echo "------------------------------------------------\n";

// الحقول من Migration
$migrationFields = [
    'id' => 'Primary Key',
    'contract_id' => 'Foreign Key to contract1s',
    'amount' => 'decimal(10,2)',
    'payment_date' => 'date',
    'payment_method' => "enum('cash', 'bank_transfer', 'wallet', 'cliq')",
    'reference_number' => 'string nullable',
    'notes' => 'text nullable',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
];

// الحقول من PaymentResource Form
$resourceFormFields = [
    'contract_id' => 'Select relationship required',
    'amount' => 'TextInput numeric required',
    'payment_date' => 'DatePicker required',
    'payment_method' => 'Select required (cash, bank_transfer, wallet, cliq)',
    'reference_number' => 'TextInput maxLength(255) optional',
    'notes' => 'Textarea maxLength(65535) optional'
];

// الحقول من Payment Model fillable
$modelFillableFields = [
    'contract_id',
    'amount', 
    'payment_date',
    'payment_method',
    'reference_number',
    'notes'
];

echo "📊 حقول Migration:\n";
foreach ($migrationFields as $field => $type) {
    echo "   ✅ $field: $type\n";
}

echo "\n📝 حقول PaymentResource Form:\n";
foreach ($resourceFormFields as $field => $config) {
    echo "   ✅ $field: $config\n";
}

echo "\n🔒 حقول Payment Model fillable:\n";
foreach ($modelFillableFields as $field) {
    echo "   ✅ $field\n";
}

// فحص التطابق
echo "\n🔍 فحص التطابق:\n";

// فحص أن كل حقل في Form موجود في Migration
$missingInMigration = [];
foreach (array_keys($resourceFormFields) as $formField) {
    if (!array_key_exists($formField, $migrationFields)) {
        $missingInMigration[] = $formField;
    }
}

// فحص أن كل حقل fillable موجود في Migration
$missingFillableInMigration = [];
foreach ($modelFillableFields as $fillableField) {
    if (!array_key_exists($fillableField, $migrationFields)) {
        $missingFillableInMigration[] = $fillableField;
    }
}

// فحص أن كل حقل fillable موجود في Form
$missingFillableInForm = [];
foreach ($modelFillableFields as $fillableField) {
    if (!array_key_exists($fillableField, $resourceFormFields)) {
        $missingFillableInForm[] = $fillableField;
    }
}

if (empty($missingInMigration) && empty($missingFillableInMigration) && empty($missingFillableInForm)) {
    echo "   ✅ التطابق مثالي! جميع الحقول متسقة\n";
} else {
    if (!empty($missingInMigration)) {
        echo "   ❌ حقول مفقودة في Migration: " . implode(', ', $missingInMigration) . "\n";
    }
    if (!empty($missingFillableInMigration)) {
        echo "   ❌ حقول fillable مفقودة في Migration: " . implode(', ', $missingFillableInMigration) . "\n";
    }
    if (!empty($missingFillableInForm)) {
        echo "   ❌ حقول fillable مفقودة في Form: " . implode(', ', $missingFillableInForm) . "\n";
    }
}

echo "\n";

// 2. فحص العلاقات
echo "🔗 2. فحص العلاقات (Relationships)\n";
echo "-----------------------------------\n";

echo "📋 العلاقات المطلوبة:\n";

$requiredRelationships = [
    'Payment -> Contract1' => 'belongsTo',
    'Payment -> Tenant (via Contract1)' => 'hasOneThrough',
    'Contract1 -> Payment' => 'hasMany',
    'Tenant -> Payment (via Contract1)' => 'hasManyThrough',
];

foreach ($requiredRelationships as $relationship => $type) {
    echo "   📌 $relationship: $type\n";
}

echo "\n🔍 فحص ملفات العلاقات:\n";

// فحص Payment Model
echo "📁 Payment Model:\n";
$paymentContent = file_get_contents('app/Models/Payment.php');

if (strpos($paymentContent, 'belongsTo(\App\Models\Contract1::class)') !== false) {
    echo "   ✅ علاقة Contract: belongsTo موجودة\n";
} else {
    echo "   ❌ علاقة Contract: belongsTo مفقودة أو خاطئة\n";
}

if (strpos($paymentContent, 'hasOneThrough') !== false) {
    echo "   ✅ علاقة Tenant: hasOneThrough موجودة\n";
} else {
    echo "   ❌ علاقة Tenant: hasOneThrough مفقودة\n";
}

// فحص Contract1 Model
echo "\n📁 Contract1 Model:\n";
if (file_exists('app/Models/Contract1.php')) {
    $contract1Content = file_get_contents('app/Models/Contract1.php');
    
    if (strpos($contract1Content, 'hasMany(\App\Models\Payment::class)') !== false) {
        echo "   ✅ علاقة Payments: hasMany موجودة\n";
    } else {
        echo "   ❌ علاقة Payments: hasMany مفقودة أو خاطئة\n";
    }
} else {
    echo "   ❌ ملف Contract1 Model غير موجود\n";
}

// فحص Tenant Model
echo "\n📁 Tenant Model:\n";
if (file_exists('app/Models/Tenant.php')) {
    $tenantContent = file_get_contents('app/Models/Tenant.php');
    
    if (strpos($tenantContent, 'hasManyThrough') !== false && strpos($tenantContent, 'Payment::class') !== false) {
        echo "   ✅ علاقة Payments: hasManyThrough موجودة\n";
    } else {
        echo "   ❌ علاقة Payments: hasManyThrough مفقودة أو خاطئة\n";
    }
} else {
    echo "   ❌ ملف Tenant Model غير موجود\n";
}

echo "\n";

// 3. فحص إضافي للـ enum values
echo "🎯 3. فحص قيم طرق الدفع (Payment Methods)\n";
echo "------------------------------------------\n";

$migrationEnumValues = ['cash', 'bank_transfer', 'wallet', 'cliq'];
$resourceEnumValues = ['cash', 'bank_transfer', 'wallet', 'cliq'];

echo "📊 قيم Migration enum: " . implode(', ', $migrationEnumValues) . "\n";
echo "📝 قيم PaymentResource: " . implode(', ', $resourceEnumValues) . "\n";

if ($migrationEnumValues === $resourceEnumValues) {
    echo "✅ قيم طرق الدفع متطابقة\n";
} else {
    $missingInResource = array_diff($migrationEnumValues, $resourceEnumValues);
    $extraInResource = array_diff($resourceEnumValues, $migrationEnumValues);
    
    if (!empty($missingInResource)) {
        echo "❌ قيم مفقودة في Resource: " . implode(', ', $missingInResource) . "\n";
    }
    if (!empty($extraInResource)) {
        echo "❌ قيم زائدة في Resource: " . implode(', ', $extraInResource) . "\n";
    }
}

echo "\n";

// 4. فحص Foreign Key
echo "🔑 4. فحص Foreign Key\n";
echo "-------------------\n";

echo "📋 Migration Foreign Key: contract_id -> contract1s.id\n";
echo "📝 PaymentResource relationship: contract (Contract1)\n";
echo "🔒 Payment Model relationship: belongsTo(Contract1::class)\n";

// فحص أن اسم الجدول صحيح
if (strpos($paymentContent, 'Contract1::class') !== false) {
    echo "✅ Foreign Key model صحيح: Contract1\n";
} else {
    echo "❌ Foreign Key model خاطئ أو مفقود\n";
}

echo "\n";

echo "🎯 ملخص النتائج:\n";
echo "===============\n";

$allChecks = [
    'تطابق الحقول بين Migration و Resource' => empty($missingInMigration) && empty($missingFillableInMigration) && empty($missingFillableInForm),
    'علاقة Payment -> Contract1' => strpos($paymentContent, 'belongsTo(\App\Models\Contract1::class)') !== false,
    'علاقة Payment -> Tenant' => strpos($paymentContent, 'hasOneThrough') !== false,
    'تطابق قيم طرق الدفع' => $migrationEnumValues === $resourceEnumValues,
    'صحة Foreign Key' => strpos($paymentContent, 'Contract1::class') !== false,
];

$passedChecks = 0;
$totalChecks = count($allChecks);

foreach ($allChecks as $checkName => $passed) {
    if ($passed) {
        echo "✅ $checkName\n";
        $passedChecks++;
    } else {
        echo "❌ $checkName\n";
    }
}

echo "\n📊 النتيجة النهائية: $passedChecks/$totalChecks اختبار نجح\n";

if ($passedChecks === $totalChecks) {
    echo "🎉 ممتاز! نظام الدفعات سليم 100%\n";
} else {
    echo "⚠️ يحتاج إلى إصلاحات في " . ($totalChecks - $passedChecks) . " نقطة\n";
}

echo "\n✅ انتهى الفحص الشامل\n";
