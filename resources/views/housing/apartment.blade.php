@extends('layouts.home')

@section('title', __('housing.apartments.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-building" :label="__('housing.apartments.total_apartments')" id="apartments" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('housing.apartments.male_apartments')" id="apartments-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="danger" icon="bx bx-female" :label="__('housing.apartments.female_apartments')" id="apartments-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('housing.apartments.page_header')"
        :description="__('housing.apartments.page_description')"
        icon="bx bx-building"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#apartmentSearchCollapse" aria-expanded="false" aria-controls="apartmentSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('housing.common.search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedApartmentSearch" 
        collapseId="apartmentSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">Building Number:</label>
            <select class="form-control" id="search_building_id">
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_id" class="form-label">Apartment Number:</label>
            <select class="form-control" id="search_apartment_id" disabled>
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_gender_restriction" class="form-label">Gender Restriction:</label>
            <select class="form-control" id="search_gender_restriction">
                <option value="">All</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="mixed">Mixed</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">Active Status:</label>
            <select class="form-control" id="search_active">
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearApartmentFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Number', 'Building', 'Total Rooms', 'Gender', 'Active', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'building_number', 'name' => 'building_number'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'building_gender_restriction', 'name' => 'building_gender_restriction'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.apartments.datatable')"
        :table-id="'apartments-table'"
        :filter-fields="['search_apartment_id','search_building_id','search_gender_restriction','search_active']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Apartment Modal --}}
    <x-ui.modal 
        id="viewApartmentModal"
        title="Apartment Details"
        size="md"
        :scrollable="false"
        class="view-apartment-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Number:</label>
                    <p id="view-apartment-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Building:</label>
                    <p id="view-apartment-building" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Total Rooms:</label>
                    <p id="view-apartment-total-rooms" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Gender Restriction:</label>
                    <p id="view-apartment-gender-restriction" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Active:</label>
                    <p id="view-apartment-is-active" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-apartment-created" class="mb-0"></p>
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

const MESSAGES = {
  confirm: {
    activate: {
      title: 'Activate Apartment?',
      text: 'Are you sure you want to activate this apartment?',
      button: 'Yes, activate it!'
    },
    deactivate: {
      title: 'Deactivate Apartment?',
      text: 'Are you sure you want to deactivate this apartment?',
      button: 'Yes, deactivate it!'
    },
    delete: {
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      button: 'Yes, delete it!'
    }
  },
  success: {
    activated: 'Apartment activated successfully',
    deactivated: 'Apartment deactivated successfully',
    deleted: 'Apartment has been deleted.'
  },
  error: {
    loadStats: 'Failed to load apartment statistics',
    loadApartment: 'Failed to load apartment data',
    deleteApartment: 'Failed to delete apartment.',
    operationFailed: 'Operation failed'
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
  onError: MESSAGES.error.loadStats
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
        Utils.showError(MESSAGES.error.loadApartment);
      });
  },
  /**
   * Handle delete apartment button click
   */
  handleDeleteApartment: function(e) {
    var apartmentId = $(e.currentTarget).data('id');
    Utils.showConfirmDialog(
      {
        title: MESSAGES.confirm.delete.title,
        text: MESSAGES.confirm.delete.text,
        confirmButtonText: MESSAGES.confirm.delete.button,
        cancelButtonText: 'Cancel',
        showCancelButton: true,
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
    var confirmOptions = isActivate ? MESSAGES.confirm.activate : MESSAGES.confirm.deactivate;
    var apiCall = isActivate ? ApiService.activateApartment : ApiService.deactivateApartment;
    var successMessage = isActivate ? MESSAGES.success.activated : MESSAGES.success.deactivated;

    Utils.showConfirmDialog({
      title: confirmOptions.title,
      text: confirmOptions.text,
      confirmButtonText: confirmOptions.button,
      cancelButtonText: 'Cancel',
      showCancelButton: true,
    }).then(function(result) {
      if (!result.isConfirmed) return;

      Utils.setLoadingState($btn, true);

      apiCall(id)
        .done(function(response) {
          if (response.success) {
            Utils.showSuccess(successMessage);
            Utils.reloadDataTable('#apartments-table');
          } else {
            Utils.showError(response.message || MESSAGES.error.operationFailed);
          }
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, MESSAGES.error.operationFailed);
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
      .done(function() {
        Utils.reloadDataTable('#apartments-table');
        Utils.showSuccess(MESSAGES.success.deleted);
        StatsManager.load();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, MESSAGES.error.deleteApartment);
      });
  },
  /**
   * Populate the view modal with apartment data
   */
  populateViewModal: function(apartment) {
    Utils.setElementText('#view-apartment-number', apartment.number);
    Utils.setElementText('#view-apartment-building', apartment.building);
    Utils.setElementText('#view-apartment-total-rooms', apartment.total_rooms);
    Utils.setElementText('#view-apartment-gender-restriction', apartment.gender_restriction);
    Utils.setElementText('#view-apartment-is-active', apartment.active ? 'Active' : 'Inactive');
    Utils.setElementText('#view-apartment-created', Utils.formatDate ? Utils.formatDate(apartment.created_at) : (new Date(apartment.created_at).toLocaleString()));
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
    ['#search_apartment_id', '#search_building_id', '#search_gender_restriction', '#search_active'].forEach(function(selector) {
      $(selector).val('').trigger('change');
    });
    // Reset apartment select to disabled
    $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">Select Building First</option>');
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
    $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">Select Building First</option>');
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
            placeholder: 'Select Building',
            includePlaceholder: true
          });
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
        $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">Select Building First</option>');
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
            placeholder: 'Select Apartment',
            includePlaceholder: true
          });
          $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">No Apartments</option>');
        } else {
          Utils.disable('#search_apartment_id', true);
          $('#search_apartment_id').empty().append('<option value="">No Apartments</option>');
        }
      })
      .fail(function(xhr) {
        Utils.disable('#search_apartment_id', true);
        Utils.handleAjaxError(xhr,"An Error Occured");
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
