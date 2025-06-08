<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run()
    {
        // إنشاء 50 عنوان بأسماء مدن وشوارع أردنية
        Address::factory(50)->create();
        
        $this->command->info('✅ تم إنشاء 50 عنوان بأسماء مدن وشوارع أردنية');
    }
}
