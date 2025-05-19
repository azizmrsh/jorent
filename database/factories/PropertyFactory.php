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
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type1' => $this->faker->randomElement(['building', 'villa', 'house', 'warehouse']),
            'type2' => $this->faker->randomElement(['Commercial', 'Residential', 'Industrial']),
            'acc_id' => \App\Models\Acc::factory(),
        ];
    }
}
