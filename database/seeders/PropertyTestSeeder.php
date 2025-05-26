<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Address;
use App\Models\Acc;

class PropertyTestSeeder extends Seeder
{
    public function run()
    {
        // التأكد من وجود حساب
        $acc = Acc::first();
        if (!$acc) {
            $acc = Acc::factory()->create();
        }

        // إنشاء عناوين
        $address1 = Address::create([
            'country' => 'Jordan',
            'governorate' => 'Amman',
            'city' => 'Amman',
            'district' => 'Abdoun',
            'building_number' => '123',
            'street_name' => 'King Abdullah Street'
        ]);

        $address2 = Address::create([
            'country' => 'Jordan',
            'governorate' => 'Amman', 
            'city' => 'Amman',
            'district' => 'Downtown',
            'building_number' => '456',
            'street_name' => 'Rainbow Street'
        ]);

        // إنشاء عقارات مع الحقول الجديدة
        Property::create([
            'name' => 'Luxury Villa',
            'description' => 'Beautiful villa with garden',
            'type1' => 'villa',
            'type2' => 'residential',
            'price' => 450000,
            'floors_count' => 3,
            'total_area' => 600,
            'is_for_sale' => true,
            'is_for_rent' => false,
            'address_id' => $address1->id,
            'acc_id' => $acc->id
        ]);

        Property::create([
            'name' => 'Modern Apartment',
            'description' => 'Downtown apartment',
            'type1' => 'building',
            'type2' => 'residential', 
            'price' => 1200,
            'floors_count' => 1,
            'total_area' => 120,
            'is_for_sale' => false,
            'is_for_rent' => true,
            'address_id' => $address2->id,
            'acc_id' => $acc->id
        ]);

        Property::create([
            'name' => 'Commercial Warehouse',
            'description' => 'Large storage facility',
            'type1' => 'warehouse',
            'type2' => 'commercial',
            'price' => 180000,
            'floors_count' => 1,
            'total_area' => 800,
            'is_for_sale' => true,
            'is_for_rent' => true,
            'address_id' => $address1->id,
            'acc_id' => $acc->id
        ]);
    }
}
