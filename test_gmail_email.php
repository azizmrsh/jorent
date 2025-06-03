<?php

echo "🧪 اختبار إرسال البريد الإلكتروني\n";
echo "================================\n\n";

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    echo "📧 محاولة إرسال إيميل تجريبي...\n";
    
    // إرسال إيميل بسيط للاختبار
    Mail::raw('هذا إيميل تجريبي من نظام JoRent للتأكد من عمل البريد الإلكتروني بشكل صحيح.', function ($message) {
        $message->to('osaidalhajj03@gmail.com')
                ->subject('🧪 اختبار البريد الإلكتروني - JoRent');
    });
    
    echo "✅ تم إرسال الإيميل بنجاح!\n";
    echo "📬 تحقق من صندوق الوارد في: osaidalhajj03@gmail.com\n\n";
    
    // فحص إعدادات البريد
    echo "⚙️ إعدادات البريد الحالية:\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في إرسال الإيميل:\n";
    echo "الرسالة: " . $e->getMessage() . "\n";
    echo "الملف: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    
    echo "🔍 أسباب محتملة:\n";
    echo "1. App Password غير صحيح\n";
    echo "2. التحقق بخطوتين غير مفعل في Gmail\n";
    echo "3. مشكلة في الاتصال بالإنترنت\n";
    echo "4. Gmail يحجب الوصول من تطبيقات أقل أماناً\n\n";
    
    echo "🛠️ الحلول:\n";
    echo "1. تأكد من تفعيل التحقق بخطوتين في Gmail\n";
    echo "2. أنشئ App Password جديد\n";
    echo "3. تأكد من عدم وجود مسافات في App Password\n";
}

echo "\n🔄 لمسح cache الإعدادات، شغّل:\n";
echo "php artisan config:clear\n";
