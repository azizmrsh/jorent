<?php

namespace Database\Factories;

use App\Models\CustomInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomInvoiceFactory extends Factory
{
    protected $model = CustomInvoice::class;

    public function definition()
    {
        return [
            'tenant_id' => \App\Models\Tenant::factory(),
            'contract_id' => \App\Models\Contract::factory(),
            'invoice_number' => $this->faker->unique()->randomNumber(),
            'issue_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['paid', 'unpaid', 'overdue']),
        ];
    }
}
