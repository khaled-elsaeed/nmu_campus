@extends('layouts.home')

@section('title', 'Reservation Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-calendar" label="Total Reservations" id="reservations" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-check-circle" label="Active Reservations" id="reservations-active" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-x-circle" label="Inactive Reservations" id="reservations-inactive" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Reservations"
        description="Manage reservations and their details."
        icon="bx bx-calendar"
    >
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationSearchCollapse" aria-expanded="false" aria-controls="reservationSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedReservationSearch" 
        collapseId="reservationSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_student_name" class="form-label">Student Name:</label>
            <input type="text" class="form-control" id="search_student_name" name="search_student_name">
        </div>
        <div class="col-md-4">
            <label for="search_student_national_id" class="form-label">Student National ID:</label>
            <input type="text" class="form-control" id="search_student_national_id" name="search_student_national_id">
        </div>
        <div class="col-md-4">
            <label for="search_student_academic_id" class="form-label">Student Academic ID:</label>
            <input type="text" class="form-control" id="search_student_academic_id" name="search_student_academic_id">
        </div>
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">Building:</label>
            <select class="form-control" id="search_building_id" name="search_building_id">
                <option value="">Select Building</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_number" class="form-label">Apartment:</label>
            <select class="form-control" id="search_apartment_number" name="search_apartment_number">
                <option value="">Select Apartment</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_room_number" class="form-label">Room:</label>
            <select class="form-control" id="search_room_number" name="search_room_number">
                <option value="">Select Room</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Student', 'Room', 'Start Date', 'End Date', 'Status', 'Actions']"
        :columns="[
            ['data' => 'student', 'name' => 'student_name'],
            ['data' => 'room', 'name' => 'room_number'],
            ['data' => 'start_date', 'name' => 'start_date'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservations.datatable')"
        :table-id="'reservations-table'"
        :filter-fields="[
            'search_student_name',
            'search_student_national_id',
            'search_student_academic_id',
            'search_building_id',
            'search_apartment_number',
            'search_room_number'
        ]"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Reservation Modal --}}
    <x-ui.modal 
        id="viewReservationModal"
        title="Reservation Details"
        size="md"
        :scrollable="true"
        class="view-reservation-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Student:</label>
                <p id="view-reservation-student" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Room:</label>
                <p id="view-reservation-room" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Start Date:</label>
                <p id="view-reservation-start-date" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">End Date:</label>
                <p id="view-reservation-end-date" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Status:</label>
                <p id="view-reservation-status" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Created At:</label>
                <p id="view-reservation-created" class="mb-0"></p>
            </div>
          </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection 

@push('scripts')
<script>

// ===========================
// CONSTANTS AND CONFIGURATION
// ===========================
const ROUTES = {
  reservations: {
    stats: '{{ route("reservations.stats") }}',
    show: '{{ route("reservations.show", ":id") }}',
    update: '{{ route("reservations.update", ":id") }}',
    destroy: '{{ route("reservations.destroy", ":id") }}',
    datatable: '{{ route("reservations.datatable") }}',
  },
  buildings: {
    all: '{{ route("housing.buildings.all") }}'
  },
  apartments: {
    all: '{{ route("housing.apartments.all") }}'
  },
  rooms: {
    all: '{{ route("housing.rooms.all") }}'
  }
};

const SELECTORS = {
  stats: {
    reservations: '#reservations',
    active: '#reservations-active',
    inactive: '#reservations-inactive'
  },
  table: '#reservations-table',
  modals: {
    viewReservation: '#viewReservationModal'
  },
  forms: {
    advancedSearch: '#advancedReservationSearch'
  },
  filters: {
    studentName: '#search_student_name',
    studentNationalId: '#search_student_national_id',
    studentAcademicId: '#search_student_academic_id',
    buildingId: '#search_building_id',
    apartmentNumber: '#search_apartment_number',
    roomNumber: '#search_room_number'
  },
  buttons: {
    clearFilters: '#clearReservationFiltersBtn',
    viewReservation: '.viewReservationBtn',
    deleteReservation: '.deleteReservationBtn',
    // activateReservation: '.activateReservationBtn', // Removed activateReservationBtn selector
  }
};

const MESSAGES = {
  success: {
    reservationDeleted: 'Reservation has been deleted.',
    // reservationActivated: 'Reservation activated successfully.', // Removed reservationActivated message
  },
  error: {
    statsLoadFailed: 'Failed to load reservation statistics.',
    reservationLoadFailed: 'Failed to load reservation data.',
    reservationDeleteFailed: 'Failed to delete reservation.',
    buildingsLoadFailed: 'Failed to load buildings.',
    apartmentsLoadFailed: 'Failed to load apartments.',
    roomsLoadFailed: 'Failed to load rooms.',
    operationFailed: 'Operation failed.'
  },
  confirm: {
    deleteReservation: {
      title: 'Delete Reservation?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    },
    // activateReservation: { // Removed activateReservation confirm message
    //   title: 'Activate Reservation?',
    //   text: 'Are you sure you want to activate this reservation?',
    //   confirmButtonText: 'Yes, activate it!'
    // },
    cancelReservation: { // Added cancelReservation confirm message
      title: 'Cancel Reservation?',
      text: 'Are you sure you want to cancel this reservation?',
      confirmButtonText: 'Yes, cancel it!'
    }
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
const Utils = {
  showError(message) {
    Swal.fire({ 
      title: 'Error', 
      html: message, 
      icon: 'error' 
    });
  },

  showSuccess(message) {
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

  toggleLoadingState(elementId, isLoading) {
    const $value = $(`#${elementId}-value`);
    const $loader = $(`#${elementId}-loader`);
    const $updated = $(`#${elementId}-last-updated`);
    const $updatedLoader = $(`#${elementId}-last-updated-loader`);
    
    if (isLoading) {
      $value.addClass('d-none');
      $loader.removeClass('d-none');
      $updated.addClass('d-none');
      $updatedLoader.removeClass('d-none');
    } else {
      $value.removeClass('d-none');
      $loader.addClass('d-none');
      $updated.removeClass('d-none');
      $updatedLoader.addClass('d-none');
    }
  },

  replaceRouteId(route, id) {
    return route.replace(':id', id);
  },

  formatDate(dateString) {
    return dateString ? new Date(dateString).toLocaleString() : '--';
  },

  confirmAction(options = {}) {
    const defaults = {
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, do it!'
    };
    return Swal.fire({ ...defaults, ...options });
  },

  getElementData(element, attribute) {
    return $(element).data(attribute);
  },

  disableButton($button, disabled = true) {
    $button.prop('disabled', disabled);
  }
};

// ===========================
// API SERVICE LAYER
// ===========================
const ApiService = {
  request(options) {
    return $.ajax(options);
  },

  fetchStats() {
    return this.request({
      url: ROUTES.reservations.stats,
      method: 'GET'
    });
  },

  fetchReservation(id) {
    return this.request({
      url: Utils.replaceRouteId(ROUTES.reservations.show, id),
      method: 'GET'
    });
  },

  updateReservation(id, data) {
    return this.request({
      url: Utils.replaceRouteId(ROUTES.reservations.update, id),
      method: 'PUT',
      data
    });
  },

  deleteReservation(id) {
    return this.request({
      url: Utils.replaceRouteId(ROUTES.reservations.destroy, id),
      method: 'DELETE'
    });
  },

  // activateReservation(id) { // Removed activateReservation API call
  //   return this.request({
  //     url: Utils.replaceRouteId(ROUTES.reservations.activate, id),
  //     method: 'PATCH'
  //   });
  // },

  fetchBuildings() {
    return this.request({
      url: ROUTES.buildings.all,
      method: 'GET'
    });
  },

  fetchApartments(buildingId = null) {
    const data = buildingId ? { building_id: buildingId } : {};
    return this.request({
      url: ROUTES.apartments.all,
      method: 'GET',
      data
    });
  },

  fetchRooms(apartmentId = null) {
    const data = apartmentId ? { apartment_id: apartmentId } : {};
    return this.request({
      url: ROUTES.rooms.all,
      method: 'GET',
      data
    });
  }
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================
const StatsManager = {
  init() {
    this.loadStats();
  },

  loadStats() {
    this.toggleAllStatsLoading(true);
    
    ApiService.fetchStats()
      .done((response) => {
        if (response.success) {
          this.updateStatsDisplay(response.data);
        } else {
          this.setStatsError();
        }
      })
      .fail(() => {
        this.setStatsError();
        Utils.showError(MESSAGES.error.statsLoadFailed);
      })
      .always(() => {
        this.toggleAllStatsLoading(false);
      });
  },

  updateStatsDisplay(data) {
    this.updateSingleStat('reservations', data.total);
    this.updateSingleStat('reservations-active', data.active);
    this.updateSingleStat('reservations-inactive', data.inactive);
  },

  updateSingleStat(statId, data) {
    $(`#${statId}-value`).text(data.count);
    $(`#${statId}-last-updated`).text(data.lastUpdateTime);
  },

  setStatsError() {
    const statIds = ['reservations', 'reservations-active', 'reservations-inactive'];
    statIds.forEach(id => {
      $(`#${id}-value`).text('N/A');
      $(`#${id}-last-updated`).text('N/A');
    });
  },

  toggleAllStatsLoading(isLoading) {
    Utils.toggleLoadingState('reservations', isLoading);
    Utils.toggleLoadingState('reservations-active', isLoading);
    Utils.toggleLoadingState('reservations-inactive', isLoading);
  }
};

// ===========================
// RESERVATION MANAGEMENT
// ===========================
const ReservationManager = {
  init() {
    this.bindEvents();
  },

  bindEvents() {
    $(document)
      .on('click', SELECTORS.buttons.viewReservation, (e) => this.handleViewReservation(e))
      .on('click', SELECTORS.buttons.deleteReservation, (e) => this.handleDeleteReservation(e)); // Removed activateReservation event handler
  },

  handleViewReservation(e) {
    const reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.viewReservation(reservationId);
  },

  handleDeleteReservation(e) {
    const reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.confirmAndDeleteReservation(reservationId);
  },

  // handleActivateReservation(e) { // Removed handleActivateReservation
  //   e.preventDefault();
  //   const reservationId = Utils.getElementData(e.currentTarget, 'id');
  //   this.confirmAndToggleReservationStatus(reservationId, true, $(e.currentTarget));
  // },

  viewReservation(reservationId) {
    ApiService.fetchReservation(reservationId)
      .done((response) => {
        if (response.success) {
          this.populateViewModal(response.data);
          $(SELECTORS.modals.viewReservation).modal('show');
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationLoadFailed);
        }
      })
      .fail(() => {
        $(SELECTORS.modals.viewReservation).modal('hide');
        Utils.showError(MESSAGES.error.reservationLoadFailed);
      });
  },

  confirmAndDeleteReservation(reservationId) {
    Utils.confirmAction(MESSAGES.confirm.deleteReservation)
      .then((result) => {
        if (result.isConfirmed) {
          this.deleteReservation(reservationId);
        }
      });
  },

  // confirmAndToggleReservationStatus(reservationId, isActivate, $button) { // Removed confirm and toggle reservation status logic
  //   const confirmMessage = isActivate ? MESSAGES.confirm.activateReservation : MESSAGES.confirm.cancelReservation;
    
  //   Utils.confirmAction(confirmMessage)
  //     .then((result) => {
  //       if (result.isConfirmed) {
  //         this.toggleReservationStatus(reservationId, isActivate, $button);
  //       }
  //     });
  // },

  populateViewModal(reservation) {
    const fields = [
      { id: 'student', value: reservation.student_name },
      { id: 'room', value: reservation.room_number },
      { id: 'start-date', value: Utils.formatDate(reservation.start_date) },
      { id: 'end-date', value: Utils.formatDate(reservation.end_date) },
      { id: 'status', value: reservation.status },
      { id: 'created', value: Utils.formatDate(reservation.created_at) }
    ];

    fields.forEach(field => {
      $(`#view-reservation-${field.id}`).text(field.value);
    });
  },

  deleteReservation(reservationId) {
    ApiService.deleteReservation(reservationId)
      .done((response) => {
        if (response.success) {
          this.reloadTable();
          Utils.showSuccess(MESSAGES.success.reservationDeleted);
          StatsManager.loadStats();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationDeleteFailed);
        }
      })
      .fail((xhr) => {
        const message = xhr.responseJSON?.message || MESSAGES.error.reservationDeleteFailed;
        Utils.showError(message);
      });
  },

  // toggleReservationStatus(reservationId, isActivate, $button) { // Removed toggle reservation status logic
  //   const apiCall = isActivate ? ApiService.activateReservation : ApiService.cancelReservation;
  //   const successMessage = isActivate ? MESSAGES.success.reservationActivated : MESSAGES.success.reservationCancelled;
    
  //   Utils.disableButton($button, true);
    
  //   apiCall(reservationId)
  //     .done((response) => {
  //       if (response.success) {
  //         Utils.showSuccess(successMessage);
  //         this.reloadTable();
  //         StatsManager.loadStats();
  //       } else {
  //         Utils.showError(response.message || MESSAGES.error.operationFailed);
  //       }
  //     })
  //     .fail((xhr) => {
  //       const message = xhr.responseJSON?.message || MESSAGES.error.operationFailed;
  //       Utils.showError(message);
  //     })
  //     .always(() => {
  //       Utils.disableButton($button, false);
  //     });
  // },

  reloadTable() {
    $(SELECTORS.table).DataTable().ajax.reload(null, false);
  }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
const SearchManager = {
  init() {
    this.bindEvents();
  },

  bindEvents() {
    const filterSelectors = Object.values(SELECTORS.filters).join(', ');
    
    $(filterSelectors).on('keyup change', () => {
      this.reloadTable();
    });
    
    $(SELECTORS.buttons.clearFilters).on('click', () => {
      this.clearFilters();
    });
  },

  clearFilters() {
    const filterSelectors = Object.values(SELECTORS.filters).join(', ');
    $(filterSelectors).val('');
    this.reloadTable();
  },

  reloadTable() {
    $(SELECTORS.table).DataTable().ajax.reload();
  }
};

// ===========================
// SELECT MANAGEMENT
// ===========================
const SelectManager = {
  init() {
    this.populateBuildingSelect();
    this.bindEvents();
  },

  bindEvents() {
    $(SELECTORS.filters.buildingId).on('change', (e) => {
      const buildingId = $(e.target).val();
      this.handleBuildingChange(buildingId);
    });
    
    $(SELECTORS.filters.apartmentNumber).on('change', (e) => {
      const apartmentNumber = $(e.target).val();
      this.handleApartmentChange(apartmentNumber);
    });
  },

  handleBuildingChange(buildingId) {
    this.populateApartmentSelect(buildingId);
    this.clearRoomSelect();
  },

  handleApartmentChange(apartmentNumber) {
    this.populateRoomSelect(apartmentNumber);
  },

  populateBuildingSelect() {
    ApiService.fetchBuildings()
      .done((response) => {
        if (response.success && response.data) {
          this.populateSelect(SELECTORS.filters.buildingId, response.data, 'number', 'Select Building');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.buildingsLoadFailed);
      });
  },

  populateApartmentSelect(buildingId) {
    if (!buildingId) {
      this.clearSelect(SELECTORS.filters.apartmentNumber, 'Select Apartment');
      return;
    }

    ApiService.fetchApartments(buildingId)
      .done((response) => {
        if (response.success && response.data) {
          const filteredApartments = response.data.filter(apartment => apartment.building_id == buildingId);
          this.populateSelect(SELECTORS.filters.apartmentNumber, filteredApartments, 'number', 'Select Apartment');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.apartmentsLoadFailed);
      });
  },

  populateRoomSelect(apartmentNumber) {
    if (!apartmentNumber) {
      this.clearRoomSelect();
      return;
    }

    ApiService.fetchRooms(apartmentNumber)
      .done((response) => {
        if (response.success && response.data) {
          this.populateSelect(SELECTORS.filters.roomNumber, response.data, 'number', 'Select Room');
        }
      })
      .fail(() => {
        Utils.showError(MESSAGES.error.roomsLoadFailed);
      });
  },

  populateSelect(selector, data, valueField, placeholder) {
    const $select = $(selector);
    $select.empty().append(`<option value="">${placeholder}</option>`);
    
    data.forEach(item => {
      $select.append(`<option value="${item.id}">${item[valueField]}</option>`);
    });
  },

  clearSelect(selector, placeholder) {
    $(selector).empty().append(`<option value="">${placeholder}</option>`);
  },

  clearRoomSelect() {
    this.clearSelect(SELECTORS.filters.roomNumber, 'Select Room');
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
const ReservationApp = {
  init() {
    this.initializeManagers();
  },

  initializeManagers() {
    StatsManager.init();
    ReservationManager.init();
    SearchManager.init();
    SelectManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
  ReservationApp.init();
});

</script>
@endpush 