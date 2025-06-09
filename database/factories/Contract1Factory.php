<?php

namespace Database\Factories;

use App\Models\Contract1;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

class Contract1Factory extends Factory
{
    protected $model = Contract1::class;

    public function definition()
    {
        $termsAndConditions = [
            'يلتزم المستأجر بدفع الإيجار في الموعد المحدد',
            'يحق للمالك زيارة العقار بعد إخطار مسبق',
            'يمنع إجراء تعديلات على العقار دون موافقة المالك',
            'المستأجر مسؤول عن فواتير الكهرباء والماء',
            'العقد قابل للتجديد بموافقة الطرفين'
        ];
        
        return [
            'landlord_name' => \ArabicFakerHelper::getRandomArabicName(),
            'property_id' => function() {
                return \App\Models\Property::inRandomOrder()->first()?->id ?? \App\Models\Property::factory()->create()->id;
            },
            'tenant_id' => function() {
                return \App\Models\Tenant::inRandomOrder()->first()?->id ?? \App\Models\Tenant::factory()->create()->id;
            },
            'unit_id' => function() {
                return \App\Models\Unit::inRandomOrder()->first()?->id ?? \App\Models\Unit::factory()->create()->id;
            },
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'rent_amount' => $this->faker->randomFloat(2, 300, 2000),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'), // تاريخ الاستحقاق
            'status' => $this->faker->randomElement(['active', 'inactive']), // القيم المسموحة فقط
            'terms_and_conditions_extra' => $termsAndConditions[array_rand($termsAndConditions)],
            'tenant_signature_path' => null,
            'landlord_signature_path' => null,
            'pdf_path' => null,
            'witness1_signature_path' => null,
            'witness2_signature_path' => null,
            'witness1_name' => null,
            'witness2_name' => null,
            'hired_date' => now(),
            'hired_by' => \ArabicFakerHelper::getRandomArabicName(),
        ];
    }
}
