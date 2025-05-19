<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'property_id',
        'country',
        'governorate',
        'city',
        'district',
        'building_number',
        'plot_number',
        'basin_number',
        'property_number',
        'street_name',  
    ];
    protected $casts = [
        'country' => 'string',
        'governorate' => 'string',
        'city' => 'string',
        'district' => 'string',
        'building_number' => 'string',
        'plot_number' => 'string',
        'basin_number' => 'string',
        'property_number' => 'string',
        'street_name' => 'string',  
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $table = 'addresses';
    protected $attributes = [
        'country' => null,
        'governorate' => null,
        'city' => null,
        'district' => null,
        'building_number' => null,
        'plot_number' => null,
        'basin_number' => null,
        'property_number' => null,
        'street_name' => null,  
    ];

    // relationship with table properties one to one //osaidhaj03
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getFullAddressAttribute()
    {
        return $this->country . ', ' . $this->governorate . ', ' . $this->city . ', ' . $this->district . ', ' . $this->building_number . ', ' . $this->plot_number . ', ' . $this->basin_number . ', ' . $this->property_number . ', ' . $this->street_name;
    }
    public function getFullAddressWithStreetAttribute()
    {
        return $this->country . ', ' . $this->governorate . ', ' . $this->city . ', ' . $this->district . ', ' . $this->building_number . ', ' . $this->plot_number . ', ' . $this->basin_number . ', ' . $this->property_number . ', ' . $this->street_name;
    }

}
