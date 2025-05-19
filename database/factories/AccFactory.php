<?php

namespace Database\Factories;

use App\Models\Acc;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccFactory extends Factory
{
    protected $model = Acc::class;

    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName(),
            'midname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->dateTime(),
            'phone' => $this->faker->phoneNumber(),
            'password' => bcrypt('password'), // Default password
        ];
    }
}