<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract;

class ContractSeeder extends Seeder
{
    public function run()
    {
        // Generate 50 fake contracts
        Contract::factory()->count(50)->create();
    }
}
