@extends('layouts.home')

@section('title', __('faculties.page.title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    
  {{-- ===== STATISTICS CARDS ===== --}}
  <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="faculties"
              label="{{ __('faculties.stats.total_faculties') }}"
              color="secondary"
              icon="bx bx-building"
          />
      </div>
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="with-programs"
              label="{{ __('faculties.stats.with_programs') }}"
              color="success"
              icon="bx bx-check-circle"
          />
      </div>
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="without-programs"
              label="{{ __('faculties.stats.without_programs') }}"
              color="warning"
              icon="bx bx-x-circle"
          />
      </div>
  </div>

  {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
  <x-ui.page-header 
      title="{{ __('faculties.page.header.title') }}"
      description="{{ __('faculties.page.header.description') }}"
      icon="bx bx-building"
  >
  <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-primary mx-2" id="addFacultyBtn" type="button" data-bs-toggle="modal" data-bs-target="#facultyModal">
            <i class="bx bx-plus me-1"></i> {{ __('faculties.buttons.add_faculty') }}
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#facultySearchCollapse" aria-expanded="false" aria-controls="facultySearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> {{ __('faculties.buttons.search') }}
        </button>
    </div>
  </x-ui.page-header>

  {{-- ===== ADVANCED SEARCH SECTION ===== --}}
  <x-ui.advanced-search 
      title="{{ __('faculties.search.title') }}" 
      formId="advancedFacultySearch" 
      collapseId="facultySearchCollapse"
      :collapsed="false"
  >
      <div class="col-md-4">
          <label for="search_name" class="form-label">{{ __('faculties.search.labels.faculty_name') }}:</label>
          <input type="text" class="form-control" id="search_name" name="search_name" placeholder="{{ __('faculties.search.placeholders.faculty_name') }}">
      </div>
      <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button">
          <i class="bx bx-x"></i> {{ __('faculties.buttons.clear_filters') }}
      </button>
  </x-ui.advanced-search>

  {{-- ===== DATA TABLE ===== --}}
  <x-ui.datatable.table
      :headers="[
          __('faculties.table.headers.name'),
          __('faculties.table.headers.programs'),
          __('faculties.table.headers.students'),
          __('faculties.table.headers.staff'),
          __('faculties.table.headers.action')
      ]"
      :columns="[
          ['data' => 'name', 'name' => 'name'],
          ['data' => 'programs', 'name' => 'programs'],
          ['data' => 'students', 'name' => 'students'],
          ['data' => 'staff', 'name' => 'staff'],
          ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
      ]"
      :ajax-url="route('academic.faculties.datatable')"
      table-id="faculties-table"
      :filter-fields="['search_name']"
  />

  {{-- ===== MODALS SECTION ===== --}}
  
  {{-- Add/Edit Faculty Modal --}}
  <x-ui.modal 
      id="facultyModal"
      title="{{ __('faculties.modal.title') }}"
      size="md"
      :scrollable="false"
      class="faculty-modal"
  >
      <x-slot name="slot">
          <form id="facultyForm">
              <input type="hidden" id="faculty_id" name="faculty_id">
              <div class="row">
                  <div class="col-md-12 mb-3">
                      <label for="name_en" class="form-label">{{ __('faculties.form.labels.name_en') }}</label>
                      <input type="text" class="form-control" id="name_en" name="name_en" required>
                  </div>
                  <div class="col-md-12 mb-3">
                      <label for="name_ar" class="form-label">{{ __('faculties.form.labels.name_ar') }}</label>
                      <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                  </div>
              </div>
          </form>
      </x-slot>
      <x-slot name="footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              {{ __('faculties.buttons.close') }}
          </button>
          <button type="submit" class="btn btn-primary" id="saveFacultyBtn" form="facultyForm">
              {{ __('faculties.buttons.save') }}
          </button>
      </x-slot>
  </x-ui.modal>
</div>
@endsection

@push('scripts')
<script>
/**
 * Faculty Management Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - FacultyManager: Handles CRUD for faculties
 * - SearchManager: Handles advanced search
 * - FacultyManagementApp: Initializes all managers
 *   NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
const ROUTES = {
  faculties: {
    stats: '{{ route('academic.faculties.stats') }}',
    store: '{{ route('academic.faculties.store') }}',
    show: '{{ route('academic.faculties.show', ':id') }}',
    destroy: '{{ route('academic.faculties.destroy', ':id') }}',
    datatable: '{{ route('academic.faculties.datatable') }}'
  }
};

// ===========================
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
  stats: {
    error: '{{ __('faculties.messages.stats_error') }}'
  },
  faculty: {
    saveSuccess: '{{ __('faculties.messages.save_success') }}',
    deleteSuccess: '{{ __('faculties.messages.delete_success') }}',
    fetchError: '{{ __('faculties.messages.fetch_error') }}',
    deleteError: '{{ __('faculties.messages.delete_error') }}',
    inputError: '{{ __('faculties.messages.input_error') }}'
  },
  buttons: {
    saving: '{{ __('faculties.buttons.saving') }}',
    updating: '{{ __('faculties.buttons.updating') }}',
    save: '{{ __('faculties.buttons.save') }}',
    update: '{{ __('faculties.buttons.update') }}'
  },
  modal: {
    addTitle: '{{ __('faculties.modal.add_title') }}',
    editTitle: '{{ __('faculties.modal.edit_title') }}'
  },
  confirm: {
    title: '{{ __('faculties.confirm.title') }}',
    text: '{{ __('faculties.confirm.text') }}',
    confirmButton: '{{ __('faculties.confirm.confirm_button') }}'
  }
};

// ===========================
// API SERVICE
// ===========================
const ApiService = {
  /**
   * Generic AJAX request
   * @param {object} options
   * @returns {jqXHR}
   */
  request(options) {
    return $.ajax(options);
  },
  /**
   * Fetch faculty statistics
   * @returns {jqXHR}
   */
  fetchFacultyStats() {
    return ApiService.request({ url: ROUTES.faculties.stats, method: 'GET' });
  },
  /**
   * Fetch a single faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchFaculty(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.faculties.show, id), method: 'GET' });
  },
  /**
   * Save (create or update) a faculty
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveFaculty(data, id) {
    const url = id ? Utils.replaceRouteId(ROUTES.faculties.show, id) : ROUTES.faculties.store;
    return ApiService.request({ url, method: id ? 'PUT' : 'POST', data });
  },
  /**
   * Delete a faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteFaculty(id) {
    return ApiService.request({ url: Utils.replaceRouteId(ROUTES.faculties.destroy, id), method: 'DELETE' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
const StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchFacultyStats,
  statsKeys: ['faculties', 'with-programs', 'without-programs'],
  onError: TRANSLATION.stats.error
});

// ===========================
// FACULTY MANAGER
// ===========================
const FacultyManager = {
  /**
   * Bind add faculty button
   */
  handleAdd() {
    $('#addFacultyBtn').on('click', function() {
      $('#facultyForm')[0].reset();
      $('#faculty_id').val('');
      $('#name_en').val('');
      $('#name_ar').val('');
      $('#facultyModal .modal-title').text(TRANSLATION.modal.addTitle);
      $('#saveFacultyBtn').text(TRANSLATION.buttons.save);
      $('#facultyModal').modal('show');
    });
  },
  /**
   * Bind faculty form submit
   */
  handleFormSubmit() {
    $('#facultyForm').on('submit', function(e) {
      e.preventDefault();
      const facultyId = $('#faculty_id').val();
      const formData = $('#facultyForm').serialize();
      const $submitBtn = $('#saveFacultyBtn');
      const originalText = $submitBtn.text();

      Utils.setLoadingState($submitBtn, true, { 
        loadingText: facultyId ? TRANSLATION.buttons.updating : TRANSLATION.buttons.saving
      });

      ApiService.saveFaculty(formData, facultyId || null)
        .done(function() {
          $('#facultyModal').modal('hide');
          Utils.reloadDataTable('#faculties-table', null, true);
          Utils.showSuccess(TRANSLATION.faculty.saveSuccess, true);
          StatsManager.refresh();
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr, TRANSLATION.faculty.inputError);
        })
        .always(function() {
          Utils.setLoadingState($submitBtn, false, { normalText: originalText });
        });
    });
  },
  /**
   * Bind edit faculty button
   */
  handleEdit() {
    $(document).on('click', '.editFacultyBtn', function() {
      const facultyId = $(this).data('id');
      ApiService.fetchFaculty(facultyId)
        .done(function(response) {
          const fac = response.data;
          $('#faculty_id').val(fac.id);
          $('#name_en').val(fac.name_en);
          $('#name_ar').val(fac.name_ar);
          $('#facultyModal .modal-title').text(TRANSLATION.modal.editTitle);
          $('#saveFacultyBtn').text(TRANSLATION.buttons.update);
          $('#facultyModal').modal('show');
        })
        .fail(function(xhr) {
            Utils.handleAjaxError(xhr, TRANSLATION.faculty.fetchError);
        });
    });
  },
  /**
   * Bind delete faculty button
   */
  handleDelete() {
    $(document).on('click', '.deleteFacultyBtn', function() {
      const facultyId = $(this).data('id');
      Utils.showConfirmDialog({
          title: TRANSLATION.confirm.title,
          text: TRANSLATION.confirm.text,
          icon: 'warning',
          confirmButtonText: TRANSLATION.confirm.confirmButton,
        }).then(function(result) {
          if (result.isConfirmed) {
            ApiService.deleteFaculty(facultyId)
              .done(function() {
                Utils.reloadDataTable('#faculties-table', null, true);
                Utils.showSuccess(TRANSLATION.faculty.deleteSuccess, true);
                StatsManager.refresh();
              })
              .fail(function(xhr) {
                Utils.handleAjaxError(xhr, TRANSLATION.faculty.deleteError);
              });
          }
        });
    });
  },
  /**
   * Initialize all faculty manager handlers
   */
  init() {
    this.handleAdd();
    this.handleFormSubmit();
    this.handleEdit();
    this.handleDelete();
  }
};

// ===========================
// SEARCH MANAGER
// ===========================
const SearchManager = {
  /**
   * Bind search and clear events
   */
  bindEvents() {
    const reloadTable = Utils.debounce(function() {
        Utils.reloadDataTable('#faculties-table');
    }, 500);

    $('#search_name').on('keyup change', reloadTable);

    $('#clearFiltersBtn').on('click', function() {
      $('#search_name').val('');
      Utils.reloadDataTable('#faculties-table');
    });
  },
  /**
   * Initialize search manager
   */
  init() {
    this.bindEvents();
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
const FacultyManagementApp = {
  /**
   * Initialize all managers
   */
  init() {
    StatsManager.init();
    FacultyManager.init();
    SearchManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  FacultyManagementApp.init();
});
</script>
@endpush