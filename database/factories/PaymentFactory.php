<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'contract_id' => \App\Models\Contract1::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'payment_date' => $this->faker->date,
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'wallet', 'cliq']),
            'reference_number' => $this->faker->uuid,
            'notes' => $this->faker->paragraph,
        ];
    }
}
