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
      :headers="['Name', 'Programs Count', 'Students Count', 'Action']"
      :columns="[
          ['data' => 'name', 'name' => 'name'],
          ['data' => 'programs', 'name' => 'programs'],
          ['data' => 'students', 'name' => 'students'],
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
                      <label for="name" class="form-label">Faculty Name</label>
                      <input type="text" class="form-control" id="name" name="name" required>
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
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - FacultyManager: Handles CRUD for faculties
 * - SearchManager: Handles advanced search
 * - FacultyManagementApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  faculties: {
    stats: '{{ route('academic.faculties.stats') }}',
    store: '{{ route('academic.faculties.store') }}',
    show: '{{ route('academic.faculties.show', ':id') }}',
    destroy: '{{ route('academic.faculties.destroy', ':id') }}',
    datatable: '{{ route('academic.faculties.datatable') }}'
  }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
var Utils = {
  /**
   * Show a success toast message
   * @param {string} message
   */
  showSuccess: function(message) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: message,
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true
    });
  },
  /**
   * Show an error alert, optionally closing a modal
   * @param {string} message
   * @param {string|null} modalId
   */
  showError: function(message, modalId) {
    if (modalId) {
      var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
      if (modal) modal.hide();
    }
    $('.modal.show').each(function() {
      var modal = bootstrap.Modal.getInstance(this);
      if (modal) modal.hide();
    });
    setTimeout(function() {
      Swal.fire({
        title: 'Error',
        html: message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
    }, 300);
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
   * Generic AJAX request
   * @param {object} options
   * @returns {jqXHR}
   */
  request: function(options) {
    return $.ajax(options);
  },
  /**
   * Fetch faculty statistics
   * @returns {jqXHR}
   */
  fetchFacultyStats: function() {
    return this.request({ url: ROUTES.faculties.stats, method: 'GET' });
  },
  /**
   * Fetch a single faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  fetchFaculty: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.faculties.show, id), method: 'GET' });
  },
  /**
   * Save (create or update) a faculty
   * @param {object} data
   * @param {string|number|null} id
   * @returns {jqXHR}
   */
  saveFaculty: function(data, id) {
    var url = id ? Utils.replaceRouteId(ROUTES.faculties.show, id) : ROUTES.faculties.store;
    var method = id ? 'PUT' : 'POST';
    return this.request({ url: url, method: method, data: data });
  },
  /**
   * Delete a faculty by ID
   * @param {string|number} id
   * @returns {jqXHR}
   */
  deleteFaculty: function(id) {
    return this.request({ url: Utils.replaceRouteId(ROUTES.faculties.destroy, id), method: 'DELETE' });
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
    ApiService.fetchFacultyStats()
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
      this.updateStatElement('faculties', stats.total.total, stats.total.lastUpdateTime);
      this.updateStatElement('with-programs', stats.withPrograms.total, stats.withPrograms.lastUpdateTime);
      this.updateStatElement('without-programs', stats.withoutPrograms.total, stats.withoutPrograms.lastUpdateTime);
    } else {
      this.setAllStatsToNA();
    }
  },
  /**
   * Handle error in stats fetch
   */
  handleError: function() {
    this.setAllStatsToNA();
    Utils.showError('Failed to load faculty statistics');
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
    ['faculties', 'with-programs', 'without-programs'].forEach(function(elementId) {
      $('#' + elementId + '-value').text('N/A');
      $('#' + elementId + '-last-updated').text('N/A');
    });
  },
  /**
   * Toggle loading state for all stat cards
   * @param {boolean} isLoading
   */
  toggleAllLoadingStates: function(isLoading) {
    ['faculties', 'with-programs', 'without-programs'].forEach(function(elementId) {
      Utils.toggleLoadingState(elementId, isLoading);
    });
  }
};

// ===========================
// FACULTY MANAGER
// ===========================
var FacultyManager = {
  /**
   * Bind add faculty button
   */
  handleAdd: function() {
    $('#addFacultyBtn').on('click', function() {
      $('#facultyForm')[0].reset();
      $('#faculty_id').val('');
      $('#facultyModal .modal-title').text('Add Faculty');
      $('#saveFacultyBtn').text('Save');
      $('#facultyModal').modal('show');
    });
  },
  /**
   * Bind faculty form submit
   */
  handleFormSubmit: function() {
    $('#facultyForm').on('submit', function(e) {
      e.preventDefault();
      var facultyId = $('#faculty_id').val();
      var formData = $('#facultyForm').serialize();
      var $submitBtn = $('#saveFacultyBtn');
      var originalText = $submitBtn.text();
      $submitBtn.prop('disabled', true).text('Saving...');
      ApiService.saveFaculty(formData, facultyId || null)
        .done(function() {
          $('#facultyModal').modal('hide');
          $('#faculties-table').DataTable().ajax.reload(null, false);
          Utils.showSuccess('Faculty has been saved successfully.');
          StatsManager.load();
        })
        .fail(function(xhr) {
          var response = xhr.responseJSON;
          if (response && response.errors && Object.keys(response.errors).length > 0) {
            var errorMessages = [];
            Object.keys(response.errors).forEach(function(field) {
              if (Array.isArray(response.errors[field])) {
                errorMessages = errorMessages.concat(response.errors[field]);
              } else {
                errorMessages.push(response.errors[field]);
              }
            });
            Utils.showError(errorMessages.join('<br>'), 'facultyModal');
          } else {
            var message = response && response.message ? response.message : 'An error occurred. Please check your input.';
            Utils.showError(message, 'facultyModal');
          }
        })
        .always(function() {
          $submitBtn.prop('disabled', false).text(originalText);
        });
    });
  },
  /**
   * Bind edit faculty button
   */
  handleEdit: function() {
    $(document).on('click', '.editFacultyBtn', function() {
      var facultyId = $(this).data('id');
      ApiService.fetchFaculty(facultyId)
        .done(function(response) {
          var fac = response.data;
          $('#faculty_id').val(fac.id);
          $('#name').val(fac.name);
          $('#facultyModal .modal-title').text('Edit Faculty');
          $('#saveFacultyBtn').text('Update');
          $('#facultyModal').modal('show');
        })
        .fail(function() {
          Utils.showError('Failed to fetch faculty data.', 'facultyModal');
        });
    });
  },
  /**
   * Bind delete faculty button
   */
  handleDelete: function() {
    $(document).on('click', '.deleteFacultyBtn', function() {
      var facultyId = $(this).data('id');
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
          ApiService.deleteFaculty(facultyId)
            .done(function() {
              $('#faculties-table').DataTable().ajax.reload(null, false);
              Utils.showSuccess('Faculty has been deleted.');
              StatsManager.load();
            })
            .fail(function(xhr) {
              var response = xhr.responseJSON;
              var message = response && response.message ? response.message : 'Failed to delete faculty.';
              Utils.showError(message, 'facultyModal');
            });
        }
      });
    });
  },
  /**
   * Initialize all faculty manager handlers
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
   * Bind search and clear events
   */
  bindEvents: function() {
    var searchTimeout;
    $('#search_name').on('keyup change', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        if ($.fn.DataTable.isDataTable('#faculties-table')) {
          $('#faculties-table').DataTable().ajax.reload();
        }
      }, 500);
    });
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
  init: function() {
    this.bindEvents();
  }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var FacultyManagementApp = {
  /**
   * Initialize all managers
   */
  init: function() {
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