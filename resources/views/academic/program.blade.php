@extends('layouts.home')

@section('title', __('programs.page.title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="programs"
                label="{{ __('programs.stats.total_programs') }}"
                color="secondary"
                icon="bx bx-book-open"
            />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="with-students"
                label="{{ __('programs.stats.with_students') }}"
                color="success"
                icon="bx bx-user-check"
            />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 
                id="without-students"
                label="{{ __('programs.stats.without_students') }}"
                color="warning"
                icon="bx bx-user-x"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="{{ __('programs.page.header.title') }}"
        description="{{ __('programs.page.header.description') }}"
        icon="bx bx-book-open"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
          <button class="btn btn-primary mx-2" id="addProgramBtn" type="button" data-bs-toggle="modal" data-bs-target="#programModal">
              <i class="bx bx-plus me-1"></i> {{ __('programs.buttons.add_program') }}
          </button>
          <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#programSearchCollapse" aria-expanded="false" aria-controls="programSearchCollapse">
              <i class="bx bx-filter-alt me-1"></i> {{ __('programs.buttons.search') }}
          </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="{{ __('programs.search.title') }}" 
        formId="advancedProgramSearch" 
        collapseId="programSearchCollapse"
        :collapsed="false"
    >
        <div class="col-md-6">
            <label for="search_name" class="form-label">{{ __('programs.search.labels.program_name') }}:</label>
            <input type="text" class="form-control" id="search_name" placeholder="{{ __('programs.search.placeholders.program_name') }}">
        </div>
        <div class="col-md-6">
            <label for="faculty_id_search" class="form-label">{{ __('programs.search.labels.faculty') }}:</label>
            <select class="form-control" id="search_faculty" name="search_faculty_id">
                <option value="">{{ __('programs.search.placeholders.select_faculty') }}</option>
                <!-- Options loaded via AJAX -->
            </select>
        </div>
        <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('programs.buttons.clear_filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table
        :headers="[
            __('programs.table.headers.name'),
            __('programs.table.headers.faculty'),
            __('programs.table.headers.students_count'),
            __('programs.table.headers.action')
        ]"
        :columns="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'students', 'name' => 'students'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        :ajax-url="route('academic.programs.datatable')"
        table-id="programs-table"
        :filter-fields="['search_name', 'search_faculty']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Program Modal --}}
    <x-ui.modal 
        id="programModal"
        title="{{ __('programs.modal.title') }}"
        size="lg"
        :scrollable="false"
        class="program-modal"
    >
        <x-slot name="slot">
            <form id="programForm">
                <input type="hidden" id="program_id" name="program_id">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="name_en" class="form-label">{{ __('programs.form.labels.name_en') }}</label>
                        <input type="text" class="form-control" id="name_en" name="name_en" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="name_ar" class="form-label">{{ __('programs.form.labels.name_ar') }}</label>
                        <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="duration_years" class="form-label">{{ __('programs.form.labels.duration_years') }}</label>
                        <select class="form-control" id="duration_years" name="duration_years" required>
                            <option value="">{{ __('programs.form.placeholders.select_duration') }}</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="faculty" class="form-label">{{ __('programs.form.labels.faculty') }}</label>
                        <select class="form-control" id="faculty" name="faculty_id" required>
                            <option value="">{{ __('programs.form.placeholders.select_faculty') }}</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                {{ __('programs.buttons.close') }}
            </button>
            <button type="submit" class="btn btn-primary" id="saveProgramBtn" form="programForm">
                {{ __('programs.buttons.save') }}
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
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
  stats: {
    error: '{{ __('programs.messages.stats_error') }}'
  },
  program: {
    saveSuccess: '{{ __('programs.messages.save_success') }}',
    deleteSuccess: '{{ __('programs.messages.delete_success') }}',
    fetchError: '{{ __('programs.messages.fetch_error') }}',
    deleteError: '{{ __('programs.messages.delete_error') }}',
    inputError: '{{ __('programs.messages.input_error') }}'
  },
  buttons: {
    saving: '{{ __('programs.buttons.saving') }}',
    updating: '{{ __('programs.buttons.updating') }}',
    save: '{{ __('programs.buttons.save') }}',
    update: '{{ __('programs.buttons.update') }}'
  },
  modal: {
    addTitle: '{{ __('programs.modal.add_title') }}',
    editTitle: '{{ __('programs.modal.edit_title') }}'
  },
  confirm: {
    title: '{{ __('programs.confirm.title') }}',
    text: '{{ __('programs.confirm.text') }}',
    confirmButton: '{{ __('programs.confirm.confirm_button') }}'
  },
  dropdown: {
    selectFaculty: '{{ __('programs.dropdown.select_faculty') }}',
    error: '{{ __('programs.messages.dropdown_error') }}'
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
            '#search_faculty': { placeholder: TRANSLATION.dropdown.selectFaculty }, 
        },
        modal: {
            '#faculty': { placeholder: TRANSLATION.dropdown.selectFaculty, dropdownParent: $('#programModal') }, 
        }
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
            $(selector).val('').trigger('change');
        });
    },

    /**
     * Reset modal Select2 elements
     */
    resetModalSelect2: function() {
        this.clearSelect2(['#faculty']);
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function() {
        this.clearSelect2(['#search_faculty']);
    }
};

// ===========================
// DROPDOWN MANAGER
// ===========================
var DropdownManager = {
  loadFaculties: function(selector, selectedId) {
    ApiService.fetchFaculties()
      .done(function(response) {
        Utils.populateSelect(selector, response.data, {
          valueField: 'id',
          textField: 'name',
          placeholder: TRANSLATION.dropdown.selectFaculty,
          selected: selectedId,
          includePlaceholder: true
        });
        // Initialize Select2 after populating
        if (selector === '#search_faculty') {
          Select2Manager.initSearchSelect2();
        } else if (selector === '#faculty') {
          Select2Manager.initModalSelect2();
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, TRANSLATION.dropdown.error);
      });
  },
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchProgramStats,
  statsKeys: ['programs', 'with-students', 'without-students'],
  onError: TRANSLATION.stats.error
});

// ===========================
// PROGRAM MANAGER
// ===========================
var ProgramManager = {
  handleAdd: function() {
    $('#addProgramBtn').on('click', function() {
      $('#programForm')[0].reset();
      $('#program_id').val('');
      $('#programModal .modal-title').text(TRANSLATION.modal.addTitle);
      $('#saveProgramBtn').text(TRANSLATION.buttons.save);
      Select2Manager.resetModalSelect2();
      DropdownManager.loadFaculties('#faculty');
      $('#programModal').modal('show');
    });
  },
  handleFormSubmit: function() {
    $('#programForm').on('submit', function(e) {
      e.preventDefault();
      var programId = $('#program_id').val();
      var formData = $(this).serialize();
      var $submitBtn = $('#saveProgramBtn');
      var originalText = $submitBtn.text();
      Utils.setLoadingState($submitBtn, true, { 
        loadingText: programId ? TRANSLATION.buttons.updating : TRANSLATION.buttons.saving
      });
      ApiService.saveProgram(formData, programId || null)
        .done(function() {
          $('#programModal').modal('hide');
          Utils.reloadDataTable('#programs-table', null, true);
          Utils.showSuccess(TRANSLATION.program.saveSuccess);
          StatsManager.refresh();
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, TRANSLATION.program.inputError);
        })
        .always(function() {
          Utils.setLoadingState($submitBtn, false, { normalText: originalText });
        });
    });
  },
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
          DropdownManager.loadFaculties('#faculty', prog.faculty_id);
          $('#programModal .modal-title').text(TRANSLATION.modal.editTitle);
          $('#saveProgramBtn').text(TRANSLATION.buttons.update);
          $('#programModal').modal('show');
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, TRANSLATION.program.fetchError);
        });
    });
  },
  handleDelete: function() {
    $(document).on('click', '.deleteProgramBtn', function() {
      console.log('Delete button clicked');
      var programId = $(this).data('id');
      Utils.showConfirmDialog({
        title: TRANSLATION.confirm.title,
        text: TRANSLATION.confirm.text,
        icon: 'warning',
        confirmButtonText: TRANSLATION.confirm.confirmButton
      }).then(function(result) {
        if (result.isConfirmed) {
          ApiService.deleteProgram(programId)
            .done(function() {
              Utils.reloadDataTable('#programs-table', null, true);
              Utils.showSuccess(TRANSLATION.program.deleteSuccess);
              StatsManager.refresh();
            })
            .fail(function(xhr) {
              Utils.handleAjaxError(xhr, TRANSLATION.program.deleteError);
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
    DropdownManager.loadFaculties('#search_faculty');
  },
  /**
   * Bind search and clear events
   */
  bindEvents: function() {
    $('#clearFiltersBtn').on('click', function() {
      $('#search_name').val('');
      Select2Manager.resetSearchSelect2();
      Utils.reloadDataTable('#programs-table');
    });
    $('#search_name, #search_faculty').on('keyup change', function() {
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