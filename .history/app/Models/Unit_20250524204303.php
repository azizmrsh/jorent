<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units'; // Specify the table name

    protected $fillable = [
        'property_id',
        'name', 
        'unit_number',
        'unit_type',
        'area',
        'unit_details',
        'features',
        'images',
        'status',
        'rental_price',
        'notes'
    ]; // Define fillable attributes

    protected $hidden = [
        'created_at', 
        'updated_at'
    ]; // Hide timestamps in JSON output
    protected $casts = [
        'images' => 'array', // Cast the images column to an array
        'unit_details' => 'array', // Cast the unit_details column to an array
        'features' => 'array', // Cast the features column to an array
    ];

    public function property()
{
    return $this->belongsTo(Property::class);
}

public function contracts()
{
    return $this->hasMany(Contract1::class);
}


}
