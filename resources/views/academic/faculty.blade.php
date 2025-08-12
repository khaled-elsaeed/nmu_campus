@extends('layouts.home')

@section('title',  __('Faculties'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    
  {{-- ===== STATISTICS CARDS ===== --}}
  <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 id="faculties" :label="__('Total Faculties')" color="secondary" icon="bx bx-building"/>
      </div>
  </div>

  {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
  <x-ui.page-header :title="__('Faculties')" :description="__('Manage university faculties')" icon="bx bx-building">
    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
        <button class="btn btn-primary mx-2" id="addFacultyBtn" type="button" data-bs-toggle="modal" data-bs-target="#facultyModal">
            <i class="bx bx-plus me-1"></i> {{ __('Add Faculty') }}
        </button>
        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#facultySearchCollapse" aria-expanded="false" aria-controls="facultySearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
        </button>
    </div>
  </x-ui.page-header>

  {{-- ===== ADVANCED SEARCH SECTION ===== --}}
  <x-ui.advanced-search  :title="__('Search Faculties')" formId="advancedFacultySearch" collapseId="facultySearchCollapse" collapsed="false">
      <div class="col-md-4">
            <label for="search_name" class="form-label">{{  __('Faculty Name') }}:</label>
          <input type="text" class="form-control" id="search_name" name="search_name" placeholder="{{  __('Enter faculty name') }}">
      </div>
      <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button">
          <i class="bx bx-x"></i> {{ __('Clear Filters') }}
      </button>
  </x-ui.advanced-search>

  {{-- ===== DATA TABLE ===== --}}
  <x-ui.datatable.table
      :headers="[__('Name'),__('Programs'),__('Students'),__('Staff'),__('Created At'),__('Actions')]"
      :columns="[
          ['data' => 'name', 'name' => 'name'],
          ['data' => 'programs_count', 'name' => 'programs_count'],
          ['data' => 'students_count', 'name' => 'students_count'],
          ['data' => 'staff_count', 'name' => 'staff_count'],
          ['data' => 'created_at', 'name' => 'created_at'],
          ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
      ]"
      :ajax-url="route('academic.faculties.datatable')"
      table-id="faculties-table"
      :filter-fields="['search_name']"
  />

  {{-- ===== MODALS SECTION ===== --}}
  
  {{-- Add/Edit Faculty Modal --}}
  <x-ui.modal id="facultyModal" :title="__('Faculty Details')" size="md" scrollable="false" class="faculty-modal">
      <x-slot name="slot">
          <form id="facultyForm">
              <input type="hidden" id="faculty_id" name="faculty_id">
              <div class="row">
                  <div class="col-md-12 mb-3">
                      <label for="name_en" class="form-label">{{ __('Name (English)') }}</label>
                      <input type="text" class="form-control" id="name_en" name="name_en" required>
                  </div>
                  <div class="col-md-12 mb-3">
                      <label for="name_ar" class="form-label">{{ __('Name (Arabic)') }}</label>
                      <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                  </div>
              </div>
          </form>
      </x-slot>
      <x-slot name="footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              {{ __('Close') }}
          </button>
          <button type="submit" class="btn btn-primary" id="saveFacultyBtn" form="facultyForm">
              {{ __('Save') }}
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
  buttons: {
    saving: @json(__('Saving...')),
    updating: @json(__('Updating...')),
    save: @json(__('Save')),
    update: @json(__('Update'))
  },
  modal: {
    addTitle: @json(__('Add New Faculty')),
    editTitle: @json(__('Edit Faculty')),
    viewTitle: @json(__('View Faculty'))
  },
  confirm: {
    delete: {
      title: @json(__('Delete Faculty')),
      text: @json(__('Are you sure you want to delete this Faculty ?')),
      button: @json(__('Yes, Delete'))
    }
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
  statsKeys: ['faculties'],
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

      ApiService.saveFaculty(formData, facultyId)
        .done(function(response) {
          $('#facultyModal').modal('hide');
          Utils.reloadDataTable('#faculties-table', null, true);
          Utils.showSuccess(response.message, true);
          StatsManager.refresh();
        })
        .fail(function(xhr) {
          Utils.handleAjaxError(xhr,xhr.responseJSON?.message);
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
            Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
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
          title: TRANSLATION.confirm.delete.title,
          text: TRANSLATION.confirm.delete.text,
          icon: 'warning',
          confirmButtonText: TRANSLATION.confirm.delete.button,
        }).then(function(result) {
          if (result.isConfirmed) {
            ApiService.deleteFaculty(facultyId)
              .done(function(response) {
                Utils.reloadDataTable('#faculties-table', null, true);
                Utils.showSuccess(response.message, true);
                StatsManager.refresh();
              })
              .fail(function(xhr) {
                Utils.handleAjaxError(xhr,xhr.responseJSON?.message);
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