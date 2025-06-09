<?php

namespace Database\Factories;

use App\Models\Acc;
use App\Models\Address;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

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
        
        $propertyNames = [
            'مجمع الياسمين السكني',
            'أبراج العاصمة',
            'مركز الأعمال التجاري',
            'فيلا الورود',
            'عمارة النخيل',
            'مجمع الزهور',
            'برج الأمل',
            'مركز الملك حسين التجاري'
        ];
        
        return [
            'name' => $propertyNames[array_rand($propertyNames)] . ' ' . $this->faker->numberBetween(1, 100),
            'description' => 'عقار مميز في موقع استراتيجي بمواصفات عالية الجودة ومرافق متكاملة.',
            'type1' => $this->faker->randomElement(['building', 'villa', 'house', 'warehouse']),
            'type2' => $this->faker->randomElement(['residential', 'commercial', 'industrial']),
            'features' => $features,
            'birth_date' => $this->faker->dateTimeBetween('-30 years', '-1 year'),
            'floors_count' => $this->faker->numberBetween(1, 20),
            'floor_area' => $this->faker->randomFloat(2, 100, 1000),
            'total_area' => $this->faker->randomFloat(2, 1000, 10000),
            // ✅ إضافة الحقول الجديدة المضافة في migration
            'price' => $this->faker->randomFloat(2, 50000, 500000), // السعر بالدينار الأردني
            'main_image' => 'uploads/properties/main_' . $this->faker->uuid . '.jpg',
            'is_for_sale' => $this->faker->boolean(60), // 60% احتمال للبيع
            'is_for_rent' => $this->faker->boolean(80), // 80% احتمال للإيجار
            'acc_id' => function () {
                return \App\Models\Acc::inRandomOrder()->first()?->id ?? \App\Models\Acc::factory()->create()->id;
            },
            'image_path' => 'uploads/properties/' . $this->faker->uuid . '.jpg',
            'address_id' => function () {
                // إنشاء عنوان جديد لكل عقار لضمان التنوع
                return Address::factory()->create()->id;
            },
        ];
    }
    
    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Property $property) {
            // ربط العنوان بالعقار (علاقة مزدوجة للمرونة)
            if ($property->address_id && $property->address) {
                $property->address->update(['property_id' => $property->id]);
            }
        });
    }
}
