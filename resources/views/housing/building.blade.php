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
        :headers="['Number', 'Total Apartments', 'Total Rooms', 'Gender Restriction', 'Active', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'total_apartments', 'name' => 'total_apartments'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'gender_restriction', 'name' => 'gender_restriction'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
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

// ===========================
// CONSTANTS AND CONFIGURATION
// ===========================
const ROUTES = {
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

  showConfirmDialog(options) {
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

  resetForm(formId) {
    $(`#${formId}`)[0].reset();
  },

  setFormData(formId, data) {
    const form = $(`#${formId}`);
    Object.keys(data).forEach(key => {
      const element = form.find(`[name="${key}"]`);
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
// API SERVICE LAYER
// ===========================
const ApiService = {
  request: (options) => $.ajax(options),

  fetchStats: () => ApiService.request({
    url: ROUTES.buildings.stats,
    method: 'GET'
  }),

  fetchBuilding: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.buildings.show, id),
    method: 'GET'
  }),

  saveBuilding: (data, id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.buildings.update, id),
    method: 'PUT',
    data
  }),

  createBuilding: (data) => ApiService.request({
    url: ROUTES.buildings.store,
    method: 'POST',
    data
  }),

  deleteBuilding: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.buildings.destroy, id),
    method: 'DELETE'
  }),

  activateBuilding: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.buildings.activate, id),
    method: 'PATCH'
  }),

  deactivateBuilding: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.buildings.deactivate, id),
    method: 'PATCH'
  })
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================
const StatsManager = {
  elements: {
    buildings: 'buildings',
    maleBuildings: 'buildings-male',
    femaleBuildings: 'buildings-female'
  },

  init() {
    this.loadStats();
  },

  loadStats() {
    this.toggleAllLoadingStates(true);

    ApiService.fetchStats()
      .done((response) => {
        this.handleStatsSuccess(response);
      })
      .fail(() => {
        this.handleStatsError();
      })
      .always(() => {
        this.toggleAllLoadingStates(false);
      });
  },

  handleStatsSuccess(response) {
    if (response.success) {
      this.updateStatElement(this.elements.buildings, response.data.total);
      this.updateStatElement(this.elements.maleBuildings, response.data.male);
      this.updateStatElement(this.elements.femaleBuildings, response.data.female);
    } else {
      this.setAllStatsToNA();
    }
  },

  handleStatsError() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load building statistics');
  },

  updateStatElement(elementId, data) {
    $(`#${elementId}-value`).text(data.count ?? 'N/A');
    $(`#${elementId}-last-updated`).text(data.lastUpdateTime ?? 'N/A');
  },

  setAllStatsToNA() {
    Object.values(this.elements).forEach(elementId => {
      $(`#${elementId}-value`).text('N/A');
      $(`#${elementId}-last-updated`).text('N/A');
    });
  },

  toggleAllLoadingStates(isLoading) {
    Object.values(this.elements).forEach(elementId => {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// BUILDING CRUD MANAGEMENT
// ===========================
const BuildingManager = {
  currentBuildingId: null,
  elements: {
    addBtn: '#addBuildingBtn',
    form: '#buildingForm',
    modal: '#buildingModal',
    modalTitle: '#buildingModalTitle',
    viewModal: '#viewBuildingModal',
    table: '#buildings-table',
    doubleRoomsCheckbox: '#has_double_rooms',
    doubleRoomsSection: '#apartments-double-rooms-section'
  },

  init() {
    this.bindEvents();
  },

  bindEvents() {
    this.handleAddBuilding();
    this.handleEditBuilding();
    this.handleViewBuilding();
    this.handleDeleteBuilding();
    this.handleFormSubmit();
    this.handleActivateDeactivate();
  },

  handleAddBuilding() {
    $(document).on('click', this.elements.addBtn, () => {
      this.openModal('add');
    });
  },

  handleEditBuilding() {
    $(document).on('click', '.editBuildingBtn', (e) => {
      const buildingId = $(e.currentTarget).data('id');
      this.openModal('edit', buildingId);
    });
  },

  handleViewBuilding() {
    $(document).on('click', '.viewBuildingBtn', (e) => {
      const buildingId = $(e.currentTarget).data('id');
      this.viewBuilding(buildingId);
    });
  },

  handleDeleteBuilding() {
    $(document).on('click', '.deleteBuildingBtn', (e) => {
      const buildingId = $(e.currentTarget).data('id');
      this.deleteBuilding(buildingId);
    });
  },

  handleFormSubmit() {
    $(this.elements.form).on('submit', (e) => {
      e.preventDefault();
      this.saveBuilding();
    });
  },

  handleActivateDeactivate() {
    $(document).on('click', '.activateBuildingBtn, .deactivateBuildingBtn', (e) => {
      e.preventDefault();
      const $btn = $(e.currentTarget);
      const id = $btn.data('id');
      const isActivate = $btn.hasClass('activateBuildingBtn');
      this.toggleBuildingStatus(id, isActivate, $btn);
    });
  },

  openModal(mode, buildingId = null) {
    this.currentBuildingId = buildingId;
    this.resetModalState();

    if (mode === 'add') {
      this.setupAddModal();
    } else if (mode === 'edit') {
      this.setupEditModal(buildingId);
    }
  },

  resetModalState() {
    Utils.resetForm('buildingForm');
    $(this.elements.doubleRoomsCheckbox).prop('checked', false);
    $(this.elements.doubleRoomsSection).hide().empty();
    $('.edit-hide').show().find('input[type="number"], select, textarea').prop('required', true).prop('disabled', false);
    $(this.elements.doubleRoomsCheckbox).prop('required', false).prop('disabled', false);
  },

  setupAddModal() {
    $(this.elements.modalTitle).text('Add Building');
    $(this.elements.modal).modal('show');
  },

  setupEditModal(buildingId) {
    $(this.elements.modalTitle).text('Edit Building');
    
    ApiService.fetchBuilding(buildingId)
      .done((response) => {
        if (response.success) {
          this.populateEditForm(response.data);
          $(this.elements.modal).modal('show');
        }
      })
      .fail(() => {
        $(BuildingManager.elements.modal).modal('hide');
        Utils.showError('Failed to load building data');
      });
  },

  populateEditForm(building) {
    $('#building_number').val(building.number).prop('disabled', false);
    $('#building_gender_restriction').val(building.gender_restriction).prop('disabled', false);
    $('#building_active').val(building.active).prop('disabled', false);
    
    $('.edit-hide').hide().find('input, select, textarea').prop('required', false).prop('disabled', true);
    $(this.elements.doubleRoomsCheckbox).prop('checked', building.has_double_rooms).prop('disabled', false);
    
    if (building.has_double_rooms) {
      $(this.elements.doubleRoomsSection).show();
      DoubleRoomManager.renderDoubleRoomSelectors();
    } else {
      $(this.elements.doubleRoomsSection).hide().empty();
    }
  },

  viewBuilding(buildingId) {
    ApiService.fetchBuilding(buildingId)
      .done((response) => {
        if (response.success) {
          this.populateViewModal(response.data);
          $(this.elements.viewModal).modal('show');
        }
      })
      .fail(() => {
        $(BuildingManager.elements.viewModal).modal('hide');
        Utils.showError('Failed to load building data');
      });
  },

  populateViewModal(building) {
    $('#view-building-number').text(building.number);
    $('#view-building-total-apartments').text(building.total_apartments);
    $('#view-building-total-rooms').text(building.total_rooms);
    $('#view-building-gender-restriction').text(building.gender_restriction);
    $('#view-building-is-active').text(building.active ? 'Active' : 'Inactive');
    $('#view-building-created').text(new Date(building.created_at).toLocaleString());
  },

  saveBuilding() {
    const formData = $(this.elements.form).serialize();

    let apiCall;
    if (this.currentBuildingId) {
        // Edit mode
        apiCall = ApiService.saveBuilding(formData, this.currentBuildingId);
    } else {
        // Add mode
        apiCall = ApiService.createBuilding(formData);
    }

    apiCall
      .done(() => {
        this.handleSaveSuccess();
      })
      .fail((xhr) => {
        this.handleSaveError(xhr);
      });
  },

  handleSaveSuccess() {
    $(this.elements.modal).modal('hide');
    $(this.elements.table).DataTable().ajax.reload(null, false);
    Utils.showSuccess('Building has been saved successfully.');
    StatsManager.loadStats();
  },

  handleSaveError(xhr) {
    $(this.elements.modal).modal('hide');
    const message = xhr.responseJSON?.message || 'An error occurred. Please check your input.';
    Utils.showError(message);
  },

  deleteBuilding(buildingId) {
    Utils.showConfirmDialog({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.performDelete(buildingId);
      }
    });
  },

  performDelete(buildingId) {
    ApiService.deleteBuilding(buildingId)
      .done(() => {
        $(this.elements.table).DataTable().ajax.reload(null, false);
        Utils.showSuccess('Building has been deleted.');
        StatsManager.loadStats();
      })
      .fail((xhr) => {
        const message = xhr.responseJSON?.message || 'Failed to delete building.';
        Utils.showError(message);
      });
  },

  toggleBuildingStatus(id, isActivate, $btn) {
    const action = isActivate ? 'activate' : 'deactivate';
    const apiCall = isActivate ? ApiService.activateBuilding : ApiService.deactivateBuilding;
    
    const options = {
      title: `${isActivate ? 'Activate' : 'Deactivate'} Building?`,
      text: `Are you sure you want to ${action} this building?`,
      confirmButtonText: `Yes, ${action} it!`
    };

    Utils.showConfirmDialog(options).then((result) => {
      if (result.isConfirmed) {
        this.performStatusToggle(id, isActivate, apiCall, $btn);
      }
    });
  },

  performStatusToggle(id, isActivate, apiCall, $btn) {
    $btn.prop('disabled', true);
    
    apiCall(id)
      .done((response) => {
        if (response.success) {
          Utils.showSuccess(`Building ${isActivate ? 'activated' : 'deactivated'} successfully`);
          $(this.elements.table).DataTable().ajax.reload(null, false);
        } else {
          Utils.showError(response.message || 'Operation failed');
        }
      })
      .fail((xhr) => {
        Utils.showError(xhr.responseJSON?.message || 'Operation failed');
      })
      .always(() => {
        $btn.prop('disabled', false);
      });
  }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
const SearchManager = {
  elements: {
    filters: '#search_gender_restriction, #search_active',
    clearBtn: '#clearBuildingFiltersBtn',
    table: '#buildings-table'
  },

  init() {
    this.bindEvents();
  },

  bindEvents() {
    this.initializeAdvancedSearch();
    this.handleClearFilters();
  },

  initializeAdvancedSearch() {
    $(this.elements.filters).on('keyup change', () => {
      $(this.elements.table).DataTable().ajax.reload();
    });
  },

  handleClearFilters() {
    $(this.elements.clearBtn).on('click', () => {
      $(this.elements.filters).val('');
      $(this.elements.table).DataTable().ajax.reload();
    });
  }
};

// ===========================
// DOUBLE ROOM MANAGER
// ===========================
const DoubleRoomManager = {
  elements: {
    checkbox: '#has_double_rooms',
    section: '#apartments-double-rooms-section',
    modal: '#buildingModal',
    totalApartments: '#building_total_apartments',
    roomsPerApartment: '#building_rooms_per_apartment'
  },

  init() {
    this.bindEvents();
  },

  bindEvents() {
    this.handleDoubleRoomToggle();
    this.handleModalShow();
    this.handleInputChange();
    this.handleDocumentReady();
  },

  handleDoubleRoomToggle() {
    $(this.elements.checkbox).on('change', (e) => {
      if ($(e.currentTarget).is(':checked')) {
        this.showDoubleRoomSection();
      } else {
        this.hideDoubleRoomSection();
      }
    });
  },

  handleModalShow() {
    $(this.elements.modal).on('show.bs.modal', () => {
      if ($(this.elements.checkbox).is(':checked')) {
        this.showDoubleRoomSection();
      } else {
        this.hideDoubleRoomSection();
      }
    });
  },

  handleInputChange() {
    $(`${this.elements.totalApartments}, ${this.elements.roomsPerApartment}`).on('input', () => {
      if ($(this.elements.checkbox).is(':checked')) {
        this.renderDoubleRoomSelectors();
      }
    });
  },

  handleDocumentReady() {
    $(document).ready(() => {
      if ($(this.elements.checkbox).is(':checked')) {
        this.showDoubleRoomSection();
      } else {
        this.hideDoubleRoomSection();
      }
    });
  },

  showDoubleRoomSection() {
    $(this.elements.section).show();
    this.renderDoubleRoomSelectors();
  },

  hideDoubleRoomSection() {
    $(this.elements.section).hide().empty();
  },

  renderDoubleRoomSelectors() {
    const totalApartments = parseInt($(this.elements.totalApartments).val());
    const roomsPerApartment = parseInt($(this.elements.roomsPerApartment).val());
    const $section = $(this.elements.section);
    
    $section.empty();
    
    if (!totalApartments || !roomsPerApartment) return;
    
    const accordionHtml = this.generateAccordionHtml(totalApartments, roomsPerApartment);
    const boxHtml = this.wrapInScrollableBox(accordionHtml);
    
    $section.append(boxHtml);
  },

  generateAccordionHtml(totalApartments, roomsPerApartment) {
    let accordionHtml = '<div class="accordion" id="apartmentsAccordion">';
    
    for (let i = 1; i <= totalApartments; i++) {
      accordionHtml += this.generateApartmentAccordionItem(i, roomsPerApartment);
    }
    
    accordionHtml += '</div>';
    return accordionHtml;
  },

  generateApartmentAccordionItem(apartmentNumber, roomsPerApartment) {
    const collapseId = `apartment${apartmentNumber}Collapse`;
    const headingId = `apartment${apartmentNumber}Heading`;
    const checkboxes = this.generateRoomCheckboxes(apartmentNumber, roomsPerApartment);
    
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

  generateRoomCheckboxes(apartmentNumber, roomsPerApartment) {
    let checkboxes = '';
    
    for (let j = 1; j <= roomsPerApartment; j++) {
      checkboxes += `
        <label class="me-2">
          <input type="checkbox" name="apartments[${apartmentNumber-1}][double_rooms][]" value="${j}"> 
          Room ${j}
        </label>
      `;
    }
    
    return checkboxes;
  },

  wrapInScrollableBox(content) {
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
const BuildingApp = {
  init() {
    StatsManager.init();
    BuildingManager.init();
    SearchManager.init();
    DoubleRoomManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
  BuildingApp.init();
});

</script>
@endpush 