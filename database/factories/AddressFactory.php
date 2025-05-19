<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'country' => $this->faker->country(),
            'governorate' => $this->faker->state(),
            'city' => $this->faker->city(),
            'district' => $this->faker->word(),
            'building_number' => $this->faker->buildingNumber(),
            'plot_number' => $this->faker->randomNumber(),
            'basin_number' => $this->faker->randomNumber(),
            'property_number' => $this->faker->randomNumber(),
            'street_name' => $this->faker->streetName(),
            'property_id' => \App\Models\Property::factory(),
        ];
    }
}
