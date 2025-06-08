<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Application;

// إنشاء Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 بدء تنظيف قاعدة البيانات (باستثناء جدول users)...\n";

try {
    // تعطيل foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    // قائمة الجداول المراد تنظيفها (باستثناء users وجداول النظام)
    $tablesToClean = [
        'contract1s',
        'payments', 
        'units',
        'properties',
        'tenants',
        'addresses',
        'accs'
    ];
    
    echo "📋 الجداول المراد تنظيفها:\n";
    foreach ($tablesToClean as $table) {
        echo "   - $table\n";
    }
    echo "\n";
    
    // تنظيف كل جدول
    foreach ($tablesToClean as $table) {
        try {
            $count = DB::table($table)->count();
            DB::table($table)->truncate();
            echo "✅ تم تنظيف جدول $table (كان يحتوي على $count سجل)\n";
        } catch (Exception $e) {
            echo "❌ خطأ في تنظيف جدول $table: " . $e->getMessage() . "\n";
        }
    }
    
    // إعادة تفعيل foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "\n🎯 تم تنظيف قاعدة البيانات بنجاح (باستثناء جدول users)!\n";
    echo "📊 جدول users محفوظ ولم يتم المساس به.\n\n";
    
    // عرض حالة جدول users
    $usersCount = DB::table('users')->count();
    echo "👥 عدد المستخدمين المحفوظين: $usersCount\n\n";
    
} catch (Exception $e) {
    echo "❌ خطأ عام: " . $e->getMessage() . "\n";
}
