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
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#roomSearchCollapse" aria-expanded="false" aria-controls="roomSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
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
        :headers="['Number', 'Apartment', 'Building', 'Type', 'Building Purpose', 'Gender Restriction', 'Available Capacity', 'Active', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'room_number'],
            ['data' => 'apartment', 'name' => 'apartment_number'],
            ['data' => 'building', 'name' => 'building_number'],
            ['data' => 'type', 'name' => 'type'],
            ['data' => 'purpose', 'name' => 'purpose'],
            ['data' => 'gender_restriction', 'name' => 'building_gender_restriction'],
            ['data' => 'available_capacity', 'name' => 'available_capacity'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
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
  
// ===========================
// CONSTANTS AND CONFIGURATION
// ===========================
const ROUTES = {
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

const SELECTORS = {
  stats: {
    rooms: '#rooms',
    roomsMale: '#rooms-male',
    roomsFemale: '#rooms-female'
  },
  table: '#rooms-table',
  modals: {
    room: '#roomModal',
    viewRoom: '#viewRoomModal'
  },
  forms: {
    room: '#roomForm',
    advancedSearch: '#advancedRoomSearch'
  },
  filters: {
    building: '#search_building_id',
    apartment: '#search_apartment_number',
    gender: '#search_gender_restriction'
  },
  buttons: {
    clearFilters: '#clearRoomFiltersBtn',
    editRoom: '.editRoomBtn',
    viewRoom: '.viewRoomBtn',
    deleteRoom: '.deleteRoomBtn',
    activateRoom: '.activateRoomBtn',
    deactivateRoom: '.deactivateRoomBtn'
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
const Utils = {
  showError(message) {
    Swal.fire({ 
      title: 'Error', 
      html: message, 
      icon: 'error' 
    });
  },

  showSuccess(message) {
    Swal.fire({ 
      toast: true, 
      position: 'top-end', 
      icon: 'success', 
      title: message, 
      showConfirmButton: false, 
      timer: 2500, 
      timerProgressBar: true 
    });
  },

  toggleLoadingState(elementId, isLoading) {
    const $value = $(`#${elementId}-value`);
    const $loader = $(`#${elementId}-loader`);
    const $updated = $(`#${elementId}-last-updated`);
    const $updatedLoader = $(`#${elementId}-last-updated-loader`);
    
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

  replaceRouteId(route, id) {
    return route.replace(':id', id);
  },

  formatDate(dateString) {
    return dateString ? new Date(dateString).toLocaleString() : '--';
  },

  confirmAction(options = {}) {
    const defaults = {
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, do it!'
    };
    
    return Swal.fire({ ...defaults, ...options });
  }
};

// ===========================
// API SERVICE LAYER
// ===========================
const ApiService = {
  request: (options) => $.ajax(options),

  fetchStats: () => ApiService.request({
    url: ROUTES.rooms.stats,
    method: 'GET'
  }),

  fetchRoom: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.rooms.show, id),
    method: 'GET'
  }),

  saveRoom: (data, id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.rooms.update, id),
    method: 'PUT',
    data
  }),

  deleteRoom: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.rooms.destroy, id),
    method: 'DELETE'
  }),

  activateRoom: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.rooms.activate, id),
    method: 'PATCH'
  }),

  deactivateRoom: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.rooms.deactivate, id),
    method: 'PATCH'
  }),

  fetchBuildings: () => ApiService.request({
    url: ROUTES.buildings.all,
    method: 'GET'
  }),

  fetchApartments: () => ApiService.request({
    url: ROUTES.apartments.all,
    method: 'GET'
  })
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================
const StatsManager = {
  init() {
    this.loadStats();
  },

  loadStats() {
    this.toggleAllStatsLoading(true);

    ApiService.fetchStats()
      .done((response) => {
        if (response.success) {
          this.updateStatsDisplay(response.data);
        } else {
          this.setStatsError();
        }
        this.toggleAllStatsLoading(false);
      })
      .fail(() => {
        this.setStatsError();
        this.toggleAllStatsLoading(false);
        Utils.showError('Failed to load room statistics');
      });
  },

  updateStatsDisplay(data) {
    // Total rooms
    $('#rooms-value').text(data.total.count);
    $('#rooms-last-updated').text(data.total.lastUpdateTime);
    
    // Male rooms
    $('#rooms-male-value').text(data.male.count);
    $('#rooms-male-last-updated').text(data.male.lastUpdateTime);
    
    // Female rooms
    $('#rooms-female-value').text(data.female.count);
    $('#rooms-female-last-updated').text(data.female.lastUpdateTime);
  },

  setStatsError() {
    $('#rooms-value, #rooms-male-value, #rooms-female-value').text('N/A');
    $('#rooms-last-updated, #rooms-male-last-updated, #rooms-female-last-updated').text('N/A');
  },

  toggleAllStatsLoading(isLoading) {
    Utils.toggleLoadingState('rooms', isLoading);
    Utils.toggleLoadingState('rooms-male', isLoading);
    Utils.toggleLoadingState('rooms-female', isLoading);
  }
};

// ===========================
// ROOM MANAGEMENT
// ===========================
const RoomManager = {
  currentRoomId: null,

  init() {
    this.bindEvents();
  },

  bindEvents() {
    $(document).on('click', SELECTORS.buttons.editRoom, (e) => this.handleEditRoom(e));
    $(document).on('click', SELECTORS.buttons.viewRoom, (e) => this.handleViewRoom(e));
    $(document).on('click', SELECTORS.buttons.deleteRoom, (e) => this.handleDeleteRoom(e));
    $(document).on('click', SELECTORS.buttons.activateRoom, (e) => this.handleActivateRoom(e));
    $(document).on('click', SELECTORS.buttons.deactivateRoom, (e) => this.handleDeactivateRoom(e));
    $(SELECTORS.forms.room).on('submit', (e) => this.handleFormSubmit(e));
  },

  handleEditRoom(e) {
    const roomId = $(e.currentTarget).data('id');
    this.currentRoomId = roomId;
    
    ApiService.fetchRoom(roomId)
      .done((response) => {
        if (response.success) {
          this.populateEditForm(response.data);
          $(SELECTORS.modals.room).modal('show');
        }
      })
      .fail(() => {
        $(SELECTORS.modals.room).modal('hide');
        Utils.showError('Failed to load room data');
      });
  },

  handleViewRoom(e) {
    const roomId = $(e.currentTarget).data('id');
    
    ApiService.fetchRoom(roomId)
      .done((response) => {
        if (response.success) {
          this.populateViewModal(response.data);
          $(SELECTORS.modals.viewRoom).modal('show');
        }
      })
      .fail(() => {
        $(SELECTORS.modals.viewRoom).modal('hide');
        Utils.showError('Failed to load room data');
      });
  },

  handleDeleteRoom(e) {
    const roomId = $(e.currentTarget).data('id');
    
    Utils.confirmAction({
      title: 'Delete Room?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.deleteRoom(roomId);
      }
    });
  },

  handleActivateRoom(e) {
    e.preventDefault();
    const roomId = $(e.currentTarget).data('id');
    
    Utils.confirmAction({
      title: 'Activate Room?',
      text: 'Are you sure you want to activate this room?',
      confirmButtonText: 'Yes, activate it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.toggleRoomStatus(roomId, true, $(e.currentTarget));
      }
    });
  },

  handleDeactivateRoom(e) {
    e.preventDefault();
    const roomId = $(e.currentTarget).data('id');
    
    Utils.confirmAction({
      title: 'Deactivate Room?',
      text: 'Are you sure you want to deactivate this room?',
      confirmButtonText: 'Yes, deactivate it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.toggleRoomStatus(roomId, false, $(e.currentTarget));
      }
    });
  },

  handleFormSubmit(e) {
    e.preventDefault();
    const formData = $(e.currentTarget).serialize();
    
    ApiService.saveRoom(formData, this.currentRoomId)
      .done(() => {
        $(SELECTORS.modals.room).modal('hide');
        this.reloadTable();
        Utils.showSuccess('Room has been saved successfully.');
        StatsManager.loadStats();
      })
      .fail((xhr) => {
        $(SELECTORS.modals.room).modal('hide');
        const message = xhr.responseJSON?.message || 'An error occurred. Please check your input.';
        Utils.showError(message);
      });
  },

  populateEditForm(room) {
    $('#type').val(room.type).prop('disabled', false);
    $('#purpose').val(room.purpose).prop('disabled', false);
    $('#room_description').val(room.description).prop('disabled', false);
  },

  populateViewModal(room) {
    $('#view-room-number').text(`Room ${room.number}`);
    $('#view-room-apartment').text(`Apartment ${room.apartment}`);
    $('#view-room-building').text(`Building ${room.building}`);
    $('#view-room-type').text(room.type);
    $('#view-room-gender-restriction').text(room.gender_restriction);
    $('#view-room-is-active').text(room.active ? 'Active' : 'Inactive');
    $('#view-room-created').text(Utils.formatDate(room.created_at));
    $('#view-room-updated').text(Utils.formatDate(room.updated_at));
    $('#view-room-capacity').text(room.capacity);
    $('#view-room-current-occupancy').text(room.current_occupancy);
    $('#view-room-available-capacity').text(room.available_capacity);
  },

  deleteRoom(roomId) {
    ApiService.deleteRoom(roomId)
      .done(() => {
        this.reloadTable();
        Utils.showSuccess('Room has been deleted.');
        StatsManager.loadStats();
      })
      .fail((xhr) => {
        const message = xhr.responseJSON?.message || 'Failed to delete room.';
        Utils.showError(message);
      });
  },

  toggleRoomStatus(roomId, isActivate, $button) {
    const apiCall = isActivate ? ApiService.activateRoom : ApiService.deactivateRoom;
    const successMessage = isActivate ? 'activated' : 'deactivated';
    
    $button.prop('disabled', true);
    
    apiCall(roomId)
      .done((response) => {
        if (response.success) {
          Utils.showSuccess(`Room ${successMessage} successfully`);
          this.reloadTable();
        } else {
          Utils.showError(response.message || 'Operation failed');
        }
      })
      .fail((xhr) => {
        Utils.showError(xhr.responseJSON?.message || 'Operation failed');
      })
      .always(() => {
        $button.prop('disabled', false);
      });
  },

  reloadTable() {
    $(SELECTORS.table).DataTable().ajax.reload(null, false);
  }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
const SearchManager = {
  init() {
    this.bindEvents();
  },

  bindEvents() {
    $(Object.values(SELECTORS.filters).join(', ')).on('keyup change', () => {
      this.reloadTable();
    });
    
    $(SELECTORS.buttons.clearFilters).on('click', () => {
      this.clearFilters();
    });
  },

  clearFilters() {
    $(Object.values(SELECTORS.filters).join(', ')).val('');
    this.reloadTable();
  },

  reloadTable() {
    $(SELECTORS.table).DataTable().ajax.reload();
  }
};

// ===========================
// SELECT MANAGEMENT
// ===========================
const SelectManager = {
  init() {
    this.populateBuildingSelect();
    this.populateApartmentSelect();
  },

  populateBuildingSelect() {
    ApiService.fetchBuildings()
      .done((response) => {
        if (response.success) {
          const $select = $(SELECTORS.filters.building);
          $select.empty().append('<option value="">All</option>');
          
          response.data.forEach(building => {
            $select.append(`<option value="${building.id}">Building ${building.number}</option>`);
          });
        }
      })
      .fail(() => {
        Utils.showError('Failed to load buildings');
      });
  },

  populateApartmentSelect() {
    ApiService.fetchApartments()
      .done((response) => {
        if (response.success) {
          const $select = $(SELECTORS.filters.apartment);
          $select.empty().append('<option value="">All</option>');
          
          const uniqueNumbers = new Set();
          response.data.forEach(apartment => {
            if (!uniqueNumbers.has(apartment.number)) {
              uniqueNumbers.add(apartment.number);
              $select.append(`<option value="${apartment.number}">Apartment ${apartment.number}</option>`);
            }
          });
        }
      })
      .fail(() => {
        Utils.showError('Failed to load apartments');
      });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
const RoomApp = {
  init() {
    // Initialize all managers
    StatsManager.init();
    RoomManager.init();
    SearchManager.init();
    SelectManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
  RoomApp.init();
});
  
</script>
@endpush 