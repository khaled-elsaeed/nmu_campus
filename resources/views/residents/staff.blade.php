@extends('layouts.home')

@section('title', __('Staff Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-group" :label="__('Total Staff')" id="staff" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('Male Staff')" id="staff-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" :label="__('Female Staff')" id="staff-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Staff Management')"
        :description="__('Manage all staff members and their details')"
        icon="bx bx-group"
    >
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-primary mx-2" id="addStaffBtn">
            <i class="bx bx-plus me-1"></i> {{ __('Add Staff') }}
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#staffSearchCollapse" aria-expanded="false" aria-controls="staffSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
        </button>
    </div>

    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
<x-ui.advanced-search 
    :title="__('Advanced Staff Search')" 
    formId="advancedStaffSearch" 
    collapseId="staffSearchCollapse"
    :collapsed="false"
>

    <div class="col-md-4">
        <label for="search_name" class="form-label">{{ __('Name') }}:</label>
        <input type="text" class="form-control" id="search_name" name="search_name">
    </div>
    <div class="col-md-4">
        <label for="search_gender" class="form-label">{{ __('Gender') }}:</label>
        <select class="form-control" id="search_gender" name="gender">
            <option value="">{{ __('All') }}</option>
            <option value="male">{{ __('Male') }}</option>
            <option value="female">{{ __('Female') }}</option>
            <option value="other">{{ __('Other') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_department_id" class="form-label">{{ __('Department') }}:</label>
        <select class="form-control" id="search_department_id" name="department_id">
            <option value="">{{ __('All Departments') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_category_id" class="form-label">{{ __('Category') }}:</label>
        <select class="form-control" id="search_category_id" name="category_id">
            <option value="">{{ __('All Categories') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_faculty_id" class="form-label">{{ __('Faculty') }}:</label>
        <select class="form-control" id="search_faculty_id" name="search_faculty_id">
            <option value="">{{ __('All Faculties') }}</option>
        </select>
    </div>
    <div class="w-100"></div>
    <button class="btn btn-outline-secondary mt-2 ms-2" id="clearStaffFiltersBtn" type="button">
        <i class="bx bx-x"></i> {{ __('Clear Filters') }}
    </button>
</x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[
            __('Name'), 
            __('Gender'), 
            __('Category'), 
            __('Unit Type'), 
            __('Unit Name'), 
            __('Notes'), 
            __('Actions')
        ]"
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
        :title="__('Add/Edit Staff')"
        :scrollable="true"
        class="staff-modal"
    >
        <x-slot name="slot">
            <form id="staffForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="staff_name_en" class="form-label">{{ __('Name (EN)') }}</label>
                        <input type="text" id="staff_name_en" name="name_en" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_name_ar" class="form-label">{{ __('Name (AR)') }}</label>
                        <input type="text" id="staff_name_ar" name="name_ar" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" id="staff_email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_national_id" class="form-label">{{ __('National ID') }} <span class="text-danger">*</span></label>
                        <input type="text" id="staff_national_id" name="national_id" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_gender" class="form-label">{{ __('Gender') }}</label>
                        <select id="staff_gender" name="gender" class="form-control" required>
                            <option value="">{{ __('Select Gender') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="staff_category_id" class="form-label">{{ __('Category') }}</label>
                        <select id="staff_category_id" name="staff_category_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3" id="unit_field_container">
                        <label for="staff_unit_id" class="form-label">{{ __('Unit') }}</label>
                        <select id="staff_unit_id" name="unit_id" class="form-control"></select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="staff_notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea id="staff_notes" name="notes" class="form-control"></textarea>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary" form="staffForm">{{ __('Save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Staff Modal --}}
    <x-ui.modal 
        id="viewStaffModal"
        :title="__('Staff Details')"
        :scrollable="true"
        class="view-staff-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Staff ID') }}:</label>
                    <p id="view-staff-staff-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Name (EN)') }}:</label>
                    <p id="view-staff-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Email') }}:</label>
                    <p id="view-staff-email" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('National ID') }}:</label>
                    <p id="view-staff-national-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Gender') }}:</label>
                    <p id="view-staff-gender" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Category') }}:</label>
                    <p id="view-staff-category" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Unit Type') }}:</label>
                    <p id="view-staff-unit-type" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Unit Name') }}:</label>
                    <p id="view-staff-unit-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Notes') }}:</label>
                    <p id="view-staff-notes" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}:</label>
                    <p id="view-staff-created" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
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
// Translations
// ===========================
var TRANSLATION = {
  add: '{{ __("Add") }}',
  edit: '{{ __("Edit") }}',
  delete: '{{ __("Delete") }}',
  close: '{{ __("Close") }}',
  save: '{{ __("Save") }}',
  update: '{{ __("Update") }}',
  cancel: '{{ __("Cancel") }}',
  confirm: '{{ __("Confirm") }}',
  are_you_sure: '{{ __("Are you sure?") }}',
  you_wont_be_able_to_revert: '{{ __("You won\'t be able to revert this!") }}',
  yes_delete_it: '{{ __("Yes, delete it!") }}',
  error: '{{ __("Error") }}',
  success: '{{ __("Success") }}',
  warning: '{{ __("Warning") }}',
  info: '{{ __("Info") }}',
  loading: '{{ __("Loading...") }}',
  saving: '{{ __("Saving...") }}',
  updating: '{{ __("Updating...") }}',
  validation_error: '{{ __("Validation Error") }}',
  an_error_occurred: '{{ __("An error occurred") }}',
  error_loading: '{{ __("Error loading") }}',
  search: '{{ __("Search") }}',
  clear_filters: '{{ __("Clear Filters") }}',
  all: '{{ __("All") }}',
  name: '{{ __("Name") }}',
  gender: '{{ __("Gender") }}',
  male: '{{ __("Male") }}',
  female: '{{ __("Female") }}',
  other: '{{ __("Other") }}',
  department: '{{ __("Department") }}',
  faculty: '{{ __("Faculty") }}',
  category: '{{ __("Category") }}',
  campus_unit: '{{ __("Campus Unit") }}',
  unit: '{{ __("Unit") }}',
  staff: '{{ __("Staff") }}',
  departments: '{{ __("Departments") }}',
  faculties: '{{ __("Faculties") }}',
  categories: '{{ __("Categories") }}',
  select: '{{ __("Select") }}',
  select_option: '{{ __("Select Option") }}',
  select_department: '{{ __("Select Department") }}',
  select_faculty: '{{ __("Select Faculty") }}',
  select_category: '{{ __("Select Category") }}',
  select_gender: '{{ __("Select Gender") }}',
  unassigned: '{{ __("Unassigned") }}',
  na: '{{ __("N/A") }}',
  staff_id_not_set: '{{ __("Staff ID not set") }}',
  // Residents/Staff specific
  add_staff: '{{ __("Add Staff") }}',
  edit_staff: '{{ __("Edit Staff") }}',
  delete_staff: '{{ __("Delete Staff") }}',
  total_staff: '{{ __("Total Staff") }}',
  male_staff: '{{ __("Male Staff") }}',
  female_staff: '{{ __("Female Staff") }}',
  page_title: '{{ __("Staff Management") }}',
  page_header: '{{ __("Staff Management") }}',
  page_description: '{{ __("Manage all staff members and their details") }}',
  // Validation
  required: '{{ __("Required") }}',
  optional: '{{ __("Optional") }}',
  // Misc
  no_data: '{{ __("No data") }}',
  no_results: '{{ __("No results") }}',
  failed_to_load_data: '{{ __("Failed to load data") }}',
  unknown_error: '{{ __("Unknown error") }}',
  server_error: '{{ __("Server error") }}',
  staff_has_been_deleted: '{{ __("Staff has been deleted") }}',
  staff_has_been_saved_successfully: '{{ __("Staff has been saved successfully") }}',
  failed_to_load_statistics: '{{ __("Failed to load statistics") }}',
  units: '{{ __("Units") }}'
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
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.select + ' ' + TRANSLATION.department });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.select + ' ' + TRANSLATION.department });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.departments });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
      });
  },
  populateModalCategories: function() {
    var $select = $('#staff_category_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.select + ' ' + TRANSLATION.category });
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          // Add data-type attribute for each option
          $select.empty().append('<option value="">' + TRANSLATION.select + ' ' + TRANSLATION.category + '</option>');
          response.data.forEach(function(cat) {
            $select.append(`<option value="${cat.id}" data-type="${cat.type ? cat.type.toLowerCase() : ''}">${cat.name}</option>`);
          });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.categories });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
      });
  },
  populateModalFaculties: function() {
    var $select = $('#staff_faculty_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.select + ' ' + TRANSLATION.faculty });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.select + ' ' + TRANSLATION.faculty });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.faculties });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
      });
  },
  populateUnitField: function(categoryType) {
    var $select = $('#staff_unit_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.select + ' ' + TRANSLATION.unit });

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
        Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.select + ' ' + TRANSLATION.unit, triggerChange: true});
      }
    }).fail(function(xhr) {
      Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + (TRANSLATION.units || TRANSLATION.unit) });
      Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
    });
  },
  populateSearchDepartments: function() {
    var $select = $('#search_department_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.all + ' ' + TRANSLATION.departments });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.all + ' ' + TRANSLATION.departments });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.departments });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
      });
  },
  populateSearchCategories: function() {
    var $select = $('#search_category_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.all + ' ' + TRANSLATION.categories });
    ApiService.fetchCategories()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.all + ' ' + TRANSLATION.categories });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.categories });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
      });
  },
  populateSearchFaculties: function() {
    var $select = $('#search_faculty_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.all + ' ' + TRANSLATION.faculties });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.all + ' ' + TRANSLATION.faculties });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.error_loading + ' ' + TRANSLATION.faculties });
        Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred);
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
      $('#staffModal .modal-title').text(TRANSLATION.add + ' ' + TRANSLATION.staff);
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
            $unitLabel.text(TRANSLATION.faculty);
            break;
          case 'administrative':
            $unitLabel.text(TRANSLATION.department);
            break;
          case 'campus':
            $unitLabel.text(TRANSLATION.campus_unit);
            break;
          default:
            $unitLabel.text(TRANSLATION.unit);
        }
        SelectManager.populateUnitField(dataType);
      } else {
        $unitFieldContainer.hide();
        $unitLabel.text(TRANSLATION.unit);
      }
    });
  },
  handleEdit: function() {
    var self = this;
    $(document).on('click', '.editStaffBtn', function() {
      var staffId = $(this).data('id');
      if (!staffId) {
        Utils.showError(TRANSLATION.staff_id_not_set);
        return;
      }
      self.currentStaffId = staffId;
      $('#staffModal .modal-title').text(TRANSLATION.edit + ' ' + TRANSLATION.staff);
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
            Utils.showError(TRANSLATION.failed_to_load_data + ': ' + (response.message || TRANSLATION.unknown_error));
          }
        })
        .fail(function(jqXHR) {
          $('#staffModal').modal('hide');
          var msg = jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : TRANSLATION.server_error;
          Utils.showError(TRANSLATION.failed_to_load_data + ': ' + msg);
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
            $('#view-staff-staff-id').text(staff.id ?? TRANSLATION.na);
            $('#view-staff-name').text(staff.name_en ?? TRANSLATION.na);
            $('#view-staff-email').text(staff.email ?? TRANSLATION.na);
            $('#view-staff-national-id').text(staff.national_id ?? TRANSLATION.na);
            $('#view-staff-gender').text(staff.gender ?? TRANSLATION.na);
            $('#view-staff-category').text(staff.staff_category_type ? staff.staff_category_type.charAt(0).toUpperCase() + staff.staff_category_type.slice(1) : TRANSLATION.na);
            var unitType = TRANSLATION.unassigned;
            var unitName = TRANSLATION.na;
            if (staff.unit && staff.unit.type) {
              unitType = staff.unit.type.charAt(0).toUpperCase() + staff.unit.type.slice(1);
              unitName = staff.unit.name ?? TRANSLATION.na;
            }
            $('#view-staff-unit-type').text(unitType);
            $('#view-staff-unit-name').text(unitName);
            $('#view-staff-notes').text(staff.notes !== null && staff.notes !== undefined ? staff.notes : TRANSLATION.na);
            $('#view-staff-created').text(staff.created_at ? new Date(staff.created_at).toLocaleString() : TRANSLATION.na);
            $('#viewStaffModal').modal('show');
          }
        })
        .fail(function(xhr) {
          $('#viewStaffModal').modal('hide');
          Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred)
        });
    });
  },
  handleDelete: function() {
    $(document).on('click', '.deleteStaffBtn', function() {
      var staffId = $(this).data('id');
      Utils.showConfirmDialog({
        title: TRANSLATION.are_you_sure,
        text: TRANSLATION.you_wont_be_able_to_revert,
        icon: 'warning',
        confirmButtonText: TRANSLATION.yes_delete_it,
        cancelButtonText: TRANSLATION.cancel
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteStaff(staffId)
            .done(function() {
              $('#staff-table').DataTable().ajax.reload(null, false);
              StatsManager.load();
              Utils.showSuccess(TRANSLATION.staff_has_been_deleted);
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred)
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
          Utils.showSuccess(TRANSLATION.staff_has_been_saved_successfully);
        })
        .fail(function(xhr) {
          $('#staffModal').modal('hide');
          Utils.handleAjaxError(xhr, TRANSLATION.an_error_occurred)
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
      Utils.reloadDataTable('#staff-table');
    });
    $('#clearStaffFiltersBtn').on('click', function() {
      $('#search_name, #search_gender, #search_department_id, #search_category_id, #search_faculty_id').val('');
      Utils.reloadDataTable('#staff-table');
    });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: ['staff','staff-male', 'staff-female'],
  onError: TRANSLATION.failed_to_load_statistics
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