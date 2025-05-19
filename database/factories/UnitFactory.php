<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition()
    {
        return [
            'property_id' => \App\Models\Property::factory(),
            'name' => $this->faker->word(),
            'unit_number' => $this->faker->randomNumber(),
        ];
    }
}
