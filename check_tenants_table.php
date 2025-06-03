<?php

echo "🔍 فحص جدول tenants\n";
echo "==================\n\n";

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "📊 فحص أعمدة جدول tenants:\n";
    echo "-------------------------\n";
    
    $columns = DB::select('DESCRIBE tenants');
    
    $hasEmailVerifiedAt = false;
    foreach ($columns as $column) {
        echo "- " . $column->Field . " (" . $column->Type . ")\n";
        if ($column->Field === 'email_verified_at') {
            $hasEmailVerifiedAt = true;
        }
    }
    
    echo "\n";
    if ($hasEmailVerifiedAt) {
        echo "✅ عمود email_verified_at موجود!\n";
        
        // عد المستأجرين
        $totalTenants = DB::table('tenants')->count();
        $verifiedTenants = DB::table('tenants')->whereNotNull('email_verified_at')->count();
        
        echo "📈 إحصائيات:\n";
        echo "- إجمالي المستأجرين: $totalTenants\n";
        echo "- المؤكدين: $verifiedTenants\n";
        echo "- غير المؤكدين: " . ($totalTenants - $verifiedTenants) . "\n";
        
    } else {
        echo "❌ عمود email_verified_at غير موجود!\n";
        echo "🛠️ يجب تشغيل migration لإضافته\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
