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
            <label for="search_reservation_number" class="form-label">Reservation Number:</label>
            <input type="text" class="form-control" id="search_reservation_number" name="search_reservation_number">
        </div>
        <div class="col-md-4">
            <label for="search_user_name" class="form-label">User Name:</label>
            <input type="text" class="form-control" id="search_user_name" name="search_user_name">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">Status:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked_in">Checked In</option>
                <option value="checked_out">Checked Out</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">Active:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term_id" class="form-label">Academic Term:</label>
            <select class="form-control" id="search_academic_term_id" name="search_academic_term_id">
                <option value="">All Terms</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_accommodation_id" class="form-label">Accommodation:</label>
            <select class="form-control" id="search_accommodation_id" name="search_accommodation_id">
                <option value="">All Accommodations</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Reservation #', 'User', 'Accommodation', 'Academic Term', 'Check-in', 'Check-out', 'Status', 'Active', 'Period Type', 'Created', 'Actions']"
        :columns="[
            ['data' => 'reservation_number', 'name' => 'reservation_number'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'accommodation_info', 'name' => 'accommodation_info'],
            ['data' => 'academic_term', 'name' => 'academic_term'],
            ['data' => 'check_in_date', 'name' => 'check_in_date'],
            ['data' => 'check_out_date', 'name' => 'check_out_date'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'period_type', 'name' => 'period_type'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservations.datatable')"
        :table-id="'reservations-table'"
        :filter-fields="[
            'search_reservation_number',
            'search_user_name',
            'search_status',
            'search_active',
            'search_academic_term_id',
            'search_accommodation_id',
            'search_period_type'
        ]"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add Reservation Modal --}}
    {{-- (Removed) --}}

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
                <label class="form-label fw-bold">Reservation Number:</label>
                <p id="view-reservation-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">User:</label>
                <p id="view-reservation-user" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Accommodation:</label>
                <p id="view-reservation-accommodation" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Academic Term:</label>
                <p id="view-reservation-academic-term" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Check-in Date:</label>
                <p id="view-reservation-check-in" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Check-out Date:</label>
                <p id="view-reservation-check-out" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Status:</label>
                <p id="view-reservation-status" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Active:</label>
                <p id="view-reservation-active" class="mb-0"></p>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">Notes:</label>
                <p id="view-reservation-notes" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Created At:</label>
                <p id="view-reservation-created" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Period Type:</label>
                <p id="view-reservation-period-type" class="mb-0"></p>
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
/**
 * Reservation Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ReservationManager: Handles CRUD and actions for reservations
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ReservationApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  reservations: {
    stats: '{{ route("reservations.stats") }}',
    store: '{{ route("reservations.store") }}',
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
  },
  users: {
    all: '{{ route("users.all") }}'
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
  }
};

var MESSAGES = {
  success: {
    reservationCreated: 'Reservation has been created successfully.',
    reservationUpdated: 'Reservation has been updated successfully.',
    reservationDeleted: 'Reservation has been deleted.'
  },
  error: {
    statsLoadFailed: 'Failed to load reservation statistics.',
    reservationLoadFailed: 'Failed to load reservation data.',
    reservationCreateFailed: 'Failed to create reservation.',
    reservationUpdateFailed: 'Failed to update reservation.',
    reservationDeleteFailed: 'Failed to delete reservation.',
    buildingsLoadFailed: 'Failed to load buildings.',
    apartmentsLoadFailed: 'Failed to load apartments.',
    roomsLoadFailed: 'Failed to load rooms.',
    usersLoadFailed: 'Failed to load users.',
    academicTermsLoadFailed: 'Failed to load academic terms.',
    operationFailed: 'Operation failed.'
  },
  confirm: {
    deleteReservation: {
      title: 'Delete Reservation?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    },
    cancelReservation: {
      title: 'Cancel Reservation?',
      text: 'Are you sure you want to cancel this reservation?',
      confirmButtonText: 'Yes, cancel it!'
    }
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
var Utils = {
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  toggleLoadingState: function(elementId, isLoading) {
    var $value = $('#' + elementId + '-value');
    var $loader = $('#' + elementId + '-loader');
    var $updated = $('#' + elementId + '-last-updated');
    var $updatedLoader = $('#' + elementId + '-last-updated-loader');
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
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
  },
  formatDate: function(dateString) {
    return dateString ? new Date(dateString).toLocaleString() : '--';
  },
  confirmAction: function(options) {
    var defaults = {
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, do it!'
    };
    return Swal.fire(Object.assign({}, defaults, options));
  },
  getElementData: function(element, attribute) {
    return $(element).data(attribute);
  },
  disableButton: function($button, disabled) {
    $button.prop('disabled', disabled === undefined ? true : disabled);
  }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  request: function(options) {
    return $.ajax(options);
  },
  fetchStats: function() {
    return this.request({ url: ROUTES.reservations.stats, method: 'GET' });
  },
  fetchReservation: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservations.show, id), method: 'GET' });
  },
  updateReservation: function(id, data) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservations.update, id), method: 'PUT', data: data });
  },
  deleteReservation: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservations.destroy, id), method: 'DELETE' });
  },
  fetchBuildings: function() {
    return this.request({ url: ROUTES.buildings.all, method: 'GET' });
  },
  fetchApartments: function(buildingId) {
    var data = buildingId ? { building_id: buildingId } : {};
    return this.request({ url: ROUTES.apartments.all, method: 'GET', data: data });
  },
  fetchRooms: function(apartmentId) {
    var data = apartmentId ? { apartment_id: apartmentId } : {};
    return this.request({ url: ROUTES.rooms.all, method: 'GET', data: data });
  },
  fetchUsers: function() {
    return this.request({ url: ROUTES.users.all, method: 'GET' });
  },
  fetchAcademicTerms: function() {
    return this.request({ url: ROUTES.academicTerms.all, method: 'GET' });
  },
  createReservation: function(data) {
    return this.request({ url: ROUTES.reservations.store, method: 'POST', data: data });
  },
  updateReservation: function(id, data) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservations.update, id), method: 'PUT', data: data });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = {
  init: function() {
    this.load();
  },
  load: function() {
    this.toggleAllLoadingStates(true);
    ApiService.fetchStats()
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
  handleSuccess: function(response) {
    if (response.success) {
      let stats = response.data;
      this.updateStatElement('reservations', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('reservations-active', stats.active.count, stats.active.lastUpdateTime);
      this.updateStatElement('reservations-inactive', stats.inactive.count, stats.inactive.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load reservation statistics');
  },
  updateStatElement: function(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  setAllStatsToNA: function() {
    ['reservations', 'reservations-active', 'reservations-inactive'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  toggleAllLoadingStates: function(isLoading) {
    ['reservations', 'reservations-active', 'reservations-inactive'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// RESERVATION MANAGER
// ===========================
var ReservationManager = {
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    $(document)
      .on('click', '.viewReservationBtn', function(e) { self.handleViewReservation(e); })
      .on('click', '.deleteReservationBtn', function(e) { self.handleDeleteReservation(e); })
      .on('hidden.bs.modal', '#viewReservationModal', function() { self.resetViewModal(); });
  },
  handleViewReservation: function(e) {
    var reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.viewReservation(reservationId);
  },
  handleDeleteReservation: function(e) {
    var reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.confirmAndDeleteReservation(reservationId);
  },
  viewReservation: function(reservationId) {
    ApiService.fetchReservation(reservationId)
      .done(function(response) {
        if (response.success) {
          ReservationManager.populateViewModal(response.data);
          $('#viewReservationModal').modal('show');
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationLoadFailed);
        }
      })
      .fail(function() {
        $('#viewReservationModal').modal('hide');
        Utils.showError(MESSAGES.error.reservationLoadFailed);
      });
  },
  confirmAndDeleteReservation: function(reservationId) {
    Utils.confirmAction(MESSAGES.confirm.deleteReservation)
      .then(function(result) {
        if (result.isConfirmed) {
          ReservationManager.deleteReservation(reservationId);
        }
      });
  },
  populateViewModal: function(reservation) {
    var fields = [
      { id: 'number', value: reservation.reservation_number },
      { id: 'user', value: reservation.user_name },
      { id: 'accommodation', value: reservation.accommodation_info },
      { id: 'academic-term', value: reservation.academic_term },
      { id: 'check-in', value: reservation.check_in_date },
      { id: 'check-out', value: reservation.check_out_date },
      { id: 'status', value: reservation.status },
      { id: 'active', value: reservation.active },
      { id: 'notes', value: reservation.notes },
      { id: 'created', value: reservation.created_at },
      { id: 'period-type', value: reservation.period_type }
    ];
    fields.forEach(function(field) {
      $('#view-reservation-' + field.id).text(field.value);
    });
  },
  resetViewModal: function() {
    // No specific reset needed for view modal as it's just a display
  },
  deleteReservation: function(reservationId) {
    ApiService.deleteReservation(reservationId)
      .done(function(response) {
        if (response.success) {
          ReservationManager.reloadTable();
          Utils.showSuccess(MESSAGES.success.reservationDeleted);
          StatsManager.load();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationDeleteFailed);
        }
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : MESSAGES.error.reservationDeleteFailed;
        Utils.showError(message);
      });
  },
  reloadTable: function() {
    $('#reservations-table').DataTable().ajax.reload(null, false);
  }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    var filterSelectors = [
      '#search_reservation_number',
      '#search_user_name',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
      '#search_accommodation_id',
      '#search_period_type'
    ].join(', ');
    $(filterSelectors).on('keyup change', function() { self.reloadTable(); });
    $('#clearReservationFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  clearFilters: function() {
    var filterSelectors = [
      '#search_reservation_number',
      '#search_user_name',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
      '#search_accommodation_id',
      '#search_period_type'
    ].join(', ');
    $(filterSelectors).val('');
    this.reloadTable();
  },
  reloadTable: function() {
    $('#reservations-table').DataTable().ajax.reload();
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  init: function() {
    this.populateBuildingSelect();
    this.populateUserSelect();
    this.populateAcademicTermsSelect();
    this.populateSearchAcademicTermsSelect();
    this.populateEditUserSelect();
    this.populateEditAcademicTermsSelect();
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    // Search filters
    $('#search_building_id').on('change', function(e) {
      var buildingId = $(e.target).val();
      self.handleBuildingChange(buildingId);
    });
    $('#search_apartment_number').on('change', function(e) {
      var apartmentNumber = $(e.target).val();
      self.handleApartmentChange(apartmentNumber);
    });
    // Edit form
    $('#edit_accommodation_type').on('change', function(e) {
      var type = $(e.target).val();
      self.handleEditAccommodationTypeChange(type);
    });
    $('#edit_building_id').on('change', function(e) {
      var buildingId = $(e.target).val();
      var accommodationType = $('#edit_accommodation_type').val();
      self.handleEditBuildingChange(buildingId, accommodationType);
    });
  },
  handleBuildingChange: function(buildingId) {
    this.populateApartmentSelect(buildingId);
    this.clearRoomSelect();
  },
  handleApartmentChange: function(apartmentNumber) {
    this.populateRoomSelect(apartmentNumber);
  },
  handleEditAccommodationTypeChange: function(type) {
    if (type) {
      this.populateEditBuildingSelect();
      this.clearEditAccommodationSelect();
    } else {
      this.clearEditBuildingSelect();
      this.clearEditAccommodationSelect();
    }
  },
  handleEditBuildingChange: function(buildingId, accommodationType) {
    this.populateEditAccommodationSelect(buildingId, accommodationType);
  },
  populateBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_building_id', response.data, 'number', 'Select Building');
        }
      })
      .fail(function() {
        Utils.showError(MESSAGES.error.buildingsLoadFailed);
      });
  },
  populateEditBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#edit_building_id', response.data, 'number', 'Select Building');
        }
      })
      .fail(function() {
        Utils.showError(MESSAGES.error.buildingsLoadFailed);
      });
  },
  populateApartmentSelect: function(buildingId) {
    if (!buildingId) {
      this.clearSelect('#search_apartment_number', 'Select Apartment');
      return;
    }
    ApiService.fetchApartments(buildingId)
      .done(function(response) {
        if (response.success && response.data) {
          var filteredApartments = response.data.filter(function(apartment) { return apartment.building_id == buildingId; });
          SelectManager.populateSelect('#search_apartment_number', filteredApartments, 'number', 'Select Apartment');
        }
      })
      .fail(function() {
        Utils.showError(MESSAGES.error.apartmentsLoadFailed);
      });
  },
  populateEditAccommodationSelect: function(buildingId, accommodationType) {
    if (!buildingId || !accommodationType) {
      this.clearEditAccommodationSelect();
      return;
    }
    if (accommodationType === 'room') {
      ApiService.fetchRooms(buildingId)
        .done(function(response) {
          if (response.success && response.data) {
            var filteredRooms = response.data.filter(function(room) { return room.building_id == buildingId; });
            SelectManager.populateSelect('#edit_accommodation_id', filteredRooms, 'number', 'Select Room');
          }
        })
        .fail(function() {
          Utils.showError(MESSAGES.error.roomsLoadFailed);
        });
    } else if (accommodationType === 'apartment') {
      ApiService.fetchApartments(buildingId)
        .done(function(response) {
          if (response.success && response.data) {
            var filteredApartments = response.data.filter(function(apartment) { return apartment.building_id == buildingId; });
            SelectManager.populateSelect('#edit_accommodation_id', filteredApartments, 'number', 'Select Apartment');
          }
        })
        .fail(function() {
          Utils.showError(MESSAGES.error.apartmentsLoadFailed);
        });
    }
  },
  clearEditAccommodationSelect: function() {
    this.clearSelect('#edit_accommodation_id', 'Select Room/Apartment');
  },
  clearEditBuildingSelect: function() {
    this.clearSelect('#edit_building_id', 'Select Building');
  },
  populateUserSelect: function() {
    ApiService.fetchUsers()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelectWithUserInfo('#edit_user_id', response.data, 'Select User');
        }
      })
      .fail(function() {
        Utils.showError(MESSAGES.error.usersLoadFailed);
      });
  },
  populateEditAcademicTermsSelect: function() {
    ApiService.fetchAcademicTerms()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#edit_academic_term_id', response.data, 'name_en', 'Select Academic Term');
        }
      })
      .fail(function() {
        Utils.showError(MESSAGES.error.academicTermsLoadFailed);
      });
  },
  populateSelect: function(selector, data, valueField, placeholder) {
    var $select = $(selector);
    $select.empty().append('<option value="">' + placeholder + '</option>');
    data.forEach(function(item) {
      $select.append('<option value="' + item.id + '">' + item[valueField] + '</option>');
    });
  },
  populateSelectWithUserInfo: function(selector, data, placeholder) {
    var $select = $(selector);
    $select.empty().append('<option value="">' + placeholder + '</option>');
    data.forEach(function(item) {
      var displayText = item.name_en;
      if (item.user_type && item.additional_info) {
        displayText += ' (' + item.user_type + ' - ' + item.additional_info + ')';
      }
      $select.append('<option value="' + item.id + '">' + displayText + '</option>');
    });
  },
  clearSelect: function(selector, placeholder) {
    $(selector).empty().append('<option value="">' + placeholder + '</option>');
  },
  clearRoomSelect: function() {
    this.clearSelect('#search_room_number', 'Select Room');
  },
  resetEditFormDropdowns: function() {
    this.clearEditAccommodationSelect();
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var ReservationApp = {
  init: function() {
    StatsManager.init();
    ReservationManager.init();
    SearchManager.init();
    SelectManager.init();
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