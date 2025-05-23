<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract1 extends Model
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
        'status',
        'terms_and_conditions_extra',
        'tenant_signature',
        'landlord_signature',
        'witness1_signature',
        'witness2_signature',
        'hired_date',
        'hired_by',
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

    
}
// Compare this snippet from app/Models/Tenant.php: