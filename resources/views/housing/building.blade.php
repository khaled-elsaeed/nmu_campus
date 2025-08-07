@extends('layouts.home')

@section('title', __('buildings.page.title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-buildings" :label="__('buildings.stats.total_buildings')" id="buildings" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('buildings.stats.male_buildings')" id="buildings-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="danger" icon="bx bx-female" :label="__('buildings.stats.female_buildings')" id="buildings-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('buildings.page.header.title')"
        :description="__('buildings.page.header.description')"
        icon="bx bx-buildings"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-primary mx-2" id="addBuildingBtn" type="button" data-bs-toggle="modal" data-bs-target="#buildingModal">
                <i class="bx bx-plus me-1"></i> {{ __('buildings.buttons.add_building') }}
            </button>
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#buildingSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('buildings.buttons.search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('buildings.search.title')" 
        formId="advancedBuildingSearch" 
        collapseId="buildingSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_gender_restriction" class="form-label">{{ __('buildings.search.labels.gender_restriction') }}:</label>
            <select class="form-control" id="search_gender_restriction">
                <option value="">{{ __('general.messages.select.gender_placeholder') }}</option>
                <option value="male">{{ __('general.male') }}</option>
                <option value="female">{{ __('general.female') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('buildings.search.labels.active_status') }}:</label>
            <select class="form-control" id="search_active">
                <option value="">{{ __('general.messages.select.status_placeholder') }}</option>
                <option value="1">{{ __('general.active') }}</option>
                <option value="0">{{ __('general.inactive') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearBuildingFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('buildings.buttons.clear_filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[
            __('buildings.table.headers.number'),
            __('buildings.table.headers.total_apartments'),
            __('buildings.table.headers.total_rooms'),
            __('buildings.table.headers.has_double_room'),
            __('buildings.table.headers.gender'),
            __('buildings.table.headers.active'),
            __('buildings.table.headers.current_occupancy'),
            __('buildings.table.headers.actions')
        ]"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'total_apartments', 'name' => 'total_apartments'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'has_double_rooms', 'name' => 'has_double_rooms'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'current_occupancy', 'name' => 'current_occupancy'],
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
        :title="__('buildings.modal.title')"
        :scrollable="true"
        class="building-modal"
    >
        <x-slot name="slot">
            <form id="buildingForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="building_number" class="form-label">{{ __('buildings.form.labels.building_number') }}</label>
                        <input type="number" id="building_number" name="number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <label for="building_total_apartments" class="form-label">{{ __('buildings.form.labels.total_apartments') }}</label>
                        <input type="number" id="building_total_apartments" name="total_apartments" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <label for="building_rooms_per_apartment" class="form-label">{{ __('buildings.form.labels.rooms_per_apartment') }}</label>
                        <input type="number" id="building_rooms_per_apartment" name="rooms_per_apartment" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3 edit-hide">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="has_double_rooms" name="has_double_rooms">
                            <label class="form-check-label" for="has_double_rooms">
                                {{ __('buildings.form.labels.has_double_rooms') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 edit-hide" id="apartments-double-rooms-section" style="display: none;"></div>
                    <div class="col-md-12 mb-3">
                        <label for="building_gender_restriction" class="form-label">{{ __('buildings.form.labels.gender_restriction') }}</label>
                        <select id="building_gender_restriction" name="gender_restriction" class="form-control" required>
                            <option value="">{{ __('buildings.form.placeholders.select_gender_restriction') }}</option>
                            <option value="male">{{ __('general.male') }}</option>
                            <option value="female">{{ __('general.female') }}</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('buildings.buttons.close') }}</button>
            <button type="submit" class="btn btn-primary" form="buildingForm">{{ __('buildings.buttons.save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Building Modal --}}
    <x-ui.modal 
        id="viewBuildingModal"
        :title="__('buildings.modal.view_title')"
        :scrollable="true"
        class="view-building-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.number') }}:</label>
                    <p id="view-building-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.total_apartments') }}:</label>
                    <p id="view-building-total-apartments" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.total_rooms') }}:</label>
                    <p id="view-building-total-rooms" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.gender_restriction') }}:</label>
                    <p id="view-building-gender-restriction" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.active') }}:</label>
                    <p id="view-building-is-active" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('buildings.modal.labels.created_at') }}:</label>
                    <p id="view-building-created" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('buildings.buttons.close') }}</button>
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
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - BuildingManager: Handles CRUD and actions for buildings
 * - BuildingApp: Initializes all managers
 *  * NOTE: Uses global Utils from public/js/utils.js
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
// API SERVICE
// ===========================
var ApiService = {
  /**
     * Generic AJAX request
     * @param {object} options
     * @returns {jqXHR}
     */
    request(options) {
        return $.ajax(options);
    },
  /**
   * Fetch building statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.buildings.stats, method: 'GET' });
  },
  /**
   * Fetch a single building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchBuilding: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.show, id), method: 'GET' });
  },
  /**
   * Save (update) a building
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveBuilding: function(data, id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.update, id), method: 'PUT', data: data });
  },
  /**
   * Create a new building
   * @param {object} data
   * @returns {jqXHR}
   */
  createBuilding: function(data) {
    return ApiService.request({ url: ROUTES.buildings.store, method: 'POST', data: data });
  },
  /**
   * Delete a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteBuilding: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.destroy, id), method: 'DELETE' });
  },
  /**
   * Activate a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  activateBuilding: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.activate, id), method: 'PATCH' });
  },
  /**
   * Deactivate a building by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deactivateBuilding: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.deactivate, id), method: 'PATCH' });
  }
};

// ===========================
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
  confirm: {
    activate: {
      title: '{{ __('buildings.confirm.activate.title') }}',
      text: '{{ __('buildings.confirm.activate.text') }}',
      button: '{{ __('buildings.confirm.activate.button') }}'
    },
    deactivate: {
      title: '{{ __('buildings.confirm.deactivate.title') }}',
      text: '{{ __('buildings.confirm.deactivate.text') }}',
      button: '{{ __('buildings.confirm.deactivate.button') }}'
    },
    delete: {
      title: '{{ __('buildings.confirm.delete.title') }}',
      text: '{{ __('buildings.confirm.delete.text') }}',
      button: '{{ __('buildings.confirm.delete.button') }}'
    }
  },
  success: {
    activated: '{{ __('buildings.messages.activated') }}',
    deactivated: '{{ __('buildings.messages.deactivated') }}',
    saved: '{{ __('buildings.messages.saved') }}',
    deleted: '{{ __('buildings.messages.deleted') }}'
  },
  error: {
    loadStats: '{{ __('buildings.errors.load_stats') }}',
    loadBuilding: '{{ __('buildings.errors.load_building') }}',
    saveError: '{{ __('buildings.errors.save') }}',
    deleteError: '{{ __('buildings.errors.delete') }}',
    activateError: '{{ __('buildings.errors.activate') }}',
    deactivateError: '{{ __('buildings.errors.deactivate') }}'
  },
  modal: {
    addTitle: '{{ __('buildings.modal.add_title') }}',
    editTitle: '{{ __('buildings.modal.edit_title') }}',
    saving: '{{ __('general.saving') }}'
  },
  status: {
    active: '{{ __('general.active') }}',
    inactive: '{{ __('general.inactive') }}',
    activating: '{{ __('buildings.status.activating') }}',
    deactivating: '{{ __('buildings.status.deactivating') }}'
  },
  apartment: {
    title: '{{ __('buildings.apartment.title') }}',
    doubleRooms: '{{ __('buildings.apartment.double_rooms') }}',
    room: '{{ __('buildings.apartment.room') }}'
  },
  placeholders: {
    selectGenderRestriction: '{{ __('general.messages.select.gender_placeholder') }}',
    selectStatus : '{{ __('general.messages.select.status_placeholder') }}'
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
            '#search_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGenderRestriction },
            '#search_active': { placeholder: TRANSLATION.placeholders.selectStatus }
        },
        modal: {
            '#building_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGenderRestriction, dropdownParent: $('#buildingModal') }
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
            var $element = $(selector);
            if ($element.hasClass('select2-hidden-accessible')) {
                // If it's a Select2 element
                $element.val(null).trigger('change');
            } else {
                // If it's a regular select element
                $element.val('').trigger('change');
            }
        });
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function() {
        // Clear individual elements
        $('#search_gender_restriction').val('').trigger('change');
        $('#search_active').val('').trigger('change');
        
        // Also use the clearSelect2 method for consistency
        this.clearSelect2(['#search_gender_restriction', '#search_active']);
    },

    /**
     * Reset modal Select2 elements
     */
    resetModalSelect2: function() {
        this.clearSelect2(['#building_gender_restriction']);
    }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: ['buildings', 'buildings-male', 'buildings-female'],
  onError: TRANSLATION.error.loadStats
});

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
      if (self.currentBuildingId) {
        self.updateBuilding();
      } else {
        self.createBuilding();
      }
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
      Utils.showConfirmDialog({
        title: TRANSLATION.confirm.activate.title,
        text: TRANSLATION.confirm.activate.text,
        icon: 'question',
        confirmButtonText: TRANSLATION.confirm.activate.button,
        cancelButtonText: '{{ __('general.cancel') }}'
      }).then(function(result) {
        if (result.isConfirmed) {
          Utils.setLoadingState($btn, true, { loadingText: TRANSLATION.status.activating });
          ApiService.activateBuilding(id)
            .done(function(response) {
              Utils.showSuccess(response.message || TRANSLATION.success.activated);
              Utils.reloadDataTable('#buildings-table', null, true);
              StatsManager.refresh();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, TRANSLATION.error.activateError);
            })
            .always(function() {
              Utils.setLoadingState($btn, false, { normalText: '{{ __('general.activate') }}', normalIcon: 'bx bx-check' });
            });
        }
      });
    });
    // Deactivate
    $(document).on('click', '.deactivateBuildingBtn', function(e) {
      e.preventDefault();
      var $btn = $(e.currentTarget);
      var id = $btn.data('id');
      Utils.showConfirmDialog({
        title: TRANSLATION.confirm.deactivate.title,
        text: TRANSLATION.confirm.deactivate.text,
        icon: 'warning',
        confirmButtonText: TRANSLATION.confirm.deactivate.button,
        cancelButtonText: '{{ __('general.cancel') }}'
      }).then(function(result) {
        if (result.isConfirmed) {
          Utils.setLoadingState($btn, true, { loadingText: TRANSLATION.status.deactivating });
          ApiService.deactivateBuilding(id)
            .done(function(response) {
              Utils.showSuccess(response.message || TRANSLATION.success.deactivated);
              Utils.reloadDataTable('#buildings-table', null, true);
              StatsManager.refresh();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, TRANSLATION.error.deactivateError);
            })
            .always(function() {
              Utils.setLoadingState($btn, false, { normalText: '{{ __('general.deactivate') }}', normalIcon: 'bx bx-x' });
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
   * Reset a form by ID
   * @param {string} formId
   */
  resetForm: function(formId) {
    var $form = $('#' + formId);
    $form[0].reset();
    Utils.clearValidation($form);
  },
  /**
   * Reset modal state
   */
  resetModalState: function() {
    this.resetForm('buildingForm');
    $('#has_double_rooms').prop('checked', false);
  },
  /**
   * Setup add modal
   */
  setupAddModal: function() {
    $('#buildingModalTitle').text(TRANSLATION.modal.addTitle);
    Select2Manager.initModalSelect2();
    $('#buildingModal').modal('show');
  },
  /**
   * Setup edit modal
   */
  setupEditModal: function(buildingId) {
    $('#buildingModalTitle').text(TRANSLATION.modal.editTitle);
    ApiService.fetchBuilding(buildingId)
      .done(function(response) {
        if (response.success) {
          BuildingManager.populateEditForm(response.data);
          Select2Manager.initModalSelect2();
          $('#buildingModal').modal('show');
        }
      })
      .fail(function(xhr) {
        $('#buildingModal').modal('hide');
        Utils.handleAjaxError(xhr, '{{ __('buildings.errors.load_building') }}');
      });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(building) {
    $('#building_number').val(building.number).prop('disabled', false);
    $('.edit-hide').hide().find('input, select, textarea').prop('required', false).prop('disabled', true);
    Utils.populateSelect('#building_gender_restriction', [{ id: 'male', name: '{{ __('general.male') }}' }, { id: 'female', name: '{{ __('general.female') }}' }], {
      valueField: 'id',
      textField: 'name',
      placeholder: TRANSLATION.placeholders.selectGenderRestriction,
      selected: building.gender,
      includePlaceholder: true
    });
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
      .fail(function(xhr) {
        $('#viewBuildingModal').modal('hide');
        Utils.handleAjaxError(xhr, '{{ __('buildings.errors.load_building') }}');
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
    $('#view-building-is-active').text(building.active ? TRANSLATION.status.active : TRANSLATION.status.inactive);
    $('#view-building-created').text(new Date(building.created_at).toLocaleString());
  },
  /**
   * Create new building
   */
  createBuilding: function() {
    var $submitBtn = $('#buildingForm').find('button[type="submit"]');
    var formData = $('#buildingForm').serialize();
    
    Utils.setLoadingState($submitBtn, true, {
      loadingText: TRANSLATION.modal.saving, 
      normalText: $submitBtn.text()
    });
    
    ApiService.createBuilding(formData)
      .done(function() {
        $('#buildingModal').modal('hide');
        Utils.reloadDataTable('#buildings-table', null, true);
        Utils.showSuccess(TRANSLATION.success.saved);
        StatsManager.refresh();
      })
      .fail(function(xhr) {
        $('#buildingModal').modal('hide');
        Utils.handleAjaxError(xhr, TRANSLATION.error.saveError);
      })
      .always(function() {
        Utils.setLoadingState($submitBtn, false);
      });
  },
  
  /**
   * Update existing building
   */
  updateBuilding: function() {
    var $submitBtn = $('#buildingForm').find('button[type="submit"]');
    var formData = $('#buildingForm').serialize();
    
    Utils.setLoadingState($submitBtn, true, {
      loadingText: TRANSLATION.modal.saving, 
      normalText: $submitBtn.text()
    });
    
    ApiService.saveBuilding(formData, this.currentBuildingId)
      .done(function() {
        $('#buildingModal').modal('hide');
        Utils.reloadDataTable('#buildings-table', null, true);
        Utils.showSuccess(TRANSLATION.success.saved);
        StatsManager.refresh();
      })
      .fail(function(xhr) {
        $('#buildingModal').modal('hide');
        Utils.handleAjaxError(xhr, TRANSLATION.error.saveError);
      })
      .always(function() {
        Utils.setLoadingState($submitBtn, false);
      });
  },
  /**
   * Delete building
   */
  deleteBuilding: function(buildingId) {
    Utils.showConfirmDialog({
      title: TRANSLATION.confirm.delete.title,
      text: TRANSLATION.confirm.delete.text,
      confirmButtonText: TRANSLATION.confirm.delete.button
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
        Utils.reloadDataTable('#buildings-table', null, true);
        Utils.showSuccess(TRANSLATION.success.deleted);
        StatsManager.refresh();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.error.deleteError);
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
    Select2Manager.initSearchSelect2();
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
    // Handle search field changes
    $('#search_gender_restriction, #search_active').on('change', function() {
      Utils.reloadDataTable('#buildings-table');
    });
    
    // Handle keyup events with simple debouncing
    var searchTimeout;
    $('#search_gender_restriction, #search_active').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        Utils.reloadDataTable('#buildings-table');
      }, 300);
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearBuildingFiltersBtn').on('click', function() {
      // Clear all search fields explicitly
      $('#search_gender_restriction').val('').trigger('change');
      $('#search_active').val('').trigger('change');
      
      // Reset Select2 elements
      Select2Manager.resetSearchSelect2();
      
      // Reload the datatable
      Utils.reloadDataTable('#buildings-table');
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
            ${TRANSLATION.apartment.title} ${apartmentNumber} - ${TRANSLATION.apartment.doubleRooms}
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
          ${TRANSLATION.apartment.room} ${j}
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
    Select2Manager.initAll();
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