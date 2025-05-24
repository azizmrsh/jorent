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
            'firstname' => $this->faker->firstName,
            'midname' => $this->faker->lastName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'birth_date' => $this->faker->date,
            'profile_photo' => $this->faker->imageUrl,
            'password' => bcrypt('password'),
            'status' => 'active',
            'document_type' => $this->faker->randomElement(['ID', 'passport', 'driver_license', 'residency_permit', 'other']),
            'document_number' => $this->faker->randomNumber(8),
            'document_photo' => $this->faker->imageUrl,
            'nationality' => $this->faker->country,
            'hired_date' => $this->faker->date,
            'hired_by' => $this->faker->name,
        ];
    }
}
