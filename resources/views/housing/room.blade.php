@extends('layouts.home')

@section('title', __('Room Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row mb-4 g-2">
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('Total Rooms')"
              id="rooms"
              :subStats="[
                  'male' => [
                      'label' => __('Male Rooms'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Rooms'), 
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
              :label="__('Total Double Rooms')"
              id="double-rooms"
              :subStats="[
                  'male' => [
                      'label' => __('Male Double Rooms'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Double Rooms'),
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
              :label="__('Total Beds')"
              id="beds"
              :subStats="[
                  'male' => [
                      'label' => __('Male Beds'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Beds'),
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
              :label="__('Total Available Beds')"
              id="available-beds"
              :subStats="[
                  'male' => [
                      'label' => __('Available Male Beds'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Available Female Beds'),
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Room Management')"
        :description="__('Manage all campus rooms and their details')"
        icon="bx bx-door-open"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">

            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#roomSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('Advanced Room Search')" 
        formId="advancedRoomSearch" 
        collapseId="roomSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">{{ __('Building Number') }}:</label>
            <select class="form-control" id="search_building_id">
                <option value="">{{ __('All') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_id" class="form-label">{{ __('Apartment Number') }}:</label>
            <select class="form-control" id="search_apartment_id">
                <!-- Apartment options will be loaded by JS -->
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
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearRoomFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers=" [
            __('Name'),
            __('Apartment'),
            __('Building'),
            __('Type'),
            __('Purpose'),
            __('Gender'),
            __('Occupied'),
            __('Status'),
            __('Actions')
        ]"
        :columns="[
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'apartment', 'name' => 'apartment'],
            ['data' => 'building', 'name' => 'building'],
            ['data' => 'type', 'name' => 'type'],
            ['data' => 'purpose', 'name' => 'purpose'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'occupied', 'name' => 'occupied', 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.rooms.datatable')"
        table-id="rooms-table"
        :filter-fields="['search_apartment_id','search_building_id','search_gender_restriction']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Edit Room Modal (no create) --}}
    <x-ui.modal 
        id="roomModal"
        :title="__('Edit Room')"
        size="lg"
        :scrollable="true"
        class="room-modal"
    >
        <x-slot name="slot">
            <form id="roomForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">{{ __('Room Type') }}</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">{{ __('Select Room Type') }}</option>
                            <option value="single">{{ __('Single') }}</option>
                            <option value="double">{{ __('Double') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label">{{ __('Purpose') }}</label>
                        <select id="purpose" name="purpose" class="form-control" required>
                            <option value="">{{ __('Select Purpose') }}</option>
                            <option value="housing">{{ __('Housing') }}</option>
                            <option value="staff_housing">{{ __('Staff Housing') }}</option>
                            <option value="office">{{ __('Office') }}</option>
                            <option value="storage">{{ __('Storage') }}</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="room_description" class="form-label">{{ __('Description') }}</label>
                        <textarea id="room_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary" form="roomForm">{{ __('Save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Room Modal --}}
    <x-ui.modal 
        id="viewRoomModal"
        :title="__('Room Details')"
        size="md"
        :scrollable="true"
        class="view-room-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-hash"></i> {{ __('Number') }}:
                </label>
                <p id="view-room-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-building"></i> {{ __('Building') }}:
                </label>
                <p id="view-room-building" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-home"></i> {{ __('Apartment') }}:
                </label>
                <p id="view-room-apartment" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user"></i> {{ __('Type') }}:
                </label>
                <p id="view-room-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-male-female"></i> {{ __('Gender Restriction') }}:
                </label>
                <p id="view-room-gender-restriction" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-check-circle"></i> {{ __('Active') }}:
                </label>
                <p id="view-room-is-active" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-group"></i> {{ __('Capacity') }}:
                </label>
                <p id="view-room-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-check"></i> {{ __('Current Occupancy') }}:
                </label>
                <p id="view-room-current-occupancy" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-plus"></i> {{ __('Available Capacity') }}:
                </label>
                <p id="view-room-available-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar"></i> {{ __('Created At') }}:
                </label>
                <p id="view-room-created" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar-edit"></i> {{ __('Updated At') }}:
                </label>
                <p id="view-room-updated" class="mb-0"></p>
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
      title: @json(__('Activate Room')),
      text: @json(__('Are you sure you want to activate this room?')),
      button: @json(__('Activate'))
    },
    deactivate: {
      title: @json(__('Deactivate Room')),
      text: @json(__('Are you sure you want to deactivate this room?')),
      button: @json(__('Deactivate'))
    },
    delete: {
      title: @json(__('Delete Room')),
      text: @json(__('Are you sure you want to delete this room? This action cannot be undone.')),
      button: @json(__('Delete'))
    }
  },
  placeholders: {
    selectBuilding: @json(__('Select Building')),
    selectApartment: @json(__('Select Apartment')),
    selectBuildingFirst: @json(__('Select a building first')),
    noApartments: @json(__('No apartments available')),
    selectGender: @json(__('Select Gender')),
    selectType: @json(__('Select Room Type')),
    selectPurpose: @json(__('Select Purpose'))
  },
  gender: {
    male: @json(__('Male')),
    female: @json(__('Female'))
  },
  status: {
    active: @json(__('Active')),
    inactive: @json(__('Inactive'))
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
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
        Utils.showSuccess(response.message);
        StatsManager.refresh();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
        Utils.showSuccess(response.message);
        StatsManager.load();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
      });
  },
  /**
   * Toggle room status (activate/deactivate)
   */
  toggleRoomStatus: function(roomId, isActivate, $button) {
    var apiCall = isActivate ? ApiService.activateRoom : ApiService.deactivateRoom;
    Utils.setLoadingState($button, true);
    apiCall(roomId)
      .done(function(response) {
        if (response.success) {
          Utils.showSuccess(response.message);
          Utils.reloadDataTable('#rooms-table');
        } else {
          Utils.showError(response.message);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
      .fail(function(xhr) {
          Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
        Utils.handleAjaxError(xhr, xhr.responseJSON.message);
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
  try {
    RoomApp.init();
  } catch (error) {
    console.error('RoomApp initialization error:', error);
  }
});
</script>
@endpush