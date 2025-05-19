<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition()
    {
        return [
            'contract_id' => \App\Models\Contract::factory(),
            'tenant_id' => \App\Models\Tenant::factory(),
            'invoice_number' => $this->faker->unique()->randomNumber(),
            'issue_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['paid', 'unpaid', 'overdue']),
        ];
    }
}
