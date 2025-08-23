@extends('layouts.home')

@section('title', __('Insurance Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <x-ui.card.stat2 
              color="secondary"
              icon="bx bx-door-open"
              :label="__('Total Insurances')"
              id="insurances"
              :subStats="[
                  'male' => [
                      'label' => __('Male Insurances'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Insurances'), 
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
              :label="__('Active Insurances')"
              id="insurances-active"
              :subStats="[
                  'male' => [
                      'label' => __('Male Active'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Active'), 
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
              :label="__('Refunded Insurances')"
              id="insurances-refunded"
              :subStats="[
                  'male' => [
                      'label' => __('Male Refunded'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Refunded'), 
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
              :label="__('Carried Over Insurances')"
              id="insurances-carried-over"
              :subStats="[
                  'male' => [
                      'label' => __('Male Carried Over'),
                      'icon' => 'bx bx-male-sign',
                      'color' => 'info'
                  ],
                  'female' => [
                      'label' => __('Female Carried Over'), 
                      'icon' => 'bx bx-female-sign',
                      'color' => 'danger'
                  ]
              ]"
          />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Insurance Management')"
        :description="__('Manage student housing insurance deposits and refunds')"
        icon="bx bx-shield"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#insuranceSearchCollapse" aria-expanded="false" aria-controls="insuranceSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('Advanced Insurance Search')" 
        formId="advancedInsuranceSearch" 
        collapseId="insuranceSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('Status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">{{ __('All') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="refunded">{{ __('Refunded') }}</option>
                <option value="carried_over">{{ __('Carried Over') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_reservation_number" class="form-label">{{ __('Reservation Number') }}:</label>
            <input type="text" class="form-control" id="search_reservation_number" name="search_reservation_number" placeholder="{{ __('Enter reservation number') }}">
        </div>
        <div class="col-md-4">
            <label for="search_national_id" class="form-label">{{ __('National ID') }}:</label>
            <input type="text" class="form-control" id="search_national_id" name="search_national_id" placeholder="{{ __('Enter national ID') }}">
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearInsuranceFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[
            __('User'), 
            __('National ID'),
            __('Reservation Number'), 
            __('Amount'), 
            __('Status'), 
            __('Created At'), 
            __('Actions')
        ]"
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
    {{-- View Insurance Modal --}}
    <x-ui.modal 
        id="viewInsuranceModal"
        :title="__('Insurance Details')"
        :scrollable="true"
        class="view-insurance-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('User Name') }}:</label>
                    <p id="view-insurance-user-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('National ID') }}:</label>
                    <p id="view-insurance-national-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Reservation Number') }}:</label>
                    <p id="view-insurance-reservation-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Amount') }}:</label>
                    <p id="view-insurance-amount" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Status') }}:</label>
                    <p id="view-insurance-status" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}:</label>
                    <p id="view-insurance-created-at" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Updated At') }}:</label>
                    <p id="view-insurance-updated-at" class="mb-0"></p>
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
 * Insurance Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - InsuranceManager: Handles CRUD and actions for insurances
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
    cancel: '{{ route('insurances.cancel', ':id') }}',
    refund: '{{ route('insurances.refund', ':id') }}',
  }
};

const TRANSLATIONS = {
  placeholders: {
    select_status: @json(__('Select Status'))
  },
  status: {
    active: @json(__('Active')),
    inactive: @json(__('Inactive')),
    refunded: @json(__('Refunded')),
    carried_over: @json(__('Carried Over'))
  },
  actions: {
    viewDetails: @json(__('View Details')),
    cancel: @json(__('Cancel')),
    refund: @json(__('Refund')),
    loading: @json(__('Loading...')),
    canceling: @json(__('Canceling...')),
    refunding: @json(__('Refunding...'))
  },
  confirm: {
    cancel: {
      title:  @json(__('Cancel Insurance')),
      text: @json(__('Are you sure you want to cancel this insurance?')),
      button: @json(__('Yes,Cancel'))
    },
    refund: {
      title: @json(__('Refund Insurance')),
      text: @json(__('Are you sure you want to refund this insurance?')),
      button: @json(__('Yes,Refund'))
    },
  },
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
   * Cancel insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  cancelInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.cancel, id), method: 'POST' });
  },
  /**
   * Refund insurance by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  refundInsurance: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.insurances.refund, id), method: 'POST' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats.bind(ApiService),
  statsKeys: [
    'insurances',
    'insurances-active',
    'insurances-refunded',
    'insurances-carried-over'
  ],
  subStatsConfig: {
    'insurances': ['male', 'female'],
    'insurances-active': ['male', 'female'],
    'insurances-refunded': ['male', 'female'],
    'insurances-carried-over': ['male', 'female']
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
    this.handleViewInsurance();
    this.handleCancelInsurance();
    this.handleRefundInsurance();
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
   * Handle cancel insurance button click
   */
  handleCancelInsurance: function() {
    $(document).on('click', '.cancelInsuranceBtn', function(e) {
      var insuranceId = $(e.currentTarget).data('id');
      Utils.showConfirmDialog({
        title: TRANSLATIONS.confirm.cancel.title,
        text: TRANSLATIONS.confirm.cancel.text,
        confirmButtonText: TRANSLATIONS.confirm.cancel.button,
      }).then(function(result) {
        if (result.isConfirmed) {
          InsuranceManager.cancelInsurance(insuranceId, e.currentTarget);
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
        title: TRANSLATIONS.confirm.refund.title,
        text: TRANSLATIONS.confirm.refund.text,
        confirmButtonText: TRANSLATIONS.confirm.refund.button
      }).then(function(result) {
        if (result.isConfirmed) {
          InsuranceManager.refundInsurance(insuranceId, e.currentTarget);
        }
      });
    });
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
      .fail(function(xhr) {
        $('#viewInsuranceModal').modal('hide');
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
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
   * Cancel insurance
   */
  cancelInsurance: function(insuranceId, buttonElement) {
    Utils.setLoadingState(buttonElement, true, {
      loadingText: TRANSLATIONS.actions.canceling,
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: TRANSLATIONS.actions.cancel,
      normalIcon: 'bx bx-x'
    });

    ApiService.cancelInsurance(insuranceId)
      .done((response) => {
        Utils.showSuccess(response.message, true);
        Utils.reloadDataTable('#insurances-table');
        StatsManager.load();
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      })
      .always(() => {
        Utils.setLoadingState(buttonElement, false, {
          normalText: TRANSLATIONS.actions.cancel,
          normalIcon: 'bx bx-x'
        });
      });
  },
  /**
   * Refund insurance
   */
  refundInsurance: function(insuranceId, buttonElement) {
    Utils.setLoadingState(buttonElement, true, {
      loadingText: TRANSLATIONS.actions.refunding,
      loadingIcon: 'bx bx-loader-alt bx-spin',
      normalText: TRANSLATIONS.actions.refund,
      normalIcon: 'bx bx-money'
    });

    ApiService.refundInsurance(insuranceId)
      .done((response) => {
        Utils.showSuccess(response.message, true);
        Utils.reloadDataTable('#insurances-table');
        StatsManager.load();
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      })
      .always(() => {
        Utils.setLoadingState(buttonElement, false, {
          normalText: TRANSLATIONS.actions.refund,
          normalIcon: 'bx bx-money'
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
    $('#search_status, #search_reservation_number, #search_national_id').on('change keyup', function() { 
      self.handleFilterChange(); 
    });
    $('#clearInsuranceFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  /**
   * Handle filter change
   */
  handleFilterChange: function() {
    Utils.reloadDataTable('#insurances-table');
  },
  /**
   * Clear all filters
   */
  clearFilters: function() {
    Select2Manager.resetSearchSelect2();
    $('#search_status').val('').trigger('change');
    $('#search_reservation_number').val('');
    $('#search_national_id').val('');
    Utils.reloadDataTable('#insurances-table');
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
    Select2Manager.initAll();
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