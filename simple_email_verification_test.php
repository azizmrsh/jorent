<?php

// اختبار بسيط لنظام التحقق من البريد الإلكتروني
// Simple Email Verification System Test

echo "🔍 فحص نظام التحقق من البريد الإلكتروني\n";
echo "=====================================\n\n";

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

echo "📁 فحص الملفات المطلوبة:\n";
echo "------------------------\n";

$allFilesExist = true;
$existingFiles = 0;
$totalFiles = count($requiredFiles);

foreach ($requiredFiles as $file => $description) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$description}: موجود\n";
        $existingFiles++;
    } else {
        echo "❌ {$description}: مفقود\n";
        $allFilesExist = false;
    }
}

echo "\n";
echo "📊 إحصائيات الملفات: {$existingFiles}/{$totalFiles} موجود\n";
echo "النسبة المئوية: " . round(($existingFiles / $totalFiles) * 100, 1) . "%\n\n";

// فحص محتويات الملفات الأساسية
echo "🔍 فحص محتويات الملفات الأساسية:\n";
echo "--------------------------------\n";

$checks = [];

// فحص User Model
$userModelPath = __DIR__ . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    $userContent = file_get_contents($userModelPath);
    
    $checks['user_implements_must_verify'] = strpos($userContent, 'implements MustVerifyEmail') !== false;
    $checks['user_custom_notification'] = strpos($userContent, 'sendEmailVerificationNotification') !== false;
    $checks['user_custom_notification_import'] = strpos($userContent, 'CustomVerifyEmail') !== false;
}

// فحص Tenant Model
$tenantModelPath = __DIR__ . '/app/Models/Tenant.php';
if (file_exists($tenantModelPath)) {
    $tenantContent = file_get_contents($tenantModelPath);
    
    $checks['tenant_email_verified_field'] = strpos($tenantContent, 'email_verified_at') !== false;
    $checks['tenant_verification_methods'] = strpos($tenantContent, 'sendEmailVerificationNotification') !== false;
    $checks['tenant_notifiable'] = strpos($tenantContent, 'use Notifiable') !== false;
    $checks['tenant_has_verified_method'] = strpos($tenantContent, 'hasVerifiedEmail') !== false;
}

// فحص Routes
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    $checks['user_verification_routes'] = strpos($routesContent, 'verification.notice') !== false;
    $checks['tenant_verification_routes'] = strpos($routesContent, 'tenant.verification.verify') !== false;
    $checks['api_verification_routes'] = strpos($routesContent, '/api/user/verification-status') !== false;
}

// فحص AdminPanelProvider
$adminPanelPath = __DIR__ . '/app/Providers/Filament/AdminPanelProvider.php';
if (file_exists($adminPanelPath)) {
    $adminContent = file_get_contents($adminPanelPath);
    $checks['filament_middleware'] = strpos($adminContent, 'EnsureEmailIsVerified') !== false;
}

// فحص UserResource
$userResourcePath = __DIR__ . '/app/Filament/Resources/UserResource.php';
if (file_exists($userResourcePath)) {
    $userResourceContent = file_get_contents($userResourcePath);
    $checks['user_resource_verification_column'] = strpos($userResourceContent, 'email_verified_at') !== false;
    $checks['user_resource_verification_actions'] = strpos($userResourceContent, 'resend_verification') !== false;
}

// فحص TenantResource
$tenantResourcePath = __DIR__ . '/app/Filament/Resources/TenantResource.php';
if (file_exists($tenantResourcePath)) {
    $tenantResourceContent = file_get_contents($tenantResourcePath);
    $checks['tenant_resource_verification_column'] = strpos($tenantResourceContent, 'email_verified_at') !== false;
    $checks['tenant_resource_verification_actions'] = strpos($tenantResourceContent, 'resend_verification') !== false;
}

// عرض نتائج الفحص
$passedChecks = 0;
$totalChecks = count($checks);

foreach ($checks as $check => $result) {
    $checkName = str_replace('_', ' ', $check);
    $checkName = ucwords($checkName);
    
    if ($result) {
        echo "✅ {$checkName}: تم التطبيق\n";
        $passedChecks++;
    } else {
        echo "❌ {$checkName}: لم يتم التطبيق\n";
    }
}

echo "\n";
echo "📊 إحصائيات الفحوص: {$passedChecks}/{$totalChecks} نجح\n";
echo "النسبة المئوية: " . round(($passedChecks / $totalChecks) * 100, 1) . "%\n\n";

// فحص ملف البيئة
echo "⚙️ فحص إعدادات البريد الإلكتروني:\n";
echo "-----------------------------\n";

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    $mailSettings = [
        'MAIL_MAILER=' => 'Mail Driver',
        'MAIL_HOST=' => 'Mail Host', 
        'MAIL_FROM_ADDRESS=' => 'From Address',
        'MAIL_FROM_NAME=' => 'From Name'
    ];
    
    $configuredSettings = 0;
    foreach ($mailSettings as $setting => $name) {
        if (strpos($envContent, $setting) !== false) {
            echo "✅ {$name}: تم التكوين\n";
            $configuredSettings++;
        } else {
            echo "❌ {$name}: لم يتم التكوين\n";
        }
    }
    
    echo "\nإعدادات البريد: {$configuredSettings}/" . count($mailSettings) . " تم تكوينها\n";
} else {
    echo "❌ ملف .env غير موجود\n";
}

echo "\n";
echo "📋 ملخص التطبيق:\n";
echo "================\n";

$overallScore = (($existingFiles / $totalFiles) + ($passedChecks / $totalChecks)) / 2 * 100;

echo "📁 الملفات: {$existingFiles}/{$totalFiles} (" . round(($existingFiles / $totalFiles) * 100, 1) . "%)\n";
echo "🔍 الفحوص: {$passedChecks}/{$totalChecks} (" . round(($passedChecks / $totalChecks) * 100, 1) . "%)\n";
echo "📊 النتيجة الإجمالية: " . round($overallScore, 1) . "%\n\n";

if ($overallScore >= 90) {
    echo "🎉 ممتاز! نظام التحقق من البريد الإلكتروني مطبق بالكامل\n";
    echo "✨ الخطوات التالية:\n";
    echo "   1. تشغيل المايجريشن: php artisan migrate\n";
    echo "   2. اختبار الأوامر: php artisan user:verify-email --stats\n";
    echo "   3. اختبار الأوامر: php artisan tenant:verify-email --stats\n";
    echo "   4. فتح لوحة التحكم (/admin) والتحقق من الـ widgets\n";
} elseif ($overallScore >= 70) {
    echo "✅ جيد! النظام مطبق بشكل كبير مع بعض النقص البسيط\n";
    echo "🔧 يرجى مراجعة العناصر المفقودة أعلاه\n";
} else {
    echo "⚠️ يحتاج للمزيد من العمل، راجع العناصر المفقودة\n";
}

echo "\n";
echo "📖 للمزيد من التفاصيل: EMAIL_VERIFICATION_SYSTEM_COMPLETE.md\n";
echo "🔧 ملف الاختبار: test_email_verification_system.php\n";

?>
