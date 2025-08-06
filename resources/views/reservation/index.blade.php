@extends('layouts.home')

@section('title', __('reservations.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="secondary"
                icon="bx bx-calendar"
                label="Total Reservations"
                id="reservations"
                :subStats="[
                    'male' => [
                        'label' => 'Male Reservations',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Female Reservations',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="warning"
                icon="bx bx-time"
                label="Pending Reservations"
                id="pending"
                :subStats="[
                    'male' => [
                        'label' => 'Pending Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Pending Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="info"
                icon="bx bx-check-circle"
                label="Confirmed Reservations"
                id="confirmed"
                :subStats="[
                    'male' => [
                        'label' => 'Confirmed Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Confirmed Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="success"
                icon="bx bx-log-in"
                label="Checked In"
                id="checked_in"
                :subStats="[
                    'male' => [
                        'label' => 'Checked In Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Checked In Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('reservations.page_header')"
        :description="__('reservations.page_description')"
        icon="bx bx-calendar"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationSearchCollapse" aria-expanded="false" aria-controls="reservationSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> Search
        </button>
    </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('reservations.search.advanced_title')" 
        formId="advancedReservationSearch" 
        collapseId="reservationSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_national_id" class="form-label">{{ __('reservations.search.fields.national_id') }}:</label>
            <input type="text" class="form-control" id="search_national_id" name="search_national_id">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('reservations.search.fields.status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">{{ __('reservations.search.placeholders.all_statuses') }}</option>
                <option value="pending">{{ __('reservations.status.pending') }}</option>
                <option value="confirmed">{{ __('reservations.status.confirmed') }}</option>
                <option value="checked_in">{{ __('reservations.status.checked_in') }}</option>
                <option value="checked_out">{{ __('reservations.status.checked_out') }}</option>
                <option value="cancelled">{{ __('reservations.status.cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('reservations.search.fields.active') }}:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="">{{ __('reservations.search.placeholders.all') }}</option>
                <option value="1">{{ __('reservations.active_status.active') }}</option>
                <option value="0">{{ __('reservations.active_status.inactive') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term_id" class="form-label">{{ __('reservations.search.fields.academic_term') }}:</label>
            <select class="form-control" id="search_academic_term_id" name="search_academic_term_id">
                <option value="">{{ __('reservations.search.placeholders.all_terms') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">{{ __('reservations.search.fields.building') }}:</label>
            <select class="form-control" id="search_building_id" name="search_building_id">
                <option value="">{{ __('reservations.search.placeholders.select_building') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_number" class="form-label">{{ __('reservations.search.fields.apartment') }}:</label>
            <select class="form-control" id="search_apartment_number" name="search_apartment_number" disabled>
                <option value="">{{ __('reservations.search.placeholders.select_apartment') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_room_number" class="form-label">{{ __('reservations.search.fields.room') }}:</label>
            <select class="form-control" id="search_room_number" name="search_room_number" disabled>
                <option value="">{{ __('reservations.search.placeholders.select_room') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('reservations.search.clear_filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="[
            __('reservations.table.headers.reservation_number'),
            __('reservations.table.headers.user'),
            __('reservations.table.headers.location'),
            __('reservations.table.headers.period'),
            __('reservations.table.headers.status'),
            __('reservations.table.headers.created'),
            __('reservations.table.headers.actions')
        ]"
        :columns="[
            ['data' => 'reservation_number', 'name' => 'reservation_number'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'location', 'name' => 'location'],
            ['data' => 'period', 'name' => 'period'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservations.datatable')"
        :table-id="'reservations-table'"
        :filter-fields="[
            'search_national_id',
            'search_status',
            'search_active',
            'search_academic_term_id',
            'search_building_id',
            'search_apartment_number',
            'search_room_number',
            'search_period_type'
        ]"
    />
</div>
@endsection

@push('scripts')
<script>
/**
 * Reservation Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ReservationManager: Handles CRUD and actions for reservations
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ReservationApp: Initializes all managers
 */

// ===========================
// TRANSLATION CONSTANTS
// ===========================
var MESSAGES = {
  success: {
    reservationCreated: @json(__('reservations.messages.success.created')),
    reservationUpdated: @json(__('reservations.messages.success.updated')),
    reservationDeleted: @json(__('reservations.messages.success.deleted')),
    reservationCancelled: @json(__('reservations.messages.success.cancelled'))
  },
  error: {
    statsLoadFailed: @json(__('reservations.messages.error.stats_load_failed')),
    reservationLoadFailed: @json(__('reservations.messages.error.load_failed')),
    reservationCreateFailed: @json(__('reservations.messages.error.create_failed')),
    reservationUpdateFailed: @json(__('reservations.messages.error.update_failed')),
    reservationDeleteFailed: @json(__('reservations.messages.error.delete_failed')),
    reservationCancelFailed: @json(__('reservations.messages.error.cancel_failed')),
    buildingsLoadFailed: @json(__('reservations.messages.error.buildings_load_failed')),
    apartmentsLoadFailed: @json(__('reservations.messages.error.apartments_load_failed')),
    roomsLoadFailed: @json(__('reservations.messages.error.rooms_load_failed')),
    academicTermsLoadFailed: @json(__('reservations.messages.error.academic_terms_load_failed')),
    operationFailed: @json(__('reservations.messages.error.operation_failed'))
  },
  confirm: {
    deleteReservation: {
      title: @json(__('reservations.confirm.delete.title')),
      text: @json(__('reservations.confirm.delete.text')),
      confirmButtonText: @json(__('reservations.confirm.delete.button'))
    },
    cancelReservation: {
      title: @json(__('reservations.confirm.cancel.title')),
      text: @json(__('reservations.confirm.cancel.text')),
      confirmButtonText: @json(__('reservations.confirm.cancel.button'))
    }
  },
  placeholders: {
    selectBuilding: @json(__('reservations.search.placeholders.select_building')),
    selectApartment: @json(__('reservations.search.placeholders.select_apartment')),
    selectRoom: @json(__('reservations.search.placeholders.select_room')),
    allTerms: @json(__('reservations.search.placeholders.all_terms'))
  }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  reservations: {
    stats: '{{ route("reservations.stats") }}',
    store: '{{ route("reservations.store") }}',
    destroy: '{{ route("reservations.destroy", ":id") }}',
    datatable: '{{ route("reservations.datatable") }}',
    cancel: '{{ route("reservations.cancel", ":id") }}', // Added cancel route
  },
  buildings: {
    all: '{{ route("housing.buildings.all") }}'
  },
  apartments: {
    all: '{{ route("housing.apartments.all", ":id") }}'
  },
  rooms: {
    all: '{{ route("housing.rooms.all", ":id") }}'
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
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
    return ApiService.request({ url: ROUTES.reservations.stats, method: 'GET' });
  },
  deleteReservation: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.reservations.destroy, id), method: 'DELETE' });
  },
  cancelReservation: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.reservations.cancel, id), method: 'POST' });
  },
  fetchBuildings: function() {
    return ApiService.request({ url: ROUTES.buildings.all, method: 'GET' });
  },
  fetchApartments: function(buildingId) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.all,buildingId ), method: 'GET'});
  },
  fetchRooms: function(apartmentId) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.all, apartmentId), method: 'GET'});
  },
  fetchAcademicTerms: function() {
    return ApiService.request({ url: ROUTES.academicTerms.all, method: 'GET' });
  },
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: [
    'reservations',
    'pending',
    'confirmed',
    'checked_in'
  ],
  subStatsConfig: {
    'reservations': ['male', 'female'],
    'pending': ['male', 'female'],
    'confirmed': ['male', 'female'],
    'checked_in': ['male', 'female']
  },
  onError: 'Failed to load Reservation statistics'
});

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
      .on('click', '.deleteReservationBtn', function(e) { self.handleDeleteReservation(e); })
      .on('click', '.cancelReservationBtn', function(e) { self.handleCancelReservation(e); });
  },
  handleDeleteReservation: function(e) {
    var reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.confirmAndDeleteReservation(reservationId);
  },
  confirmAndDeleteReservation: function(reservationId) {
    Utils.confirmAction(MESSAGES.confirm.deleteReservation)
      .then(function(result) {
        if (result.isConfirmed) {
          ReservationManager.deleteReservation(reservationId);
        }
      });
  },
  deleteReservation: function(reservationId) {
    ApiService.deleteReservation(reservationId)
      .done(function(response) {
        if (response.success) {
          Utils.reloadDataTable('#reservations-table');
          Utils.showSuccess(MESSAGES.success.reservationDeleted);
          StatsManager.load();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationDeleteFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
      });
  },
  // --- Cancel Reservation Handlers ---
  handleCancelReservation: function(e) {
    var reservationId = Utils.getElementData(e.currentTarget, 'id');
    this.confirmAndCancelReservation(reservationId);
  },
  confirmAndCancelReservation: function(reservationId) {
    Utils.confirmAction(MESSAGES.confirm.cancelReservation)
      .then(function(result) {
        if (result.isConfirmed) {
          ReservationManager.cancelReservation(reservationId);
        }
      });
  },
  cancelReservation: function(reservationId) {
    ApiService.cancelReservation(reservationId)
      .done(function(response) {
        if (response.success) {
          Utils.reloadDataTable('#reservations-table');
          Utils.showSuccess(MESSAGES.success.reservationCancelled);
          StatsManager.refresh();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationCancelFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
      });
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
      '#search_national_id',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
      '#search_building_id',
      '#search_apartment_number',
      '#search_room_number',
      '#search_period_type'
    ].join(', ');
    $(filterSelectors).on('keyup change', function() {Utils.reloadDataTable('#reservations-table');});
    $('#clearReservationFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  clearFilters: function() {
    var filterSelectors = [
      '#search_national_id',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
      '#search_building_id',
      '#search_apartment_number',
      '#search_room_number',
      '#search_period_type'
    ].join(', ');
    $(filterSelectors).val('');
    // Reset apartment and room selects to disabled
    $('#search_apartment_number').prop('disabled', true).empty().append('<option value="">' + MESSAGES.placeholders.selectApartment + '</option>');
    $('#search_room_number').prop('disabled', true).empty().append('<option value="">' + MESSAGES.placeholders.selectRoom + '</option>');
    Utils.reloadDataTable('#reservations-table');
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  init: function() {
    this.populateBuildingSelect();
    this.populateAcademicTermsSelect();
    this.disableApartmentAndRoom();
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    $('#search_building_id').on('change', function(e) {
      var buildingId = $(e.target).val();
      if (buildingId) {
        self.populateApartmentSelect(buildingId);
        $('#search_apartment_number').prop('disabled', false);
      } else {
        self.clearSelect('#search_apartment_number', MESSAGES.placeholders.selectApartment);
        $('#search_apartment_number').prop('disabled', true);
      }
      self.clearRoomSelect();
      $('#search_room_number').prop('disabled', true);
    });
    $('#search_apartment_number').on('change', function(e) {
      var apartmentNumber = $(e.target).val();
      if (apartmentNumber) {
        self.populateRoomSelect(apartmentNumber);
        $('#search_room_number').prop('disabled', false);
      } else {
        self.clearRoomSelect();
        $('#search_room_number').prop('disabled', true);
      }
    });
  },
  disableApartmentAndRoom: function() {
    $('#search_apartment_number').prop('disabled', true);
    $('#search_room_number').prop('disabled', true);
  },
  populateBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_building_id', response.data, 'number', MESSAGES.placeholders.selectBuilding);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
      });
  },
  populateApartmentSelect: function(buildingId) {
    if (!buildingId) {
      this.clearSelect('#search_apartment_number', MESSAGES.placeholders.selectApartment);
      $('#search_apartment_number').prop('disabled', true);
      return;
    }
    ApiService.fetchApartments(buildingId)
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_apartment_number', response.data, 'number', MESSAGES.placeholders.selectApartment);
        } else {
          SelectManager.clearSelect('#search_apartment_number', MESSAGES.placeholders.selectApartment);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
        SelectManager.clearSelect('#search_apartment_number', MESSAGES.placeholders.selectApartment);
      });
  },
  populateRoomSelect: function(apartmentNumber) {
    if (!apartmentNumber) {
      this.clearRoomSelect();
      $('#search_room_number').prop('disabled', true);
      return;
    }
    ApiService.fetchRooms(apartmentNumber)
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_room_number', response.data, 'number', MESSAGES.placeholders.selectRoom);
        } else {
          SelectManager.clearSelect('#search_room_number', MESSAGES.placeholders.selectRoom);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
        SelectManager.clearSelect('#search_room_number', MESSAGES.placeholders.selectRoom);
      });
  },
  populateAcademicTermsSelect: function() {
    ApiService.fetchAcademicTerms()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_academic_term_id', response.data, 'name', MESSAGES.placeholders.allTerms);
        } else {
          SelectManager.clearSelect('#search_academic_term_id', MESSAGES.placeholders.allTerms);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,'An error occurred')
        SelectManager.clearSelect('#search_academic_term_id', MESSAGES.placeholders.allTerms);
      });
  },
  populateSelect: function(selector, data, valueField, placeholder) {
    var $select = $(selector);
    $select.empty().append('<option value="">' + placeholder + '</option>');
    data.forEach(function(item) {
      $select.append('<option value="' + item.id + '">' + item[valueField] + '</option>');
    });
  },
  clearSelect: function(selector, placeholder) {
    $(selector).empty().append('<option value="">' + placeholder + '</option>');
  },
  clearRoomSelect: function() {
    this.clearSelect('#search_room_number', MESSAGES.placeholders.selectRoom);
  },
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