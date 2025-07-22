@extends('layouts.home')

@section('title', 'Insurance Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="primary" icon="bx bx-shield" label="Total Insurances" id="insurances" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="success" icon="bx bx-check-circle" label="Active" id="insurances-active" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="danger" icon="bx bx-undo" label="Refunded" id="insurances-refunded" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="info" icon="bx bx-transfer" label="Carried Over" id="insurances-carried-over" />
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="warning" icon="bx bx-block" label="Cancelled" id="insurances-cancelled" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Insurances"
        description="Manage insurances and their details."
        icon="bx bx-shield"
    >
        <button class="btn btn-primary mx-2" id="addInsuranceBtn">
            <i class="bx bx-plus me-1"></i> Add Insurance
        </button>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#insuranceSearchCollapse" aria-expanded="false" aria-controls="insuranceSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
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
    {{-- Add/Edit Insurance Modal --}}
    <x-ui.modal 
        id="insuranceModal"
        title="Add/Edit Insurance"
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

    {{-- Details Modal --}}
    <x-ui.modal 
        id="insuranceDetailsModal"
        title="Insurance Details"
        :scrollable="true"
        class="view-insurance-details-modal"
    >
        <x-slot name="slot">
            <pre id="insurance-details-json" class="mb-0"></pre>
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
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - InsuranceManager: Handles CRUD and actions for insurances
 * - InsuranceApp: Initializes all managers
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
   * Fetch insurance statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return this.request({ url: ROUTES.insurances.stats, method: 'GET' });
  },
  /**
   * Fetch a single insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.show, id), method: 'GET' });
  },
  /**
   * Save (update) an insurance
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  saveInsurance: function(data, id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.update, id), method: 'PUT', data: data });
  },
  /**
   * Create a new insurance
   * @param {object} data
   * @returns {jqXHR}
   */
  createInsurance: function(data) {
    return this.request({ url: ROUTES.insurances.store, method: 'POST', data: data });
  },
  /**
   * Delete an insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.destroy, id), method: 'DELETE' });
  },
  fetchInsuranceDetails: function(id) {
    return this.request({ url: ROUTES.insurances.details.replace(':id', id), method: 'GET' });
  },
  cancelInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.cancel, id), method: 'POST' });
  },
  refundInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.refund, id), method: 'POST' });
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
      this.updateStatElement('insurances', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('insurances-active', stats.active.count, stats.active.lastUpdateTime);
      this.updateStatElement('insurances-refunded', stats.refunded.count, stats.refunded.lastUpdateTime);
      this.updateStatElement('insurances-carried-over', stats.carried_over.count, stats.carried_over.lastUpdateTime);
      this.updateStatElement('insurances-cancelled', stats.cancelled.count, stats.cancelled.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load insurance statistics');
  },
  updateStatElement: function(elementId, value, lastUpdateTime) {
    // Hide loader, show value and last updated
    $('#' + elementId + '-value').text(value ?? '0').removeClass('d-none');
    $('#' + elementId + '-loader').addClass('d-none');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--').removeClass('d-none');
    $('#' + elementId + '-last-updated-loader').addClass('d-none');
  },
  setAllStatsToNA: function() {
    ['insurances', 'insurances-active', 'insurances-refunded', 'insurances-carried-over', 'insurances-cancelled'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A').removeClass('d-none');
      $('#' + elementId + '-loader').addClass('d-none');
      $('#' + elementId + '-last-updated').text('N/A').removeClass('d-none');
      $('#' + elementId + '-last-updated-loader').addClass('d-none');
    });
  },
   /**
   * Update a single stat card
   * @param {string} elementId
   * @param {string|number} value
   * @param {string} lastUpdateTime
   */
  toggleAllLoadingStates: function(isLoading) {
    ['insurances', 'insurances-active', 'insurances-refunded', 'insurances-carried-over', 'insurances-cancelled'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

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
    this.handleAddInsurance();
    this.handleEditInsurance();
    this.handleViewInsurance();
    this.handleDeleteInsurance();
    this.handleFormSubmit();
    this.handleVewInsuranceDetails();
    this.handleCancelInsurance();
    this.handleRefundInsurance();
  },
  /**
   * Handle add insurance button click
   */
  handleAddInsurance: function() {
    var self = this;
    $(document).on('click', '#addInsuranceBtn', function() {
      self.openModal('add');
    });
  },
  /**
   * Handle edit insurance button click
   */
  handleEditInsurance: function() {
    var self = this;
    $(document).on('click', '.editInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      self.openModal('edit', insuranceId);
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

  handleVewInsuranceDetails(){
    var self = this;
    $(document).on('click', '.viewDetailsBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      self.viewDetails(insuranceId);
    });
  },
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
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to cancel insurance.';
              Utils.showError(message);
            });
        }
      });
    });
  },
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
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to refund insurance.';
              Utils.showError(message);
            });
        }
      });
    });
  },
  /**
   * Open add/edit modal
   */
  openModal: function(mode, insuranceId) {
    this.currentInsuranceId = insuranceId;
    this.resetModalState();
    if (mode === 'add') {
      this.setupAddModal();
    } else if (mode === 'edit') {
      this.setupEditModal(insuranceId);
    }
  },
  /**
   * Reset modal state
   */
  resetModalState: function() {
    Utils.resetForm('insuranceForm');
  },
  /**
   * Setup add modal
   */
  setupAddModal: function() {
    $('#insuranceModalTitle').text('Add Insurance');
    $('#insuranceModal').modal('show');
  },
  /**
   * Setup edit modal
   */
  setupEditModal: function(insuranceId) {
    $('#insuranceModalTitle').text('Edit Insurance');
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
    apiCall
      .done(function() {
        InsuranceManager.handleSaveSuccess();
      })
      .fail(function(xhr) {
        InsuranceManager.handleSaveError(xhr);
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
   * Handle save error
   */
  handleSaveError: function(xhr) {
    $('#insuranceModal').modal('hide');
    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
    Utils.showError(message);
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
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete insurance.';
        Utils.showError(message);
      });
  },
  viewDetails: function(insuranceId) {
    $('#insurance-details-json').html('<div class="text-center py-4"><span class="spinner-border text-info" role="status"></span></div>');
    $('#insuranceDetailsModal').modal('show');
    ApiService.fetchInsuranceDetails(insuranceId)
      .done(function(response) {
        if (response.success) {
          var details = response.data;
          var html = '';
          if (Array.isArray(details) && details.length > 0) {
            html = '<ul class="list-group">';
            details.forEach(function(item) {
              html += '<li class="list-group-item d-flex align-items-center">'
                + '<i class="bx bx-info-circle text-primary me-2"></i>'
                + '<span class="fw-bold me-2">' + (item.type ? item.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '') + '</span>'
                + (typeof item.amount !== 'undefined' ? '<span class="badge bg-success me-2">' + item.amount + '</span>' : '')
                + (item.description ? '<span class="text-muted">' + item.description + '</span>' : '')
                + '</li>';
            });
            html += '</ul>';
          } else {
            html = '<span>--</span>';
          }
          $('#insurance-details-json').html(html);
        } else {
          $('#insurance-details-json').html('<span class="text-danger">Failed to load details.</span>');
        }
      })
      .fail(function() {
        $('#insurance-details-json').html('<span class="text-danger">Failed to load details.</span>');
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