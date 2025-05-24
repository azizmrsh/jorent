<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

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
            'document_type' => 'ID',
            'document_number' => $this->faker->randomNumber(8),
            'document_photo' => $this->faker->imageUrl,
            'nationality' => $this->faker->country,
            'hired_date' => $this->faker->date,
            'hired_by' => $this->faker->name,
            'occupation' => $this->faker->jobTitle,
            'employer' => $this->faker->company,
            'employer_phone' => $this->faker->phoneNumber,
            'emergency_contact_name' => $this->faker->name,
            'emergency_contact_phone' => $this->faker->phoneNumber,
            'notes' => $this->faker->paragraph,
        ];
    }
}
