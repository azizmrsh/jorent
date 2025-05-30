<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Contract1;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        $paymentMethods = ['cash', 'bank_transfer', 'wallet', 'cliq'];
        $paymentStatuses = ['pending', 'completed', 'failed', 'cancelled'];
        $currencies = ['JOD', 'USD', 'EUR'];
        $banks = ['البنك الأهلي الأردني', 'البنك العربي', 'بنك الإسكان', 'بنك القاهرة عمان', 'البنك الإسلامي الأردني'];
        
        $paymentMethod = $this->faker->randomElement($paymentMethods);
        
        return [
            'contract_id' => Contract1::factory(),
            'payment_number' => $this->generatePaymentNumber(),
            'amount' => $this->faker->randomFloat(2, 100, 2000),
            'currency' => $this->faker->randomElement($currencies),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'payer_name' => $this->faker->name(),
            'receiver_name' => $this->faker->name(),
            'payment_method' => $paymentMethod,
            'bank_name' => $paymentMethod === 'bank_transfer' ? $this->faker->randomElement($banks) : null,
            'transaction_id' => $paymentMethod === 'bank_transfer' ? $this->faker->uuid() : null,
            'reference_number' => $this->faker->optional(0.7)->numerify('REF-########'),
            'payment_status' => $this->faker->randomElement($paymentStatuses),
            'notes' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    private function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        $number = $this->faker->numberBetween(1, 9999);
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
    }

    // State للدفعات المكتملة
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'completed',
            ];
        });
    }

    // State للدفعات النقدية
    public function cash()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_method' => 'cash',
                'bank_name' => null,
                'transaction_id' => null,
            ];
        });
    }

    // State للتحويلات البنكية
    public function bankTransfer()
    {
        return $this->state(function (array $attributes) {
            $banks = ['البنك الأهلي الأردني', 'البنك العربي', 'بنك الإسكان', 'بنك القاهرة عمان'];
            
            return [
                'payment_method' => 'bank_transfer',
                'bank_name' => $this->faker->randomElement($banks),
                'transaction_id' => $this->faker->uuid(),
            ];
        });
    }
}
