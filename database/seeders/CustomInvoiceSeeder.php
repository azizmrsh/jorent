<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomInvoice;

class CustomInvoiceSeeder extends Seeder
{
    public function run()
    {
        CustomInvoice::factory(10)->create();
    }
}
