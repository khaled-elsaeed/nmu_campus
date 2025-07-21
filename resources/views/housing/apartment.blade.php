@extends('layouts.home')

@section('title', 'Apartment Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-building" label="Total Apartments" id="apartments" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" label="Male Apartments" id="apartments-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" label="Female Apartments" id="apartments-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Apartments"
        description="Manage apartments and their associated rooms."
        icon="bx bx-building"
    >
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#apartmentSearchCollapse" aria-expanded="false" aria-controls="apartmentSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedApartmentSearch" 
        collapseId="apartmentSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_apartment_number" class="form-label">Apartment Number:</label>
            <select class="form-control" id="search_apartment_number">
                <option value="">All</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">Building Number:</label>
            <select class="form-control" id="search_building_id">
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
        :filter-fields="['search_apartment_number','search_building_id','search_gender_restriction','search_active']"
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
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ApartmentManager: Handles CRUD and actions for apartments
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ApartmentApp: Initializes all managers
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
    all: '{{ route('housing.apartments.all') }}',
    update: '{{ route('housing.apartments.update', ':id') }}',
    activate: '{{ route('housing.apartments.activate', ':id') }}',
    deactivate: '{{ route('housing.apartments.deactivate', ':id') }}'
  },
  buildings: {
    all: '{{ route('housing.buildings.all') }}'
  }
};

var MESSAGES = {
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
// UTILITY FUNCTIONS
// ===========================
var Utils = {
  /**
   * Show an error alert
   * @param {string} message
   */
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  /**
   * Show a success toast message
   * @param {string} message
   */
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  /**
   * Show a confirmation dialog
   * @param {object} options
   * @returns {Promise}
   */
  showConfirm: function(options) {
    return Swal.fire({
      title: options.title,
      text: options.text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: options.button
    });
  },
  /**
   * Toggle loading state for a stat card
   * @param {string} elementId
   * @param {boolean} isLoading
   */
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
  /**
   * Replace :id in a route string
   * @param {string} route
   * @param {string|number} id
   * @returns {string}
   */
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
  },
  /**
   * Set text for an element
   * @param {string} selector
   * @param {string} text
   */
  setElementText: function(selector, text) {
    $(selector).text(text || '--');
  },
  /**
   * Format a date string
   * @param {string} dateString
   * @returns {string}
   */
  formatDate: function(dateString) {
    return new Date(dateString).toLocaleString();
  },
  /**
   * Enable or disable a button
   * @param {object} $button
   * @param {boolean} disabled
   */
  disableButton: function($button, disabled) {
    $button.prop('disabled', disabled === undefined ? true : disabled);
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
  fetchApartments: function() {
    return ApiService.request({ url: ROUTES.apartments.all, method: 'GET' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = {
  /**
   * Initialize statistics cards
   */
  init: function() {
    this.load();
  },
  /**
   * Load statistics data
   */
  load: function() {
    this.toggleAllLoadingStates(true);
    ApiService.fetchStats()
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
  /**
   * Handle successful stats fetch
   * @param {object} response
   */
  handleSuccess: function(response) {
    if (response.success) {
      let stats = response.data;
      this.updateStatElement('apartments', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('apartments-male', stats.male.count, stats.male.lastUpdateTime);
      this.updateStatElement('apartments-female', stats.female.count, stats.female.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError(MESSAGES.error.loadStats || 'Failed to load apartment statistics');
  },
  /**
   * Update a single stat card
   * @param {string} elementId
   * @param {string|number} value
   * @param {string} lastUpdateTime
   */
  updateStatElement: function(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  /**
   * Set all stat cards to N/A
   */
  setAllStatsToNA: function() {
    ['apartments', 'apartments-male', 'apartments-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['apartments', 'apartments-male', 'apartments-female'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

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
    Utils.showConfirm(MESSAGES.confirm.delete)
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
    Utils.showConfirm(confirmOptions)
      .then(function(result) {
        if (result.isConfirmed) {
          ApartmentManager.executeStatusToggle(id, apiCall, successMessage, $btn);
        }
      });
  },
  /**
   * Execute status toggle API call
   */
  executeStatusToggle: function(id, apiCall, successMessage, $btn) {
    Utils.disableButton($btn);
    apiCall(id)
      .done(function(response) {
        if (response.success) {
          Utils.showSuccess(successMessage);
          ApartmentManager.reloadTable();
        } else {
          Utils.showError(response.message || MESSAGES.error.operationFailed);
        }
      })
      .fail(function(xhr) {
        Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : MESSAGES.error.operationFailed);
      })
      .always(function() {
        Utils.disableButton($btn, false);
      });
  },
  /**
   * Delete an apartment
   */
  deleteApartment: function(apartmentId) {
    ApiService.deleteApartment(apartmentId)
      .done(function() {
        ApartmentManager.reloadTable();
        Utils.showSuccess(MESSAGES.success.deleted);
        StatsManager.load();
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : MESSAGES.error.deleteApartment;
        Utils.showError(message);
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
    Utils.setElementText('#view-apartment-created', Utils.formatDate(apartment.created_at));
  },
  /**
   * Reload the apartments table
   */
  reloadTable: function() {
    $('#apartments-table').DataTable().ajax.reload(null, false);
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
    $('#search_apartment_number, #search_building_id, #search_gender_restriction, #search_active').on('change', function() { self.handleFilterChange(); });
    $('#clearApartmentFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Handle filter change
   */
  handleFilterChange: function() {
    $('#apartments-table').DataTable().ajax.reload();
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    ['#search_apartment_number', '#search_building_id', '#search_gender_restriction', '#search_active'].forEach(function(selector) {
      $(selector).val('').trigger('change');
    });
    $('#apartments-table').DataTable().ajax.reload();
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
    this.populateApartmentSelect();
  },
  /**
   * Populate building select dropdown
   */
  populateBuildingSelect: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success) {
          SelectManager.populateSelect('#search_building_id', response.data, 'id', 'number');
        }
      })
      .fail(function() {
        console.error('Failed to load buildings');
      });
  },
  /**
   * Populate apartment select dropdown
   */
  populateApartmentSelect: function() {
    ApiService.fetchApartments()
      .done(function(response) {
        if (response.success) {
          SelectManager.populateApartmentNumbers(response.data);
        }
      })
      .fail(function() {
        console.error('Failed to load apartments');
      });
  },
  /**
   * Populate a select dropdown with data
   */
  populateSelect: function(selector, data, valueField, textField) {
    var $select = $(selector);
    $select.empty().append('<option value="">All</option>');
    data.forEach(function(item) {
      $select.append('<option value="' + item[valueField] + '">' + item[textField] + '</option>');
    });
  },
  /**
   * Populate apartment numbers dropdown
   */
  populateApartmentNumbers: function(apartments) {
    var $select = $('#search_apartment_number');
    $select.empty().append('<option value="">All</option>');
    var uniqueNumbers = new Set();
    apartments.forEach(function(apartment) {
      if (!uniqueNumbers.has(apartment.number)) {
        uniqueNumbers.add(apartment.number);
        $select.append('<option value="' + apartment.number + '">' + apartment.number + '</option>');
      }
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