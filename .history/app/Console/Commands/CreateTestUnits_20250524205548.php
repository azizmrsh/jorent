<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Console\Command;

class CreateTestUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-units';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test units for property grid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test units...');
        
        $properties = Property::all();
        
        if ($properties->isEmpty()) {
            $this->error('No properties found! Please create properties first.');
            return 1;
        }

        $this->info("Found {$properties->count()} properties");
        
        $unitTypes = ['apartment', 'studio', 'office', 'villa'];
        $unitCount = 0;
        
        foreach ($properties as $property) {
            $unitsToCreate = rand(2, 4);
            $this->info("Creating {$unitsToCreate} units for property: {$property->name}");
            
            for ($i = 1; $i <= $unitsToCreate; $i++) {
                try {
                    $unit = Unit::create([
                        'property_id' => $property->id,
                        'name' => "Unit {$i} - {$property->name}",
                        'unit_number' => $i,
                        'unit_type' => $unitTypes[array_rand($unitTypes)],
                        'area' => rand(60, 200),
                        'rental_price' => rand(400, 1200),
                        'status' => 'available',
                        'notes' => 'Test unit created for property grid demo',
                        'unit_details' => [
                            ['detail_name' => 'bedrooms', 'detail_value' => rand(1, 3)],
                            ['detail_name' => 'bathrooms', 'detail_value' => rand(1, 2)]
                        ],
                        'features' => [
                            ['feature_name' => 'furnished', 'feature_value' => 'Yes']
                        ],
                        'images' => [
                            'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=800'
                        ]
                    ]);
                    
                    $this->info("✓ Created: {$unit->name} - $${$unit->rental_price}");
                    $unitCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to create unit {$i} for {$property->name}: " . $e->getMessage());
                }
            }
        }
        
        $this->info("Successfully created {$unitCount} units!");
        $this->info("Total units in database: " . Unit::count());
        
        return 0;
    }
}