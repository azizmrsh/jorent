<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'midname',
        'lastname',
        'email',
        'phone',
        'address',
        'birth_date',
        'profile_photo',
        'password',
        'status',
        'document_type',
        'document_number',
        'document_photo',
        'nationality',
        'hired_date',
        'hired_by',
        'occupation',
        'employer',
        'employer_phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'tenant_signature',
        'landlord_signature'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'hired_date' => 'date',
    ];

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function customInvoices(): HasMany
    {
        return $this->hasMany(CustomInvoice::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->midname} {$this->lastname}";
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address?->full_address ?? 'No address';
    }

    public function getFullAddressWithStreetAttribute(): string
    {
        return $this->address?->full_address_with_street ?? 'No address';
    }

    public function getActiveContractAttribute()
    {
        return $this->contracts()->where('status', 'active')->first();
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalInvoicesAttribute()
    {
        return $this->invoices()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_invoices - $this->total_payments;
    }
}