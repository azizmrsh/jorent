<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->text,
            'type1' => $this->faker->randomElement(['building', 'villa', 'house', 'warehouse']),
            'type2' => $this->faker->randomElement(['Commercial', 'Residential', 'Industrial']),
            'acc_id' => \App\Models\Acc::factory(),
            'birth_date' => $this->faker->date,
            'floors_count' => $this->faker->numberBetween(1, 10),
            'floor_area' => $this->faker->randomFloat(2, 50, 500),
            'total_area' => $this->faker->randomFloat(2, 100, 1000),
            'features' => json_encode(['pool', 'garden', 'garage']),
        ];
    }
}
