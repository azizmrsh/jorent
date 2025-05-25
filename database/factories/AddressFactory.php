<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => null, // Will be set when created by PropertyFactory
            'country' => $this->faker->country(),
            'governorate' => $this->faker->state(),
            'city' => $this->faker->city(),
            'district' => $this->faker->word(),
            'building_number' => $this->faker->buildingNumber(),
            'plot_number' => $this->faker->numerify('Plot-###'),
            'basin_number' => $this->faker->numerify('Basin-###'),
            'property_number' => $this->faker->numerify('Prop-####'),
            'street_name' => $this->faker->streetName(),
        ];
    }
}
