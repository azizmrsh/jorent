<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract1;

class Contract1Seeder extends Seeder
{
    public function run()
    {
        Contract1::factory(10)->create();
    }
}
