import noUiSlider from 'nouislider/dist/nouislider'
import wNumb from 'wnumb/wNumb'

const slider = document.getElementById("product-price-range");
if (slider) {
    // Get min and max values from the data attributes or default values
    const minPrice = parseInt(document.getElementById("minCost").value) || 0;
    const maxPrice = parseInt(document.getElementById("maxCost").value) || 200000;
    
    // Create the price range slider
    noUiSlider.create(slider, {
        start: [minPrice, maxPrice], // Handle start position based on actual data
        step: 1000, // Slider moves in increments of 1000
        margin: 1000, // Handles must be more than 1000 apart
        connect: true, // Display a colored bar between the handles
        behaviour: 'tap-drag', // Move a handle on tap, bar is draggable
        range: { // Slider range based on actual property data
            'min': 0,
            'max': Math.max(maxPrice * 1.2, 500000) // 120% of max price or 500k minimum
        },
        format: wNumb({
            decimals: 0,
            prefix: '$ ',
            thousand: ','
        })
    });

    const minCostInput = document.getElementById("minCost"),
        maxCostInput = document.getElementById("maxCost");

    // When the slider value changes, update the input fields
    slider.noUiSlider.on('update', function (values, handle) {
        if (handle) {
            maxCostInput.value = values[handle];
        } else {
            minCostInput.value = values[handle];
        }
        
        // Trigger filter update if auto-filter is enabled
        if (window.autoFilterEnabled) {
            debounceFilter();
        }
    });

    // When input fields change, update the slider
    minCostInput.addEventListener('change', function () {
        slider.noUiSlider.set([this.value, null]);
    });

    maxCostInput.addEventListener('change', function () {
        slider.noUiSlider.set([null, this.value]);
    });
    
    // Store slider reference for reset functionality
    window.priceSlider = {
        reset: function() {
            slider.noUiSlider.set([minPrice, maxPrice]);
        }
    };
}

// Debounce function for filter updates
let filterTimeout;
function debounceFilter() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        if (typeof applyFilters === 'function') {
            applyFilters();
        }
    }, 500);
}

// Initialize Choices.js for select dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Initialize location dropdown
    const locationSelect = document.getElementById('property-location');
    if (locationSelect && typeof Choices !== 'undefined') {
        new Choices(locationSelect, {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Select City'
        });
    }
    
    // Initialize property type dropdown
    const typeSelect = document.getElementById('property-type');
    if (typeSelect && typeof Choices !== 'undefined') {
        new Choices(typeSelect, {
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'Select Property Type'
        });
    }
    
    // Add event listeners for real-time filtering
    const filterElements = document.querySelectorAll('#property-location, #property-type, .accessibility-filter, .property-type-filter, .bedroom-filter, .feature-filter');
    filterElements.forEach(element => {
        element.addEventListener('change', function() {
            if (window.autoFilterEnabled) {
                debounceFilter();
            }
        });
    });
});

// Export functions for use in the view
window.propertyGridUtils = {
    initializeSlider: function(min, max) {
        if (slider && slider.noUiSlider) {
            slider.noUiSlider.updateOptions({
                range: {
                    'min': 0,
                    'max': Math.max(max * 1.2, 500000)
                },
                start: [min, max]
            });
        }
    },
    
    resetSlider: function() {
        if (window.priceSlider) {
            window.priceSlider.reset();
        }
    }
};