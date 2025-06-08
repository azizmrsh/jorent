<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        // إنشاء 50 دفعة مالية بملاحظات باللغة العربية
        Payment::factory(50)->create();
        
        $this->command->info('✅ تم إنشاء 50 دفعة مالية بملاحظات باللغة العربية');
    }
}
