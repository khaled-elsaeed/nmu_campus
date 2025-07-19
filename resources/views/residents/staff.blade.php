@extends('layouts.home')

@section('title', 'Staff Management | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="primary" icon="bx bx-group" label="Total Staff" id="staff" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" label="Male Staff" id="staff-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" label="Female Staff" id="staff-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Staff"
        description="Manage staff and their information."
        icon="bx bx-group"
    >
        <button class="btn btn-primary mx-2" id="addStaffBtn">
            <i class="bx bx-plus me-1"></i> Add Staff
        </button>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#staffSearchCollapse" aria-expanded="false" aria-controls="staffSearchCollapse">
            <i class="bx bx-search"></i>
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
<x-ui.advanced-search 
    title="Advanced Search" 
    formId="advancedStaffSearch" 
    collapseId="staffSearchCollapse"
    :collapsed="false"
>
    <div class="col-md-4">
        <label for="search_staff_id" class="form-label">Staff ID:</label>
        <input type="text" class="form-control" id="search_staff_id">
    </div>
    <div class="col-md-4">
        <label for="search_name" class="form-label">Name:</label>
        <input type="text" class="form-control" id="search_name">
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
        <label for="search_department_id" class="form-label">Department:</label>
        <select class="form-control" id="search_department_id">
            <option value="">All Departments</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_category_id" class="form-label">Category:</label>
        <select class="form-control" id="search_category_id">
            <option value="">All Categories</option>
        </select>
    </div>
    <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearStaffFiltersBtn" type="button">
        <i class="bx bx-x"></i> Clear Filters
    </button>
</x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Name', 'Gender', 'Category', 'Faculty', 'Department', 'Notes', 'Actions']"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'category', 'name' => 'category'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'department', 'name' => 'department'],
            ['data' => 'notes', 'name' => 'notes'],
            ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.staff.datatable')"
        :table-id="'staff-table'"
        :filter-fields="['search_name','search_gender','search_category_id','search_faculty_id','search_department_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Staff Modal --}}
    <x-ui.modal 
        id="staffModal"
        title="Add/Edit Staff"
        :scrollable="true"
        class="staff-modal"
    >
        <x-slot name="slot">
            <form id="staffForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="staff_name_en" class="form-label">Name (EN)</label>
                        <input type="text" id="staff_name_en" name="name_en" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_name_ar" class="form-label">Name (AR)</label>
                        <input type="text" id="staff_name_ar" name="name_ar" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_email" class="form-label">Email</label>
                        <input type="email" id="staff_email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_gender" class="form-label">Gender</label>
                        <select id="staff_gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_password" class="form-label">Password</label>
                        <input type="password" id="staff_password" name="password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_category_id" class="form-label">Category</label>
                        <select id="staff_category_id" name="staff_category_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_faculty_id" class="form-label">Faculty</label>
                        <select id="staff_faculty_id" name="faculty_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_department_id" class="form-label">Department</label>
                        <select id="staff_department_id" name="department_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="staff_notes" class="form-label">Notes</label>
                        <textarea id="staff_notes" name="notes" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" form="staffForm">Save</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Staff Modal --}}
    <x-ui.modal 
        id="viewStaffModal"
        title="Staff Details"
        :scrollable="true"
        class="view-staff-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Staff ID:</label>
                    <p id="view-staff-staff-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Name:</label>
                    <p id="view-staff-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Phone:</label>
                    <p id="view-staff-phone" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Gender:</label>
                    <p id="view-staff-gender" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Department:</label>
                    <p id="view-staff-department" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Category:</label>
                    <p id="view-staff-category" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Active:</label>
                    <p id="view-staff-is-active" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-staff-created" class="mb-0"></p>
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
  staff: {
    show: '{{ route('resident.staff.show', ':id') }}',
    store: '{{ route('resident.staff.store') }}',
    update: '{{ route('resident.staff.update', ':id') }}',
    destroy: '{{ route('resident.staff.destroy', ':id') }}',
    datatable: '{{ route('resident.staff.datatable') }}'
  },
  departments: {
    all: '{{ route('departments.all') }}'
  },
  categories: {
    all: '{{ route('staff-categories.all') }}'
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
  fetchStaff(id) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.staff.show, id),
      method: 'GET'
    });
  },
  saveStaff(data, id = null) {
    const url = id ? Utils.replaceRouteId(ROUTES.staff.update, id) : ROUTES.staff.store;
    const method = id ? 'PUT' : 'POST';
    return ApiService.request({ url, method, data });
  },
  deleteStaff(id) {
    return ApiService.request({
      url: Utils.replaceRouteId(ROUTES.staff.destroy, id),
      method: 'DELETE'
    });
  },
  fetchDepartments() {
    return ApiService.request({
      url: ROUTES.departments.all,
      method: 'GET'
    });
  },
  fetchCategories() {
    return ApiService.request({
      url: ROUTES.categories.all,
      method: 'GET'
    });
  },
  fetchFaculties() {
    return ApiService.request({
      url: ROUTES.faculties.all,
      method: 'GET'
    });
  }
};

// ===========================
// SELECT MANAGEMENT
// ===========================
const SelectManager = {
  // Modal form selects
  populateModalDepartments() {
    const $select = $('#staff_department_id');
    $select.empty().append('<option value="">Select Department</option>');
    ApiService.fetchDepartments()
      .done((response) => {
        if (response.success) {
          response.data.forEach(dep => {
            $select.append(`<option value="${dep.id}">${dep.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading departments</option>');
      });
  },
  populateModalCategories() {
    const $select = $('#staff_category_id');
    $select.empty().append('<option value="">Select Category</option>');
    ApiService.fetchCategories()
      .done((response) => {
        if (response.success) {
          response.data.forEach(cat => {
            $select.append(`<option value="${cat.id}">${cat.name}</option>`);
          });
        }
      })
      .fail(() => {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading categories</option>');
      });
  },
  populateModalFaculties() {
    const $select = $('#staff_faculty_id');
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
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },
  // Search filter selects
  populateSearchDepartments() {
    const $select = $('#search_department_id');
    $select.empty().append('<option value="">All Departments</option>');
    ApiService.fetchDepartments()
      .done((response) => {
        if (response.success) {
          response.data.forEach(dep => {
            $select.append(`<option value="${dep.id}">${dep.name}</option>`);
          });
        }
      })
      .fail(() => {
        $select.empty().append('<option value="">Error loading departments</option>');
      });
  },
  populateSearchCategories() {
    const $select = $('#search_category_id');
    $select.empty().append('<option value="">All Categories</option>');
    ApiService.fetchCategories()
      .done((response) => {
        if (response.success) {
          response.data.forEach(cat => {
            $select.append(`<option value="${cat.id}">${cat.name}</option>`);
          });
        }
      })
      .fail(() => {
        $select.empty().append('<option value="">Error loading categories</option>');
      });
  },
  init() {
    // Initialize modal selects
    this.populateModalDepartments();
    this.populateModalCategories();
    this.populateModalFaculties();
    // Initialize search selects
    this.populateSearchDepartments();
    this.populateSearchCategories();
  }
};

// ===========================
// STAFF CRUD & MODALS
// ===========================
let currentStaffId = null;

const StaffManager = {
  handleAddStaff() {
    $(document).on('click', '#addStaffBtn', function() {
      currentStaffId = null;
      $('#staffModal .modal-title').text('Add Staff');
      $('#staffForm')[0].reset();
      $('#staffModal').modal('show');
    });
  },
  handleEditStaff() {
    $(document).on('click', '.editStaffBtn', function() {
      const staffId = $(this).data('id');
      currentStaffId = staffId;
      $('#staffModal .modal-title').text('Edit Staff');
      ApiService.fetchStaff(staffId)
        .done((response) => {
          if (response.success) {
            const staff = response.data;
            $('#staff_name_en').val(staff.name_en);
            $('#staff_name_ar').val(staff.name_ar);
            $('#staff_email').val(staff.email);
            $('#staff_gender').val(staff.gender);
            $('#staff_password').val(''); // Clear password for edit
            $('#staff_category_id').val(staff.staff_category_id);
            $('#staff_faculty_id').val(staff.faculty_id);
            $('#staff_department_id').val(staff.department_id);
            $('#staff_notes').val(staff.notes);
            $('#staffModal').modal('show');
          }
        })
        .fail(() => {
          $('#staffModal').modal('hide');
          Utils.showError('Failed to load staff data');
        });
    });
  },
  handleViewStaff() {
    $(document).on('click', '.viewStaffBtn', function() {
      const staffId = $(this).data('id');
      ApiService.fetchStaff(staffId)
        .done((response) => {
          if (response.success) {
            const staff = response.data;
            $('#view-staff-staff-id').text(staff.staff_id);
            $('#view-staff-name').text(staff.name_en); // Display name_en
            $('#view-staff-phone').text(staff.phone);
            $('#view-staff-gender').text(staff.gender);
            $('#view-staff-department').text(staff.department?.name ?? 'N/A');
            $('#view-staff-category').text(staff.category?.name ?? 'N/A');
            $('#view-staff-is-active').text(staff.active ? 'Active' : 'Inactive');
            $('#view-staff-created').text(new Date(staff.created_at).toLocaleString());
            $('#viewStaffModal').modal('show');
          }
        })
        .fail(() => {
          $('#viewStaffModal').modal('hide');
          Utils.showError('Failed to load staff data');
        });
    });
  },
  handleDeleteStaff() {
    $(document).on('click', '.deleteStaffBtn', function() {
      const staffId = $(this).data('id');
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
          ApiService.deleteStaff(staffId)
            .done(() => {
              $('#staff-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Staff has been deleted.');
            })
            .fail((xhr) => {
              const message = xhr.responseJSON?.message || 'Failed to delete staff.';
              Utils.showError(message);
            });
        }
      });
    });
  },
  handleFormSubmit() {
    $('#staffForm').on('submit', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();
      ApiService.saveStaff(formData, currentStaffId)
        .done(() => {
          $('#staffModal').modal('hide');
          $('#staff-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Staff has been saved successfully.');
        })
        .fail((xhr) => {
          $('#staffModal').modal('hide');
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
    $('#search_staff_id, #search_name, #search_gender, #search_active, #search_department_id, #search_category_id').on('keyup change', function() {
      $('#staff-table').DataTable().ajax.reload();
    });
    $('#clearStaffFiltersBtn').on('click', function() {
      $('#search_staff_id, #search_name, #search_gender, #search_active, #search_department_id, #search_category_id').val('');
      $('#staff-table').DataTable().ajax.reload();
    });
  }
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================
const StatsManager = {
  loadStats() {
    Utils.toggleLoadingState('staff', true);
    Utils.toggleLoadingState('staff-male', true);
    Utils.toggleLoadingState('staff-female', true);
    $.ajax({ url: '{{ route('resident.staff.stats') }}', method: 'GET' })
      .done((response) => {
        if (response.success) {
          // Total staff
          $('#staff-value').text(response.data.total.count ?? '--');
          $('#staff-last-updated').text(response.data.total.lastUpdateTime ?? '--');
          // Male staff
          $('#staff-male-value').text(response.data.male.count ?? '--');
          $('#staff-male-last-updated').text(response.data.male.lastUpdateTime ?? '--');
          // Female staff
          $('#staff-female-value').text(response.data.female.count ?? '--');
          $('#staff-female-last-updated').text(response.data.female.lastUpdateTime ?? '--');
        } else {
          $('#staff-value, #staff-male-value, #staff-female-value').text('N/A');
          $('#staff-last-updated, #staff-male-last-updated, #staff-female-last-updated').text('N/A');
        }
        Utils.toggleLoadingState('staff', false);
        Utils.toggleLoadingState('staff-male', false);
        Utils.toggleLoadingState('staff-female', false);
      })
      .fail(() => {
        $('#staff-value, #staff-male-value, #staff-female-value').text('N/A');
        $('#staff-last-updated, #staff-male-last-updated, #staff-female-last-updated').text('N/A');
        Utils.toggleLoadingState('staff', false);
        Utils.toggleLoadingState('staff-male', false);
        Utils.toggleLoadingState('staff-female', false);
        Utils.showError('Failed to load staff statistics');
      });
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
const StaffApp = {
  init() {
    StaffManager.handleAddStaff();
    StaffManager.handleEditStaff();
    StaffManager.handleViewStaff();
    StaffManager.handleDeleteStaff();
    StaffManager.handleFormSubmit();
    SearchManager.initializeAdvancedSearch();
    SelectManager.init();
    StatsManager.loadStats();
  }
};

$(document).ready(() => {
  StaffApp.init();
});
</script>
@endpush 