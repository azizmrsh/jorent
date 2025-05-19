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
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'payment_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['completed', 'pending', 'failed']),
            'contract_id' => \App\Models\Contract::factory(),
        ];
    }
}
