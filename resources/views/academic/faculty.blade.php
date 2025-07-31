@extends('layouts.home')

@section('title', 'Faculty Management | AcadOps')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    
  {{-- ===== STATISTICS CARDS ===== --}}
  <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="faculties"
              label="Total Faculties"
              color="primary"
              icon="bx bx-building"
          />
      </div>
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="with-programs"
              label="Faculties with Programs"
              color="success"
              icon="bx bx-check-circle"
          />
      </div>
      <div class="col-sm-6 col-xl-4">
          <x-ui.card.stat2 
              id="without-programs"
              label="Faculties without Programs"
              color="warning"
              icon="bx bx-x-circle"
          />
      </div>
  </div>

  {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
  <x-ui.page-header 
      title="Faculties"
      description="Manage all faculty records and add new faculties using the options on the right."
      icon="bx bx-building"
  >
      @can('faculty.create')
      <button class="btn btn-primary mx-2" id="addFacultyBtn" type="button" data-bs-toggle="modal" data-bs-target="#facultyModal">
          <i class="bx bx-plus me-1"></i> Add Faculty
      </button>
      @endcan
      <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#facultySearchCollapse" aria-expanded="false" aria-controls="facultySearchCollapse">
          <i class="bx bx-filter-alt me-1"></i> Search
      </button>
  </x-ui.page-header>

  {{-- ===== ADVANCED SEARCH SECTION ===== --}}
  <x-ui.advanced-search 
      title="Advanced Search" 
      formId="advancedFacultySearch" 
      collapseId="facultySearchCollapse"
      :collapsed="false"
  >
      <div class="col-md-4">
          <label for="search_name" class="form-label">Faculty Name:</label>
          <input type="text" class="form-control" id="search_name" name="search_name" placeholder="Faculty Name">
      </div>
      <button class="btn btn-outline-secondary" id="clearFiltersBtn" type="button">
          <i class="bx bx-x"></i> Clear Filters
      </button>
  </x-ui.advanced-search>

  {{-- ===== DATA TABLE ===== --}}
  <x-ui.datatable
      :headers="['Name', 'Programs', 'Students', 'Staff', 'Action']"
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
      title="Add/Edit Faculty"
      size="md"
      :scrollable="false"
      class="faculty-modal"
  >
      <x-slot name="slot">
          <form id="facultyForm">
              <input type="hidden" id="faculty_id" name="faculty_id">
              <div class="row">
                  <div class="col-md-12 mb-3">
                      <label for="name_en" class="form-label">Faculty Name (EN)</label>
                      <input type="text" class="form-control" id="name_en" name="name_en" required>
                  </div>
                  <div class="col-md-12 mb-3">
                      <label for="name_ar" class="form-label">Faculty Name (AR)</label>
                      <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                  </div>
              </div>
          </form>
      </x-slot>
      <x-slot name="footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Close
          </button>
          <button type="submit" class="btn btn-primary" id="saveFacultyBtn" form="facultyForm">
              Save
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
    return this.request({ url: ROUTES.faculties.stats, method: 'GET' });
  },
  /**
   * Fetch a single faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchFaculty(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.faculties.show, id), method: 'GET' });
  },
  /**
   * Save (create or update) a faculty
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveFaculty(data, id) {
    const url = id ? Utils.replaceRouteId(ROUTES.faculties.show, id) : ROUTES.faculties.store;
    const method = id ? 'PUT' : 'POST';
    return this.request({ url, method, data });
  },
  /**
   * Delete a faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteFaculty(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.faculties.destroy, id), method: 'DELETE' });
  }
};

// ===========================
// STATISTICS MANAGER
// ===========================
const StatsManager = {
  /**
   * Initialize statistics cards
   */
  init() {
    this.load();
  },
  /**
   * Load statistics data
   */
  load() {
    this.toggleAllLoadingStates(true);
    ApiService.fetchFacultyStats()
      .done(this.handleSuccess.bind(this))
      .fail(this.handleError.bind(this))
      .always(this.toggleAllLoadingStates.bind(this, false));
  },
  /**
   * Handle successful stats fetch
   * @param {object} response
   */
  handleSuccess(response) {
    if (response.success !== false) {
      const stats = response.data;
      this.updateStatElement('faculties', stats.total.count, stats.total.lastUpdateTime);
      this.updateStatElement('with-programs', stats.withPrograms.count, stats.withPrograms.lastUpdateTime);
      this.updateStatElement('without-programs', stats.withoutPrograms.count, stats.withoutPrograms.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load faculty statistics');
  },
  /**
   * Update a single stat card
   * @param {string} elementId
   * @param {string|number} value
   * @param {string} lastUpdateTime
   */
  updateStatElement(elementId, value, lastUpdateTime) {
    $('#' + elementId + '-value').text(value ?? '0');
    $('#' + elementId + '-last-updated').text(lastUpdateTime ?? '--');
  },
  /**
   * Set all stat cards to N/A
   */
  setAllStatsToNA() {
    ['faculties', 'with-programs', 'without-programs'].forEach(elementId => {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
      /**
     * Toggle loading state for a single stat card
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
     * Toggle loading state for all stat cards
     * @param {boolean} isLoading
     */
    toggleAllLoadingStates: function(isLoading) {
        ['faculties', 'with-programs', 'without-programs'].forEach(function(elementId) {
            this.toggleLoadingState(elementId, isLoading);
        }, this);
    }
};

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
      $('#facultyModal .modal-title').text('Add Faculty');
      $('#saveFacultyBtn').text('Save');
      $('#facultyModal').modal('show');
      if (Utils && Utils.clearValidation) {
        Utils.clearValidation($('#facultyForm'));
      }
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

      if (Utils && Utils.setLoadingState) {
        Utils.setLoadingState($submitBtn, true, { loadingText: facultyId ? 'Updating...' : 'Saving...' });
      } else {
        $submitBtn.prop('disabled', true).text(facultyId ? 'Updating...' : 'Saving...');
      }

      ApiService.saveFaculty(formData, facultyId || null)
        .done(function() {
          $('#facultyModal').modal('hide');
          $('#faculties-table').DataTable().ajax.reload(null, false);
          if (Utils && Utils.showSuccess) {
            Utils.showSuccess('Faculty has been saved successfully.', true);
          }
          StatsManager.load();
        })
        .fail(function(xhr) {
          if (Utils && Utils.handleAjaxError) {
            Utils.handleAjaxError(xhr, 'An error occurred. Please check your input.');
          } else {
            let response = xhr.responseJSON;
            let message = response && response.message ? response.message : 'An error occurred. Please check your input.';
            alert(message);
          }
        })
        .always(function() {
          if (Utils && Utils.setLoadingState) {
            Utils.setLoadingState($submitBtn, false, { normalText: originalText });
          } else {
            $submitBtn.prop('disabled', false).text(originalText);
          }
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
          $('#facultyModal .modal-title').text('Edit Faculty');
          $('#saveFacultyBtn').text('Update');
          $('#facultyModal').modal('show');
          if (Utils && Utils.clearValidation) {
            Utils.clearValidation($('#facultyForm'));
          }
        })
        .fail(function(xhr) {
          if (Utils && Utils.handleAjaxError) {
            Utils.handleAjaxError(xhr, 'Failed to fetch faculty data.');
          } else {
            alert('Failed to fetch faculty data.');
          }
        });
    });
  },
  /**
   * Bind delete faculty button
   */
  handleDelete() {
    $(document).on('click', '.deleteFacultyBtn', function() {
      const facultyId = $(this).data('id');
      if (Utils && Utils.showConfirmDialog) {
        Utils.showConfirmDialog({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          confirmButtonText: 'Yes, delete it!',
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33'
        }).then(function(result) {
          if (result.isConfirmed) {
            ApiService.deleteFaculty(facultyId)
              .done(function() {
                $('#faculties-table').DataTable().ajax.reload(null, false);
                if (Utils && Utils.showSuccess) {
                  Utils.showSuccess('Faculty has been deleted.', true);
                }
                StatsManager.load();
              })
              .fail(function(xhr) {
                if (Utils && Utils.handleAjaxError) {
                  Utils.handleAjaxError(xhr, 'Failed to delete faculty.');
                } else {
                  alert('Failed to delete faculty.');
                }
              });
          }
        });
      } else {
        // fallback
        if (confirm('Are you sure you want to delete this faculty?')) {
          ApiService.deleteFaculty(facultyId)
            .done(function() {
              $('#faculties-table').DataTable().ajax.reload(null, false);
              alert('Faculty has been deleted.');
              StatsManager.load();
            })
            .fail(function() {
              alert('Failed to delete faculty.');
            });
        }
      }
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
    const reloadTable = Utils && Utils.debounce
      ? Utils.debounce(function() {
          if ($.fn.DataTable.isDataTable('#faculties-table')) {
            $('#faculties-table').DataTable().ajax.reload();
          }
        }, 500)
      : function() {
          if ($.fn.DataTable.isDataTable('#faculties-table')) {
            $('#faculties-table').DataTable().ajax.reload();
          }
        };

    $('#search_name').on('keyup change', reloadTable);

    $('#clearFiltersBtn').on('click', function() {
      $('#search_name').val('');
      if ($.fn.DataTable.isDataTable('#faculties-table')) {
        $('#faculties-table').DataTable().ajax.reload();
      }
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