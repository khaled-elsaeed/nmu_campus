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
                color="primary"
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
        <button class="btn btn-primary mx-2" id="addProgramBtn" type="button" data-bs-toggle="modal" data-bs-target="#programModal">
            <i class="bx bx-plus me-1"></i> Add Program
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#programSearchCollapse" aria-expanded="false" aria-controls="programSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> Search
        </button>
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
            <select class="form-control" id="faculty_id_search" name="faculty_id">
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
        :filter-fields="['search_name', 'faculty_id']"
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
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - ProgramManager: Handles CRUD for programs
 * - SearchManager: Handles advanced search
 * - ProgramManagementApp: Initializes all managers
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
// CONSTANTS AND CONFIGURATION
// ===========================
var Utils = {
  /**
   * Show a success toast message
   * @param {string} message
   */
  showSuccess: function(message) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500, timerProgressBar: true });
  },
  /**
   * Show an error alert
   * @param {string} message
   */
  showError: function(message) {
    Swal.fire({ title: 'Error', html: message, icon: 'error' });
  },
  /**
   * Toggle loading state for a stat card
   * @param {string} elementId
   * @param {boolean} isLoading
   */
  toggleLoadingState: function(elementId, isLoading) {
    var $value = $('#' + elementId + '-value');
    var $loader = $('#' + elementId + '-loader');
    var $updated = $('#' + elementId + '-last-updated');
    var $updatedLoader = $('#' + elementId + '-last-updated-loader');
    if (isLoading) {
      $value.addClass('d-none');
      $loader.removeClass('d-none');
      $updated.addClass('d-none');
      $updatedLoader.removeClass('d-none');
    } else {
      $value.removeClass('d-none');
      $loader.addClass('d-none');
      $updated.removeClass('d-none');
      $updatedLoader.addClass('d-none');
    }
  },
  /**
   * Replace :id in a route string
   * @param {string} route
   * @param {string|number} id
   * @returns {string}
   */
  replaceRouteId: function(route, id) {
    return route.replace(':id', id);
  }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  /**
   * Generic AJAX request with CSRF
   * @param {object} options
   * @returns {jqXHR}
   */
  request: function(options) {
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    return $.ajax(options);
  },
  /**
   * Fetch program statistics
   * @returns {jqXHR}
   */
  fetchProgramStats: function() {
    return this.request({ url: ROUTES.programs.stats, method: 'GET' });
  },
  /**
   * Fetch all faculties
   * @returns {jqXHR}
   */
  fetchFaculties: function() {
    return this.request({ url: ROUTES.faculties.all, method: 'GET' });
  },
  /**
   * Fetch a single program by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchProgram: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.programs.show, id), method: 'GET' });
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
    return this.request({ url: url, method: method, data: data });
  },
  /**
   * Delete a program by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteProgram: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.programs.destroy, id), method: 'DELETE' });
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
        DropdownManager.populateFacultiesModalSelect(selector, response.data, selectedId);
      })
      .fail(function() {
        Utils.showError('Failed to load faculties');
      });
  },

  /**
   * Populate faculties into the select element
   * @param {string} selector
   * @param {Array} faculties
   * @param {string|number|null} selectedId
   */
  populateFacultiesModalSelect: function(selector, faculties, selectedId) {
    var $select = $(selector);
    $select.empty().append('<option value="">Select Faculty</option>');
    faculties.forEach(function(faculty) {
      $select.append($('<option>', { value: faculty.id, text: faculty.name }));
    });
    if (selectedId) {
      $select.val(selectedId);
    }
    $select.trigger('change');
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
    ApiService.fetchProgramStats()
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
  /**
   * Handle successful stats fetch
   * @param {object} response
   */
  handleSuccess: function(response) {
    if (response.success !== false) {
      let stats = response.data;
      this.updateStatElement('programs', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('with-students', stats.withStudents.count, stats.withStudents.lastUpdateTime);
      this.updateStatElement('without-students', stats.withoutStudents.count, stats.withoutStudents.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load program statistics');
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
    ['programs', 'with-students', 'without-students'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['programs', 'with-students', 'without-students'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

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
      $submitBtn.prop('disabled', true).text('Saving...');
      ApiService.saveProgram(formData, programId || null)
        .done(function() {
          $('#programModal').modal('hide');
          $('#programs-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Program has been saved successfully.');
          StatsManager.load();
        })
        .fail(function(xhr) {
          $('#programModal').modal('hide');
          var message = xhr.responseJSON?.message || 'An error occurred. Please check your input.';
          Utils.showError(message);
        })
        .always(function() {
          $submitBtn.prop('disabled', false).text(originalText);
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
        .fail(function() {
          Utils.showError('Failed to fetch program data.');
        });
    });
  },
  /**
   * Bind delete program button
   */
  handleDelete: function() {
    $(document).on('click', '.deleteProgramBtn', function() {
      var programId = $(this).data('id');
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
          ApiService.deleteProgram(programId)
            .done(function() {
              $('#programs-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Program has been deleted.');
              StatsManager.load();
            })
            .fail(function(xhr) {
              var message = xhr.responseJSON?.message || 'Failed to delete program.';
              Utils.showError(message);
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
    DropdownManager.loadFaculties('#faculty_id_search');
  },
  /**
   * Bind search and clear events
   */
  bindEvents: function() {
    $('#clearFiltersBtn').on('click', function() {
      $('#search_name, #faculty_id_search').val('').trigger('change');
      $('#programs-table').DataTable().ajax.reload();
    });
    $('#search_name, #faculty_id_search').on('keyup change', function() {
      $('#programs-table').DataTable().ajax.reload();
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