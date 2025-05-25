<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(),
            'midname' => $this->faker->optional()->firstName(),
            'lastname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'profile_photo' => null,
            'password' => Hash::make('password'),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'document_type' => $this->faker->randomElement(['passport', 'id', 'driver_license']),
            'document_number' => $this->faker->numerify('DOC-#######'),
            'document_photo' => null,
            'nationality' => $this->faker->country(),
            'hired_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'hired_by' => $this->faker->name(),
            'occupation' => $this->faker->jobTitle(),
            'employer' => $this->faker->company(),
            'employer_phone' => $this->faker->phoneNumber(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->paragraph(),
            'tenant_signature' => null,
            'landlord_signature' => null
        ];
    }
}
