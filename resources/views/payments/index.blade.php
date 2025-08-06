@extends('layouts.home')

@section('title', __('payments.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row mb-4 g-2">
      <!-- Total Payments -->
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('payments.stats.total_payments')"
              id="payments"
              :subStats="[
                  'male' => [
                      'label' => __('payments.stats.male_payments'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('payments.stats.female_payments'), 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>

      <!-- Pending Payments -->
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="warning"
              icon="bx bx-time-five"
              label="Pending Payments"
              id="payments-pending"
              :subStats="[
                  'male' => [
                      'label' => 'Male Pending',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Female Pending', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>

      <!-- Completed Payments -->
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="success"
              icon="bx bx-check-circle"
              label="Completed Payments"
              id="payments-completed"
              :subStats="[
                  'male' => [
                      'label' => 'Male Completed',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Female Completed', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>

      <!-- Cancelled Payments -->
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="dark"
              icon="bx bx-x-circle"
              label="Cancelled Payments"
              id="payments-cancelled"
              :subStats="[
                  'male' => [
                      'label' => 'Male Cancelled',
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => 'Female Cancelled', 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
  </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Payments"
        description="Manage payments and their details."
        icon="bx bx-money"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
                            <button class="btn btn-primary mx-2" id="addPaymentBtn">
            <i class="bx bx-plus me-1"></i> Add Payment
        </button>
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentSearchCollapse" aria-expanded="false" aria-controls="paymentSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> Search
            </button>
        </div>
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
                        <label for="payment_reservation_id" class="form-label">Reservation Number</label>
                        <input type="text" id="payment_reservation_id" name="reservation_number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea id="payment_notes" name="notes" class="form-control"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Total Amount</label>
                        <input type="number" step="0.01" id="payment_amount" name="amount" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Payment Details</label>
                        <div id="payment_details_container">
                            <!-- Template for payment detail item -->
                            <div class="payment-detail-item card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Payment Type</label>
                                            <select name="details[0][type]" class="form-control payment-type" required>
                                                <option value="">Select Type</option>
                                                <option value="damages">damages</option>
                                                <option value="service_fee">Service Fee</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Amount</label>
                                            <input type="number" name="details[0][amount]" class="form-control payment-amount" step="0.01" required>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Description</label>
                                            <textarea name="details[0][description]" class="form-control payment-description" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-detail" style="display: none;">
                                                <i class="bx bx-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add_payment_detail">
                            <i class="bx bx-plus"></i> Add
                        </button>
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
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats.bind(ApiService),
  statsKeys: [
    'payments',
    'payments-pending',
    'payments-completed',
    'payments-cancelled'
  ],
  subStatsConfig: {
    'payments': ['male', 'female'],
    'payments-pending': ['male', 'female'],
    'payments-completed': ['male', 'female'],
    'payments-cancelled': ['male', 'female']
  },
  onError: 'Failed to load payment statistics'
});
  

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
    this.initializePaymentDetails();
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
      Utils.resetForm('paymentForm');
      $('#paymentModalTitle').text('Add Payment');
      $('#paymentModal').modal('show');
    });
  },
  /**
   * Handle edit payment button click
   */
  handleEditPayment: function() {
    var self = this;
    $(document).on('click', '.editPaymentBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      Utils.resetForm('paymentForm');
        $('#paymentModalTitle').text('Edit Payment');
    ApiService.fetchPayment(paymentId)
      .done(function(response) {
        if (response.success) {
          PaymentManager.populateEditForm(response.data);
          $('#paymentModal').modal('show');
        }
      })
      .fail(function(xhr) {
        $('#paymentModal').modal('hide');
          Utils.handleAjaxError(xhr,'Failed to load payment data');
      });
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
      Utils.clearValidation('#paymentForm');
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
   * Populate edit form
   */
  /**
   * Initialize payment details handlers
   */
  initializePaymentDetails: function() {
    var self = this;
    
    // Handle add new payment detail
    $(document).on('click', '#add_payment_detail', function() {
      var detailsContainer = $('#payment_details_container');
      var newItem = detailsContainer.find('.payment-detail-item').first().clone();
      var itemIndex = detailsContainer.find('.payment-detail-item').length;
      
      // Update name attributes with new index
      newItem.find('select.payment-type').attr('name', 'details[' + itemIndex + '][type]').val('');
      newItem.find('input.payment-amount').attr('name', 'details[' + itemIndex + '][amount]').val('');
      newItem.find('textarea.payment-description').attr('name', 'details[' + itemIndex + '][description]').val('');
      
      // Show remove button
      newItem.find('.remove-detail').show();
      
      detailsContainer.append(newItem);
      self.updateRemoveButtons();
      self.updateTotalAmount();
    });

    // Handle amount changes
    $(document).on('input', '.payment-amount', function() {
      self.updateTotalAmount();
    });
    
    // Handle remove payment detail
    $(document).on('click', '.remove-detail', function() {
      $(this).closest('.payment-detail-item').remove();
      self.updateRemoveButtons();
      self.reindexPaymentDetails();
      self.updateTotalAmount();
    });
  },

  /**
   * Calculate and update the total amount
   */
  updateTotalAmount: function() {
    var total = 0;
    $('.payment-amount').each(function() {
      var amount = parseFloat($(this).val()) || 0;
      total += amount;
    });
    $('#payment_amount').val(total.toFixed(2));
  },
  
  /**
   * Update remove buttons visibility
   */
  updateRemoveButtons: function() {
    var items = $('.payment-detail-item');
    if (items.length === 1) {
      items.find('.remove-detail').hide();
    } else {
      items.find('.remove-detail').show();
    }
  },
  
  /**
   * Reindex payment details form fields
   */
  reindexPaymentDetails: function() {
    $('#payment_details_container .payment-detail-item').each(function(index) {
      $(this).find('select.payment-type').attr('name', 'details[' + index + '][type]');
      $(this).find('input.payment-amount').attr('name', 'details[' + index + '][amount]');
      $(this).find('textarea.payment-description').attr('name', 'details[' + index + '][description]');
    });
  },

  /**
   * Populate edit form
   */
  populateEditForm: function(payment) {
    $('#payment_reservation_id').val(payment.reservation_number);
    $('#payment_amount').val(payment.amount);
    $('#payment_notes').val(payment.notes);
    
    // Clear existing payment details except the first one
    var detailsContainer = $('#payment_details_container');
    detailsContainer.find('.payment-detail-item:not(:first)').remove();
    
    // Reset the first item
    var firstItem = detailsContainer.find('.payment-detail-item').first();
    firstItem.find('select.payment-type').val('');
    firstItem.find('input.payment-amount').val('');
    firstItem.find('textarea.payment-description').val('');
    
    // Add payment details if they exist
    if (payment.details && Array.isArray(payment.details)) {
      payment.details.forEach((detail, index) => {
        if (index === 0) {
          // Update first item
          firstItem.find('select.payment-type').val(detail.type || '');
          firstItem.find('input.payment-amount').val(detail.amount || '');
          firstItem.find('textarea.payment-description').val(detail.description || '');
        } else {
          // Add new items for remaining details
          $('#add_payment_detail').click();
          var newItem = detailsContainer.find('.payment-detail-item').last();
          newItem.find('select.payment-type').val(detail.type || '');
          newItem.find('input.payment-amount').val(detail.amount || '');
          newItem.find('textarea.payment-description').val(detail.description || '');
        }
      });
    }
    
    this.updateRemoveButtons();
    this.updateTotalAmount();
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
    Utils.setLoadingState('#paymentForm button[type="submit"]', true, {
      loadingText: 'Saving...',
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: 'Save',
      normalIcon: 'bx bx-save'
    });

    var formData = $('#paymentForm').serialize();
    var apiCall = this.currentPaymentId ? 
      ApiService.savePayment(formData, this.currentPaymentId) :
      ApiService.createPayment(formData);

    apiCall
      .done(() => {
        Utils.showSuccess('Payment saved successfully', true);
        $('#paymentModal').modal('hide');
        Utils.reloadDataTable('#payments-table');
        StatsManager.load();
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, 'Failed to save payment');
      })
      .always(() => {
        Utils.setLoadingState('#paymentForm button[type="submit"]', false, {
          normalText: 'Save',
          normalIcon: 'bx bx-save'
        });
      });
  },
  /**
   * Handle successful save
   */
  handleSaveSuccess: function() {
    $('#paymentModal').modal('hide');
    Utils.reloadDataTable('#payments-table');
    Utils.showSuccess('Payment has been saved successfully.');
    StatsManager.load();
  },
  /**
   * Handle save error
   */
  handleSaveError: function(xhr) {
    $('#paymentModal').modal('hide');
    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
    Utils.showError(message);
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
    Utils.reloadDataTable('#payments-table');
    Utils.showSuccess('Payment has been deleted.');
    StatsManager.load();
      })
      .fail(function(xhr) {
    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete payment.';
    Utils.showError(message);
      });
  },
  viewDetails: function(paymentId) {
    Utils.setLoadingState('.viewDetailsBtn[data-id="' + paymentId + '"]', true, {
      loadingText: 'Loading...',
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: 'View Details',
      normalIcon: 'bx bx-info-circle'
    });

    $('#paymentDetailsModal').modal('show');
    $('#payment-details-json').html('<div class="text-center py-4"><span class="spinner-border text-info" role="status"></span></div>');

    ApiService.fetchPaymentDetails(paymentId)
      .done((response) => {
        if (response.success) {
          var details = response.data;
          var html = '';
          if (!Utils.isEmpty(details)) {
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
            html = '<span class="text-muted">No details available</span>';
          }
          $('#payment-details-json').html(html);
        } else {
          Utils.showError('Failed to load payment details', true);
        }
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, 'Failed to load payment details');
      })
      .always(() => {
        Utils.setLoadingState('.viewDetailsBtn[data-id="' + paymentId + '"]', false, {
          normalText: 'View Details',
          normalIcon: 'bx bx-info-circle'
        });
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
    $('#search_status, #search_reservation_id').on('keyup change', function() {
      Utils.reloadDataTable('#payments-table');
    });
  },
  /**
   * Handle clear filters button click
   */
  handleClearFilters: function() {
    $('#clearPaymentFiltersBtn').on('click', function() {
    Utils.clearValidation('#advancedPaymentSearch');
    $('#search_status').val('').trigger('change');
    $('#search_reservation_id').val('');
    Utils.reloadDataTable('#payments-table');
    Utils.showSuccess('Filters cleared', true, 'top-end');
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