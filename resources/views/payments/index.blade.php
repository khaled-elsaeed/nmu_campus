@extends('layouts.home')

@section('title', 'Payment Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="primary" icon="bx bx-money" label="Total Payments" id="payments" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="info" icon="bx bx-time-five" label="Pending" id="payments-pending" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="success" icon="bx bx-check-circle" label="Completed" id="payments-completed" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="danger" icon="bx bx-undo" label="Refunded" id="payments-refunded" />
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="warning" icon="bx bx-block" label="Cancelled" id="payments-cancelled" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Payments"
        description="Manage payments and their details."
        icon="bx bx-money"
    >
        <button class="btn btn-primary mx-2" id="addPaymentBtn">
            <i class="bx bx-plus me-1"></i> Add Payment
        </button>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentSearchCollapse" aria-expanded="false" aria-controls="paymentSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedPaymentSearch" 
        collapseId="paymentSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_status" class="form-label">Status:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="refunded">Refunded</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_reservation_id" class="form-label">Reservation ID:</label>
            <input type="text" class="form-control" id="search_reservation_id" name="search_reservation_id">
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearPaymentFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Reservation Number', 'User', 'Amount', 'Status', 'Notes', 'Details', 'Created At', 'Actions']"
        :columns="[
            ['data' => 'reservation_number', 'name' => 'reservation_number'],
            ['data' => 'user_name', 'name' => 'user_name'],
            ['data' => 'amount', 'name' => 'amount'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'notes', 'name' => 'notes'],
            ['data' => 'details', 'name' => 'details', 'orderable' => false, 'searchable' => false],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('payments.datatable')"
        :table-id="'payments-table'"
        :filter-fields="['search_status','search_reservation_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Payment Modal --}}
    <x-ui.modal 
        id="paymentModal"
        title="Add/Edit Payment"
        :scrollable="true"
        class="payment-modal"
    >
        <x-slot name="slot">
            <form id="paymentForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="payment_reservation_id" class="form-label">Reservation ID</label>
                        <input type="number" id="payment_reservation_id" name="reservation_id" class="form-control" required readonly>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" id="payment_amount" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_status" class="form-label">Status</label>
                        <select id="payment_status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="refunded">Refunded</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea id="payment_notes" name="notes" class="form-control"></textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_details" class="form-label">Details (JSON)</label>
                        <textarea id="payment_details" name="details" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="paymentForm">Save</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Payment Modal --}}
    <x-ui.modal 
        id="viewPaymentModal"
        title="Payment Details"
        :scrollable="true"
        class="view-payment-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Reservation Number:</label>
                    <p id="view-payment-reservation-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">User:</label>
                    <p id="view-payment-user" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Amount:</label>
                    <p id="view-payment-amount" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Status:</label>
                    <p id="view-payment-status" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Notes:</label>
                    <p id="view-payment-notes" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Details:</label>
                    <div id="view-payment-details-list"></div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-payment-created" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </x-slot>
    </x-ui.modal>

    {{-- Details Modal --}}
    <x-ui.modal 
        id="paymentDetailsModal"
        title="Payment Details"
        :scrollable="true"
        class="view-payment-details-modal"
    >
        <x-slot name="slot">
            <pre id="payment-details-json" class="mb-0"></pre>
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
 * Payment Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - PaymentManager: Handles CRUD and actions for payments
 * - PaymentApp: Initializes all managers
 *
 * Uses global @utils.js functions for common utilities.
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  payments: {
    stats: '{{ route('payments.stats') }}',
    show: '{{ route('payments.show', ':id') }}',
    store: '{{ route('payments.store') }}',
    update: '{{ route('payments.update', ':id') }}',
    destroy: '{{ route('payments.destroy', ':id') }}',
    details: '{{ route('payments.details', ':id') }}',
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
   * Fetch payment statistics
   * @returns {jqXHR}
   */
  fetchStats: function() {
    return this.request({ url: ROUTES.payments.stats, method: 'GET' });
  },
  /**
   * Fetch a single payment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchPayment: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.payments.show, id), method: 'GET' });
  },
  /**
   * Save (update) a payment
   * @param {object} data
   * @param {string|number} id
   * @returns {jqXHR}
   */
  savePayment: function(data, id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.payments.update, id), method: 'PUT', data: data });
  },
  /**
   * Create a new payment
   * @param {object} data
   * @returns {jqXHR}
   */
  createPayment: function(data) {
    return this.request({ url: ROUTES.payments.store, method: 'POST', data: data });
  },
  /**
   * Delete a payment by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deletePayment: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.payments.destroy, id), method: 'DELETE' });
  },
  fetchPaymentDetails: function(id) {
    return this.request({ url: ROUTES.payments.details.replace(':id', id), method: 'GET' });
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
      this.updateStatElement('payments', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('payments-pending', stats.pending.count, stats.pending.lastUpdateTime);
      this.updateStatElement('payments-completed', stats.completed.count, stats.completed.lastUpdateTime);
      this.updateStatElement('payments-refunded', stats.refunded.count, stats.refunded.lastUpdateTime);
      this.updateStatElement('payments-cancelled', stats.cancelled.count, stats.cancelled.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load payment statistics');
  },
  updateStatElement: function(elementId, value, lastUpdateTime) {
    // Hide loader, show value and last updated
    $('#' + elementId + '-value').text(value ?? '0').removeClass('d-none');
    $('#' + elementId + '-loader').addClass('d-none');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--').removeClass('d-none');
    $('#' + elementId + '-last-updated-loader').addClass('d-none');
  },
  setAllStatsToNA: function() {
    ['payments', 'payments-pending', 'payments-completed', 'payments-refunded', 'payments-cancelled'].forEach(function(elementId) {
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
    ['payments', 'payments-pending', 'payments-completed', 'payments-refunded', 'payments-cancelled'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// PAYMENT MANAGER
// ===========================
var PaymentManager = {
  currentPaymentId: null,
  /**
   * Initialize payment manager
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind all payment-related events
   */
  bindEvents: function() {
    this.handleAddPayment();
    this.handleEditPayment();
    this.handleViewPayment();
    this.handleDeletePayment();
    this.handleFormSubmit();
    this.handleVewPaymentDetails();
  },
  /**
   * Handle add payment button click
   */
  handleAddPayment: function() {
    var self = this;
    $(document).on('click', '#addPaymentBtn', function() {
      self.openModal('add');
    });
  },
  /**
   * Handle edit payment button click
   */
  handleEditPayment: function() {
    var self = this;
    $(document).on('click', '.editPaymentBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      self.openModal('edit', paymentId);
    });
  },
  /**
   * Handle view payment button click
   */
  handleViewPayment: function() {
    $(document).on('click', '.viewPaymentBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      PaymentManager.viewPayment(paymentId);
    });
  },
  /**
   * Handle delete payment button click
   */
  handleDeletePayment: function() {
    $(document).on('click', '.deletePaymentBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      PaymentManager.deletePayment(paymentId);
    });
  },
  /**
   * Handle form submit
   */
  handleFormSubmit: function() {
    var self = this;
    $('#paymentForm').on('submit', function(e) {
      e.preventDefault();
      self.savePayment();
    });
  },

  handleVewPaymentDetails(){
    var self = this;
    $(document).on('click', '.viewDetailsBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      self.viewDetails(paymentId);
    });
  },
  /**
   * Open add/edit modal
   */
  openModal: function(mode, paymentId) {
    this.currentPaymentId = paymentId;
    this.resetModalState();
    if (mode === 'add') {
      this.setupAddModal();
    } else if (mode === 'edit') {
      this.setupEditModal(paymentId);
    }
  },
  /**
   * Reset modal state
   */
  resetModalState: function() {
    if (Utils && typeof Utils.resetForm === 'function') {
      Utils.resetForm('paymentForm');
    } else {
      // fallback
      $('#paymentForm')[0].reset();
    }
  },
  /**
   * Setup add modal
   */
  setupAddModal: function() {
    $('#paymentModalTitle').text('Add Payment');
    $('#paymentModal').modal('show');
  },
  /**
   * Setup edit modal
   */
  setupEditModal: function(paymentId) {
    $('#paymentModalTitle').text('Edit Payment');
    ApiService.fetchPayment(paymentId)
      .done(function(response) {
        if (response.success) {
          PaymentManager.populateEditForm(response.data);
          $('#paymentModal').modal('show');
        }
      })
      .fail(function() {
        $('#paymentModal').modal('hide');
        if (Utils && typeof Utils.showError === 'function') {
          Utils.showError('Failed to load payment data');
        } else {
          alert('Failed to load payment data');
        }
      });
  },
  /**
   * Populate edit form
   */
  populateEditForm: function(payment) {
    $('#payment_reservation_id').val(payment.reservation_id).prop('readonly', true);
    $('#payment_amount').val(payment.amount);
    $('#payment_status').val(payment.status);
    $('#payment_notes').val(payment.notes);
    $('#payment_details').val(payment.details ? JSON.stringify(payment.details, null, 2) : '');
  },
  /**
   * View payment details
   */
  viewPayment: function(paymentId) {
    ApiService.fetchPayment(paymentId)
      .done(function(response) {
        if (response.success) {
          PaymentManager.populateViewModal(response.data);
          $('#viewPaymentModal').modal('show');
        }
      })
      .fail(function() {
        $('#viewPaymentModal').modal('hide');
        if (Utils && typeof Utils.showError === 'function') {
          Utils.showError('Failed to load payment data');
        } else {
          alert('Failed to load payment data');
        }
      });
  },
  /**
   * Populate view modal
   */
  populateViewModal: function(payment) {
    $('#view-payment-reservation-number').text(payment.reservation_number || '--');
    $('#view-payment-user').text(payment.user_name || '--');
    $('#view-payment-amount').text(payment.amount);
    $('#view-payment-status').text(payment.status);
    $('#view-payment-notes').text(payment.notes);
    $('#view-payment-created').text(payment.created_at);
    // Render details as a list
    var details = payment.details;
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
    $('#view-payment-details-list').html(html);
  },
  /**
   * Save payment
   */
  savePayment: function() {
    var formData = $('#paymentForm').serialize();
    var apiCall;
    if (this.currentPaymentId) {
      apiCall = ApiService.savePayment(formData, this.currentPaymentId);
    } else {
      apiCall = ApiService.createPayment(formData);
    }
    apiCall
      .done(function() {
        PaymentManager.handleSaveSuccess();
      })
      .fail(function(xhr) {
        PaymentManager.handleSaveError(xhr);
      });
  },
  /**
   * Handle successful save
   */
  handleSaveSuccess: function() {
    $('#paymentModal').modal('hide');
    $('#payments-table').DataTable().ajax.reload(null, false);
    if (Utils && typeof Utils.showSuccess === 'function') {
      Utils.showSuccess('Payment has been saved successfully.');
    }
    StatsManager.load();
  },
  /**
   * Handle save error
   */
  handleSaveError: function(xhr) {
    $('#paymentModal').modal('hide');
    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
    if (Utils && typeof Utils.showError === 'function') {
      Utils.showError(message);
    } else {
      alert(message);
    }
  },
  /**
   * Delete payment
   */
  deletePayment: function(paymentId) {
    if (Utils && typeof Utils.showConfirmDialog === 'function') {
      Utils.showConfirmDialog({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        confirmButtonText: 'Yes, delete it!'
      }).then(function(result) {
        if (result.isConfirmed) {
          PaymentManager.performDelete(paymentId);
        }
      });
    } else {
      if (confirm('Are you sure?')) {
        PaymentManager.performDelete(paymentId);
      }
    }
  },
  /**
   * Perform actual deletion
   */
  performDelete: function(paymentId) {
    ApiService.deletePayment(paymentId)
      .done(function() {
        $('#payments-table').DataTable().ajax.reload(null, false);
        if (Utils && typeof Utils.showSuccess === 'function') {
          Utils.showSuccess('Payment has been deleted.');
        }
        StatsManager.load();
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete payment.';
        if (Utils && typeof Utils.showError === 'function') {
          Utils.showError(message);
        } else {
          alert(message);
        }
      });
  },
  viewDetails: function(paymentId) {
    $('#payment-details-json').html('<div class="text-center py-4"><span class="spinner-border text-info" role="status"></span></div>');
    $('#paymentDetailsModal').modal('show');
    ApiService.fetchPaymentDetails(paymentId)
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
          $('#payment-details-json').html(html);
        } else {
          $('#payment-details-json').html('<span class="text-danger">Failed to load details.</span>');
        }
      })
      .fail(function() {
        $('#payment-details-json').html('<span class="text-danger">Failed to load details.</span>');
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
    $('#search_status, #search_reservation_id').on('keyup change', function() {
      $('#payments-table').DataTable().ajax.reload();
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearPaymentFiltersBtn').on('click', function() {
      $('#search_status, #search_reservation_id').val('');
      $('#payments-table').DataTable().ajax.reload();
    });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
var PaymentApp = {
  /**
   * Initialize the entire application
   */
  init: function() {
    StatsManager.init();
    PaymentManager.init();
    SearchManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  PaymentApp.init();
});

</script>
@endpush 