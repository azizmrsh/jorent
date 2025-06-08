<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // إنشاء 50 عقار بأسماء عربية ووصف باللغة العربية، كل عقار مع عنوان
        Property::factory(50)->create();
        
        $this->command->info('✅ تم إنشاء 50 عقار بأسماء عربية ووصف باللغة العربية');
    }
}
