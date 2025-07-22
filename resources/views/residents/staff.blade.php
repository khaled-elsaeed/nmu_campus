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
        <label for="search_name" class="form-label">Name:</label>
        <input type="text" class="form-control" id="search_name" name="search_name">
    </div>
    <div class="col-md-4">
        <label for="search_gender" class="form-label">Gender:</label>
        <select class="form-control" id="search_gender" name="gender">
            <option value="">All</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_department_id" class="form-label">Department:</label>
        <select class="form-control" id="search_department_id" name="department_id">
            <option value="">All Departments</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_category_id" class="form-label">Category:</label>
        <select class="form-control" id="search_category_id" name="category_id">
            <option value="">All Categories</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_faculty_id" class="form-label">Faculty:</label>
        <select class="form-control" id="search_faculty_id" name="search_faculty_id">
            <option value="">All Faculties</option>
        </select>
    </div>
    <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearStaffFiltersBtn" type="button">
        <i class="bx bx-x"></i> Clear Filters
    </button>
</x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable 
        :headers="['Name', 'Gender', 'Category', 'Unit Type', 'Unit Name', 'Notes', 'Actions']"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'category', 'name' => 'category'],
            ['data' => 'unit_type', 'name' => 'unit_type'],
            ['data' => 'unit_name', 'name' => 'unit_name'],
            ['data' => 'notes', 'name' => 'notes'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.staff.datatable')"
        :table-id="'staff-table'"
        :filter-fields="['search_name','search_gender','search_category_id','search_department_id','search_faculty_id']"
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
                        <label for="staff_national_id" class="form-label">National ID <span class="text-danger">*</span></label>
                        <input type="text" id="staff_national_id" name="national_id" class="form-control" required>
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
                        <label for="staff_category_id" class="form-label">Category</label>
                        <select id="staff_category_id" name="staff_category_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3" id="unit_field_container">
                        <label for="staff_unit_id" class="form-label">Unit</label>
                        <select id="staff_unit_id" name="unit_id" class="form-control"></select>
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
                    <label class="form-label fw-bold">Email:</label>
                    <p id="view-staff-email" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">National ID:</label>
                    <p id="view-staff-national-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Gender:</label>
                    <p id="view-staff-gender" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Category:</label>
                    <p id="view-staff-category" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Unit Type:</label>
                    <p id="view-staff-unit-type" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Unit Name:</label>
                    <p id="view-staff-unit-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Notes:</label>
                    <p id="view-staff-notes" class="mb-0"></p>
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
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
  },
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
  request: function(options) { return $.ajax(options); },
  fetchStaff: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.staff.show, id), method: 'GET' });
  },
  saveStaff: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.staff.update, id) : ROUTES.staff.store;
    var method = id ? 'PUT' : 'POST';
    return this.request({ url: url, method: method, data: data });
  },
  deleteStaff: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.staff.destroy, id), method: 'DELETE' });
  },
  fetchDepartments: function() {
    return this.request({ url: ROUTES.departments.all, method: 'GET' });
  },
  fetchCategories: function() {
    return this.request({ url: ROUTES.categories.all, method: 'GET' });
  },
  fetchFaculties: function() {
    return this.request({ url: ROUTES.faculties.all, method: 'GET' });
  },
  fetchCampusUnits: function() {
    return this.request({ url: '{{ route('campus-units.all') }}', method: 'GET' });
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  populateModalDepartments: function() {
    var $select = $('#staff_department_id');
    $select.empty().append('<option value="">Select Department</option>');
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(dep) {
            $select.append(`<option value="${dep.id}">${dep.name}</option>`);
          });
        }
      })
      .fail(function() {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading departments</option>');
        Utils.showError('Failed to load departments');
      });
  },
  populateModalCategories: function() {
    var $select = $('#staff_category_id');
    $select.empty().append('<option value="">Select Category</option>');
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(cat) {
            $select.append(`<option value="${cat.id}" data-type="${cat.type.toLowerCase()}">${cat.name}</option>`);
          });
        }
      })
      .fail(function() {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading categories</option>');
        Utils.showError('Failed to load categories');
      });
  },
  populateModalFaculties: function() {
    var $select = $('#staff_faculty_id');
    $select.empty().append('<option value="">Select Faculty</option>');
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(faculty) {
            $select.append(`<option value="${faculty.id}">${faculty.name}</option>`);
          });
        }
      })
      .fail(function() {
        $('#staffModal').modal('hide');
        $select.empty().append('<option value="">Error loading faculties</option>');
        Utils.showError('Failed to load faculties');
      });
  },
  populateUnitField: function(categoryType) {
    var $select = $('#staff_unit_id');
    $select.empty().append('<option value="">Select Unit</option>');
    
    var promise;
    if (categoryType === 'faculty') {
      promise = ApiService.fetchFaculties();
    } else if (categoryType === 'administrative') {
      promise = ApiService.fetchDepartments();
    } else if (categoryType === 'campus') {
      promise = ApiService.fetchCampusUnits();
    } else {
      return $.Deferred().resolve();
    }
    
    return promise.done(function(response) {
      if (response.success) {
        response.data.forEach(function(item) {
          $select.append(`<option value="${item.id}">${item.name}</option>`);
        });
      }
    }).fail(function() {
      $select.empty().append('<option value="">Error loading units</option>');
      Utils.showError(`Failed to load ${categoryType} units`);
    });
  },
  populateSearchDepartments: function() {
    var $select = $('#search_department_id');
    $select.empty().append('<option value="">All Departments</option>');
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(dep) {
            $select.append(`<option value="${dep.id}">${dep.name}</option>`);
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading departments</option>');
        Utils.showError('Failed to load departments');
      });
  },
  populateSearchCategories: function() {
    var $select = $('#search_category_id');
    $select.empty().append('<option value="">All Categories</option>');
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(cat) {
            $select.append(`<option value="${cat.id}">${cat.name}</option>`);
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading categories</option>');
        Utils.showError('Failed to load categories');
      });
  },
  populateSearchFaculties: function() {
    var $select = $('#search_faculty_id');
    $select.empty().append('<option value="">All Faculties</option>');
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          response.data.forEach(function(faculty) {
            $select.append(`<option value="${faculty.id}">${faculty.name}</option>`);
          });
        }
      })
      .fail(function() {
        $select.empty().append('<option value="">Error loading faculties</option>');
        Utils.showError('Failed to load faculties');
      });
  },
  init: function() {
    this.populateModalDepartments();
    this.populateModalCategories();
    this.populateModalFaculties();
    this.populateSearchDepartments();
    this.populateSearchCategories();
    this.populateSearchFaculties();
  }
};

// ===========================
// STAFF MANAGER
// ===========================
var StaffManager = {
  currentStaffId: null,
  handleAdd: function() {
    var self = this;
    $(document).on('click', '#addStaffBtn', function() {
      self.currentStaffId = null;
      $('#staffModal .modal-title').text('Add Staff');
      $('#staffForm')[0].reset();
      $('#staffModal').modal('show');
    });
  },
  handleCategoryChange: function() {
    $(document).on('change', '#staff_category_id', function() {
      var dataType = $(this).find('option:selected').data('type') || '';
      console.log('Category changed. Selected data-type:', dataType);
      
      var $unitFieldContainer = $('#unit_field_container');
      var $unitLabel = $('label[for="staff_unit_id"]');
      
      if (dataType === 'administrative' || dataType === 'faculty' || dataType === 'campus') {
        console.log('Showing unit field for type:', dataType);
        $unitFieldContainer.show();
        
        switch (dataType) {
          case 'faculty':
            $unitLabel.text('Faculty');
            break;
          case 'administrative':
            $unitLabel.text('Department');
            break;
          case 'campus':
            $unitLabel.text('Campus Unit');
            break;
          default:
            $unitLabel.text('Unit');
        }
        
        SelectManager.populateUnitField(dataType);
      } else {
        console.log('Hiding unit field for unknown type:', dataType);
        $unitFieldContainer.hide();
        $unitLabel.text('Unit');
      }
    });
    
    $('#staffModal').on('show.bs.modal', function() {
      if (!$('#staff_category_id').val()) {
        console.log('Modal opened for new staff. Hiding unit field initially.');
        $('#unit_field_container').hide();
        $('label[for="staff_unit_id"]').text('Unit');
      } else {
        console.log('Modal opened for editing. Triggering category change.');
        $('#staff_category_id').trigger('change');
      }
    });
  },
  handleEdit: function() {
    var self = this;
    $(document).on('click', '.editStaffBtn', function() {
      var staffId = $(this).data('id');
      if (!staffId) {
        Utils.showError('Staff ID not set');
        return;
      }
      self.currentStaffId = staffId;
      $('#staffModal .modal-title').text('Edit Staff');
      ApiService.fetchStaff(staffId)
        .done(function(response) {
          if (response.success && response.data) {
            var staff = response.data;
            $('#staff_name_en').val(staff.name_en || '');
            $('#staff_name_ar').val(staff.name_ar || '');
            $('#staff_email').val(staff.email || '');
            $('#staff_national_id').val(staff.national_id || '');
            $('#staff_gender').val(staff.gender || '');
            
            $('#staff_category_id').val(staff.staff_category_id || '');
            
            console.log('Selected option value:', $('#staff_category_id').val());
            console.log('Selected option data-type:', $('#staff_category_id').find('option:selected').data('type'));
            
            var dataType = $('#staff_category_id').find('option:selected').data('type') || '';
            if (dataType) {
              SelectManager.populateUnitField(dataType).done(function() {
                $('#staff_unit_id').val(staff.unit && staff.unit.id ? staff.unit.id : '');
                console.log('staff_unit_id set to:', $('#staff_unit_id').val());
              });
            } else {
              $('#staff_unit_id').val('');
              console.log('staff_unit_id cleared');
            }

            $('#staff_notes').val(staff.notes !== null ? staff.notes : '');
            $('#staffModal').modal('show');
          } else {
            $('#staffModal').modal('hide');
            Utils.showError('Failed to load staff data: ' + (response.message || 'Unknown error'));
          }
        })
        .fail(function(jqXHR) {
          $('#staffModal').modal('hide');
          Utils.showError('Failed to load staff data: ' + (jqXHR.responseJSON?.message || 'Server error'));
        });
    });
  },
  handleView: function() {
    $(document).on('click', '.viewStaffBtn', function() {
      var staffId = $(this).data('id');
      ApiService.fetchStaff(staffId)
        .done(function(response) {
          if (response.success) {
            var staff = response.data;
            // Use direct fields from API response structure
            $('#view-staff-staff-id').text(staff.id ?? 'N/A');
            $('#view-staff-name').text(staff.name_en ?? 'N/A');
            $('#view-staff-email').text(staff.email ?? 'N/A');
            $('#view-staff-national-id').text(staff.national_id ?? 'N/A');
            $('#view-staff-gender').text(staff.gender ?? 'N/A');
            // Category: try to use staff.staff_category_type or fallback
            $('#view-staff-category').text(staff.staff_category_type ? staff.staff_category_type.charAt(0).toUpperCase() + staff.staff_category_type.slice(1) : 'N/A');
            // Unit type and name
            var unitType = 'Unassigned';
            var unitName = 'N/A';
            if (staff.unit && staff.unit.type) {
              unitType = staff.unit.type.charAt(0).toUpperCase() + staff.unit.type.slice(1);
              unitName = staff.unit.name ?? 'N/A';
            }
            $('#view-staff-unit-type').text(unitType);
            $('#view-staff-unit-name').text(unitName);
            $('#view-staff-notes').text(staff.notes !== null && staff.notes !== undefined ? staff.notes : 'N/A');
            $('#view-staff-created').text(staff.created_at ? new Date(staff.created_at).toLocaleString() : 'N/A');
            $('#viewStaffModal').modal('show');
          }
        })
        .fail(function() {
          $('#viewStaffModal').modal('hide');
          Utils.showError('Failed to load staff data');
        });
    });
  },
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
              StatsManager.load();
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
  handleFormSubmit: function() {
    var self = this;
    $('#staffForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      ApiService.saveStaff(formData, self.currentStaffId)
        .done(function() {
          $('#staffModal').modal('hide');
          $('#staff-table').DataTable().ajax.reload(null, false);
          StatsManager.load();
          Utils.showSuccess('Staff has been saved successfully.');
        })
        .fail(function(xhr) {
          $('#staffModal').modal('hide');
          var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please check your input.';
          Utils.showError(message);
        });
    });
  },
  init: function() {
    this.handleAdd();
    this.handleCategoryChange();
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
  init: function() {
    this.bindEvents();
  },
  bindEvents: function() {
    $('#search_name, #search_gender, #search_department_id, #search_category_id, #search_faculty_id').on('keyup change', function() {
      $('#staff-table').DataTable().ajax.reload();
    });
    $('#clearStaffFiltersBtn').on('click', function() {
      $('#search_name, #search_gender, #search_department_id, #search_category_id, #search_faculty_id').val('');
      $('#staff-table').DataTable().ajax.reload();
    });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = {
  init: function() {
    this.load();
  },
  load: function() {
    this.toggleAllLoadingStates(true);
    $.ajax({ url: '{{ route('resident.staff.stats') }}', method: 'GET' })
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
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
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load staff statistics');
  },
  updateStatElement: function(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  setAllStatsToNA: function() {
    ['staff', 'staff-male', 'staff-female'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
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