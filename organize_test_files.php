<?php

echo "🧹 تنظيف وترتيب ملفات الاختبار\n";
echo "==============================\n\n";

// قائمة ملفات الاختبار المتعلقة بـ Tenant Panel
$tenantTestFiles = [
    'create_test_tenant.php',
    'create_test_data.php', 
    'verify_tenant_panel.php',
    'test_tenant_authentication.php'
];

// إنشاء مجلد للملفات التجريبية إذا لم يكن موجوداً
$testDir = 'tenant-panel-tests';
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
    echo "✅ تم إنشاء مجلد: {$testDir}\n";
}

// نقل الملفات
foreach ($tenantTestFiles as $file) {
    if (file_exists($file)) {
        $newPath = $testDir . '/' . $file;
        if (copy($file, $newPath)) {
            echo "✅ تم نسخ: {$file} → {$newPath}\n";
        } else {
            echo "❌ فشل نسخ: {$file}\n";
        }
    } else {
        echo "⚠️ الملف غير موجود: {$file}\n";
    }
}

echo "\n📋 إنشاء ملف README للاختبارات...\n";

$readmeContent = "# 🧪 ملفات اختبار نظام لوحة المستأجرين

## 📁 الملفات المتاحة:

### 1. **create_test_tenant.php**
- ينشئ مستأجر تجريبي أساسي
- Email: tenant@test.com
- Password: password

### 2. **create_test_data.php**
- ينشئ بيانات شاملة للاختبار
- عقود ومدفوعات متنوعة
- بيانات واقعية

### 3. **verify_tenant_panel.php**
- يتحقق من صحة النظام
- يفحص التكوينات
- يعرض إحصائيات

### 4. **test_tenant_authentication.php**
- يختبر نظام المصادقة
- يتحقق من الـ Guards
- يختبر تسجيل الدخول

## 🚀 كيفية الاستخدام:

```bash
# تشغيل الخادم
php artisan serve

# إنشاء البيانات التجريبية
php tenant-panel-tests/create_test_data.php

# اختبار المصادقة
php tenant-panel-tests/test_tenant_authentication.php

# التحقق من النظام
php tenant-panel-tests/verify_tenant_panel.php
```

## 🌐 الوصول للنظام:

- **لوحة المستأجرين:** http://127.0.0.1:8000/tenant/login
- **Email:** tenant@test.com
- **Password:** password

## 📊 المسارات المتاحة:

- `/tenant` - Dashboard
- `/tenant/my-contracts` - العقود
- `/tenant/my-payments` - المدفوعات
- `/tenant/tenant-profile` - الملف الشخصي
";

file_put_contents($testDir . '/README.md', $readmeContent);
echo "✅ تم إنشاء: {$testDir}/README.md\n";

echo "\n🎯 تم الانتهاء من التنظيم!\n";
echo "📁 جميع ملفات الاختبار في مجلد: {$testDir}/\n";
