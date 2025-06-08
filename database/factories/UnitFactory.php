<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

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
        
        $unitNames = [
            'شقة', 'مكتب', 'محل تجاري', 'استوديو', 'مستودع'
        ];
        
        return [
            'name' => $unitNames[array_rand($unitNames)] . ' رقم ' . $this->faker->numberBetween(1, 100),
            'unit_number' => $this->faker->numberBetween(1, 100),
            'area' => $this->faker->randomFloat(2, 50, 300),
            'images' => json_encode([
                'uploads/units/' . $this->faker->uuid . '.jpg',
                'uploads/units/' . $this->faker->uuid . '.jpg',
            ]),
            'property_id' => function () {
                // استخدام property موجود أو إنشاء واحد جديد
                return \App\Models\Property::inRandomOrder()->first()?->id 
                    ?? \App\Models\Property::factory()->create()->id;
            },
            'unit_details' => $unitDetails,
            'features' => $unitFeatures,
            'status' => $this->faker->randomElement(['available', 'rented', 'under_maintenance', 'unavailable', 'reserved']),
            'unit_type' => $this->faker->randomElement($unitTypes),
            'rental_price' => $this->faker->randomFloat(2, 500, 5000),
            'notes' => $this->faker->optional(0.7)->randomElement([
                'وحدة مميزة بإطلالة جميلة',
                'تحتاج صيانة بسيطة',
                'مفروشة بالكامل',
                'موقع ممتاز قريب من الخدمات',
                \ArabicFakerHelper::getRandomNote()
            ]),
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
