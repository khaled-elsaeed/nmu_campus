@extends('layouts.home')

@section('title', 'Building Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-buildings" label="Total Buildings" id="buildings" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" label="Male Buildings" id="buildings-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" label="Female Buildings" id="buildings-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Buildings"
        description="Manage buildings and their associated apartments."
        icon="bx bx-buildings"
    >
        <button class="btn btn-primary mx-2" id="addBuildingBtn">
            <i class="bx bx-plus me-1"></i> Add Building
        </button>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#buildingSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedBuildingSearch" 
        collapseId="buildingSearchCollapse"
        :collapsed="false"
    >
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
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearBuildingFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Number', 'Total Apartments', 'Total Rooms', 'Has Double Room', 'Gender', 'Active', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'total_apartments', 'name' => 'total_apartments'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'has_double_rooms', 'name' => 'has_double_rooms'],
            ['data' => 'gender_restriction', 'name' => 'gender_restriction'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.buildings.datatable')"
        :table-id="'buildings-table'"
        :filter-fields="['search_gender_restriction','search_active']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Building Modal --}}
    <x-ui.modal 
        id="buildingModal"
        title="Add/Edit Building"
        :scrollable="true"
        class="building-modal"
    >
        <x-slot name="slot">
            <form id="buildingForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="building_number" class="form-label">Building Number</label>
                        <input type="number" id="building_number" name="number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <label for="building_total_apartments" class="form-label">Total Apartments</label>
                        <input type="number" id="building_total_apartments" name="total_apartments" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <label for="building_rooms_per_apartment" class="form-label">Rooms Per Apartment</label>
                        <input type="number" id="building_rooms_per_apartment" name="rooms_per_apartment" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_double_rooms" name="has_double_rooms">
                            <label class="form-check-label" for="has_double_rooms">
                                This building has double rooms
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 edit-hide" id="apartments-double-rooms-section" style="display: none;"></div>
                    <div class="col-md-12 mb-3">
                        <label for="building_gender_restriction" class="form-label">Gender Restriction</label>
                        <select id="building_gender_restriction" name="gender_restriction" class="form-control" required>
                            <option value="">Select Gender Restriction</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="building_active" class="form-label">Active</label>
                        <select id="building_active" name="active" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="buildingForm">Save</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Building Modal --}}
    <x-ui.modal 
        id="viewBuildingModal"
        title="Building Details"
        :scrollable="true"
        class="view-building-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Number:</label>
                    <p id="view-building-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Total Apartments:</label>
                    <p id="view-building-total-apartments" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Total Rooms:</label>
                    <p id="view-building-total-rooms" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Gender Restriction:</label>
                    <p id="view-building-gender-restriction" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Active:</label>
                    <p id="view-building-is-active" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-building-created" class="mb-0"></p>
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
 * Building Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - BuildingManager: Handles CRUD and actions for buildings
 * - BuildingApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  buildings: {
    stats: '{{ route('housing.buildings.stats') }}',
    show: '{{ route('housing.buildings.show', ':id') }}',
    store: '{{ route('housing.buildings.store') }}',
    update: '{{ route('housing.buildings.update', ':id') }}',
    destroy: '{{ route('housing.buildings.destroy', ':id') }}',
    activate: '{{ route('housing.buildings.activate', ':id') }}',
    deactivate: '{{ route('housing.buildings.deactivate', ':id') }}'
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
   * Show a confirmation dialog
   * @param {object} options
   * @returns {Promise}
   */
  showConfirmDialog: function(options) {
    return Swal.fire({
      title: options.title || 'Are you sure?',
      text: options.text || "You won't be able to revert this!",
      icon: options.icon || 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: options.confirmButtonText || 'Yes, proceed!'
    });
  },
  /**
   * Reset a form by ID
   * @param {string} formId
   */
  resetForm: function(formId) {
    $('#' + formId)[0].reset();
  },
  /**
   * Set form data by object
   * @param {string} formId
   * @param {object} data
   */
  setFormData: function(formId, data) {
    var form = $('#' + formId);
    Object.keys(data).forEach(function(key) {
      var element = form.find('[name="' + key + '"]');
      if (element.length) {
        if (element.attr('type') === 'checkbox') {
          element.prop('checked', data[key]);
        } else {
          element.val(data[key]);
        }
      }
    });
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
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    return $.ajax(options);
  },
  /**
   * Fetch building statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return this.request({ url: ROUTES.buildings.stats, method: 'GET' });
  },
  /**
   * Fetch a single building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchBuilding: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.buildings.show, id), method: 'GET' });
  },
  /**
   * Save (update) a building
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveBuilding: function(data, id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.buildings.update, id), method: 'PUT', data: data });
  },
  /**
   * Create a new building
   * @param {object} data
   * @returns {jqXHR}
   */
  createBuilding: function(data) {
    return this.request({ url: ROUTES.buildings.store, method: 'POST', data: data });
  },
  /**
   * Delete a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteBuilding: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.buildings.destroy, id), method: 'DELETE' });
  },
  /**
   * Activate a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  activateBuilding: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.buildings.activate, id), method: 'PATCH' });
  },
  /**
   * Deactivate a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deactivateBuilding: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.buildings.deactivate, id), method: 'PATCH' });
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
      this.updateStatElement('buildings', stats.total.total, stats.total.lastUpdateTime);
      this.updateStatElement('buildings-male', stats.male.total, stats.male.lastUpdateTime);
      this.updateStatElement('buildings-female', stats.female.total, stats.female.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load building statistics');
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
    ['buildings', 'buildings-male', 'buildings-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['buildings', 'buildings-male', 'buildings-female'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// BUILDING MANAGER
// ===========================
var BuildingManager = {
  currentBuildingId: null,
  /**
   * Initialize building manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind all building-related events
   */
  bindEvents: function() {
    this.handleAddBuilding();
    this.handleEditBuilding();
    this.handleViewBuilding();
    this.handleDeleteBuilding();
    this.handleFormSubmit();
    this.handleActivateDeactivate();
  },
  /**
   * Handle add building button click
   */
  handleAddBuilding: function() {
    var self = this;
    $(document).on('click', '#addBuildingBtn', function() {
      self.openModal('add');
    });
  },
  /**
   * Handle edit building button click
   */
  handleEditBuilding: function() {
    var self = this;
    $(document).on('click', '.editBuildingBtn', function(e) {
      var buildingId = $(e.currentTarget).data('id');
      self.openModal('edit', buildingId);
    });
  },
  /**
   * Handle view building button click
   */
  handleViewBuilding: function() {
    $(document).on('click', '.viewBuildingBtn', function(e) {
      var buildingId = $(e.currentTarget).data('id');
      BuildingManager.viewBuilding(buildingId);
    });
  },
  /**
   * Handle delete building button click
   */
  handleDeleteBuilding: function() {
    $(document).on('click', '.deleteBuildingBtn', function(e) {
      var buildingId = $(e.currentTarget).data('id');
      BuildingManager.deleteBuilding(buildingId);
    });
  },
  /**
   * Handle form submit
   */
  handleFormSubmit: function() {
    var self = this;
    $('#buildingForm').on('submit', function(e) {
      e.preventDefault();
      self.saveBuilding();
    });
  },
  /**
   * Handle activate/deactivate building
   */
  handleActivateDeactivate: function() {
    // Activate
    $(document).on('click', '.activateBuildingBtn', function(e) {
      e.preventDefault();
      var $btn = $(e.currentTarget);
      var id = $btn.data('id');
      Swal.fire({
        title: 'Activate Building?',
        text: 'Are you sure you want to activate this building? This will make it available for use.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, activate!',
        cancelButtonText: 'Cancel'
      }).then(function(result) {
        if (result.isConfirmed) {
          $btn.prop('disabled', true);
          ApiService.activateBuilding(id)
            .done(function(response) {
              Utils.showSuccess(response.message || 'Building activated successfully.');
              $('#buildings-table').DataTable().ajax.reload(null, false);
              StatsManager.load();
            })
            .fail(function(xhr) {
              Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to activate building.');
            })
            .always(function() {
              $btn.prop('disabled', false);
            });
        }
      });
    });
    // Deactivate
    $(document).on('click', '.deactivateBuildingBtn', function(e) {
      e.preventDefault();
      var $btn = $(e.currentTarget);
      var id = $btn.data('id');
      Swal.fire({
        title: 'Deactivate Building?',
        text: 'Are you sure you want to deactivate this building? This will make it unavailable for new reservations.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, deactivate!',
        cancelButtonText: 'Cancel'
      }).then(function(result) {
        if (result.isConfirmed) {
          $btn.prop('disabled', true);
          ApiService.deactivateBuilding(id)
            .done(function(response) {
              Utils.showSuccess(response.message || 'Building deactivated successfully.');
              $('#buildings-table').DataTable().ajax.reload(null, false);
              StatsManager.load();
            })
            .fail(function(xhr) {
              Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to deactivate building.');
            })
            .always(function() {
              $btn.prop('disabled', false);
            });
        }
      });
    });
  },
  /**
   * Open add/edit modal
   */
  openModal: function(mode, buildingId) {
    this.currentBuildingId = buildingId;
    this.resetModalState();
    if (mode === 'add') {
      this.setupAddModal();
    } else if (mode === 'edit') {
      this.setupEditModal(buildingId);
    }
  },
  /**
   * Reset modal state
   */
  resetModalState: function() {
    Utils.resetForm('buildingForm');
    $('#has_double_rooms').prop('checked', false);
  },
  /**
   * Setup add modal
   */
  setupAddModal: function() {
    $('#buildingModalTitle').text('Add Building');
    $('#buildingModal').modal('show');
  },
  /**
   * Setup edit modal
   */
  setupEditModal: function(buildingId) {
    $('#buildingModalTitle').text('Edit Building');
    ApiService.fetchBuilding(buildingId)
      .done(function(response) {
        if (response.success) {
          BuildingManager.populateEditForm(response.data);
          $('#buildingModal').modal('show');
        }
      })
      .fail(function() {
        $('#buildingModal').modal('hide');
        Utils.showError('Failed to load building data');
      });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(building) {
    $('#building_number').val(building.number).prop('disabled', false);
    $('#building_gender_restriction').val(building.gender_restriction).prop('disabled', false);
    $('#building_active').val(building.active).prop('disabled', false);
    $('.edit-hide').hide().find('input, select, textarea').prop('required', false).prop('disabled', true);
    $('#has_double_rooms').prop('checked', building.has_double_rooms).prop('disabled', false);
    if (building.has_double_rooms) {
      $('#apartments-double-rooms-section').show();
      DoubleRoomManager.renderDoubleRoomSelectors();
    } else {
      $('#apartments-double-rooms-section').hide().empty();
    }
  },
  /**
   * View building details
   */
  viewBuilding: function(buildingId) {
    ApiService.fetchBuilding(buildingId)
      .done(function(response) {
        if (response.success) {
          BuildingManager.populateViewModal(response.data);
          $('#viewBuildingModal').modal('show');
        }
      })
      .fail(function() {
        $('#viewBuildingModal').modal('hide');
        Utils.showError('Failed to load building data');
      });
  },
  /**
   * Populate view modal
   */
  populateViewModal: function(building) {
    $('#view-building-number').text(building.number);
    $('#view-building-total-apartments').text(building.total_apartments);
    $('#view-building-total-rooms').text(building.total_rooms);
    $('#view-building-gender-restriction').text(building.gender_restriction);
    $('#view-building-is-active').text(building.active ? 'Active' : 'Inactive');
    $('#view-building-created').text(new Date(building.created_at).toLocaleString());
  },
  /**
   * Save building
   */
  saveBuilding: function() {
    var formData = $('#buildingForm').serialize();
    var apiCall;
    if (this.currentBuildingId) {
      apiCall = ApiService.saveBuilding(formData, this.currentBuildingId);
    } else {
      apiCall = ApiService.createBuilding(formData);
    }
    apiCall
      .done(function() {
        BuildingManager.handleSaveSuccess();
      })
      .fail(function(xhr) {
        BuildingManager.handleSaveError(xhr);
      });
  },
  /**
   * Handle successful save
   */
  handleSaveSuccess: function() {
    $('#buildingModal').modal('hide');
    $('#buildings-table').DataTable().ajax.reload(null, false);
    Utils.showSuccess('Building has been saved successfully.');
    StatsManager.load();
  },
  /**
   * Handle save error
   */
  handleSaveError: function(xhr) {
    $('#buildingModal').modal('hide');
    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
    Utils.showError(message);
  },
  /**
   * Delete building
   */
  deleteBuilding: function(buildingId) {
    Utils.showConfirmDialog({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
      if (result.isConfirmed) {
        BuildingManager.performDelete(buildingId);
      }
    });
  },
  /**
   * Perform actual deletion
   */
  performDelete: function(buildingId) {
    ApiService.deleteBuilding(buildingId)
      .done(function() {
        $('#buildings-table').DataTable().ajax.reload(null, false);
        Utils.showSuccess('Building has been deleted.');
        StatsManager.load();
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete building.';
        Utils.showError(message);
      });
  }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
var SearchManager = {
  /**
   * Initialize search functionality
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind search events
   */
  bindEvents: function() {
    this.initializeAdvancedSearch();
    this.handleClearFilters();
  },
  /**
   * Initialize advanced search event listeners
   */
  initializeAdvancedSearch: function() {
    $('#search_gender_restriction, #search_active').on('keyup change', function() {
      $('#buildings-table').DataTable().ajax.reload();
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearBuildingFiltersBtn').on('click', function() {
      $('#search_gender_restriction, #search_active').val('');
      $('#buildings-table').DataTable().ajax.reload();
    });
  }
};

// ===========================
// DOUBLE ROOM MANAGER
// ===========================
var DoubleRoomManager = {
  /**
   * Initialize double room manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind double room related events
   */
  bindEvents: function() {
    this.handleDoubleRoomToggle();
    this.handleModalShow();
    this.handleInputChange();
    this.handleDocumentReady();
  },
  /**
   * Handle double room toggle
   */
  handleDoubleRoomToggle: function() {
    $('#has_double_rooms').on('change', function(e) {
      if ($(e.currentTarget).is(':checked')) {
        DoubleRoomManager.showDoubleRoomSection();
      } else {
        DoubleRoomManager.hideDoubleRoomSection();
      }
    });
  },
  /**
   * Handle modal show event
   */
  handleModalShow: function() {
    $('#buildingModal').on('show.bs.modal', function() {
      if ($('#has_double_rooms').is(':checked')) {
        DoubleRoomManager.showDoubleRoomSection();
      } else {
        DoubleRoomManager.hideDoubleRoomSection();
      }
    });
  },
  /**
   * Handle input change
   */
  handleInputChange: function() {
    $('#building_total_apartments, #building_rooms_per_apartment').on('input', function() {
      if ($('#has_double_rooms').is(':checked')) {
        DoubleRoomManager.renderDoubleRoomSelectors();
      }
    });
  },
  /**
   * Handle document ready
   */
  handleDocumentReady: function() {
    $(document).ready(function() {
      if ($('#has_double_rooms').is(':checked')) {
        DoubleRoomManager.showDoubleRoomSection();
      } else {
        DoubleRoomManager.hideDoubleRoomSection();
      }
    });
  },
  /**
   * Show double room section
   */
  showDoubleRoomSection: function() {
    $('#apartments-double-rooms-section').show();
    DoubleRoomManager.renderDoubleRoomSelectors();
  },
  /**
   * Hide double room section
   */
  hideDoubleRoomSection: function() {
    $('#apartments-double-rooms-section').hide().empty();
  },
  /**
   * Render double room selectors
   */
  renderDoubleRoomSelectors: function() {
    var totalApartments = parseInt($('#building_total_apartments').val());
    var roomsPerApartment = parseInt($('#building_rooms_per_apartment').val());
    var $section = $('#apartments-double-rooms-section');
    $section.empty();
    if (!totalApartments || !roomsPerApartment) return;
    var accordionHtml = DoubleRoomManager.generateAccordionHtml(totalApartments, roomsPerApartment);
    var boxHtml = DoubleRoomManager.wrapInScrollableBox(accordionHtml);
    $section.append(boxHtml);
  },
  /**
   * Generate accordion HTML
   */
  generateAccordionHtml: function(totalApartments, roomsPerApartment) {
    var accordionHtml = '<div class="accordion" id="apartmentsAccordion">';
    for (var i = 1; i <= totalApartments; i++) {
      accordionHtml += DoubleRoomManager.generateApartmentAccordionItem(i, roomsPerApartment);
    }
    accordionHtml += '</div>';
    return accordionHtml;
  },
  /**
   * Generate single apartment accordion item
   */
  generateApartmentAccordionItem: function(apartmentNumber, roomsPerApartment) {
    var collapseId = 'apartment' + apartmentNumber + 'Collapse';
    var headingId = 'apartment' + apartmentNumber + 'Heading';
    var checkboxes = DoubleRoomManager.generateRoomCheckboxes(apartmentNumber, roomsPerApartment);
    return `
      <div class="accordion-item">
        <h2 class="accordion-header" id="${headingId}">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
            Apartment ${apartmentNumber} - Double Rooms
          </button>
        </h2>
        <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#apartmentsAccordion">
          <div class="accordion-body">
            ${checkboxes}
          </div>
        </div>
      </div>
    `;
  },
  /**
   * Generate room checkboxes
   */
  generateRoomCheckboxes: function(apartmentNumber, roomsPerApartment) {
    var checkboxes = '';
    for (var j = 1; j <= roomsPerApartment; j++) {
      checkboxes += `
        <label class="me-2">
          <input type="checkbox" name="apartments[${apartmentNumber-1}][double_rooms][]" value="${j}"> 
          Room ${j}
        </label>
      `;
    }
    return checkboxes;
  },
  /**
   * Wrap content in a scrollable box
   */
  wrapInScrollableBox: function(content) {
    return `
      <div class="card" style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd;">
        <div class="card-body p-2">
          ${content}
        </div>
      </div>
    `;
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
var BuildingApp = {
  /**
   * Initialize the entire application
   */
  init: function() {
    StatsManager.init();
    BuildingManager.init();
    SearchManager.init();
    DoubleRoomManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  BuildingApp.init();
});

</script>
@endpush 