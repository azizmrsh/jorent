<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract1;

class Contract1Seeder extends Seeder
{
    public function run()
    {
        // إنشاء 50 عقد إيجار بأسماء عربية وشروط باللغة العربية
        Contract1::factory(50)->create();
        
        $this->command->info('✅ تم إنشاء 50 عقد إيجار بأسماء عربية وشروط باللغة العربية');
    }
}
