<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

require_once database_path('helpers/ArabicFakerHelper.php');

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        // مولد ملاحظات أكثر تنوعاً وواقعية
        $monthsArabic = [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];
        
        $paymentTypes = [
            'إيجار شهر ' . $this->faker->randomElement($monthsArabic) . ' ' . $this->faker->year(),
            'دفعة مقدمة للعقد - ' . $this->faker->numberBetween(1, 6) . ' أشهر',
            'تأمين العقار لسنة ' . $this->faker->year(),
            'فاتورة كهرباء شهر ' . $this->faker->randomElement($monthsArabic),
            'فاتورة ماء وصرف صحي - ' . $this->faker->randomElement($monthsArabic),
            'رسوم صيانة دورية - الربع ' . $this->faker->numberBetween(1, 4),
            'رسوم إدارية وخدمات شهرية',
            'دفعة متأخرة مع غرامة ' . $this->faker->numberBetween(5, 25) . '%',
            'صيانة طارئة - ' . $this->faker->randomElement(['سباكة', 'كهرباء', 'تكييف', 'طلاء']),
            'رسوم تجديد العقد لسنة ' . $this->faker->year(),
            'تعويض عن أضرار في الوحدة رقم ' . $this->faker->numberBetween(1, 50),
            'رد جزئي لتأمين العقار',
            'مستحقات نظافة شهرية',
            'رسوم حراسة وأمن',
            'فاتورة غاز شهر ' . $this->faker->randomElement($monthsArabic),
            'رسوم صيانة مصعد',
            'تكلفة إصلاحات عامة في المبنى',
            'رسوم خدمات إنترنت ومرافق',
            'دفعة استثنائية لطوارئ',
            'مبلغ مسترد من وديعة سابقة'
        ];

        $paymentMethod = $this->faker->randomElement(['cash', 'bank_transfer', 'wallet', 'cliq']);
        $bankName = null;
        $transferRef = null;

        // إضافة بيانات بنكية فقط للتحويلات البنكية والمحافظ
        if (in_array($paymentMethod, ['bank_transfer', 'wallet', 'cliq'])) {
            $bankName = \ArabicFakerHelper::getRandomBankName();
            $transferRef = \ArabicFakerHelper::generateTransferReference();
        }

        // أسماء مستلمين ثابتة (مالكين العقارات)
        $propertyOwners = [
            'شركة الاستثمار العقاري الأردنية',
            'مؤسسة الأملاك التجارية',
            'شركة البناء والتطوير',
            'مكتب إدارة الأملاك',
            'شركة العقارات المتميزة',
            'أحمد محمد الخالدي',
            'فاطمة عبدالله الزعبي',
            'محمد علي المجالي',
            'سارة حسن الطراونة',
            'عبدالرحمن يوسف العجلوني',
            'نورا سامي البطاينة',
            'خالد محمود الكركي'
        ];
        
        return [
            'contract_id' => function() {
                return \App\Models\Contract1::inRandomOrder()->first()?->id ?? \App\Models\Contract1::factory()->create()->id;
            },
            'amount' => $this->faker->randomFloat(2, 100, 3000), // نطاق أوسع للمبالغ
            'payment_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'payment_method' => $paymentMethod,
            'payer_name' => \ArabicFakerHelper::getRandomArabicName(), // اسم المستأجر الدافع
            'receiver_name' => $this->faker->randomElement($propertyOwners), // اسم المالك المستلم
            'bank_name' => $bankName,
            'transfer_reference' => $transferRef,
            'notes' => $this->faker->randomElement([
                ...$paymentTypes, // الملاحظات المفصلة المحلية
                \ArabicFakerHelper::getRandomPaymentNote(), // ملاحظات إضافية من المساعد
                \ArabicFakerHelper::getRandomNote() // ملاحظات عامة
            ]),
        ];
    }
}
