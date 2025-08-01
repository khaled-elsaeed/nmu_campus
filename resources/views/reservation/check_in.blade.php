@extends('layouts.home')

@section('title', 'Check-in / Check-out | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-ui.page-header 
        title="Guest Check-in / Check-out"
        description="Process guest check-in with equipment assignment or check-out with equipment return."
        icon="bx bx-transfer-alt"
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

        <!-- Step 3: Check-out Form (for checked-in reservations) -->
        <form id="checkoutReservationForm" method="POST" action="{{ route('reservations.checkout') }}" class="d-none" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="checkout_reservation_id" name="reservation_id">
            
            <!-- Equipment Return -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>Equipment Return</h5>
                    <small class="text-muted">Mark the condition of each equipment item being returned</small>
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
                    <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>Check-out Summary</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="checkout_notes" class="form-label">Check-out Notes</label>
                            <textarea class="form-control" id="checkout_notes" name="checkout_notes" rows="3" placeholder="Enter any notes about the check-out process..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-lg btn-danger px-4 shadow">
                    <i class="bx bx-log-out-circle"></i> Complete Check-out
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
 * RESERVATION CHECK-IN/CHECK-OUT SYSTEM
 * ========================================
 * 
 * Organized modular system for handling guest check-in and check-out processes
 * Features:
 * - Equipment assignment and return tracking
 * - Real-time form validation
 * - Damage cost estimation
 * - Status tracking and notifications
 */

// ===========================
// CONFIGURATION & CONSTANTS
// ===========================
const CONFIG = {
    routes: {
        reservations: {
            checkin: '{{ route("reservations.checkin") }}',
            checkout: '{{ route("reservations.checkout") }}',
            findByNumber: '{{ route("reservations.findByNumber") }}'
        },
        equipment: {
            all: '{{ route("equipment.all") }}'
        }
    },
    
    messages: {
        success: {
            checkinCompleted: 'Guest has been checked in successfully.',
            checkoutCompleted: 'Guest has been checked out successfully.'
        },
        error: {
            enterReservationNumber: 'Please enter a Reservation Number.',
            reservationNotFound: 'No reservation found for this Reservation Number.',
            fetchReservationFailed: 'Failed to fetch reservation details.',
            checkinFailed: 'Failed to complete check-in.',
            checkoutFailed: 'Failed to complete check-out.',
            noEquipmentToReturn: 'No equipment found to return.',
            invalidDamageData: 'Please provide valid damage information (cost and notes) for damaged/missing items.',
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
    },

    /**
     * Process check-out
     */
    processCheckout(formData) {
        return this.request({ 
            url: CONFIG.routes.reservations.checkout, 
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
        $('#checkin_date').val(Utils.getCurrentDate() : (new Date().toISOString().split('T')[0]));
        $('#checkin_time').val(Utils.getCurrentTime() : (new Date().toTimeString().split(' ')[0].substring(0, 5)));
        $('#checkout_date').val(Utils.getCurrentDate() : (new Date().toISOString().split('T')[0]));
        $('#checkout_time').val(Utils.getCurrentTime() : (new Date().toTimeString().split(' ')[0].substring(0, 5)));
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
            this.setupCheckoutForm(reservation);
        } else {
            Utils.showWarning(`Reservation status is "${reservation.status}". Only confirmed reservations can be checked in, and checked-in guests can be checked out.`);
        }
    },

    /**
     * Handle reservation not found
     */
    handleReservationNotFound(message = null) {
        this.resetForms();
        Utils.hideElement($('#reservation-info-section'));
        Utils.showError(message || CONFIG.messages.error.reservationNotFound) ;
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
            actionText = '<i class="bx bx-log-out-circle text-danger me-1"></i>Ready for Check-out';
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
     * Setup check-out form
     */
    setupCheckoutForm(reservation) {
        $('#checkout_reservation_id').val(reservation.id);
        Utils.showElement($('#checkoutReservationForm'));

        let allEquipmentDetails = [];
        if (Array.isArray(reservation.equipment_tracking)) {
            reservation.equipment_tracking.forEach(tracking => {
                if (Array.isArray(tracking.equipment_details)) {
                    allEquipmentDetails = allEquipmentDetails.concat(tracking.equipment_details);
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
    resetForms() {
        Utils.hideElement($('#checkinReservationForm'), false);
        Utils.hideElement($('#checkoutReservationForm'), false);
        $('#checkinReservationForm')[0].reset();
        $('#checkoutReservationForm')[0].reset();
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
    },

    /**
     * Render equipment list for check-out
     */
    renderCheckoutEquipment(equipmentData) {
        const $list = $('#equipment-return-list');
        $list.empty();

        if (!equipmentData.length) {
            this.showNoEquipmentMessage();
            return;
        }

        equipmentData.forEach((item, index) => {
            const html = `
                <div class="equipment-item mb-4 border rounded" data-equipment-id="${item.equipment_id}">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">${item.equipment.name || 'Unknown Equipment'}</h6>
                        <small class="text-muted">Checked out: ${item.quantity || 1} ${item.quantity > 1 ? 'items' : 'item'}</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Quantity Returned</label>
                                <input type="number" 
                                       class="form-control equipment-qty-returned" 
                                       name="equipment[${index}][quantity]" 
                                       value="${item.quantity || 1}" 
                                       min="0" 
                                       max="${item.quantity || 1}">
                                <input type="hidden" 
                                       name="equipment[${index}][equipment_id]" 
                                       value="${item.equipment_id}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Condition</label>
                                <select class="form-control equipment-condition" 
                                        name="equipment[${index}][returned_status]">
                                    <option value="good">Good Condition</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="missing">Missing/Lost</option>
                                </select>
                            </div>
                            <div class="col-md-3 damage-cost-group" style="display:none;">
                                <label class="form-label">Estimated Cost ($)</label>
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
                                <label class="form-label">Damage/Missing Notes</label>
                                <textarea class="form-control equipment-notes" 
                                          name="equipment[${index}][returned_notes]" 
                                          rows="3" 
                                          placeholder="Describe the damage, missing circumstances, or any other relevant information..."></textarea>
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
    bindCheckoutEvents($list) {
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
                $item.find('.equipment-cost').prop('required', true);
                $item.find('.equipment-notes').prop('required', true);
            } else {
                $costGroup.hide();
                $notesGroup.hide();
                $item.removeClass('border-warning border-danger');
                $item.find('.equipment-cost').prop('required', false).val('');
                $item.find('.equipment-notes').prop('required', false).val('');
            }
        });
    },

    /**
     * Show no equipment message
     */
    showNoEquipmentMessage() {
        $('#equipment-return-list').html(`
            <div class="text-center py-5">
                <i class="bx bx-info-circle text-muted" style="font-size: 4rem;"></i>
                <h5 class="text-muted mt-3">No Equipment Found</h5>
                <p class="text-muted">No equipment was checked out with this reservation.</p>
            </div>
        `);
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
        Utils.hideElement($('#reservation-info-section'));
        Utils.hideElement($('#checkinReservationForm'));
        $('#search_reservation_number').val('');
        ReservationManager.setDefaultDates();
    }
};

// ===========================
// CHECK-OUT PROCESSOR
// ===========================
const CheckoutProcessor = {
    /**
     * Initialize check-out processor
     */
    init() {
        this.bindEvents();
    },

    /**
     * Bind check-out events
     */
    bindEvents() {
        $('#checkoutReservationForm').on('submit', this.handleSubmit.bind(this));
    },

    /**
     * Handle form submission
     */
    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateForm()) {
            return;
        }
        
        const formData = this.getFormData();
        
        const $btn = $('#checkoutReservationForm button[type="submit"]');
            Utils.setLoadingState($btn, true, { loadingText: 'Processing...' });

        try {
            const response = await ApiService.processCheckout(formData);
            
            if (response.success) {
                this.handleSuccess(response);
            } else {
                Utils.showError(response.message || CONFIG.messages.error.checkoutFailed);
            }
        } catch (error) {
            if (error && error.xhr) {
                Utils.handleAjaxError(error.xhr, CONFIG.messages.error.checkoutFailed);
            }
        } finally {
            Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-log-out-circle"></i> Complete Check-out' });
        }
    },

    /**
     * Get form data for submission
     */
    getFormData() {
        const fd = new FormData($('#checkoutReservationForm')[0]);
        return fd;
    },

    /**
     * Validate form data
     */
    validateForm() {
        const reservationId = $('#checkout_reservation_id').val();
        
        if (!reservationId) {
            Utils.showError('No reservation selected.');
            return false;
        }

        // Validate damage/missing items
        let hasInvalidDamageData = false;
        const invalidItems = [];
        
        $('#equipment-return-list .equipment-item').each(function() {
            const condition = $(this).find('.equipment-condition').val();
            const equipmentName = $(this).find('.card-header h6').text();
            
            if (condition === 'damaged' || condition === 'missing') {
                const cost = $(this).find('.equipment-cost').val();
                const notes = $(this).find('.equipment-notes').val();
                
                if (!cost || parseFloat(cost) < 0) {
                    invalidItems.push(`${equipmentName}: Missing or invalid cost`);
                    hasInvalidDamageData = true;
                }
                
                if (!notes || notes.trim() === '') {
                    invalidItems.push(`${equipmentName}: Missing notes`);
                    hasInvalidDamageData = true;
                }
            }
        });

        if (hasInvalidDamageData) {
            const errorMessage = `Please fix the following issues:<br>• ${invalidItems.join('<br>• ')}`;
            Utils.showError(errorMessage);
            return false;
        }

        return true;
    },

    /**
     * Handle successful check-out
     */
    handleSuccess(response) {
        let message = CONFIG.messages.success.checkoutCompleted;
        
        // Add damage summary if applicable
        if (response.damages && response.damages.length > 0) {
            // Use global Utils.formatCurrency if available
            const formatCurrency = Utils.formatCurrency : (amt) => '$' + (amt || 0).toFixed(2);

            const totalCost = response.damages.reduce((sum, damage) => 
                sum + parseFloat(damage.estimated_cost || 0), 0);
            message += `<br><br><strong>Damage Charges:</strong> ${formatCurrency(totalCost)}`;
            
            const damageList = response.damages.map(damage => 
                `• ${damage.equipment_name}: ${formatCurrency(damage.estimated_cost)}`
            ).join('<br>');
            message += `<br><br><strong>Details:</strong><br>${damageList}`;
        }
        
        if (window.Swal) {
            Swal.fire({
                title: 'Check-out Complete',
                html: message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            alert(message.replace(/<br>/g, '\n').replace(/<strong>.*?<\/strong>/g, ''));
        }
        
        this.resetForm();
    },

    /**
     * Reset form after successful submission
     */
    resetForm() {
        $('#checkoutReservationForm')[0].reset();
        $('#checkout_reservation_id').val('');
        Utils.hideElement($('#reservation-info-section'));
        Utils.hideElement($('#checkoutReservationForm'));
        $('#search_reservation_number').val('');
        ReservationManager.setDefaultDates();
    }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const CheckinCheckoutApp = {
    /**
     * Initialize the entire application
     */
    init() {
        // Use global Utils for logging if desired
        if (window.console) console.log('Initializing Check-in/Check-out System...');
        
        // Initialize all managers
        ReservationManager.init();
        CheckinProcessor.init();
        CheckoutProcessor.init();
        
        // Set up global error handling
        this.setupErrorHandling();
        
        if (window.console) console.log('Check-in/Check-out System initialized successfully');
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
    CheckinCheckoutApp.init();
});
</script>
@endpush