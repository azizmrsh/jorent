<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Contract1 extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_name',
        'tenant_id',
        'unit_id',
        'property_id',
        'start_date',
        'end_date',
        'due_date',
        'rent_amount',
        'status',
        'terms_and_conditions_extra',
        'tenant_signature_path',
        'landlord_signature_path',
        'witness1_signature_path',
        'witness2_signature_path',
        'witness1_name',
        'witness2_name',
        'hired_date',
        'hired_by',
        'pdf_path',
    ];

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class);
    }

    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    // Export accessor methods for relationships
    public function getTenantNameAttribute()
    {
        return $this->tenant ? $this->tenant->firstname . ' ' . $this->tenant->lastname : 'N/A';
    }

    public function getTenantPhoneAttribute()
    {
        return $this->tenant?->phone ?? 'N/A';
    }

    public function getTenantEmailAttribute()
    {
        return $this->tenant?->email ?? 'N/A';
    }

    public function getPropertyNameAttribute()
    {
        return $this->property?->name ?? 'N/A';
    }

    public function getUnitNameAttribute()
    {
        return $this->unit?->name ?? 'N/A';
    }

    public function getRentalPriceAttribute()
    {
        return $this->unit?->rental_price ?? 0;
    }

    /**
     * Get the PDF URL for this contract
     */
    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->attributes['pdf_path'] || !file_exists(public_path($this->attributes['pdf_path']))) {
            return null;
        }
        
        return asset($this->attributes['pdf_path']);
    }
    
    /**
     * Check if PDF exists for this contract
     */
    public function hasPdf(): bool
    {
        return $this->attributes['pdf_path'] && file_exists(public_path($this->attributes['pdf_path']));
    }
}