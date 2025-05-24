<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class PropertyGridController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display the property grid page with filters
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Get all properties with their related units and address data
        $properties = Property::with(['units', 'address'])->get();
        
        // Get unique locations for filter dropdown
        $locations = Address::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');
            
        // Get property types for filter
        $propertyTypes = Property::select('type1')
            ->distinct()
            ->whereNotNull('type1')
            ->orderBy('type1')
            ->pluck('type1');
            
        // Calculate price range for slider from units
        $prices = Unit::whereNotNull('rental_price')
            ->orderBy('rental_price')
            ->pluck('rental_price');
            
        $minPrice = $prices->min() ?? 0;
        $maxPrice = $prices->max() ?? 200000;
        
        return view('property-grid', compact('properties', 'locations', 'propertyTypes', 'minPrice', 'maxPrice'));
    }
    
    /**
     * Filter properties based on request parameters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $query = Property::with(['units', 'address']);
        
        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->whereHas('address', function($q) use ($request) {
                $q->where('city', $request->location);
            });
        }
        
        // Filter by property type
        if ($request->has('property_type') && $request->property_type) {
            $query->where('type1', $request->property_type);
        }
        
        // Filter by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereHas('units', function($q) use ($request) {
                $q->whereBetween('price', [$request->min_price, $request->max_price]);
            });
        }
        
        $properties = $query->get();
        
        return response()->json([
            'properties' => $properties,
            'count' => $properties->count()
        ]);
    }
}