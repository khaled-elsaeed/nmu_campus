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
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
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
/**
 * Staff Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - SelectManager: Handles dropdown population
 * - StaffManager: Handles CRUD and actions for staff
 * - SearchManager: Handles advanced search
 * - StatsManager: Handles statistics cards
 * - StaffApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
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
   * Fetch a staff by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchStaff: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.staff.show, id), method: 'GET' });
  },
  /**
   * Save (create or update) a staff
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveStaff: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.staff.update, id) : ROUTES.staff.store;
    var method = id ? 'PUT' : 'POST';
    return this.request({ url: url, method: method, data: data });
  },
  /**
   * Delete a staff by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteStaff: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.staff.destroy, id), method: 'DELETE' });
  },
  /**
   * Fetch all departments
   * @returns {jqXHR}
   */
  fetchDepartments: function() {
    return this.request({ url: ROUTES.departments.all, method: 'GET' });
  },
  /**
   * Fetch all categories
   * @returns {jqXHR}
   */
  fetchCategories: function() {
    return this.request({ url: ROUTES.categories.all, method: 'GET' });
  },
  /**
   * Fetch all faculties
   * @returns {jqXHR}
   */
  fetchFaculties: function() {
    return this.request({ url: ROUTES.faculties.all, method: 'GET' });
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  /**
   * Populate departments in modal
   */
  populateModalDepartments: function() {
    var $select = $('#staff_department_id');
    $select.empty().append('<option value="">Select Department</option>');
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(dep) {
            $select.append('<option value="' + dep.id + '">' + dep.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading departments</option>');
      });
  },
  /**
   * Populate categories in modal
   */
  populateModalCategories: function() {
    var $select = $('#staff_category_id');
    $select.empty().append('<option value="">Select Category</option>');
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(cat) {
            $select.append('<option value="' + cat.id + '">' + cat.name + '</option>');
          });
        }
      })
      .fail(function() {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading categories</option>');
      });
  },
  /**
   * Populate faculties in modal
   */
  populateModalFaculties: function() {
    var $select = $('#staff_faculty_id');
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
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading faculties</option>');
      });
  },
  /**
   * Populate departments in search
   */
  populateSearchDepartments: function() {
    var $select = $('#search_department_id');
    $select.empty().append('<option value="">All Departments</option>');
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(dep) {
            $select.append('<option value="' + dep.id + '">' + dep.name + '</option>');
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading departments</option>');
      });
  },
  /**
   * Populate categories in search
   */
  populateSearchCategories: function() {
    var $select = $('#search_category_id');
    $select.empty().append('<option value="">All Categories</option>');
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(cat) {
            $select.append('<option value="' + cat.id + '">' + cat.name + '</option>');
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading categories</option>');
      });
  },
  /**
   * Initialize select manager
   */
  init: function() {
    this.populateModalDepartments();
    this.populateModalCategories();
    this.populateModalFaculties();
    this.populateSearchDepartments();
    this.populateSearchCategories();
  }
};

// ===========================
// STAFF MANAGER
// ===========================
var StaffManager = {
  currentStaffId: null,
  /**
   * Bind add staff button
   */
  handleAdd: function() {
    var self = this;
    $(document).on('click', '#addStaffBtn', function() {
      self.currentStaffId = null;
      $('#staffModal .modal-title').text('Add Staff');
      $('#staffForm')[0].reset();
      $('#staffModal').modal('show');
    });
  },
  /**
   * Bind edit staff button
   */
  handleEdit: function() {
    var self = this;
    $(document).on('click', '.editStaffBtn', function() {
      var staffId = $(this).data('id');
      self.currentStaffId = staffId;
      $('#staffModal .modal-title').text('Edit Staff');
      ApiService.fetchStaff(staffId)
        .done(function(response) {
          if (response.success) {
            var staff = response.data;
            $('#staff_name_en').val(staff.name_en);
            $('#staff_name_ar').val(staff.name_ar);
            $('#staff_email').val(staff.email);
            $('#staff_gender').val(staff.gender);
            $('#staff_password').val('');
            $('#staff_category_id').val(staff.staff_category_id);
            $('#staff_faculty_id').val(staff.faculty_id);
            $('#staff_department_id').val(staff.department_id);
            $('#staff_notes').val(staff.notes);
            $('#staffModal').modal('show');
          }
        })
        .fail(function() {
          $('#staffModal').modal('hide');
          Utils.showError('Failed to load staff data');
        });
    });
  },
  /**
   * Bind view staff button
   */
  handleView: function() {
    $(document).on('click', '.viewStaffBtn', function() {
      var staffId = $(this).data('id');
      ApiService.fetchStaff(staffId)
        .done(function(response) {
          if (response.success) {
            var staff = response.data;
            $('#view-staff-staff-id').text(staff.staff_id);
            $('#view-staff-name').text(staff.name_en);
            $('#view-staff-phone').text(staff.phone);
            $('#view-staff-gender').text(staff.gender);
            $('#view-staff-department').text(staff.department && staff.department.name ? staff.department.name : 'N/A');
            $('#view-staff-category').text(staff.category && staff.category.name ? staff.category.name : 'N/A');
            $('#view-staff-is-active').text(staff.active ? 'Active' : 'Inactive');
            $('#view-staff-created').text(new Date(staff.created_at).toLocaleString());
            $('#viewStaffModal').modal('show');
          }
        })
        .fail(function() {
          $('#viewStaffModal').modal('hide');
          Utils.showError('Failed to load staff data');
        });
    });
  },
  /**
   * Bind delete staff button
   */
  handleDelete: function() {
    $(document).on('click', '.deleteStaffBtn', function() {
      var staffId = $(this).data('id');
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
          ApiService.deleteStaff(staffId)
            .done(function() {
              $('#staff-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Staff has been deleted.');
            })
            .fail(function(xhr) {
              var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete staff.';
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
    $('#staffForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      ApiService.saveStaff(formData, self.currentStaffId)
        .done(function() {
          $('#staffModal').modal('hide');
          $('#staff-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Staff has been saved successfully.');
        })
        .fail(function(xhr) {
          $('#staffModal').modal('hide');
          var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
          Utils.showError(message);
        });
    });
  },
  /**
   * Initialize all staff manager handlers
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
    $.ajax({ url: '{{ route('resident.staff.stats') }}', method: 'GET' })
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
      this.updateStatElement('staff', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('staff-male', stats.male.count, stats.male.lastUpdateTime);
      this.updateStatElement('staff-female', stats.female.count, stats.female.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load staff statistics');
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
    ['staff', 'staff-male', 'staff-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['staff', 'staff-male', 'staff-female'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var StaffApp = {
  /**
   * Initialize all managers
   */
  init: function() {
    StaffManager.init();
    SearchManager.init();
    SelectManager.init();
    StatsManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  StaffApp.init();
});
</script>
@endpush 