@extends('layouts.home')

@section('title', __('Reservations'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
      <x-ui.card.stat2 color="secondary" icon="bx bx-calendar" :label="__('Total Reservations')" id="reservations"
        :subStats="[
          'male' => [
            'label' => __('Male Reservations'),
            'icon' => 'bx bx-male-sign',
            'color' => 'info'
          ],
          'female' => [
            'label' => __('Female Reservations'),
            'icon' => 'bx bx-female-sign',
            'color' => 'danger'
          ]
        ]"
      />
        </div>
    <div class="col-sm-6 col-xl-3">
      <x-ui.card.stat2 color="warning" icon="bx bx-time" :label="__('Pending Reservations')"
        id="pending"
        :subStats="[
          'male' => [
            'label' => __('Male Pending Reservations'),
            'icon' => 'bx bx-male-sign',
            'color' => 'info'
          ],
          'female' => [
            'label' => __('Female Pending Reservations'),
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
        :label="__('Confirmed Reservations')"
        id="confirmed"
        :subStats="[
          'male' => [
            'label' => __('Male Confirmed Reservations'),
            'icon' => 'bx bx-male-sign',
            'color' => 'info'
          ],
          'female' => [
            'label' => __('Female Confirmed Reservations'),
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
        :label="__('Checked In Reservations')"
        id="checked_in"
        :subStats="[
          'male' => [
            'label' => __('Male Checked In Reservations'),
            'icon' => 'bx bx-male-sign',
            'color' => 'info'
          ],
          'female' => [
            'label' => __('Female Checked In Reservations'),
            'icon' => 'bx bx-female-sign',
            'color' => 'danger'
          ]
        ]"
      />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
  :title="__('Reservations')"
  :description="__('Manage and view all reservations.')"
        icon="bx bx-calendar"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
    <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationSearchCollapse" aria-expanded="false" aria-controls="reservationSearchCollapse">
  <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
    </button>
    </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
  :title="__('Advanced Search')" 
        formId="advancedReservationSearch" 
        collapseId="reservationSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_national_id" class="form-label">{{ __('National ID') }}:</label>
            <input type="text" class="form-control" id="search_national_id" name="search_national_id">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('Status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="" disabled selected>{{ __('Select Status') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="confirmed">{{ __('Confirmed') }}</option>
                <option value="checked_in">{{ __('Checked In') }}</option>
                <option value="checked_out">{{ __('Checked Out') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('Active') }}:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="" disabled selected>{{ __('Select Status') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term" class="form-label">{{ __('Academic Term') }}:</label>
            <select class="form-control" id="search_academic_term" name="search_academic_term">
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_building" class="form-label">{{ __('Building') }}:</label>
            <select class="form-control" id="search_building" name="search_building">
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment" class="form-label">{{ __('Apartment') }}:</label>
            <select class="form-control" id="search_apartment" name="search_apartment" disabled>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_room" class="form-label">{{ __('Room') }}:</label>
            <select class="form-control" id="search_room" name="search_room" disabled>
            </select>
        </div>
        <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationFiltersBtn" type="button">
      <i class="bx bx-x"></i> {{ __('Clear Filters') }}
    </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
    :headers="[
      __('Reservation Number'),
      __('User'),
      __('Location'),
      __('Period'),
      __('Status'),
      __('Created'),
      __('Actions')
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
            'search_academic_term',
            'search_building',
            'search_apartment',
            'search_room',
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
var TRANSLATIONS = {
  confirm: {
    delete: {
      title: @json(__('Delete Reservation')),
      text: @json(__('Are you sure you want to delete this reservation?')),
      confirmButtonText: @json(__('yes, delete it'))
    },
    cancel: {
      title: @json(__('Cancel Reservation')),
      text: @json(__('Are you sure you want to cancel this reservation?')),
      confirmButtonText: @json(__('yes, cancel it'))
    }
  },
  placeholders: {
    selectBuilding: @json(__('Select Building')),
    selectApartment: @json(__('Select Apartment')),
    selectRoom: @json(__('Select Room')),
    selectTerm: @json(__('Select Term')),
    selectStatus: @json(__('Select Status')),
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
    cancel: '{{ route("reservations.cancel", ":id") }}',
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
    Utils.showConfirmDialog(
      {
        title: TRANSLATIONS.confirm.delete.title,
        text: TRANSLATIONS.confirm.delete.text,
        icon: 'warning',
        confirmButtonText: TRANSLATIONS.confirm.delete.confirmButtonText,
      }
    )
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
          Utils.showSuccess(response.message);
          StatsManager.load();
        } else {
          Utils.showError(response.message);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,xhr.responseJSON?.message)
      });
  },
  // --- Cancel Reservation Handlers ---
  handleCancelReservation: function(e) {
    var reservationId = Utils.getElementData(e.currentTarget, ['id']);
    this.confirmAndCancelReservation(reservationId);
  },
  confirmAndCancelReservation: function(reservationId) {
    Utils.showConfirmDialog(
      {
        title: TRANSLATIONS.confirm.cancel.title,
        text: TRANSLATIONS.confirm.cancel.text,
        icon: 'warning',
        confirmButtonText: TRANSLATIONS.confirm.cancel.confirmButtonText
      }
    )
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
          Utils.showSuccess(response.message);
          StatsManager.refresh();
        } else {
          Utils.showError(response.message);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr,xhr.responseJSON?.message)
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
      '#search_academic_term',
      '#search_building',
      '#search_apartment',
      '#search_room',
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
      '#search_academic_term',
      '#search_building',
      '#search_apartment',
      '#search_room',
      '#search_period_type'
    ].join(', ');
    $(filterSelectors).val('');
    // Reset apartment and room selects to disabled
    $('#search_apartment').prop('disabled', true).empty().append('<option value="">' + TRANSLATIONS.placeholders.selectApartment + '</option>');
    $('#search_room').prop('disabled', true).empty().append('<option value="">' + TRANSLATIONS.placeholders.selectRoom + '</option>');
    Utils.reloadDataTable('#reservations-table');
    Select2Manager.resetSearchSelect2();
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
    $('#search_building').on('change', function(e) {
      var buildingId = $(e.target).val();
      if (buildingId) {
        self.populateApartmentSelect(buildingId);
        $('#search_apartment').prop('disabled', false);
      } else {
        self.clearSelect('#search_apartment', TRANSLATIONS.placeholders.selectApartment);
        $('#search_apartment').prop('disabled', true);
      }
      self.clearRoomSelect();
      $('#search_room').prop('disabled', true);
    });
    $('#search_apartment').on('change', function(e) {
      var apartmentNumber = $(e.target).val();
      if (apartmentNumber) {
        self.populateRoomSelect(apartmentNumber);
        $('#search_room').prop('disabled', false);
      } else {
        self.clearRoomSelect();
        $('#search_room').prop('disabled', true);
      }
    });
  },
  disableApartmentAndRoom: function() {
    $('#search_apartment').prop('disabled', true);
    $('#search_room').prop('disabled', true);
  },
  populateBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#search_building', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectBuilding
          }, true);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  populateApartmentSelect: function(buildingId) {
    if (!buildingId) {
      this.clearSelect('#search_apartment', TRANSLATIONS.placeholders.selectApartment);
      $('#search_apartment').prop('disabled', true);
      return;
    }
    ApiService.fetchApartments(buildingId)
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#search_apartment', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectApartment
          }, true);
        } else {
          SelectManager.clearSelect('#search_apartment', TRANSLATIONS.placeholders.selectApartment);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        SelectManager.clearSelect('#search_apartment', TRANSLATIONS.placeholders.selectApartment);
      });
  },
  populateRoomSelect: function(apartmentNumber) {
    if (!apartmentNumber) {
      this.clearRoomSelect();
      $('#search_room').prop('disabled', true);
      return;
    }
    ApiService.fetchRooms(apartmentNumber)
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#search_room', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectRoom
          }, true);
        } else {
          SelectManager.clearSelect('#search_room', TRANSLATIONS.placeholders.selectRoom);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        SelectManager.clearSelect('#search_room', TRANSLATIONS.placeholders.selectRoom);
      });
  },
  populateAcademicTermsSelect: function() {
    ApiService.fetchAcademicTerms()
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#search_academic_term', response.data, {
            valueField: 'id',
            textField: 'name',
            placeholder: TRANSLATIONS.placeholders.selectTerm
          }, true);
        } else {
          Select2Manager.resetSearchSelect2('#search_academic_term');
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        SelectManager.clearSelect('#search_academic_term', TRANSLATIONS.placeholders.selectTerm);
      });
  },
  clearSelect: function(selector, placeholder) {
    $(selector).empty().append('<option value="">' + placeholder + '</option>');
  },
  clearRoomSelect: function() {
    this.clearSelect('#search_room', TRANSLATIONS.placeholders.selectRoom);
  }
};

// ===========================
// SELECT2 MANAGER
// ===========================
var Select2Manager = {
    /**
     * Configuration for all Select2 elements
     */
    config: {
        search: {
            '#search_status': { placeholder: TRANSLATIONS.placeholders.selectStatus },
            '#search_active': { placeholder: TRANSLATIONS.placeholders.selectStatus },
            '#search_academic_term': { placeholder: TRANSLATIONS.placeholders.selectTerm },
            '#search_building': { placeholder: TRANSLATIONS.placeholders.selectBuilding },
            '#search_apartment': { placeholder: TRANSLATIONS.placeholders.selectApartment },
            '#search_room': { placeholder: TRANSLATIONS.placeholders.selectRoom },
            '#search_period_type': { placeholder: TRANSLATIONS.placeholders.selectPeriodType }
        },
        modal: {
            // Add modal select2 configs here if needed
        }
    },

    /**
     * Initialize all search Select2 elements
     */
    initSearchSelect2: function() {
        Object.keys(this.config.search).forEach(function(selector) {
            Utils.initSelect2(selector, Select2Manager.config.search[selector]);
        });
    },

    /**
     * Initialize all modal Select2 elements
     */
    initModalSelect2: function() {
        Object.keys(this.config.modal).forEach(function(selector) {
            Utils.initSelect2(selector, Select2Manager.config.modal[selector]);
        });
    },

    /**
     * Initialize all Select2 elements
     */
    initAll: function() {
        this.initSearchSelect2();
        this.initModalSelect2();
    },

    /**
     * Clear specific Select2 elements
     * @param {Array} selectors - Array of selectors to clear
     */
    clearSelect2: function(selectors) {
        selectors.forEach(function(selector) {
            $(selector).val('').trigger('change.select2');
        });
    },

    /**
     * Reset modal Select2 elements
     */
    resetModalSelect2: function() {
        this.clearSelect2(Object.keys(this.config.modal));
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function(selector) {
        if (selector == null || selector === undefined) {
            this.clearSelect2(Object.keys(this.config.search));
        } else {
            this.clearSelect2([selector]);
        }
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
    Select2Manager.initAll();
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