@extends('layouts.home')

@section('title', 'Add Reservation | NMU Campus')

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
                        <div class="row g-3 mt-2">
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
                        <div class="row g-3">
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
                            <div class="col-md-4" id="academic-term-group">
                                <label for="add_academic_term_id" class="form-label">Academic Term</label>
                                <select class="form-control" id="add_academic_term_id" name="academic_term_id">
                                    <option value="">Select Academic Term</option>
                                </select>
                            </div>
                            <!-- Check-in/Check-out (Calendar Period) -->
                            <div class="col-md-4" id="checkinout-group">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="add_check_in_date" class="form-label">Check-in Date</label>
                                        <input type="date" class="form-control" id="add_check_in_date" name="check_in_date">
                                    </div>
                                    <div class="col-12">
                                        <label for="add_check_out_date" class="form-label">Check-out Date</label>
                                        <input type="date" class="form-control" id="add_check_out_date" name="check_out_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
 * Reservation Add Page JS (Cleaned)
 * - Only add form, dropdowns, validation, and submission logic
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
const ROUTES = {
  reservations: {
    store: '{{ route("reservations.store") }}'
  },
  buildings: {
    all: '{{ route("housing.buildings.all") }}'
  },
  apartments: {
    all: '{{ route("housing.apartments.all") }}'
  },
  rooms: {
    all: '{{ route("housing.rooms.all") }}'
  },
  users: {
    all: '{{ route("users.all") }}',
    findByNationalId: '{{ route("users.findByNationalId") }}'
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
  },
  equipment: {
    all: '{{ route("equipment.all") }}'
  }
};

const SELECTORS = {
  // Search elements
  searchNationalId: '#search_national_id',
  btnSearchNationalId: '#btnSearchNationalId',
  userInfoSection: '#user-info-section',
  userInfoContent: '#user-info-content',
  
  // Form elements
  addForm: '#addReservationForm',
  userId: '#add_user_id',
  accommodationType: '#add_accommodation_type',
  buildingId: '#add_building_id',
  apartmentId: '#add_apartment_id',
  roomId: '#add_room_id',
  academicTermId: '#add_academic_term_id',
  status: '#add_status',
  checkInDate: '#add_check_in_date',
  checkOutDate: '#add_check_out_date',
  notes: '#add_notes',
  equipmentList: '#equipment-list',
  period: '#add_period',
  academicTermGroup: '#academic-term-group',
  checkinoutGroup: '#checkinout-group',
  
  // Groups
  apartmentSelectGroup: '#apartment-select-group',
  roomSelectGroup: '#room-select-group',
  
  // Submit button
  submitBtn: '#addReservationForm button[type="submit"]'
};

const MESSAGES = {
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
    usersLoadFailed: 'Failed to load users.',
    academicTermsLoadFailed: 'Failed to load academic terms.',
    equipmentLoadFailed: 'Failed to load equipment.'
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
      timer: 2500, 
      timerProgressBar: true 
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

  clearSelect: ($select, placeholder = 'Select...') => {
    $select.empty().append(`<option value="">${placeholder}</option>`);
  }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
  request: (options) => {
    return $.ajax(options);
  },

  findUserByNationalId: (nationalId) => {
    return ApiService.request({ 
      url: ROUTES.users.findByNationalId, 
      method: 'GET', 
      data: { national_id: nationalId } 
    });
  },

  fetchBuildings: () => {
    return ApiService.request({ 
      url: ROUTES.buildings.all, 
      method: 'GET' 
    });
  },

  fetchApartments: (buildingId) => {
    const data = buildingId ? { building_id: buildingId } : {};
    return ApiService.request({ 
      url: ROUTES.apartments.all, 
      method: 'GET', 
      data 
    });
  },

  fetchRooms: (buildingId, apartmentId) => {
    const data = {};
    if (buildingId) data.building_id = buildingId;
    if (apartmentId) data.apartment_id = apartmentId;
    return ApiService.request({ 
      url: ROUTES.rooms.all, 
      method: 'GET', 
      data 
    });
  },

  fetchAcademicTerms: () => {
    return ApiService.request({ 
      url: ROUTES.academicTerms.all, 
      method: 'GET' 
    });
  },

  fetchEquipment: () => {
    return ApiService.request({ 
      url: ROUTES.equipment.all, 
      method: 'GET' 
    });
  },

  createReservation: (data) => {
    return ApiService.request({ 
      url: ROUTES.reservations.store, 
      method: 'POST', 
      data 
    });
  }
};

// ===========================
// USER SEARCH MANAGER
// ===========================
const UserSearchManager = {
  init: () => {
    UserSearchManager.bindEvents();
  },

  bindEvents: () => {
    $(SELECTORS.btnSearchNationalId).on('click', UserSearchManager.handleSearch);
    $(SELECTORS.searchNationalId).on('keypress', (e) => {
      if (e.which === 13) { // Enter key
        UserSearchManager.handleSearch();
      }
    });
  },

  handleSearch: () => {
    const nationalId = $(SELECTORS.searchNationalId).val().trim();
    
    if (!nationalId) {
      Utils.showError(MESSAGES.error.enterNationalId);
      return;
    }

    const $btn = $(SELECTORS.btnSearchNationalId);
    Utils.disableButton($btn);

    ApiService.findUserByNationalId(nationalId)
      .done((response) => {
        if (response.success && response.data) {
          UserSearchManager.handleUserFound(response.data);
        } else {
          UserSearchManager.handleUserNotFound(response.message);
        }
      })
      .fail(() => {
        UserSearchManager.handleUserNotFound();
      })
      .always(() => {
        Utils.disableButton($btn, false);
      });
  },

  handleUserFound: (user) => {
    UserSearchManager.displayUserInfo(user);
    $(SELECTORS.userId).val(user.id);
    Utils.showElement($(SELECTORS.userInfoSection));
    Utils.showElement($(SELECTORS.addForm));
  },

  handleUserNotFound: (message = null) => {
    Utils.hideElement($(SELECTORS.userInfoSection));
    Utils.hideElement($(SELECTORS.addForm));
    Utils.showError(message || MESSAGES.error.userNotFound);
  },

  displayUserInfo: (user) => {
    const html = `
      <div class="row g-3">
        <div class="col-md-6"><strong>Name:</strong> ${user.name_en || user.name_ar || '-'}</div>
        <div class="col-md-6"><strong>Email:</strong> ${user.email || '-'}</div>
        <div class="col-md-6"><strong>User Type:</strong> ${user.user_type || '-'}</div>
        <div class="col-md-6"><strong>National ID:</strong> ${user.national_id || '-'}</div>
      </div>
    `;
    $(SELECTORS.userInfoContent).html(html);
  }
};

// ===========================
// SELECT MANAGER
// ===========================
const SelectManager = {
  init: () => {
    SelectManager.loadAcademicTerms();
    SelectManager.loadEquipment();
    SelectManager.bindEvents();
  },

  bindEvents: () => {
    $(SELECTORS.accommodationType).on('change', SelectManager.handleAccommodationTypeChange);
    $(SELECTORS.buildingId).on('change', SelectManager.handleBuildingChange);
    $(SELECTORS.apartmentId).on('change', SelectManager.handleApartmentChange);
    $(SELECTORS.roomId).on('change', SelectManager.handleRoomChange); // Add this line
  },

  handleAccommodationTypeChange: () => {
    const type = $(SELECTORS.accommodationType).val();
    
    if (type) {
      SelectManager.loadBuildings();
      SelectManager.showAccommodationFields(type);
    } else {
      SelectManager.hideAccommodationFields();
    }
    
    SelectManager.clearAccommodationSelects();
  },

  handleBuildingChange: () => {
    const buildingId = $(SELECTORS.buildingId).val();
    const accommodationType = $(SELECTORS.accommodationType).val();
    
    if (buildingId && accommodationType) {
      if (accommodationType === 'apartment') {
        SelectManager.loadApartments(buildingId);
      } else if (accommodationType === 'room') {
        SelectManager.loadApartments(buildingId);
      }
    } else {
      SelectManager.clearAccommodationSelects();
    }
  },

  handleApartmentChange: () => {
    const apartmentId = $(SELECTORS.apartmentId).val();
    const accommodationType = $(SELECTORS.accommodationType).val();
    
    if (accommodationType === 'room' && apartmentId) {
      SelectManager.loadRoomsForApartment(apartmentId);
    }
  },

  handleRoomChange: () => {
    const $roomSelect = $(SELECTORS.roomId);
    const roomId = $roomSelect.val();
    if (!roomId) {
      $('#double-room-bed-options').hide();
      return;
    }
    const selectedOption = $roomSelect.find('option:selected');
    const roomType = selectedOption.data('type');
    if (roomType === 'double') {
      $('#double-room-bed-options').show();
    } else {
      $('#double-room-bed-options').hide();
    }
  },

  showAccommodationFields: (type) => {
    if (type === 'room') {
      Utils.showElement($(SELECTORS.apartmentSelectGroup));
      Utils.showElement($(SELECTORS.roomSelectGroup));
      $(SELECTORS.apartmentId).prop('required', true);
      $(SELECTORS.roomId).prop('required', true);
    } else if (type === 'apartment') {
      Utils.showElement($(SELECTORS.apartmentSelectGroup));
      Utils.hideElement($(SELECTORS.roomSelectGroup));
      $(SELECTORS.apartmentId).prop('required', true);
      $(SELECTORS.roomId).prop('required', false);
    }
  },

  hideAccommodationFields: () => {
    Utils.hideElement($(SELECTORS.apartmentSelectGroup));
    Utils.hideElement($(SELECTORS.roomSelectGroup));
    $(SELECTORS.apartmentId).prop('required', false);
    $(SELECTORS.roomId).prop('required', false);
  },

  loadBuildings: () => {
    ApiService.fetchBuildings()
      .done((response) => {
        if (response.success && response.data) {
          SelectManager.populateSelect(SELECTORS.buildingId, response.data, 'number', 'Select Building');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.buildingsLoadFailed);
      });
  },

  loadApartments: (buildingId) => {
    ApiService.fetchApartments(buildingId)
      .done((response) => {
        if (response.success && response.data) {
          const filteredData = response.data.filter(item => item.building_id == buildingId);
          SelectManager.populateSelect(SELECTORS.apartmentId, filteredData, 'number', 'Select Apartment');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.apartmentsLoadFailed);
      });
  },

  loadRoomsForApartment: (apartmentId) => {
    ApiService.fetchRooms(null, apartmentId)
      .done((response) => {
        if (response.success && response.data) {
          SelectManager.populateSelect(SELECTORS.roomId, response.data, 'number', 'Select Room');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.apartmentRoomsLoadFailed);
      });
  },

  loadAcademicTerms: () => {
    ApiService.fetchAcademicTerms()
      .done((response) => {
        if (response.success && response.data) {
          SelectManager.populateSelect(SELECTORS.academicTermId, response.data, 'name_en', 'Select Academic Term');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.academicTermsLoadFailed);
      });
  },

  loadEquipment: () => {
    ApiService.fetchEquipment()
      .done((response) => {
        if (response.success && response.data) {
          SelectManager.renderEquipmentList(response.data);
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.equipmentLoadFailed);
      });
  },

  populateSelect: (selector, data, valueField, placeholder) => {
    const $select = $(selector);
    Utils.clearSelect($select, placeholder);
    // Only add data-type for room selects
    const isRoomSelect = $select.is(SELECTORS.roomId);
    data.forEach(item => {
      if (isRoomSelect && item.type) {
        $select.append(`<option value="${item.id}" data-type="${item.type}">${item[valueField]}</option>`);
      } else {
        $select.append(`<option value="${item.id}">${item[valueField]}</option>`);
      }
    });
  },

  renderEquipmentList: (equipmentData) => {
    const $list = $(SELECTORS.equipmentList);
    $list.empty();
    
    if (!equipmentData.length) {
      $list.append('<div class="col-12 text-muted">No equipment available.</div>');
      return;
    }
    
    equipmentData.forEach(item => {
      const html = `
        <div class="col-md-6 mb-2">
          <div class="form-check d-flex align-items-center">
            <input class="form-check-input equipment-checkbox" 
                   type="checkbox" 
                   value="${item.id}" 
                   id="equipment_${item.id}" 
                   data-name="${item.name_en}">
            <label class="form-check-label ms-2" for="equipment_${item.id}">
              ${item.name_en}
            </label>
            <input type="number" 
                   min="1" 
                   class="form-control ms-3 equipment-qty" 
                   id="equipment_qty_${item.id}" 
                   name="equipment_qty_${item.id}" 
                   value="1" 
                   style="width:80px;" 
                   disabled>
          </div>
        </div>
      `;
      $list.append(html);
    });
    
    // Bind equipment checkbox events
    $list.find('.equipment-checkbox').on('change', function() {
      const id = $(this).val();
      $(`#equipment_qty_${id}`).prop('disabled', !this.checked);
    });
  },

  clearAccommodationSelects: () => {
    Utils.clearSelect($(SELECTORS.apartmentId), 'Select Apartment');
    Utils.clearSelect($(SELECTORS.roomId), 'Select Room');
  },

  resetForm: () => {
    Utils.clearSelect($(SELECTORS.buildingId), 'Select Building');
    SelectManager.clearAccommodationSelects();
    SelectManager.hideAccommodationFields();
  }
};

// ===========================
// PERIOD MANAGER
// ===========================
const PeriodManager = {
  init: () => {
    $(SELECTORS.period).on('change', PeriodManager.handlePeriodChange);
    PeriodManager.handlePeriodChange(); // Set initial state
  },
  handlePeriodChange: () => {
    const period = $(SELECTORS.period).val();
    if (period === 'academic') {
      Utils.showElement($(SELECTORS.academicTermGroup));
      Utils.hideElement($(SELECTORS.checkinoutGroup));
      $(SELECTORS.academicTermId).prop('required', true);
      $(SELECTORS.checkInDate).prop('required', false);
      $(SELECTORS.checkOutDate).prop('required', false);
    } else if (period === 'calendar') {
      Utils.hideElement($(SELECTORS.academicTermGroup));
      Utils.showElement($(SELECTORS.checkinoutGroup));
      $(SELECTORS.academicTermId).prop('required', false);
      $(SELECTORS.checkInDate).prop('required', true);
      $(SELECTORS.checkOutDate).prop('required', true);
    } else {
      Utils.hideElement($(SELECTORS.academicTermGroup));
      Utils.hideElement($(SELECTORS.checkinoutGroup));
      $(SELECTORS.academicTermId).prop('required', false);
      $(SELECTORS.checkInDate).prop('required', false);
      $(SELECTORS.checkOutDate).prop('required', false);
    }
  }
};

// ===========================
// RESERVATION MANAGER
// ===========================
const ReservationManager = {
  init: () => {
    ReservationManager.bindEvents();
  },

  bindEvents: () => {
    $(SELECTORS.addForm).on('submit', ReservationManager.handleSubmit);
  },

  handleSubmit: (e) => {
    e.preventDefault();
    ReservationManager.saveReservation();
  },

  saveReservation: () => {
    const formData = ReservationManager.getFormData();
    
    if (!ReservationManager.validateForm(formData)) {
      return;
    }
    
    const $btn = $(SELECTORS.submitBtn);
    Utils.disableButton($btn);
    
    ApiService.createReservation(formData)
      .done((response) => {
        if (response.success) {
          ReservationManager.handleSaveSuccess();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationCreateFailed);
        }
      })
      .fail((xhr) => {
        const message = xhr.responseJSON?.message || MESSAGES.error.reservationCreateFailed;
        Utils.showError(message);
      })
      .always(() => {
        Utils.disableButton($btn, false);
      });
  },

  getFormData: () => {
    const formData = {};
    
    // Serialize form fields
    $(SELECTORS.addForm).serializeArray().forEach(item => {
      if (item.value) {
        formData[item.name] = item.value;
      }
    });
    
    // Handle accommodation_id based on type
    const accommodationType = $(SELECTORS.accommodationType).val();
    if (accommodationType === 'room') {
      formData.accommodation_id = $(SELECTORS.roomId).val();
    } else if (accommodationType === 'apartment') {
      formData.accommodation_id = $(SELECTORS.apartmentId).val();
    }
    
    // Collect equipment
    const equipment = [];
    $(SELECTORS.equipmentList).find('.equipment-checkbox:checked').each(function() {
      const id = $(this).val();
      const qty = $(`#equipment_qty_${id}`).val();
      equipment.push({ 
        equipment_id: id, 
        quantity: parseInt(qty) || 1 
      });
    });
    
    if (equipment.length) {
      formData.equipment = equipment;
    }
    
    // Add double_room_bed_option if present
    const bedOption = $('input[name="double_room_bed_option"]:checked').val();
    if (bedOption) {
        formData.double_room_bed_option = bedOption;
    }
    
    return formData;
  },

  validateForm: (formData) => {
    if (!formData.user_id) {
      Utils.showError(MESSAGES.error.selectUser);
      return false;
    }
    if (!formData.period) {
      Utils.showError('Please select a period.');
      return false;
    }
    if (formData.period_type === 'academic' && !formData.academic_term_id) {
      Utils.showError('Please select an academic term.');
      return false;
    }
    if (formData.period_type === 'calendar') {
      if (!formData.check_in_date || !formData.check_out_date) {
        Utils.showError('Please select check-in and check-out dates.');
        return false;
      }
      if (new Date(formData.check_out_date) <= new Date(formData.check_in_date)) {
        Utils.showError(MESSAGES.error.invalidCheckOutDate);
        return false;
      }
    }
    
    return true;
  },

  handleSaveSuccess: () => {
    $(SELECTORS.addForm)[0].reset();
    $(SELECTORS.userId).val('');
    
    // Hide form sections
    Utils.hideElement($(SELECTORS.userInfoSection));
    Utils.hideElement($(SELECTORS.addForm));
    
    // Reset form state
    SelectManager.resetForm();
    $(SELECTORS.searchNationalId).val('');
    
    Utils.showSuccess(MESSAGES.success.reservationCreated);
  }
};

// ===========================
// APPLICATION INITIALIZER
// ===========================
const ReservationApp = {
  init: () => {
    UserSearchManager.init();
    SelectManager.init();
    ReservationManager.init();
    PeriodManager.init();
  },
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
  ReservationApp.init();
});


</script>
@endpush 