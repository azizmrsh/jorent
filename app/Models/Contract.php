<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'start_date',
        'end_date',
        'rent_amount',
        'payment_frequency',
        'terms_and_conditions',
        'terms_and_conditions_extra',
        'status',
        'contract_document',
        'hired_date',
        'hired_by',
        'landlord_name',
        'tenant_signature',
        'witness_signature',
        'landlord_signature',
        'property_id',
        'governorate',
        'city',
        'district',
        'building_number',
        'plot_number',
        'basin_number',
        'property_number',
        'street_name'
    ];

    protected $casts = [
        
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}