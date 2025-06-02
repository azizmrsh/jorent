<?php

namespace Database\Factories;

use App\Models\Acc;
use App\Models\Address;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create features array with random amenities
        $features = [
            'parking' => $this->faker->boolean(70),
            'security' => $this->faker->boolean(60),
            'gym' => $this->faker->boolean(40),
            'swimming_pool' => $this->faker->boolean(30),
            'elevator' => $this->faker->boolean(80),
            'garden' => $this->faker->boolean(50),
        ];
        
        return [
            'name' => 'Property ' . $this->faker->word() . ' ' . $this->faker->randomNumber(3),
            'description' => $this->faker->paragraph(),
            'type1' => $this->faker->randomElement(['building', 'villa', 'house', 'warehouse']),
            'type2' => $this->faker->randomElement(['residential', 'commercial', 'industrial']),
            'features' => $features,
            'birth_date' => $this->faker->dateTimeBetween('-30 years', '-1 year'),
            'floors_count' => $this->faker->numberBetween(1, 20),
            'floor_area' => $this->faker->randomFloat(2, 100, 1000),
            'total_area' => $this->faker->randomFloat(2, 1000, 10000),
            'acc_id' => function () {
                return Acc::factory()->create()->id;
            },
            'image_path' => 'uploads/properties/' . $this->faker->uuid . '.jpg', // مسار صورة تجريبي
            'address_id' => function () {
                return Address::factory()->create()->id;
            },
        ];
    }
}
