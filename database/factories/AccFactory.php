<?php

namespace Database\Factories;

use App\Models\Acc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Acc>
 */
class AccFactory extends Factory
{
    protected $model = Acc::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = \ArabicFakerHelper::getArabicFirstNames();
        $lastNames = \ArabicFakerHelper::getArabicLastNames();
        
        return [
            'firstname' => $firstNames[array_rand($firstNames)],
            'midname' => $this->faker->optional(0.6)->randomElement($firstNames),
            'lastname' => $lastNames[array_rand($lastNames)],
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => \ArabicFakerHelper::generateJordanianPhone(),
            'address' => \ArabicFakerHelper::getRandomCity(),
            'birth_date' => \ArabicFakerHelper::getRandomBirthdate(),
            'profile_photo' => null,
            'password' => Hash::make('password'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'document_type' => $this->faker->randomElement(['passport', 'id', 'driver_license']),
            'document_number' => \ArabicFakerHelper::generateNationalId(),
            'document_photo' => null,
            'nationality' => 'أردني',
            'hired_date' => now(),
            'hired_by' => \ArabicFakerHelper::getRandomArabicName(),
        ];
    }
}