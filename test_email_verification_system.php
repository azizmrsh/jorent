<?php

require_once __DIR__ . '/vendor/autoload.php';

// بسيط للتحقق من تطبيق نظام التحقق من البريد الإلكتروني
// Test script for Email Verification System

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
foreach ($requiredFiles as $file => $description) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$description}: موجود\n";
    } else {
        echo "❌ {$description}: مفقود\n";
        $allFilesExist = false;
    }
}

echo "\n";

// فحص محتويات الملفات الأساسية
echo "🔍 فحص محتويات الملفات:\n";
echo "----------------------\n";

// فحص User Model
$userModelPath = __DIR__ . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    $userContent = file_get_contents($userModelPath);
    
    if (strpos($userContent, 'implements MustVerifyEmail') !== false) {
        echo "✅ User Model: تم تطبيق MustVerifyEmail\n";
    } else {
        echo "❌ User Model: لم يتم تطبيق MustVerifyEmail\n";
    }
    
    if (strpos($userContent, 'sendEmailVerificationNotification') !== false) {
        echo "✅ User Model: تم إضافة custom notification method\n";
    } else {
        echo "❌ User Model: لم يتم إضافة custom notification method\n";
    }
}

// فحص Tenant Model
$tenantModelPath = __DIR__ . '/app/Models/Tenant.php';
if (file_exists($tenantModelPath)) {
    $tenantContent = file_get_contents($tenantModelPath);
    
    if (strpos($tenantContent, 'email_verified_at') !== false) {
        echo "✅ Tenant Model: تم إضافة email_verified_at field\n";
    } else {
        echo "❌ Tenant Model: لم يتم إضافة email_verified_at field\n";
    }
    
    if (strpos($tenantContent, 'sendEmailVerificationNotification') !== false) {
        echo "✅ Tenant Model: تم إضافة verification methods\n";
    } else {
        echo "❌ Tenant Model: لم يتم إضافة verification methods\n";
    }
    
    if (strpos($tenantContent, 'use Notifiable') !== false) {
        echo "✅ Tenant Model: تم إضافة Notifiable trait\n";
    } else {
        echo "❌ Tenant Model: لم يتم إضافة Notifiable trait\n";
    }
}

// فحص Routes
echo "\n";
echo "🛣️ فحص الـ Routes:\n";
echo "----------------\n";

$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    if (strpos($routesContent, 'verification.notice') !== false) {
        echo "✅ Routes: تم إضافة user email verification routes\n";
    } else {
        echo "❌ Routes: لم يتم إضافة user email verification routes\n";
    }
    
    if (strpos($routesContent, 'tenant.verification.verify') !== false) {
        echo "✅ Routes: تم إضافة tenant email verification routes\n";
    } else {
        echo "❌ Routes: لم يتم إضافة tenant email verification routes\n";
    }
}

// فحص AdminPanelProvider
echo "\n";
echo "🔐 فحص Filament Middleware:\n";
echo "-------------------------\n";

$adminPanelPath = __DIR__ . '/app/Providers/Filament/AdminPanelProvider.php';
if (file_exists($adminPanelPath)) {
    $adminContent = file_get_contents($adminPanelPath);
    
    if (strpos($adminContent, 'EnsureEmailIsVerified') !== false) {
        echo "✅ AdminPanelProvider: تم إضافة email verification middleware\n";
    } else {
        echo "❌ AdminPanelProvider: لم يتم إضافة email verification middleware\n";
    }
}

// فحص الـ Environment
echo "\n";
echo "⚙️ فحص إعدادات البريد الإلكتروني:\n";
echo "-----------------------------\n";

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    if (strpos($envContent, 'MAIL_MAILER=') !== false) {
        echo "✅ .env: تم تحديد MAIL_MAILER\n";
    } else {
        echo "❌ .env: لم يتم تحديد MAIL_MAILER\n";
    }
    
    if (strpos($envContent, 'MAIL_FROM_ADDRESS=') !== false) {
        echo "✅ .env: تم تحديد MAIL_FROM_ADDRESS\n";
    } else {
        echo "❌ .env: لم يتم تحديد MAIL_FROM_ADDRESS\n";
    }
} else {
    echo "❌ .env: ملف البيئة غير موجود\n";
}

echo "\n";
echo "📊 النتيجة النهائية:\n";
echo "==================\n";

if ($allFilesExist) {
    echo "🎉 تم تطبيق نظام التحقق من البريد الإلكتروني بنجاح!\n";
    echo "\n";
    echo "الخطوات التالية:\n";
    echo "1. تشغيل المايجريشن: php artisan migrate\n";
    echo "2. اختبار الأوامر: php artisan user:verify-email --stats\n";
    echo "3. اختبار الأوامر: php artisan tenant:verify-email --stats\n";
    echo "4. فتح لوحة التحكم والتحقق من الـ widgets\n";
} else {
    echo "⚠️ يوجد ملفات مفقودة، يرجى مراجعة التطبيق.\n";
}

echo "\n";
echo "📖 للمزيد من التفاصيل، راجع ملف: EMAIL_VERIFICATION_SYSTEM_COMPLETE.md\n";
?>
