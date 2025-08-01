@extends('layouts.home')

@section('title', 'Program Management | AcadOps')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="programs"
                label="Total Programs"
                color="secondary"
                icon="bx bx-book-open"
            />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="with-students"
                label="Programs with Students"
                color="success"
                icon="bx bx-user-check"
            />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="without-students"
                label="Programs without Students"
                color="warning"
                icon="bx bx-user-x"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Programs"
        description="Manage all program records and add new programs using the options on the right."
        icon="bx bx-book-open"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
          <button class="btn btn-primary mx-2" id="addProgramBtn" type="button" data-bs-toggle="modal" data-bs-target="#programModal">
              <i class="bx bx-plus me-1"></i> Add Program
          </button>
          <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#programSearchCollapse" aria-expanded="false" aria-controls="programSearchCollapse">
              <i class="bx bx-filter-alt me-1"></i> Search
          </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedProgramSearch" 
        collapseId="programSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-6">
            <label for="search_name" class="form-label">Program Name:</label>
            <input type="text" class="form-control" id="search_name" placeholder="Program Name">
        </div>
        <div class="col-md-6">
            <label for="faculty_id_search" class="form-label">Faculty:</label>
            <select class="form-control" id="search_faculty_id" name="search_faculty_id">
                <option value="">Select Faculty</option>
                <!-- Options loaded via AJAX -->
            </select>
        </div>
        <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button">
            <i class="bx bx-x"></i> Clear Filters
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable
        :headers="['Name', 'Faculty', 'Students Count', 'Action']"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'students', 'name' => 'students'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        :ajax-url="route('academic.programs.datatable')"
        table-id="programs-table"
        :filter-fields="['search_name', 'search_faculty_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Program Modal --}}
    <x-ui.modal 
        id="programModal"
        title="Add/Edit Program"
        size="lg"
        :scrollable="false"
        class="program-modal"
    >
        <x-slot name="slot">
            <form id="programForm">
                <input type="hidden" id="program_id" name="program_id">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="name_en" class="form-label">Program Name (EN)</label>
                        <input type="text" class="form-control" id="name_en" name="name_en" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="name_ar" class="form-label">Program Name (AR)</label>
                        <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="duration_years" class="form-label">Duration (Years)</label>
                        <select class="form-control" id="duration_years" name="duration_years" required>
                            <option value="">Select Duration</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="faculty_id_modal" class="form-label">Faculty</label>
                        <select class="form-control" id="faculty_id_modal" name="faculty_id" required>
                            <option value="">Select Faculty</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Close
            </button>
            <button type="submit" class="btn btn-primary" id="saveProgramBtn" form="programForm">
                Save
            </button>
        </x-slot>
    </x-ui.modal>

</div>
@endsection

@push('scripts')
<script>
/**
 * Program Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ProgramManager: Handles CRUD for programs
 * - SearchManager: Handles advanced search
 * - ProgramManagementApp: Initializes all managers
 * NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  programs: {
    stats: '{{ route('academic.programs.stats') }}',
    store: '{{ route('academic.programs.store') }}',
    show: '{{ route('academic.programs.show', ':id') }}',
    destroy: '{{ route('academic.programs.destroy', ':id') }}',
    datatable: '{{ route('academic.programs.datatable') }}',
  },
  faculties : {
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
  request(options) {
    return $.ajax(options);
  },
  /**
   * Fetch program statistics
   * @returns {jqXHR}
   */
  fetchProgramStats: function() {
    return ApiService.request({ url: ROUTES.programs.stats, method: 'GET' });
  },
  /**
   * Fetch all faculties
   * @returns {jqXHR}
   */
  fetchFaculties: function() {
    return ApiService.request({ url: ROUTES.faculties.all, method: 'GET' });
  },
  /**
   * Fetch a single program by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchProgram: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.programs.show, id), method: 'GET' });
  },
  /**
   * Save (create or update) a program
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveProgram: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.programs.show, id) : ROUTES.programs.store;
    var method = id ? 'PUT' : 'POST';
    return ApiService.request({ url: url, method: method, data: data });
  },
  /**
   * Delete a program by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteProgram: function(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.programs.destroy, id), method: 'DELETE' });
  }
};

// ===========================
// DROPDOWN MANAGER
// ===========================
var DropdownManager = {
  /**
   * Load faculties into a select dropdown
   * @param {string} selector
   * @param {string|number|null} selectedId
   * @returns {void}
   */
  loadFaculties: function(selector, selectedId) {
    ApiService.fetchFaculties()
      .done(function(response) {
        Utils.populateSelect(selector, response.data, {
          valueField: 'id',
          textField: 'name',
          placeholder: 'Select Faculty',
          selected: selectedId,
          includePlaceholder: true
        });
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'An error occurred.');
      });
  },
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchProgramStats,
  statsKeys: ['programs', 'with-students', 'without-students'],
  onError: 'Failed to load program statistics'
});

// ===========================
// PROGRAM MANAGER
// ===========================
var ProgramManager = {
  /**
   * Bind add program button
   */
  handleAdd: function() {
    $('#addProgramBtn').on('click', function() {
      $('#programForm')[0].reset();
      $('#program_id').val('');
      $('#programModal .modal-title').text('Add Program');
      $('#saveProgramBtn').text('Save');
      DropdownManager.loadFaculties('#faculty_id_modal');
      $('#programModal').modal('show');
    });
  },
  /**
   * Bind program form submit
   */
  handleFormSubmit: function() {
    $('#programForm').on('submit', function(e) {
      e.preventDefault();
      var programId = $('#program_id').val();
      var formData = $(this).serialize();
      var $submitBtn = $('#saveProgramBtn');
      var originalText = $submitBtn.text();
      Utils.setLoadingState($submitBtn, true, { loadingText: programId ? 'Updating...' : 'Saving...' });
      ApiService.saveProgram(formData, programId || null)
        .done(function() {
          $('#programModal').modal('hide');
          Utils.reloadDataTable('#programs-table', null, true);
          Utils.showSuccess('Program has been saved successfully.');
          StatsManager.refresh();
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, 'An error occurred. Please check your input.');
        })
        .always(function() {
          Utils.setLoadingState($submitBtn, false, { normalText: originalText });
        });
    });
  },
  /**
   * Bind edit program button
   */
  handleEdit: function() {
    $(document).on('click', '.editProgramBtn', function() {
      var programId = $(this).data('id');
      ApiService.fetchProgram(programId)
        .done(function(program) {
          var prog = program.data ? program.data : program;
          $('#program_id').val(prog.id);
          $('#name_en').val(prog.name_en);
          $('#name_ar').val(prog.name_ar);
          $('#duration_years').val(prog.duration_years);
          DropdownManager.loadFaculties('#faculty_id_modal', prog.faculty_id);
          $('#programModal .modal-title').text('Edit Program');
          $('#saveProgramBtn').text('Update');
          $('#programModal').modal('show');
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, 'Failed to fetch program data.');
        });
    });
  },
  /**
   * Bind delete program button
   */
  handleDelete: function() {
    $(document).on('click', '.deleteProgramBtn', function() {
      var programId = $(this).data('id');
      Utils.showConfirmDialog({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        confirmButtonText: 'Yes, delete it!'
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteProgram(programId)
            .done(function() {
              Utils.reloadDataTable('#programs-table', null, true);
              Utils.showSuccess('Program has been deleted.');
              StatsManager.refresh();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, 'Failed to delete program.');
            });
        }
      });
    });
  },
  /**
   * Initialize all program manager handlers
   */
  init: function() {
    this.handleAdd();
    this.handleFormSubmit();
    this.handleEdit();
    this.handleDelete();
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
    DropdownManager.loadFaculties('#search_faculty_id');
  },
  /**
   * Bind search and clear events
   */
  bindEvents: function() {
    $('#clearFiltersBtn').on('click', function() {
      $('#search_name, #search_faculty_id').val('').trigger('change');
      Utils.reloadDataTable('#programs-table');
    });
    $('#search_name, #search_faculty_id').on('keyup change', function() {
      Utils.reloadDataTable('#programs-table');
    });
  }
};


// ===========================
// MAIN APP INITIALIZER
// ===========================
var ProgramManagementApp = {
  /**
   * Initialize all managers
   */
  init: function() {
    StatsManager.init();
    ProgramManager.init();
    SearchManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  ProgramManagementApp.init();
});
</script>
@endpush 