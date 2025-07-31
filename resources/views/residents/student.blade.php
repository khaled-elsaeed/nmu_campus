@extends('layouts.home')

@section('title', 'Student Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-students" label="Total Students" id="students" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" label="Male Students" id="students-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" label="Female Students" id="students-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Students"
        description="Manage students and their information."
        icon="bx bx-user"
    >
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#studentSearchCollapse" aria-expanded="false" aria-controls="studentSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
<x-ui.advanced-search 
    title="Advanced Search" 
    formId="advancedStudentSearch" 
    collapseId="studentSearchCollapse"
    :collapsed="false"
>
    <div class="col-md-4">
        <label for="search_id" class="form-label">Academic ID or National ID:</label>
        <input type="text" class="form-control" id="search_id" placeholder="Enter Academic ID or National ID">
    </div>
    <div class="col-md-4">
        <label for="search_name" class="form-label">Name:</label>
        <input type="text" class="form-control" id="search_name">
    </div>
    <div class="col-md-4">
        <label for="search_gender" class="form-label">Gender:</label>
        <select class="form-control" id="search_gender">
            <option value="">All Genders</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_governorate_id" class="form-label">Governorate:</label>
        <select class="form-control" id="search_governorate_id">
            <option value="">All Governorates</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="faculty_id" class="form-label">Faculty:</label>
        <select class="form-control" id="faculty_id">
            <option value="">All Faculties</option>
        </select>
    </div>
    <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearStudentFiltersBtn" type="button">
        <i class="bx bx-x"></i> Clear Filters
    </button>
</x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Academic ID', 'Name', 'Phone', 'Gender', 'Level', 'Faculty', 'Actions']"
        :columns="[
            ['data' => 'academic_id', 'name' => 'academic_id'],
            ['data' => 'name_en', 'name' => 'name_en'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'level', 'name' => 'level'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.students.datatable')"
        :table-id="'students-table'"
        :filter-fields="['search_id','search_name','search_gender','faculty_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Student Modal --}}
    <x-ui.modal 
        id="viewStudentModal"
        title="Student Details"
        :scrollable="true"
        class="view-student-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Academic ID:</label>
                    <p id="view-student-academic-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Name:</label>
                    <p id="view-student-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Academic Email:</label>
                    <p id="view-student-academic-email" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Phone:</label>
                    <p id="view-student-phone" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Gender:</label>
                    <p id="view-student-gender" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Level:</label>
                    <p id="view-level-year" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Faculty:</label>
                    <p id="view-student-faculty" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Program:</label>
                    <p id="view-student-program" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Profile Complete:</label>
                    <p id="view-student-profile-complete" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-student-created" class="mb-0"></p>
                </div>
            </div>
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
 * Student Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions (from utils.js)
 * - ApiService: Handles all AJAX requests
 * - SelectManager: Handles dropdown population
 * - StudentManager: Handles actions for students (view only)
 * - SearchManager: Handles advanced search
 * - StatsManager: Handles statistics cards
 * - StudentApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  students: {
    show: '{{ route('resident.students.show', ':id') }}',
    datatable: '{{ route('resident.students.datatable') }}'
  },
  governorates: {
    all: '{{ route('governorates.all') }}'
  },
  programs: {
    all: '{{ route('academic.programs.all', ':id') }}'
  },
  cities: {
    all: '{{ route('cities.all', ':governorateId') }}'
  },
  faculties: {
    all: '{{ route('academic.faculties.all') }}'
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
  request: function(options) { return $.ajax(options); },
  /**
   * Fetch a student by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchStudent: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.students.show, id), method: 'GET' });
  },
  /**
   * Fetch all governorates
   * @returns {jqXHR}
   */
  fetchGovernorates: function() {
    return this.request({ url: ROUTES.governorates.all, method: 'GET' });
  },
  /**
   * Fetch all cities for a governorate
   * @param {string|number} governorateId
   * @returns {jqXHR}
   */
  fetchCities: function(governorateId) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.cities.all, governorateId), method: 'GET' });
  },
  /**
   * Fetch all faculties
   * @returns {jqXHR}
   */
  fetchFaculties: function() {
    return this.request({ url: ROUTES.faculties.all, method: 'GET' });
  },
  /**
   * Fetch all programs for a faculty
   * @param {string|number} facultyId
   * @returns {jqXHR}
   */
  fetchPrograms: function(facultyId) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.programs.all, facultyId), method: 'GET', data: { faculty_id: facultyId } });
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  /**
   * Populate governorates in search
   */
  populateSearchGovernorates: function() {
    var $select = $('#search_governorate_id');
    $select.empty();
    Utils.populateSelect($select, [], { placeholder: 'All Governorates' });
    ApiService.fetchGovernorates()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { placeholder: 'All Governorates' });
        }
      })
      .fail(function() {
        Utils.populateSelect($select, [], { placeholder: 'Error loading governorates' });
      });
  },
  /**
   * Populate faculties in search
   */
  populateSearchFaculties: function() {
    var $select = $('#faculty_id');
    $select.empty();
    Utils.populateSelect($select, [], { placeholder: 'All Faculties' });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { placeholder: 'All Faculties' });
        }
      })
      .fail(function() {
        Utils.populateSelect($select, [], { placeholder: 'Error loading faculties' });
      });
  },
  /**
   * Initialize select manager
   */
  init: function() {
    this.populateSearchGovernorates();
    this.populateSearchFaculties();
  }
};

// ===========================
// STUDENT MANAGER
// ===========================
var StudentManager = {
  /**
   * Bind view student button
   */
  handleView: function() {
    $(document).on('click', '.viewStudentBtn', function() {
      var studentId = $(this).data('id');
      ApiService.fetchStudent(studentId)
        .done(function(response) {
          if (response.success) {
            var student = response.data;
            $('#view-student-academic-id').text(student.academic_id);
            $('#view-student-name').text(student.name);
            $('#view-student-academic-email').text(student.academic_email);
            $('#view-student-phone').text(student.phone);
            $('#view-student-gender').text(student.gender);
            $('#view-level-year').text(student.level);
            $('#view-student-faculty').text(student.faculty ? student.faculty : 'N/A');
            $('#view-student-program').text(student.program ? student.program : 'N/A');
            $('#view-student-profile-complete').text(student.is_profile_complete ? 'Yes' : 'No');
            $('#view-student-created').text(new Date(student.created_at).toLocaleString());
            $('#viewStudentModal').modal('show');
          }
        })
        .fail(function() {
          $('#viewStudentModal').modal('hide');
          Utils.showError('Failed to load student data');
        });
    });
  },
  /**
   * Initialize all student manager handlers
   */
  init: function() {
    this.handleView();
  }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
  /**
   * Initialize advanced search
   */
  init: function() {
    this.bindEvents();
  },
  /**
   * Bind search and clear events
   */
  bindEvents: function() {
    $('#search_id, #search_name, #search_gender, #search_governorate_id, #faculty_id').on('keyup change', function() {
      $('#students-table').DataTable().ajax.reload();
    });
    $('#clearStudentFiltersBtn').on('click', function() {
      $('#search_id, #search_name, #search_gender, #search_governorate_id, #faculty_id').val('');
      $('#students-table').DataTable().ajax.reload();
    });
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
    $.ajax({ url: '{{ route('resident.students.stats') }}', method: 'GET' })
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
      this.updateStatElement('students', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('students-male', stats.male.count, stats.male.lastUpdateTime);
      this.updateStatElement('students-female', stats.female.count, stats.female.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load student statistics');
  },
  /**
   * Update a single stat card
   * @param {string} elementId
   * @param {string|number} value
   * @param {string} lastUpdateTime
   */
  updateStatElement: function(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  /**
   * Set all stat cards to N/A
   */
  setAllStatsToNA: function() {
    ['students', 'students-male', 'students-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
    /**
   * Toggle loading state for a stat card
   * @param {string} id
   * @param {boolean} show
   */
  toggleLoadingState: function(id, show) {
    var loader = document.getElementById(id + '-loader');
    var valueEl = document.getElementById(id + '-value');
    var lastUpdatedLoader = document.getElementById(id + '-last-updated-loader');
    var lastUpdatedEl = document.getElementById(id + '-last-updated');
    if (loader) loader.classList.toggle('d-none', !show);
    if (valueEl) valueEl.classList.toggle('d-none', show);
    if (lastUpdatedLoader) lastUpdatedLoader.classList.toggle('d-none', !show);
    if (lastUpdatedEl) lastUpdatedEl.classList.toggle('d-none', show);
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    var self = this;
    ['students', 'students-male', 'students-female'].forEach(function(elementId) {
      self.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var StudentApp = {
  /**
   * Initialize all managers
   */
  init: function() {
    StudentManager.init();
    SearchManager.init();
    SelectManager.init();
    StatsManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  StudentApp.init();
});

</script>
@endpush
