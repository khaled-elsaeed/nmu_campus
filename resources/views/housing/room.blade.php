@extends('layouts.home')

@section('title', __('rooms.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row mb-4 g-2">
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('rooms.stats.total_rooms')"
              id="rooms"
              :subStats="[
                  'male' => [
                      'label' => __('rooms.stats.male_rooms'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('rooms.stats.female_rooms'), 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="info"
              icon="bx bx-door-open"
              :label="__('rooms.stats.total_double_rooms')"
              id="double-rooms"
              :subStats="[
                  'male' => [
                      'label' => __('rooms.stats.male_double_rooms'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('rooms.stats.female_double_rooms'),
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('rooms.stats.total_beds')"
              id="beds"
              :subStats="[
                  'male' => [
                      'label' => __('rooms.stats.male_beds'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('rooms.stats.female_beds'),
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('rooms.stats.total_available_beds')"
              id="available-beds"
              :subStats="[
                  'male' => [
                      'label' => __('rooms.stats.available_male_beds'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('rooms.stats.available_female_beds'),
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('rooms.page.header.title')"
        :description="__('rooms.page.header.description')"
        icon="bx bx-door-open"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">

            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#roomSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('rooms.buttons.search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('rooms.search.title')" 
        formId="advancedRoomSearch" 
        collapseId="roomSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">{{ __('rooms.search.labels.building_number') }}:</label>
            <select class="form-control" id="search_building_id">
                <option value="">{{ __('rooms.search.placeholders.all') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_id" class="form-label">{{ __('rooms.search.labels.apartment_number') }}:</label>
            <select class="form-control" id="search_apartment_id">
                <!-- Apartment options will be loaded by JS -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_gender_restriction" class="form-label">{{ __('rooms.search.labels.gender_restriction') }}:</label>
            <select class="form-control" id="search_gender_restriction">
                <option value="">{{ __('rooms.search.placeholders.all') }}</option>
                <option value="male">{{ __('rooms.search.options.male') }}</option>
                <option value="female">{{ __('rooms.search.options.female') }}</option>
                <option value="mixed">{{ __('rooms.search.options.mixed') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearRoomFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('rooms.buttons.clear_filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[
            __('rooms.table.headers.name'),
            __('rooms.table.headers.apartment'),
            __('rooms.table.headers.building'),
            __('rooms.table.headers.type'),
            __('rooms.table.headers.purpose'),
            __('rooms.table.headers.gender'),
            __('rooms.table.headers.available_capacity'),
            __('rooms.table.headers.active'),
            __('rooms.table.headers.actions')
        ]"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'apartment', 'name' => 'apartment'],
            ['data' => 'building', 'name' => 'building'],
            ['data' => 'type', 'name' => 'type'],
            ['data' => 'purpose', 'name' => 'purpose'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'available_capacity', 'name' => 'available_capacity'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.rooms.datatable')"
        :table-id="'rooms-table'"
        :filter-fields="['search_apartment_id','search_building_id','search_gender_restriction']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Edit Room Modal (no create) --}}
    <x-ui.modal 
        id="roomModal"
        :title="__('rooms.modals.edit.title')"
        size="lg"
        :scrollable="true"
        class="room-modal"
    >
        <x-slot name="slot">
            <form id="roomForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">{{ __('rooms.modals.edit.labels.type') }}</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">{{ __('rooms.modals.edit.placeholders.select_type') }}</option>
                            <option value="single">{{ __('rooms.modals.edit.options.single') }}</option>
                            <option value="double">{{ __('rooms.modals.edit.options.double') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label">{{ __('rooms.modals.edit.labels.purpose') }}</label>
                        <select id="purpose" name="purpose" class="form-control" required>
                            <option value="">{{ __('rooms.modals.edit.placeholders.select_purpose') }}</option>
                            <option value="housing">{{ __('rooms.modals.edit.options.housing') }}</option>
                            <option value="staff_housing">{{ __('rooms.modals.edit.options.staff_housing') }}</option>
                            <option value="office">{{ __('rooms.modals.edit.options.office') }}</option>
                            <option value="storage">{{ __('rooms.modals.edit.options.storage') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="room_description" class="form-label">{{ __('rooms.modals.edit.labels.description') }}</label>
                        <textarea id="room_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('rooms.buttons.close') }}</button>
            <button type="submit" class="btn btn-primary" form="roomForm">{{ __('rooms.buttons.save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Room Modal --}}
    <x-ui.modal 
        id="viewRoomModal"
        :title="__('rooms.modals.view.title')"
        size="md"
        :scrollable="true"
        class="view-room-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-hash"></i> {{ __('rooms.modals.view.labels.number') }}:
                </label>
                <p id="view-room-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-building"></i> {{ __('rooms.modals.view.labels.building') }}:
                </label>
                <p id="view-room-building" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-home"></i> {{ __('rooms.modals.view.labels.apartment') }}:
                </label>
                <p id="view-room-apartment" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user"></i> {{ __('rooms.modals.view.labels.type') }}:
                </label>
                <p id="view-room-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-male-female"></i> {{ __('rooms.modals.view.labels.gender_restriction') }}:
                </label>
                <p id="view-room-gender-restriction" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-check-circle"></i> {{ __('rooms.modals.view.labels.active') }}:
                </label>
                <p id="view-room-is-active" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-group"></i> {{ __('rooms.modals.view.labels.capacity') }}:
                </label>
                <p id="view-room-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-check"></i> {{ __('rooms.modals.view.labels.current_occupancy') }}:
                </label>
                <p id="view-room-current-occupancy" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-plus"></i> {{ __('rooms.modals.view.labels.available_capacity') }}:
                </label>
                <p id="view-room-available-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar"></i> {{ __('rooms.modals.view.labels.created_at') }}:
                </label>
                <p id="view-room-created" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar-edit"></i> {{ __('rooms.modals.view.labels.updated_at') }}:
                </label>
                <p id="view-room-updated" class="mb-0"></p>
            </div>
          </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('rooms.buttons.close') }}</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection

@push('scripts')
<script>
/**
 * Room Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - RoomManager: Handles CRUD and actions for rooms
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - RoomApp: Initializes all managers
 *  NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// TRANSLATIONS
// ===========================
const TRANSLATION = {
  confirm: {
    activate: {
      title: '{{ __('rooms.confirm.activate.title') }}',
      text: '{{ __('rooms.confirm.activate.text') }}',
      button: '{{ __('rooms.confirm.activate.button') }}'
    },
    deactivate: {
      title: '{{ __('rooms.confirm.deactivate.title') }}',
      text: '{{ __('rooms.confirm.deactivate.text') }}',
      button: '{{ __('rooms.confirm.deactivate.button') }}'
    },
    delete: {
      title: '{{ __('rooms.confirm.delete.title') }}',
      text: '{{ __('rooms.confirm.delete.text') }}',
      button: '{{ __('rooms.confirm.delete.button') }}'
    }
  },
  success: {
    activated: '{{ __('rooms.messages.activated') }}',
    deactivated: '{{ __('rooms.messages.deactivated') }}',
    deleted: '{{ __('rooms.messages.deleted') }}',
    saved: '{{ __('rooms.messages.saved') }}'
  },
  error: {
    loadStats: '{{ __('rooms.messages.load_stats_error') }}',
    loadRoom: '{{ __('rooms.messages.load_room_error') }}',
    deleteRoom: '{{ __('rooms.messages.delete_error') }}',
    operationFailed: '{{ __('rooms.messages.operation_failed') }}'
  },
  placeholders: {
    selectBuilding: '{{ __('rooms.placeholders.select_building') }}',
    selectApartment: '{{ __('rooms.placeholders.select_apartment') }}',
    selectBuildingFirst: '{{ __('rooms.placeholders.select_building_first') }}',
    noApartments: '{{ __('rooms.placeholders.no_apartments') }}',
    selectGender: '{{ __('rooms.placeholders.select_gender') }}',
    selectType: '{{ __('rooms.placeholders.select_type') }}',
    selectPurpose: '{{ __('rooms.placeholders.select_purpose') }}'
  },
  status: {
    active: '{{ __('rooms.status.active') }}',
    inactive: '{{ __('rooms.status.inactive') }}'
  }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  rooms: {
    stats: '{{ route('housing.rooms.stats') }}',
    show: '{{ route('housing.rooms.show', ':id') }}',
    update: '{{ route('housing.rooms.update', ':id') }}',
    destroy: '{{ route('housing.rooms.destroy', ':id') }}',
    activate: '{{ route('housing.rooms.activate', ':id') }}',
    deactivate: '{{ route('housing.rooms.deactivate', ':id') }}'
  },
  buildings: {
    all: '{{ route('housing.buildings.all') }}'
  },
  apartments: {
    all: '{{ route('housing.apartments.all', ':id') }}'
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
   * Fetch room statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.rooms.stats, method: 'GET' });
  },
  /**
   * Fetch a single room by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchRoom: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.show, id), method: 'GET' });
  },
  /**
   * Save (update) a room
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveRoom: function(data, id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.update, id), method: 'PUT', data: data });
  },
  /**
   * Delete a room by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteRoom: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.destroy, id), method: 'DELETE' });
  },
  /**
   * Activate a room by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  activateRoom: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.activate, id), method: 'PATCH' });
  },
  /**
   * Deactivate a room by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deactivateRoom: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.deactivate, id), method: 'PATCH' });
  },
  /**
   * Fetch all buildings
   * @returns {jqXHR}
   */
  fetchBuildings: function() {
    return ApiService.request({ url: ROUTES.buildings.all, method: 'GET' });
  },
  /**
   * Fetch all apartments for specific building
   * @returns {jqXHR}
   */
  fetchApartments: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.all,id), method: 'GET' });
  }
};

// ===========================
// STATISTICS MANAGER
// =========================== 
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: ['rooms','double-rooms', 'beds','available-beds'],
  subStatsConfig: {
    'rooms': ['male', 'female'],
    'double-rooms': ['male', 'female'],
    'beds': ['male', 'female'],
    'available-beds':['male', 'female']
  },
  onError: TRANSLATION.error.loadStats
});

// ===========================
// ROOM MANAGER
// ===========================
var RoomManager = {
  currentRoomId: null,
  /**
   * Initialize room manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind all room-related events
   */
  bindEvents: function() {
    var self = this;
    $(document).on('click', '.editRoomBtn', function(e) { self.handleEditRoom(e); });
    $(document).on('click', '.viewRoomBtn', function(e) { self.handleViewRoom(e); });
    $(document).on('click', '.deleteRoomBtn', function(e) { self.handleDeleteRoom(e); });
    $(document).on('click', '.activateRoomBtn', function(e) { self.handleActivateRoom(e); });
    $(document).on('click', '.deactivateRoomBtn', function(e) { self.handleDeactivateRoom(e); });
    $('#roomForm').on('submit', function(e) { self.handleFormSubmit(e); });
  },
  /**
   * Handle edit room button click
   */
  handleEditRoom: function(e) {
    var roomId = $(e.currentTarget).data('id');
    this.currentRoomId = roomId;
    ApiService.fetchRoom(roomId)
      .done(function(response) {
        if (response.success) {
          RoomManager.populateEditForm(response.data);
          $('#roomModal').modal('show');
        }
      })
      .fail(function(xhr) {
        $('#roomModal').modal('hide');
        Utils.handleAjaxError(xhr, TRANSLATION.error.loadRoom)
      });
  },
  /**
   * Handle view room button click
   */
  handleViewRoom: function(e) {
    var roomId = $(e.currentTarget).data('id');
    ApiService.fetchRoom(roomId)
      .done(function(response) {
        if (response.success) {
          RoomManager.populateViewModal(response.data);
          $('#viewRoomModal').modal('show');
        }
      })
      .fail(function(xhr) {
        $('#viewRoomModal').modal('hide');
        Utils.handleAjaxError(xhr, TRANSLATION.error.loadRoom)
      });
  },
  /**
   * Handle delete room button click
   */
  handleDeleteRoom: function(e) {
    var roomId = $(e.currentTarget).data('id');
    Utils.showConfirmDialog({
      title: TRANSLATION.confirm.delete.title,
      text: TRANSLATION.confirm.delete.text,
      confirmButtonText: TRANSLATION.confirm.delete.button
    }).then(function(result) {
      if (result.isConfirmed) {
        RoomManager.deleteRoom(roomId);
      }
    });
  },
  /**
   * Handle activate room button click
   */
  handleActivateRoom: function(e) {
    e.preventDefault();
    var roomId = $(e.currentTarget).data('id');
    Utils.showConfirmDialog({
      title: TRANSLATION.confirm.activate.title,
      text: TRANSLATION.confirm.activate.text,
      confirmButtonText: TRANSLATION.confirm.activate.button
    }).then(function(result) {
      if (result.isConfirmed) {
        RoomManager.toggleRoomStatus(roomId, true, $(e.currentTarget));
      }
    });
  },
  /**
   * Handle deactivate room button click
   */
  handleDeactivateRoom: function(e) {
    e.preventDefault();
    var roomId = $(e.currentTarget).data('id');
    Utils.showConfirmDialog({
      title: TRANSLATION.confirm.deactivate.title,
      text: TRANSLATION.confirm.deactivate.text,
      confirmButtonText: TRANSLATION.confirm.deactivate.button
    }).then(function(result) {
      if (result.isConfirmed) {
        RoomManager.toggleRoomStatus(roomId, false, $(e.currentTarget));
      }
    });
  },
  /**
   * Handle form submit
   */
  handleFormSubmit: function(e) {
    e.preventDefault();
    var formData = $(e.currentTarget).serialize();
    ApiService.saveRoom(formData, this.currentRoomId)
      .done(function(response) {
        $('#roomModal').modal('hide');
        Utils.reloadDataTable('#rooms-table');
        Utils.showSuccess(response.message || TRANSLATION.success.saved);
        StatsManager.refresh();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.error.operationFailed)
      });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(room) {
    Utils.setElementText('#type', room.type);
    Utils.disable('#type', false);
    Utils.setElementText('#purpose', room.purpose);
    Utils.disable('#purpose', false);
    Utils.setElementText('#room_description', room.description);
    Utils.disable('#room_description', false);
  },
  /**
   * Populate view modal
   */
  populateViewModal: function(room) {
    Utils.setElementText('#view-room-number', 'Room ' + room.number);
    Utils.setElementText('#view-room-apartment', 'Apartment ' + room.apartment);
    Utils.setElementText('#view-room-building', 'Building ' + room.building);
    Utils.setElementText('#view-room-type', room.type);
    Utils.setElementText('#view-room-gender-restriction', room.gender_restriction);
    Utils.setElementText('#view-room-is-active', room.active ? 'Active' : 'Inactive');
    var formatDate = Utils && Utils.formatDate ? Utils.formatDate : function(dateString) {
      return dateString ? new Date(dateString).toLocaleString() : '--';
    };
    Utils.setElementText('#view-room-created', formatDate(room.created_at));
    Utils.setElementText('#view-room-updated', formatDate(room.updated_at));
    Utils.setElementText('#view-room-capacity', room.capacity);
    Utils.setElementText('#view-room-current-occupancy', room.current_occupancy);
    Utils.setElementText('#view-room-available-capacity', room.available_capacity);
  },
  /**
   * Delete room
   */
  deleteRoom: function(roomId) {
    ApiService.deleteRoom(roomId)
      .done(function(response) {
        Utils.reloadDataTable('#rooms-table');
        Utils.showSuccess(response.message || TRANSLATION.success.deleted);
        StatsManager.load();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.error.deleteRoom)
      });
  },
  /**
   * Toggle room status (activate/deactivate)
   */
  toggleRoomStatus: function(roomId, isActivate, $button) {
    var apiCall = isActivate ? ApiService.activateRoom : ApiService.deactivateRoom;
    var successMessage = isActivate ? TRANSLATION.success.activated : TRANSLATION.success.deactivated;
    Utils.setLoadingState($button, true);
    apiCall(roomId)
      .done(function(response) {
        if (response.success) {
          Utils.showSuccess(response.message || successMessage);
          Utils.reloadDataTable('#rooms-table');
        } else {
          Utils.showError(response.message || TRANSLATION.error.operationFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.error.operationFailed)
      })
      .always(function() {
        Utils.setLoadingState($button, false);
      });
  },
  /**
   * Reload the rooms table
   */
  reloadTable: function() {
    $('#rooms-table').DataTable().ajax.reload(null, false);
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
            '#type': { placeholder: TRANSLATION.placeholders.selectType },
            '#purpose': { placeholder: TRANSLATION.placeholders.selectPurpose }
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
            $(selector).val('').trigger('change');
        });
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function() {
        this.clearSelect2(['#search_building_id', '#search_apartment_id', '#search_gender_restriction']);
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
    $('#search_building_id, #search_apartment_id, #search_gender_restriction').on('change', function() { self.handleFilterChange(); });
    $('#clearRoomFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Handle filter change
   */
  handleFilterChange: function() {
    Utils.reloadDataTable('#rooms-table');
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    Select2Manager.resetSearchSelect2();
    // Reset apartment select to disabled
    $('#search_apartment_id').prop('disabled', true).empty().append('<option value="">' + TRANSLATION.placeholders.selectBuildingFirst + '</option>');
    Utils.reloadDataTable('#rooms-table');
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
        Select2Manager.initSearchSelect2();
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
var RoomApp = {
  /**
   * Initialize all managers
   */
  init: function() {
    StatsManager.init();
    RoomManager.init();
    SearchManager.init();
    SelectManager.init();
    Select2Manager.initAll();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  RoomApp.init();
});
</script>
@endpush 