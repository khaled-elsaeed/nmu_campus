@extends('layouts.home')

@section('title', 'Insurance Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              label="Total Insurances"
              id="insurances"
              :subStats="[
                  'male' => [
                      'label' => 'Male Insurances',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Female Insurances', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="info"
              icon="bx bx-check-circle"
              label="Active Insurances"
              id="insurances-active"
              :subStats="[
                  'male' => [
                      'label' => 'Active Male Insurances',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Active Female Insurances', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="danger"
              icon="bx bx-check-circle"
              label="Refunded Insurances"
              id="insurances-refunded"
              :subStats="[
                  'male' => [
                      'label' => 'Refunded Male Insurances',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Refunded Female Insurances', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="warning"
              icon="bx bx-check-circle"
              label="Carried Over Insurances"
              id="insurances-carried-over"
              :subStats="[
                  'male' => [
                      'label' => 'Carried Over Male Insurances',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Carried Over Female Insurances', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
        </div>
    </div>
    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Insurances"
        description="Manage insurances and their details."
        icon="bx bx-shield"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">

            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#insuranceSearchCollapse" aria-expanded="false" aria-controls="insuranceSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> Search
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedInsuranceSearch" 
        collapseId="insuranceSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_status" class="form-label">Status:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="refunded">Refunded</option>
                <option value="carried_over">Carried Over</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_reservation_number" class="form-label">Reservation Number:</label>
            <input type="text" class="form-control" id="search_reservation_number" name="search_reservation_number">
        </div>
        <div class="col-md-4">
            <label for="search_national_id" class="form-label">National ID:</label>
            <input type="text" class="form-control" id="search_national_id" name="search_national_id">
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearInsuranceFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['User', 'National ID','Reservation Number', 'Amount', 'Status', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'user_name', 'name' => 'user_name'],
            ['data' => 'national_id', 'name' => 'national_id'],
            ['data' => 'reservation_number', 'name' => 'reservation_number'],
            ['data' => 'amount', 'name' => 'amount'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('insurances.datatable')"
        :table-id="'insurances-table'"
        :filter-fields="['search_status','search_reservation_number','search_national_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Edit Insurance Modal --}}
    <x-ui.modal 
        id="insuranceModal"
        title="Edit Insurance"
        :scrollable="true"
        class="insurance-modal"
    >
        <x-slot name="slot">
            <form id="insuranceForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="insurance_reservation_number" class="form-label">Reservation Number</label>
                        <input type="text" id="insurance_reservation_number" name="reservation_number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="insurance_amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" id="insurance_amount" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="insurance_status" class="form-label">Status</label>
                        <select id="insurance_status" name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="refunded">Refunded</option>
                            <option value="carried_over">Carried Over</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="insuranceForm">Save</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Insurance Modal --}}
    <x-ui.modal 
        id="viewInsuranceModal"
        title="Insurance Details"
        :scrollable="true"
        class="view-insurance-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">User Name:</label>
                    <p id="view-insurance-user-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">National ID:</label>
                    <p id="view-insurance-national-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Reservation Number:</label>
                    <p id="view-insurance-reservation-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Amount:</label>
                    <p id="view-insurance-amount" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Status:</label>
                    <p id="view-insurance-status" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-insurance-created-at" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Updated At:</label>
                    <p id="view-insurance-updated-at" class="mb-0"></p>
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
 * Insurance Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - InsuranceManager: Handles CRUD and actions for insurances
 * - SearchManager: Handles search functionality
 * - InsuranceApp: Initializes all managers
 *
 * Uses global @utils.js functions for common utilities.
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  insurances: {
    stats: '{{ route('insurances.stats') }}',
    show: '{{ route('insurances.show', ':id') }}',
    store: '{{ route('insurances.store') }}',
    update: '{{ route('insurances.update', ':id') }}',
    destroy: '{{ route('insurances.destroy', ':id') }}',
    all: '{{ route('insurances.all') }}',
    cancel: '{{ route('insurances.cancel', ':id') }}',
    refund: '{{ route('insurances.refund', ':id') }}',
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
   * Fetch insurance statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.insurances.stats, method: 'GET' });
  },
  /**
   * Fetch a single insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchInsurance: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.insurances.show, id), method: 'GET' });
  },
  /**
   * Save (update) an insurance
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveInsurance: function(data, id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.insurances.update, id), method: 'PUT', data: data });
  },
  /**
   * Create a new insurance
   * @param {object} data
   * @returns {jqXHR}
   */
  createInsurance: function(data) {
    return ApiService.request({ url: ROUTES.insurances.store, method: 'POST', data: data });
  },
  /**
   * Delete an insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteInsurance: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.insurances.destroy, id), method: 'DELETE' });
  },
  cancelInsurance: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.insurances.cancel, id), method: 'POST' });
  },
  refundInsurance: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.insurances.refund, id), method: 'POST' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: [
    'insurances',
    'insurances-active',
    'insurances-refunded',
    'insurances-carried-over',
  ],
  subStatsConfig: {
    'insurances': ['male', 'female'],
    'insurances-active': ['male', 'female'],
    'insurances-refunded': ['male', 'female'],
    'insurances-carried-over': ['male', 'female'],
  },
});
  
// ===========================
// INSURANCE MANAGER
// ===========================
var InsuranceManager = {
  currentInsuranceId: null,
  /**
   * Initialize insurance manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind all insurance-related events
   */
  bindEvents: function() {
    this.handleEditInsurance();
    this.handleViewInsurance();
    this.handleDeleteInsurance();
    this.handleFormSubmit();
    this.handleCancelInsurance();
    this.handleRefundInsurance();
  },
  /**
   * Handle edit insurance button click
   */
  handleEditInsurance: function() {
    var self = this;
    $(document).on('click', '.editInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      ApiService.fetchInsurance(insuranceId)
        .done(function(response) {
          if (response.success) {
            InsuranceManager.populateEditForm(response.data);
            $('#insuranceModal').modal('show');
          }
        })
        .fail(function() {
          $('#insuranceModal').modal('hide');
          Utils.showError('Failed to load insurance data');
        });
    });
  },
  /**
   * Handle view insurance button click
   */
  handleViewInsurance: function() {
    $(document).on('click', '.viewInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      InsuranceManager.viewInsurance(insuranceId);
    });
  },
  /**
   * Handle delete insurance button click
   */
  handleDeleteInsurance: function() {
    $(document).on('click', '.deleteInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      InsuranceManager.deleteInsurance(insuranceId);
    });
  },
  /**
   * Handle form submit
   */
  handleFormSubmit: function() {
    var self = this;
    $('#insuranceForm').on('submit', function(e) {
      e.preventDefault();
      self.saveInsurance();
    });
  },
  /**
   * Handle cancel insurance button click
   */
  handleCancelInsurance: function() {
    $(document).on('click', '.cancelInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      Utils.showConfirmDialog({
        title: 'Are you sure?',
        text: 'This will cancel the insurance.',
        confirmButtonText: 'Yes, cancel it!'
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.cancelInsurance(insuranceId)
            .done(function() {
              $('#insurances-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Insurance has been cancelled.');
              StatsManager.load();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError ? Utils.handleAjaxError(xhr, 'Failed to cancel insurance.') : Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to cancel insurance.');
            });
        }
      });
    });
  },
  /**
   * Handle refund insurance button click
   */
  handleRefundInsurance: function() {
    $(document).on('click', '.refundInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      Utils.showConfirmDialog({
        title: 'Are you sure?',
        text: 'This will refund the insurance.',
        confirmButtonText: 'Yes, refund it!'
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.refundInsurance(insuranceId)
            .done(function() {
              $('#insurances-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Insurance has been refunded.');
              StatsManager.load();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError ? Utils.handleAjaxError(xhr, 'Failed to refund insurance.') : Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to refund insurance.');
            });
        }
      });
    });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(insurance) {
    $('#insurance_reservation_number').val(insurance.reservation_number);
    $('#insurance_amount').val(insurance.amount);
    $('#insurance_status').val(insurance.status);
  },
  /**
   * View insurance details
   */
  viewInsurance: function(insuranceId) {
    ApiService.fetchInsurance(insuranceId)
      .done(function(response) {
        if (response.success) {
          InsuranceManager.populateViewModal(response.data);
          $('#viewInsuranceModal').modal('show');
        }
      })
      .fail(function() {
        $('#viewInsuranceModal').modal('hide');
        Utils.showError('Failed to load insurance data');
      });
  },
  /**
   * Populate view modal
   */
  populateViewModal: function(insurance) {
    $('#view-insurance-user-name').text(insurance.user_name || '--');
    $('#view-insurance-national-id').text(insurance.national_id || '--');
    $('#view-insurance-reservation-number').text(insurance.reservation_number || '--');
    $('#view-insurance-amount').text(insurance.amount || '--');
    $('#view-insurance-status').text(insurance.status || '--');
    $('#view-insurance-created-at').text(insurance.created_at || '--');
    $('#view-insurance-updated-at').text(insurance.updated_at || '--');
  },
  /**
   * Save insurance
   */
  saveInsurance: function() {
    var formData = $('#insuranceForm').serialize();
    var apiCall;
    if (this.currentInsuranceId) {
      apiCall = ApiService.saveInsurance(formData, this.currentInsuranceId);
    } else {
      apiCall = ApiService.createInsurance(formData);
    }
    var self = this;
    apiCall
      .done(function() {
        self.handleSaveSuccess();
      })
      .fail(function(xhr) {
        // Use global Utils.handleAjaxError if available, otherwise fallback
        if (Utils.handleAjaxError) {
          Utils.handleAjaxError(xhr, 'An error occurred. Please check your input.');
        } else {
          $('#insuranceModal').modal('hide');
          var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
          Utils.showError(message);
        }
      });
  },
  /**
   * Handle successful save
   */
  handleSaveSuccess: function() {
    $('#insuranceModal').modal('hide');
    $('#insurances-table').DataTable().ajax.reload(null, false);
    Utils.showSuccess('Insurance has been saved successfully.');
    StatsManager.load();
  },
  /**
   * Delete insurance
   */
  deleteInsurance: function(insuranceId) {
    Utils.showConfirmDialog({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
      if (result.isConfirmed) {
        InsuranceManager.performDelete(insuranceId);
      }
    });
  },
  /**
   * Perform actual deletion
   */
  performDelete: function(insuranceId) {
    ApiService.deleteInsurance(insuranceId)
      .done(function() {
        $('#insurances-table').DataTable().ajax.reload(null, false);
        Utils.showSuccess('Insurance has been deleted.');
        StatsManager.load();
      })
      .fail(function(xhr) {
        Utils.handleAjaxError ? Utils.handleAjaxError(xhr, 'Failed to delete insurance.') : Utils.showError(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete insurance.');
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
    $('#search_status, #search_reservation_number, #search_national_id').on('keyup change', function() {
      $('#insurances-table').DataTable().ajax.reload();
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearInsuranceFiltersBtn').on('click', function() {
      $('#search_status, #search_reservation_number, #search_national_id').val('');
      $('#insurances-table').DataTable().ajax.reload();
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
    // Initialize select2 for status dropdown
    Utils.initSelect2('#search_status', {
      placeholder: 'Select Status',
      allowClear: true
    });

    // Handle search input changes
    $('#search_status, #search_reservation_number, #search_national_id').on('keyup change', function() {
      Utils.reloadDataTable('#insurances-table');
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearInsuranceFiltersBtn').on('click', function() {
      Utils.clearValidation('#advancedInsuranceSearch');
      $('#search_status').val('').trigger('change');
      $('#search_reservation_number').val('');
      $('#search_national_id').val('');
      Utils.reloadDataTable('#insurances-table');
      Utils.showSuccess('Filters cleared', true, 'top-end');
    });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
var InsuranceApp = {
  /**
   * Initialize the entire application
   */
  init: function() {
    StatsManager.init();
    InsuranceManager.init();
    SearchManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  InsuranceApp.init();
});

</script>
@endpush 