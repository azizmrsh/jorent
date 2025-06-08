<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

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
        $jordanianGovernorates = [
            'عمان', 'إربد', 'الزرقاء', 'البلقاء', 'الكرك', 'معان', 'العقبة', 
            'مادبا', 'جرش', 'عجلون', 'الطفيلة', 'المفرق'
        ];
        
        $districts = [
            'وسط البلد', 'الأشرفية', 'جبل الحسين', 'جبل اللويبدة', 'الدوار السابع',
            'الصويفية', 'العبدلي', 'الشميساني', 'أم أذينة', 'خلدا'
        ];
        
        $streetNames = [
            'شارع الملك حسين', 'شارع الملكة رانيا', 'شارع الاستقلال', 'شارع الجامعة',
            'شارع الحرية', 'شارع النصر', 'شارع السلام', 'شارع الوحدة'
        ];
        
        return [
            'property_id' => null, // Will be set when created by PropertyFactory
            'country' => 'الأردن',
            'governorate' => $jordanianGovernorates[array_rand($jordanianGovernorates)],
            'city' => \ArabicFakerHelper::getRandomCity(),
            'district' => $districts[array_rand($districts)],
            'building_number' => $this->faker->numberBetween(1, 999),
            'plot_number' => 'قطعة-' . $this->faker->numberBetween(1, 999),
            'basin_number' => 'حوض-' . $this->faker->numberBetween(1, 99),
            'property_number' => 'عقار-' . $this->faker->numberBetween(1000, 9999),
            'street_name' => $streetNames[array_rand($streetNames)],
        ];
    }
}
