<?php

/**
 * Script لتنظيف قاعدة البيانات وإغلاق الاتصالات الزائدة
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🔧 بدء تنظيف قاعدة البيانات...\n";
    
    // إغلاق جميع الاتصالات النشطة
    DB::disconnect();
    echo "✅ تم إغلاق الاتصالات النشطة\n";
    
    // إعادة الاتصال بإعدادات محسنة
    DB::reconnect();
    echo "✅ تم إعادة الاتصال بقاعدة البيانات\n";
    
    // تنظيف جلسات قديمة (إذا كانت تستخدم database)
    $sessionCleanup = DB::table('sessions')
        ->where('last_activity', '<', time() - (60 * 60 * 2)) // حذف الجلسات الأقدم من ساعتين
        ->delete();
    echo "✅ تم حذف {$sessionCleanup} جلسة قديمة\n";
    
    // تنظيف cache قديم (إذا كان يستخدم database)
    try {
        $cacheCleanup = DB::table('cache')
            ->where('expiration', '<', time())
            ->delete();
        echo "✅ تم حذف {$cacheCleanup} عنصر cache منتهي الصلاحية\n";
    } catch (\Exception $e) {
        echo "ℹ️ جدول cache غير موجود أو لا يحتاج تنظيف\n";
    }
    
    // تنظيف failed_jobs قديمة
    try {
        $failedJobsCleanup = DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(7))
            ->delete();
        echo "✅ تم حذف {$failedJobsCleanup} وظيفة فاشلة قديمة\n";
    } catch (\Exception $e) {
        echo "ℹ️ جدول failed_jobs غير موجود أو لا يحتاج تنظيف\n";
    }
    
    // إظهار إحصائيات الاتصال الحالية
    $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
    if (!empty($connections)) {
        echo "📊 عدد الاتصالات النشطة حالياً: " . $connections[0]->Value . "\n";
    }
    
    // إغلاق الاتصال نهائياً
    DB::disconnect();
    echo "✅ تم إغلاق جميع الاتصالات\n";
    
    echo "\n🎉 تم تنظيف قاعدة البيانات بنجاح!\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ في تنظيف قاعدة البيانات: " . $e->getMessage() . "\n";
    exit(1);
}
