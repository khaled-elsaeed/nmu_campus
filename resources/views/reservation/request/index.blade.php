@extends('layouts.home')

@section('title', __('Request Requests'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
        <x-ui.card.stat2 
          color="secondary" 
          icon="bx bx-calendar" 
          :label="__('Total Requests')" 
          id="requests"
          :subStats="[
            'male' => [
              'label' => __('Male Requests'),
              'icon' => 'bx bx-male-sign',
              'color' => 'info'
            ],
            'female' => [
              'label' => __('Female Requests'),
              'icon' => 'bx bx-female-sign',
              'color' => 'danger'
            ]
          ]"
        />
      </div>
      <div class="col-sm-6 col-xl-3">
        <x-ui.card.stat2 
          color="warning" 
          icon="bx bx-time" 
          :label="__('Pending Requests')" 
          id="requests-pending"
          :subStats="[
            'male' => [
              'label' => __('Male Pending Requests'),
              'icon' => 'bx bx-male-sign',
              'color' => 'info'
            ],
            'female' => [
              'label' => __('Female Pending Requests'),
              'icon' => 'bx bx-female-sign',
              'color' => 'danger'
            ]
          ]"
        />
      </div>
      <div class="col-sm-6 col-xl-3">
        <x-ui.card.stat2 
          color="info"
          icon="bx bx-check-circle"
          :label="__('Approved Requests')"
          id="requests-approved"
          :subStats="[
            'male' => [
              'label' => __('Male Approved Requests'),
              'icon' => 'bx bx-male-sign',
              'color' => 'info'
            ],
            'female' => [
              'label' => __('Female Approved Requests'),
              'icon' => 'bx bx-female-sign',
              'color' => 'danger'
            ]
          ]"
        />
      </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
  :title="__('Requests')"
  :description="__('Manage and view all requests.')"
        icon="bx bx-calendar"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
    <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#requestSearchCollapse" aria-expanded="false" aria-controls="requestSearchCollapse">
  <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
    </button>
    </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
  :title="__('Advanced Search')" 
        formId="advancedRequestSearch" 
        collapseId="requestSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_national_id" class="form-label">{{ __('National ID') }}:</label>
            <input type="text" class="form-control" id="search_national_id" name="search_national_id">
        </div>
        <div class="col-md-4">
            <label for="search_status" class="form-label">{{ __('Status') }}:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="" disabled selected>{{ __('Select Status') }}</option>
                <option value="pending">{{ __('Pending') }}</option>
                <option value="confirmed">{{ __('Confirmed') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('Active') }}:</label>
            <select class="form-control" id="search_active" name="search_active">
                <option value="" disabled selected>{{ __('Select Status') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_academic_term" class="form-label">{{ __('Academic Term') }}:</label>
            <select class="form-control" id="search_academic_term" name="search_academic_term">
            </select>
        </div>
        <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearRequestFiltersBtn" type="button">
      <i class="bx bx-x"></i> {{ __('Clear Filters') }}
    </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
    :headers="[
      __('Request Number'),
      __('User'),
      __('Period'),
      __('Status'),
      __('Created'),
      __('Actions')
    ]"
        :columns="[
            ['data' => 'request_number', 'name' => 'request_number'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'period', 'name' => 'period'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('reservation-requests.datatable')"
        table-id="requests-table"
        :filter-fields="[
            'search_national_id',
            'search_status',
            'search_active',
            'search_academic_term'
        ]"
    />

    {{-- ===== ACCEPT REQUEST MODAL ===== --}}
    <x-ui.modal 
        id="acceptRequestModal" 
        :title="__('Accept Request')" 
        size="lg"
        scrollable="true"
        class=""
    >
        <form id="acceptRequestForm" method="POST">
      @csrf
      <input type="hidden" id="request_id" name="request_id">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="accommodation_type" class="form-label">{{ __('Accommodation Type') }} <span class="text-danger">*</span></label>
          <select class="form-control" id="accommodation_type" name="accommodation_type" required>
            <option value="">{{ __('Select Type') }}</option>
            <option value="room">{{ __('Room') }}</option>
            <option value="apartment">{{ __('Apartment') }}</option>
          </select>
        </div>
         <div class="col-md-6 mb-3" id="buildingDiv" style="display:none;">
          <label for="building" class="form-label">{{ __('Building') }} <span class="text-danger">*</span></label>
          <select class="form-control" id="building" name="building_id" required disabled>
            <option value="">{{ __('Select Building') }}</option>
          </select>
        </div>
        <div class="col-md-6 mb-3" id="apartmentDiv" style="display:none;">
          <label for="apartment" class="form-label">{{ __('Apartment') }} <span class="text-danger">*</span></label>
          <select class="form-control" id="apartment" name="accommodation_id" disabled>
            <option value="">{{ __('Select Apartment') }}</option>
          </select>
        </div>
        <div class="col-md-6 mb-3" id="roomDiv" style="display:none;">
          <label for="room" class="form-label">{{ __('Room') }} <span class="text-danger">*</span></label>
          <select class="form-control" id="room" name="accommodation_id" disabled>
            <option value="">{{ __('Select Room') }}</option>
          </select>
        </div>
        <div class="col-md-6 mb-3" id="isFullRoomDiv" style="display: none;">
          <label for="bed_count" class="form-label">{{ __('Room Option') }}</label>
          <select class="form-control" id="bed_count" name="bed_count">
            <option value="">{{ __('Select Option') }}</option>
            <option value="1">{{ __('Single Bedroom') }}</option>
            <option value="2">{{ __('Both (Full Room)') }}</option>
          </select>
        </div>
        <div class="col-12 mb-3">
          <label for="notes" class="form-label">{{ __('Notes') }}</label>
          <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="{{ __('Optional notes for acceptance') }}"></textarea>
        </div>
      </div>
        </form>
        
        @slot('footer')
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bx bx-x me-1"></i> {{ __('Cancel') }}
            </button>
            <button type="button" class="btn btn-success" id="confirmAcceptBtn">
                <i class="bx bx-check me-1"></i> {{ __('Accept Request') }}
            </button>
        @endslot
    </x-ui.modal>
</div>
@endsection

@push('scripts')
<script>
/**
 * Request Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - RequestManager: Handles CRUD and actions for requests
 * - SearchManager: Handles advanced search
 * - SelectManager: Handles dropdown population
 * - AcceptRequestManager: Handles request acceptance modal
 * - RequestApp: Initializes all managers
 */

// ===========================
// TRANSLATION CONSTANTS
// ===========================
var TRANSLATIONS = {
  confirm: {
    cancel: {
      title: @json(__('Cancel Request')),
      text: @json(__('Are you sure you want to cancel this request?')),
      confirmButtonText: @json(__('Yes, cancel it'))
    },
    accept: {
      title: @json(__('Accept Request')),
      text: @json(__('Are you sure you want to accept this request?')),
      confirmButtonText: @json(__('Yes, accept it'))
    }
  },
  placeholders: {
    selectBuilding: @json(__('Select Building')),
    selectApartment: @json(__('Select Apartment')),
    selectRoom: @json(__('Select Room')),
    selectBedroom: @json(__('Select Bedroom')),
    selectTerm: @json(__('Select Term')),
    selectStatus: @json(__('Select Status')),
  },
  messages: {
    fillRequiredFields: @json(__('Please fill all required fields')),
    requestAccepted: @json(__('Request accepted successfully')),
    requestCanceled: @json(__('Request canceled successfully')),
  }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  requests: {
    stats: '{{ route("reservation-requests.stats") }}',
    datatable: '{{ route("reservation-requests.datatable") }}',
    cancel: '{{ route("reservation-requests.cancel", ":id") }}',
    accept: '{{ route("reservation-requests.accept", ":id") }}',
  },
  buildings: {
    all: '{{ route("housing.buildings.all") }}'
  },
  apartments: {
    all: '{{ route("housing.apartments.all", ":id") }}'
  },
  rooms: {
    all: '{{ route("housing.rooms.all", ":id") }}',
  },
  academicTerms: {
    all: '{{ route("academic.academic_terms.all") }}'
  }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  request: function(options) {
    return $.ajax(options);
  },
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.requests.stats, method: 'GET' });
  },
  cancelRequest: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.requests.cancel, id), method: 'POST' });
  },
  acceptRequest: function(id, data) {
    return ApiService.request({ 
      url: Utils.replaceRouteId(ROUTES.requests.accept, id), 
      method: 'POST',
      data: data
    });
  },
  fetchBuildings: function() {
    return ApiService.request({ url: ROUTES.buildings.all, method: 'GET' });
  },
  fetchApartments: function(buildingId) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.apartments.all, buildingId), method: 'GET'});
  },
  fetchRooms: function(apartmentId) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.rooms.all, apartmentId), method: 'GET'});
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
  ],
  subStatsConfig: {
    'requests': ['male', 'female'],
    'requests-pending': ['male', 'female'],
    'requests-approved': ['male', 'female'],
  },
});

// ===========================
// REQUEST MANAGER
// ===========================
var RequestManager = {
  confirmAndCancelRequest: function(requestId) {
    Utils.showConfirmDialog(
      {
        title: TRANSLATIONS.confirm.cancel.title,
        text: TRANSLATIONS.confirm.cancel.text,
        icon: 'warning',
        confirmButtonText: TRANSLATIONS.confirm.cancel.confirmButtonText
      }
    )
      .then(function(result) {
        if (result.isConfirmed) {
          RequestManager.cancelRequest(requestId);
        }
      });
  },
  cancelRequest: function(requestId) {
    ApiService.cancelRequest(requestId)
      .done(function(response) {
        if (response.success) {
          Utils.reloadDataTable('#requests-table');
          Utils.showSuccess(response.message);
          StatsManager.refresh();
        } else {
          Utils.showError(response.message);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  }
};

// ===========================
// ACCEPT REQUEST MANAGER
// ===========================
var AcceptRequestManager = {  
  init: function() {
    this.bindEvents();
    this.initializeDropdowns();
    this.handleAccommodationType();
  },
  
  bindEvents: function() {
    var self = this;
    // Modal events
    $('#acceptRequestModal').on('show.bs.modal', function(event) {
      var triggerButton = event.relatedTarget;
      var accommodationType = triggerButton ? $(triggerButton).data('accommodation-type') : null;
      var bedCount = triggerButton ? $(triggerButton).data('bed-count') : null;
      var requestId = triggerButton ? $(triggerButton).data('id') : null;
      self.resetModal();
      $('#accommodation_type').prop('disabled', false).val(accommodationType).trigger('change').prop('disabled', true);
      $('#bed_count').val(bedCount).prop('disabled', true);
      $('#request_id').val(requestId);
      console.log(accommodationType, bedCount, requestId);
    });
    // Accommodation type change
    $('#accommodation_type').on('change', function() {
      self.handleAccommodationType();
    });
    // Building change event
    $('#building').on('change', function() {
      var buildingId = $(this).val();
      if (buildingId) {
        self.loadApartments(buildingId);
      } else {
        self.clearApartments();
        self.clearRooms();
        self.hideBedroom();
      }
    });
    // Apartment change event
    $('#apartment').on('change', function() {
      var apartmentId = $(this).val();
      if (apartmentId) {
        self.loadRooms(apartmentId);
      } else {
        self.clearRooms();
        self.hideBedroom();
      }
    });
    // Room change event
    $('#room').on('change', function() {
      var roomId = $(this).val();
      var roomType = $(this).find('option:selected').data('type');
      if (roomId) {
        self.checkRoomType(roomType);
      } else {
        self.hideBedroom();
        $('#isFullRoomDiv').hide();
      }
    });
    // Confirm accept button
    $('#confirmAcceptBtn').on('click', function() {
      self.submitAcceptance();
    });
  },

  handleAccommodationType: function() {
    var type = $('#accommodation_type').val();
    if (type === 'room') {
      $('#buildingDiv').show();
      $('#apartmentDiv').show();
      $('#roomDiv').show();
      $('#room').prop('disabled', true).attr('name', 'accommodation_id');
      $('#apartment').prop('disabled', true).removeAttr('name');
      $('#building').prop('disabled', false);
      this.clearRooms();
      this.clearApartments();
    } else if (type === 'apartment') {
      $('#buildingDiv').show();
      $('#apartmentDiv').show();
      $('#roomDiv').hide();
      $('#apartment').prop('disabled', true).attr('name', 'accommodation_id');
      $('#room').prop('disabled', true).removeAttr('name');
      $('#building').prop('disabled', false);
      this.clearRooms();
      this.clearApartments();
    } else {
      $('#buildingDiv').hide();
      $('#apartmentDiv').hide();
      $('#roomDiv').hide();
      $('#apartment').prop('disabled', true).removeAttr('name');
      $('#room').prop('disabled', true).removeAttr('name');
      $('#building').prop('disabled', true);
      $('#bedroomSelectionDiv').hide();
      $('#isFullRoomDiv').hide();
      this.clearRooms();
      this.clearApartments();
    }
  },
  
  initializeDropdowns: function() {
    this.loadBuildings();
  },
  
  resetModal: function() {
  $('#acceptRequestForm')[0].reset();
  $('#apartment').prop('disabled', true).empty().append('<option value="">' + TRANSLATIONS.placeholders.selectApartment + '</option>');
  $('#room').prop('disabled', true).empty().append('<option value="">' + TRANSLATIONS.placeholders.selectRoom + '</option>');
  $('#building').prop('disabled', true);
  $('#buildingDiv').hide();
  $('#apartmentDiv').hide();
  $('#roomDiv').hide();
  this.hideBedroom();
  Select2Manager.resetModalSelect2();
  },
  
  loadBuildings: function() {
    ApiService.fetchBuildings()
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#building', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectBuilding
          }, true);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  
  loadApartments: function(buildingId) {
    var self = this;
    ApiService.fetchApartments(buildingId)
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#apartment', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectApartment
          }, true);
          $('#apartment').prop('disabled', false); 
        } else {
          self.clearApartments();
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        self.clearApartments();
      });
  },
  
  loadRooms: function(apartmentId) {
    var self = this;
    ApiService.fetchRooms(apartmentId)
      .done(function(response) {
        if (response.success && response.data) {
          Utils.populateSelect('#room', response.data, {
            valueField: 'id',
            textField: 'number',
            placeholder: TRANSLATIONS.placeholders.selectRoom,
            dataAttributes: { type: 'type' }
          }, true);
          $('#room').prop('disabled', false);
        } else {
          self.clearRooms();
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        self.clearRooms();
      });
  },

  checkRoomType: function(roomType) {
    if (roomType === 'double') {
      this.showBedroom();
      $('#isFullRoomDiv').show();
    } else {
      this.hideBedroom();
      $('#isFullRoomDiv').hide();
    }
  },
  
  showBedroom: function() {
    $('#bedroomSelectionDiv').show();
    $('#bedroom').prop('required', true);
  },
  
  hideBedroom: function() {
    $('#bedroomSelectionDiv').hide();
    $('#bedroom').prop('required', false).val('');
  },
  
  clearApartments: function() {
    $('#apartment')
      .prop('disabled', true)
      .empty()
      .append('<option value="">' + TRANSLATIONS.placeholders.selectApartment + '</option>');
    this.clearRooms();
  },
  
  clearRooms: function() {
    $('#room')
      .prop('disabled', true)
      .empty()
      .append('<option value="">' + TRANSLATIONS.placeholders.selectRoom + '</option>');
    this.hideBedroom();
  },
  
  validateForm: function() {
    var building = $('#building').val();
    var type = $('#accommodation_type').val();
    var accommodationId = type === 'room' ? $('#room').val() : $('#apartment').val();
    var bedroom = $('#bedroom').val();
    var isBedroomRequired = $('#bedroom').prop('required');
    if (!building || !type || !accommodationId) {
      return false;
    }
    if (type === 'room' && isBedroomRequired && !bedroom) {
      return false;
    }
    return true;
  },
  
  submitAcceptance: function() {
    var self = this;
    
    if (!this.validateForm()) {
      Utils.showError(TRANSLATIONS.messages.fillRequiredFields);
      return;
    }
    
    var type = $('#accommodation_type').val();
    var requestId = $('#request_id').val(); 

    var formData = {
      accommodation_type: type,
      accommodation_id: type === 'room' ? $('#room').val() : $('#apartment').val(),
      bed_count: $('#bed_count').val() || null,
      notes: $('#notes').val() || null
    };

    ApiService.acceptRequest(requestId, formData)
      .done(function(response) {
        if (response.success) {
          $('#acceptRequestModal').modal('hide');
          Utils.reloadDataTable('#requests-table');
          Utils.showSuccess(response.message);
          StatsManager.refresh();
        } else {
          Utils.showError(response.message);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
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
      '#search_national_id',
      '#search_status',
      '#search_active',
      '#search_academic_term'
    ].join(', ');
    $(filterSelectors).on('keyup change', function() {Utils.reloadDataTable('#requests-table');});
    $('#clearRequestFiltersBtn').on('click', function() { self.clearFilters(); });
  },
  clearFilters: function() {
    var filterSelectors = [
      '#search_national_id',
      '#search_status',
      '#search_active',
      '#search_academic_term'
    ].join(', ');
    $(filterSelectors).val('');
    Utils.reloadDataTable('#requests-table');
    Select2Manager.resetSearchSelect2();
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
          Utils.populateSelect('#search_academic_term', response.data, {
            valueField: 'id',
            textField: 'name',
            placeholder: TRANSLATIONS.placeholders.selectTerm
          }, true);
        } else {
          SelectManager.clearSelect('#search_academic_term', TRANSLATIONS.placeholders.selectTerm);
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        SelectManager.clearSelect('#search_academic_term', TRANSLATIONS.placeholders.selectTerm);
      });
  },
  
  clearSelect: function(selector, placeholder) {
    $(selector).empty().append('<option value="">' + placeholder + '</option>');
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
            '#search_status': { placeholder: TRANSLATIONS.placeholders.selectStatus },
            '#search_active': { placeholder: TRANSLATIONS.placeholders.selectStatus },
            '#search_academic_term': { placeholder: TRANSLATIONS.placeholders.selectTerm }
        },
        modal: {
            '#building': { placeholder: TRANSLATIONS.placeholders.selectBuilding, dropdownParent: $('#acceptRequestModal') },
            '#apartment': { placeholder: TRANSLATIONS.placeholders.selectApartment, dropdownParent: $('#acceptRequestModal') },
            '#room': { placeholder: TRANSLATIONS.placeholders.selectRoom, dropdownParent: $('#acceptRequestModal') },
            '#bedroom': { placeholder: TRANSLATIONS.placeholders.selectBedroom, dropdownParent: $('#acceptRequestModal') }
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
     * Initialize all modal Select2 elements
     */
    initModalSelect2: function() {
        Object.keys(this.config.modal).forEach(function(selector) {
            Utils.initSelect2(selector, Select2Manager.config.modal[selector]);
        });
    },

    /**
     * Initialize all Select2 elements
     */
    initAll: function() {
        this.initSearchSelect2();
        this.initModalSelect2();
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
     * Reset modal Select2 elements
     */
    resetModalSelect2: function() {
        this.clearSelect2(Object.keys(this.config.modal));
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function(selector) {
        if (selector == null || selector === undefined) {
            this.clearSelect2(Object.keys(this.config.search));
        } else {
            this.clearSelect2([selector]);
        }
    }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var RequestApp = {
  init: function() {
    StatsManager.init();
    AcceptRequestManager.init();
    SearchManager.init();
    SelectManager.init();
    Select2Manager.initAll();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  RequestApp.init();
});

</script>
@endpush