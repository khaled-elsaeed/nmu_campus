@extends('layouts.home')

@section('title', __('Check-in Reservation'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-ui.page-header 
        :title="__('Reservation Check-in')"
        :description="__('Process guest check-in and equipment assignment')"
        icon="bx bx-log-in-circle"
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
                            <strong>{{ __('Current Status:') }}</strong> <span id="current-status"></span>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Step 3: Check-in Form (for confirmed reservations) -->
        <form id="checkinReservationForm" method="POST" action="{{ route('reservations.checkin') }}" class="d-none" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="checkin_reservation_id" name="reservation_id">
            
            <!-- Equipment Assignment -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>{{ __('Equipment Assignment') }}</h5>
                    <small class="text-muted">{{ __('Select equipment to assign to the guest and specify condition') }}</small>
                </div>
                <div class="card-body pt-3">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select_all_equipment">
                                <label class="form-check-label" for="select_all_equipment">
                                    <strong>{{ __('Select All Equipment') }}</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="equipment-checkin-list" class="row" style="max-height: 250px; overflow-y: auto;">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Check-in Details -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-log-in-circle me-2"></i>{{ __('Check-in Details') }}</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="checkin_notes" class="form-label">{{ __('Check-in Notes') }}</label>
                            <textarea class="form-control" id="checkin_notes" name="checkin_notes" rows="3" placeholder="{{ __('Enter any notes about the check-in process...') }}"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-lg btn-success px-4 shadow">
                    <i class="bx bx-log-in-circle"></i> {{ __('Complete Check-in') }}
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
 * RESERVATION CHECK-IN SYSTEM
 * ========================================
 * 
 * Organized modular system for handling guest check-in processes
 * Features:
 * - Equipment assignment tracking
 * - Real-time form validation
 * - Status tracking and notifications
 */

// ===========================
// Routes
// ===========================
const Routes = {
    reservations: {
        checkin: '{{ route("reservations.checkin") }}',
        findByNumber: '{{ route("reservations.findByNumber") }}'
    },
    equipment: {
        all: '{{ route("equipment.all") }}'
    }
};

// ============================
// TRANSLATIONS
// ============================
const TRANSLATIONS = {
    general: {
        searchReservation: @json(__('Search Reservation')),
        completeCheckin: @json(__('Complete Check-in')),
        searching: @json(__('Searching...')),
        processing: @json(__('Processing...'))
    },
    validation: {
        required: @json(__('This field is required.')),
        invalidCost: @json(__('Please enter a valid cost amount.')),
        missingNotes: @json(__('Please provide notes for damaged/missing items.')),
        noReservationSelected: @json(__('No reservation selected.')),
        reservationNotFound: @json(__('Reservation not found.'))
    },
    equipment: {
        noEquipmentAvailable: @json(__('No equipment available for assignment.'))
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
            url: Routes.reservations.findByNumber, 
            method: 'GET', 
            data: { reservation_number: reservationNumber } 
        });
    },

    /**
     * Fetch all equipment
     */
    fetchEquipment: function() {
        return this.request({ 
            url: Routes.equipment.all, 
            method: 'GET' 
        });
    },

    /**
     * Process check-in
     */
    processCheckin: function(formData) {
        return this.request({ 
            url: Routes.reservations.checkin, 
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
            Utils.showError('Please enter a reservation number.');
            return;
        }

        const $btn = $('#btnSearchReservation');
        Utils.setLoadingState($btn, true, { loadingText: TRANSLATIONS.general.searching });

        ApiService.findReservationByNumber(reservationNumber)
        .done((response) => {
            if (response.success && response.data) {
                this.handleReservationFound(response.data);
            } else {
                this.handleReservationNotFound(response.message || TRANSLATIONS.validation.reservationNotFound);
            }
        }).fail((xhr) => {
            const errorMessage = xhr.responseJSON?.message || 'An error occurred while searching.';
            Utils.showError(errorMessage);
        }).always(() => {
            Utils.setLoadingState($btn, false, { normalText: `<i class="bx bx-search"></i> ${TRANSLATIONS.general.searchReservation}` });
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
        this.setupCheckinForm(reservation);
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
        $('#current-status').text(reservation.status || 'N/A');
    },

    /**
     * Setup check-in form
     */
    setupCheckinForm: function(reservation) {
        $('#checkin_reservation_id').val(reservation.id);
        $('#checkinReservationForm').removeClass('d-none').show();
        EquipmentManager.loadAvailableEquipment();
    },

    /**
     * Reset all forms
     */
    resetForms: function() {
        $('#checkinReservationForm').addClass('d-none').hide();
        $('#checkinReservationForm')[0].reset();
        $('#equipment-checkin-list').empty();
    }
};

// ===========================
// EQUIPMENT MANAGER
// ===========================
const EquipmentManager = {
    /**
     * Load available equipment for check-in
     */
    loadAvailableEquipment: function() {
        ApiService.fetchEquipment()
        .done((response) => {
            if (response.success && response.data) {
                this.renderCheckinEquipment(response.data);
            }
        }).fail((xhr) => {
            const errorMessage = xhr.responseJSON?.message || 'Failed to load equipment.';
            Utils.showError(errorMessage);
        });
    },

    /**
     * Render equipment list for check-in
     */
    renderCheckinEquipment: function(equipmentData) {
        const $list = $('#equipment-checkin-list');
        $list.empty();

        if (!equipmentData.length) {
            $list.append(`<div class="col-12 text-muted">${TRANSLATIONS.equipment.noEquipmentAvailable}</div>`);
            return;
        }

        equipmentData.forEach(item => {
            const html = `
                <div class="col-12 mb-3 equipment-item" data-equipment-id="${item.id}">
                    <div class="card border">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <input class="form-check-input equipment-checkbox" 
                                           type="checkbox" 
                                           value="${item.id}" 
                                           id="checkin_equipment_${item.id}" 
                                           data-name="${item.name_en || item.name || 'Unknown'}">
                                </div>
                                <div class="col">
                                    <label class="form-check-label fw-bold" for="checkin_equipment_${item.id}">
                                        ${item.name_en || item.name || 'Unknown Equipment'}
                                    </label>
                                    <div class="text-muted small">${item.description || 'No description available'}</div>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label small mb-1">Quantity</label>
                                    <input type="number" 
                                           min="1" 
                                           max="10"
                                           class="form-control equipment-qty" 
                                           id="checkin_equipment_qty_${item.id}" 
                                           name="equipment_qty_${item.id}" 
                                           value="1" 
                                           style="width:80px;" 
                                           disabled>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label small mb-1">Condition</label>
                                    <select class="form-control equipment-status" 
                                            name="equipment_status_${item.id}" 
                                            id="checkin_equipment_status_${item.id}" 
                                            style="width:140px;"
                                            disabled>
                                        <option value="good">Good Condition</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="missing">Missing/Lost</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check">
                                        <input class="form-check-input equipment-add-note-checkbox" 
                                               type="checkbox" 
                                               id="add_note_${item.id}">
                                        <label class="form-check-label small" for="add_note_${item.id}">Add Note</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2" style="display:none;" id="note_field_${item.id}">
                                <div class="col-12">
                                    <input type="text" 
                                           class="form-control form-control-sm equipment-note-input" 
                                           name="equipment_note_${item.id}" 
                                           placeholder="Enter notes about this equipment...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $list.append(html);
        });

        this.bindCheckinEvents($list);
    },

    /**
     * Bind check-in equipment events
     */
    bindCheckinEvents: function($list) {
        // Equipment checkbox change
        $list.find('.equipment-checkbox').on('change', function() {
            const id = $(this).val();
            const isChecked = $(this).is(':checked');
            
            $(`#checkin_equipment_qty_${id}`).prop('disabled', !isChecked);
            $(`#checkin_equipment_status_${id}`).prop('disabled', !isChecked);
            
            if (!isChecked) {
                $(`#add_note_${id}`).prop('checked', false).trigger('change');
                $(`#checkin_equipment_status_${id}`).val('good');
            }
        });

        // Add note checkbox change
        $list.find('.equipment-add-note-checkbox').on('change', function() {
            const id = $(this).attr('id').replace('add_note_', '');
            const $noteField = $(`#note_field_${id}`);
            
            if ($(this).is(':checked')) {
                $noteField.show();
            } else {
                $noteField.hide();
                $noteField.find('input').val('');
            }
        });

        // Select all functionality
        $('#select_all_equipment').off('change').on('change', function() {
            const checked = $(this).is(':checked');
            $list.find('.equipment-checkbox').each(function() {
                if ($(this).prop('checked') !== checked) {
                    $(this).prop('checked', checked).trigger('change');
                }
            });
        });
    }
};

// ===========================
// CHECK-IN PROCESSOR
// ===========================
const CheckinProcessor = {
    /**
     * Initialize check-in processor
     */
    init: function() {
        this.bindEvents();
    },

    /**
     * Bind check-in events
     */
    bindEvents: function() {
        $('#checkinReservationForm').on('submit', this.handleSubmit.bind(this));
    },

    /**
     * Handle form submission
     */
    handleSubmit: function(e) {
        e.preventDefault();
        
        const formData = this.getFormData();
        
        if (!this.validateForm(formData)) {
            return;
        }
        
        const $btn = $('#checkinReservationForm button[type="submit"]');
        Utils.setLoadingState($btn, true, { loadingText: TRANSLATIONS.general.processing });

        ApiService.processCheckin(formData)
        .done((response) => {
            if (response.success) {
                this.handleSuccess(response);
            } else {
                Utils.showError(response.message || 'Check-in failed.');
            }
        }).fail((xhr) => {
            const errorMessage = xhr.responseJSON?.message || 'An error occurred during check-in.';
            Utils.showError(errorMessage);
        }).always(() => {
            Utils.setLoadingState($btn, false, { normalText: `<i class="bx bx-log-in-circle"></i> ${TRANSLATIONS.general.completeCheckin}` });
        });
    },

    /**
     * Get form data for submission
     */
    getFormData: function() {
        const fd = new FormData();
        const reservationId = $('#checkin_reservation_id').val();
        const notes = $('#checkin_notes').val();
        
        fd.append('reservation_id', reservationId);
        fd.append('checkin_notes', notes || '');
        
        // Add CSRF token
        fd.append('_token', $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val());
        
        // Collect selected equipment
        const equipment = [];
        $('#equipment-checkin-list').find('.equipment-checkbox:checked').each(function() {
            const id = $(this).val();
            const qty = $(`#checkin_equipment_qty_${id}`).val();
            const status = $(`#checkin_equipment_status_${id}`).val();
            const note = $(`#add_note_${id}`).is(':checked') ? 
                         $(`#note_field_${id} input`).val() : '';
            
            equipment.push({ 
                equipment_id: id, 
                quantity: parseInt(qty) || 1,
                status: status,
                note: note
            });
        });
        
        if (equipment.length) {
            fd.append('equipment', JSON.stringify(equipment));
        }
        
        return fd;
    },

    /**
     * Validate form data
     */
    validateForm: function(formData) {
        const reservationId = $('#checkin_reservation_id').val();
        
        if (!reservationId) {
            Utils.showError(TRANSLATIONS.validation.noReservationSelected);
            return false;
        }

        return true;
    },

    /**
     * Handle successful check-in
     */
    handleSuccess: function(response) {
        Utils.showSuccess(response.message || 'Check-in completed successfully!');
        this.resetForm();
    },

    /**
     * Reset form after successful submission
     */
    resetForm: function() {
        $('#checkinReservationForm')[0].reset();
        $('#checkin_reservation_id').val('');
        $('#reservation-info-section').addClass('d-none').hide();
        $('#checkinReservationForm').addClass('d-none').hide();
        $('#search_reservation_number').val('');
        $('#equipment-checkin-list').empty();
        $('#select_all_equipment').prop('checked', false);
    }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const CheckinApp = {
    /**
     * Initialize the entire application
     */
    init: function() {
        ReservationManager.init();
        CheckinProcessor.init();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
    CheckinApp.init();
});
</script>
@endpush