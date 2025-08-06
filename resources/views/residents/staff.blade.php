@extends('layouts.home')

@section('title', __('residents.staff.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-group" :label="__('residents.staff.total_staff')" id="staff" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('residents.staff.male_staff')" id="staff-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" :label="__('residents.staff.female_staff')" id="staff-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('residents.staff.page_header')"
        :description="__('residents.staff.page_description')"
        icon="bx bx-group"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-primary mx-2" id="addStaffBtn">
            <i class="bx bx-plus me-1"></i> {{ __('residents.staff.add_staff') }}
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#staffSearchCollapse" aria-expanded="false" aria-controls="staffSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> Search
        </button>
    </div>

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
    datatable: '{{ route('resident.staff.datatable') }}',
    stats: '{{ route('resident.staff.stats') }}'
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
// API SERVICE
// ===========================
var ApiService = {

  request: function(options) { return $.ajax(options); },

  fetchStaff: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.staff.show, id), method: 'GET' });
  },

  saveStaff: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.staff.update, id) : ROUTES.staff.store;
    var method = id ? 'PUT' : 'POST';
    return ApiService.request({ url: url, method: method, data: data });
  },

  deleteStaff: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.staff.destroy, id), method: 'DELETE' });
  },

  fetchDepartments: function() {
    return ApiService.request({ url: ROUTES.departments.all, method: 'GET' });
  },
  fetchCategories: function() {
    return ApiService.request({ url: ROUTES.categories.all, method: 'GET' });
  },
  fetchFaculties: function() {
    return ApiService.request({ url: ROUTES.faculties.all, method: 'GET' });
  },
  fetchCampusUnits: function() {
    return ApiService.request({ url: '{{ route('campus-units.all') }}', method: 'GET' });
  },
  fetchStats: function() {
    return ApiService.request({ url: ROUTES.staff.stats, method: 'GET' });
  }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
  populateModalDepartments: function() {
    var $select = $('#staff_department_id');
    Utils.populateSelect($select, [], { placeholder: 'Select Department' });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'Select Department' });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: 'Error loading departments' });
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateModalCategories: function() {
    var $select = $('#staff_category_id');
    Utils.populateSelect($select, [], { placeholder: 'Select Category' });
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          // Add data-type attribute for each option
          $select.empty().append('<option value="">Select Category</option>');
          response.data.forEach(function(cat) {
            $select.append(`<option value="${cat.id}" data-type="${cat.type ? cat.type.toLowerCase() : ''}">${cat.name}</option>`);
          });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: 'Error loading categories' });
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateModalFaculties: function() {
    var $select = $('#staff_faculty_id');
    Utils.populateSelect($select, [], { placeholder: 'Select Faculty' });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'Select Faculty' });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: 'Error loading faculties' });
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateUnitField: function(categoryType) {
    var $select = $('#staff_unit_id');
    Utils.populateSelect($select, [], { placeholder: 'Select Unit' });

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
        Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'Select Unit', triggerChange: true});
      }
    }).fail(function(xhr) {
      Utils.populateSelect($select, [], { placeholder: 'Error loading units' });
      Utils.handleAjaxError(xhr, 'An error occurred');
    });
  },
  populateSearchDepartments: function() {
    var $select = $('#search_department_id');
    Utils.populateSelect($select, [], { placeholder: 'All Departments' });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'All Departments' });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: 'Error loading departments' });
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateSearchCategories: function() {
    var $select = $('#search_category_id');
    Utils.populateSelect($select, [], { placeholder: 'All Categories' });
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'All Categories' });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: 'Error loading categories' });
        Utils.handleAjaxError(xhr, 'An error occurred');
      });
  },
  populateSearchFaculties: function() {
    var $select = $('#search_faculty_id');
    Utils.populateSelect($select, [], { placeholder: 'All Faculties' });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: 'All Faculties' });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: 'Error loading faculties' });
        Utils.handleAjaxError(xhr, 'An error occurred');
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
      var $unitFieldContainer = $('#unit_field_container');
      var $unitLabel = $('label[for="staff_unit_id"]');

      if (dataType === 'administrative' || dataType === 'faculty' || dataType === 'campus') {
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
        $unitFieldContainer.hide();
        $unitLabel.text('Unit');
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

            var dataType = $('#staff_category_id').find('option:selected').data('type') || '';
            if (dataType) {
              SelectManager.populateUnitField(dataType).done(function() {
                $('#staff_unit_id').val(staff.unit && staff.unit.id ? staff.unit.id : '');
              });
            } else {
              $('#staff_unit_id').val('');
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
          var msg = jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : 'Server error';
          Utils.showError('Failed to load staff data: ' + msg);
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
            $('#view-staff-staff-id').text(staff.id ?? 'N/A');
            $('#view-staff-name').text(staff.name_en ?? 'N/A');
            $('#view-staff-email').text(staff.email ?? 'N/A');
            $('#view-staff-national-id').text(staff.national_id ?? 'N/A');
            $('#view-staff-gender').text(staff.gender ?? 'N/A');
            $('#view-staff-category').text(staff.staff_category_type ? staff.staff_category_type.charAt(0).toUpperCase() + staff.staff_category_type.slice(1) : 'N/A');
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
        .fail(function(xhr) {
          $('#viewStaffModal').modal('hide');
          Utils.handleAjaxError(xhr,'An error occurred')
        });
    });
  },
  handleDelete: function() {
    $(document).on('click', '.deleteStaffBtn', function() {
      var staffId = $(this).data('id');
      Utils.showConfirmDialog({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteStaff(staffId)
            .done(function() {
              $('#staff-table').DataTable().ajax.reload(null, false);
              StatsManager.load();
              Utils.showSuccess('Staff has been deleted.');
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr,'An error occurred')
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
          Utils.handleAjaxError(xhr,'An error occurred')

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
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: ['staff','staff-male', 'staff-female'],
  onError: 'Failed to load staff statistics'
});

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