<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitTypes = ['apartment', 'office', 'shop', 'studio', 'warehouse'];
        $unitFeatures = [
            'air_conditioning' => $this->faker->boolean(80),
            'heating' => $this->faker->boolean(70),
            'balcony' => $this->faker->boolean(60),
            'furnished' => $this->faker->boolean(40),
            'pets_allowed' => $this->faker->boolean(30),
        ];
        
        $unitDetails = [
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 3),
            'floor' => $this->faker->numberBetween(0, 20),
            'size' => $this->faker->numberBetween(50, 300),
        ];
        
        return [
            'name' => 'Unit ' . $this->faker->bothify('##??'),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 300, 5000),
            'images' => json_encode([
                $this->faker->imageUrl(640, 480, 'apartment'),
                $this->faker->imageUrl(640, 480, 'apartment'),
            ]),
            'property_id' => function () {
                return Property::factory()->create()->id;
            },
            'unit_details' => $unitDetails,
            'features' => $unitFeatures,
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance', 'reserved']),
            'unit_type' => $this->faker->randomElement($unitTypes),
        ];
    }
    
    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterMaking(function (Unit $unit) {
            // Additional configuration after making the unit
        })->afterCreating(function (Unit $unit) {
            // Additional configuration after creating the unit
        });
    }
    
    /**
     * Indicate that the unit belongs to a specific property.
     */
    public function forProperty(Property $property): Factory
    {
        return $this->state(function (array $attributes) use ($property) {
            return [
                'property_id' => $property->id,
            ];
        });
    }
}
