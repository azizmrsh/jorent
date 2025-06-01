<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'contract_id',
        'amount',
        'payment_date',
        'payment_method',
        'payer_name',         // اسم الدافع
        'receiver_name',      // اسم المستلم
        'bank_name',          // اسم البنك/المحفظة
        'transfer_reference', // الرقم المرجعي للحوالة
        'reference_number',   // رقم مرجعي إضافي
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the contract that owns the payment.
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Contract1::class, 'contract_id');
    }

    /**
     * Get the tenant through the contract.
     */
    public function tenant(): HasOneThrough
    {
        return $this->hasOneThrough(
            \App\Models\Tenant::class,
            \App\Models\Contract1::class,
            'id', // Foreign key on contracts table
            'id', // Foreign key on tenants table
            'contract_id', // Local key on payments table
            'tenant_id' // Local key on contracts table
        );
    }

    /**
     * Scope to filter payments by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('payment_date', [$from, $to]);
    }

    /**
     * Scope to filter payments by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' jd';
    }

    /**
     * Get payment method in Arabic.
     */
    public function getPaymentMethodArabicAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'cash',
            'bank_transfer' => 'bank transfer',
            'wallet' => 'wallet',
            'cliq' => 'cliq',
            default => $this->payment_method,
        };
    }
}
