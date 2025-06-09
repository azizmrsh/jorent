<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;

echo "🔍 اختبار methods الـ Tenant\n";
echo "==========================\n\n";

try {
    $tenant = Tenant::where('email', 'tenant@test.com')->first();
    
    if (!$tenant) {
        echo "❌ المستأجر غير موجود\n";
        exit(1);
    }
    
    echo "✅ المستأجر موجود\n";
    echo "📧 Email: {$tenant->email}\n";
    echo "👤 getFullNameAttribute: {$tenant->getFullNameAttribute()}\n";
    echo "🏷️  getFilamentName: {$tenant->getFilamentName()}\n";
    echo "📛 getName: {$tenant->getName()}\n\n";
    
    echo "✅ جميع methods تعمل بشكل صحيح!\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
