@extends('layouts.home')

@section('title', 'My Reservations')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="My Reservations"
        description="View and manage your current housing reservation details below."
        icon="bx bx-calendar"
    >
        <button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="collapse" data-bs-target="#reservationSearchCollapse" aria-expanded="false" aria-controls="reservationSearchCollapse" title="{{ __('reservations.search.button_tooltip') }}">
            <i class="bx bx-search"></i>
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReservationModal">
            <i class="bx bx-plus me-1"></i>New Reservation
        </button>
    </x-ui.page-header>

    {{-- Search Collapse --}}
    <div class="collapse mb-4" id="reservationSearchCollapse">
        <div class="card">
            <div class="card-body">
                <form id="reservationSearchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Property</label>
                            <input type="text" class="form-control" name="property" id="search_property" placeholder="Search by property name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="search_status">
                                <option value="">All Status</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <input type="date" class="form-control" name="date_from" id="search_date_from">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="button" id="resetSearchBtn" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Reservations List --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between gap-4">
                    <div class="card-title mb-0 me-1">
                        <h5 class="mb-0">My Reservations</h5>
                        <p class="mb-0">Manage your housing reservations</p>
                    </div>
                </div>
                <div class="card-body">
                    <div id="reservationCards" class="row g-4 mb-4">
                        {{-- Reservation cards will be rendered here --}}
                        <div class="text-center w-100 py-5" id="reservationLoading">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
                        <div class="d-flex align-items-center gap-4">
                            <div class="text-muted small" id="reservationResultsInfo">
                                {{-- Results info will be rendered here --}}
                            </div>
                            <ul class="pagination mb-0 pagination-rounded" id="reservationPagination">
                                {{-- Pagination will be rendered here --}}
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * My Reservations Page JS
 * Handles: Fetching reservations, rendering cards, paging, search/filter, and actions (cancel, etc)
 */

// ===========================
// TRANSLATION CONSTANTS
// ===========================
var MESSAGES = {
  success: {
    reservationCancelled: @json(__('reservations.messages.success.cancelled'))
  },
  error: {
    reservationLoadFailed: @json(__('reservations.messages.error.load_failed')),
    reservationCancelFailed: @json(__('reservations.messages.error.cancel_failed'))
  },
  confirm: {
    cancelReservation: {
      title: @json(__('reservations.confirm.cancel.title')),
      text: @json(__('reservations.confirm.cancel.text')),
      confirmButtonText: @json(__('reservations.confirm.cancel.button'))
    }
  }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  reservations: {
    fetch: '{{ route("my_reservations.cardData") }}',
    cancel: '{{ route("my_reservations.cancel", ":id") }}'
  }
};


// ===========================
// API SERVICE
// ===========================
var ApiService = {
  fetchReservations: function(params) {
    return $.ajax({
      url: ROUTES.reservations.fetch,
      method: 'GET',
      data: params
    });
  },
  cancelReservation: function(id) {
    return $.ajax({
      url: Utils.replaceRouteId(ROUTES.reservations.cancel, id),
      method: 'POST'
    });
  }
};

// ===========================
// RESERVATION PAGE MANAGER
// ===========================
var MyReservationPage = {
  state: {
    page: 1,
    perPage: 6,
    total: 0,
    totalPages: 1,
    filters: {}
  },
  init: function() {
    this.bindEvents();
    this.fetchAndRender();
  },
  bindEvents: function() {
    var self = this;
    // Search form submit
    $('#reservationSearchForm').on('submit', function(e) {
      e.preventDefault();
      self.state.page = 1;
      self.state.filters = self.getFilters();
      self.fetchAndRender();
    });
    // Reset button
    $('#resetSearchBtn').on('click', function() {
      $('#reservationSearchForm')[0].reset();
      self.state.page = 1;
      self.state.filters = {};
      self.fetchAndRender();
    });
    // Property type filter
    $('#propertyTypeFilter').on('change', function() {
      self.state.page = 1;
      self.state.filters.property_type = $(this).val();
      self.fetchAndRender();
    });
    // Pagination click
    $(document).on('click', '.reservation-page-link', function(e) {
      e.preventDefault();
      var page = $(this).data('page');
      if (page && page !== self.state.page) {
        self.state.page = page;
        self.fetchAndRender();
      }
    });
    // Cancel reservation
    $(document).on('click', '.cancelReservationBtn', function(e) {
      e.preventDefault();
      var reservationId = $(this).data('id');
      self.handleCancelReservation(reservationId);
    });
  },
  getFilters: function() {
    return {
      property: $('#search_property').val(),
      status: $('#search_status').val(),
      date_from: $('#search_date_from').val(),
      property_type: $('#propertyTypeFilter').val()
    };
  },
  fetchAndRender: function() {
    var self = this;
    $('#reservationCards').html('<div class="text-center w-100 py-5" id="reservationLoading"><div class="spinner-border text-primary" role="status"></div></div>');
    var params = Object.assign({}, self.state.filters, { page: self.state.page, per_page: self.state.perPage });
    ApiService.fetchReservations(params)
      .done(function(response) {
        if (response.success && response.data) {
          self.state.total = response.data.total;
          self.state.totalPages = response.data.last_page;
          self.renderCards(response.data.data);
          self.renderPagination();
          self.renderResultsInfo();
        } else {
          self.state.total = 0;
          self.state.totalPages = 1;
          self.renderCards([]);
          self.renderPagination();
          self.renderResultsInfo();
          Utils.showError(MESSAGES.error.reservationLoadFailed);
        }
      })
      .fail(function() {
        self.state.total = 0;
        self.state.totalPages = 1;
        self.renderCards([]);
        self.renderPagination();
        self.renderResultsInfo();
        Utils.showError(MESSAGES.error.reservationLoadFailed);
      });
  },
  renderCards: function(reservations) {
    var $container = $('#reservationCards');
    $container.empty();
    if (!reservations || reservations.length === 0) {
      $container.html('<div class="text-center w-100 py-5 text-muted">No reservations found.</div>');
      // Do not return here, so pagination is always rendered
      return;
    }
    var html = '';
    reservations.forEach(function(res) {
      html += MyReservationPage.renderCard(res);
    });
    $container.html(html);
  },
  renderCard: function(res) {
    // Status badge color
    var statusMap = {
      confirmed: 'bg-label-success',
      pending: 'bg-label-warning',
      cancelled: 'bg-label-danger',
      completed: 'bg-label-secondary',
      upcoming: 'bg-label-primary'
    };
    var statusClass = statusMap[res.status] || 'bg-label-info';
    // Actions
    var actions = '';
    if (res.status === 'pending' || res.status === 'confirmed' || res.status === 'upcoming') {
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-edit me-2"></i>Edit</a></li>`;
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>View Details</a></li>`;
      actions += `<li><hr class="dropdown-divider"></li>`;
      actions += `<li><a class="dropdown-item text-danger cancelReservationBtn" href="#" data-id="${res.id}"><i class="bx bx-trash me-2"></i>Cancel</a></li>`;
    } else if (res.status === 'completed') {
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>View Details</a></li>`;
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-star me-2"></i>Leave Review</a></li>`;
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-copy me-2"></i>Book Again</a></li>`;
    } else if (res.status === 'cancelled') {
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>View Details</a></li>`;
      actions += `<li><a class="dropdown-item" href="#"><i class="bx bx-copy me-2"></i>Rebook</a></li>`;
    }
    // Card
    return `
      <div class="col-lg-6 col-xl-4">
        <div class="card h-100 shadow-none border">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <span class="badge ${statusClass} text-capitalize">${res.status_label || res.status}</span>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <ul class="dropdown-menu">
                  ${actions}
                </ul>
              </div>
            </div>
            <div class="mb-3">
              <h5 class="mb-1">${res.property_name || '-'}</h5>
              <p class="text-muted mb-2">${res.property_address || '-'}</p>
              <div class="d-flex align-items-center mb-2">
                <i class="bx bx-calendar me-2 text-primary"></i>
                <span class="fw-medium">${res.start_date}${res.end_date ? ' - ' + res.end_date : ''}</span>
              </div>
              <div class="d-flex align-items-center mb-2">
                <i class="bx bx-user me-2 text-primary"></i>
                <span>${res.guests || 1} Guest${res.guests > 1 ? 's' : ''}</span>
              </div>
              <div class="d-flex align-items-center">
                <i class="bx bx-dollar me-2 text-primary"></i>
                <span class="fw-medium">${res.price ? '$' + res.price : '-'}</span>
              </div>
            </div>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-outline-primary btn-sm flex-grow-1">
                <i class="bx bx-show me-1"></i>View
              </a>
              ${res.status === 'pending' ? `
                <a href="#" class="btn btn-warning btn-sm flex-grow-1">
                  <i class="bx bx-time me-1"></i>Pending
                </a>
              ` : res.status === 'confirmed' || res.status === 'upcoming' ? `
                <a href="#" class="btn btn-primary btn-sm flex-grow-1">
                  <i class="bx bx-message me-1"></i>Contact
                </a>
              ` : res.status === 'completed' ? `
                <a href="#" class="btn btn-primary btn-sm flex-grow-1">
                  <i class="bx bx-copy me-1"></i>Book Again
                </a>
              ` : res.status === 'cancelled' ? `
                <a href="#" class="btn btn-primary btn-sm flex-grow-1">
                  <i class="bx bx-copy me-1"></i>Rebook
                </a>
              ` : ''}
            </div>
          </div>
        </div>
      </div>
    `;
  },
  renderPagination: function() {
    var self = this;
    var $pagination = $('#reservationPagination');
    $pagination.empty();

    // Always show pagination, even if there is no data
    var currentPage = self.state.page;
    var totalPages = self.state.totalPages;

    // If there are no results, still show a single disabled page
    if (self.state.total === 0) {
      var html = '';
      html += `<li class="page-item disabled"><a class="page-link" href="#">1</a></li>`;
      $pagination.html(html);
      return;
    }

    if (totalPages <= 1) {
      // Show only one page if only one page exists
      var html = '';
      html += `<li class="page-item ${currentPage === 1 ? 'active' : ''}"><a class="page-link reservation-page-link" href="#" data-page="1">1</a></li>`;
      $pagination.html(html);
      return;
    }

    var html = '';

    // First
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
      <a class="page-link reservation-page-link" href="#" data-page="1" aria-label="First">
        <i class="bx bx-chevrons-left icon-sm scaleX-n1-rtl"></i>
      </a>
    </li>`;
    // Prev
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
      <a class="page-link reservation-page-link" href="#" data-page="${Math.max(1, currentPage-1)}" aria-label="Previous">
        <i class="bx bx-chevron-left icon-sm scaleX-n1-rtl"></i>
      </a>
    </li>`;
    // Page numbers
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    for (var i = startPage; i <= endPage; i++) {
      html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link reservation-page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }
    // Next
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
      <a class="page-link reservation-page-link" href="#" data-page="${Math.min(totalPages, currentPage+1)}" aria-label="Next">
        <i class="bx bx-chevron-right icon-sm scaleX-n1-rtl"></i>
      </a>
    </li>`;
    // Last
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
      <a class="page-link reservation-page-link" href="#" data-page="${totalPages}" aria-label="Last">
        <i class="bx bx-chevrons-right icon-sm scaleX-n1-rtl"></i>
      </a>
    </li>`;
    $pagination.html(html);
  },
  renderResultsInfo: function() {
    var self = this;
    var $info = $('#reservationResultsInfo');
    if (self.state.total === 0) {
      $info.text('No results');
      return;
    }
    var from = (self.state.page - 1) * self.state.perPage + 1;
    var to = Math.min(self.state.page * self.state.perPage, self.state.total);
    $info.text(`Showing ${from} to ${to} of ${self.state.total} results`);
  },
  handleCancelReservation: function(reservationId) {
    var self = this;
    Utils.confirmAction(MESSAGES.confirm.cancelReservation)
      .then(function(result) {
        if (result.isConfirmed) {
          ApiService.cancelReservation(reservationId)
            .done(function(response) {
              if (response.success) {
                Utils.showSuccess(MESSAGES.success.reservationCancelled);
                self.fetchAndRender();
              } else {
                Utils.showError(response.message || MESSAGES.error.reservationCancelFailed);
              }
            })
            .fail(function(xhr) {
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : MESSAGES.error.reservationCancelFailed;
              Utils.showError(message);
            });
        }
      });
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  MyReservationPage.init();
});
</script>
@endpush 