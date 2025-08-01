@extends('layouts.home')

@section('title', 'Room Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-door-open" label="Total Rooms" id="rooms" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" label="Male Rooms" id="rooms-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" label="Female Rooms" id="rooms-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Rooms"
        description="Manage rooms and their details."
        icon="bx bx-door-open"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">

            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#roomSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> Search
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedRoomSearch" 
        collapseId="roomSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_building_id" class="form-label">Building:</label>
            <select class="form-control" id="search_building_id">
                <option value="">All</option>
                <!-- Building options will be loaded by JS -->
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_apartment_number" class="form-label">Apartment:</label>
            <select class="form-control" id="search_apartment_number">
                <option value="">All</option>
                <!-- Apartment options will be loaded by JS -->
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
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearRoomFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Number', 'Apartment', 'Building', 'Type', 'Building', 'Gender', 'Available Capacity', 'Active', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'apartment_number', 'name' => 'apartment_number'],
            ['data' => 'building_number', 'name' => 'building_number'],
            ['data' => 'type', 'name' => 'type'],
            ['data' => 'purpose', 'name' => 'purpose'],
            ['data' => 'building_gender_restriction', 'name' => 'building_gender_restriction'],
            ['data' => 'available_capacity', 'name' => 'available_capacity'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.rooms.datatable')"
        :table-id="'rooms-table'"
        :filter-fields="['search_apartment_number','search_building_id','search_gender_restriction']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Edit Room Modal (no create) --}}
    <x-ui.modal 
        id="roomModal"
        title="Edit Room"
        size="lg"
        :scrollable="true"
        class="room-modal"
    >
        <x-slot name="slot">
            <form id="roomForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="single">Single</option>
                            <option value="double">Double</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="purpose" class="form-label">Building Purpose</label>
                        <select id="purpose" name="purpose" class="form-control" required>
                            <option value="">Select Purpose</option>
                            <option value="housing">Housing</option>
                            <option value="staff_housing">Staff Housing</option>
                            <option value="office">Office</option>
                            <option value="storage">Storage</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="room_description" class="form-label">Description</label>
                        <textarea id="room_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="roomForm">Save</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Room Modal --}}
    <x-ui.modal 
        id="viewRoomModal"
        title="Room Details"
        size="md"
        :scrollable="true"
        class="view-room-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-hash"></i> Number:
                </label>
                <p id="view-room-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-building"></i> Building:
                </label>
                <p id="view-room-building" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-home"></i> Apartment:
                </label>
                <p id="view-room-apartment" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user"></i> Type:
                </label>
                <p id="view-room-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-male-female"></i> Gender Restriction:
                </label>
                <p id="view-room-gender-restriction" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-check-circle"></i> Active:
                </label>
                <p id="view-room-is-active" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-group"></i> Capacity:
                </label>
                <p id="view-room-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-check"></i> Current Occupancy:
                </label>
                <p id="view-room-current-occupancy" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-user-plus"></i> Available Capacity:
                </label>
                <p id="view-room-available-capacity" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar"></i> Created At:
                </label>
                <p id="view-room-created" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">
                    <i class="bx bx-calendar-edit"></i> Updated At:
                </label>
                <p id="view-room-updated" class="mb-0"></p>
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
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  rooms: {
    stats: '{{ route('housing.rooms.stats') }}',
    show: '{{ route('housing.rooms.show', ':id') }}',
    update: '{{ route('housing.rooms.update', ':id') }}',
    destroy: '{{ route('housing.rooms.destroy', ':id') }}',
    datatable: '{{ route('housing.rooms.datatable') }}',
    activate: '{{ route('housing.rooms.activate', ':id') }}',
    deactivate: '{{ route('housing.rooms.deactivate', ':id') }}'
  },
  buildings: {
    all: '{{ route('housing.buildings.all') }}'
  },
  apartments: {
    all: '{{ route('housing.apartments.all') }}'
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
      this.updateStatElement('rooms', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('rooms-male', stats.male.count, stats.male.lastUpdateTime);
      this.updateStatElement('rooms-female', stats.female.count, stats.female.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load room statistics');
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
    ['rooms', 'rooms-male', 'rooms-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['rooms', 'rooms-male', 'rooms-female'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

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
      .fail(function() {
        $('#roomModal').modal('hide');
        Utils.showError('Failed to load room data');
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
      .fail(function() {
        $('#viewRoomModal').modal('hide');
        Utils.showError('Failed to load room data');
      });
  },
  /**
   * Handle delete room button click
   */
  handleDeleteRoom: function(e) {
    var roomId = $(e.currentTarget).data('id');
    Utils.confirmAction({
      title: 'Delete Room?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
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
    Utils.confirmAction({
      title: 'Activate Room?',
      text: 'Are you sure you want to activate this room?',
      confirmButtonText: 'Yes, activate it!'
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
    Utils.confirmAction({
      title: 'Deactivate Room?',
      text: 'Are you sure you want to deactivate this room?',
      confirmButtonText: 'Yes, deactivate it!'
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
      .done(function() {
        $('#roomModal').modal('hide');
        RoomManager.reloadTable();
        Utils.showSuccess('Room has been saved successfully.');
        StatsManager.load();
      })
      .fail(function(xhr) {
        $('#roomModal').modal('hide');
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
        Utils.showError(message);
      });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(room) {
    $('#type').val(room.type).prop('disabled', false);
    $('#purpose').val(room.purpose).prop('disabled', false);
    $('#room_description').val(room.description).prop('disabled', false);
  },
  /**
   * Populate view modal
   */
  populateViewModal: function(room) {
    $('#view-room-number').text('Room ' + room.number);
    $('#view-room-apartment').text('Apartment ' + room.apartment);
    $('#view-room-building').text('Building ' + room.building);
    $('#view-room-type').text(room.type);
    $('#view-room-gender-restriction').text(room.gender_restriction);
    $('#view-room-is-active').text(room.active ? 'Active' : 'Inactive');
    // Use global utils.formatDate if available, else fallback
    var formatDate = Utils && Utils.formatDate ? Utils.formatDate : function(dateString) {
      return dateString ? new Date(dateString).toLocaleString() : '--';
    };
    $('#view-room-created').text(formatDate(room.created_at));
    $('#view-room-updated').text(formatDate(room.updated_at));
    $('#view-room-capacity').text(room.capacity);
    $('#view-room-current-occupancy').text(room.current_occupancy);
    $('#view-room-available-capacity').text(room.available_capacity);
  },
  /**
   * Delete room
   */
  deleteRoom: function(roomId) {
    ApiService.deleteRoom(roomId)
      .done(function() {
        RoomManager.reloadTable();
        Utils.showSuccess('Room has been deleted.');
        StatsManager.load();
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete room.';
        Utils.showError(message);
      });
  },
  /**
   * Toggle room status (activate/deactivate)
   */
  toggleRoomStatus: function(roomId, isActivate, $button) {
    var apiCall = isActivate ? ApiService.activateRoom : ApiService.deactivateRoom;
    var successMessage = isActivate ? 'activated' : 'deactivated';
    $button.prop('disabled', true);
    apiCall(roomId)
      .done(function(response) {
        if (response.success) {
          Utils.showSuccess('Room ' + successMessage + ' successfully');
          RoomManager.reloadTable();
        } else {
          Utils.showError(response.message || 'Operation failed');
        }
      })
      .fail(function(xhr) {
        Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Operation failed');
      })
      .always(function() {
        $button.prop('disabled', false);
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
    $('#search_building_id, #search_apartment_number, #search_gender_restriction').on('keyup change', function() { self.reloadTable(); });
    $('#clearRoomFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    $('#search_building_id, #search_apartment_number, #search_gender_restriction').val('');
    this.reloadTable();
  },
  /**
   * Reload the rooms table
   */
  reloadTable: function() {
    $('#rooms-table').DataTable().ajax.reload();
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
          var $select = $('#search_building_id');
          $select.empty().append('<option value="">All</option>');
          response.data.forEach(function(building) {
            $select.append('<option value="' + building.id + '">Building ' + building.number + '</option>');
          });
        }
      })
      .fail(function() {
        Utils.showError('Failed to load buildings');
      });
  },
  /**
   * Populate apartment select dropdown
   */
  populateApartmentSelect: function() {
    ApiService.fetchApartments()
      .done(function(response) {
        if (response.success) {
          var $select = $('#search_apartment_number');
          $select.empty().append('<option value="">All</option>');
          var uniqueNumbers = new Set();
          response.data.forEach(function(apartment) {
            if (!uniqueNumbers.has(apartment.number)) {
              uniqueNumbers.add(apartment.number);
              $select.append('<option value="' + apartment.number + '">Apartment ' + apartment.number + '</option>');
            }
          });
        }
      })
      .fail(function() {
        Utils.showError('Failed to load apartments');
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