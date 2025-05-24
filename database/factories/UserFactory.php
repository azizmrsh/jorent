<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'midname' => $this->faker->optional()->firstName(),
            'lastname' => $this->faker->lastName(),
            'role' => $this->faker->randomElement(['admin', 'user', 'manager']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password for all users
            'remember_token' => Str::random(10),
            'phone' => $this->faker->phoneNumber(),
            'phone_verified_at' => now(),
            'address' => $this->faker->address(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'profile_photo' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'phone_verified_at' => null,
        ]);
    }
    
    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
