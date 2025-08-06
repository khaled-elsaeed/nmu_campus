@extends('layouts.home')

@section('title', 'Add Reservation | Housing')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <x-ui.page-header 
        title="Add Reservation"
        description="Create a new reservation."
        icon="bx bx-calendar"
    >
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back"></i> Back to List
        </a>
    </x-ui.page-header>
    
    <div class="row justify-content-center">
        <!-- Step 1: National ID Search -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-id-card me-2"></i>Enter National ID</h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="search_national_id" placeholder="Enter National ID...">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" id="btnSearchNationalId">
                            <i class="bx bx-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: User Info (hidden until found) -->
        <div id="user-info-section" class="card mb-4 shadow-sm border-0 d-none">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h5 class="mb-0"><i class="bx bx-user me-2"></i>User Info</h5>
            </div>
            <div class="card-body pt-3">
                <div id="user-info-content">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>

        <!-- Step 3: Reservation Form (hidden until user found) -->
        <form id="addReservationForm" method="POST" action="{{ route('reservations.store') }}" class="d-none">
            @csrf
            <input type="hidden" id="add_user_id" name="user_id">
            
            <!-- Accommodation Details -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-home me-2"></i>Accommodation Details</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="add_accommodation_type" class="form-label">Accommodation Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="add_accommodation_type" name="accommodation_type" required>
                                <option value="">Select Type</option>
                                <option value="room">Room</option>
                                <option value="apartment">Apartment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="add_building_id" class="form-label">Building <span class="text-danger">*</span></label>
                            <select class="form-control" id="add_building_id" name="building_id" required>
                                <option value="">Select Building</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="apartment-select-group" style="display:none;">
                            <label for="add_apartment_id" class="form-label">Apartment <span class="text-danger">*</span></label>
                            <select class="form-control" id="add_apartment_id" name="apartment_id">
                                <option value="">Select Apartment</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="room-select-group" style="display:none;">
                            <label for="add_room_id" class="form-label">Room <span class="text-danger">*</span></label>
                            <select class="form-control" id="add_room_id" name="room_id">
                                <option value="">Select Room</option>
                            </select>
                            <div id="double-room-bed-options" class="mt-2" style="display:none;">
                                <label class="form-label">Double Room Option</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="double_room_bed_option" id="double_room_one_bed" value="one">
                                        <label class="form-check-label" for="double_room_one_bed">Take one bed</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="double_room_bed_option" id="double_room_both_beds" value="both">
                                        <label class="form-check-label" for="double_room_both_beds">Take both beds</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <!-- Period Selector -->
                        <div class="col-md-4">
                            <label for="add_period" class="form-label">Period <span class="text-danger">*</span></label>
                            <select class="form-control" id="add_period" name="period_type" required>
                                <option value="">Select Period</option>
                                <option value="academic">Academic</option>
                                <option value="calendar">Calendar</option>
                            </select>
                        </div>
                        <!-- Academic Term (Academic Period) -->
                        <div class="col-md-4" id="academic-term-group" style="display:none;">
                            <label for="add_academic_term_id" class="form-label">Academic Term</label>
                            <select class="form-control" id="add_academic_term_id" name="academic_term_id">
                                <option value="">Select Academic Term</option>
                            </select>
                        </div>
                        <!-- Check-in/Check-out (Calendar Period) -->
                        <div class="col-md-8" id="checkinout-group" style="display:none;">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="add_check_in_date" class="form-label">Check-in Date</label>
                                    <input type="date" class="form-control" id="add_check_in_date" name="check_in_date">
                                </div>
                                <div class="col-md-6">
                                    <label for="add_check_out_date" class="form-label">Check-out Date</label>
                                    <input type="date" class="form-control" id="add_check_out_date" name="check_out_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="add_status" class="form-label">Status</label>
                            <select class="form-control" id="add_status" name="status">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-cube me-2"></i>Equipment</h5>
                </div>
                <div class="card-body pt-3">
                    <div id="equipment-list" class="row g-3"></div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Additional Info</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="add_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="add_notes" name="notes" rows="3" placeholder="Enter any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-lg btn-primary px-4 shadow">
                    <i class="bx bx-save"></i> Save Reservation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 

@push('scripts')
<script>
/**
 * Add Reservation Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - UserSearchManager: Handles user search functionality
 * - AccommodationManager: Handles building/apartment/room selection
 * - PeriodManager: Handles academic/calendar period management
 * - EquipmentManager: Handles equipment selection
 * - ReservationProcessor: Handles form submission and validation
 * - ReservationApp: Initializes all managers
 * 
 * NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    reservations: {
        store: '{{ route("reservations.store") }}'
    },
    buildings: {
        all: '{{ route("housing.buildings.all") }}'
    },
    apartments: {
        show: '{{ route("housing.apartments.all", ":id") }}'
    },
    rooms: {
        show: '{{ route("housing.rooms.all", ":id") }}'
    },
    users: {
        findByNationalId: '{{ route("users.findByNationalId") }}'
    },
    academicTerms: {
        all: '{{ route("academic.academic_terms.all") }}'
    },
    equipment: {
        all: '{{ route("equipment.all") }}'
    }
};

// ===========================
// MESSAGES CONSTANTS
// ===========================
var MESSAGES = {
    success: {
        reservationCreated: 'Reservation has been created successfully.'
    },
    error: {
        enterNationalId: 'Please enter a National ID.',
        userNotFound: 'User not found.',
        fetchUserFailed: 'Failed to fetch user.',
        selectUser: 'Please select a user.',
        selectAccommodationType: 'Please select accommodation type.',
        selectAccommodation: 'Please select a room/apartment.',
        invalidCheckOutDate: 'Check-out date must be after check-in date.',
        reservationCreateFailed: 'Failed to create reservation.',
        buildingsLoadFailed: 'Failed to load buildings.',
        apartmentsLoadFailed: 'Failed to load apartments.',
        roomsLoadFailed: 'Failed to load rooms.',
        apartmentRoomsLoadFailed: 'Failed to load rooms for this apartment.',
        academicTermsLoadFailed: 'Failed to load academic terms.',
        equipmentLoadFailed: 'Failed to load equipment.'
    },
    validation: {
        required: 'This field is required.',
        selectPeriod: 'Please select a period.',
        selectAcademicTerm: 'Please select an academic term.',
        selectDates: 'Please select check-in and check-out dates.'
    }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
    /**
     * Generic AJAX request wrapper
     * @param {object} options
     * @returns {jqXHR}
     */
    request: function(options) {
        var requestOptions = {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };
        
        // Merge options
        for (var key in options) {
            if (options.hasOwnProperty(key)) {
                requestOptions[key] = options[key];
            }
        }
        
        return $.ajax(requestOptions);
    },

    /**
     * Find user by national ID
     * @param {string} nationalId
     * @returns {jqXHR}
     */
    findUserByNationalId: function(nationalId) {
        return this.request({ 
            url: ROUTES.users.findByNationalId, 
            method: 'GET', 
            data: { national_id: nationalId } 
        });
    },

    /**
     * Fetch all buildings
     * @returns {jqXHR}
     */
    fetchBuildings: function() {
        return this.request({ 
            url: ROUTES.buildings.all, 
            method: 'GET' 
        });
    },

    /**
     * Fetch apartments by building
     * @param {string|number} buildingId
     * @returns {jqXHR}
     */
    fetchApartments: function(buildingId) {
        var url = Utils.replaceRouteId(ROUTES.apartments.show, buildingId);
        return this.request({ url: url, method: 'GET' });
    },

    /**
     * Fetch rooms by apartment
     * @param {string|number} apartmentId
     * @returns {jqXHR}
     */
    fetchRooms: function(apartmentId) {
        var url = Utils.replaceRouteId(ROUTES.rooms.show, apartmentId);
        return this.request({ url: url, method: 'GET' });
    },

    /**
     * Fetch academic terms
     * @returns {jqXHR}
     */
    fetchAcademicTerms: function() {
        return this.request({ 
            url: ROUTES.academicTerms.all, 
            method: 'GET' 
        });
    },

    /**
     * Fetch equipment
     * @returns {jqXHR}
     */
    fetchEquipment: function() {
        return this.request({ 
            url: ROUTES.equipment.all, 
            method: 'GET' 
        });
    },

    /**
     * Create reservation
     * @param {object} data
     * @returns {jqXHR}
     */
    createReservation: function(data) {
        return this.request({ 
            url: ROUTES.reservations.store, 
            method: 'POST', 
            data: data,
            contentType: 'application/json',
            processData: false
        });
    }
};

// ===========================
// USER SEARCH MANAGER
// ===========================
var UserSearchManager = {
    /**
     * Initialize user search manager
     */
    init: function() {
        this.bindEvents();
    },

    /**
     * Bind user search events
     */
    bindEvents: function() {
        var self = this;
        $('#btnSearchNationalId').on('click', function() {
            self.handleSearch();
        });
        $('#search_national_id').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                self.handleSearch();
            }
        });
    },

    /**
     * Handle user search
     */
    handleSearch: function() {
        var self = this;
        var nationalId = $('#search_national_id').val().trim();
        
        if (!nationalId) {
            Utils.showError(MESSAGES.error.enterNationalId);
            return;
        }

        var $btn = $('#btnSearchNationalId');
        Utils.setLoadingState($btn, true, { loadingText: 'Searching...' });

        ApiService.findUserByNationalId(nationalId)
            .done(function(response) {
                if (response.success && response.data) {
                    self.handleUserFound(response.data);
                } else {
                    self.handleUserNotFound(response.message);
                }
            })
            .fail(function() {
                self.handleUserNotFound();
            })
            .always(function() {
                Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-search"></i> Search' });
            });
    },

    /**
     * Handle successful user found
     */
    handleUserFound: function(user) {
        this.displayUserInfo(user);
        $('#add_user_id').val(user.id);
        $('#user-info-section').removeClass('d-none').show();
        $('#addReservationForm').removeClass('d-none').show();
    },

    /**
     * Handle user not found
     */
    handleUserNotFound: function(message) {
        $('#user-info-section').hide();
        $('#addReservationForm').hide();
        Utils.showError(message || MESSAGES.error.userNotFound);
    },

    /**
     * Display user information
     */
    displayUserInfo: function(user) {
        var html = '<div class="row g-3">' +
            '<div class="col-md-6">' +
                '<strong>Name:</strong> ' + (user.name_en || user.name_ar || '-') +
            '</div>' +
            '<div class="col-md-6">' +
                '<strong>Email:</strong> ' + (user.email || '-') +
            '</div>' +
            '<div class="col-md-6">' +
                '<strong>User Type:</strong> ' + (user.user_type || '-') +
            '</div>' +
            '<div class="col-md-6">' +
                '<strong>National ID:</strong> ' + (user.national_id || '-') +
            '</div>' +
        '</div>';
        $('#user-info-content').html(html);
    }
};

// ===========================
// ACCOMMODATION MANAGER
// ===========================
var AccommodationManager = {
    /**
     * Initialize accommodation manager
     */
    init: function() {
        this.bindEvents();
        this.loadInitialData();
    },

    /**
     * Bind accommodation events
     */
    bindEvents: function() {
        var self = this;
        $('#add_accommodation_type').on('change', function() {
            self.handleAccommodationTypeChange();
        });
        $('#add_building_id').on('change', function() {
            self.handleBuildingChange();
        });
        $('#add_apartment_id').on('change', function() {
            self.handleApartmentChange();
        });
        $('#add_room_id').on('change', function() {
            self.handleRoomChange();
        });
    },

    /**
     * Load initial data
     */
    loadInitialData: function() {
        this.loadBuildings();
    },

    /**
     * Handle accommodation type change
     */
    handleAccommodationTypeChange: function() {
        var type = $('#add_accommodation_type').val();
        
        if (type) {
            this.showAccommodationFields(type);
        } else {
            this.hideAccommodationFields();
        }
        
        this.clearAccommodationSelects();
    },

    /**
     * Handle building change
     */
    handleBuildingChange: function() {
        var self = this;
        var buildingId = $('#add_building_id').val();
        var accommodationType = $('#add_accommodation_type').val();
        
        if (buildingId && accommodationType) {
            self.loadApartments(buildingId);
        } else {
            this.clearAccommodationSelects();
        }
    },

    /**
     * Handle apartment change
     */
    handleApartmentChange: function() {
        var self = this;
        var apartmentId = $('#add_apartment_id').val();
        var accommodationType = $('#add_accommodation_type').val();
        
        if (accommodationType === 'room' && apartmentId) {
            self.loadRooms(apartmentId);
        }
    },

    /**
     * Handle room change
     */
    handleRoomChange: function() {
        var $roomSelect = $('#add_room_id');
        var roomId = $roomSelect.val();
        
        if (!roomId) {
            $('#double-room-bed-options').hide();
            return;
        }
        
        var selectedOption = $roomSelect.find('option:selected');
        var roomType = selectedOption.data('type');
        
        if (roomType === 'double') {
            $('#double-room-bed-options').show();
        } else {
            $('#double-room-bed-options').hide();
        }
    },

    /**
     * Show accommodation fields based on type
     */
    showAccommodationFields: function(type) {
        if (type === 'room') {
            $('#apartment-select-group').show();
            $('#room-select-group').show();
            $('#add_apartment_id').prop('required', true);
            $('#add_room_id').prop('required', true);
        } else if (type === 'apartment') {
            $('#apartment-select-group').show();
            $('#room-select-group').hide();
            $('#add_apartment_id').prop('required', true);
            $('#add_room_id').prop('required', false);
        }
    },

    /**
     * Hide accommodation fields
     */
    hideAccommodationFields: function() {
        $('#apartment-select-group').hide();
        $('#room-select-group').hide();
        $('#add_apartment_id').prop('required', false);
        $('#add_room_id').prop('required', false);
        $('#double-room-bed-options').hide();
    },

    /**
     * Load buildings
     */
    loadBuildings: function() {
        var self = this;
        ApiService.fetchBuildings()
            .done(function(response) {
                if (response.success && response.data) {
                    self.populateSelect($('#add_building_id'), response.data, 'number', 'Select Building');
                }
            })
            .fail(function() {
                Utils.showError(MESSAGES.error.buildingsLoadFailed);
            });
    },

    /**
     * Load apartments
     */
    loadApartments: function(buildingId) {
        var self = this;
        ApiService.fetchApartments(buildingId)
            .done(function(response) {
                if (response.success && response.data) {
                    self.populateSelect($('#add_apartment_id'), response.data, 'number', 'Select Apartment');
                }
            })
            .fail(function(error) {
                console.log(error);
                Utils.showError(MESSAGES.error.apartmentsLoadFailed);
            });
    },

    /**
     * Load rooms
     */
    loadRooms: function(apartmentId) {
        var self = this;
        ApiService.fetchRooms(apartmentId)
            .done(function(response) {
                if (response.success && response.data) {
                    self.populateSelect($('#add_room_id'), response.data, 'number', 'Select Room', true);
                    $('#add_room_id').trigger('change');
                }
            })
            .fail(function() {
                Utils.showError(MESSAGES.error.apartmentRoomsLoadFailed);
            });
    },

    /**
     * Populate select dropdown
     */
    populateSelect: function($select, data, textField, placeholder, includeType) {
        $select.empty().append('<option value="">' + placeholder + '</option>');
        
        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            if (includeType && item.type) {
                $select.append('<option value="' + item.id + '" data-type="' + item.type + '">' + item[textField] + '</option>');
            } else {
                $select.append('<option value="' + item.id + '">' + item[textField] + '</option>');
            }
        }
    },

    /**
     * Clear accommodation selects
     */
    clearAccommodationSelects: function() {
        $('#add_apartment_id').empty().append('<option value="">Select Apartment</option>');
        $('#add_room_id').empty().append('<option value="">Select Room</option>');
        $('#double-room-bed-options').hide();
    },

    /**
     * Reset accommodation form
     */
    resetForm: function() {
        $('#add_building_id').empty().append('<option value="">Select Building</option>');
        this.clearAccommodationSelects();
        this.hideAccommodationFields();
    }
};

// ===========================
// PERIOD MANAGER
// ===========================
var PeriodManager = {
    /**
     * Initialize period manager
     */
    init: function() {
        this.bindEvents();
        this.loadAcademicTerms();
        this.handlePeriodChange(); // Set initial state
    },

    /**
     * Bind period events
     */
    bindEvents: function() {
        var self = this;
        $('#add_period').on('change', function() {
            self.handlePeriodChange();
        });
    },

    /**
     * Handle period type change
     */
    handlePeriodChange: function() {
        var period = $('#add_period').val();
        
        if (period === 'academic') {
            $('#academic-term-group').show();
            $('#checkinout-group').hide();
            $('#add_academic_term_id').prop('required', true);
            $('#add_check_in_date').prop('required', false);
            $('#add_check_out_date').prop('required', false);
        } else if (period === 'calendar') {
            $('#academic-term-group').hide();
            $('#checkinout-group').show();
            $('#add_academic_term_id').prop('required', false);
            $('#add_check_in_date').prop('required', true);
            $('#add_check_out_date').prop('required', true);
        } else {
            $('#academic-term-group').hide();
            $('#checkinout-group').hide();
            $('#add_academic_term_id').prop('required', false);
            $('#add_check_in_date').prop('required', false);
            $('#add_check_out_date').prop('required', false);
        }
    },

    /**
     * Load academic terms
     */
    loadAcademicTerms: function() {
        ApiService.fetchAcademicTerms()
            .done(function(response) {
                if (response.success && response.data) {
                    AccommodationManager.populateSelect($('#add_academic_term_id'), response.data, 'name', 'Select Academic Term');
                }
            })
            .fail(function() {
                Utils.showError(MESSAGES.error.academicTermsLoadFailed);
            });
    }
};

// ===========================
// EQUIPMENT MANAGER
// ===========================
var EquipmentManager = {
    /**
     * Initialize equipment manager
     */
    init: function() {
        this.loadEquipment();
    },

    /**
     * Load equipment
     */
    loadEquipment: function() {
        var self = this;
        ApiService.fetchEquipment()
            .done(function(response) {
                if (response.success && response.data) {
                    self.renderEquipmentList(response.data);
                }
            })
            .fail(function() {
                Utils.showError(MESSAGES.error.equipmentLoadFailed);
            });
    },

    /**
     * Render equipment list
     */
    renderEquipmentList: function(equipmentData) {
        var $list = $('#equipment-list');
        $list.empty();
        
        if (!equipmentData.length) {
            $list.append('<div class="col-12 text-muted">No equipment available.</div>');
            return;
        }
        
        for (var i = 0; i < equipmentData.length; i++) {
            var item = equipmentData[i];
            var html = '<div class="col-md-6 mb-2">' +
                '<div class="form-check d-flex align-items-center">' +
                    '<input class="form-check-input equipment-checkbox" ' +
                           'type="checkbox" ' +
                           'value="' + item.id + '" ' +
                           'id="equipment_' + item.id + '" ' +
                           'data-name="' + item.name_en + '">' +
                    '<label class="form-check-label ms-2" for="equipment_' + item.id + '">' +
                        item.name_en +
                    '</label>' +
                    '<input type="number" ' +
                           'min="1" ' +
                           'class="form-control ms-3 equipment-qty" ' +
                           'id="equipment_qty_' + item.id + '" ' +
                           'name="equipment_qty_' + item.id + '" ' +
                           'value="1" ' +
                           'style="width:80px;" ' +
                           'disabled>' +
                '</div>' +
            '</div>';
            $list.append(html);
        }
        
        this.bindEquipmentEvents($list);
    },

    /**
     * Bind equipment events
     */
    bindEquipmentEvents: function($list) {
        $list.find('.equipment-checkbox').on('change', function() {
            var id = $(this).val();
            $('#equipment_qty_' + id).prop('disabled', !this.checked);
        });
    }
};

// ===========================
// RESERVATION PROCESSOR
// ===========================
var ReservationProcessor = {
    /**
     * Initialize reservation processor
     */
    init: function() {
        this.bindEvents();
    },

    /**
     * Bind reservation events
     */
    bindEvents: function() {
        var self = this;
        $('#addReservationForm').on('submit', function(e) {
            e.preventDefault();
            self.handleSubmit();
        });
    },

    /**
     * Handle form submission
     */
    handleSubmit: function() {
        var self = this;
        var formData = this.getFormData();
        
        if (!this.validateForm(formData)) {
            return;
        }
        
        var $btn = $('#addReservationForm button[type="submit"]');
        Utils.setLoadingState($btn, true, { loadingText: 'Saving...' });
        
        ApiService.createReservation(JSON.stringify(formData))
            .done(function(response) {
                if (response.success) {
                    self.handleSuccess(response);
                } else {
                    Utils.showError(response.message || MESSAGES.error.reservationCreateFailed);
                }
            })
            .fail(function(xhr) {
                if (xhr && xhr.responseJSON) {
                    Utils.handleAjaxError(xhr, MESSAGES.error.reservationCreateFailed);
                }
            })
            .always(function() {
                Utils.setLoadingState($btn, false, { normalText: '<i class="bx bx-save"></i> Save Reservation' });
            });
    },

    /**
     * Get form data for submission
     */
    getFormData: function() {
        var formData = {};
        
        // Serialize form fields
        var formArray = $('#addReservationForm').serializeArray();
        for (var i = 0; i < formArray.length; i++) {
            var item = formArray[i];
            if (item.value) {
                formData[item.name] = item.value;
            }
        }
        
        // Handle accommodation_id based on type
        var accommodationType = $('#add_accommodation_type').val();
        if (accommodationType === 'room') {
            formData.accommodation_id = $('#add_room_id').val();
        } else if (accommodationType === 'apartment') {
            formData.accommodation_id = $('#add_apartment_id').val();
        }
        
        // Collect equipment
        var equipment = [];
        $('#equipment-list').find('.equipment-checkbox:checked').each(function() {
            var id = $(this).val();
            var qty = $('#equipment_qty_' + id).val();
            equipment.push({ 
                equipment_id: id, 
                quantity: parseInt(qty) || 1 
            });
        });
        
        if (equipment.length) {
            formData.equipment = equipment;
        }
        
        // Add double_room_bed_option if present
        var bedOption = $('input[name="double_room_bed_option"]:checked').val();
        if (bedOption) {
            formData.double_room_bed_option = bedOption;
        }
        
        return formData;
    },

    /**
     * Validate form data
     */
    validateForm: function(formData) {
        if (!formData.user_id) {
            Utils.showError(MESSAGES.error.selectUser);
            return false;
        }
        
        if (!formData.period_type) {
            Utils.showError(MESSAGES.validation.selectPeriod);
            return false;
        }
        
        if (formData.period_type === 'academic' && !formData.academic_term_id) {
            Utils.showError(MESSAGES.validation.selectAcademicTerm);
            return false;
        }
        
        if (formData.period_type === 'calendar') {
            if (!formData.check_in_date || !formData.check_out_date) {
                Utils.showError(MESSAGES.validation.selectDates);
                return false;
            }
            if (new Date(formData.check_out_date) <= new Date(formData.check_in_date)) {
                Utils.showError(MESSAGES.error.invalidCheckOutDate);
                return false;
            }
        }
        
        return true;
    },

    /**
     * Handle successful reservation creation
     */
    handleSuccess: function(response) {
        Utils.showSuccess(MESSAGES.success.reservationCreated);
        this.resetForm();
    },

    /**
     * Reset form after successful submission
     */
    resetForm: function() {
        $('#addReservationForm')[0].reset();
        $('#add_user_id').val('');
        
        // Hide form sections
        $('#user-info-section').addClass('d-none').hide();
        $('#addReservationForm').addClass('d-none').hide();
        
        // Reset form state
        AccommodationManager.resetForm();
        $('#search_national_id').val('');
        
        // Reset period state
        PeriodManager.handlePeriodChange();
    }
};

// ===========================
// MAIN APPLICATION
// ===========================
var ReservationApp = {
    /**
     * Initialize the entire application
     */
    init: function() {
        // Use global Utils for logging if desired
        if (window.console) console.log('Initializing Add Reservation System...');
        
        // Initialize all managers
        UserSearchManager.init();
        AccommodationManager.init();
        PeriodManager.init();
        EquipmentManager.init();
        ReservationProcessor.init();
        
        // Set up global error handling
        this.setupErrorHandling();
        
        if (window.console) console.log('Add Reservation System initialized successfully');
    },

    /**
     * Setup global error handling
     */
    setupErrorHandling: function() {
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
$(document).ready(function() {
    ReservationApp.init();
});
</script>
@endpush