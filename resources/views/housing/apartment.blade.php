@extends('layouts.home')

@section('title', __('Apartment Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-building" :label="__('Total Apartments')" id="apartments" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('Male Apartments')" id="apartments-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="danger" icon="bx bx-female" :label="__('Female Apartments')" id="apartments-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header :title="__('Apartments')" :description="__('Manage all campus apartments and their details')" icon="bx bx-building">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#apartmentSearchCollapse" aria-expanded="false" aria-controls="apartmentSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search :title="__('Advanced Apartment Search')" formId="advancedApartmentSearch" collapseId="apartmentSearchCollapse" :collapsed="false">
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">{{ __('Building Number') }}:</label>
            <select class="form-control" id="search_building_id">
                <option value="">{{ __('All') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_id" class="form-label">{{ __('Apartment Number') }}:</label>
            <select class="form-control" id="search_apartment_id" disabled>
                <option value="">{{ __('All') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_gender_restriction" class="form-label">{{ __('Gender Restriction') }}:</label>
            <select class="form-control" id="search_gender_restriction">
                <option value="">{{ __('All') }}</option>
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('Active Status') }}:</label>
            <select class="form-control" id="search_active">
                <option value="">{{ __('All') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearApartmentFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers=" [__('Name'),__('Building'),__('Total Rooms'),__('Gender'),__('Status'),__('Current Occupancy'),__('Created At'),__('Actions')]"
        :columns=" [
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'building', 'name' => 'building'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'current_occupancy', 'name' => 'current_occupancy', 'orderable' => false, 'searchable' => false],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.apartments.datatable')"
        table-id='apartments-table'
        :filter-fields="['search_apartment_id','search_building_id','search_gender_restriction','search_active']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Apartment Modal --}}
    <x-ui.modal 
        id="viewApartmentModal"
        :title="__('View Apartment Details')"
        size="md"
        :scrollable="false"
        class="view-apartment-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Number') }}:</label>
                    <p id="view-apartment-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Building') }}:</label>
                    <p id="view-apartment-building" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Total Rooms') }}:</label>
                    <p id="view-apartment-total-rooms" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Gender Restriction') }}:</label>
                    <p id="view-apartment-gender-restriction" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Current Occupancy') }}:</label>
                    <p id="view-apartment-current-occupancy" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Active') }}:</label>
                    <p id="view-apartment-is-active" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection

@push('scripts')
<script>
/**
 * Apartment Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ApartmentManager: Handles CRUD and actions for apartments
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ApartmentApp: Initializes all managers
 *  NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  apartments: {
    stats: '{{ route('housing.apartments.stats') }}',
    show: '{{ route('housing.apartments.show', ':id') }}',
    destroy: '{{ route('housing.apartments.destroy', ':id') }}',
    datatable: '{{ route('housing.apartments.datatable') }}',
    all: '{{ route('housing.apartments.all', ':id') }}',
    update: '{{ route('housing.apartments.update', ':id') }}',
    activate: '{{ route('housing.apartments.activate', ':id') }}',
    deactivate: '{{ route('housing.apartments.deactivate', ':id') }}',
  },
  buildings: {
    all: '{{ route('housing.buildings.all') }}'
  }
};

// ===========================
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
  confirm: {
    activate: {
      title:  @json(__('Activate Apartment')),
      text: @json(__('Are you sure you want to activate this apartment?')),
      button: @json(__('Yes,Activate'))
    },
    deactivate: {
      title: @json(__('Deactivate Apartment')),
      text: @json(__('Are you sure you want to deactivate this apartment?')),
      button: @json(__('Yes,Deactivate'))
    },
    delete: {
      title: @json(__('Delete Apartment')),
      text: @json(__('Are you sure you want to delete this apartment? This action cannot be undone.')),
      button: @json(__('Yes,Delete'))
    }
  },
  success: {
    activated: @json(__('Apartment has been successfully activated.')),
    deactivated: @json(__('Apartment has been successfully deactivated.')),
    deleted: @json(__('Apartment has been successfully deleted.'))
  },
  error: {
    loadStats: @json(__('Failed to load apartment statistics.')),
    loadApartment: @json(__('Failed to load apartment details.')),
    deleteApartment: @json(__('Failed to delete apartment.')),
    operationFailed: @json(__('Operation failed. Please try again.'))
  },
  placeholders: {
    selectBuilding: @json(__('Select Building')),
    selectApartment: @json(__('Select Apartment')),
    selectBuildingFirst: @json(__('Select a building first')),
    noApartments: @json(__('No apartments available')),
    selectGender: @json(__('Select Gender')),
    selectStatus: @json(__('Select Status'))
  },
  status: {
    active: @json(__('Active')),
    inactive: @json(__('Inactive'))
  }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  /**
   * Generic AJAX request
   * @param {object} options
   * @returns {jqXHR}
   */
  request: function(options) {
    return $.ajax(options);
  },
  /**
   * Fetch apartment statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.apartments.stats, method: 'GET' });
  },
  /**
   * Fetch a single apartment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchApartment: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.show, id), method: 'GET' });
  },
  /**
   * Save (update) an apartment
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveApartment: function(data, id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.update, id), method: 'PUT', data: data });
  },
  /**
   * Delete an apartment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteApartment: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.destroy, id), method: 'DELETE' });
  },
  /**
   * Activate an apartment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  activateApartment: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.activate, id), method: 'PATCH' });
  },
  /**
   * Deactivate an apartment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deactivateApartment: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.deactivate, id), method: 'PATCH' });
  },
  /**
   * Fetch all buildings
   * @returns {jqXHR}
   */
  fetchBuildings: function() {
    return ApiService.request({ url: ROUTES.buildings.all, method: 'GET' });
  },
  /**
   * Fetch all apartments
   * @returns {jqXHR}
   */
  fetchApartments: function(buildingId) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.all, buildingId), method: 'GET' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
const StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: ['apartments', 'apartments-male', 'apartments-female'],
  onError: TRANSLATION.error.loadStats
});


// ===========================
// APARTMENT MANAGER
// ===========================
var ApartmentManager = {
  /**
   * Initialize apartment manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind all apartment-related events
   */
  bindEvents: function() {
    var self = this;
    $(document).on('click', '.viewApartmentBtn', function(e) { self.handleViewApartment(e); });
    $(document).on('click', '.deleteApartmentBtn', function(e) { self.handleDeleteApartment(e); });
    $(document).on('click', '.activateApartmentBtn', function(e) { self.handleActivateApartment(e); });
    $(document).on('click', '.deactivateApartmentBtn', function(e) { self.handleDeactivateApartment(e); });
  },
  /**
   * Handle view apartment button click
   */
  handleViewApartment: function(e) {
    var apartmentId = $(e.currentTarget).data('id');
    ApiService.fetchApartment(apartmentId)
      .done(function(response) {
        if (response.success) {
          ApartmentManager.populateViewModal(response.data);
          $('#viewApartmentModal').modal('show');
        }
      })
      .fail(function() {
        $('#viewApartmentModal').modal('hide');
        Utils.showError(TRANSLATION.error.loadApartment);
      });
  },
  /**
   * Handle delete apartment button click
   */
  handleDeleteApartment: function(e) {
    var apartmentId = $(e.currentTarget).data('id');
    Utils.showConfirmDialog(
      {
        title: TRANSLATION.confirm.delete.title,
        text: TRANSLATION.confirm.delete.text,
        confirmButtonText: TRANSLATION.confirm.delete.button,        
      }
    )
      .then(function(result) {
        if (result.isConfirmed) {
          ApartmentManager.deleteApartment(apartmentId);
        }
      });
  },
  /**
   * Handle activate apartment button click
   */
  handleActivateApartment: function(e) {
    e.preventDefault();
    var $btn = $(e.currentTarget);
    var id = $btn.data('id');
    ApartmentManager.toggleApartmentStatus(id, true, $btn);
  },
  /**
   * Handle deactivate apartment button click
   */
  handleDeactivateApartment: function(e) {
    e.preventDefault();
    var $btn = $(e.currentTarget);
    var id = $btn.data('id');
    ApartmentManager.toggleApartmentStatus(id, false, $btn);
  },
  /**
   * Toggle apartment status (activate/deactivate)
   */
  toggleApartmentStatus: function(id, isActivate, $btn) {
    var confirmOptions = isActivate ? TRANSLATION.confirm.activate : TRANSLATION.confirm.deactivate;
    var apiCall = isActivate ? ApiService.activateApartment : ApiService.deactivateApartment;
    var successMessage = isActivate ? TRANSLATION.success.activated : TRANSLATION.success.deactivated;

    Utils.showConfirmDialog({
      title: confirmOptions.title,
      text: confirmOptions.text,
      confirmButtonText: confirmOptions.button,
      
      
    }).then(function(result) {
      if (!result.isConfirmed) return;

      Utils.setLoadingState($btn, true);

      apiCall(id)
        .done(function(response) {
          if (response.success) {
            Utils.showSuccess(successMessage);
            Utils.reloadDataTable('#apartments-table');
          } else {
            Utils.showError(response.message || TRANSLATION.error.operationFailed);
          }
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, TRANSLATION.error.operationFailed);
        })
        .always(function() {
          Utils.setLoadingState($btn, false);
        });
    });
  },

  /**
   * Delete an apartment
   */
  deleteApartment: function(apartmentId) {
    ApiService.deleteApartment(apartmentId)
        .done(function(response) {
        Utils.reloadDataTable('#apartments-table');
        Utils.showSuccess(response.message || TRANSLATION.success.deleted);
        StatsManager.load();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.error.deleteApartment);
      });
  },
  /**
   * Populate the view modal with apartment data
   */
  populateViewModal: function(apartment) {
    Utils.setElementText('#view-apartment-number', apartment.name);
    Utils.setElementText('#view-apartment-building', apartment.building);
    Utils.setElementText('#view-apartment-total-rooms', apartment.roomsCount);
    Utils.setElementText('#view-apartment-gender-restriction', apartment.gender);
    Utils.setElementText('#view-apartment-current-occupancy', apartment.currentOccupancy);
    Utils.setElementText('#view-apartment-is-active', apartment.active);
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
            '#search_building_id': { placeholder: TRANSLATION.placeholders.selectBuilding },
            '#search_apartment_id': { placeholder: TRANSLATION.placeholders.selectApartment },
            '#search_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGender },
            '#search_active': { placeholder: TRANSLATION.placeholders.selectStatus }
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
     * Initialize all Select2 elements
     */
    initAll: function() {
        this.initSearchSelect2();
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
     * Reset search Select2 elements
     */
    resetSearchSelect2: function() {
        this.clearSelect2(['#search_building_id', '#search_apartment_id', '#search_gender_restriction', '#search_active']);
    }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
  /**
   * Initialize search manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind search and clear events
   */
  bindEvents: function() {
    var self = this;
    $('#search_building_id, #search_gender_restriction, #search_active').on('change', function() { self.handleFilterChange(); });
    $('#search_apartment_id').on('change', function() { self.handleFilterChange(); });
    $('#clearApartmentFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Handle filter change
   */
  handleFilterChange: function() {
    Utils.reloadDataTable('#apartments-table');
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    Select2Manager.resetSearchSelect2();
    $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">' + TRANSLATION.placeholders.selectBuildingFirst + '</option>');
    Utils.reloadDataTable('#apartments-table');
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  /**
   * Initialize select manager
   */
  init: function() {
    this.populateBuildingSelect();
    this.bindBuildingChange();
    $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">' + TRANSLATION.placeholders.selectBuildingFirst + '</option>');
  },
  /**
   * Populate building select dropdown
   */
  populateBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect('#search_building_id', response.data,{
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATION.placeholders.selectBuilding,
            includePlaceholder: true
          });
          Select2Manager.initSearchSelect2();
        }
      })
      .fail(function() {
        console.error('Failed to load buildings');
      });
  },
  /**
   * Bind change event for building select to populate apartments
   */
  bindBuildingChange: function() {
    var self = this;
    $('#search_building_id').on('change', function() {
      var buildingId = $(this).val();
      if (buildingId) {
        self.populateApartmentSelect(buildingId);
      } else {
        $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">' + TRANSLATION.placeholders.selectBuildingFirst + '</option>');
      }
    });
  },
  /**
   * Populate apartment select dropdown based on building
   */
  populateApartmentSelect: function(buildingId) {
    // Fetch all apartments and filter client-side by building_id
    ApiService.fetchApartments(buildingId)
      .done(function(response) {
        if (response.success && Array.isArray(response.data) && response.data.length > 0) {
          Utils.populateSelect('#search_apartment_id', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATION.placeholders.selectApartment,
            includePlaceholder: true
          });
          $('#search_apartment_id').prop('disabled', false);
          Select2Manager.initSearchSelect2();
        } else {
          $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">' + TRANSLATION.placeholders.noApartments + '</option>');
        }
      })
      .fail(function(xhr) {
        $('#search_apartment_id').prop('disabled', true);
        Utils.handleAjaxError(xhr, TRANSLATION.error.operationFailed);
      });
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var ApartmentApp = {
  /**
   * Initialize all managers
   */
  init: function() {
    StatsManager.init();
    ApartmentManager.init();
    SearchManager.init();
    SelectManager.init();
    Select2Manager.initAll();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  ApartmentApp.init();
});
</script>
@endpush
