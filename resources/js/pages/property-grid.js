// Property Grid JavaScript functionality
console.log('Property Grid JS loaded');

// NoUiSlider and wNumb imports for price range slider
let noUiSlider, wNumb;

// Dynamic imports for required libraries
async function loadLibraries() {
    try {
        if (typeof window.noUiSlider === 'undefined') {
            // Load NoUiSlider from CDN
            await loadScript('https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js');
        }
        if (typeof window.wNumb === 'undefined') {
            // Load wNumb from CDN  
            await loadScript('https://cdn.jsdelivr.net/npm/wnumb@1.2.0/wNumb.min.js');
        }
        if (typeof window.Choices === 'undefined') {
            // Load Choices.js from CDN
            await loadScript('https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js');
        }

        noUiSlider = window.noUiSlider;
        wNumb = window.wNumb;

        console.log('All libraries loaded successfully');
        return true;
    } catch (error) {
        console.error('Error loading libraries:', error);
        return false;
    }
}

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// Initialize property grid functionality
document.addEventListener('DOMContentLoaded', async function () {
    // Load required libraries
    const librariesLoaded = await loadLibraries();

    if (librariesLoaded) {
        // Initialize components
        initializePriceSlider();
        initializeDropdowns();
        initializePropertyFilters();
        initializeBookmarks();
        initializeInquiries();

        console.log('Property Grid initialized successfully');
    } else {
        console.error('Failed to load required libraries');
    }
});

function initializePriceSlider() {
    const slider = document.getElementById("product-price-range");
    if (slider && noUiSlider && wNumb) {
        try {
            // Get price range from data attributes or use defaults
            const minPrice = parseInt(document.getElementById("minCost")?.value.replace(/[^0-9]/g, '')) || 0;
            const maxPrice = parseInt(document.getElementById("maxCost")?.value.replace(/[^0-9]/g, '')) || 200000;

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
                    thousand: ',',
                    prefix: '$'
                })
            });

            const minCostInput = document.getElementById("minCost");
            const maxCostInput = document.getElementById("maxCost");

            // Update inputs when slider changes
            slider.noUiSlider.on('update', function (values, handle) {
                if (handle) {
                    maxCostInput.value = values[handle];
                } else {
                    minCostInput.value = values[handle];
                }
            });

            // Update slider when inputs change
            minCostInput?.addEventListener('change', function () {
                slider.noUiSlider.set([this.value.replace(/[^0-9]/g, ''), null]);
            });

            maxCostInput?.addEventListener('change', function () {
                slider.noUiSlider.set([null, this.value.replace(/[^0-9]/g, '')]);
            });

            // Store slider reference for reset
            window.priceSlider = {
                reset: function () {
                    slider.noUiSlider.set([minPrice, maxPrice]);
                }
            };

            console.log('Price slider initialized');
        } catch (error) {
            console.error('Error initializing price slider:', error);
        }
    }
}

function initializeDropdowns() {
    if (typeof window.Choices !== 'undefined') {
        try {
            const locationSelect = document.getElementById('property-location');
            if (locationSelect) {
                new Choices(locationSelect, {
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Select City',
                    shouldSort: false
                });
            }

            const typeSelect = document.getElementById('property-type');
            if (typeSelect) {
                new Choices(typeSelect, {
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Select Property Type',
                    shouldSort: false
                });
            }

            console.log('Dropdowns initialized');
        } catch (error) {
            console.error('Error initializing dropdowns:', error);
        }
    }
}

function initializePropertyFilters() {
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const propertyCards = document.querySelectorAll('.property-card');
    const propertyCount = document.getElementById('property-count');

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetFilters);
    }

    // Auto-apply filters on change
    const filterElements = document.querySelectorAll('#property-location, #property-type, .accessibility-filter, .property-type-filter');
    filterElements.forEach(element => {
        element.addEventListener('change', debounceFilter);
    });

    function applyFilters() {
        const location = document.getElementById('property-location')?.value || '';
        const propertyType = document.getElementById('property-type')?.value || '';
        const minPrice = parseFloat(document.getElementById('minCost')?.value.replace(/[^0-9.-]+/g, "")) || 0;
        const maxPrice = parseFloat(document.getElementById('maxCost')?.value.replace(/[^0-9.-]+/g, "")) || Infinity;

        const selectedPropertyTypes = Array.from(document.querySelectorAll('.property-type-filter:checked'))
            .map(cb => cb.value).filter(v => v !== '');
        const selectedAccessibility = Array.from(document.querySelectorAll('.accessibility-filter:checked'))
            .map(cb => cb.value);

        let visibleCount = 0;

        propertyCards.forEach(card => {
            const cardLocation = card.dataset.location || '';
            const cardType = card.dataset.type || '';
            const cardPrice = parseFloat(card.dataset.price) || 0;

            let shouldShow = true;

            // Filter by location
            if (location && cardLocation.toLowerCase() !== location.toLowerCase()) {
                shouldShow = false;
            }

            // Filter by property type (dropdown)
            if (propertyType && cardType.toLowerCase() !== propertyType.toLowerCase()) {
                shouldShow = false;
            }

            // Filter by property type (checkboxes)
            if (selectedPropertyTypes.length > 0 && !selectedPropertyTypes.some(type =>
                type.toLowerCase() === cardType.toLowerCase())) {
                shouldShow = false;
            }

            // Filter by price range
            if (cardPrice < minPrice || cardPrice > maxPrice) {
                shouldShow = false;
            }

            // Show/hide card
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (propertyCount) {
            propertyCount.textContent = `Show ${visibleCount} Properties`;
        }

        console.log(`Filters applied: ${visibleCount} properties shown`);
    }

    function resetFilters() {
        // Reset dropdowns
        const locationSelect = document.getElementById('property-location');
        const typeSelect = document.getElementById('property-type');

        if (locationSelect) locationSelect.value = '';
        if (typeSelect) typeSelect.value = '';

        // Reset checkboxes
        document.querySelectorAll('.accessibility-filter, .property-type-filter').forEach(cb => {
            cb.checked = false;
        });

        const allPropertiesCheckbox = document.getElementById('allProperties');
        if (allPropertiesCheckbox) {
            allPropertiesCheckbox.checked = true;
        }

        // Reset price slider
        if (window.priceSlider) {
            window.priceSlider.reset();
        }

        // Show all cards
        propertyCards.forEach(card => {
            card.style.display = 'block';
        });

        if (propertyCount) {
            propertyCount.textContent = `Show ${propertyCards.length} Properties`;
        }

        console.log('Filters reset');
    }

    // Debounced filter function
    let filterTimeout;
    function debounceFilter() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(applyFilters, 300);
    }
}

function initializeBookmarks() {
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const propertyId = this.dataset.propertyId;

            // Toggle visual state
            this.classList.toggle('active');

            // Here you would make an AJAX call to save/remove bookmark
            console.log('Bookmark toggled for property:', propertyId);

            // Optional: Show toast notification
            showToast(`Property ${propertyId} ${this.classList.contains('active') ? 'bookmarked' : 'unbookmarked'}`);
        });
    });
}

function initializeInquiries() {
    const inquiryModalElement = document.getElementById('inquiryModal');

    if (inquiryModalElement && typeof bootstrap !== 'undefined') {
        const inquiryModal = new bootstrap.Modal(inquiryModalElement);

        document.querySelectorAll('.inquiry-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const propertyId = this.dataset.propertyId;

                const propertyIdInput = document.getElementById('inquiryPropertyId');
                if (propertyIdInput) {
                    propertyIdInput.value = propertyId;
                }

                inquiryModal.show();
            });
        });

        const submitBtn = document.getElementById('submitInquiry');
        if (submitBtn) {
            submitBtn.addEventListener('click', function () {
                const form = document.getElementById('inquiryForm');
                if (form && form.checkValidity()) {
                    const formData = new FormData(form);

                    // Here you would submit via AJAX
                    console.log('Inquiry submitted:', Object.fromEntries(formData));

                    showToast('Thank you for your inquiry! We will contact you soon.');
                    inquiryModal.hide();
                    form.reset();
                } else {
                    form.reportValidity();
                }
            });
        }
    } else {
        // Fallback for browsers without Bootstrap modal
        document.querySelectorAll('.inquiry-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const propertyId = this.dataset.propertyId;
                alert(`Opening inquiry for property ${propertyId}`);
            });
        });
    }
}

// Utility function to show toast notifications
function showToast(message, type = 'info') {
    // Create a simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Export for use in other scripts
window.PropertyGrid = {
    applyFilters: () => initializePropertyFilters(),
    resetFilters: () => initializePropertyFilters(),
    showToast: showToast
};
