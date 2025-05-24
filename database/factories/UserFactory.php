<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'midname' => $this->faker->lastName,
            'lastname' => $this->faker->lastName,
            'role' => $this->faker->randomElement(['user', 'admin', 'superadmin']),
            'status' => 'active',
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ];
    }
}
