<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acc;

class AccSeeder extends Seeder
{
    public function run()
    {
        // إنشاء 50 حساب مالك عقار بأسماء عربية وبيانات أردنية
        Acc::factory(50)->create();
        
        $this->command->info('✅ تم إنشاء 50 حساب مالك عقار بأسماء عربية وبيانات أردنية');
    }
}
