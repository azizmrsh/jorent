@extends('layouts.vertical', ['title' => 'Property Grid', 'subTitle' => 'Real Estate'])

@section('css')
@vite(['node_modules/nouislider/dist/nouislider.min.css', 'node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

@section('content')

<div class="row">
    <div class="col-xl-3 col-lg-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div>
                    <h4 class="card-title">Properties</h4>
                    <p class="mb-0" id="property-count">Show {{ $properties->count() }} Properties</p>
                </div>
            </div>
            <div class="card-body border-light">
                <form id="property-filter-form">
                    <label for="property-location" class="form-label">Properties Location</label>
                    <select class="form-control" id="property-location" data-choices data-placeholder="Select City" name="location">
                        <option value="">Choose a city</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>

                    <div class="mb-3">
                        <label for="property-type" class="form-label">Type Of Place</label>
                        <select class="form-control" id="property-type" data-choices data-placeholder="Select Property Type" name="property_type">
                            <option value="">All Types</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                
                <h5 class="text-dark fw-medium my-3">Custom Price Range :</h5>
                <div id="product-price-range" data-slider-size="md" class=""></div>
                <div class="formCost d-flex gap-2 align-items-center mt-3">
                    <input class="form-control form-control-sm text-center" type="text" id="minCost" value="{{ $minPrice }}">
                    <span class="fw-semibold text-muted">to</span>
                    <input class="form-control form-control-sm text-center" type="text" id="maxCost" value="{{ $maxPrice }}">
                </div>
                
                <h5 class="text-dark fw-medium my-3">Accessibility Features :</h5>
                <div class="row g-1">
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input accessibility-filter" type="checkbox" value="rent" id="accessibilityRent">
                            <label class="form-check-label ms-1" for="accessibilityRent">
                                For Rent
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input accessibility-filter" type="checkbox" value="sale" id="accessibilitySale">
                            <label class="form-check-label ms-1" for="accessibilitySale">
                                For Sale
                            </label>
                        </div>
                    </div>
                </div>
                
                <h5 class="text-dark fw-medium my-3">Properties Type :</h5>
                <div class="row g-1">
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input property-type-filter" type="checkbox" value="" id="allProperties" checked>
                            <label class="form-check-label ms-1" for="allProperties">
                                All Properties
                            </label>
                        </div>
                    </div>
                    @foreach($propertyTypes->take(6) as $index => $type)
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input property-type-filter" type="checkbox" value="{{ $type }}" id="propertyType{{ $index }}">
                            <label class="form-check-label ms-1" for="propertyType{{ $index }}">
                                {{ ucfirst($type) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <h5 class="text-dark fw-medium my-3">Bedrooms :</h5>
                <div class="" role="group" aria-label="Bedroom filter button group">
                    <input type="checkbox" class="btn-check bedroom-filter" id="bedroom1" value="1">
                    <label class="btn btn-outline-primary" for="bedroom1">1 BHK</label>

                    <input type="checkbox" class="btn-check bedroom-filter" id="bedroom2" value="2">
                    <label class="btn btn-outline-primary" for="bedroom2">2 BHK</label>

                    <input type="checkbox" class="btn-check bedroom-filter" id="bedroom3" value="3">
                    <label class="btn btn-outline-primary" for="bedroom3">3 BHK</label>

                    <input type="checkbox" class="btn-check bedroom-filter" id="bedroom4" value="4">
                    <label class="btn btn-outline-primary my-1" for="bedroom4">4+ BHK</label>
                </div>
                
                <h5 class="text-dark fw-medium my-3">Accessibility Features :</h5>
                <div class="row g-1">
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input feature-filter" type="checkbox" value="balcony" id="featureBalcony">
                            <label class="form-check-label ms-1" for="featureBalcony">
                                Balcony
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input feature-filter" type="checkbox" value="parking" id="featureParking">
                            <label class="form-check-label ms-1" for="featureParking">
                                Parking
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input feature-filter" type="checkbox" value="pool" id="featurePool">
                            <label class="form-check-label ms-1" for="featurePool">
                                Swimming Pool
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <input class="form-check-input feature-filter" type="checkbox" value="gym" id="featureGym">
                            <label class="form-check-label ms-1" for="featureGym">
                                Gym
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-primary w-100" id="apply-filters">Apply Filters</button>
                    <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="reset-filters">Reset Filters</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-9 col-lg-12">
        <div class="row" id="property-grid">            @foreach($properties as $property)
            @php
                $firstUnit = $property->units->first(); // Get the first unit for display
                $totalUnits = $property->units->count();
            @endphp            <div class="col-lg-4 col-md-6 property-card" 
                 data-location="{{ $property->address->city ?? '' }}"
                 data-type="{{ $property->type1 ?? '' }}"
                 data-price="{{ $firstUnit->rental_price ?? 0 }}"
                 data-bedrooms="{{ $totalUnits }}"
                 data-status="available">
                <div class="card overflow-hidden">
                    <div class="position-relative">
                        @if($firstUnit && $firstUnit->images && is_array($firstUnit->images) && count($firstUnit->images) > 0)
                            <img src="{{ asset('storage/' . $firstUnit->images[0]) }}" alt="{{ $property->type1 ?? 'Property' }}" class="img-fluid rounded-top" style="height: 200px; object-fit: cover;">
                        @elseif($property->images && is_array($property->images) && count($property->images) > 0)
                            <img src="{{ asset('storage/' . $property->images[0]) }}" alt="{{ $property->type1 ?? 'Property' }}" class="img-fluid rounded-top" style="height: 200px; object-fit: cover;">
                        @else
                            <img src="/images/properties/p-1.jpg" alt="Property" class="img-fluid rounded-top" style="height: 200px; object-fit: cover;">
                        @endif
                        
                        <span class="position-absolute top-0 start-0 p-1">
                            <button type="button" class="btn btn-warning avatar-sm d-inline-flex align-items-center justify-content-center fs-20 rounded text-light bookmark-btn" data-property-id="{{ $property->id }}">
                                <iconify-icon icon="solar:bookmark-broken"></iconify-icon>
                            </button>
                        </span>
                        
                        <span class="position-absolute top-0 end-0 p-1">
                            @if($totalUnits > 0)
                                <span class="badge bg-success text-white fs-13">{{ $totalUnits }} Unit{{ $totalUnits > 1 ? 's' : '' }}</span>
                            @else
                                <span class="badge bg-secondary text-white fs-13">No Units</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar bg-light rounded">
                                <iconify-icon icon="solar:home-bold-duotone" class="fs-24 text-primary avatar-title"></iconify-icon>
                            </div>
                            <div>
                                <a href="#!" class="text-dark fw-medium fs-16">{{ $property->name ?? $property->type1 ?? 'Property' }} - {{ $property->id }}</a>
                                <p class="text-muted mb-0">
                                    @if($property->address)
                                        {{ $property->address->street ?? '' }}
                                        @if($property->address->street && $property->address->city), @endif
                                        {{ $property->address->city ?? '' }}
                                    @else
                                        No address available
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mt-2 g-2">
                            <div class="col-lg-4 col-4">
                                <span class="badge bg-light-subtle text-muted border fs-12">
                                    <span class="fs-16"><iconify-icon icon="solar:home-bold" class="align-middle"></iconify-icon></span>
                                    {{ $totalUnits }} Units
                                </span>
                            </div>
                            <div class="col-lg-4 col-4">
                                <span class="badge bg-light-subtle text-muted border fs-12">
                                    <span class="fs-16"><iconify-icon icon="solar:buildings-3-bold" class="align-middle"></iconify-icon></span>
                                    {{ $property->type1 ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="col-lg-4 col-4">
                                <span class="badge bg-light-subtle text-muted border fs-12">
                                    <span class="fs-16"><iconify-icon icon="solar:scale-broken" class="align-middle"></iconify-icon></span>
                                    {{ $property->total_area ?? 'N/A' }}ft²
                                </span>
                            </div>
                            <div class="col-lg-4 col-4">
                                <span class="badge bg-light-subtle text-muted border fs-12">
                                    <span class="fs-16"><iconify-icon icon="solar:double-alt-arrow-up-broken" class="align-middle"></iconify-icon></span>
                                    {{ $property->floors_count ?? 1 }} Floor{{ ($property->floors_count ?? 1) > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light-subtle d-flex justify-content-between align-items-center border-top">                        <p class="fw-medium text-dark fs-16 mb-0">
                            @if($firstUnit && $firstUnit->rental_price)
                                ${{ number_format($firstUnit->rental_price, 2) }}
                                @if($totalUnits > 1)
                                    <small class="text-muted">from</small>
                                @endif
                            @else
                                Price on request
                            @endif
                        </p>
                        <div>
                            <a href="#!" class="link-primary fw-medium inquiry-btn" data-property-id="{{ $property->id }}">
                                More Inquiry <i class='ri-arrow-right-line align-middle'></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($properties->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <iconify-icon icon="solar:home-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                        <h4 class="mt-3">No Properties Found</h4>
                        <p class="text-muted">Try adjusting your search criteria to find more properties.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Pagination -->
        @if($properties->count() > 9)
        <div class="row">
            <div class="col-12">
                <nav aria-label="Property pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#!" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#!">1</a></li>
                        <li class="page-item"><a class="page-link" href="#!">2</a></li>
                        <li class="page-item"><a class="page-link" href="#!">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#!">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/property-grid.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter functionality
    initializePropertyFilters();
    
    // Initialize bookmark functionality
    initializeBookmarks();
    
    // Initialize inquiry functionality
    initializeInquiries();
});

function initializePropertyFilters() {
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const propertyCards = document.querySelectorAll('.property-card');
    const propertyCount = document.getElementById('property-count');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetFilters();
        });
    }
      function applyFilters() {
        const location = document.getElementById('property-location').value;
        const propertyType = document.getElementById('property-type').value;
        const minPrice = parseFloat(document.getElementById('minCost').value.replace(/[^0-9.-]+/g,"")) || 0;
        const maxPrice = parseFloat(document.getElementById('maxCost').value.replace(/[^0-9.-]+/g,"")) || Infinity;
        
        const selectedBedrooms = Array.from(document.querySelectorAll('.bedroom-filter:checked')).map(cb => cb.value);
        const selectedAccessibility = Array.from(document.querySelectorAll('.accessibility-filter:checked')).map(cb => cb.value);
        const selectedPropertyTypes = Array.from(document.querySelectorAll('.property-type-filter:checked')).map(cb => cb.value).filter(v => v !== '');
        
        let visibleCount = 0;
        
        propertyCards.forEach(card => {
            const cardLocation = card.dataset.location;
            const cardType = card.dataset.type;
            const cardPrice = parseFloat(card.dataset.price) || 0;
            const cardUnits = parseInt(card.dataset.bedrooms) || 0; // This is actually total units now
            
            let shouldShow = true;
            
            // Filter by location
            if (location && cardLocation !== location) {
                shouldShow = false;
            }
            
            // Filter by property type
            if (propertyType && cardType !== propertyType) {
                shouldShow = false;
            }
            
            // Filter by property type checkboxes
            if (selectedPropertyTypes.length > 0 && !selectedPropertyTypes.includes(cardType)) {
                shouldShow = false;
            }
            
            // Filter by price range
            if (cardPrice < minPrice || cardPrice > maxPrice) {
                shouldShow = false;
            }
            
            // Note: Bedroom filtering is less relevant for properties with multiple units
            // You might want to modify this based on your specific requirements
            
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        propertyCount.textContent = `Show ${visibleCount} Properties`;
    }
    
    function resetFilters() {
        // Reset form elements
        document.getElementById('property-location').value = '';
        document.getElementById('property-type').value = '';
        document.querySelectorAll('.accessibility-filter, .property-type-filter, .bedroom-filter, .feature-filter').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('allProperties').checked = true;
        
        // Reset price range
        if (window.priceSlider) {
            window.priceSlider.reset();
        }
        
        // Show all cards
        propertyCards.forEach(card => {
            card.style.display = 'block';
        });
        
        propertyCount.textContent = `Show ${propertyCards.length} Properties`;
    }
}

function initializeBookmarks() {
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const propertyId = this.dataset.propertyId;
            // Toggle bookmark visual state
            this.classList.toggle('active');
            
            // Here you would typically make an AJAX call to save/remove bookmark
            console.log('Bookmark toggled for property:', propertyId);
        });
    });
}

function initializeInquiries() {
    document.querySelectorAll('.inquiry-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.dataset.propertyId;
            
            // Here you would typically open a modal or redirect to inquiry form
            alert(`Opening inquiry form for property ${propertyId}`);
        });
    });
}
</script>
@endsection
