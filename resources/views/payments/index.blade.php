@extends('layouts.home')

@section('title', __('Payment Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row mb-4 g-2">
      <!-- Total Payments -->
      <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('Total Payments')"
              id="payments"
              :subStats="[
                  'male' => [
                      'label' => __('Male Payments'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Payments'), 
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
              :label="__('Pending Payments')"
              id="payments-pending"
              :subStats="[
                  'male' => [
                      'label' => __('Male Pending'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Pending'), 
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
              :label="__('Completed Payments')"
              id="payments-completed"
              :subStats="[
                  'male' => [
                      'label' => __('Male Completed'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Completed'), 
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
              :label="__('Cancelled Payments')"
              id="payments-cancelled"
              :subStats="[
                  'male' => [
                      'label' => __('Male Cancelled'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Cancelled'), 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
      </div>
  </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Payments Management')"
        :description="__('Manage all campus payments and transactions')"
        icon="bx bx-money"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-primary mx-2" id="addPaymentBtn">
            <i class="bx bx-plus me-1"></i> {{ __('Add Payment') }}
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentSearchCollapse" aria-expanded="false" aria-controls="paymentSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
        </button>
    </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('Advanced Payment Search')" 
        formId="advancedPaymentSearch" 
        collapseId="paymentSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('Status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">{{ __('All') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="refunded">{{ __('Refunded') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_reservation_id" class="form-label">{{ __('Reservation ID') }}:</label>
            <input type="text" class="form-control" id="search_reservation_id" name="search_reservation_id" placeholder="{{ __('Enter reservation ID') }}">
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearPaymentFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers=" [
            __('Reservation Number'),
            __('User'),
            __('Amount'),
            __('Status'),
            __('Notes'),
            __('Details'),
            __('Created At'),
            __('Actions')
        ]"
        :columns=" [
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
    {{-- Add Payment Modal --}}
    <x-ui.modal 
        id="addPaymentModal"
        :title="__('Add Payment')"
        :scrollable="true"
    >
        <x-slot name="slot">
            <form id="addPaymentForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="payment_reservation_id" class="form-label">{{ __('Reservation Number') }}</label>
                        <input type="text" id="payment_reservation_id" name="reservation_number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="payment_notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea id="payment_notes" name="notes" class="form-control"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">{{ __('Total Amount') }}</label>
                        <input type="number" step="0.01" id="payment_amount" name="amount" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{ __('Payment Details') }}</label>
                        <div id="payment_details_container">
                            <!-- Template for payment detail item -->
                            <div class="payment-detail-item card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">{{ __('Payment Type') }}</label>
                                            <select name="details[0][type]" class="form-control payment-type" required>
                                                <option value="">{{ __('Select Type') }}</option>
                                                <option value="damages">{{ __('Damages') }}</option>
                                                <option value="service_fee">{{ __('Service Fee') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">{{ __('Amount') }}</label>
                                            <input type="number" name="details[0][amount]" class="form-control payment-amount" step="0.01" required>
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">{{ __('Description') }}</label>
                                            <textarea name="details[0][description]" class="form-control payment-description" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-detail" style="display: none;">
                                                <i class="bx bx-trash"></i> {{ __('Remove') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add_payment_detail">
                            <i class="bx bx-plus"></i> {{ __('Add Detail') }}
                        </button>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary" form="addPaymentForm">{{ __('Save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Payment Modal --}}
    <x-ui.modal 
        id="viewPaymentModal"
        :title="__('Payment Details')"
        :scrollable="true"
        class="view-payment-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Reservation Number') }}:</label>
                    <p id="view-payment-reservation-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('User') }}:</label>
                    <p id="view-payment-user" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Amount') }}:</label>
                    <p id="view-payment-amount" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Status') }}:</label>
                    <p id="view-payment-status" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Notes') }}:</label>
                    <p id="view-payment-notes" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Details') }}:</label>
                    <div id="view-payment-details-list"></div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}:</label>
                    <p id="view-payment-created" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- Details Modal --}}
    <x-ui.modal 
        id="paymentDetailsModal"
        :title="__('Payment Details')"
        :scrollable="true"
        class="view-payment-details-modal"
    >
        <x-slot name="slot">
            <pre id="payment-details-json" class="mb-0"></pre>
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
    details: '{{ route('payments.details', ':id') }}',
  }
};

const TRANSLATIONS = {
  placeholders: {
    select_status: @json(__('Select Status'))
  },
  status: {
    active: @json(__('Active')),
    inactive: @json(__('Inactive'))
  },
  actions: {
    viewDetails: @json(__('View Details')),
    loading: @json(__('Loading...')),
    saving: @json(__('Saving...')),
    save: @json(__('Save'))
  },
  messages: {
    no_details_available: @json(__('No details available.'))
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
   * Create a new payment
   * @param {object} data
   * @returns {jqXHR}
   */
  createPayment: function(data) {
    return this.request({ url: ROUTES.payments.store, method: 'POST', data: data });
  },
  /**
   * Fetch payment details by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
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
    this.handleViewPayment();
    this.handleFormSubmit();
    this.handleViewPaymentDetails();
  },
  /**
   * Handle add payment button click
   */
  handleAddPayment: function() {
    var self = this;
    $(document).on('click', '#addPaymentBtn', function() {
      Utils.resetForm('addPaymentForm');
      $('#addPaymentModal').modal('show');
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
   * Handle form submit
   */
  handleFormSubmit: function() {
    var self = this;
    $('#addPaymentForm').on('submit', function(e) {
      e.preventDefault();
      Utils.clearValidation('#addPaymentForm');
      self.savePayment();
    });
  },

  handleViewPaymentDetails: function() {
    var self = this;
    $(document).on('click', '.viewDetailsBtn', function(e) {
      var paymentId = $(e.currentTarget).data('id');
      self.viewDetails(paymentId);
    });
  },

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
      .fail(function(xhr) {
        $('#viewPaymentModal').modal('hide');
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
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
    Utils.setLoadingState('#addPaymentForm button[type="submit"]', true, {
      loadingText: TRANSLATIONS.actions.saving,
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: TRANSLATIONS.actions.save,
      normalIcon: 'bx bx-save'
    });

    var formData = $('#addPaymentForm').serialize();

    ApiService.createPayment(formData)
      .done((response) => {
        Utils.showSuccess(response.message, true);
        $('#addPaymentModal').modal('hide');
        Utils.reloadDataTable('#payments-table');
        StatsManager.load();
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      })
      .always(() => {
        Utils.setLoadingState('#addPaymentForm button[type="submit"]', false, {
          normalText: TRANSLATIONS.actions.save,
          normalIcon: 'bx bx-save'
        });
      });
  },
  viewDetails: function(paymentId) {
    Utils.setLoadingState('.viewDetailsBtn[data-id="' + paymentId + '"]', true, {
      loadingText: TRANSLATIONS.actions.loading,
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: TRANSLATIONS.actions.viewDetails,
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
            html = '<span class="text-muted">' + TRANSLATIONS.messages.no_details_available + '</span>';
          }
          $('#payment-details-json').html(html);
        } else {
          Utils.showError(response.message);
        }
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      })
      .always(() => {
        Utils.setLoadingState('.viewDetailsBtn[data-id="' + paymentId + '"]', false, {
          normalText: TRANSLATIONS.actions.viewDetails,
          normalIcon: 'bx bx-info-circle'
        });
      });
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
            '#search_status': { placeholder: TRANSLATIONS.placeholders.select_status }
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
        this.clearSelect2(['#search_status']);
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
    $('#search_status, #search_reservation_id').on('change', function() { self.handleFilterChange(); });
    $('#clearPaymentFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Handle filter change
   */
  handleFilterChange: function() {
    Utils.reloadDataTable('#payments-table');
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    Select2Manager.resetSearchSelect2();
    $('#search_status').val('').trigger('change');
    $('#search_reservation_id').val('');
    Utils.reloadDataTable('#payments-table');
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
    Select2Manager.initAll();
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