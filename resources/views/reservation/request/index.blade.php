@extends('layouts.home')

@section('title', __('reservation_requests.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="secondary"
                icon="bx bx-calendar"
                label="Total Requests"
                id="requests"
                :subStats="[
                    'male' => [
                        'label' => 'Male Requests',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Female Requests',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="warning"
                icon="bx bx-time-five"
                label="Pending"
                id="requests-pending"
                :subStats="[
                    'male' => [
                        'label' => 'Pending Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Pending Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="success"
                icon="bx bx-check-circle"
                label="Approved"
                id="requests-approved"
                :subStats="[
                    'male' => [
                        'label' => 'Approved Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Approved Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                color="info"
                icon="bx bx-hourglass"
                label="Rejected"
                id="requests-rejected"
                :subStats="[
                    'male' => [
                        'label' => 'Rejected Male',
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => 'Rejected Female',
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('reservation_requests.page_header')"
        :description="__('reservation_requests.page_description')"
        icon="bx bx-calendar"
    >
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationRequestSearchCollapse" aria-expanded="false" aria-controls="reservationRequestSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> Search
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('reservation_requests.search.advanced_title')" 
        formId="advancedReservationRequestSearch" 
        collapseId="reservationRequestSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_request_number" class="form-label">{{ __('reservation_requests.search.fields.request_number') }}:</label>
            <input type="text" class="form-control" id="search_request_number" name="search_request_number">
        </div>
        <div class="col-md-4">
            <label for="search_user_name" class="form-label">{{ __('reservation_requests.search.fields.user_name') }}:</label>
            <input type="text" class="form-control" id="search_user_name" name="search_user_name">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('reservation_requests.search.fields.status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">{{ __('reservation_requests.search.placeholders.all_statuses') }}</option>
                <option value="pending">{{ __('reservation_requests.status.pending') }}</option>
                <option value="approved">{{ __('reservation_requests.status.approved') }}</option>
                <option value="rejected">{{ __('reservation_requests.status.rejected') }}</option>
                <option value="cancelled">{{ __('reservation_requests.status.cancelled') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('reservation_requests.search.fields.active') }}:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="">{{ __('reservation_requests.search.placeholders.all') }}</option>
                <option value="1">{{ __('reservation_requests.active_status.active') }}</option>
                <option value="0">{{ __('reservation_requests.active_status.inactive') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term_id" class="form-label">{{ __('reservation_requests.search.fields.academic_term') }}:</label>
            <select class="form-control" id="search_academic_term_id" name="search_academic_term_id">
                <option value="">{{ __('reservation_requests.search.placeholders.all_terms') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationRequestFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('reservation_requests.search.clear_filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="[
            __('reservation_requests.table.headers.request_number'),
            __('reservation_requests.table.headers.user'),
            __('reservation_requests.table.headers.accommodation'),
            __('reservation_requests.table.headers.academic_term'),
            __('reservation_requests.table.headers.requested_check_in'),
            __('reservation_requests.table.headers.requested_check_out'),
            __('reservation_requests.table.headers.status'),
            __('reservation_requests.table.headers.total_points'),
            __('reservation_requests.table.headers.created'),
            __('reservation_requests.table.headers.actions')
        ]"
        :columns="[
            ['data' => 'request_number', 'name' => 'request_number', 'orderable' => false],
            ['data' => 'name', 'name' => 'name', 'orderable' => false],
            ['data' => 'accommodation_info', 'name' => 'accommodation_info', 'orderable' => false],
            ['data' => 'academic_term', 'name' => 'academic_term', 'orderable' => false],
            ['data' => 'requested_check_in_date', 'name' => 'requested_check_in_date', 'orderable' => false],
            ['data' => 'requested_check_out_date', 'name' => 'requested_check_out_date', 'orderable' => false],
            ['data' => 'status', 'name' => 'status', 'orderable' => false],
            ['data' => 'total_points', 'name' => 'total_points', 'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'orderable' => false],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservation-requests.datatable')"
        :table-id="'reservation-requests-table'"
        :filter-fields="[
            'search_request_number',
            'search_user_name',
            'search_status',
            'search_active',
            'search_academic_term_id',
        ]"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Reservation Modal --}}
    <x-ui.modal 
        id="viewReservationRequestModal"
        title="{{ __('reservation_requests.modals.view.title') }}"
        size="md"
        :scrollable="true"
        class="view-reservation-request-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.request_number') }}:</label>
                <p id="view-reservation-request-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.user') }}:</label>
                <p id="view-reservation-request-user" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.accommodation_type') }}:</label>
                <p id="view-reservation-request-accommodation-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3" id="view-room-type-group" style="display:none;">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.room_type') }}:</label>
                <p id="view-reservation-request-room-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3" id="view-double-room-bed-option-group" style="display:none;">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.double_room_bed_option') }}:</label>
                <p id="view-reservation-request-double-room-bed-option" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.academic_term') }}:</label>
                <p id="view-reservation-request-academic-term" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.requested_check_in') }}:</label>
                <p id="view-reservation-request-check-in" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.requested_check_out') }}:</label>
                <p id="view-reservation-request-check-out" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.status') }}:</label>
                <p id="view-reservation-request-status" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.total_points') }}:</label>
                <p id="view-reservation-request-total-points" class="mb-0"></p>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.notes') }}:</label>
                <p id="view-reservation-request-notes" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">{{ __('reservation_requests.modals.view.fields.created') }}:</label>
                <p id="view-reservation-request-created" class="mb-0"></p>
            </div>
          </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('reservation_requests.modals.view.close') }}</button>
        </x-slot>
    </x-ui.modal>
    
    {{-- Edit Reservation Modal --}}
    <x-ui.modal 
        id="editReservationRequestModal"
        title="{{ __('reservation_requests.modals.edit.title') }}"
        size="md"
        :scrollable="true"
        class="edit-reservation-request-modal"
    >
        <x-slot name="slot">
            <form id="editReservationRequestForm">
                <input type="hidden" id="edit_reservation_request_id" name="reservation_request_id">
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.user') }}:</label>
                        <input type="text" class="form-control" id="edit_user_name" name="user_name" readonly>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.accommodation_type') }}:</label>
                        <select class="form-control" id="edit_accommodation_type" name="accommodation_type">
                            <option value="room">{{ __('reservation_requests.accommodation_types.room') }}</option>
                            <option value="apartment">{{ __('reservation_requests.accommodation_types.apartment') }}</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="edit_room_type_group" style="display:none;">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.room_type') }}:</label>
                        <select class="form-control" id="edit_room_type" name="room_type">
                            <option value="single">{{ __('reservation_requests.room_types.single') }}</option>
                            <option value="double">{{ __('reservation_requests.room_types.double') }}</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="edit_double_room_bed_option_group" style="display:none;">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.double_room_bed_option') }}:</label>
                        <select class="form-control" id="edit_double_room_bed_option" name="double_room_bed_option">
                            <option value="both">{{ __('reservation_requests.bed_options.both') }}</option>
                            <option value="one">{{ __('reservation_requests.bed_options.one') }}</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.academic_term') }}:</label>
                        <select class="form-control" id="edit_academic_term_id" name="academic_term_id"></select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.requested_check_in') }}:</label>
                        <input type="date" class="form-control" id="edit_check_in_date" name="requested_check_in_date">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.requested_check_out') }}:</label>
                        <input type="date" class="form-control" id="edit_check_out_date" name="requested_check_out_date">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.status') }}:</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="pending">{{ __('reservation_requests.status.pending') }}</option>
                            <option value="approved">{{ __('reservation_requests.status.approved') }}</option>
                            <option value="rejected">{{ __('reservation_requests.status.rejected') }}</option>
                            <option value="cancelled">{{ __('reservation_requests.status.cancelled') }}</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.period') }}:</label>
                        <select class="form-control" id="edit_period" name="period">
                            <option value="long">{{ __('reservation_requests.period_types.long') }}</option>
                            <option value="short">{{ __('reservation_requests.period_types.short') }}</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">{{ __('reservation_requests.modals.edit.fields.notes') }}:</label>
                        <textarea class="form-control" id="edit_notes" name="notes"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-primary" id="updateReservationRequestBtn">{{ __('reservation_requests.modals.edit.save') }}</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('reservation_requests.modals.edit.close') }}</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection 

@push('scripts')
<script>
/**
 * Reservation Requests Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ReservationRequestManager: Handles CRUD and actions for reservation requests
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ReservationRequestApp: Initializes all managers
 */

// ===========================
// TRANSLATION CONSTANTS
// ===========================
var MESSAGES = {
  success: {
    reservationRequestCreated: @json(__('reservation_requests.messages.success.created')),
    reservationRequestUpdated: @json(__('reservation_requests.messages.success.updated')),
    reservationRequestDeleted: @json(__('reservation_requests.messages.success.deleted'))
  },
  error: {
    statsLoadFailed: @json(__('reservation_requests.messages.error.stats_load_failed')),
    reservationRequestLoadFailed: @json(__('reservation_requests.messages.error.load_failed')),
    reservationRequestCreateFailed: @json(__('reservation_requests.messages.error.create_failed')),
    reservationRequestUpdateFailed: @json(__('reservation_requests.messages.error.update_failed')),
    reservationRequestDeleteFailed: @json(__('reservation_requests.messages.error.delete_failed')),
    academicTermsLoadFailed: @json(__('reservation_requests.messages.error.academic_terms_load_failed')),
    accommodationsLoadFailed: @json(__('reservation_requests.messages.error.accommodations_load_failed')),
    operationFailed: @json(__('reservation_requests.messages.error.operation_failed'))
  },
  confirm: {
    deleteReservationRequest: {
      title: @json(__('reservation_requests.confirm.delete.title')),
      text: @json(__('reservation_requests.confirm.delete.text')),
      confirmButtonText: @json(__('reservation_requests.confirm.delete.button'))
    }
  },
  placeholders: {
    allTerms: @json(__('reservation_requests.search.placeholders.all_terms')),
    allAccommodations: @json(__('reservation_requests.search.placeholders.all_accommodations'))
  }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  reservationRequests: {
    stats: '{{ route("reservation-requests.stats") }}',
    show: '{{ route("reservation-requests.show", ":id") }}',
    update: '{{ route("reservation-requests.update", ":id") }}',
    destroy: '{{ route("reservation-requests.destroy", ":id") }}',
    datatable: '{{ route("reservation-requests.datatable") }}',
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
  },
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  request: function(options) {
    return $.ajax(options);
  },
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.reservationRequests.stats, method: 'GET' });
  },
  fetchReservationRequest: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.show, id), method: 'GET' });
  },
  updateReservationRequest: function(id, data) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.update, id), method: 'PUT', data: data });
  },
  deleteReservationRequest: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.destroy, id), method: 'DELETE' });
  },
  fetchAcademicTerms: function() {
    return ApiService.request({ url: ROUTES.academicTerms.all, method: 'GET' });
  },
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: [
    'requests',
    'requests-pending',
    'requests-approved',
    'requests-rejected',
  ],
  subStatsConfig: {
    'requests': ['male', 'female'],
    'requests-pending': ['male', 'female'],
    'requests-approved': ['male', 'female'],
    'requests-rejected': ['male', 'female'],
  },
  onError: 'Failed to load Reservation Request statistics'
});

// ===========================
// RESERVATION REQUEST MANAGER
// ===========================
var ReservationRequestManager = {
  currentReservationRequestId: null,
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    $(document)
      .on('click', '.viewReservationRequestBtn', function(e) { self.handleViewReservationRequest(e); })
      .on('click', '.editReservationRequestBtn', function(e) { self.handleEditReservationRequest(e); })
      .on('click', '.deleteReservationRequestBtn', function(e) { self.handleDeleteReservationRequest(e); })
      .on('click', '#updateReservationRequestBtn', function() { self.updateReservationRequest(); })
      .on('change', '#edit_accommodation_type', function() { self.toggleAccommodationFields(); })
      .on('change', '#edit_room_type', function() { self.toggleRoomTypeFields(); })
      .on('change', '#edit_period', function() { self.togglePeriodFields(); })
      .on('hidden.bs.modal', '#editReservationRequestModal', function() { self.resetEditModal(); });
  },
  handleViewReservationRequest: function(e) {
    var reservationRequestId = Utils.getElementData(e.currentTarget, 'id');
    this.viewReservationRequest(reservationRequestId);
  },
  handleEditReservationRequest: function(e) {
    var reservationRequestId = Utils.getElementData(e.currentTarget, 'id');
    this.openEditModal(reservationRequestId);
  },
  handleDeleteReservationRequest: function(e) {
    var reservationRequestId = Utils.getElementData(e.currentTarget, 'id');
    this.confirmAndDeleteReservationRequest(reservationRequestId);
  },
  viewReservationRequest: function(id) {
    ApiService.fetchReservationRequest(id)
      .done(function(response) {
        if (response.success) {
          ReservationRequestManager.populateViewModal(response.data);
          $('#viewReservationRequestModal').modal('show');
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationRequestLoadFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  openEditModal: function(id) {
    this.currentReservationRequestId = id;
    ApiService.fetchReservationRequest(id)
      .done(function(response) {
        if (response.success) {
          ReservationRequestManager.populateEditModal(response.data);
          $('#editReservationRequestModal').modal('show');
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationRequestLoadFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  confirmAndDeleteReservationRequest: function(reservationRequestId) {
    Utils.confirmAction(MESSAGES.confirm.deleteReservationRequest)
      .then(function(result) {
        if (result.isConfirmed) {
          ReservationRequestManager.deleteReservationRequest(reservationRequestId);
        }
      });
  },
  deleteReservationRequest: function(reservationRequestId) {
    ApiService.deleteReservationRequest(reservationRequestId)
      .done(function(response) {
        if (response.success) {
          Utils.reloadDataTable('#reservation-requests-table');
          Utils.showSuccess(MESSAGES.success.reservationRequestDeleted);
          StatsManager.load();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationRequestDeleteFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  updateReservationRequest: function() {
    var formData = {
      period: $('#edit_period').val(),
      requested_check_in_date: $('#edit_check_in_date').val(),
      requested_check_out_date: $('#edit_check_out_date').val(),
      status: $('#edit_status').val(),
      notes: $('#edit_notes').val(),
      requested_accommodation_type: $('#edit_accommodation_type').val(),
      room_type: $('#edit_room_type').val(),
      requested_double_room_bed_option: $('#edit_double_room_bed_option').val(),
      academic_term_id: $('#edit_academic_term_id').val(),
    };
    var id = $('#edit_reservation_request_id').val();
    ApiService.updateReservationRequest(id, formData)
      .done(function(response) {
        if (response.success) {
          $('#editReservationRequestModal').modal('hide');
          Utils.reloadDataTable('#reservation-requests-table');
          Utils.showSuccess(MESSAGES.success.reservationRequestUpdated);
          StatsManager.load();
        } else {
          Utils.showError(response.message || MESSAGES.error.reservationRequestUpdateFailed);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateViewModal: function(reservationRequest) {
    $('#view-reservation-request-number').text(reservationRequest.request_number ?? '--');
    $('#view-reservation-request-user').text(
      reservationRequest.user?.name_en ?? reservationRequest.user_name ?? '--'
    );
    $('#view-reservation-request-accommodation-type').text(reservationRequest.requested_accommodation_type ?? '--');
    if (reservationRequest.requested_accommodation_type === 'room') {
      $('#view-room-type-group').show();
      $('#view-reservation-request-room-type').text(reservationRequest.room_type ?? '--');
      if (reservationRequest.room_type === 'double') {
        $('#view-double-room-bed-option-group').show();
        $('#view-reservation-request-double-room-bed-option').text(reservationRequest.requested_double_room_bed_option ?? '--');
      } else {
        $('#view-double-room-bed-option-group').hide();
      }
    } else {
      $('#view-room-type-group').hide();
      $('#view-double-room-bed-option-group').hide();
    }
    $('#view-reservation-request-academic-term').text(
      reservationRequest.academic_term?.name ?? reservationRequest.academic_term ?? '--'
    );
    $('#view-reservation-request-check-in').text(reservationRequest.requested_check_in_date ?? '--');
    $('#view-reservation-request-check-out').text(reservationRequest.requested_check_out_date ?? '--');
    $('#view-reservation-request-status').text(reservationRequest.status ?? '--');
    $('#view-reservation-request-total-points').text(reservationRequest.total_points ?? '--');
    $('#view-reservation-request-notes').text(reservationRequest.resident_notes ?? reservationRequest.notes ?? '--');
    $('#view-reservation-request-created').text(reservationRequest.created_at ?? '--');
  },
  populateEditModal: function(reservationRequest) {
    $('#edit_reservation_request_id').val(reservationRequest.id);
    var userName = reservationRequest.user ? reservationRequest.user.name_en : 'N/A';
    $('#edit_user_name').val(userName);
    $('#edit_accommodation_type').val(reservationRequest.requested_accommodation_type || 'room').trigger('change');
    if (reservationRequest.requested_accommodation_type === 'room') {
      $('#edit_room_type_group').show();
      $('#edit_room_type').val(reservationRequest.room_type || 'single').trigger('change');
      if (reservationRequest.room_type === 'double') {
        $('#edit_double_room_bed_option_group').show();
        $('#edit_double_room_bed_option').val(reservationRequest.requested_double_room_bed_option || 'both');
      } else {
        $('#edit_double_room_bed_option_group').hide();
      }
    } else {
      $('#edit_room_type_group').hide();
      $('#edit_double_room_bed_option_group').hide();
    }
    SelectManager.populateAcademicTerms(reservationRequest.academic_term_id);
    var checkInDate = reservationRequest.requested_check_in_date;
    var checkOutDate = reservationRequest.requested_check_out_date;
    if (checkInDate) {
      $('#edit_check_in_date').val(new Date(checkInDate).toISOString().split('T')[0]);
    } else {
      $('#edit_check_in_date').val('');
    }
    if (checkOutDate) {
      $('#edit_check_out_date').val(new Date(checkOutDate).toISOString().split('T')[0]);
    } else {
      $('#edit_check_out_date').val('');
    }
    $('#edit_status').val(reservationRequest.status || 'pending');
    $('#edit_notes').val(reservationRequest.resident_notes || '');
    $('#edit_period').val(reservationRequest.period || 'long').trigger('change');
  },
  toggleAccommodationFields: function() {
    var type = $('#edit_accommodation_type').val();
    if (type === 'room') {
      $('#edit_room_type_group').show();
      $('#edit_room_type').trigger('change');
    } else {
      $('#edit_room_type_group').hide();
      $('#edit_double_room_bed_option_group').hide();
    }
  },
  toggleRoomTypeFields: function() {
    var type = $('#edit_room_type').val();
    if (type === 'double') {
      $('#edit_double_room_bed_option_group').show();
    } else {
      $('#edit_double_room_bed_option_group').hide();
    }
  },
  togglePeriodFields: function() {
    var period = $('#edit_period').val();
    if (period === 'long') {
      $('#edit_academic_term_id').closest('.col-6').show();
      $('#edit_check_in_date').closest('.col-6').hide();
      $('#edit_check_out_date').closest('.col-6').hide();
    } else {
      $('#edit_academic_term_id').closest('.col-6').hide();
      $('#edit_check_in_date').closest('.col-6').show();
      $('#edit_check_out_date').closest('.col-6').show();
    }
  },
  resetEditModal: function() {
    Utils.resetForm('editReservationRequestForm');
    $('#edit_reservation_request_id').val('');
    this.currentReservationRequestId = null;
  }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    var filterSelectors = [
      '#search_request_number',
      '#search_user_name',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
    ].join(', ');
    $(filterSelectors).on('keyup change', function() { Utils.reloadDataTable('#reservation-requests-table'); });
    $('#clearReservationRequestFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  clearFilters: function() {
    var filterSelectors = [
      '#search_request_number',
      '#search_user_name',
      '#search_status',
      '#search_active',
      '#search_academic_term_id',
    ].join(', ');
    $(filterSelectors).val('');
    Utils.reloadDataTable('#reservation-requests-table');
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  init: function() {
    this.populateAcademicTermsSelect();
  },
  populateAcademicTermsSelect: function() {
    ApiService.fetchAcademicTerms()
      .done(function(response) {
        if (response.success && response.data) {
          SelectManager.populateSelect('#search_academic_term_id', response.data, 'name', MESSAGES.placeholders.allTerms);
        } else {
          SelectManager.clearSelect('#search_academic_term_id', MESSAGES.placeholders.allTerms);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
        SelectManager.clearSelect('#search_academic_term_id', MESSAGES.placeholders.allTerms);
      });
  },
  populateAcademicTerms: function(selectedTermId) {
    ApiService.fetchAcademicTerms()
      .done(function(response) {
        var $termSelect = $('#edit_academic_term_id');
        $termSelect.empty();
        if (response.success && response.data) {
          response.data.forEach(function(term) {
            $termSelect.append('<option value="' + term.id + '"' + (term.id == selectedTermId ? ' selected' : '') + '>' + term.name + '</option>');
          });
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateSelect: function(selector, data, valueField, placeholder) {
    var $select = $(selector);
    $select.empty().append('<option value="">' + placeholder + '</option>');
    data.forEach(function(item) {
      $select.append('<option value="' + item.id + '">' + item[valueField] + '</option>');
    });
  },
  clearSelect: function(selector, placeholder) {
    $(selector).empty().append('<option value="">' + placeholder + '</option>');
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var ReservationRequestApp = {
  init: function() {
    StatsManager.init();
    ReservationRequestManager.init();
    SearchManager.init();
    SelectManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  ReservationRequestApp.init();
});

</script>
@endpush