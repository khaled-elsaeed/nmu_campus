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

<!-- Equipment Image Upload Modal -->
<div class="modal fade" id="equipmentImageUploadModal" tabindex="-1" aria-labelledby="equipmentImageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentImageUploadModalLabel">Manage Equipment Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_equipment_id" name="modal_equipment_id" value="">
                <input type="hidden" id="modal_equipment_index" name="modal_equipment_index" value="">
                
                <!-- File Upload Section -->
                <div class="mb-3">
                    <label for="equipment_image_file" class="form-label">Select Images</label>
                    <input class="form-control" type="file" id="equipment_image_file" name="equipment_image_file" accept="image/*" multiple>
                    <div class="form-text">You can select multiple images. Supported formats: JPG, PNG, GIF</div>
                </div>

                <!-- Attached Images Grid -->
                <div id="attached_images_container" class="mb-3">
                    <h6>Attached Images</h6>
                    <div id="attached_images_grid" class="row g-2">
                        <!-- Images will be populated here -->
                    </div>
                    <div id="no_images_message" class="text-muted text-center py-3">
                        No images attached yet
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="attach_images_btn" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Add Selected Images
                </button>
            </div>
        </div>
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
 * - Multiple image attachments with delete functionality
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
            checkoutCompleted: 'Guest has been checked out successfully.',
            imagesAttached: 'Images attached successfully.',
            imageDeleted: 'Image removed successfully.'
        },
        error: {
            enterReservationNumber: 'Please enter a Reservation Number.',
            reservationNotFound: 'No reservation found for this Reservation Number.',
            fetchReservationFailed: 'Failed to fetch reservation details.',
            checkinFailed: 'Failed to complete check-in.',
            checkoutFailed: 'Failed to complete check-out.',
            noEquipmentToReturn: 'No equipment found to return.',
            invalidDamageData: 'Please provide valid damage information (cost and notes) for damaged/missing items.',
            equipmentLoadFailed: 'Failed to load equipment list.',
            imageUploadFailed: 'Failed to attach images. Please try again.',
            noImagesSelected: 'Please select at least one image to attach.'
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
        },
        maxImageSize: 5 * 1024 * 1024, // 5MB
        supportedImageTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    }
};

// Global storage for equipment images
window.equipmentImages = {};

// ===========================
// UTILITY FUNCTIONS
// ===========================
const Utils = {
    /**
     * Show error message using SweetAlert
     */
    showError(message) {
        Swal.fire({ 
            title: 'Error', 
            html: message, 
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    },

    /**
     * Show success message using SweetAlert toast
     */
    showSuccess(message) {
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

    /**
     * Show warning message using SweetAlert
     */
    showWarning(message) {
        Swal.fire({ 
            title: 'Warning', 
            html: message, 
            icon: 'warning',
            confirmButtonText: 'I Understand',
            confirmButtonColor: '#f39c12'
        });
    },

    /**
     * Show confirmation dialog
     */
    async showConfirm(title, message, confirmText = 'Yes', cancelText = 'No') {
        const result = await Swal.fire({
            title,
            html: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        });
        return result.isConfirmed;
    },

    /**
     * Enable/disable button with loading state
     */
    toggleButton($button, disabled = true, loadingText = 'Loading...') {
        if (disabled) {
            $button.prop('disabled', true);
            if (!$button.data('original-text')) {
                $button.data('original-text', $button.html());
            }
            $button.html(`<i class="bx bx-loader-alt bx-spin"></i> ${loadingText}`);
        } else {
            $button.prop('disabled', false);
            const originalText = $button.data('original-text');
            if (originalText) {
                $button.html(originalText);
            }
        }
    },

    /**
     * Show/hide element with animation
     */
    showElement($element, animate = true) {
        $element.removeClass('d-none');
        if (animate) {
            $element.hide().fadeIn(300);
        } else {
            $element.show();
        }
    },

    /**
     * Hide element with animation
     */
    hideElement($element, animate = true) {
        if (animate) {
            $element.fadeOut(300, function() {
                $(this).addClass('d-none');
            });
        } else {
            $element.addClass('d-none').hide();
        }
    },

    /**
     * Format currency amount
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount || 0);
    },

    /**
     * Get current date in YYYY-MM-DD format
     */
    getCurrentDate() {
        return new Date().toISOString().split('T')[0];
    },

    /**
     * Get current time in HH:MM format
     */
    getCurrentTime() {
        return new Date().toTimeString().split(' ')[0].substring(0, 5);
    },

    /**
     * Get Bootstrap badge class for status
     */
    getStatusBadge(status) {
        return CONFIG.ui.statusBadges[status] || 'bg-secondary';
    },

    /**
     * Generate unique ID
     */
    generateId() {
        return Math.random().toString(36).substr(2, 9);
    },

    /**
     * Validate image file
     */
    validateImageFile(file) {
        if (!CONFIG.ui.supportedImageTypes.includes(file.type)) {
            return { valid: false, message: 'Unsupported file type. Please select a valid image file.' };
        }
        
        if (file.size > CONFIG.ui.maxImageSize) {
            return { valid: false, message: 'File size too large. Maximum size is 5MB.' };
        }
        
        return { valid: true };
    },

    /**
     * Format file size for display
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
            console.error('API Request failed:', error);
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
// IMAGE MANAGER
// ===========================
const ImageManager = {
    /**
     * Initialize image manager
     */
    init() {
        this.bindEvents();
    },

    /**
     * Bind image-related events
     */
    bindEvents() {
        // File input change
        $('#equipment_image_file').on('change', this.handleFileSelection.bind(this));
        
        // Add images button
        $('#attach_images_btn').on('click', this.handleAttachImages.bind(this));
        
        // Modal reset on close
        $('#equipmentImageUploadModal').on('hidden.bs.modal', this.resetModal.bind(this));
    },

    /**
     * Handle file selection
     */
    handleFileSelection(e) {
        const files = Array.from(e.target.files);
        
        if (files.length === 0) {
            return;
        }

        // Validate files
        const validFiles = [];
        const invalidFiles = [];

        files.forEach(file => {
            const validation = Utils.validateImageFile(file);
            if (validation.valid) {
                validFiles.push(file);
            } else {
                invalidFiles.push({ file, message: validation.message });
            }
        });

        // Show validation errors
        if (invalidFiles.length > 0) {
            const errorMessages = invalidFiles.map(item => 
                `${item.file.name}: ${item.message}`
            ).join('<br>');
            Utils.showError(`Some files could not be processed:<br>${errorMessages}`);
        }

        // Update UI to show selection
        if (validFiles.length > 0) {
            this.updateFilePreview(validFiles);
        }
    },

    /**
     * Update file selection preview
     */
    updateFilePreview(files) {
        const $btn = $('#attach_images_btn');
        if (files.length > 0) {
            $btn.html(`<i class="bx bx-plus"></i> Add ${files.length} Image(s)`);
            $btn.removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $btn.html('<i class="bx bx-plus"></i> Add Selected Images');
            $btn.removeClass('btn-primary').addClass('btn-secondary');
        }
    },

    /**
     * Handle attach images
     */
    async handleAttachImages() {
        const files = Array.from($('#equipment_image_file')[0].files);
        
        if (files.length === 0) {
            Utils.showError(CONFIG.messages.error.noImagesSelected);
            return;
        }

        const equipmentId = $('#modal_equipment_id').val();
        const equipmentIndex = $('#modal_equipment_index').val();
        const key = `${equipmentId}_${equipmentIndex}`;

        // Initialize storage if not exists
        if (!window.equipmentImages[key]) {
            window.equipmentImages[key] = [];
        }

        // Process each file
        for (const file of files) {
            const validation = Utils.validateImageFile(file);
            if (validation.valid) {
                const imageData = await this.processImageFile(file);
                window.equipmentImages[key].push(imageData);
            }
        }

        this.updateAttachedImagesDisplay(key);
        this.updateEquipmentImageCounter(equipmentId, equipmentIndex);
        Utils.showSuccess(CONFIG.messages.success.imagesAttached);
        
        // Reset file input
        $('#equipment_image_file').val('');
        this.updateFilePreview([]);
    },

    /**
     * Process image file to base64
     */
    processImageFile(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                resolve({
                    id: Utils.generateId(),
                    file: file,
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    dataUrl: e.target.result,
                    timestamp: Date.now()
                });
            };
            reader.readAsDataURL(file);
        });
    },

    /**
     * Update attached images display in modal
     */
    updateAttachedImagesDisplay(key) {
        const images = window.equipmentImages[key] || [];
        const $grid = $('#attached_images_grid');
        const $noMessage = $('#no_images_message');

        $grid.empty();

        if (images.length === 0) {
            $noMessage.show();
            return;
        }

        $noMessage.hide();

        images.forEach(image => {
            const $imageCard = $(`
                <div class="col-md-4 col-sm-6">
                    <div class="card image-card">
                        <div class="card-body p-2">
                            <div class="position-relative">
                                <img src="${image.dataUrl}" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                        onclick="ImageManager.deleteImage('${key}', '${image.id}')">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted d-block text-truncate">${image.name}</small>
                                <small class="text-muted">${Utils.formatFileSize(image.size)}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $grid.append($imageCard);
        });
    },

    /**
     * Delete image
     */
    async deleteImage(key, imageId) {
        const confirmed = await Utils.showConfirm(
            'Delete Image',
            'Are you sure you want to remove this image?',
            'Delete',
            'Cancel'
        );

        if (!confirmed) return;

        // Remove from storage
        if (window.equipmentImages[key]) {
            window.equipmentImages[key] = window.equipmentImages[key].filter(img => img.id !== imageId);
        }

        // Update displays
        this.updateAttachedImagesDisplay(key);
        
        const [equipmentId, equipmentIndex] = key.split('_');
        this.updateEquipmentImageCounter(equipmentId, equipmentIndex);
        
        Utils.showSuccess(CONFIG.messages.success.imageDeleted);
    },

    /**
     * Update equipment image counter in main form
     */
    updateEquipmentImageCounter(equipmentId, equipmentIndex) {
        const key = `${equipmentId}`;
        const images = window.equipmentImages[key] || [];
        const $counter = $(`#equipment_images_counter_${equipmentId}_${equipmentIndex}`);
        
        if (images.length > 0) {
            $counter.html(`<i class="bx bx-image text-success"></i> ${images.length} image(s)`).show();
        } else {
            $counter.hide();
        }
    },

    /**
     * Open image manager modal
     */
    openModal(equipmentId, equipmentIndex, equipmentName) {
        $('#modal_equipment_id').val(equipmentId);
        $('#modal_equipment_index').val(equipmentIndex);
        $('#equipmentImageUploadModalLabel').text(`Manage Images for ${equipmentName}`);
        
        const key = `${equipmentId}_${equipmentIndex}`;
        this.updateAttachedImagesDisplay(key);
        
        $('#equipmentImageUploadModal').modal('show');
    },

    /**
     * Reset modal state
     */
    resetModal() {
        $('#equipment_image_file').val('');
        $('#modal_equipment_id').val('');
        $('#modal_equipment_index').val('');
        $('#attached_images_grid').empty();
        $('#no_images_message').show();
        this.updateFilePreview([]);
    },

    /**
     * Get all images for form submission
     */
    getAllImagesForSubmission() {
        const formData = new FormData();
        let imageCount = 0;

        Object.keys(window.equipmentImages).forEach(key => {
            const images = window.equipmentImages[key];
            images.forEach((image, index) => {
                formData.append(`equipment_images[${key}][${index}]`, image.file);
                imageCount++;
            });
        });

        return { formData, imageCount };
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
        $('#checkin_date').val(Utils.getCurrentDate());
        $('#checkin_time').val(Utils.getCurrentTime());
        $('#checkout_date').val(Utils.getCurrentDate());
        $('#checkout_time').val(Utils.getCurrentTime());
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
        Utils.toggleButton($btn, true, 'Searching...');

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
            Utils.toggleButton($btn, false);
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
        
        // Clear equipment images
        window.equipmentImages = {};
        
        // Reset form fields
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
            console.log(item);
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
                            <div class="col-md-3 damage-image-group" style="display:none;">
                                <label class="form-label">Images</label>
                                <div>
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm equipment-manage-images-btn" 
                                            data-equipment-id="${item.equipment_id}" 
                                            data-equipment-index="${index}" 
                                            data-equipment-name="${item.equipment.name || item.equipment.name}">
                                        <i class="bx bx-image"></i> Manage Images
                                    </button>
                                    <div class="mt-1">
                                        <small class="text-success" 
                                               id="equipment_images_counter_${item.equipment_id}_${index}" 
                                               style="display:none;"></small>
                                    </div>
                                </div>
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
            const $imageGroup = $item.find('.damage-image-group');

            if (condition === 'damaged' || condition === 'missing') {
                $costGroup.show();
                $notesGroup.show();
                $imageGroup.show();
                $item.addClass(condition === 'damaged' ? 'border-warning' : 'border-danger');
                $item.find('.equipment-cost').prop('required', true);
                $item.find('.equipment-notes').prop('required', true);
            } else {
                $costGroup.hide();
                $notesGroup.hide();
                $imageGroup.hide();
                $item.removeClass('border-warning border-danger');
                $item.find('.equipment-cost').prop('required', false).val('');
                $item.find('.equipment-notes').prop('required', false).val('');
                
                // Clear images for this equipment
                const equipmentId = $item.data('equipment-id');
                const equipmentIndex = $item.find('.equipment-manage-images-btn').data('equipment-index');
                const key = `${equipmentId}_${equipmentIndex}`;
                
                if (window.equipmentImages[key]) {
                    delete window.equipmentImages[key];
                }
                ImageManager.updateEquipmentImageCounter(equipmentId, equipmentIndex);
            }
        });

        // Image management button events
        $list.find('.equipment-manage-images-btn').on('click', function() {
            const equipmentId = $(this).data('equipment-id');
            const equipmentIndex = $(this).data('equipment-index');
            const equipmentName = $(this).data('equipment-name');
            
            ImageManager.openModal(equipmentId, equipmentIndex, equipmentName);
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
        Utils.toggleButton($btn, true, 'Processing...');

        try {
            const response = await ApiService.processCheckin(formData);
            
            if (response.success) {
                this.handleSuccess(response);
            } else {
                Utils.showError(response.message || CONFIG.messages.error.checkinFailed);
            }
        } catch (error) {
            const message = error.responseJSON?.message || CONFIG.messages.error.checkinFailed;
            Utils.showError(message);
        } finally {
            Utils.toggleButton($btn, false);
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
        Utils.toggleButton($btn, true, 'Processing...');

        try {
            const response = await ApiService.processCheckout(formData);
            
            if (response.success) {
                this.handleSuccess(response);
            } else {
                Utils.showError(response.message || CONFIG.messages.error.checkoutFailed);
            }
        } catch (error) {
            const message = error.responseJSON?.message || CONFIG.messages.error.checkoutFailed;
            Utils.showError(message);
        } finally {
            Utils.toggleButton($btn, false);
        }
    },

    /**
     * Get form data for submission
     */
    getFormData() {
        const fd = new FormData($('#checkoutReservationForm')[0]);
        
        // Add equipment images
        const { formData: imageFormData } = ImageManager.getAllImagesForSubmission();
        
        // Append image files to main form data
        for (let [key, value] of imageFormData.entries()) {
            fd.append(key, value);
        }
        
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
            const totalCost = response.damages.reduce((sum, damage) => 
                sum + parseFloat(damage.estimated_cost || 0), 0);
            message += `<br><br><strong>Damage Charges:</strong> ${Utils.formatCurrency(totalCost)}`;
            
            const damageList = response.damages.map(damage => 
                `• ${damage.equipment_name}: ${Utils.formatCurrency(damage.estimated_cost)}`
            ).join('<br>');
            message += `<br><br><strong>Details:</strong><br>${damageList}`;
        }
        
        Swal.fire({
            title: 'Check-out Complete',
            html: message,
            icon: 'success',
            confirmButtonText: 'OK'
        });
        
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
        
        // Clear equipment images
        window.equipmentImages = {};
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
        console.log('Initializing Check-in/Check-out System...');
        
        // Initialize all managers
        ReservationManager.init();
        ImageManager.init();
        CheckinProcessor.init();
        CheckoutProcessor.init();
        
        // Initialize global storage
        window.equipmentImages = {};
        
        // Set up global error handling
        this.setupErrorHandling();
        
        console.log('Check-in/Check-out System initialized successfully');
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

// Make ImageManager globally accessible for onclick handlers
window.ImageManager = ImageManager;
</script>
@endpush