@extends('layouts.home')

@section('title', __('Reservation Requests'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
        </div>
        <div class="col-sm-6 col-xl-3">
        </div>
        <div class="col-sm-6 col-xl-3">
        </div>
        <div class="col-sm-6 col-xl-3">
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Reservation Requests')"
        :description="__('Manage all reservation requests')"
        icon="bx bx-calendar"
    >
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationRequestSearchCollapse" aria-expanded="false" aria-controls="reservationRequestSearchCollapse">
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('Advanced Search')" 
        formId="advancedReservationRequestSearch" 
        collapseId="reservationRequestSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationRequestFiltersBtn" type="button">
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers=" [
            __('Actions')
        ]"
        :columns=" [
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservation-requests.datatable')"
        :table-id="'reservation-requests-table'"
        :filter-fields=" [
            'search_academic_term_id',
        ]"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Reservation Modal --}}
    <x-ui.modal 
        id="viewReservationRequestModal"
        title="{{ __('View Reservation Request') }}"
        size="md"
        :scrollable="true"
        class="view-reservation-request-modal"
    >
        <x-slot name="slot">
        </x-slot>
        <x-slot name="footer">
        </x-slot>
    </x-ui.modal>
    
    {{-- Edit Reservation Modal --}}
    <x-ui.modal 
        id="editReservationRequestModal"
        title="{{ __('Edit Reservation Request') }}"
        size="md"
        :scrollable="true"
        class="edit-reservation-request-modal"
    >
        <x-slot name="slot">
        </x-slot>
        <x-slot name="footer">
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
    reservationRequestCreated: @json(__('Reservation request created successfully')),
    reservationRequestUpdated: @json(__('Reservation request updated successfully')),
    reservationRequestDeleted: @json(__('Reservation request deleted successfully'))
  },
  error: {
    statsLoadFailed: @json(__('Failed to load statistics')),
    reservationRequestLoadFailed: @json(__('Failed to load reservation request')),
    reservationRequestCreateFailed: @json(__('Failed to create reservation request')),
    reservationRequestUpdateFailed: @json(__('Failed to update reservation request')),
    reservationRequestDeleteFailed: @json(__('Failed to delete reservation request')),
    academicTermsLoadFailed: @json(__('Failed to load academic terms')),
    accommodationsLoadFailed: @json(__('Failed to load accommodations')),
    operationFailed: @json(__('Operation failed'))
  },
  confirm: {
    deleteReservationRequest: {
      title: @json(__('reservation_requests.confirm.delete.title')),
      text: @json(__('reservation_requests.confirm.delete.text')),
      confirmButtonText: @json(__('reservation_requests.confirm.delete.button'))
    }
  },
  placeholders: {
    allTerms: @json(__('All Terms')),
    allAccommodations: @json(__('All Accommodations'))
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