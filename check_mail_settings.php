<?php
echo "🔍 فحص إعدادات البريد الإلكتروني الحالية\n";
echo "==========================================\n\n";

// قراءة ملف .env
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ ملف .env غير موجود!\n";
    exit(1);
}

$envContent = file_get_contents($envPath);
$envLines = explode("\n", $envContent);

echo "📧 الإعدادات الحالية:\n";
echo "--------------------\n";

$mailSettings = [
    'MAIL_MAILER',
    'MAIL_HOST', 
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME'
];

foreach ($envLines as $line) {
    foreach ($mailSettings as $setting) {
        if (strpos($line, $setting . '=') === 0) {
            // إخفاء كلمة المرور
            if ($setting === 'MAIL_PASSWORD') {
                $parts = explode('=', $line, 2);
                if (isset($parts[1]) && !empty($parts[1])) {
                    echo "✅ $setting=***المخفي***\n";
                } else {
                    echo "❌ $setting= (فارغ)\n";
                }
            } else {
                echo ($line ? "✅" : "❌") . " $line\n";
            }
        }
    }
}

echo "\n📋 خيارات البريد الإلكتروني لـ Hostinger:\n";
echo "=====================================\n";
echo "1️⃣ SMTP الخاص بـ Hostinger\n";
echo "   - Host: smtp.hostinger.com\n";
echo "   - Port: 587 (TLS) أو 465 (SSL)\n";
echo "   - يحتاج إيميل صالح من الدومين\n\n";

echo "2️⃣ Gmail SMTP\n";
echo "   - Host: smtp.gmail.com\n";
echo "   - Port: 587\n";
echo "   - يحتاج App Password من Google\n\n";

echo "3️⃣ Resend.com (مجاني)\n";
echo "   - سهل التسجيل\n";
echo "   - API Token بدلاً من SMTP\n";
echo "   - 3000 إيميل شهرياً مجاناً\n\n";

echo "4️⃣ Mailgun (مجاني)\n";
echo "   - 5000 إيميل شهرياً مجاناً\n";
echo "   - API أو SMTP\n\n";

echo "🎯 التوصية: استخدام Resend.com للسهولة\n";
echo "============================================\n";
echo "1. انشئ حساب على https://resend.com\n";
echo "2. احصل على API Key\n";
echo "3. غير الإعدادات في .env\n\n";

echo "💡 إيش تفضل تستخدم؟\n";
echo "   A) Hostinger SMTP (إذا عندك إيميل من الدومين)\n";
echo "   B) Gmail SMTP\n";
echo "   C) Resend.com (مُوصى به)\n";
echo "   D) Mailgun\n";
