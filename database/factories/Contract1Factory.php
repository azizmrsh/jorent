<?php

namespace Database\Factories;

use App\Models\Contract1;
use Illuminate\Database\Eloquent\Factories\Factory;

class Contract1Factory extends Factory
{
    protected $model = Contract1::class;

    public function definition()
    {
        return [
            'landlord_name' => $this->faker->name,
            'property_id' => \App\Models\Property::factory(),
            'tenant_id' => \App\Models\Tenant::factory(),
            'unit_id' => \App\Models\Unit::factory(),
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'rent_amount' => $this->faker->randomFloat(2, 500, 2000),
            'due_date' => $this->faker->date,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'terms_and_conditions_extra' => $this->faker->paragraph,
            'tenant_signature_path' => $this->faker->imageUrl(200, 100, 'people', true, 'Tenant Signature'),
            'landlord_signature_path' => $this->faker->imageUrl(200, 100, 'people', true, 'Landlord Signature'),


            'hired_date' => $this->faker->date,
            'hired_by' => $this->faker->name,

        ];
    }
}
