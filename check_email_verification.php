<?php

echo "🔍 فحص نظام التحقق من البريد الإلكتروني - إصدار مبسط\n";
echo "================================================\n\n";

// الدليل الحالي
$currentDir = __DIR__;

// فحص الملفات المطلوبة
$requiredFiles = [
    'app/Models/User.php' => 'User Model',
    'app/Models/Tenant.php' => 'Tenant Model', 
    'app/Notifications/CustomVerifyEmail.php' => 'Custom User Email Notification',
    'app/Notifications/TenantVerifyEmail.php' => 'Tenant Email Notification',
    'app/Filament/Resources/UserResource.php' => 'User Resource',
    'app/Filament/Resources/TenantResource.php' => 'Tenant Resource',
    'app/Filament/Widgets/EmailVerificationStatsWidget.php' => 'User Stats Widget',
    'app/Filament/Widgets/TenantEmailVerificationStatsWidget.php' => 'Tenant Stats Widget',
    'app/Filament/Widgets/EmailVerificationOverviewWidget.php' => 'Overview Widget',
    'app/Console/Commands/VerifyUserEmail.php' => 'User Verification Command',
    'app/Console/Commands/VerifyTenantEmail.php' => 'Tenant Verification Command',
    'resources/views/auth/verify-email.blade.php' => 'Email Verification View',
    'database/migrations/2025_06_02_233123_add_email_verified_at_to_tenants_table.php' => 'Tenant Migration'
];

echo "📁 فحص وجود الملفات:\n";
echo "--------------------\n";

$existingFiles = 0;
$totalFiles = count($requiredFiles);

foreach ($requiredFiles as $file => $description) {
    $fullPath = $currentDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    if (file_exists($fullPath)) {
        echo "✅ {$description}\n";
        $existingFiles++;
    } else {
        echo "❌ {$description} (غير موجود: {$file})\n";
    }
}

$filePercentage = round(($existingFiles / $totalFiles) * 100, 1);
echo "\n📊 نسبة الملفات الموجودة: {$existingFiles}/{$totalFiles} ({$filePercentage}%)\n\n";

// فحص محتويات الملفات
echo "🔍 فحص محتويات الملفات:\n";
echo "----------------------\n";

$contentChecks = [];

// فحص User Model
$userModelPath = $currentDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'User.php';
if (file_exists($userModelPath)) {
    $userContent = file_get_contents($userModelPath);
    
    if (strpos($userContent, 'implements MustVerifyEmail') !== false) {
        echo "✅ User Model: تطبيق MustVerifyEmail\n";
        $contentChecks['user_must_verify'] = true;
    } else {
        echo "❌ User Model: لم يتم تطبيق MustVerifyEmail\n";
        $contentChecks['user_must_verify'] = false;
    }
    
    if (strpos($userContent, 'sendEmailVerificationNotification') !== false) {
        echo "✅ User Model: custom notification method\n";
        $contentChecks['user_custom_notification'] = true;
    } else {
        echo "❌ User Model: لا يوجد custom notification method\n";
        $contentChecks['user_custom_notification'] = false;
    }
} else {
    echo "❌ User Model: الملف غير موجود\n";
    $contentChecks['user_must_verify'] = false;
    $contentChecks['user_custom_notification'] = false;
}

// فحص Tenant Model
$tenantModelPath = $currentDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Tenant.php';
if (file_exists($tenantModelPath)) {
    $tenantContent = file_get_contents($tenantModelPath);
    
    if (strpos($tenantContent, 'email_verified_at') !== false) {
        echo "✅ Tenant Model: email_verified_at field\n";
        $contentChecks['tenant_email_field'] = true;
    } else {
        echo "❌ Tenant Model: لا يوجد email_verified_at field\n";
        $contentChecks['tenant_email_field'] = false;
    }
    
    if (strpos($tenantContent, 'use Notifiable') !== false) {
        echo "✅ Tenant Model: Notifiable trait\n";
        $contentChecks['tenant_notifiable'] = true;
    } else {
        echo "❌ Tenant Model: لا يوجد Notifiable trait\n";
        $contentChecks['tenant_notifiable'] = false;
    }
    
    if (strpos($tenantContent, 'hasVerifiedEmail') !== false) {
        echo "✅ Tenant Model: verification methods\n";
        $contentChecks['tenant_methods'] = true;
    } else {
        echo "❌ Tenant Model: لا توجد verification methods\n";
        $contentChecks['tenant_methods'] = false;
    }
} else {
    echo "❌ Tenant Model: الملف غير موجود\n";
    $contentChecks['tenant_email_field'] = false;
    $contentChecks['tenant_notifiable'] = false;
    $contentChecks['tenant_methods'] = false;
}

// فحص Routes
$routesPath = $currentDir . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    if (strpos($routesContent, 'verification.notice') !== false) {
        echo "✅ Routes: user verification routes\n";
        $contentChecks['user_routes'] = true;
    } else {
        echo "❌ Routes: لا توجد user verification routes\n";
        $contentChecks['user_routes'] = false;
    }
    
    if (strpos($routesContent, 'tenant.verification.verify') !== false) {
        echo "✅ Routes: tenant verification routes\n";
        $contentChecks['tenant_routes'] = true;
    } else {
        echo "❌ Routes: لا توجد tenant verification routes\n";
        $contentChecks['tenant_routes'] = false;
    }
} else {
    echo "❌ Routes: ملف web.php غير موجود\n";
    $contentChecks['user_routes'] = false;
    $contentChecks['tenant_routes'] = false;
}

// فحص UserResource  
$userResourcePath = $currentDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Filament' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'UserResource.php';
if (file_exists($userResourcePath)) {
    $userResourceContent = file_get_contents($userResourcePath);
    
    if (strpos($userResourceContent, 'resend_verification') !== false) {
        echo "✅ UserResource: verification actions\n";
        $contentChecks['user_resource_actions'] = true;
    } else {
        echo "❌ UserResource: لا توجد verification actions\n";
        $contentChecks['user_resource_actions'] = false;
    }
} else {
    echo "❌ UserResource: الملف غير موجود\n";
    $contentChecks['user_resource_actions'] = false;
}

// فحص TenantResource
$tenantResourcePath = $currentDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Filament' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'TenantResource.php';
if (file_exists($tenantResourcePath)) {
    $tenantResourceContent = file_get_contents($tenantResourcePath);
    
    if (strpos($tenantResourceContent, 'resend_verification') !== false) {
        echo "✅ TenantResource: verification actions\n";
        $contentChecks['tenant_resource_actions'] = true;
    } else {
        echo "❌ TenantResource: لا توجد verification actions\n";
        $contentChecks['tenant_resource_actions'] = false;
    }
} else {
    echo "❌ TenantResource: الملف غير موجود\n";
    $contentChecks['tenant_resource_actions'] = false;
}

// حساب نسبة نجاح فحص المحتويات
$passedContentChecks = array_sum($contentChecks);
$totalContentChecks = count($contentChecks);
$contentPercentage = round(($passedContentChecks / $totalContentChecks) * 100, 1);

echo "\n📊 نسبة نجاح فحص المحتويات: {$passedContentChecks}/{$totalContentChecks} ({$contentPercentage}%)\n\n";

// النتيجة الإجمالية
echo "📋 النتيجة الإجمالية:\n";
echo "===================\n";

$overallPercentage = ($filePercentage + $contentPercentage) / 2;

echo "📁 وجود الملفات: {$filePercentage}%\n";
echo "🔍 صحة المحتويات: {$contentPercentage}%\n";
echo "🎯 النتيجة النهائية: " . round($overallPercentage, 1) . "%\n\n";

if ($overallPercentage >= 90) {
    echo "🎉 ممتاز! نظام التحقق من البريد الإلكتروني مطبق بالكامل!\n";
    echo "\n✨ الخطوات التالية:\n";
    echo "   1️⃣ تشغيل المايجريشن: php artisan migrate\n";
    echo "   2️⃣ اختبار commands: php artisan user:verify-email --stats\n";
    echo "   3️⃣ اختبار commands: php artisan tenant:verify-email --stats\n";
    echo "   4️⃣ فتح Admin Panel (/admin) والتحقق من الـ widgets\n";
    echo "   5️⃣ اختبار إرسال الإيميلات\n";
} elseif ($overallPercentage >= 70) {
    echo "✅ جيد جداً! النظام مطبق بشكل كبير\n";
    echo "🔧 راجع العناصر المفقودة أعلاه لإكمال التطبيق\n";
} elseif ($overallPercentage >= 50) {
    echo "⚠️ مقبول، لكن يحتاج للمزيد من العمل\n";
    echo "🛠️ راجع العناصر المفقودة والمحتويات غير الصحيحة\n";
} else {
    echo "❌ يحتاج إعادة تطبيق، العديد من العناصر مفقودة\n";
    echo "📖 راجع الوثائق في EMAIL_VERIFICATION_SYSTEM_COMPLETE.md\n";
}

echo "\n📚 الموارد المفيدة:\n";
echo "   📖 الوثائق الشاملة: EMAIL_VERIFICATION_SYSTEM_COMPLETE.md\n";
echo "   🧪 ملف الاختبار: simple_email_verification_test.php\n";
echo "   🔧 ملف الاختبار المتقدم: test_email_verification_system.php\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🏁 انتهى الفحص\n";

?>
