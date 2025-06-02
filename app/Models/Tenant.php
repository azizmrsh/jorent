<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // إضافة هذا
use Illuminate\Notifications\Notifiable;
use App\Notifications\TenantVerifyEmail;


class Tenant extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'midname',
        'lastname',
        'email',
        'email_verified_at',
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
        'hired_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'hired_date' => 'date',
        'email_verified_at' => 'datetime',
    ];


    public function contracts()
    {
        return $this->hasMany(Contract1::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            Contract1::class,
            'tenant_id', // Foreign key on contracts table
            'contract_id', // Foreign key on payments table
            'id', // Local key on tenants table
            'id' // Local key on contracts table
        );
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

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new TenantVerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Determine if the tenant has verified their email address.
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given tenant's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Get the key name for the tenant.
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }




}