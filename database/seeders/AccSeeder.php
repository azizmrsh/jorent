<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acc;

class AccSeeder extends Seeder
{
    public function run()
    {
        Acc::factory(10)->create();
    }
}
