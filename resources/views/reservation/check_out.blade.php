@extends('layouts.home')

@section('title', __('Check-out Reservation'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-ui.page-header 
        :title="__('Reservation Check-out')"
        :description="__('Process guest check-out and equipment return')"
        icon="bx bx-log-out-circle"
    >
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> {{ __('Back to List') }}
        </a>
    </x-ui.page-header>

    <div class="row justify-content-center">
        <!-- Step 1: Reservation Number Search -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-id-card me-2"></i>{{ __('Search Reservation') }}</h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="search_reservation_number" placeholder="{{ __('Enter Reservation Number') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" id="btnSearchReservation">
                            <i class="bx bx-search"></i> {{ __('Search') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Reservation Info (hidden until found) -->
        <div id="reservation-info-section" class="card mb-4 shadow-sm border-0 d-none">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>{{ __('Reservation Details') }}</h5>
            </div>
            <div class="card-body pt-3">
                <div id="reservation-info-content">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>{{ __('Guest Name:') }}</strong> <span id="guest-name"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Email:') }}</strong> <span id="guest-email"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Location:') }}</strong> <span id="location-info"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Period:') }}</strong> <span id="period-info"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Current Status:') }}</strong> <span id="reservation-status"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Check-out Form (for checked-in reservations) -->
        <form id="checkoutReservationForm" method="POST" action="{{ route('reservations.checkout') }}" class="d-none" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="checkout_reservation_id" name="reservation_id">
            
            <!-- Equipment Return -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>{{ __('Equipment Return') }}</h5>
                    <small class="text-muted">{{ __('Mark the condition of each equipment item being returned') }}</small>
                </div>
                <div class="card-body pt-3" style="max-height: 250px; overflow-y: auto;">
                    <div id="equipment-return-list">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Checkout Summary -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>{{ __('Check-out Summary') }}</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="checkout_notes" class="form-label">{{ __('Check-out Notes') }}</label>
                            <textarea class="form-control" id="checkout_notes" name="checkout_notes" rows="3" placeholder="{{ __('Enter any notes about the check-out process...') }}"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-lg btn-danger px-4 shadow">
                    <i class="bx bx-log-out-circle"></i> {{ __('Complete Check-out') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 

@push('scripts')
<script>
/**
 * ========================================
 * RESERVATION CHECK-OUT SYSTEM
 * ========================================
 * 
 * Organized modular system for handling guest check-out processes
 * Features:
 * - Equipment return tracking
 * - Damage cost estimation
 * - Real-time form validation
 * - Status tracking and notifications
 */

// ===========================
// ROUTES
// ===========================
const ROUTES = {
    reservations: {
        checkout: '{{ route("reservations.checkout") }}',
        findByNumber: '{{ route("reservations.findByNumber") }}'
    }
};

const TRANSLATIONS = {
    general: {
        search: @json(__('Search')),
        searchReservation: @json(__('Search Reservation')),
        searching: @json(__('Searching...')),
        unknownEquipment: @json(__('Unknown Equipment')),
        checkedOut: @json(__('Checked out')),
        items: @json(__('items')),
        item: @json(__('item')),
        quantityReturned: @json(__('Quantity Returned')),
        condition: @json(__('Condition')),
        goodCondition: @json(__('Good Condition')),
        damaged: @json(__('Damaged')),
        missingLost: @json(__('Missing/Lost')),
        estimatedCost: @json(__('Estimated Cost ($)')),
        damageMissingNotes: @json(__('Damage/Missing Notes')),
        damageMissingPlaceholder: @json(__('Describe the damage, missing circumstances, or any other relevant information...')),
        processing: @json(__('Processing...')),
        completeCheckout: @json(__('Complete Check-out')),
        errorOccurred: @json(__('An error occurred. Please try again.')),
        noReservationSelected: @json(__('No reservation selected.')),
        noEquipmentFound: @json(__('No Equipment Found')),
        noEquipmentCheckedOut: @json(__('No equipment was checked out with this reservation.')),
        details: @json(__('Details:')),
        enterReservationNumber: @json(__('Please enter a reservation number.'))
    },
    validation: {
        required: @json(__('This field is required.')),
        invalidCost: @json(__('Please enter a valid cost amount.')),
        missingNotes: @json(__('Please provide notes for damaged/missing items.')),
        validationErrors: @json(__('Please fix the following validation errors:'))
    }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
    request: function(options) {
        return $.ajax(options);
    },

    /**
     * Find reservation by number
     */
    findReservationByNumber: function(reservationNumber) {
        return this.request({ 
            url: ROUTES.reservations.findByNumber, 
            method: 'GET', 
            data: { reservation_number: reservationNumber } 
        });
    },

    /**
     * Process check-out
     */
    processCheckout: function(formData) {
        return this.request({ 
            url: ROUTES.reservations.checkout, 
            method: 'POST', 
            data: formData,
            processData: false,
            contentType: false
        });
    }
};

// ===========================
// RESERVATION SEARCH MANAGER
// ===========================
const ReservationManager = {
    /**
     * Initialize reservation manager
     */
    init: function() {
        this.bindEvents();
    },

    /**
     * Bind reservation search events
     */
    bindEvents: function() {
        $('#btnSearchReservation').on('click', this.handleSearch.bind(this));
        $('#search_reservation_number').on('keypress', (e) => {
            if (e.which === 13) {
                this.handleSearch();
            }
        });
    },

    /**
     * Handle reservation search
     */
    handleSearch: function() {
        const reservationNumber = $('#search_reservation_number').val().trim();
        
        if (!reservationNumber) {
            Utils.showError(TRANSLATIONS.general.enterReservationNumber);
            return;
        }
        
        const $btn = $('#btnSearchReservation');
        Utils.setLoadingState($btn, true, { loadingText: TRANSLATIONS.general.searching });

        ApiService.findReservationByNumber(reservationNumber)
            .done((response) => {
                if (response.success && response.data) {
                    this.handleReservationFound(response.data);
                } else {
                    this.handleReservationNotFound(response.message || TRANSLATIONS.general.errorOccurred);
                }
            }).fail((xhr) => {
                const errorMessage = xhr.responseJSON?.message || TRANSLATIONS.general.errorOccurred;
                Utils.showError(errorMessage);
            }).always(() => {
                Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-search"></i> ' + TRANSLATIONS.general.searchReservation });
            });
    },

    /**
     * Handle successful reservation found
     */
    handleReservationFound: function(reservation) {
        this.populateReservationInfo(reservation);
        $('#reservation-info-section').removeClass('d-none').show();

        // Reset forms
        this.resetForms();
        this.setupCheckoutForm(reservation);
    },

    /**
     * Handle reservation not found
     */
    handleReservationNotFound: function(message) {
        this.resetForms();
        $('#reservation-info-section').addClass('d-none').hide();
        Utils.showError(message);
    },

    /**
    * Populate reservation information
    */
    populateReservationInfo: function(reservation) {
        const user = reservation.user || {};
        const location = reservation.location || {};
        const period = reservation.period || {};

        $('#guest-name').text(user.name || 'N/A');
        $('#guest-email').text(user.email || 'N/A');
        $('#location-info').text(location || 'N/A');
        $('#period-info').text(period || 'N/A');
        $('#reservation-status').text(reservation.status || 'N/A');
    },

    /**
     * Setup check-out form
     */
    setupCheckoutForm: function(reservation) {
        $('#checkout_reservation_id').val(reservation.id);
        $('#checkoutReservationForm').removeClass('d-none').show();

        let allEquipmentDetails = [];
        if (Array.isArray(reservation.equipmentTracking) || Array.isArray(reservation.equipment_tracking)) {
            const equipmentTracking = reservation.equipmentTracking || reservation.equipment_tracking;
            equipmentTracking.forEach(tracking => {
                if (Array.isArray(tracking.equipmentDetails) || Array.isArray(tracking.equipment_details)) {
                    const details = tracking.equipmentDetails || tracking.equipment_details;
                    allEquipmentDetails = allEquipmentDetails.concat(details);
                }
            });
        }

        if (allEquipmentDetails.length > 0) {
            EquipmentManager.renderCheckoutEquipment(allEquipmentDetails);
        } else {
            EquipmentManager.showNoEquipmentMessage();
        }
    },

    /**
     * Reset all forms
     */
    resetForms: function() {
        $('#checkoutReservationForm').addClass('d-none').hide();
        $('#checkoutReservationForm')[0].reset();
        $('#equipment-return-list').empty();
    }
};

// ===========================
// EQUIPMENT MANAGER
// ===========================
const EquipmentManager = {
    /**
     * Render equipment list for check-out
     */
    renderCheckoutEquipment: function(equipmentData) {
        const $list = $('#equipment-return-list');
        $list.empty();

        if (!equipmentData.length) {
            this.showNoEquipmentMessage();
            return;
        }

        equipmentData.forEach((item, index) => {
            const equipment = item.equipment || {};
            const html = `
                <div class="equipment-item mb-4 border rounded" data-equipment-id="${item.equipment_id || equipment.id}">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">${equipment.name_en || equipment.name || TRANSLATIONS.general.unknownEquipment}</h6>
                        <small class="text-muted">${TRANSLATIONS.general.checkedOut}: ${item.quantity || 1} ${(item.quantity || 1) > 1 ? TRANSLATIONS.general.items : TRANSLATIONS.general.item}</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">${TRANSLATIONS.general.quantityReturned}</label>
                                <input type="number" 
                                       class="form-control equipment-qty-returned" 
                                       name="equipment[${index}][quantity]" 
                                       value="${item.quantity || 1}" 
                                       min="0" 
                                       max="${item.quantity || 1}">
                                <input type="hidden" 
                                       name="equipment[${index}][equipment_id]" 
                                       value="${item.equipment_id || equipment.id}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">${TRANSLATIONS.general.condition}</label>
                                <select class="form-control equipment-condition" 
                                        name="equipment[${index}][returned_status]">
                                    <option value="good">${TRANSLATIONS.general.goodCondition}</option>
                                    <option value="damaged">${TRANSLATIONS.general.damaged}</option>
                                    <option value="missing">${TRANSLATIONS.general.missingLost}</option>
                                </select>
                            </div>
                            <div class="col-md-3 damage-cost-group" style="display:none;">
                                <label class="form-label">${TRANSLATIONS.general.estimatedCost}</label>
                                <input type="number" 
                                       class="form-control equipment-cost" 
                                       name="equipment[${index}][estimated_cost]" 
                                       placeholder="0.00" 
                                       step="0.01" 
                                       min="0">
                            </div>
                        </div>
                        <div class="row mt-3 damage-notes-group" style="display:none;">
                            <div class="col-12">
                                <label class="form-label">${TRANSLATIONS.general.damageMissingNotes}</label>
                                <textarea class="form-control equipment-notes" 
                                          name="equipment[${index}][returned_notes]" 
                                          rows="3" 
                                          placeholder="${TRANSLATIONS.general.damageMissingPlaceholder}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $list.append(html);
        });

        this.bindCheckoutEvents($list);
    },

    /**
     * Bind check-out equipment events
     */
    bindCheckoutEvents: function($list) {
        // Condition change events
        $list.find('.equipment-condition').on('change', function() {
            const $item = $(this).closest('.equipment-item');
            const condition = $(this).val();
            const $costGroup = $item.find('.damage-cost-group');
            const $notesGroup = $item.find('.damage-notes-group');

            if (condition === 'damaged' || condition === 'missing') {
                $costGroup.show();
                $notesGroup.show();
                $item.addClass(condition === 'damaged' ? 'border-warning' : 'border-danger');
                $item.removeClass(condition === 'damaged' ? 'border-danger' : 'border-warning');
                $item.find('.equipment-cost').prop('required', true);
            } else {
                $costGroup.hide();
                $notesGroup.hide();
                $item.removeClass('border-warning border-danger');
                $item.find('.equipment-cost').prop('required', false).val('');
                $item.find('.equipment-notes').val('');
            }
        });
    },

    /**
     * Show no equipment message
     */
    showNoEquipmentMessage: function() {
        $('#equipment-return-list').html(`
            <div class="text-center py-5">
                <i class="bx bx-info-circle text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">${TRANSLATIONS.general.noEquipmentFound}</h5>
                <p class="text-muted">${TRANSLATIONS.general.noEquipmentCheckedOut}</p>
            </div>
        `);
    }
};

// ===========================
// CHECK-OUT PROCESSOR
// ===========================
const CheckoutProcessor = {
    /**
     * Initialize check-out processor
     */
    init: function() {
        this.bindEvents();
    },

    /**
     * Bind check-out events
     */
    bindEvents: function() {
        $('#checkoutReservationForm').on('submit', this.handleSubmit.bind(this));
    },

    /**
     * Handle form submission
     */
    handleSubmit: function(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }
        
        const formData = this.getFormData();
        
        const $btn = $('#checkoutReservationForm button[type="submit"]');
        Utils.setLoadingState($btn, true, { loadingText: TRANSLATIONS.general.processing });

        ApiService.processCheckout(formData)
            .done((response) => {
                if (response.success) {
                    this.handleSuccess(response);
                } else {
                    Utils.showError(response.message || TRANSLATIONS.general.errorOccurred);
                }
            }).fail((xhr) => {
                const errorMessage = xhr.responseJSON?.message || TRANSLATIONS.general.errorOccurred;
                Utils.showError(errorMessage);
            }).always(() => {
                Utils.setLoadingState($btn, false, { normalText: `<i class="bx bx-log-out-circle"></i> ${TRANSLATIONS.general.completeCheckout}` });
            });
    },

    /**
     * Get form data for submission
     */
    getFormData: function() {
        const fd = new FormData($('#checkoutReservationForm')[0]);
        // Add CSRF token if not already present
        if (!fd.has('_token')) {
            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
            if (token) {
                fd.append('_token', token);
            }
        }
        return fd;
    },

    /**
     * Validate form data
     */
    validateForm: function() {
        const reservationId = $('#checkout_reservation_id').val();
        if (!reservationId) {
            Utils.showError(TRANSLATIONS.general.noReservationSelected);
            return false;
        }

        // Validate damage/missing items
        const invalidItems = [];
        
        $('#equipment-return-list .equipment-item').each(function() {
            const condition = $(this).find('.equipment-condition').val();
            const equipmentName = $(this).find('.card-header h6').text();
            
            if (condition === 'damaged' || condition === 'missing') {
                const cost = $(this).find('.equipment-cost').val();
                
                if (!cost || parseFloat(cost) < 0) {
                    invalidItems.push(`${equipmentName}: ${TRANSLATIONS.validation.invalidCost}`);
                }
            }
        });

        if (invalidItems.length > 0) {
            let errorMessage = TRANSLATIONS.validation.validationErrors + '\n' + invalidItems.join('\n');
            Utils.showError(errorMessage);
            return false;
        }

        return true;
    },

    /**
     * Handle successful check-out
     */
    handleSuccess: function(response) {
        Utils.showSuccess(response.message || 'Check-out completed successfully!');
        this.resetForm();
    },

    /**
     * Reset form after successful submission
     */
    resetForm: function() {
        $('#checkoutReservationForm')[0].reset();
        $('#checkout_reservation_id').val('');
        $('#reservation-info-section').addClass('d-none').hide();
        $('#checkoutReservationForm').addClass('d-none').hide();
        $('#search_reservation_number').val('');
        $('#equipment-return-list').empty();
    }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const CheckoutApp = {
    /**
     * Initialize the entire application
     */
    init: function() {
        // Initialize all managers
        ReservationManager.init();
        CheckoutProcessor.init();        
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
    CheckoutApp.init();
});
</script>
@endpush