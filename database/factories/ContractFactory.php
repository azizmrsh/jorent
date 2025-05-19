<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition()
    {
        return [
            'tenant_id' => \App\Models\Tenant::factory(),
            'unit_id' => \App\Models\Unit::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'rent_amount' => $this->faker->randomFloat(2, 1000, 10000),
            'payment_frequency' => $this->faker->randomElement(['monthly', 'quarterly']),
            'status' => $this->faker->randomElement(['active', 'expired', 'terminated', 'pending']),
            'notes' => $this->faker->text(),
            'contract_document' => $this->faker->filePath(),
        ];
    }
}
