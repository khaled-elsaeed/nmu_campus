@extends('layouts.home')

@section('title', 'Check-in | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-ui.page-header 
        title="Guest Check-in"
        description="Process guest check-in with equipment assignment."
        icon="bx bx-log-in-circle"
    >
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to List
        </a>
    </x-ui.page-header>

    <div class="row justify-content-center">
        <!-- Step 1: Reservation Number Search -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-id-card me-2"></i>Search Reservation</h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="search_reservation_number" placeholder="Enter Reservation Number...">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" id="btnSearchReservation">
                            <i class="bx bx-search"></i> Search Reservation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Reservation Info (hidden until found) -->
        <div id="reservation-info-section" class="card mb-4 shadow-sm border-0 d-none">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Reservation Details</h5>
            </div>
            <div class="card-body pt-3">
                <div id="reservation-info-content">
                    <!-- Populated by JS -->
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
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>Equipment Assignment</h5>
                    <small class="text-muted">Select equipment to assign to the guest and specify condition</small>
                </div>
                <div class="card-body pt-3">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select_all_equipment">
                                <label class="form-check-label" for="select_all_equipment">
                                    <strong>Select All Equipment</strong>
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
                    <h5 class="mb-0"><i class="bx bx-log-in-circle me-2"></i>Check-in Details</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="checkin_notes" class="form-label">Check-in Notes</label>
                            <textarea class="form-control" id="checkin_notes" name="checkin_notes" rows="3" placeholder="Enter any notes about the check-in process..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-lg btn-success px-4 shadow">
                    <i class="bx bx-log-in-circle"></i> Complete Check-in
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
// CONFIGURATION & CONSTANTS
// ===========================
const CONFIG = {
    routes: {
        reservations: {
            checkin: '{{ route("reservations.checkin") }}',
            findByNumber: '{{ route("reservations.findByNumber") }}'
        },
        equipment: {
            all: '{{ route("equipment.all") }}'
        }
    },
    
    messages: {
        success: {
            checkinCompleted: 'Guest has been checked in successfully.'
        },
        error: {
            enterReservationNumber: 'Please enter a Reservation Number.',
            reservationNotFound: 'No reservation found for this Reservation Number.',
            fetchReservationFailed: 'Failed to fetch reservation details.',
            checkinFailed: 'Failed to complete check-in.',
            equipmentLoadFailed: 'Failed to load equipment list.'
        },
        validation: {
            required: 'This field is required.',
            invalidCost: 'Please enter a valid cost amount.',
            missingNotes: 'Please provide notes for damaged/missing items.'
        }
    },

    ui: {
        statusBadges: {
            'pending': 'bg-warning',
            'confirmed': 'bg-info',
            'checked_in': 'bg-success',
            'checked_out': 'bg-secondary',
            'cancelled': 'bg-danger',
            'good': 'bg-success',
            'damaged': 'bg-warning',
            'missing': 'bg-danger'
        }
    }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
    /**
     * Generic AJAX request wrapper
     */
    async request(options) {
        try {
            const response = await $.ajax({
                ...options,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    ...options.headers
                }
            });
            return response;
        } catch (error) {
            if (error && error.xhr) {
                Utils.handleAjaxError(error.xhr, 'API Request failed.');
            }
            throw error;
        }
    },

    /**
     * Find reservation by number
     */
    findReservationByNumber(reservationNumber) {
        return this.request({ 
            url: CONFIG.routes.reservations.findByNumber, 
            method: 'GET', 
            data: { reservation_number: reservationNumber } 
        });
    },

    /**
     * Fetch all equipment
     */
    fetchEquipment() {
        return this.request({ 
            url: CONFIG.routes.equipment.all, 
            method: 'GET' 
        });
    },

    /**
     * Process check-in
     */
    processCheckin(formData) {
        return this.request({ 
            url: CONFIG.routes.reservations.checkin, 
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
    init() {
        this.bindEvents();
        this.setDefaultDates();
    },

    /**
     * Bind reservation search events
     */
    bindEvents() {
        $('#btnSearchReservation').on('click', this.handleSearch.bind(this));
        $('#search_reservation_number').on('keypress', (e) => {
            if (e.which === 13) { // Enter key
                this.handleSearch();
            }
        });
    },

    /**
     * Set default dates and times
     */
    setDefaultDates() {
        // Use global Utils for date/time
        $('#checkin_date').val(Utils.getCurrentDate ? Utils.getCurrentDate() : (new Date().toISOString().split('T')[0]));
        $('#checkin_time').val(Utils.getCurrentTime ? Utils.getCurrentTime() : (new Date().toTimeString().split(' ')[0].substring(0, 5)));
    },

    /**
     * Handle reservation search
     */
    async handleSearch() {
        const reservationNumber = $('#search_reservation_number').val().trim();
        
        if (!reservationNumber) {
            Utils.showError(CONFIG.messages.error.enterReservationNumber);
            return;
        }

        const $btn = $('#btnSearchReservation');
        Utils.setLoadingState($btn, true, { loadingText: 'Searching...' });

        try {
            const response = await ApiService.findReservationByNumber(reservationNumber);
            
            if (response.success && response.data) {
                this.handleReservationFound(response.data);
            } else {
                this.handleReservationNotFound(response.message);
            }
        } catch (error) {
            this.handleReservationNotFound();
        } finally {
            Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-search"></i> Search Reservation' });
        }
    },

    /**
     * Handle successful reservation found
     */
    handleReservationFound(reservation) {
        this.displayReservationInfo(reservation);
        Utils.showElement($('#reservation-info-section'));

        // Reset forms
        this.resetForms();

        if (reservation.status === 'confirmed') {
            this.setupCheckinForm(reservation);
        } else if (reservation.status === 'checked_in') {
            Utils.showWarning(`Guest is already checked in. Check-in is not required.`);
        } else {
            Utils.showWarning(`Reservation status is "${reservation.status}". Only confirmed reservations can be checked in.`);
        }
    },

    /**
     * Handle reservation not found
     */
    handleReservationNotFound(message = null) {
        this.resetForms();
        $('#reservation-info-section').hide();
        Utils.showError(message || CONFIG.messages.error.reservationNotFound);
    },

    /**
     * Display reservation information
     */
    displayReservationInfo(reservation) {
        const user = reservation.user || {};
        const accommodation = reservation.accommodation || {};
        const statusBadge = Utils.getStatusBadge(reservation.status);
        
        let actionText = '';
        if (reservation.status === 'confirmed') {
            actionText = '<i class="bx bx-log-in-circle text-success me-1"></i>Ready for Check-in';
        } else if (reservation.status === 'checked_in') {
            actionText = '<i class="bx bx-check-circle text-success me-1"></i>Already Checked In';
        }
        
        const html = `
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Guest Name:</strong> ${user.name_en || user.name_ar || '-'}
                </div>
                <div class="col-md-6">
                    <strong>Email:</strong> ${user.email || '-'}
                </div>
                <div class="col-md-6">
                    <strong>Accommodation:</strong> ${accommodation.number || accommodation.name || '-'}
                </div>
                <div class="col-md-6">
                    <strong>Reservation Period:</strong> ${reservation.check_in_date || '-'} to ${reservation.check_out_date || '-'}
                </div>
                <div class="col-md-6">
                    <strong>Current Status:</strong> 
                    <span class="badge ${statusBadge}">${reservation.status}</span>
                </div>
                <div class="col-md-6">
                    <strong>Action:</strong> ${actionText}
                </div>
            </div>
        `;
        $('#reservation-info-content').html(html);
    },

    /**
     * Setup check-in form
     */
    setupCheckinForm(reservation) {
        $('#checkin_reservation_id').val(reservation.id);
        Utils.showElement($('#checkinReservationForm'));
        EquipmentManager.loadAvailableEquipment();
    },

    /**
     * Reset all forms
     */
    resetForms() {
        $('#checkinReservationForm').hide();
        $('#checkinReservationForm')[0].reset();
    }
};

// ===========================
// EQUIPMENT MANAGER
// ===========================
const EquipmentManager = {
    /**
     * Load available equipment for check-in
     */
    async loadAvailableEquipment() {
        try {
            const response = await ApiService.fetchEquipment();
            if (response.success && response.data) {
                this.renderCheckinEquipment(response.data);
            }
        } catch (error) {
            Utils.showError(CONFIG.messages.error.equipmentLoadFailed);
        }
    },

    /**
     * Render equipment list for check-in
     */
    renderCheckinEquipment(equipmentData) {
        const $list = $('#equipment-checkin-list');
        $list.empty();

        if (!equipmentData.length) {
            $list.append('<div class="col-12 text-muted">No equipment available for assignment.</div>');
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
                                           data-name="${item.name_en}">
                                </div>
                                <div class="col">
                                    <label class="form-check-label fw-bold" for="checkin_equipment_${item.id}">
                                        ${item.name_en}
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
    bindCheckinEvents($list) {
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
    init() {
        this.bindEvents();
    },

    /**
     * Bind check-in events
     */
    bindEvents() {
        $('#checkinReservationForm').on('submit', this.handleSubmit.bind(this));
    },

    /**
     * Handle form submission
     */
    async handleSubmit(e) {
        e.preventDefault();
        
        const formData = this.getFormData();
        
        if (!this.validateForm(formData)) {
            return;
        }
        
        const $btn = $('#checkinReservationForm button[type="submit"]');
        Utils.setLoadingState($btn, true, { loadingText: 'Processing...' });

        try {
            const response = await ApiService.processCheckin(formData);
            
            if (response.success) {
                this.handleSuccess(response);
            } else {
                Utils.showError(response.message || CONFIG.messages.error.checkinFailed);
            }
        } catch (error) {
            // Use global error handler if available
            if (error && error.xhr) {
                Utils.handleAjaxError(error.xhr, CONFIG.messages.error.checkinFailed);
            }
        } finally {
            Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-log-in-circle"></i> Complete Check-in' });
        }
    },

    /**
     * Get form data for submission
     */
    getFormData() {
        const fd = new FormData();
        const reservationId = $('#checkin_reservation_id').val();
        const notes = $('#checkin_notes').val();
        
        fd.append('reservation_id', reservationId);
        fd.append('checkin_notes', notes || '');
        
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
    validateForm(formData) {
        const reservationId = $('#checkin_reservation_id').val();
        
        if (!reservationId) {
            Utils.showError('No reservation selected.');
            return false;
        }

        return true;
    },

    /**
     * Handle successful check-in
     */
    handleSuccess(response) {
        Utils.showSuccess(CONFIG.messages.success.checkinCompleted);
        this.resetForm();
    },

    /**
     * Reset form after successful submission
     */
    resetForm() {
        $('#checkinReservationForm')[0].reset();
        $('#checkin_reservation_id').val('');
        $('#reservation-info-section').hide();
        $('#checkinReservationForm').hide();
        $('#search_reservation_number').val('');
        ReservationManager.setDefaultDates();
    }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const CheckinApp = {
    /**
     * Initialize the entire application
     */
    init() {
        // Use global Utils for logging if desired
        if (window.console) console.log('Initializing Check-in System...');
        
        // Initialize all managers
        ReservationManager.init();
        CheckinProcessor.init();
        
        // Set up global error handling
        this.setupErrorHandling();
        
        if (window.console) console.log('Check-in System initialized successfully');
    },

    /**
     * Setup global error handling
     */
    setupErrorHandling() {
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 419) {
                Utils.showError('Session expired. Please refresh the page and try again.');
            }
        });
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