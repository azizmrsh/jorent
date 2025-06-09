<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'name',
        'description',
        'type1', // building, villa, house, warehouse
        'type2', // residential, commercial, industrial
        'features',
        'birth_date',
        'floors_count',
        'floor_area',
        'total_area',
        'acc_id', // foreign key to accs table
        'image_path', // مسار الصورة الرئيسية
        'address_id', // foreign key to addresses table
        'created_at',
        'updated_at',
        
    ];
    protected $attributes = [
        'features' => null,
        'birth_date' => null,
        'floors_count' => null,
        'floor_area' => null,
        'total_area' => null,
    ];
    protected $casts = [
        'features' => 'array',
        'birth_date' => 'date',
        'floors_count' => 'integer',
        'floor_area' => 'decimal:2',
        'total_area' => 'decimal:2',
        'images' => 'array', // Cast the images column to an array
    ];

    protected $table = 'properties';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = json_encode($value);
    }

    /**
     * Get the full address attribute
     */
    public function getFullAddressAttribute(): string
    {
        return $this->address?->full_address ?? 'No address';
    }


    // relationships with table addresses one to one //osaidhaj03
public function address()
{
    return $this->belongsTo(Address::class, 'address_id');
}


public function contracts()
{
    return $this->hasMany(\App\Models\Contract1::class);
}

    // relationship with table acc one to many //osaidhaj03
    public function acc()
    {
        return $this->belongsTo(Acc::class);
    }

    // relationship with table units one to many //osaidhaj03
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // relationship with table tenants one to many //osaidhaj03
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }   
    // relationship with table payments one to many //osaidhaj03
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * 🖼️ Accessor للحصول على رابط الصورة الكامل
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }
        
        // صورة افتراضية إذا لم تكن هناك صورة
        return asset('images/property-placeholder.jpg');
    }
    
    /**
     * 📁 Mutator لحفظ الصورة المرفوعة
     */
    public function setImagePathAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            // حفظ الصورة في مجلد properties
            $filename = time() . '_' . $value->getClientOriginalName();
            $path = $value->storeAs('uploads/properties', $filename, 'public');
            $this->attributes['image_path'] = $path;
        } else {
            $this->attributes['image_path'] = $value;
        }
    }
}