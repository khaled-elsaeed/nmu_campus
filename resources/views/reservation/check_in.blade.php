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
                    <small class="text-muted">Select equipment to assign to the guest</small>
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
        <form id="checkoutReservationForm" method="POST" action="{{ route('reservations.checkout') }}" class="d-none">
            @csrf
            <input type="hidden" id="checkout_reservation_id" name="reservation_id">
            
            <!-- Equipment Return -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>Equipment Return</h5>
                    <small class="text-muted">Mark the condition of each equipment item being returned</small>
                </div>
                <div class="card-body pt-3">
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

            <!-- Damage Summary (hidden until damages found) -->
            <div id="damage-summary-section" class="card mb-4 shadow-sm border-0 d-none">
                <div class="card-header bg-danger text-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-error-circle me-2"></i>Damage Summary</h5>
                </div>
                <div class="card-body pt-3">
                    <div id="damage-summary-content">
                        <!-- Populated by JS when damages are detected -->
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

<!-- Equipment Image Upload Modal -->
<div class="modal fade" id="equipmentImageUploadModal" tabindex="-1" aria-labelledby="equipmentImageUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="equipmentImageUploadForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="equipmentImageUploadModalLabel">Upload Equipment Image</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="modal_equipment_id" name="modal_equipment_id" value="">
          <div class="mb-3">
            <label for="equipment_image_file" class="form-label">Select Image</label>
            <input class="form-control" type="file" id="equipment_image_file" name="equipment_image_file" accept="image/*">
          </div>
          <div id="equipment_image_preview" class="mb-2" style="display:none;">
            <img src="" alt="Preview" class="img-fluid rounded" style="max-height:200px;">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection 

@push('scripts')
<script>
/**
 * Reservation Check-in/Check-out Page JS
 * - Handles both check-in and check-out processes
 * - Dynamically shows appropriate form based on reservation status
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
const ROUTES = {
    reservations: {
        checkin: '{{ route("reservations.checkin") }}',
        checkout: '{{ route("reservations.checkout") }}',
        findByNumber: '{{ route("reservations.findByNumber") }}'
    },
    equipment: {
        all: '{{ route("equipment.all") }}'
    }
};

const MESSAGES = {
    success: {
        checkinCompleted: 'Guest has been checked in successfully.',
        checkoutCompleted: 'Guest has been checked out successfully.',
        imageUploaded: 'Image uploaded successfully.'
    },
    error: {
        enterReservationNumber: 'Please enter a Reservation Number.',
        reservationNotFound: 'No reservation found for this Reservation Number.',
        fetchReservationFailed: 'Failed to fetch reservation details.',
        checkinFailed: 'Failed to complete check-in.',
        checkoutFailed: 'Failed to complete check-out.',
        noEquipmentToReturn: 'No equipment found to return.',
        invalidDamageData: 'Please provide valid damage information for damaged/missing items.',
        equipmentLoadFailed: 'Failed to load equipment list.',
        imageUploadFailed: 'Failed to upload image. Please try again.'
    }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
const Utils = {
    showError: (message) => {
        Swal.fire({ 
            title: 'Error', 
            html: message, 
            icon: 'error' 
        });
    },

    showSuccess: (message) => {
        Swal.fire({ 
            toast: true, 
            position: 'top-end', 
            icon: 'success', 
            title: message, 
            showConfirmButton: false, 
            timer: 3000, 
            timerProgressBar: true 
        });
    },

    showWarning: (message) => {
        Swal.fire({ 
            title: 'Warning', 
            html: message, 
            icon: 'warning',
            confirmButtonText: 'I Understand'
        });
    },

    disableButton: ($button, disabled = true) => {
        $button.prop('disabled', disabled);
    },

    showElement: ($element) => {
        $element.removeClass('d-none').show();
    },

    hideElement: ($element) => {
        $element.addClass('d-none').hide();
    },

    formatCurrency: (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    getCurrentDate: () => {
        return new Date().toISOString().split('T')[0];
    },

    getCurrentTime: () => {
        return new Date().toTimeString().split(' ')[0].substring(0, 5);
    },

    getStatusBadge: (status) => {
        const badges = {
            'pending': 'bg-warning',
            'confirmed': 'bg-info',
            'checked_in': 'bg-success',
            'checked_out': 'bg-secondary',
            'cancelled': 'bg-danger'
        };
        return badges[status] || 'bg-secondary';
    }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
    request: (options) => {
        return $.ajax(options);
    },

    findReservationByNumber: (reservationNumber) => {
        return ApiService.request({ 
            url: ROUTES.reservations.findByNumber, 
            method: 'GET', 
            data: { 
                reservation_number: reservationNumber
            } 
        });
    },

    fetchEquipment: () => {
        return ApiService.request({ 
            url: ROUTES.equipment.all, 
            method: 'GET' 
        });
    },

    processCheckin: (data) => {
        return ApiService.request({ 
            url: ROUTES.reservations.checkin, 
            method: 'POST', 
            data 
        });
    },

    processCheckout: (data) => {
        return ApiService.request({ 
            url: ROUTES.reservations.checkout, 
            method: 'POST', 
            data 
        });
    },

    uploadEquipmentImage: (equipmentId, file) => {
        // This is a placeholder. You should implement the backend route and logic for image upload.
        // For now, we'll just simulate a successful upload with a Promise.
        // Replace this with your actual AJAX upload logic.
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulate success
                resolve({ success: true, url: URL.createObjectURL(file) });
            }, 1000);
        });
    }
};

// ===========================
// RESERVATION SEARCH MANAGER
// ===========================
const ReservationSearchManager = {
    init: () => {
        ReservationSearchManager.bindEvents();
        ReservationSearchManager.setDefaultDates();
    },

    bindEvents: () => {
        $('#btnSearchReservation').on('click', ReservationSearchManager.handleSearch);
        $('#search_reservation_number').on('keypress', (e) => {
            if (e.which === 13) { // Enter key
                ReservationSearchManager.handleSearch();
            }
        });
    },

    setDefaultDates: () => {
        $('#checkin_date').val(Utils.getCurrentDate());
        $('#checkin_time').val(Utils.getCurrentTime());
        $('#checkout_date').val(Utils.getCurrentDate());
        $('#checkout_time').val(Utils.getCurrentTime());
    },

    handleSearch: () => {
        const reservationNumber = $('#search_reservation_number').val().trim();
        
        if (!reservationNumber) {
            Utils.showError(MESSAGES.error.enterReservationNumber);
            return;
        }

        const $btn = $('#btnSearchReservation');
        Utils.disableButton($btn);

        ApiService.findReservationByNumber(reservationNumber)
            .done((response) => {
                if (response.success && response.data) {
                    ReservationSearchManager.handleReservationFound(response.data);
                } else {
                    ReservationSearchManager.handleReservationNotFound(response.message);
                }
            })
            .fail(() => {
                ReservationSearchManager.handleReservationNotFound();
            })
            .always(() => {
                Utils.disableButton($btn, false);
            });
    },

    handleReservationFound: (reservation) => {
        ReservationSearchManager.displayReservationInfo(reservation);
        Utils.showElement($('#reservation-info-section'));
        
        // Show appropriate form based on reservation status
        if (reservation.status === 'confirmed') {
            // Show check-in form
            $('#checkin_reservation_id').val(reservation.id);
            Utils.showElement($('#checkinReservationForm'));
            Utils.hideElement($('#checkoutReservationForm'));
            Utils.hideElement($('#damage-summary-section'));
            EquipmentCheckinManager.loadAvailableEquipment();
        } else if (reservation.status === 'checked_in') {
            // Show check-out form
            $('#checkout_reservation_id').val(reservation.id);
            Utils.hideElement($('#checkinReservationForm'));
            Utils.showElement($('#checkoutReservationForm'));
            
            if (reservation.equipment && reservation.equipment.length > 0) {
                EquipmentCheckoutManager.renderEquipmentReturnList(reservation.equipment);
            } else {
                EquipmentCheckoutManager.showNoEquipmentMessage();
            }
        } else {
            Utils.showWarning(`Reservation status is "${reservation.status}". Only confirmed reservations can be checked in, and checked-in guests can be checked out.`);
        }
    },

    handleReservationNotFound: (message = null) => {
        Utils.hideElement($('#reservation-info-section'));
        Utils.hideElement($('#checkinReservationForm'));
        Utils.hideElement($('#checkoutReservationForm'));
        Utils.hideElement($('#damage-summary-section'));
        Utils.showError(message || MESSAGES.error.reservationNotFound);
    },

    displayReservationInfo: (reservation) => {
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
    }
};

// ===========================
// EQUIPMENT CHECK-IN MANAGER
// ===========================
const EquipmentCheckinManager = {
    loadAvailableEquipment: () => {
        ApiService.fetchEquipment()
            .done((response) => {
                if (response.success && response.data) {
                    EquipmentCheckinManager.renderEquipmentCheckinList(response.data);
                }
            })
            .fail(() => {
                Utils.showError(MESSAGES.error.equipmentLoadFailed);
            });
    },

    renderEquipmentCheckinList: (equipmentData) => {
        const $list = $('#equipment-checkin-list');
        $list.empty();

        if (!equipmentData.length) {
            $list.append('<div class="col-12 text-muted">No equipment available for assignment.</div>');
            return;
        }

        equipmentData.forEach(item => {
            const html = `
                <div class="col-12 mb-2">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <input class="form-check-input equipment-checkbox" 
                                   type="checkbox" 
                                   value="${item.id}" 
                                   id="checkin_equipment_${item.id}" 
                                   data-name="${item.name_en}">
                        </div>
                        <div class="col">
                            <label class="form-check-label" for="checkin_equipment_${item.id}">
                                ${item.name_en}
                            </label>
                        </div>
                        <div class="col-auto">
                            <input type="number" 
                                   min="1" 
                                   class="form-control equipment-qty" 
                                   id="checkin_equipment_qty_${item.id}" 
                                   name="equipment_qty_${item.id}" 
                                   value="1" 
                                   style="width:70px;" 
                                   disabled>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input equipment-add-note-checkbox" type="checkbox" id="add_note_${item.id}">
                                <label class="form-check-label small" for="add_note_${item.id}">Add Note</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-secondary btn-sm equipment-upload-image-btn" 
                                data-equipment-id="${item.id}" data-equipment-name="${item.name_en}">
                                <i class="bx bx-upload"></i> Upload Image
                            </button>
                            <span class="equipment-image-preview" id="equipment_image_preview_icon_${item.id}" style="display:none;">
                                <i class="bx bx-image text-success"></i>
                            </span>
                        </div>
                        <div class="col" style="min-width:120px;display:none;" id="note_field_${item.id}">
                            <input type="text" class="form-control form-control-sm equipment-note-input" 
                                name="equipment_note_${item.id}" 
                                placeholder="Note (optional)">
                        </div>
                        <input type="hidden" name="equipment_image_${item.id}" id="equipment_image_input_${item.id}">
                    </div>
                </div>
            `;
            $list.append(html);
        });

        // Bind equipment checkbox events
        $list.find('.equipment-checkbox').on('change', function() {
            const id = $(this).val();
            $(`#checkin_equipment_qty_${id}`).prop('disabled', !this.checked);
            // If unchecked, also uncheck note and hide note field
            if (!this.checked) {
                $(`#add_note_${id}`).prop('checked', false);
                $(`#note_field_${id}`).hide();
            }
        });

        // Bind add note checkbox events
        $list.find('.equipment-add-note-checkbox').on('change', function() {
            const id = $(this).attr('id').replace('add_note_', '');
            if ($(this).is(':checked')) {
                $(`#note_field_${id}`).show();
            } else {
                $(`#note_field_${id}`).hide();
                $(`#note_field_${id} input`).val('');
            }
        });

        // Bind upload image button events
        $list.find('.equipment-upload-image-btn').on('click', function() {
            const equipmentId = $(this).data('equipment-id');
            const equipmentName = $(this).data('equipment-name');
            $('#modal_equipment_id').val(equipmentId);
            $('#equipment_image_file').val('');
            $('#equipment_image_preview').hide();
            $('#equipmentImageUploadModalLabel').text('Upload Image for ' + equipmentName);
            $('#equipmentImageUploadModal').modal('show');
        });

        // Select All functionality
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
// EQUIPMENT IMAGE UPLOAD HANDLER
// ===========================
const EquipmentImageUploadHandler = {
    init: () => {
        // Preview image on file select
        $('#equipment_image_file').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#equipment_image_preview img').attr('src', e.target.result);
                    $('#equipment_image_preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#equipment_image_preview').hide();
            }
        });

        // Handle upload form submit
        $('#equipmentImageUploadForm').on('submit', function(e) {
            e.preventDefault();
            const equipmentId = $('#modal_equipment_id').val();
            const fileInput = $('#equipment_image_file')[0];
            if (!fileInput.files.length) {
                Utils.showError('Please select an image to upload.');
                return;
            }
            const file = fileInput.files[0];

            // Simulate upload (replace with real AJAX in production)
            ApiService.uploadEquipmentImage(equipmentId, file)
                .then(response => {
                    if (response.success) {
                        // Store image data (for demo, just store the object URL)
                        $(`#equipment_image_input_${equipmentId}`).val(response.url);
                        $(`#equipment_image_preview_icon_${equipmentId}`).show();
                        Utils.showSuccess(MESSAGES.success.imageUploaded);
                        $('#equipmentImageUploadModal').modal('hide');
                    } else {
                        Utils.showError(MESSAGES.error.imageUploadFailed);
                    }
                })
                .catch(() => {
                    Utils.showError(MESSAGES.error.imageUploadFailed);
                });
        });

        // Reset modal on close
        $('#equipmentImageUploadModal').on('hidden.bs.modal', function() {
            $('#equipment_image_file').val('');
            $('#equipment_image_preview').hide();
            $('#modal_equipment_id').val('');
        });
    }
};

// ===========================
// EQUIPMENT CHECK-OUT MANAGER
// ===========================
const EquipmentCheckoutManager = {
    renderEquipmentReturnList: (equipmentData) => {
        const $list = $('#equipment-return-list');
        $list.empty();
        
        if (!equipmentData.length) {
            EquipmentCheckoutManager.showNoEquipmentMessage();
            return;
        }
        
        equipmentData.forEach((item, index) => {
            const html = `
                <div class="equipment-item mb-3 p-3 border rounded" data-equipment-id="${item.equipment_id}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="mb-1">${item.equipment_name || item.name_en || 'Unknown Equipment'}</h6>
                            <small class="text-muted">Checked out: ${item.quantity || 1} ${item.quantity > 1 ? 'items' : 'item'}</small>
                        </div>
                        <div class="col-md-2">
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
                            <label class="form-label">Estimated Cost</label>
                            <input type="number" 
                                   class="form-control equipment-cost" 
                                   name="equipment[${index}][estimated_cost]" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0">
                        </div>
                    </div>
                    <div class="row mt-2 damage-notes-group" style="display:none;">
                        <div class="col-12">
                            <label class="form-label">Damage/Missing Notes</label>
                            <textarea class="form-control equipment-notes" 
                                      name="equipment[${index}][returned_notes]" 
                                      rows="2" 
                                      placeholder="Describe the damage or circumstances..."></textarea>
                        </div>
                    </div>
                </div>
            `;
            $list.append(html);
        });
        
        // Bind equipment condition change events
        $list.find('.equipment-condition').on('change', EquipmentCheckoutManager.handleConditionChange);
        $list.find('.equipment-qty-returned').on('input', EquipmentCheckoutManager.updateDamageSummary);
        $list.find('.equipment-cost').on('input', EquipmentCheckoutManager.updateDamageSummary);
    },

    showNoEquipmentMessage: () => {
        $('#equipment-return-list').html(`
            <div class="text-center py-4">
                <i class="bx bx-info-circle text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No equipment was checked out with this reservation.</p>
            </div>
        `);
    },

    handleConditionChange: function() {
        const $item = $(this).closest('.equipment-item');
        const condition = $(this).val();
        const $costGroup = $item.find('.damage-cost-group');
        const $notesGroup = $item.find('.damage-notes-group');
        
        if (condition === 'damaged' || condition === 'missing') {
            $costGroup.show();
            $notesGroup.show();
            $item.addClass(condition);
            $item.find('.equipment-cost').prop('required', true);
            $item.find('.equipment-notes').prop('required', true);
        } else {
            $costGroup.hide();
            $notesGroup.hide();
            $item.removeClass('damaged missing');
            $item.find('.equipment-cost').prop('required', false).val('');
            $item.find('.equipment-notes').prop('required', false).val('');
        }
        
        EquipmentCheckoutManager.updateDamageSummary();
    },

    updateDamageSummary: () => {
        const damages = [];
        let totalDamageCost = 0;
        
        $('#equipment-return-list .equipment-item').each(function() {
            const condition = $(this).find('.equipment-condition').val();
            
            if (condition === 'damaged' || condition === 'missing') {
                const equipmentName = $(this).find('h6').text();
                const quantity = parseInt($(this).find('.equipment-qty-returned').val()) || 0;
                const cost = parseFloat($(this).find('.equipment-cost').val()) || 0;
                const notes = $(this).find('.equipment-notes').val();
                
                if (quantity > 0) {
                    damages.push({
                        name: equipmentName,
                        quantity: quantity,
                        condition: condition,
                        cost: cost,
                        notes: notes
                    });
                    totalDamageCost += cost;
                }
            }
        });
        
        if (damages.length > 0) {
            EquipmentCheckoutManager.showDamageSummary(damages, totalDamageCost);
        } else {
            Utils.hideElement($('#damage-summary-section'));
        }
    },

    showDamageSummary: (damages, totalCost) => {
        let html = `
            <div class="alert alert-danger">
                <h6><i class="bx bx-error-circle me-2"></i>Equipment Issues Detected</h6>
                <p class="mb-0">The guest will be charged for the following items:</p>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Equipment</th>
                            <th>Quantity</th>
                            <th>Condition</th>
                            <th>Cost</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        damages.forEach(damage => {
            html += `
                <tr>
                    <td>${damage.name}</td>
                    <td>${damage.quantity}</td>
                    <td>
                        <span class="badge bg-${damage.condition === 'missing' ? 'danger' : 'warning'}">
                            ${damage.condition.charAt(0).toUpperCase() + damage.condition.slice(1)}
                        </span>
                    </td>
                    <td>${Utils.formatCurrency(damage.cost)}</td>
                    <td>${damage.notes || '-'}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                    <tfoot>
                        <tr class="table-danger">
                            <th colspan="3">Total Estimated Cost:</th>
                            <th>${Utils.formatCurrency(totalCost)}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
        
        $('#damage-summary-content').html(html);
        Utils.showElement($('#damage-summary-section'));
    }
};

// ===========================
// CHECK-IN MANAGER
// ===========================
const CheckinManager = {
    init: () => {
        CheckinManager.bindEvents();
    },

    bindEvents: () => {
        $('#checkinReservationForm').on('submit', CheckinManager.handleSubmit);
    },

    handleSubmit: (e) => {
        e.preventDefault();
        CheckinManager.processCheckin();
    },

    processCheckin: () => {
        const formData = CheckinManager.getFormData();
        
        if (!CheckinManager.validateForm(formData)) {
            return;
        }
        
        const $btn = $('#checkinReservationForm button[type="submit"]');
        Utils.disableButton($btn);
        
        ApiService.processCheckin(formData)
            .done((response) => {
                if (response.success) {
                    CheckinManager.handleCheckinSuccess(response);
                } else {
                    Utils.showError(response.message || MESSAGES.error.checkinFailed);
                }
            })
            .fail((xhr) => {
                const message = xhr.responseJSON?.message || MESSAGES.error.checkinFailed;
                Utils.showError(message);
            })
            .always(() => {
                Utils.disableButton($btn, false);
            });
    },

    getFormData: () => {
        const formData = {};
        
        // Serialize form fields
        $('#checkinReservationForm').serializeArray().forEach(item => {
            if (item.value) {
                formData[item.name] = item.value;
            }
        });
        
        // Collect selected equipment
        const equipment = [];
        $('#equipment-checkin-list').find('.equipment-checkbox:checked').each(function() {
            const id = $(this).val();
            const qty = $(`#checkin_equipment_qty_${id}`).val();
            const note = $(`#add_note_${id}`).is(':checked') ? $(`#note_field_${id} input`).val() : '';
            const image = $(`#equipment_image_input_${id}`).val();
            equipment.push({ 
                equipment_id: id, 
                quantity: parseInt(qty) || 1,
                note: note,
                image: image
            });
        });
        
        if (equipment.length) {
            formData.equipment = equipment;
        }
        
        return formData;
    },

    validateForm: (formData) => {
        const reservationId = $('#checkin_reservation_id').val();
        if (!reservationId) {
            Utils.showError('No reservation selected.');
            return false;
        }

        return true;
    },

    handleCheckinSuccess: (response) => {
        Utils.showSuccess(MESSAGES.success.checkinCompleted);
        CheckinManager.resetForm();
    },

    resetForm: () => {
        $('#checkinReservationForm')[0].reset();
        $('#checkin_reservation_id').val('');
        
        // Hide form sections
        Utils.hideElement($('#reservation-info-section'));
        Utils.hideElement($('#checkinReservationForm'));
        
        // Reset search
        $('#search_reservation_number').val('');
        
        // Reset default dates
        ReservationSearchManager.setDefaultDates();
    }
};

// ===========================
// CHECK-OUT MANAGER
// ===========================
const CheckoutManager = {
    init: () => {
        CheckoutManager.bindEvents();
    },

    bindEvents: () => {
        $('#checkoutReservationForm').on('submit', CheckoutManager.handleSubmit);
    },

    handleSubmit: (e) => {
        e.preventDefault();
        CheckoutManager.processCheckout();
    },

    processCheckout: () => {
        const formData = CheckoutManager.getFormData();
        
        if (!CheckoutManager.validateForm(formData)) {
            return;
        }
        
        const $btn = $('#checkoutReservationForm button[type="submit"]');
        Utils.disableButton($btn);
        
        ApiService.processCheckout(formData)
            .done((response) => {
                if (response.success) {
                    CheckoutManager.handleCheckoutSuccess(response);
                } else {
                    Utils.showError(response.message || MESSAGES.error.checkoutFailed);
                }
            })
            .fail((xhr) => {
                const message = xhr.responseJSON?.message || MESSAGES.error.checkoutFailed;
                Utils.showError(message);
            })
            .always(() => {
                Utils.disableButton($btn, false);
            });
    },

    getFormData: () => {
        const formData = new FormData($('#checkoutReservationForm')[0]);
        return formData;
    },

    validateForm: (formData) => {
        const reservationId = $('#checkout_reservation_id').val();
        if (!reservationId) {
            Utils.showError('No reservation selected.');
            return false;
        }

        const checkoutDate = $('#checkout_date').val();
        if (!checkoutDate) {
            Utils.showError('Please select a check-out date.');
            return false;
        }

        // Validate damage data for damaged/missing items
        let hasInvalidDamageData = false;
        $('#equipment-return-list .equipment-item').each(function() {
            const condition = $(this).find('.equipment-condition').val();
            
            if (condition === 'damaged' || condition === 'missing') {
                const cost = $(this).find('.equipment-cost').val();
                const notes = $(this).find('.equipment-notes').val();
                
                if (!cost || parseFloat(cost) < 0) {
                    hasInvalidDamageData = true;
                    return false;
                }
                
                if (!notes || notes.trim() === '') {
                    hasInvalidDamageData = true;
                    return false;
                }
            }
        });

        if (hasInvalidDamageData) {
            Utils.showError(MESSAGES.error.invalidDamageData);
            return false;
        }

        return true;
    },

    handleCheckoutSuccess: (response) => {
        // Show success message with damage summary if applicable
        let message = MESSAGES.success.checkoutCompleted;
        
        if (response.damages && response.damages.length > 0) {
            const totalCost = response.damages.reduce((sum, damage) => sum + parseFloat(damage.estimated_cost || 0), 0);
            message += `<br><br><strong>Damage Charges:</strong> ${Utils.formatCurrency(totalCost)}`;
        }
        
        Utils.showSuccess(message);
        
        // Reset form
        CheckoutManager.resetForm();
    },

    resetForm: () => {
        $('#checkoutReservationForm')[0].reset();
        $('#checkout_reservation_id').val('');
        
        // Hide form sections
        Utils.hideElement($('#reservation-info-section'));
        Utils.hideElement($('#checkoutReservationForm'));
        Utils.hideElement($('#damage-summary-section'));
        
        // Reset search
        $('#search_reservation_number').val('');
        
        // Reset default dates
        ReservationSearchManager.setDefaultDates();
    }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const CheckinCheckoutApp = {
    init: () => {
        ReservationSearchManager.init();
        CheckinManager.init();
        CheckoutManager.init();
        EquipmentImageUploadHandler.init();
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