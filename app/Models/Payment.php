<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'contract_id',
        'payment_number',
        'amount',
        'currency',
        'payment_date',
        'payer_name',
        'receiver_name',
        'payment_method',
        'bank_name',
        'transaction_id',
        'reference_number',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // العلاقة مع العقد
    public function contract()
    {
        return $this->belongsTo(\App\Models\Contract1::class, 'contract_id');
    }

    // العلاقة مع المستأجر عبر العقد
    public function tenant()
    {
        return $this->hasOneThrough(
            \App\Models\Tenant::class,
            \App\Models\Contract1::class,
            'id', // المفتاح الخارجي في جدول العقود
            'id', // المفتاح الخارجي في جدول المستأجرين
            'contract_id', // المفتاح المحلي في جدول المدفوعات
            'tenant_id' // المفتاح المحلي في جدول العقود
        );
    }

    // دالة لتوليد رقم دفعة فريد
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        
        // التحقق من وجود العمود أولاً
        try {
            // البحث عن آخر رقم في نفس الشهر
            $lastPayment = self::where('payment_number', 'like', "{$prefix}-{$year}{$month}-%")
                              ->orderBy('payment_number', 'desc')
                              ->first();
            
            if ($lastPayment) {
                $lastNumber = (int) substr($lastPayment->payment_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
        } catch (\Exception $e) {
            // إذا كان العمود غير موجود، ابدأ برقم 1
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    // دالة للحصول على المبلغ مع العملة
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    // دالة للحصول على حالة الدفع باللغة العربية
    public function getStatusInArabicAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغي',
            default => $this->payment_status
        };
    }

    // دالة للحصول على طريقة الدفع باللغة العربية
    public function getMethodInArabicAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية',
            'cliq' => 'كليك',
            default => $this->payment_method
        };
    }
}
