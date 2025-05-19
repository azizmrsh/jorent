<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'landlord_name',
        'tenant_id',
        'unit_id',
        'property_id',
        'start_date',
        'end_date',
        'due_date',
        'rent_amount',
        'terms_and_conditions_extra',
        'status',
        'tenant_signature',
        'landlord_signature',
        'witness1_signature',
        'witness2_signature',
        'hired_date',
        'hired_by',
        'created_at',
        'updated_at',

    ];

    protected $casts = [
        'tenant_signature' => 'array',
        'landlord_signature' => 'array',
        'witness1_signature' => 'array',
        'witness2_signature' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'hired_date' => 'date',
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
    
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}