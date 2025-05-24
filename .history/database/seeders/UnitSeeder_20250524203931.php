<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $properties = Property::all();
        
        if ($properties->isEmpty()) {
            $this->command->error('No properties found! Please create properties first.');
            return;
        }

        $unitTypes = ['apartment', 'studio', 'office', 'shop', 'warehouse', 'villa', 'house'];
        $statuses = ['available', 'rented', 'under_maintenance', 'unavailable'];
        
        foreach ($properties as $property) {
            // Create 2-4 units per property
            $unitsCount = rand(2, 4);
            
            for ($i = 1; $i <= $unitsCount; $i++) {
                $unitType = $unitTypes[array_rand($unitTypes)];
                $area = rand(50, 300);
                $bedrooms = rand(1, 4);
                $bathrooms = rand(1, 3);
                $floor = rand(1, 10);
                
                Unit::create([
                    'property_id' => $property->id,
                    'name' => "Unit {$i} - {$property->name}",
                    'unit_number' => $i,
                    'unit_type' => $unitType,
                    'area' => $area,
                    'unit_details' => [
                        [
                            'detail_name' => 'bedrooms',
                            'detail_value' => $bedrooms
                        ],
                        [
                            'detail_name' => 'bathrooms', 
                            'detail_value' => $bathrooms
                        ],
                        [
                            'detail_name' => 'floor',
                            'detail_value' => $floor
                        ],
                        [
                            'detail_name' => 'Area',
                            'detail_value' => $area
                        ],
                        [
                            'detail_name' => 'kitchen',
                            'detail_value' => 1
                        ]
                    ],
                    'features' => [
                        [
                            'feature_name' => 'furnished',
                            'feature_value' => rand(0, 1) ? 'Yes' : 'No'
                        ],
                        [
                            'feature_name' => 'elevator',
                            'feature_value' => rand(0, 1) ? 'Yes' : 'No'
                        ],
                        [
                            'feature_name' => 'camera_security',
                            'feature_value' => rand(0, 1) ? 'Yes' : 'No'
                        ]
                    ],
                    'status' => $statuses[array_rand($statuses)],
                    'rental_price' => rand(200, 1500),
                    'notes' => "Beautiful {$unitType} with {$bedrooms} bedrooms and {$bathrooms} bathrooms.",
                    'images' => [
                        'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=800',
                        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800',
                        'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=800'
                    ]
                ]);
            }
        }
        
        $this->command->info('Units seeded successfully!');
    }
}