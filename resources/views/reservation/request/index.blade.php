@extends('layouts.home')

@section('title', 'Reservation Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="primary" icon="bx bx-calendar" label="Total Requests" id="reservation-requests" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="info" icon="bx bx-time-five" label="Pending" id="reservation-requests-pending" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="success" icon="bx bx-check-circle" label="Approved" id="reservation-requests-approved" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="danger" icon="bx bx-x-circle" label="Rejected" id="reservation-requests-rejected" />
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 color="warning" icon="bx bx-block" label="Cancelled" id="reservation-requests-cancelled" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Reservation Requests"
        description="Manage reservation requests and their details."
        icon="bx bx-calendar"
    >
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reservationRequestSearchCollapse" aria-expanded="false" aria-controls="reservationRequestSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedReservationRequestSearch" 
        collapseId="reservationRequestSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_request_number" class="form-label">Reservation Request Number:</label>
            <input type="text" class="form-control" id="search_request_number" name="search_request_number">
        </div>
        <div class="col-md-4">
            <label for="search_user_name" class="form-label">User Name:</label>
            <input type="text" class="form-control" id="search_user_name" name="search_user_name">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">Status:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked_in">Checked In</option>
                <option value="checked_out">Checked Out</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">Active:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term_id" class="form-label">Academic Term:</label>
            <select class="form-control" id="search_academic_term_id" name="search_academic_term_id">
                <option value="">All Terms</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_accommodation_id" class="form-label">Accommodation:</label>
            <select class="form-control" id="search_accommodation_id" name="search_accommodation_id">
                <option value="">All Accommodations</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearReservationRequestFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Reservation Request #', 'User', 'Accommodation', 'Academic Term', 'Requested Check-in', 'Requested Check-out', 'Status', 'Total Points', 'Created', 'Actions']"
        :columns="[
            ['data' => 'request_number', 'name' => 'request_number'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'accommodation_info', 'name' => 'accommodation_info'],
            ['data' => 'academic_term', 'name' => 'academic_term'],
            ['data' => 'requested_check_in_date', 'name' => 'requested_check_in_date'],
            ['data' => 'requested_check_out_date', 'name' => 'requested_check_out_date'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'total_points', 'name' => 'total_points'],
            ['data' => 'created_at', 'name' => 'created_at'],
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
            'search_accommodation_id'
        ]"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Reservation Modal --}}
    <x-ui.modal 
        id="viewReservationRequestModal"
        title="Reservation Request Details"
        size="md"
        :scrollable="true"
        class="view-reservation-request-modal"
    >
        <x-slot name="slot">
          <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Reservation Request Number:</label>
                <p id="view-reservation-request-number" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">User:</label>
                <p id="view-reservation-request-user" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Accommodation Type:</label>
                <p id="view-reservation-request-accommodation-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3" id="view-room-type-group" style="display:none;">
                <label class="form-label fw-bold">Room Type:</label>
                <p id="view-reservation-request-room-type" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3" id="view-double-room-bed-option-group" style="display:none;">
                <label class="form-label fw-bold">Double Room Bed Option:</label>
                <p id="view-reservation-request-double-room-bed-option" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Academic Term:</label>
                <p id="view-reservation-request-academic-term" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Requested Check-in Date:</label>
                <p id="view-reservation-request-check-in" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Requested Check-out Date:</label>
                <p id="view-reservation-request-check-out" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Status:</label>
                <p id="view-reservation-request-status" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Total Points:</label>
                <p id="view-reservation-request-total-points" class="mb-0"></p>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">Notes:</label>
                <p id="view-reservation-request-notes" class="mb-0"></p>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Created At:</label>
                <p id="view-reservation-request-created" class="mb-0"></p>
            </div>
          </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </x-slot>
    </x-ui.modal>
    
    {{-- Edit Reservation Modal --}}
    <x-ui.modal 
        id="editReservationRequestModal"
        title="Edit Reservation Request"
        size="md"
        :scrollable="true"
        class="edit-reservation-request-modal"
    >
        <x-slot name="slot">
            <form id="editReservationRequestForm">
                <input type="hidden" id="edit_reservation_request_id" name="reservation_request_id">
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">User:</label>
                        <input type="text" class="form-control" id="edit_user_name" name="user_name" readonly>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Accommodation Type:</label>
                        <select class="form-control" id="edit_accommodation_type" name="accommodation_type">
                            <option value="room">Room</option>
                            <option value="apartment">Apartment</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="edit_room_type_group" style="display:none;">
                        <label class="form-label fw-bold">Room Type:</label>
                        <select class="form-control" id="edit_room_type" name="room_type">
                            <option value="single">Single</option>
                            <option value="double">Double</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3" id="edit_double_room_bed_option_group" style="display:none;">
                        <label class="form-label fw-bold">Double Room Bed Option:</label>
                        <select class="form-control" id="edit_double_room_bed_option" name="double_room_bed_option">
                            <option value="both">Both Beds</option>
                            <option value="one">One Bed</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Academic Term:</label>
                        <select class="form-control" id="edit_academic_term_id" name="academic_term_id"></select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Requested Check-in Date:</label>
                        <input type="date" class="form-control" id="edit_check_in_date" name="requested_check_in_date">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Requested Check-out Date:</label>
                        <input type="date" class="form-control" id="edit_check_out_date" name="requested_check_out_date">
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <select class="form-control" id="edit_status" name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="checked_in">Checked In</option>
                            <option value="checked_out">Checked Out</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Period:</label>
                        <select class="form-control" id="edit_period" name="period">
                            <option value="long">Long</option>
                            <option value="short">Short</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Notes:</label>
                        <textarea class="form-control" id="edit_notes" name="notes"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-primary" id="updateReservationRequestBtn">Save Changes</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection 

@push('scripts')
<script>
/**
 * Reservation Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ReservationManager: Handles CRUD and actions for reservations
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - ReservationApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  reservationRequests: {
    stats: '{{ route("reservation-requests.stats") }}',
    show: '{{ route("reservation-requests.show", ":id") }}',
    destroy: '{{ route("reservation-requests.destroy", ":id") }}',
    datatable: '{{ route("reservation-requests.datatable") }}',
  },
  buildings: {
    all: '{{ route("housing.buildings.all") }}'
  },
  apartments: {
    all: '{{ route("housing.apartments.all") }}'
  },
  rooms: {
    all: '{{ route("housing.rooms.all") }}'
  },
  users: {
    all: '{{ route("users.all") }}'
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
var Utils = {
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
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
  resetForm: function(formId) {
    $('#' + formId)[0].reset();
  },
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
  },
  formatDate: function(dateString) {
    return dateString ? new Date(dateString).toLocaleString() : '--';
  }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  request: function(options) {
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    return $.ajax(options);
  },
  fetchStats: function() {
    return this.request({ url: ROUTES.reservationRequests.stats, method: 'GET' });
  },
  fetchReservationRequest: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.show, id), method: 'GET' });
  },
  updateReservationRequest: function(id, data) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.show, id), method: 'PUT', data: data });
  },
  deleteReservationRequest: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.reservationRequests.destroy, id), method: 'DELETE' });
  },
  fetchAcademicTerms: function() {
    return this.request({ url: ROUTES.academicTerms.all, method: 'GET' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = {
  init: function() { this.load(); },
  load: function() {
    this.toggleAllLoadingStates(true);
    ApiService.fetchStats()
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
  handleSuccess: function(response) {
    if (response.success) {
      let stats = response.data;
      this.updateStatElement('reservation-requests', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('reservation-requests-pending', stats.pending.count, stats.pending.lastUpdateTime);
      this.updateStatElement('reservation-requests-approved', stats.approved.count, stats.approved.lastUpdateTime);
      this.updateStatElement('reservation-requests-rejected', stats.rejected.count, stats.rejected.lastUpdateTime);
      this.updateStatElement('reservation-requests-cancelled', stats.cancelled.count, stats.cancelled.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load reservation request statistics');
  },
  updateStatElement: function(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  setAllStatsToNA: function() {
    ['reservation-requests', 'reservation-requests-pending', 'reservation-requests-approved', 'reservation-requests-rejected', 'reservation-requests-cancelled'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  toggleAllLoadingStates: function(isLoading) {
    ['reservation-requests', 'reservation-requests-pending', 'reservation-requests-approved', 'reservation-requests-rejected', 'reservation-requests-cancelled'].forEach(function(elementId) {
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
    });
  }
};

// ===========================
// RESERVATION MANAGER
// ===========================
var ReservationManager = {
  currentReservationId: null,
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    var self = this;
    // View
    $(document).on('click', '.viewReservationRequestBtn', function(e) {
      var id = $(e.currentTarget).data('id');
      self.viewReservationRequest(id);
    });
    // Edit
    $(document).on('click', '.editReservationRequestBtn', function(e) {
      var id = $(e.currentTarget).data('id');
      self.openEditModal(id);
    });
    // Delete
    $(document).on('click', '.deleteReservationRequestBtn', function(e) {
      var id = $(e.currentTarget).data('id');
      self.deleteReservationRequest(id);
    });
    // Update
    $(document).on('click', '#updateReservationRequestBtn', function() {
      self.updateReservationRequest();
    });
    // Edit modal field logic
    $(document).on('change', '#edit_accommodation_type', function() {
      var type = $(this).val();
      if (type === 'room') {
        $('#edit_room_type_group').show();
        $('#edit_room_type').trigger('change');
      } else {
        $('#edit_room_type_group').hide();
        $('#edit_double_room_bed_option_group').hide();
      }
    });
    $(document).on('change', '#edit_room_type', function() {
      var type = $(this).val();
      if (type === 'double') {
        $('#edit_double_room_bed_option_group').show();
      } else {
        $('#edit_double_room_bed_option_group').hide();
      }
    });
    $(document).on('change', '#edit_period', function() {
      var period = $(this).val();
      if (period === 'long') {
        $('#edit_academic_term_id').closest('.col-6').show();
        $('#edit_check_in_date').closest('.col-6').hide();
        $('#edit_check_out_date').closest('.col-6').hide();
      } else {
        $('#edit_academic_term_id').closest('.col-6').hide();
        $('#edit_check_in_date').closest('.col-6').show();
        $('#edit_check_out_date').closest('.col-6').show();
      }
    });
    // Reset edit modal on close
    $(document).on('hidden.bs.modal', '#editReservationRequestModal', function() {
      Utils.resetForm('editReservationRequestForm');
      $('#edit_reservation_request_id').val('');
    });
  },
  viewReservationRequest: function(id) {
    ApiService.fetchReservationRequest(id)
      .done(function(response) {
        if (response.success) {
          ReservationManager.populateViewModal(response.data);
          $('#viewReservationRequestModal').modal('show');
        } else {
          Utils.showError(response.message || 'Failed to load reservation request data.');
        }
      })
      .fail(function() {
        $('#viewReservationRequestModal').modal('hide');
        Utils.showError('Failed to load reservation request data.');
      });
  },
  openEditModal: function(id) {
    this.currentReservationId = id;
    ApiService.fetchReservationRequest(id)
      .done(function(response) {
        if (response.success) {
          ReservationManager.populateEditModal(response.data);
          $('#editReservationRequestModal').modal('show');
        } else {
          Utils.showError(response.message || 'Failed to load reservation request data.');
        }
      })
      .fail(function() {
        Utils.showError('Failed to load reservation request data.');
      });
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
    ReservationManager.populateAcademicTerms(reservationRequest.academic_term_id);
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
      apartment_id: $('#edit_apartment_id').val(),
      academic_term_id: $('#edit_academic_term_id').val(),
    };
    var id = $('#edit_reservation_request_id').val();
    ApiService.updateReservationRequest(id, formData)
      .done(function(response) {
        if (response.success) {
          $('#editReservationRequestModal').modal('hide');
          ReservationManager.reloadTable();
          Utils.showSuccess('Reservation Request has been updated successfully.');
          StatsManager.load();
        } else {
          Utils.showError(response.message || 'Failed to update reservation request.');
        }
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to update reservation request.';
        Utils.showError(message);
      });
  },
  deleteReservationRequest: function(id) {
    Utils.showConfirmDialog({
      title: 'Delete Reservation Request?',
      text: "You won't be able to revert this!",
      confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
      if (result.isConfirmed) {
        ReservationManager.performDelete(id);
      }
    });
  },
  performDelete: function(id) {
    ApiService.deleteReservationRequest(id)
      .done(function(response) {
        if (response.success) {
          ReservationManager.reloadTable();
          Utils.showSuccess('Reservation Request has been deleted.');
          StatsManager.load();
        } else {
          Utils.showError(response.message || 'Failed to delete reservation request.');
        }
      })
      .fail(function(xhr) {
        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete reservation request.';
        Utils.showError(message);
      });
  },
  reloadTable: function() {
    $('#reservation-requests-table').DataTable().ajax.reload(null, false);
  },
  populateAcademicTerms: function(selectedTermId) {
    ApiService.fetchAcademicTerms().done(function(response) {
      var $termSelect = $('#edit_academic_term_id');
      $termSelect.empty();
      if (response.success && response.data) {
        response.data.forEach(function(term) {
          $termSelect.append('<option value="' + term.id + '"' + (term.id == selectedTermId ? ' selected' : '') + '>' + term.name + '</option>');
        });
      }
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
  }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
  init: function() { this.bindEvents(); },
  bindEvents: function() {
    var self = this;
    var filterSelectors = '#search_request_number, #search_user_name, #search_status, #search_active, #search_academic_term_id, #search_accommodation_id';
    $(filterSelectors).on('keyup change', function() { self.reloadTable(); });
    $('#clearReservationRequestFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  clearFilters: function() {
    var filterSelectors = '#search_request_number, #search_user_name, #search_status, #search_active, #search_academic_term_id, #search_accommodation_id';
    $(filterSelectors).val('');
    this.reloadTable();
  },
  reloadTable: function() {
    $('#reservation-requests-table').DataTable().ajax.reload();
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var ReservationApp = {
  init: function() {
    StatsManager.init();
    ReservationManager.init();
    SearchManager.init();
  }
};

$(document).ready(function() {
  ReservationApp.init();
});

</script>
@endpush 