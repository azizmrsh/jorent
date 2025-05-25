@extends('layouts.app', ['title' => 'Property Grid', 'subTitle' => 'Real Estate Management'])

@section('css')
<style>
    .navbar {
        background-color: #ff7f00 !important; /* Orange theme */
    }

    .property-card {
        border: 1px solid #ff7f00; /* Orange border */
    }

    .property-card:hover {
        box-shadow: 0 10px 25px rgba(255, 127, 0, 0.3); /* Orange shadow on hover */
    }

    .btn-primary {
        background-color: #ff7f00 !important; /* Orange button */
        border-color: #ff7f00 !important;
    }

    .btn-primary:hover {
        background-color: #e67300 !important; /* Darker orange on hover */
        border-color: #e67300 !important;
    }

    .noUi-connect {
        background: #007bff;
    }
    .noUi-handle {
        background: #007bff;
        border: 1px solid #007bff;
    }
    .choices__inner {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .property-grid-container {
        min-height: 500px;
    }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-xl-3 col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom bg-white">
                <div>
                    <h4 class="card-title mb-1">Properties Filter</h4>
                    <p class="mb-0 text-muted" id="property-count">Show {{ $properties->count() }} Properties</p>
                </div>
            </div>
            <div class="card-body">
                <form id="property-filter-form">
                    <div class="mb-4">
                        <label for="property-location" class="form-label fw-semibold">Properties Location</label>
                        <select class="form-control" id="property-location" name="location">
                            <option value="">Choose a city</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="property-type" class="form-label fw-semibold">Type Of Place</label>
                        <select class="form-control" id="property-type" name="property_type">
                            <option value="">All Types</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                
                <h5 class="text-dark fw-semibold my-3">Custom Price Range:</h5>
                <div id="product-price-range" class="mb-3"></div>
                <div class="formCost d-flex gap-2 align-items-center mt-3">
                    <input class="form-control form-control-sm text-center" type="text" id="minCost" value="{{ number_format($minPrice) }}" readonly>
                    <span class="fw-semibold text-muted">to</span>
                    <input class="form-control form-control-sm text-center" type="text" id="maxCost" value="{{ number_format($maxPrice) }}" readonly>
                </div>
                
                <h5 class="text-dark fw-semibold my-4">Accessibility Features:</h5>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input accessibility-filter" type="checkbox" value="rent" id="accessibilityRent">
                            <label class="form-check-label" for="accessibilityRent">
                                For Rent
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input accessibility-filter" type="checkbox" value="sale" id="accessibilitySale">
                            <label class="form-check-label" for="accessibilitySale">
                                For Sale
                            </label>
                        </div>
                    </div>
                </div>
                
                <h5 class="text-dark fw-semibold my-4">Properties Type:</h5>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input property-type-filter" type="checkbox" value="" id="allProperties" checked>
                            <label class="form-check-label" for="allProperties">
                                All Properties
                            </label>
                        </div>
                    </div>
                    @foreach($propertyTypes->take(6) as $index => $type)
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input property-type-filter" type="checkbox" value="{{ $type }}" id="propertyType{{ $index }}">
                            <label class="form-check-label" for="propertyType{{ $index }}">
                                {{ ucfirst($type) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 d-grid gap-2">
                    <button type="button" class="btn btn-primary" id="apply-filters">
                        <iconify-icon icon="solar:filter-bold" class="me-1"></iconify-icon>
                        Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="reset-filters">
                        <iconify-icon icon="solar:refresh-bold" class="me-1"></iconify-icon>
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-9 col-lg-12">
        <div class="property-grid-container">
            <div class="row" id="property-grid">
                @forelse($properties as $property)
                @php
                    $firstUnit = $property->units->first();
                    $totalUnits = $property->units->count();
                @endphp                <div class="col-lg-4 col-md-6 mb-4 property-card" 
                     data-location="{{ $property->address->city ?? '' }}"
                     data-type="{{ $property->type1 ?? '' }}"
                     data-price="{{ $firstUnit->rental_price ?? 0 }}"
                     data-units="{{ $totalUnits }}"
                     data-status="available">
                    <div class="card h-100 shadow-sm overflow-hidden">
                        <div class="position-relative">
                            @if($firstUnit && $firstUnit->images && is_array($firstUnit->images) && count($firstUnit->images) > 0)
                                <img src="{{ asset('storage/' . $firstUnit->images[0]) }}" alt="{{ $property->type1 ?? 'Property' }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @elseif($property->images && is_array($property->images) && count($property->images) > 0)
                                <img src="{{ asset('storage/' . $property->images[0]) }}" alt="{{ $property->type1 ?? 'Property' }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                    <iconify-icon icon="solar:home-bold-duotone" class="fs-1 text-muted"></iconify-icon>
                                </div>
                            @endif
                            
                            <span class="position-absolute top-0 start-0 p-2">
                                <button type="button" class="btn btn-warning btn-sm rounded-circle bookmark-btn" data-property-id="{{ $property->id }}">
                                    <iconify-icon icon="solar:bookmark-broken"></iconify-icon>
                                </button>
                            </span>
                            
                            <span class="position-absolute top-0 end-0 p-2">
                                @if($totalUnits > 0)
                                    <span class="badge bg-success">{{ $totalUnits }} Unit{{ $totalUnits > 1 ? 's' : '' }}</span>
                                @else
                                    <span class="badge bg-secondary">No Units</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="avatar bg-primary bg-opacity-10 rounded">
                                    <iconify-icon icon="solar:home-bold-duotone" class="fs-4 text-primary"></iconify-icon>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">
                                        <a href="#!" class="text-decoration-none text-dark">
                                            {{ $property->name ?? ($property->type1 ? ucfirst($property->type1) : 'Property') }} #{{ $property->id }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        @if($property->address)
                                            {{ $property->address->street ?? '' }}
                                            @if($property->address->street && $property->address->city), @endif
                                            {{ $property->address->city ?? '' }}
                                        @else
                                            <em>No address available</em>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="badge bg-light text-dark border w-100">
                                        <iconify-icon icon="solar:home-bold" class="me-1"></iconify-icon>
                                        {{ $totalUnits }} Units
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-light text-dark border w-100">
                                        <iconify-icon icon="solar:buildings-3-bold" class="me-1"></iconify-icon>
                                        {{ $property->type1 ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-light text-dark border w-100">
                                        <iconify-icon icon="solar:scale-broken" class="me-1"></iconify-icon>
                                        {{ $property->total_area ?? 'N/A' }}ft²
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-light text-dark border w-100">
                                        <iconify-icon icon="solar:double-alt-arrow-up-broken" class="me-1"></iconify-icon>
                                        {{ $property->floors_count ?? 1 }} Floor{{ ($property->floors_count ?? 1) > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                            <div>                                @if($firstUnit && $firstUnit->rental_price)
                                    <h5 class="mb-0 text-primary">
                                        ${{ number_format($firstUnit->rental_price, 2) }}
                                        @if($totalUnits > 1)
                                            <small class="text-muted fw-normal">from</small>
                                        @endif
                                    </h5>
                                @else
                                    <h6 class="mb-0 text-muted">Price on request</h6>
                                @endif
                            </div>
                            <div>
                                <a href="#!" class="btn btn-outline-primary btn-sm inquiry-btn" data-property-id="{{ $property->id }}">
                                    More Inquiry
                                    <iconify-icon icon="solar:arrow-right-linear" class="ms-1"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <iconify-icon icon="solar:home-bold-duotone" class="fs-1 text-muted mb-3"></iconify-icon>
                            <h4 class="mb-2">No Properties Found</h4>
                            <p class="text-muted">There are no properties available at the moment. Please check back later.</p>
                            <a href="{{ route('property.grid') }}" class="btn btn-primary">
                                <iconify-icon icon="solar:refresh-bold" class="me-1"></iconify-icon>
                                Refresh
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Pagination -->
        @if($properties->count() > 9)
        <div class="row mt-4">
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

<!-- Inquiry Modal -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inquiryModalLabel">Property Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="inquiryForm">
                    <input type="hidden" id="inquiryPropertyId" name="property_id">
                    <div class="mb-3">
                        <label for="inquiryName" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="inquiryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="inquiryEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="inquiryEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="inquiryPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="inquiryPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="inquiryMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="inquiryMessage" name="message" rows="3" placeholder="I'm interested in this property..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitInquiry">Send Inquiry</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize price range slider
    initializePriceSlider();
    
    // Initialize dropdowns
    initializeDropdowns();
    
    // Initialize filter functionality
    initializePropertyFilters();
    
    // Initialize bookmark functionality
    initializeBookmarks();
    
    // Initialize inquiry functionality
    initializeInquiries();
});

function initializePriceSlider() {
    const slider = document.getElementById("product-price-range");
    if (slider) {
        const minPrice = {{ $minPrice }};
        const maxPrice = {{ $maxPrice }};
        
        noUiSlider.create(slider, {
            start: [minPrice, maxPrice],
            step: 1000,
            margin: 1000,
            connect: true,
            behaviour: 'tap-drag',
            range: {
                'min': 0,
                'max': Math.max(maxPrice * 1.2, 500000)
            },
            format: wNumb({
                decimals: 0,
                thousand: ','
            })
        });

        const minCostInput = document.getElementById("minCost");
        const maxCostInput = document.getElementById("maxCost");

        slider.noUiSlider.on('update', function (values, handle) {
            if (handle) {
                maxCostInput.value = values[handle];
            } else {
                minCostInput.value = values[handle];
            }
        });

        // Store slider reference
        window.priceSlider = {
            reset: function() {
                slider.noUiSlider.set([minPrice, maxPrice]);
            }
        };
    }
}

function initializeDropdowns() {
    const locationSelect = document.getElementById('property-location');
    if (locationSelect && typeof Choices !== 'undefined') {
        new Choices(locationSelect, {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Select City'
        });
    }
    
    const typeSelect = document.getElementById('property-type');
    if (typeSelect && typeof Choices !== 'undefined') {
        new Choices(typeSelect, {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Select Property Type'
        });
    }
}

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
        
        const selectedPropertyTypes = Array.from(document.querySelectorAll('.property-type-filter:checked')).map(cb => cb.value).filter(v => v !== '');
        
        let visibleCount = 0;
        
        propertyCards.forEach(card => {
            const cardLocation = card.dataset.location;
            const cardType = card.dataset.type;
            const cardPrice = parseFloat(card.dataset.price) || 0;
            
            let shouldShow = true;
            
            // Filter by location
            if (location && cardLocation !== location) {
                shouldShow = false;
            }
            
            // Filter by property type (dropdown)
            if (propertyType && cardType !== propertyType) {
                shouldShow = false;
            }
            
            // Filter by property type (checkboxes)
            if (selectedPropertyTypes.length > 0 && !selectedPropertyTypes.includes(cardType)) {
                shouldShow = false;
            }
            
            // Filter by price range
            if (cardPrice < minPrice || cardPrice > maxPrice) {
                shouldShow = false;
            }
            
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
        document.querySelectorAll('.accessibility-filter, .property-type-filter').forEach(cb => {
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
            this.classList.toggle('active');
            
            // Here you would make an AJAX call to save/remove bookmark
            console.log('Bookmark toggled for property:', propertyId);
        });
    });
}

function initializeInquiries() {
    const inquiryModal = new bootstrap.Modal(document.getElementById('inquiryModal'));
    
    document.querySelectorAll('.inquiry-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.dataset.propertyId;
            
            document.getElementById('inquiryPropertyId').value = propertyId;
            inquiryModal.show();
        });
    });
    
    document.getElementById('submitInquiry').addEventListener('click', function() {
        const form = document.getElementById('inquiryForm');
        const formData = new FormData(form);
        
        // Here you would submit the inquiry via AJAX
        console.log('Inquiry submitted for property:', formData.get('property_id'));
        alert('Thank you for your inquiry! We will contact you soon.');
        inquiryModal.hide();
        form.reset();
    });
}
</script>
@endsection
