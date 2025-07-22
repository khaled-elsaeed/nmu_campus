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
        <button class="btn btn-primary mx-2" id="addStudentBtn">
            <i class="bx bx-plus me-1"></i> Add Student
        </button>
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
    {{-- Add/Edit Student Modal --}}
    <x-ui.modal 
        id="studentModal"
        title="Add/Edit Student"
        :scrollable="true"
        class="student-modal"
    >
        <x-slot name="slot">
            <form id="studentForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="student_academic_id" class="form-label">Academic ID</label>
                        <input type="text" id="student_academic_id" name="academic_id" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_national_id" class="form-label">National ID</label>
                        <input type="text" id="student_national_id" name="national_id" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_name_en" class="form-label">Name (EN)</label>
                        <input type="text" id="student_name_en" name="name_en" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_name_ar" class="form-label">Name (AR)</label>
                        <input type="text" id="student_name_ar" name="name_ar" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_academic_email" class="form-label">Academic Email</label>
                        <input type="email" id="student_academic_email" name="academic_email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_phone" class="form-label">Phone</label>
                        <input type="text" id="student_phone" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" id="student_date_of_birth" name="date_of_birth" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_gender" class="form-label">Gender</label>
                        <select id="student_gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_level" class="form-label">Level</label>
                        <select id="student_level" name="level" class="form-control" required>
                            <option value="">Select Year</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_faculty_id" class="form-label">Faculty</label>
                        <select id="student_faculty_id" name="faculty_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_program_id" class="form-label">Program</label>
                        <select id="student_program_id" name="program_id" class="form-control"></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_governorate_id" class="form-label">Governorate</label>
                        <select id="student_governorate_id" name="governorate_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_city_id" class="form-label">City</label>
                        <select id="student_city_id" name="city_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="student_address" class="form-label">Address</label>
                        <textarea id="student_address" name="address" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="student_is_profile_complete" class="form-label">Profile Complete</label>
                        <select id="student_is_profile_complete" name="is_profile_complete" class="form-control" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="studentForm">Save</button>
        </x-slot>
    </x-ui.modal>

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
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - SelectManager: Handles dropdown population
 * - StudentManager: Handles CRUD and actions for students
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
    store: '{{ route('resident.students.store') }}',
    update: '{{ route('resident.students.update', ':id') }}',
    destroy: '{{ route('resident.students.destroy', ':id') }}',
    datatable: '{{ route('resident.students.datatable') }}'
  },
  governorates: {
    all: '{{ route('governorates.all') }}'
  },
  programs: {
    all: '{{ route('academic.programs.all', ':id') }}'
  },
  cities: {
    all: '{{ route('cities.all', ':id') }}'
  },
  faculties: {
    all: '{{ route('academic.faculties.all') }}'
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
var Utils = {
  /**
   * Show an error alert
   * @param {string} message
   */
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  /**
   * Show a success toast message
   * @param {string} message
   */
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  /**
   * Replace :id in a route string
   * @param {string} route
   * @param {string|number} id
   * @returns {string}
   */
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
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
   * Save (create or update) a student
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveStudent: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.students.update, id) : ROUTES.students.store;
    var method = id ? 'PUT' : 'POST';
    return this.request({ url: url, method: method, data: data });
  },
  /**
   * Delete a student by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteStudent: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.students.destroy, id), method: 'DELETE' });
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
   * Populate governorates in modal
   */
  populateModalGovernorates: function() {
    var $select = $('#student_governorate_id');
    $select.empty().append('<option value="">Select Governorate</option>');
    ApiService.fetchGovernorates()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(gov) {
            $select.append('<option value="' + gov.id + '">' + gov.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading governorates</option>');
      });
  },
  /**
   * Populate cities in modal
   */
  populateModalCities: function(governorateId, selectedCityId) {
    var $select = $('#student_city_id');
    $select.empty().append('<option value="">Loading cities...</option>');
    if (!governorateId) {
      $select.empty().append('<option value="">Select Governorate first</option>');
      return;
    }
    ApiService.fetchCities(governorateId)
      .done(function(response) {
        $select.empty().append('<option value="">Select City</option>');
        if (response.success) {
          response.data.forEach(function(city) {
            var selected = selectedCityId && city.id == selectedCityId ? 'selected' : '';
            $select.append('<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading cities</option>');
      });
  },
  /**
   * Populate faculties in modal
   */
  populateModalFaculties: function() {
    var $select = $('#student_faculty_id');
    $select.empty().append('<option value="">Select Faculty</option>');
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(faculty) {
            $select.append('<option value="' + faculty.id + '">' + faculty.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },
  /**
   * Populate programs in modal
   */
  populateModalPrograms: function(facultyId, selectedProgramId) {
    var $select = $('#student_program_id');
    $select.empty().append('<option value="">Loading programs...</option>');
    if (!facultyId) {
      $select.empty().append('<option value="">Select Faculty first</option>');
      return;
    }
    ApiService.fetchPrograms(facultyId)
      .done(function(response) {
        $select.empty().append('<option value="">Select Program</option>');
        if (response.success) {
          response.data.forEach(function(program) {
            var selected = selectedProgramId && program.id == selectedProgramId ? 'selected' : '';
            $select.append('<option value="' + program.id + '" ' + selected + '>' + program.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading programs</option>');
      });
  },
  /**
   * Populate governorates in search
   */
  populateSearchGovernorates: function() {
    var $select = $('#search_governorate_id');
    $select.empty().append('<option value="">All Governorates</option>');
    ApiService.fetchGovernorates()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(gov) {
            $select.append('<option value="' + gov.id + '">' + gov.name + '</option>');
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading governorates</option>');
      });
  },
  /**
   * Populate faculties in search
   */
  populateSearchFaculties: function() {
    var $select = $('#faculty_id');
    $select.empty().append('<option value="">All Faculties</option>');
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(faculty) {
            $select.append('<option value="' + faculty.id + '">' + faculty.name + '</option>');
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },
  /**
   * Setup dynamic selects
   */
  setupDynamicSelects: function() {
    $('#student_governorate_id').on('change', function() {
      var governorateId = $(this).val();
      SelectManager.populateModalCities(governorateId);
    });
    $('#student_faculty_id').on('change', function() {
      var facultyId = $(this).val();
      SelectManager.populateModalPrograms(facultyId);
    });
  },
  /**
   * Initialize select manager
   */
  init: function() {
    this.populateModalGovernorates();
    this.populateModalFaculties();
    this.populateSearchGovernorates();
    this.populateSearchFaculties();
    this.setupDynamicSelects();
  }
};

// ===========================
// STUDENT MANAGER
// ===========================
var StudentManager = {
  currentStudentId: null,
  /**
   * Bind add student button
   */
  handleAdd: function() {
    var self = this;
    $(document).on('click', '#addStudentBtn', function() {
      self.currentStudentId = null;
      $('#studentModal .modal-title').text('Add Student');
      $('#studentForm')[0].reset();
      $('#student_city_id').empty().append('<option value="">Select Governorate first</option>');
      $('#student_program_id').empty().append('<option value="">Select Faculty first</option>');
      $('#studentModal').modal('show');
    });
  },
  /**
   * Bind edit student button
   */
  handleEdit: function() {
    var self = this;
    $(document).on('click', '.editStudentBtn', function() {
      var studentId = $(this).data('id');
      self.currentStudentId = studentId;
      $('#studentModal .modal-title').text('Edit Student');
      ApiService.fetchStudent(studentId)
        .done(function(response) {
          if (response.success) {
            var student = response.data;
            $('#student_academic_id').val(student.academic_id);
            $('#student_national_id').val(student.national_id);
            $('#student_name_en').val(student.name_en);
            $('#student_name_ar').val(student.name_ar);
            $('#student_academic_email').val(student.academic_email);
            $('#student_phone').val(student.phone);
            var dob = student.date_of_birth ? student.date_of_birth.substring(0, 10) : '';
            $('#student_date_of_birth').val(dob);
            $('#student_gender').val(student.gender);
            $('#student_level').val(student.level);
            $('#student_address').val(student.address);
            $('#student_is_profile_complete').val(student.is_profile_complete ? '1' : '0');
            $('#student_governorate_id').val(student.governorate_id);
            if (student.governorate_id) {
              SelectManager.populateModalCities(student.governorate_id, student.city_id);
            }
            $('#student_faculty_id').val(student.faculty_id);
            if (student.faculty_id) {
              SelectManager.populateModalPrograms(student.faculty_id, student.program_id);
              
            }
            $('#studentModal').modal('show');
          }
        })
        .fail(function() {
          $('#studentModal').modal('hide');
          Utils.showError('Failed to load student data');
        });
    });
  },
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
            $('#view-student-level').text(student.level);
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
   * Bind delete student button
   */
  handleDelete: function() {
    $(document).on('click', '.deleteStudentBtn', function() {
      var studentId = $(this).data('id');
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteStudent(studentId)
            .done(function() {
              $('#students-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Student has been deleted.');
            })
            .fail(function(xhr) {
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete student.';
              Utils.showError(message);
            });
        }
      });
    });
  },
  /**
   * Bind form submit
   */
  handleFormSubmit: function() {
    var self = this;
    $('#studentForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      ApiService.saveStudent(formData, self.currentStudentId)
        .done(function() {
          $('#studentModal').modal('hide');
          $('#students-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Student has been saved successfully.');
        })
        .fail(function(xhr) {
          $('#studentModal').modal('hide');
          var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
          Utils.showError(message);
        });
    });
  },
  /**
   * Initialize all student manager handlers
   */
  init: function() {
    this.handleAdd();
    this.handleEdit();
    this.handleView();
    this.handleDelete();
    this.handleFormSubmit();
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
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['students', 'students-male', 'students-female'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
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
