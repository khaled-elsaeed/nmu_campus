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
        <label for="search_academic_id" class="form-label">Academic ID:</label>
        <input type="text" class="form-control" id="search_academic_id">
    </div>
    <div class="col-md-4">
        <label for="search_name_en" class="form-label">Name (EN):</label>
        <input type="text" class="form-control" id="search_name_en">
    </div>
    <div class="col-md-4">
        <label for="search_gender" class="form-label">Gender:</label>
        <select class="form-control" id="search_gender">
            <option value="">All</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_active" class="form-label">Active Status:</label>
        <select class="form-control" id="search_active">
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_governorate_id" class="form-label">Governorate:</label>
        <select class="form-control" id="search_governorate_id">
            <option value="">All Governorates</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_faculty_id" class="form-label">Faculty:</label>
        <select class="form-control" id="search_faculty_id">
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
        :headers="['Academic ID', 'Name', 'Phone', 'Gender', 'Academic Year', 'Faculty', 'Actions']"
        :columns="[
            ['data' => 'academic_id', 'name' => 'academic_id'],
            ['data' => 'name_en', 'name' => 'name_en'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'academic_year', 'name' => 'academic_year'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.students.datatable')"
        :table-id="'students-table'"
        :filter-fields="['search_academic_id','search_name_en','search_gender','search_active']"
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
                        <label for="student_academic_year" class="form-label">Academic Year</label>
                        <select id="student_academic_year" name="academic_year" class="form-control" required>
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
                    <div class="col-md-6 mb-3">
                        <label for="student_active" class="form-label">Active</label>
                        <select id="student_active" name="active" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
                    <label class="form-label fw-bold">Name (EN):</label>
                    <p id="view-student-name-en" class="mb-0"></p>
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
                    <label class="form-label fw-bold">Academic Year:</label>
                    <p id="view-student-academic-year" class="mb-0"></p>
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
                    <label class="form-label fw-bold">Active:</label>
                    <p id="view-student-is-active" class="mb-0"></p>
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

// ===========================
// CONSTANTS AND CONFIGURATION
// ===========================
const ROUTES = {
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
const Utils = {
  showError(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  showSuccess(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  replaceRouteId(route, id) {
    return route.replace(':id', id);
  },
  toggleLoadingState(id, show) {
    const loader = document.getElementById(id + '-loader');
    const valueEl = document.getElementById(id + '-value');
    const lastUpdatedLoader = document.getElementById(id + '-last-updated-loader');
    const lastUpdatedEl = document.getElementById(id + '-last-updated');

    if (loader) loader.classList.toggle('d-none', !show);
    if (valueEl) valueEl.classList.toggle('d-none', show);
    if (lastUpdatedLoader) lastUpdatedLoader.classList.toggle('d-none', !show);
    if (lastUpdatedEl) lastUpdatedEl.classList.toggle('d-none', show);
  }
};

// ===========================
// API SERVICE LAYER
// ===========================
const ApiService = {
  request(options) { return $.ajax(options); },
  fetchStudent(id) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.students.show, id),
      method: 'GET'
    });
  },
  saveStudent(data, id = null) {
    const url = id ? Utils.replaceRouteId(ROUTES.students.update, id) : ROUTES.students.store;
    const method = id ? 'PUT' : 'POST';
    return ApiService.request({ url, method, data });
  },
  deleteStudent(id) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.students.destroy, id),
      method: 'DELETE'
    });
  },
  fetchGovernorates() {
    return ApiService.request({
      url: ROUTES.governorates.all,
      method: 'GET'
    });
  },
  fetchCities(governorateId) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.cities.all, governorateId),
      method: 'GET'
    });
  },
  fetchFaculties() {
    return ApiService.request({
      url: ROUTES.faculties.all,
      method: 'GET'
    });
  },
  fetchPrograms(facultyId) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.programs.all, facultyId),
      method: 'GET'
    });
  }
};

// ===========================
// SELECT MANAGEMENT
// ===========================
const SelectManager = {
  // Modal form selects
  populateModalGovernorates() {
    const $select = $('#student_governorate_id');
    $select.empty().append('<option value="">Select Governorate</option>');
    
    ApiService.fetchGovernorates()
      .done((response) => {
        if (response.success) {
          response.data.forEach(gov => {
            $select.append(`<option value="${gov.id}">${gov.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading governorates</option>');
      });
  },

  populateModalCities(governorateId, selectedCityId = null) {
    const $select = $('#student_city_id');
    $select.empty().append('<option value="">Loading cities...</option>');
    
    if (!governorateId) {
      $select.empty().append('<option value="">Select Governorate first</option>');
      return;
    }

    ApiService.fetchCities(governorateId)
      .done((response) => {
        $select.empty().append('<option value="">Select City</option>');
        if (response.success) {
          response.data.forEach(city => {
            const selected = selectedCityId && city.id == selectedCityId ? 'selected' : '';
            $select.append(`<option value="${city.id}" ${selected}>${city.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading cities</option>');
      });
  },

  populateModalFaculties() {
    const $select = $('#student_faculty_id');
    $select.empty().append('<option value="">Select Faculty</option>');
    
    ApiService.fetchFaculties()
      .done((response) => {
        if (response.success) {
          response.data.forEach(faculty => {
            $select.append(`<option value="${faculty.id}">${faculty.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },

  populateModalPrograms(facultyId, selectedProgramId = null) {
    const $select = $('#student_program_id');
    $select.empty().append('<option value="">Loading programs...</option>');
    
    if (!facultyId) {
      $select.empty().append('<option value="">Select Faculty first</option>');
      return;
    }

    ApiService.fetchPrograms(facultyId)
      .done((response) => {
        $select.empty().append('<option value="">Select Program</option>');
        if (response.success) {
          response.data.forEach(program => {
            const selected = selectedProgramId && program.id == selectedProgramId ? 'selected' : '';
            $select.append(`<option value="${program.id}" ${selected}>${program.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#studentModal').modal('hide');
        $select.empty().append('<option value="">Error loading programs</option>');
      });
  },

  // Search filter selects
  populateSearchGovernorates() {
    const $select = $('#search_governorate_id');
    $select.empty().append('<option value="">All Governorates</option>');
    
    ApiService.fetchGovernorates()
      .done((response) => {
        if (response.success) {
          response.data.forEach(gov => {
            $select.append(`<option value="${gov.id}">${gov.name}</option>`);
          });
        }
      })
      .fail(() => {
        $select.empty().append('<option value="">Error loading governorates</option>');
      });
  },

  populateSearchFaculties() {
    const $select = $('#search_faculty_id');
    $select.empty().append('<option value="">All Faculties</option>');
    
    ApiService.fetchFaculties()
      .done((response) => {
        if (response.success) {
          response.data.forEach(faculty => {
            $select.append(`<option value="${faculty.id}">${faculty.name}</option>`);
          });
        }
      })
      .fail(() => {
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },

  setupDynamicSelects() {
    // Governorate change -> update cities
    $('#student_governorate_id').on('change', function() {
      const governorateId = $(this).val();
      SelectManager.populateModalCities(governorateId);
    });

    // Faculty change -> update programs
    $('#student_faculty_id').on('change', function() {
      const facultyId = $(this).val();
      SelectManager.populateModalPrograms(facultyId);
    });
  },

  init() {
    // Initialize modal selects
    this.populateModalGovernorates();
    this.populateModalFaculties();
    
    // Initialize search selects
    this.populateSearchGovernorates();
    this.populateSearchFaculties();
    
    // Setup dynamic behavior
    this.setupDynamicSelects();
  }
};

// ===========================
// STUDENT CRUD & MODALS
// ===========================
let currentStudentId = null;

const StudentManager = {
  handleAddStudent() {
    $(document).on('click', '#addStudentBtn', function() {
      currentStudentId = null;
      $('#studentModal .modal-title').text('Add Student');
      $('#studentForm')[0].reset();
      
      // Reset dependent selects
      $('#student_city_id').empty().append('<option value="">Select Governorate first</option>');
      $('#student_program_id').empty().append('<option value="">Select Faculty first</option>');
      
      $('#studentModal').modal('show');
    });
  },

  handleEditStudent() {
    $(document).on('click', '.editStudentBtn', function() {
      const studentId = $(this).data('id');
      currentStudentId = studentId;
      $('#studentModal .modal-title').text('Edit Student');
      
      ApiService.fetchStudent(studentId)
        .done((response) => {
          if (response.success) {
            const student = response.data;
            
            // Fill basic fields
            $('#student_academic_id').val(student.academic_id);
            $('#student_national_id').val(student.national_id);
            $('#student_name_en').val(student.name_en);
            $('#student_name_ar').val(student.name_ar);
            $('#student_academic_email').val(student.academic_email);
            $('#student_phone').val(student.phone);
            // If student.date_of_birth is not in YYYY-MM-DD, convert it
            let dob = student.date_of_birth ? student.date_of_birth.substring(0, 10) : '';
            $('#student_date_of_birth').val(dob);
            $('#student_gender').val(student.gender);
            $('#student_academic_year').val(student.academic_year);
            $('#student_address').val(student.address);
            $('#student_is_profile_complete').val(student.is_profile_complete ? '1' : '0');
            $('#student_active').val(student.active ? '1' : '0');
            
            // Fill governorate and trigger city population
            $('#student_governorate_id').val(student.governorate_id);
            if (student.governorate_id) {
              SelectManager.populateModalCities(student.governorate_id, student.city_id);
            }
            
            // Fill faculty and trigger program population
            $('#student_faculty_id').val(student.faculty_id);
            if (student.faculty_id) {
              SelectManager.populateModalPrograms(student.faculty_id, student.program_id);
            }
            
            $('#studentModal').modal('show');
          }
        })
        .fail(() => {
          $('#studentModal').modal('hide');
          Utils.showError('Failed to load student data');
        });
    });
  },

  handleViewStudent() {
    $(document).on('click', '.viewStudentBtn', function() {
      const studentId = $(this).data('id');
      ApiService.fetchStudent(studentId)
        .done((response) => {
          if (response.success) {
            const student = response.data;
            $('#view-student-academic-id').text(student.academic_id);
            $('#view-student-name-en').text(student.name_en);
            $('#view-student-academic-email').text(student.academic_email);
            $('#view-student-phone').text(student.phone);
            $('#view-student-gender').text(student.gender);
            $('#view-student-academic-year').text(student.academic_year);
            $('#view-student-faculty').text(student.faculty?.name ?? 'N/A');
            $('#view-student-program').text(student.program?.name ?? 'N/A');
            $('#view-student-profile-complete').text(student.is_profile_complete ? 'Yes' : 'No');
            $('#view-student-is-active').text(student.active ? 'Active' : 'Inactive');
            $('#view-student-created').text(new Date(student.created_at).toLocaleString());
            $('#viewStudentModal').modal('show');
          }
        })
        .fail(() => {
          $('#viewStudentModal').modal('hide');
          Utils.showError('Failed to load student data');
        });
    });
  },

  handleDeleteStudent() {
    $(document).on('click', '.deleteStudentBtn', function() {
      const studentId = $(this).data('id');
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          ApiService.deleteStudent(studentId)
            .done(() => {
              $('#students-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Student has been deleted.');
            })
            .fail((xhr) => {
              const message = xhr.responseJSON?.message || 'Failed to delete student.';
              Utils.showError(message);
            });
        }
      });
    });
  },

  handleFormSubmit() {
    $('#studentForm').on('submit', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();
      ApiService.saveStudent(formData, currentStudentId)
        .done(() => {
          $('#studentModal').modal('hide');
          $('#students-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Student has been saved successfully.');
        })
        .fail((xhr) => {
          $('#studentModal').modal('hide');
          const message = xhr.responseJSON?.message || 'An error occurred. Please check your input.';
          Utils.showError(message);
        });
    });
  }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
const SearchManager = {
  initializeAdvancedSearch() {
    $('#search_academic_id, #search_name_en, #search_gender, #search_active, #search_governorate_id, #search_faculty_id').on('keyup change', function() {
      $('#students-table').DataTable().ajax.reload();
    });
    
    $('#clearStudentFiltersBtn').on('click', function() {
      $('#search_academic_id, #search_name_en, #search_gender, #search_active, #search_governorate_id, #search_faculty_id').val('');
      $('#students-table').DataTable().ajax.reload();
    });
  }
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================
const StatsManager = {
  loadStats() {
    Utils.toggleLoadingState('students', true);
    Utils.toggleLoadingState('students-male', true);
    Utils.toggleLoadingState('students-female', true);

    $.ajax({ url: '{{ route('resident.students.stats') }}', method: 'GET' })
      .done((response) => {
        if (response.success) {
          // Total students
          $('#students-value').text(response.data.total.count ?? '--');
          $('#students-last-updated').text(response.data.total.lastUpdateTime ?? '--');
          // Male students
          $('#students-male-value').text(response.data.male.count ?? '--');
          $('#students-male-last-updated').text(response.data.male.lastUpdateTime ?? '--');
          // Female students
          $('#students-female-value').text(response.data.female.count ?? '--');
          $('#students-female-last-updated').text(response.data.female.lastUpdateTime ?? '--');
        } else {
          $('#students-value, #students-male-value, #students-female-value').text('N/A');
          $('#students-last-updated, #students-male-last-updated, #students-female-last-updated').text('N/A');
        }
        Utils.toggleLoadingState('students', false);
        Utils.toggleLoadingState('students-male', false);
        Utils.toggleLoadingState('students-female', false);
      })
      .fail(() => {
        $('#students-value, #students-male-value, #students-female-value').text('N/A');
        $('#students-last-updated, #students-male-last-updated, #students-female-last-updated').text('N/A');
        Utils.toggleLoadingState('students', false);
        Utils.toggleLoadingState('students-male', false);
        Utils.toggleLoadingState('students-female', false);
        Utils.showError('Failed to load student statistics');
      });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
const StudentApp = {
  init() {
    StudentManager.handleAddStudent();
    StudentManager.handleEditStudent();
    StudentManager.handleViewStudent();
    StudentManager.handleDeleteStudent();
    StudentManager.handleFormSubmit();
    SearchManager.initializeAdvancedSearch();
    SelectManager.init();
    StatsManager.loadStats();
  }
};

$(document).ready(() => {
  StudentApp.init();
});

</script>
@endpush
