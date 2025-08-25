@extends('layouts.home')

@section('title', __('Resident Home'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Welcome Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow bg-light rounded-4">
        <div class="card-body p-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
          <div class="text-center text-md-start flex-fill">
            <h5 class="fw-bold text-primary mb-1" id="welcomeMessage">
              {{ __('Welcome Back') }} <span id="resident-name"><div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div></span>
            </h5>
            <div class="text-muted small mt-2" id="lastLoginInfo">
              <i class="bx bx-time me-1 text-secondary"></i>
              {{ __('Last Login') }} <span id="last-login"><div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div></span>
            </div>
          </div>
          <div class="text-center flex-shrink-0">
            <img 
              src="{{ asset('img/svg/house_restyling_cuate.svg') }}" 
              alt="Welcome Home" 
              class="img-fluid"
              style="width:120px; height:120px; object-fit:contain; background:#fff; border-radius:.75rem;"
            >
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Information Cards -->
  <div class="row g-4 mb-4">
    <!-- Current Housing Assignment Card -->
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100 bg-white">
        <div class="card-header bg-white border-bottom-0 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between py-3 gap-2">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
              <i class="bx bx-buildings fs-5 text-primary"></i>
            </div>
            <h6 class="mb-0 fw-semibold text-primary">{{ __('Current Housing Assignment') }}</h6>
          </div>
          <span class="badge bg-success rounded-pill px-3 py-2 d-flex align-items-center">
            <i class="bx bx-check-circle me-1"></i> {{ __('Active') }}
          </span>
        </div>
        <div class="card-body" id="housingAssignment">
          <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <div class="row g-3 d-none" id="housingAssignmentDetails">
              <div class="col-6 col-lg-12 col-xl-6">
                <div class="d-flex align-items-center">
                  <span class="text-primary fw-medium me-2">
                    <i class="bx bx-buildings me-1"></i>Building:
                  </span>
                  <span class="fw-semibold text-dark" id="buildingNumber"></span>
                </div>
              </div>
              <div class="col-6 col-lg-12 col-xl-6">
                <div class="d-flex align-items-center">
                  <span class="text-primary fw-medium me-2">
                    <i class="bx bx-door-open me-1"></i>Room:
                  </span>
                  <span class="fw-semibold text-dark" id="roomNumber"></span>
                </div>
              </div>
              <div class="col-6 col-lg-12 col-xl-6">
                <div class="d-flex align-items-center">
                  <span class="text-primary fw-medium me-2">
                    <i class="bx bx-layer me-1"></i>Apartment:
                  </span>
                  <span class="fw-semibold text-dark" id="apartmentNumber"></span>
                </div>
              </div>
              <div class="col-6 col-lg-12 col-xl-6">
                <div class="d-flex align-items-center">
                  <span class="text-primary fw-medium me-2">
                    <i class="bx bx-calendar me-1"></i>Contract Period:
                  </span>
                  <span class="fw-semibold text-dark" id="contractPeriod"></span>
                </div>
              </div>
            </div>
            <div class="row d-none" id="housingAssignmentEmptyDetails">
              <div class="col-12">
                <div class="text-center py-4">
                  <i class="bx bx-home fs-1 text-muted"></i>
                  <p class="mt-2 mb-0 text-muted">{{ __('No active reservation found.') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Neighbors Card -->
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100 bg-white">
        <div class="card-header bg-white border-bottom-0 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between py-3 gap-2">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
              <i class="bx bx-group fs-5 text-primary"></i>
            </div>
            <h6 class="mb-0 fw-semibold text-primary">{{ __('Neighbors') }}</h6>
          </div>
          <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#neighborsModal">
            <i class="bx bx-info-circle me-1"></i>{{ __('View All') }}
          </button>
        </div>
        <div class="card-body" id="neighborsPreview">
          <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Housing Reservation Requests -->
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom-0 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between py-3 gap-2">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
              <i class="bx bx-buildings fs-5 text-primary"></i>
            </div>
            <h6 class="mb-0 fw-semibold text-primary">
              {{ __('Reservation Requests') }}
              <span class="badge bg-light text-primary fw-medium px-2 py-1 ms-1" id="requestsCount">
                0 {{ __('Total') }}
              </span>
            </h6>
          </div>
          <button class="btn btn-outline-primary btn-sm" id="btnNewRequest">
            <i class="bx bx-plus me-1"></i>{{ __('New Request') }}
          </button>
        </div>
        <div class="card-body p-0" id="reservationRequests">
          <div class="d-flex justify-content-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Helpful Resources -->
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom-0 d-flex align-items-center justify-content-between py-3">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
              <i class="bx bx-info-circle fs-5 text-primary"></i>
            </div>
            <h6 class="mb-0 fw-semibold text-primary">
              {{ __('Helpful Resources') }}
            </h6>
          </div>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            <button type="button" class="list-group-item list-group-item-action border-0 rounded mb-2 d-flex align-items-center justify-content-between py-3 hover-bg-light" data-bs-toggle="modal" data-bs-target="#eventsModal">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-xs bg-warning bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                  <i class="bx bx-calendar-event text-white"></i>
                </div>
                <div>
                  <h6 class="mb-1 fw-medium">{{ __('Campus Events') }}</h6>
                  <small class="text-muted">{{ __('Discover upcoming activities') }}</small>
                </div>
              </div>
              <i class="bx bx-chevron-right text-muted"></i>
            </button>

            <button type="button" class="list-group-item list-group-item-action border-0 rounded d-flex align-items-center justify-content-between py-3 hover-bg-light" data-bs-toggle="modal" data-bs-target="#supportContactsModal">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-xs bg-danger bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                  <i class="bx bx-shield-alt-2 text-white"></i>
                </div>
                <div>
                  <h6 class="mb-1 fw-medium">{{ __('Support Contacts') }}</h6>
                  <small class="text-muted">{{ __('Important phone numbers') }}</small>
                </div>
              </div>
              <i class="bx bx-chevron-right text-muted"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Neighbors Modal -->
<x-ui.modal 
  id="neighborsModal" 
  :title="__('Your Neighbors')" 
  size="lg" 
  :scrollable="true">
  <div id="neighborsModalContent">
    <div class="d-flex justify-content-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
  <x-slot name="footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <button type="button" class="btn btn-primary">
      <i class="bx bx-message-square-dots me-1"></i>{{ __('Contact All') }}
    </button>
  </x-slot>
</x-ui.modal>

<!-- Request Details Modal -->
<x-ui.modal 
  id="requestDetailsModal" 
  :title="__('Reservation Request Details')" 
  size="md" 
  :scrollable="true">
  <div id="requestDetailsContent">
    <div class="d-flex justify-content-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
  <x-slot name="footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
  </x-slot>
</x-ui.modal>

<!-- Support Contacts Modal -->
<x-ui.modal 
  id="supportContactsModal" 
  :title="__('Support Contacts')" 
  :scrollable="true">

  <div id="supportContactsModalContent">
    <div class="row g-3">

      <!-- #1 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">1</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Shaimaa Abdel-Mardi Salem')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Manager')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 101')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01061612433</p>
          </div>
        </div>
      </div>

      <!-- #2 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">2</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Hind Saad Sabry Abdel Hamid')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 102')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01062951959</p>
          </div>
        </div>
      </div>

      <!-- #3 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">3</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Mohamed Ali Said Ahmed Dawood')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 103')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01224985859</p>
          </div>
        </div>
      </div>

      <!-- #4 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">4</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Ismail Mohamed Ismail')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 104')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01144246163</p>
          </div>
        </div>
      </div>

      <!-- #5 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">5</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Mohamed Mohamed El-Rahmany')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 105')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01018020302</p>
          </div>
        </div>
      </div>

      <!-- #6 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">6</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Nagwa Ibrahim Ahmed Mohamed')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Housing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 1 - Apartment 106')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01061363217</p>
          </div>
        </div>
      </div>

      <!-- #7 -->
      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <span class="badge bg-secondary mb-2">7</span>
            <h6 class="fw-bold text-dark mb-1">{{__('Nourhan Magdy Mohamed')}}</h6>
            <span class="badge bg-primary mb-2">{{__('Nursing Specialist')}}</span>
            <p class="text-sm text-muted mb-1"><i class="bx bx-building-house me-1 text-info"></i> {{__('Building 2 - Apartment 202')}}</p>
            <p class="text-sm text-muted mb-1"><i class="bx bx-phone me-1 text-success"></i> 01029303010</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <x-slot name="footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('اغلاق') }}</button>
  </x-slot>
</x-ui.modal>


<!-- Campus Events Modal -->
<x-ui.modal 
  id="eventsModal" 
  :title="__('Upcoming Campus Events')" 
  size="lg" 
  :scrollable="true">
  <div id="eventsModalContent">
    <div class="d-flex justify-content-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>

  <x-slot name="footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
  </x-slot>
</x-ui.modal>

<!-- New Reservation Request Modal -->
<x-ui.modal 
  id="newRequestModal" 
  :title="__('New Reservation Request')" 
  size="lg" 
  :scrollable="true">
  <form id="newRequestForm" class="p-2">
    <div id="newRequestFormBody">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">{{ __('Period') }}</label>
          <div class="d-flex gap-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="period_type" id="periodAcademic" value="academic">
              <label class="form-check-label" for="periodAcademic">{{ __('Academic') }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="period_type" id="periodCalendar" value="calendar">
              <label class="form-check-label" for="periodCalendar">{{ __('Calendar') }}</label>
            </div>
          </div>
        </div>

        <!-- Academic Period Block -->
        <div class="col-12 d-none" id="academicBlock">
          <div class="mb-2">
            <label class="form-label">{{ __('Academic Term') }}</label>
            <select class="form-select" name="academic_term_id" id="academicTermId">
            </select>
          </div>
        </div>

        <!-- Calendar Period Block -->
        <div class="col-12 d-none" id="calendarBlock">
          <div class="mb-2">
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">{{ __('Start Date') }}</label>
                <input type="date" class="form-control" name="start_date" id="startDate">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('End Date') }}</label>
                <input type="date" class="form-control" name="end_date" id="endDate">
              </div>
            </div>
          </div>
        </div>

        <!-- Sibling Choice Section -->
        <div class="div" id="siblingChoiceWrap">
               <!-- Sibling Choice -->
          <div class="mb-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="sibling_choice" id="staySibling" value="with">
              <label class="form-check-label" for="staySibling">{{ __('Stay with sibling') }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="sibling_choice" id="stayAlone" value="alone">
              <label class="form-check-label" for="stayAlone">{{ __('Alone') }}</label>
            </div>
          </div>

          <!-- Sibling Selection -->
          <div id="siblingSelectWrap" class="d-none mb-2">
            <small class="text-info d-block mb-1">
              <i class="bx bx-info-circle me-1"></i>
              {{ __('You and your sibling will share a double room (one bed each). Price differs from single room.') }}
            </small>
            <select class="form-select" name="sibling_id" id="siblingId">
              <option value="">{{ __('Sibling') }}</option>
            </select>
          </div>
        </div>
   
        <!-- Room Preference Section -->
        <div class="col-12 d-none" id="roomPrefWrap">
          <label class="form-label">{{ __('Room Preference') }}</label>

          <!-- Room Type Selection -->
          <div class="mb-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="room_type" id="roomSingle" value="single">
              <label class="form-check-label" for="roomSingle">{{ __('Single room') }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="room_type" id="roomDouble" value="double">
              <label class="form-check-label" for="roomDouble">{{ __('Double room') }}</label>
            </div>
          </div>

          <!-- Last Term Option -->
          <div id="lastTermWrap" class="mb-2 d-none">
            <label class="form-label" id="lastTermLabel">{{ __('Last term option') }}</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="last_term_choice" id="lastOldRoom" value="old">
              <label class="form-check-label" for="lastOldRoom">{{ __('Take last room') }}</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="last_term_choice" id="lastRandom" value="random">
              <label class="form-check-label" for="lastRandom">{{ __('Random room') }}</label>
            </div>
          </div>

          <!-- Bed Selection -->
          <div id="doubleBedWrap" class="mb-2 d-none">
            <label class="form-label">{{ __('Bed selection') }}</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="bed_count" id="singleBed" value="1">
              <label class="form-check-label" for="singleBed">{{ __('Single bed') }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="bed_count" id="bothBeds" value="2">
              <label class="form-check-label" for="bothBeds">{{ __('Both beds') }}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <x-slot name="footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" id="btnSubmitNewRequest" class="btn btn-primary" disabled>
      <i class="bx bx-check me-1"></i>{{ __('Create') }}
    </button>
  </x-slot>
</x-ui.modal>

@endsection

@push('scripts')
<script>
/**
 * Resident Home Page JS
 *
 * Structure:
 * - DATA_CONSTANTS: Static data for the page
 * - TRANSLATION: Translation constants
 * - HousingManager: Manages housing assignment display
 * - NeighborsManager: Manages neighbors display
 * - RequestsManager: Manages reservation requests
 * - NewRequestManager: Manages new reservation requests
 * - EventsManager: Manages campus events
 * - ResidentHomeApp: Initializes all managers
 */

// ===========================
// DATA CONSTANTS
// ===========================
const ROUTES = {
  UserDetails: '{{route('home.student.user-detail')}}',
  ActiveReservation: '{{route('home.student.active-reservation-details')}}',
  ActiveReservationNeighbors: '{{route('home.student.active-reservation-neighbors')}}',
  UpcomingEvents: '{{route('home.student.upcoming-events')}}',
  ReservationRequests: '{{route('home.student.reservation-requests')}}',
  NewRequestData: '{{route('home.student.new-request-data')}}',
  CreateReservationRequest: '{{route('reservation-requests.store')}}',
  academicTerms: { all: '{{ route("academic.academic_terms.all") }}' }
};

// ===========================
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
  labels: {
    building: @json(__('Building')),
    room: @json(__('Room')),
    floor: @json(__('Floor')),
    contractPeriod: @json(__('Contract Period')),
    phone: @json(__('Phone')),
    email: @json(__('Email')),
    program: @json(__('Program')),
    year: @json(__('Year')),
    interests: @json(__('Interests')),
    supportContact: @json(__('Support Contact')),
    date: @json(__('Date')),
    time: @json(__('Time')),
    location: @json(__('Location')),
    office: @json(__('Office')),
    hours: @json(__('Hours')),
    bed: @json(__('Bed')),
    total: @json(__('Total')),
    period: @json(__('Period')),
    academic: @json(__('Academic')),
    calendar: @json(__('Calendar')),
    stayWithSibling: @json(__('Stay with sibling')),
    alone: @json(__('Alone')),
    roomPreference: @json(__('Room Preference')),
    singleRoom: @json(__('Single room')),
    doubleRoom: @json(__('Double room')),
    sibling: @json(__('Sibling')),
    lastTermOption: @json(__('Last term option')),
    takeOldRoom: @json(__('Take last room')),
    randomRoom: @json(__('Random room')),
    bedSelection: @json(__('Bed selection')),
    singleBed: @json(__('Single bed')),
    bothBeds: @json(__('Both beds'))
  },
  buttons: {
    call: @json(__('Call')),
    email: @json(__('Email')),
    moreInfo: @json(__('More Info'))
  },
  messages: {
    noNeighbors: @json(__('No neighbors found.')),
    noRequests: @json(__('No Requests Yet')),
    noRequestsDesc: @json(__('Start by submitting your housing reservation request.')),
    submitFirstRequest: @json(__('Submit First Request')),
    noEvents: @json(__('No Events Scheduled')),
    noEventsDesc: @json(__('Check back later for upcoming campus events.')),
    siblingNote: @json(__('You and your sibling will share a double room (one bed each). Price differs from single room.')),
    formIncomplete: @json(__('Please complete required fields.'))
  },
  status: {
    approved: @json(__('Approved')),
    pending: @json(__('Pending')),
    rejected: @json(__('Rejected'))
  },
  placeholders: {
    selectTerm: @json(__('Select Term')),
  }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
  /**
   * Generic AJAX request
   * @param {object} options
   * @returns {jqXHR}
   */
  request(options) {
    return $.ajax(options);
  },
  /**
   * Get resident data
   * @returns {object}
   */
  fetchResidentData() {
    return ApiService.request({ url: ROUTES.UserDetails, method: 'GET' });
  },
  /**
   * Get neighbors data
   * @returns {array}
   */
  fetchNeighbors() {
    return ApiService.request({ url: ROUTES.ActiveReservationNeighbors, method: 'GET' });
  },
  /**
   * Get reservation requests
   * @returns {array}
   */
  fetchReservationRequests() {
    return ApiService.request({ url: ROUTES.ReservationRequests, method: 'GET' });
  },
  /**
   * Get active reservation details
   * @returns {object}
   */
  fetchActiveReservation() {
    return ApiService.request({ url: ROUTES.ActiveReservation, method: 'GET' });
  },
  /**
   * Get upcoming events
   * @returns {array}
   */
  fetchUpcomingEvents() {
    return ApiService.request({ url: ROUTES.UpcomingEvents, method: 'GET' });
  },
  /**
   * Get data for new reservation request (siblings, last term info)
   */
  fetchNewRequestData() {
    return ApiService.request({ url: ROUTES.NewRequestData, method: 'GET' });
  },
  /**
   * Get all academic terms
   */
  fetchAcademicTerms() {
    return ApiService.request({ url: ROUTES.academicTerms.all, method: 'GET' });
  },
  /**
   * Create reservation request
   */
  createReservationRequest(payload) {
    return ApiService.request({ url: ROUTES.CreateReservationRequest, method: 'POST', data: payload });
  }
  
};

// ===========================
// HOUSING MANAGER
// ===========================
const HousingManager = {
  /**
   * populate Resident data
   */
  populateResidentData(){
    ApiService.fetchResidentData()
      .done(function(response){
        const data = response?.data || {};
        const name = data.username || '-';
        const lastLogin = data.last_login || '-';

        $('#resident-name').text(name);
        $('#last-login').text(lastLogin);
      })
      .fail(function(xhr){
        $('#resident-name').text('-');
        $('#last-login').text('-');
          Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        
      });
  },
  /**
   * Render housing assignment
   */
  renderHousingAssignment() {
    ApiService.fetchActiveReservation()
        .done(function(response) {
          const data = response.data;
          // Hide loading spinner
          $('#housingAssignment .spinner-border').addClass('d-none');
          if (data && data.building && data.room && data.apartment && data.contract_start && data.contract_end) {
            $('#buildingNumber').text(data.building);
            $('#roomNumber').text(data.room);
            $('#apartmentNumber').text(data.apartment);
            $('#contractPeriod').text(`${data.contract_start} - ${data.contract_end}`);
            $('#housingAssignmentDetails').removeClass('d-none');
          } else {
            $('#housingAssignmentEmptyDetails').removeClass('d-none');
          }
        })
        .fail(function(xhr) {
            // Hide loading spinner and show empty state on error
            $('#housingAssignment .spinner-border').addClass('d-none');
            $('#housingAssignmentEmptyDetails').removeClass('d-none');
            Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
        });
  },
  /**
   * Initialize housing manager
   */
  init() {
    setTimeout(() => {
      this.populateResidentData();
      this.renderHousingAssignment();
    }, 500);
  }
};

// ===========================
// NEIGHBORS MANAGER
// ===========================
const NeighborsManager = {
  neighbors: [], // store neighbors globally within the manager

  /**
   * Render neighbors preview
   */
  renderNeighborsPreview() {
    ApiService.fetchNeighbors()
      .done((response) => {
        this.neighbors = response?.data || [];
        if (this.neighbors.length === 0) {
          $('#neighborsPreview').html(
            `<span class="mb-2 text-muted">${TRANSLATION.messages.noNeighbors}</span>`
          );
          return;
        }

        const html = `
          <div class="row g-2">
            ${this.neighbors
              .map(
                (neighbor) => `
              <div class="col-6 col-md-4 col-lg-12 col-xl-4">
                <div class="d-flex flex-column align-items-center text-center p-2">
                  <div class="avatar avatar-xs bg-light rounded-circle mb-2 d-flex align-items-center justify-content-center border border-1 border-secondary">
                    <i class="bx bx-user text-secondary"></i>
                  </div>
                  <span class="fw-semibold text-dark small">${neighbor.name}</span>
                  <span class="text-muted small">
                    <i class="bx bx-door-open me-1 text-primary"></i>${neighbor.room_number}
                  </span>
                </div>
              </div>
            `
              )
              .join('')}
          </div>
        `;
        $('#neighborsPreview').html(html);
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  /**
   * Render neighbors modal
   */
  renderNeighborsModal() {
    if (!this.neighbors || this.neighbors.length === 0) {
      $('#neighborsModalContent').html(
        `<div class="text-center text-muted p-4">${TRANSLATION.messages.noNeighbors}</div>`
      );
      return;
    }

    const html = `
      <div class="row g-3">
        ${this.neighbors
          .map(
            (neighbor) => `
          <div class="col-12">
            <div class="card border-0 bg-light">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                    <i class="bx bx-user text-primary fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-2">${neighbor.name}</h6>
                    <div class="row g-2 mb-3">
                      <div class="col-sm-6">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-door-open me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.room}:</span>
                          <span class="ms-1">${neighbor.room_number}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-phone me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.phone}:</span>
                          <span class="ms-1">${neighbor.phone || '-'}</span>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-envelope me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.email}:</span>
                          <span class="ms-1">${neighbor.email || '-'}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-book me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.program}:</span>
                          <span class="ms-1">${neighbor.program || '-'}</span>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-calendar me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.year}:</span>
                          <span class="ms-1">${neighbor.year || '-'}</span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <span class="fw-medium text-primary me-2">${TRANSLATION.labels.interests}:</span>
                      ${(Array.isArray(neighbor.interests) ? neighbor.interests : [])
                        .map((interest) => `<span class="badge bg-light text-dark me-1">${interest}</span>`)
                        .join('')}
                    </div>
              
                  </div>
                </div>
              </div>
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
    $('#neighborsModalContent').html(html);
  },

  /**
   * Bind modal events
   */
  bindModalEvents() {
    $('#neighborsModal').on('show.bs.modal', () => {
      this.renderNeighborsModal();
    });
  },

  /**
   * Initialize neighbors manager
   */
  init() {
    setTimeout(() => {
      this.renderNeighborsPreview();
    }, 600);
    this.bindModalEvents();
  },
};

// ===========================
// REQUESTS MANAGER
// ===========================
const RequestsManager = {
  requests: [],
  /**
   * Get status badge class
   */
  getStatusBadgeClass(status) {
    const classes = {
      approved: 'bg-success',
      pending: 'bg-warning',
      rejected: 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
  },

  /**
   * Get localized status text
   */
  getStatusText(status) {
    return (TRANSLATION?.status?.[status]) || status || '-';
  },

  /**
   * Get status icon class
   */
  getStatusIconClass(status) {
    const classes = {
      approved: 'bx-check-circle text-white',
      pending: 'bx-time text-white',
      rejected: 'bx-x-circle text-white'
    };
    return classes[status] || 'bx-list-check text-secondary';
  },

  /**
   * Get status background class
   */
  getStatusBgClass(status) {
    const classes = {
      approved: 'bg-success bg-opacity-10',
      pending: 'bg-warning bg-opacity-10',
      rejected: 'bg-danger bg-opacity-10'
    };
    return classes[status] || 'bg-secondary bg-opacity-10';
  },

  /**
   * Render reservation requests
   */
  renderReservationRequests() {
    ApiService.fetchReservationRequests()
      .done(function(response) {
        const requests = response?.data || [];
        RequestsManager.requests = requests;
        $('#requestsCount').text(`${requests.length} ${TRANSLATION.labels.total}`);

        if (requests.length === 0) {
          $('#reservationRequests').html(`
            <div class="text-center py-5">
              <div class="avatar avatar-xl bg-light rounded-circle mx-auto mb-4">
                <i class="bx bx-list-check text-muted" style="font-size: 2rem;"></i>
              </div>
              <h6 class="fw-semibold mb-2">${TRANSLATION.messages.noRequests}</h6>
              <p class="text-muted mb-4">${TRANSLATION.messages.noRequestsDesc}</p>
              <button class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>${TRANSLATION.messages.submitFirstRequest}
              </button>
            </div>
          `);
          return;
        }

        const isScrollable = requests.length > 4;
        const wrapperStyle = isScrollable ? 'style="max-height: 320px; overflow:auto;"' : '';
        const html = `
          <div class="list-group list-group-flush" ${wrapperStyle}>
            ${requests
              .map((request, index) => `
                <div class="list-group-item border-0 border-bottom py-2 ${index === requests.length - 1 ? 'border-0' : ''}">
                  <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                      <div class="avatar avatar-xs rounded-circle d-flex align-items-center justify-content-center ${RequestsManager.getStatusBgClass(request.status)}">
                        <i class="bx ${RequestsManager.getStatusIconClass(request.status)}"></i>
                      </div>
                      <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 w-100 min-w-0">
                        <span class="fw-semibold text-truncate">${request.request_number}</span>
                        <span class="badge ${RequestsManager.getStatusBadgeClass(request.status)} fw-medium">${RequestsManager.getStatusText(request.status)}</span>
                        <span class="text-muted text-sm d-none d-md-inline-flex"><i class="bx bx-calendar me-1 text-primary"></i>${request.created_at}</span>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                      <span class="badge bg-light text-dark border d-none d-lg-inline-flex"><i class="bx bx-buildings me-1 text-primary"></i>${request.accommodation_type}</span>
                      <span class="badge bg-light text-dark border d-none d-lg-inline-flex"><i class="bx bx-door-open me-1 text-primary"></i>${request.room_type}</span>
                      <button type="button" class="btn btn-sm btn-outline-primary js-request-info" data-index="${index}" title="More info">
                        <i class="bx bx-info-circle"></i>
                      </button>
                    </div>
                  </div>
                </div>
              `)
              .join('')}
          </div>
        `;

        $('#reservationRequests').html(html);
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  /**
   * Populate and show details modal
   */
  populateRequestDetailsModal(request) {
    const html = `
      <div class="p-2">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="fw-semibold">${request.request_number}</span>
          <span class="badge ${RequestsManager.getStatusBadgeClass(request.status)}">${RequestsManager.getStatusText(request.status)}</span>
        </div>
        <div class="row g-2">
          <div class="col-12">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-calendar me-2 text-primary"></i><span>${request.created_at || '-'}</span></div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-buildings me-2 text-primary"></i><span>${request.accommodation_type || '-'}</span></div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-door-open me-2 text-primary"></i><span>${request.room_type || '-'}</span></div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-bed me-2 text-primary"></i><span>${request.bed_count} ${TRANSLATION.labels.bed}</span></div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-time me-2 text-primary"></i><span>${request.period_type || '-'}</span></div>
          </div>
          <div class="col-12">
            <div class="d-flex align-items-center text-sm text-muted"><i class="bx bx-user-plus me-2 text-primary"></i><span>${request.stay_with_sibling ? (request.sibling_name || '-') : '-'}</span></div>
          </div>
        </div>
      </div>
    `;
    $('#requestDetailsContent').html(html);

    const modalEl = document.getElementById('requestDetailsModal');
    const detailsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    detailsModal.show();
  },

  /**
   * Bind events for the requests list
   */
  bindEvents() {
    $('#reservationRequests').on('click', '.js-request-info', function() {
      const index = $(this).data('index');
      const request = RequestsManager.requests?.[index];
      if (request) {
        RequestsManager.populateRequestDetailsModal(request);
      }
    });
  },

  /**
   * Initialize requests manager
   */
  init() {
    setTimeout(() => {
      this.renderReservationRequests();
    }, 700);
    this.bindEvents();
  }
};


// ===========================
// EVENTS MANAGER
// ===========================
const EventsManager = {
  /**
   * Render events modal
   */
  renderEventsModal() {
    ApiService.fetchUpcomingEvents()
      .done((response) => {
        const events = response?.data || [];
        this.populateEventsModal(events);
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  /**
   * Populate events modal with given events
   */
  populateEventsModal(events) {
    if (!events || events.length === 0) {
      const html = `
        <div class="col-12 text-center py-4">
          <div class="avatar avatar-xl bg-light rounded-circle mx-auto mb-3">
            <i class="bx bx-calendar-x text-muted" style="font-size: 2rem;"></i>
          </div>
          <h6 class="fw-semibold mb-2">${TRANSLATION.messages.noEvents}</h6>
          <p class="text-muted">${TRANSLATION.messages.noEventsDesc}</p>
        </div>
      `;
      $('#eventsModalContent').html(html);
      return;
    }

    const html = `
      <div class="row g-3">
        ${events
          .map(
            (event) => `
          <div class="col-12">
            <div class="card border-0 bg-light">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                    <i class="bx bx-calendar-event text-warning fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-2">${event.title || '-'}</h6>
                    <p class="text-muted mb-3">${event.description || ''}</p>
                    
                    <div class="row g-2 mb-3">
                      <div class="col-md-4">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-calendar me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.date}:</span>
                          <span class="ms-1">${event.date}</span>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-time me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.time}:</span>
                          <span class="ms-1">${event.time || '-'}</span>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="d-flex align-items-center text-sm">
                          <i class="bx bx-map-pin me-2 text-primary"></i>
                          <span class="fw-medium">${TRANSLATION.labels.location}:</span>
                          <span class="ms-1">${event.location || '-'}</span>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-info-circle me-1"></i>${TRANSLATION.buttons.moreInfo}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
    $('#eventsModalContent').html(html);
  },

  /**
   * Bind modal events
   */
  bindModalEvents() {
    $('#eventsModal').on('show.bs.modal', () => {
      this.renderEventsModal();
    });
  },

  /**
   * Initialize events manager
   */
  init() {
    this.bindModalEvents();
  },
};


// ===========================
// NEW REQUEST MANAGER
// ===========================
const NewRequestManager = {
  // ====================
  // INITIALIZATION
  // ====================
  
  init() {
    $('#btnNewRequest').off('click').on('click', () => this.open());
  },

  open() {
    $('#btnSubmitNewRequest').prop('disabled', true);
    this.resetForm();
    const modalEl = document.getElementById('newRequestModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
    this.loadData();
  },

  // ====================
  // DATA LOADING
  // ====================

  loadData() {
    ApiService.fetchNewRequestData()
      .done((response) => {
        this.data = response?.data || {};
        this.populateFromData();
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  populateFromData() {
    this.populateSiblings();
    this.populateTerms();
    this.bindFormEvents();
    this.handlePeriodChange();
  },

  populateSiblings() {
    const siblings = Array.isArray(this.data?.siblings_same_gender) ? this.data.siblings_same_gender : [];
    
    if (siblings.length > 0) {
      Utils.populateSelect($('#siblingId'), siblings, {
        valueField: 'id',
        textField: 'name',
        placeholder: TRANSLATION.placeholders.selectSibling,
        includePlaceholder: true
      });
      $('#siblingChoiceWrap').removeClass('d-none');
    }
  },

  populateTerms() {
    ApiService.fetchAcademicTerms()
      .done((res) => {
        const terms = res?.data || [];
        Utils.populateSelect($('#academicTermId'), terms, {
          valueField: 'id',
          textField: 'name',
          placeholder: TRANSLATION.placeholders.selectTerm,
          includePlaceholder: true
        });
      })
      .fail((xhr) => {
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  // ====================
  // EVENT BINDING
  // ====================

  bindFormEvents() {
    const self = this;
    
    // Remove existing event handlers
    $('#newRequestForm').off('change.newreq click.newreq');
    $('#btnSubmitNewRequest').off('click');
    
    // Period type change
    $('#newRequestForm').on('change.newreq', 'input[name="period_type"]', function() {
      self.handlePeriodChange();
      self.toggleSubmitState();
    });

    // Academic term change
    $('#newRequestForm').on('change.newreq', '#academicTermId', function() {
      self.handleTermChange();
      self.toggleSubmitState();
    });

    // Calendar dates change
    $('#newRequestForm').on('change.newreq', '#startDate, #endDate', function() {
      self.handleCalendarDatesChange();
      self.toggleSubmitState();
    });

    // Sibling choice change
    $('#newRequestForm').on('change.newreq', 'input[name="sibling_choice"]', function() {
      self.handleSiblingChoiceChange();
      self.toggleSubmitState();
    });

    // Sibling select change
    $('#newRequestForm').on('change.newreq', '#siblingId', function() {
      self.toggleSubmitState();
    });

    // Room type change
    $('#newRequestForm').on('change.newreq', 'input[name="room_type"]', function() {
      self.handleRoomTypeChange();
      self.toggleSubmitState();
    });

    // Bed count change
    $('#newRequestForm').on('change.newreq', 'input[name="bed_count"]', function() {
      self.toggleSubmitState();
    });

    // Last term choice change
    $('#newRequestForm').on('change.newreq', 'input[name="last_term_choice"]', function() {
      self.toggleSubmitState();
    });

    // Submit button
    $('#btnSubmitNewRequest').on('click', () => this.submit());
  },

  // ====================
  // FORM MANAGEMENT
  // ====================

  resetForm() {
    // Clear input values
    $('#siblingId').val('');
    $('#academicTermId').val('');
    $('#startDate').val('');
    $('#endDate').val('');
    
    this.hideAllSections();
  },

  hideAllSections() {
    const sections = [
      '#academicBlock',
      '#calendarBlock', 
      '#siblingChoiceWrap',
      '#siblingSelectWrap',
      '#roomPrefWrap',
      '#doubleBedWrap',
      '#lastTermWrap'
    ];
    
    sections.forEach(section => $(section).addClass('d-none'));
  },

  // ====================
  // EVENT HANDLERS
  // ====================

  handlePeriodChange() {
    const period = $('input[name="period_type"]:checked').val();
    
    this.hideAllSections();
    
    if (period === 'academic') {
      $('#academicBlock').removeClass('d-none');
    } else if (period === 'calendar') {
      $('#calendarBlock').removeClass('d-none');
    }
  },

  handleTermChange() {
    const period = $('input[name="period_type"]:checked').val();
    const termId = $('#academicTermId').val();
    
    this.hideSubsequentSections([
      '#siblingChoiceWrap',
      '#siblingSelectWrap', 
      '#roomPrefWrap',
      '#doubleBedWrap',
      '#lastTermWrap'
    ]);
    
    if (period === 'academic' && termId) {
      this.showSiblingChoiceOrRoomPreference();
    }
  },

  handleCalendarDatesChange() {
    const period = $('input[name="period_type"]:checked').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    this.hideSubsequentSections([
      '#siblingChoiceWrap',
      '#siblingSelectWrap',
      '#roomPrefWrap', 
      '#doubleBedWrap',
      '#lastTermWrap'
    ]);
    
    if (period === 'calendar' && startDate && endDate) {
      this.showSiblingChoiceOrRoomPreference();
    }
  },

  handleSiblingChoiceChange() {
    const siblingChoice = $('input[name="sibling_choice"]:checked').val();
    
    this.hideSubsequentSections([
      '#siblingSelectWrap',
      '#roomPrefWrap',
      '#doubleBedWrap', 
      '#lastTermWrap'
    ]);
    
    if (siblingChoice === 'with') {
      this.setupSiblingStay();
    } else if (siblingChoice === 'alone') {
      this.setupAloneStay();
    }
  },

  handleRoomTypeChange() {
    const siblingChoice = $('input[name="sibling_choice"]:checked').val();
    
    if (siblingChoice === 'with') {
      this.handleRoomTypeForSibling();
    } else {
      this.handleRoomTypeForAlone();
    }
  },

  // ====================
  // SECTION MANAGEMENT
  // ====================

  hideSubsequentSections(sections) {
    sections.forEach(section => $(section).addClass('d-none'));
  },

  showSiblingChoiceOrRoomPreference() {
    const siblings = Array.isArray(this.data?.siblings_same_gender) ? this.data.siblings_same_gender : [];
    
    if (siblings.length > 0) {
      console.log("Siblings available for selection");
      $('#siblingChoiceWrap').removeClass('d-none');
    } else {
      console.log("No siblings available, proceeding to room preferences");
      $('#roomPrefWrap').removeClass('d-none');
      this.handleRoomTypeForAlone();
    }
  },

  setupSiblingStay() {
    // Show sibling selection
    $('#siblingSelectWrap').removeClass('d-none');
    
    // Auto-configure for sibling stay
    $('#roomDouble').prop('checked', true);
    $('#singleBed').prop('checked', true);
    
    // Show bed selection with restrictions
    $('#doubleBedWrap').removeClass('d-none');
    $('#bothBeds').prop('disabled', true);
    $('#singleBed').prop('disabled', false);
  },

  setupAloneStay() {
    // Show room preferences
    $('#roomPrefWrap').removeClass('d-none');
    
    // Reset to single room default
    $('#roomSingle').prop('checked', true);
    
    // Enable all bed options
    $('#bothBeds').prop('disabled', false);
    $('#singleBed').prop('disabled', false);
    
    this.handleRoomTypeForAlone();
  },

  handleRoomTypeForSibling() {
    const roomType = $('input[name="room_type"]:checked').val();
    
    $('#lastTermWrap').addClass('d-none');
    
    if (roomType === 'double') {
      $('#doubleBedWrap').removeClass('d-none');
      $('#singleBed').prop('checked', true);
      $('#bothBeds').prop('disabled', true);
    }else if(roomType === 'single'){
      
    }
  },

  handleRoomTypeForAlone() {
    const roomType = $('input[name="room_type"]:checked').val();
    
    this.hideSubsequentSections(['#doubleBedWrap', '#lastTermWrap']);
    
    if (roomType === 'single') {
      this.showLastTermOption();
    } else if (roomType === 'double') {
      this.showBedCountSelection();
    }
  },

  showLastTermOption() {
    const hasLastTerm = !!(this.data?.last_term?.has_last);
    if (hasLastTerm) {
      $('#lastTermWrap').removeClass('d-none');
      this.handleLastTermChoiceChange();
      $('#lastTermWrap input[name="last_term_choice"]').off('change.lastterm').on('change.lastterm', () => {
        this.handleLastTermChoiceChange();
      });
    }
  },

  handleLastTermChoiceChange() {
    const lastTermChoice = $('input[name="last_term_choice"]:checked').val();
    const location = this.data?.last_term?.location;
    $('#lastTermLocationDetails').remove();
    if (lastTermChoice === 'old' && Array.isArray(location) && location.length > 0) {
      const html = `<div id="lastTermLocationDetails" class="alert alert-info mt-2">
        <strong>${TRANSLATION.labels.lastTermOption}:</strong> ${location.map(l => `<span class='badge bg-primary me-1'>${l}</span>`).join(' ')}
      </div>`;
      $('#lastTermWrap').append(html);
    }
  },

  showBedCountSelection() {
    $('#doubleBedWrap').removeClass('d-none');
    $('#bothBeds').prop('disabled', false);
    $('#singleBed').prop('disabled', false);
  },

  // ====================
  // FORM VALIDATION
  // ====================

  isValid() {
    return this.validatePeriod() && 
           this.validateSiblingSelection() && 
           this.validateRoomPreferences();
  },

  validatePeriod() {
    const period = $('input[name="period_type"]:checked').val();
    if (!period) return false;
    
    if (period === 'academic') {
      return !!$('#academicTermId').val();
    } else if (period === 'calendar') {
      return this.validateDateRange();
    }
    
    return true;
  },

  validateDateRange() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    if (!startDate || !endDate) return false;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    return start < end;
  },

  validateSiblingSelection() {
    if ($('#siblingChoiceWrap').hasClass('d-none')) return true;
    
    const siblingChoice = $('input[name="sibling_choice"]:checked').val();
    
    if (siblingChoice === 'with') {
      return !!$('#siblingId').val();
    }
    
    return true;
  },

  validateRoomPreferences() {
    if ($('#roomPrefWrap').hasClass('d-none')) return true;
    
    const roomType = $('input[name="room_type"]:checked').val();
    if (!roomType) return false;
    
    // Validate bed count for double rooms
    if (roomType === 'double' && !$('input[name="bed_count"]:checked').val()) {
      return false;
    }
    
    // Validate last term choice if visible
    if (!$('#lastTermWrap').hasClass('d-none') && !$('input[name="last_term_choice"]:checked').val()) {
      return false;
    }
    
    return true;
  },

  toggleSubmitState() {
    $('#btnSubmitNewRequest').prop('disabled', !this.isValid());
  },

  // ====================
  // FORM SUBMISSION
  // ====================

  collectPayload() {
    const payload = this.getBasicPayload();
    
    this.addPeriodData(payload);
    this.addSiblingData(payload);
    this.addRoomData(payload);
    this.addLastTermData(payload);
    
    return payload;
  },

  getBasicPayload() {
    return {
      period_type: $('input[name="period_type"]:checked').val(),
      accommodation_type: 'room',
      room_type: $('input[name="room_type"]:checked').val(),
      bed_count: 1,
      stay_with_sibling: 0,
      last_term_choice: null
    };
  },

  addPeriodData(payload) {
    if (payload.period_type === 'academic') {
      const termId = $('#academicTermId').val();
      if (termId) payload.academic_term_id = termId;
    } else if (payload.period_type === 'calendar') {
      const startDate = $('#startDate').val();
      const endDate = $('#endDate').val();
      if (startDate) payload.start_date = startDate;
      if (endDate) payload.end_date = endDate;
    }
  },

  addSiblingData(payload) {
    const siblingChoice = $('input[name="sibling_choice"]:checked').val();
    
    if (siblingChoice === 'with') {
      payload.stay_with_sibling = 1;
      const siblingId = $('#siblingId').val();
      if (siblingId) payload.sibling_id = siblingId;
    }
  },

  addRoomData(payload) {
    const roomType = payload.room_type;
    
    if (roomType === 'double') {
      const bedCount = parseInt($('input[name="bed_count"]:checked').val() || '1', 10);
      payload.bed_count = payload.stay_with_sibling ? 1 : bedCount;
    }
  },

  addLastTermData(payload) {
    if (!payload.stay_with_sibling && 
        payload.room_type === 'single' && 
        !$('#lastTermWrap').hasClass('d-none')) {
      
      const lastTermChoice = $('input[name="last_term_choice"]:checked').val();
      if (lastTermChoice) payload.last_term_choice = lastTermChoice;
    }
  },

  submit() {
    if (!this.isValid()) {
      Utils.showError(TRANSLATION.messages.formIncomplete);
      return;
    }
    
    const payload = this.collectPayload();
    this.setSubmitLoading(true);
    
    ApiService.createReservationRequest(payload)
      .done((response) => {
        Utils.showSuccess(response?.message);
        this.closeModal();
        this.refreshRequestsList();
      })
      .fail((xhr) => {
        this.setSubmitLoading(false);
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },

  setSubmitLoading(loading) {
    $('#btnSubmitNewRequest')
      .prop('disabled', loading)
      .toggleClass('disabled', loading);
  },

  closeModal() {
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('newRequestModal'));
    if (modalInstance) modalInstance.hide();
  },

  refreshRequestsList() {
    if (typeof RequestsManager !== 'undefined' && RequestsManager.renderReservationRequests) {
      RequestsManager.renderReservationRequests();
    }
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
const ResidentHomeApp = {
  /**
   * Initialize all managers
   */
  init() {
    HousingManager.init();
    NeighborsManager.init();
    RequestsManager.init();
    EventsManager.init();
    NewRequestManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  ResidentHomeApp.init();
});
</script>
@endpush