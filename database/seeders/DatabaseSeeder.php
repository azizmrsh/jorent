<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AccSeeder::class,
            AddressSeeder::class,
            PropertySeeder::class,
            TenantSeeder::class,
            UnitSeeder::class,
            // UserSeeder::class, // ✅ Excluded as per requirements
            PaymentSeeder::class,
            Contract1Seeder::class,
            PropertyTestSeeder::class, // Added for additional Arabic property examples
        ]);
        
        $this->command->info('🎯 تم إنشاء البيانات التجريبية العربية-الأردنية بنجاح!');
        $this->command->info('✅ تم استبعاد بيانات المستخدمين (User) حسب المطلوب');
        $this->command->info('📍 جميع البيانات تحتوي على أسماء عربية وعناوين أردنية وأرقام هواتف أردنية');
    }
}
