<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء 100 وحدة سكنية/تجارية بأسماء عربية وملاحظات باللغة العربية (2-3 وحدات لكل عقار)
        Unit::factory()->count(100)->create();
        
        $this->command->info('✅ تم إنشاء 100 وحدة سكنية/تجارية بأسماء عربية وملاحظات باللغة العربية');
    }
}
