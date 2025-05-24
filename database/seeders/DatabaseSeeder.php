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
            UserSeeder::class,
            PaymentSeeder::class,
            Contract1Seeder::class,
        ]);
    }
}
