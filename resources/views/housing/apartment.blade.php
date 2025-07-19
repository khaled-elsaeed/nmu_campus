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
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearApartmentFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Number', 'Building', 'Total Rooms', 'Gender Restriction', 'Active', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'number', 'name' => 'apartment_number'],
            ['data' => 'building', 'name' => 'building_number'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'gender_restriction', 'name' => 'building_gender_restriction'],
            ['data' => 'active', 'name' => 'active'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.apartments.datatable')"
        :table-id="'apartments-table'"
        :filter-fields="['search_apartment_number','search_building_id','search_gender_restriction']"
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
// ===========================
// CONSTANTS AND CONFIGURATION
// ===========================
const ROUTES = {
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

const SELECTORS = {
  table: '#apartments-table',
  modals: {
    view: '#viewApartmentModal'
  },
  buttons: {
    view: '.viewApartmentBtn',
    delete: '.deleteApartmentBtn',
    activate: '.activateApartmentBtn',
    deactivate: '.deactivateApartmentBtn',
    clearFilters: '#clearApartmentFiltersBtn'
  },
  filters: {
    apartmentNumber: '#search_apartment_number',
    buildingId: '#search_building_id',
    genderRestriction: '#search_gender_restriction'
  },
  stats: {
    total: '#apartments',
    male: '#apartments-male',
    female: '#apartments-female'
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

  showConfirm(options) {
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

  setElementText(selector, text) {
    $(selector).text(text || '--');
  },

  formatDate(dateString) {
    return new Date(dateString).toLocaleString();
  },

  disableButton($button, disabled = true) {
    $button.prop('disabled', disabled);
  }
};

// ===========================
// API SERVICE LAYER
// ===========================
const ApiService = {
  request: (options) => $.ajax(options),

  fetchStats: () => ApiService.request({ 
    url: ROUTES.apartments.stats, 
    method: 'GET' 
  }),

  fetchApartment: (id) => ApiService.request({ 
    url: Utils.replaceRouteId(ROUTES.apartments.show, id), 
    method: 'GET' 
  }),

  saveApartment: (data, id) => ApiService.request({ 
    url: Utils.replaceRouteId(ROUTES.apartments.update, id), 
    method: 'PUT', 
    data 
  }),

  deleteApartment: (id) => ApiService.request({ 
    url: Utils.replaceRouteId(ROUTES.apartments.destroy, id), 
    method: 'DELETE' 
  }),

  activateApartment: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.apartments.activate, id),
    method: 'PATCH'
  }),

  deactivateApartment: (id) => ApiService.request({
    url: Utils.replaceRouteId(ROUTES.apartments.deactivate, id),
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
  loadStats() {
    this.toggleAllStatsLoading(true);
    
    ApiService.fetchStats()
      .done((response) => {
        if (response.success) {
          this.updateStatsDisplay(response.data);
        } else {
          this.setStatsError();
        }
      })
      .fail(() => {
        this.setStatsError();
        Utils.showError(MESSAGES.error.loadStats);
      })
      .always(() => {
        this.toggleAllStatsLoading(false);
      });
  },

  toggleAllStatsLoading(isLoading) {
    const statsKeys = Object.keys(SELECTORS.stats);
    statsKeys.forEach(key => {
      const elementId = SELECTORS.stats[key].replace('#', '');
      Utils.toggleLoadingState(elementId, isLoading);
    });
  },

  updateStatsDisplay(data) {
    this.updateSingleStat('total', data.total);
    this.updateSingleStat('male', data.male);
    this.updateSingleStat('female', data.female);
  },

  updateSingleStat(type, data) {
    const elementId = SELECTORS.stats[type].replace('#', '');
    Utils.setElementText(`#${elementId}-value`, data.count);
    Utils.setElementText(`#${elementId}-last-updated`, data.lastUpdateTime);
  },

  setStatsError() {
    const statsKeys = Object.keys(SELECTORS.stats);
    statsKeys.forEach(key => {
      const elementId = SELECTORS.stats[key].replace('#', '');
      Utils.setElementText(`#${elementId}-value`, 'N/A');
      Utils.setElementText(`#${elementId}-last-updated`, 'N/A');
    });
  }
};

// ===========================
// APARTMENT CRUD OPERATIONS
// ===========================
const ApartmentManager = {
  init() {
    this.bindEvents();
  },

  bindEvents() {
    $(document).on('click', SELECTORS.buttons.view, (e) => this.handleViewApartment(e));
    $(document).on('click', SELECTORS.buttons.delete, (e) => this.handleDeleteApartment(e));
    $(document).on('click', SELECTORS.buttons.activate, (e) => this.handleActivateApartment(e));
    $(document).on('click', SELECTORS.buttons.deactivate, (e) => this.handleDeactivateApartment(e));
  },

  handleViewApartment(e) {
    const apartmentId = $(e.currentTarget).data('id');
    
    ApiService.fetchApartment(apartmentId)
      .done((response) => {
        if (response.success) {
          this.populateViewModal(response.data);
          $(SELECTORS.modals.view).modal('show');
        }
      })
      .fail(() => {
        $(SELECTORS.modals.view).modal('hide');
        Utils.showError(MESSAGES.error.loadApartment);
      });
  },

  handleDeleteApartment(e) {
    const apartmentId = $(e.currentTarget).data('id');
    
    Utils.showConfirm(MESSAGES.confirm.delete)
      .then((result) => {
        if (result.isConfirmed) {
          this.deleteApartment(apartmentId);
        }
      });
  },

  handleActivateApartment(e) {
    e.preventDefault();
    const $btn = $(e.currentTarget);
    const id = $btn.data('id');
    
    this.toggleApartmentStatus(id, true, $btn);
  },

  handleDeactivateApartment(e) {
    e.preventDefault();
    const $btn = $(e.currentTarget);
    const id = $btn.data('id');
    
    this.toggleApartmentStatus(id, false, $btn);
  },

  toggleApartmentStatus(id, isActivate, $btn) {
    const confirmOptions = isActivate ? MESSAGES.confirm.activate : MESSAGES.confirm.deactivate;
    const apiCall = isActivate ? ApiService.activateApartment : ApiService.deactivateApartment;
    const successMessage = isActivate ? MESSAGES.success.activated : MESSAGES.success.deactivated;

    Utils.showConfirm(confirmOptions)
      .then((result) => {
        if (result.isConfirmed) {
          this.executeStatusToggle(id, apiCall, successMessage, $btn);
        }
      });
  },

  executeStatusToggle(id, apiCall, successMessage, $btn) {
    Utils.disableButton($btn);
    
    apiCall(id)
      .done((response) => {
        if (response.success) {
          Utils.showSuccess(successMessage);
          this.reloadTable();
        } else {
          Utils.showError(response.message || MESSAGES.error.operationFailed);
        }
      })
      .fail((xhr) => {
        Utils.showError(xhr.responseJSON?.message || MESSAGES.error.operationFailed);
      })
      .always(() => {
        Utils.disableButton($btn, false);
      });
  },

  deleteApartment(apartmentId) {
    ApiService.deleteApartment(apartmentId)
      .done(() => {
        this.reloadTable();
        Utils.showSuccess(MESSAGES.success.deleted);
        StatsManager.loadStats();
      })
      .fail((xhr) => {
        const message = xhr.responseJSON?.message || MESSAGES.error.deleteApartment;
        Utils.showError(message);
      });
  },

  populateViewModal(apartment) {
    Utils.setElementText('#view-apartment-number', apartment.number);
    Utils.setElementText('#view-apartment-building', apartment.building);
    Utils.setElementText('#view-apartment-total-rooms', apartment.total_rooms);
    Utils.setElementText('#view-apartment-gender-restriction', apartment.gender_restriction);
    Utils.setElementText('#view-apartment-is-active', apartment.active ? 'Active' : 'Inactive');
    Utils.setElementText('#view-apartment-created', Utils.formatDate(apartment.created_at));
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
    const filterSelectors = Object.values(SELECTORS.filters).join(', ');
    $(filterSelectors).on('change', () => this.handleFilterChange());
    $(SELECTORS.buttons.clearFilters).on('click', () => this.clearFilters());
  },

  handleFilterChange() {
    $(SELECTORS.table).DataTable().ajax.reload();
  },

  clearFilters() {
    const filterSelectors = Object.values(SELECTORS.filters);
    filterSelectors.forEach(selector => {
      $(selector).val('').trigger('change');
    });
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
          this.populateSelect(SELECTORS.filters.buildingId, response.data, 'id', 'number');
        }
      })
      .fail(() => {
        console.error('Failed to load buildings');
      });
  },

  populateApartmentSelect() {
    ApiService.fetchApartments()
      .done((response) => {
        if (response.success) {
          this.populateApartmentNumbers(response.data);
        }
      })
      .fail(() => {
        console.error('Failed to load apartments');
      });
  },

  populateSelect(selector, data, valueField, textField) {
    const $select = $(selector);
    $select.empty().append('<option value="">All</option>');
    
    data.forEach(item => {
      $select.append(`<option value="${item[valueField]}">${item[textField]}</option>`);
    });
  },

  populateApartmentNumbers(apartments) {
    const $select = $(SELECTORS.filters.apartmentNumber);
    $select.empty().append('<option value="">All</option>');
    
    const uniqueNumbers = new Set();
    apartments.forEach(apartment => {
      if (!uniqueNumbers.has(apartment.number)) {
        uniqueNumbers.add(apartment.number);
        $select.append(`<option value="${apartment.number}">${apartment.number}</option>`);
      }
    });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================

const ApartmentApp = {
  init() {
    StatsManager.loadStats();
  ApartmentManager.init();
  SearchManager.init();
  SelectManager.init();
  }
};

// ===========================
// APPLICATION INITIALIZATION
// ===========================
$(document).ready(() => {
  ApartmentApp.init();
});
</script>
@endpush 