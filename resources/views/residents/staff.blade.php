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
        <label for="search_department" class="form-label">{{ __('Department') }}:</label>
        <select class="form-control" id="search_department" name="department_id">
            <option value="">{{ __('All Departments') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_type" class="form-label">{{ __('Type') }}:</label>
        <select class="form-control" id="search_type" name="type">
            <option value="">{{ __('All Types') }}</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="search_faculty" class="form-label">{{ __('Faculty') }}:</label>
        <select class="form-control" id="search_faculty" name="search_faculty">
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
            __('Unit Type'), 
            __('Unit Name'), 
            __('Notes'), 
            __('Actions')
        ]"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'unit_type', 'name' => 'unit_type'],
            ['data' => 'unit_name', 'name' => 'unit_name'],
            ['data' => 'notes', 'name' => 'notes'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.staff.datatable')"
        :table-id="'staff-table'"
        :filter-fields="['search_name','search_gender','search_type','search_department','search_faculty']"
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
            <label for="staff_unit_type" class="form-label">{{ __('Unit Type') }}</label>
            <select id="staff_unit_type" name="staff_unit_type" class="form-control" required></select>
          </div>
          <div class="col-md-6 mb-3" id="unit_field_container" style="display:none;">
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
                    <label class="form-label fw-bold">{{ __('Unit Type') }}:</label>
                    <p id="view-staff-unit-type" class="mb-0"></p>
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
  faculties: {
    all: '{{ route('academic.faculties.all') }}'
  }
};

// ===========================
// Translations (cleanup & fallback)
// ===========================
var TRANSLATION = {
  confirm: {
    delete :{
      title: @json(__('Confirm Delete')),
      message: @json(__('Are you sure you want to delete this staff member?')),
      confirmButtonText: @json(__('Yes, delete it')),
    }
  },
  placeholders: {
    selectDepartment: @json(__('Select Department')),
    selectFaculty: @json(__('Select Faculty')),
    selectType: @json(__('Select Type')),
    selectGender: @json(__('Select Gender')),
    selectUnit: @json(__('Select Unit')),
    allDepartments: @json(__('All Departments')),
    allFaculties: @json(__('All Faculties')),
    allTypes: @json(__('All Types')),
    all: @json(__('All'))
  },
  gender: {
    male: @json(__('Male')),
    female: @json(__('Female')),
    other: @json(__('Other'))
  },
  staff: {
    idNotSet: @json(__('Staff ID not set')),
    add: @json(__('Add Staff')),
    edit: @json(__('Edit Staff')),
    delete: @json(__('Delete Staff')),
    hasBeenDeleted: @json(__('Staff has been deleted')),
    hasBeenSaved: @json(__('Staff has been saved successfully'))
  },
  labels: {
    name: @json(__('Name')),
    gender: @json(__('Gender')),
    department: @json(__('Department')),
    faculty: @json(__('Faculty')),
    campusUnit: @json(__('Campus Unit')),
  },
  misc: {
    close: @json(__('Close')),
    save: @json(__('Save')),
    cancel: @json(__('Cancel')),
    error: @json(__('Error')),
    success: @json(__('Success')),
    warning: @json(__('Warning')),
    info: @json(__('Info')),
    loading: @json(__('Loading...')),
    anErrorOccurred: @json(__('An error occurred')),
    failedToLoadStatistics: @json(__('Failed to load statistics')),
    na: @json(__('N/A'))
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
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.selectDepartment });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.placeholders.selectDepartment });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  populateModalTypes: function() {
    var $select = $('#staff_unit_type');
    $select.empty().append('<option value="">' + TRANSLATION.placeholders.selectType + '</option>');
    [
      { id: 'faculty', name: TRANSLATION.labels.faculty, type: 'faculty' },
      { id: 'administrative', name: TRANSLATION.labels.department, type: 'administrative' },
      { id: 'campus', name: TRANSLATION.labels.campusUnit, type: 'campus' }
    ].forEach(function(cat) {
      $select.append(`<option value="${cat.id}" data-type="${cat.type}">${cat.name}</option>`);
    });
  },
  populateModalFaculties: function() {
    var $select = $('#staff_faculty_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.selectFaculty });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.placeholders.selectFaculty });
        }
      })
      .fail(function(xhr) {
        $('#staffModal').modal('hide');
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  populateUnitField: function(type) {
    var $select = $('#staff_unit_id');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.selectUnit });

    var promise;
    if (type === 'faculty') {
      promise = ApiService.fetchFaculties();
    } else if (type === 'administrative') {
      promise = ApiService.fetchDepartments();
    } else if (type === 'campus') {
      promise = ApiService.fetchCampusUnits();
    } else {
      return $.Deferred().resolve();
    }

    return promise.done(function(response) {
      if (response.success) {
        Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.labels.unit, triggerChange: true});
      }
    }).fail(function(xhr) {
      Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
      Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
    });
  },
  populateSearchDepartments: function() {
    var $select = $('#search_department');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
    ApiService.fetchDepartments()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.placeholders.selectDepartment });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  populateSearchTypes: function() {
    var $select = $('#search_type');
    $select.empty().append('<option value="">' + TRANSLATION.placeholders.selectType + '</option>');
    [
      { id: 'faculty', name: TRANSLATION.labels.faculty },
      { id: 'administrative', name: TRANSLATION.labels.department },
      { id: 'campus', name: TRANSLATION.labels.campusUnit }
    ].forEach(function(type) {
      $select.append(`<option value="${type.id}">${type.name}</option>`);
    });
  },
  populateSearchFaculties: function() {
    var $select = $('#search_faculty');
    Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.selectFaculty });
    ApiService.fetchFaculties()
      .done(function(response) {
        if (response.success) {
          Utils.populateSelect($select, response.data, { valueField: 'id', textField: 'name', placeholder: TRANSLATION.placeholders.all });
        }
      })
      .fail(function(xhr) {
        Utils.populateSelect($select, [], { placeholder: TRANSLATION.placeholders.all });
        Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
      });
  },
  init: function() {
    this.populateModalDepartments();
    this.populateModalTypes();
    this.populateModalFaculties();
    this.populateSearchDepartments();
    this.populateSearchTypes();
    this.populateSearchFaculties();
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
            '#search_gender': {placeholder: TRANSLATION.placeholders.selectGender} ,
            '#search_type': {placeholder: TRANSLATION.placeholders.selectType},
            '#search_department': {placeholder: TRANSLATION.placeholders.selectDepartment},
            '#search_faculty': {placeholder: TRANSLATION.placeholders.selectFaculty}
        },
        modal: {
            '#staff_gender': {placeholder: TRANSLATION.placeholders.selectGender} ,
            '#staff_unit_type': {placeholder: TRANSLATION.placeholders.selectType},
            '#staff_unit_id': {placeholder: TRANSLATION.placeholders.selectDepartment},
        },
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
    resetSearchSelect2: function() {
        this.clearSelect2(Object.keys(this.config.search));
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
      $('#staffModal .modal-title').text(TRANSLATION.staff.add);
      $('#staffForm')[0].reset();
      var $submitBtn = $('#staffForm button[type="submit"]');
      Utils.setLoadingState($submitBtn, false, {
        loadingText: TRANSLATION.misc.loading || 'Loading...',
        normalText: TRANSLATION.misc.save || 'Save',
      });
    });
  },
  handleTypeChange: function() {
    $(document).on('change', '#staff_unit_type', function() {
      var value = $(this).find('option:selected').val();
      var dataType = $(this).find('option:selected').data('type');
      var $unitFieldContainer = $('#unit_field_container');
      var $unitLabel = $('label[for="staff_unit_id"]');

      if (value === 'administrative' || value === 'faculty' || value === 'campus') {
        $unitFieldContainer.show();
        switch (value) {
          case 'faculty':
            $unitLabel.text(TRANSLATION.labels.faculty);
            break;
          case 'administrative':
            $unitLabel.text(TRANSLATION.labels.department);
            break;
          case 'campus':
            $unitLabel.text(TRANSLATION.labels.campusUnit);
            break;
          default:
            $unitLabel.text(TRANSLATION.labels.unit);
        }
        SelectManager.populateUnitField(dataType);
      } else {
        $unitFieldContainer.hide();
        $unitLabel.text(TRANSLATION.labels.unit);
      }
    });
  },
  handleEdit: function() {
    var self = this;
    $(document).on('click', '.editStaffBtn', function() {
      var staffId = $(this).data('id');
      if (!staffId) {
        Utils.showError(TRANSLATION.staff.idNotSet);
        return;
      }
      self.currentStaffId = staffId;
  $('#staffModal .modal-title').text(TRANSLATION.staff.edit);
      var $submitBtn = $('#staffForm button[type="submit"]');
      Utils.setLoadingState($submitBtn, true, {
        loadingText: TRANSLATION.misc.loading || 'Loading...',
        normalText: TRANSLATION.misc.save || 'Save',
      });
      ApiService.fetchStaff(staffId)
        .done(function(response) {
          if (response.success && response.data) {
            var staff = response.data;
            $('#staff_name_en').val(staff.name_en || '');
            $('#staff_name_ar').val(staff.name_ar || '');
            $('#staff_email').val(staff.email || '');
            $('#staff_national_id').val(staff.national_id || '');
            $('#staff_gender').val(staff.gender || '');
            $('#staff_unit_type').val(staff.unit_type || '').change();
            var dataType = $('#staff_unit_type').find('option:selected').data('type') || '';
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
            Utils.showError(response.message);
          }
        })
        .fail(function(xhr) {
          $('#staffModal').modal('hide');
          Utils.handleAjaxError(xhr,xhr.responseJSON?.message);
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
            $('#view-staff-staff-id').text(staff.id ?? TRANSLATION.misc.na);
            $('#view-staff-name').text(staff.name_en ?? TRANSLATION.misc.na);
            $('#view-staff-email').text(staff.email ?? TRANSLATION.misc.na);
            $('#view-staff-national-id').text(staff.national_id ?? TRANSLATION.misc.na);
            $('#view-staff-gender').text(staff.gender ?? TRANSLATION.misc.na);
            $('#view-staff-type').text(staff.unit_type ? staff.unit_type.charAt(0).toUpperCase() + staff.unit_type.slice(1) : TRANSLATION.misc.na);
            $('#view-staff-unit-name').text(unitName);
            $('#view-staff-notes').text(staff.notes !== null && staff.notes !== undefined ? staff.notes : TRANSLATION.misc.na);
            $('#view-staff-created').text(staff.created_at ? new Date(staff.created_at).toLocaleString() : TRANSLATION.misc.na);
            $('#viewStaffModal').modal('show');
          }
        })
        .fail(function(xhr) {
          $('#viewStaffModal').modal('hide');
          Utils.handleAjaxError(xhr, TRANSLATION.misc.anErrorOccurred)
        });
    });
  },
  handleDelete: function() {
    $(document).on('click', '.deleteStaffBtn', function() {
      var staffId = $(this).data('id');
      Utils.showConfirmDialog({
        title: TRANSLATION.confirm.delete.title,
        text: TRANSLATION.confirm.delete.message,
        icon: 'warning',
        confirmButtonText: TRANSLATION.confirm.delete.confirmButtonText,
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteStaff(staffId)
            .done(function() {
              $('#staff-table').DataTable().ajax.reload(null, false);
              StatsManager.load();
              Utils.showSuccess(TRANSLATION.staff.hasBeenDeleted);
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, TRANSLATION.misc.anErrorOccurred)
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
      var btnSelector = '#staffForm button[type="submit"]';
      var isEdit = !!self.currentStaffId;
      var options = {
        loadingText: isEdit ? (TRANSLATION.misc.updating || 'Updating...') : (TRANSLATION.misc.saving || 'Saving...'),
        normalText: isEdit ? (TRANSLATION.misc.update || 'Update') : (TRANSLATION.misc.save || 'Save'),
        loadingIcon: 'bx bx-loader-alt bx-spin me-1',
        normalIcon: 'bx bx-save me-1'
      };
      Utils.setLoadingState(btnSelector, true, options);
      ApiService.saveStaff(formData, self.currentStaffId)
        .done(function() {
          $('#staffModal').modal('hide');
          $('#staff-table').DataTable().ajax.reload(null, false);
          StatsManager.load();
          Utils.showSuccess(TRANSLATION.staff.hasBeenSaved);
        })
        .fail(function(xhr) {
          $('#staffModal').modal('hide');
          Utils.handleAjaxError(xhr, TRANSLATION.misc.anErrorOccurred)
        })
        .always(function() {
          Utils.setLoadingState(btnSelector, false, options);
        });
    });
  },
  init: function() {
    this.handleAdd();
    this.handleTypeChange();
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
    $('#search_name, #search_gender, #search_department, #search_type, #search_faculty').on('keyup change', function() {
      Utils.reloadDataTable('#staff-table');
    });
    $('#clearStaffFiltersBtn').on('click', function() {
      $('#search_name, #search_gender, #search_department, #search_type, #search_faculty').val('');
      Utils.reloadDataTable('#staff-table');
      Select2Manager.resetSearchSelect2();
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
    Select2Manager.initAll();
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