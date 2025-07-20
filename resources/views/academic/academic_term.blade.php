@extends('layouts.home')

@section('title', 'Academic Term Management | AcadOps')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                id="terms"
                label="Total Terms"
                color="primary"
                icon="bx bx-calendar"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                id="active"
                label="Active Terms"
                color="success"
                icon="bx bx-check-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                id="inactive"
                label="Inactive Terms"
                color="warning"
                icon="bx bx-x-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2 
                id="current"
                label="Current Term"
                color="info"
                icon="bx bx-time"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        title="Terms"
        description="Manage academic terms and their details."
        icon="bx bx-calendar"
    >
        <button class="btn btn-primary" 
                id="addTermBtn" 
                type="button" 
                data-bs-toggle="modal" 
                data-bs-target="#termModal">
            <i class="bx bx-plus me-1"></i> Add Term
        </button>
        <button class="btn btn-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#termSearchCollapse" aria-expanded="false" aria-controls="termSearchCollapse">
            <i class="bx bx-filter-alt me-1"></i> Search
        </button>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        title="Advanced Search" 
        formId="advancedTermSearch" 
        collapseId="termSearchCollapse"
        :collapsed="false"
        :show-clear-button="true"
        clear-button-text="Clear Filters"
        clear-button-id="clearTermFiltersBtn"
    >
        <div class="col-md-3">
            <label for="search_season" class="form-label">Season:</label>
            <select class="form-control" id="search_season">
                <option value="">All Seasons</option>
                <option value="Fall">Fall</option>
                <option value="Spring">Spring</option>
                <option value="Summer">Summer</option>
                <option value="Winter">Winter</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_year" class="form-label">Academic Year:</label>
            <select class="form-control" id="search_year">
                <option value="">All Years</option>
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_code" class="form-label">Term Code:</label>
            <select class="form-control" id="search_code">
                <option value="">All Codes</option>
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_active" class="form-label">Status:</label>
            <select class="form-control" id="search_active">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable
        :headers="['Season', 'Year', 'Code', 'Start Date', 'End Date', 'Reservations', 'Status', 'Action']"
        :columns="[
            ['data' => 'season', 'name' => 'season'],
            ['data' => 'year', 'name' => 'year'],
            ['data' => 'code', 'name' => 'code'],
            ['data' => 'start_date', 'name' => 'start_date'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'reservations', 'name' => 'reservations'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ]"
        :ajax-url="route('academic.academic_terms.datatable')"
        table-id="terms-table"
        :filter-fields="['search_season','search_year','search_code','search_active']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Add/Edit Term Modal --}}
    <x-ui.modal 
        id="termModal"
        title="Add/Edit Term"
        size="lg"
        :scrollable="false"
        class="term-modal"
    >
        <x-slot name="slot">
            <form id="termForm">
                <input type="hidden" id="term_id" name="term_id">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="season" class="form-label">Season <span class="text-danger">*</span></label>
                        <select class="form-select" id="season" name="season" required>
                            <option value="">Select Season</option>
                            <option value="fall">Fall</option>
                            <option value="spring">Spring</option>
                            <option value="summer">Summer</option>
                            <option value="winter">Winter</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="year" name="year" required placeholder="e.g., 2024-2025">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="semester_number" class="form-label">Semester Number <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester_number" name="semester_number" required>
                            <option value="">Select Semester</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Close
            </button>
            <button type="submit" class="btn btn-primary" id="saveTermBtn" form="termForm">Save</button>
        </x-slot>
    </x-ui.modal>
</div>
@endsection

@push('scripts')
<script>
/**
 * Academic Term Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards
 * - SearchManager: Handles advanced search
 * - TermManager: Handles CRUD and actions for terms
 * - AcademicTermApp: Initializes all managers
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    terms: {
        start: '{{ route('academic_terms.start', ':id') }}',
        end: '{{ route('academic_terms.end', ':id') }}',
        activate: '{{ route('academic_terms.activate', ':id') }}',
        deactivate: '{{ route('academic_terms.deactivate', ':id') }}',
        stats: '{{ route('academic.academic_terms.stats') }}',
        store: '{{ route('academic.academic_terms.store') }}',
        show: '{{ route('academic.academic_terms.show', ':id') }}',
        destroy: '{{ route('academic.academic_terms.destroy', ':id') }}',
        all: '{{ route('academic_terms.all') }}'
    }
};

// ===========================
// UTILITY FUNCTIONS
// ===========================
var Utils = {
    /**
     * Shows success notification
     * @param {string} message
     */
    showSuccess: function(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    },
    /**
     * Shows error notification with modal handling
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
     * Shows information notification
     * @param {string} title
     * @param {string} message
     */
    showInfo: function(title, message) {
        Swal.fire({
            title: title,
            html: message,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    },
    /**
     * Shows/hides loading spinners and content for stat2 component
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
     * Replaces :id placeholder in route URLs
     * @param {string} route
     * @param {number} id
     * @returns {string}
     */
    replaceRouteId: function(route, id) {
        return route.replace(':id', id);
    },
    /**
     * Generates academic years for dropdowns
     * @param {number} yearsBack
     * @param {number} yearsForward
     * @returns {Array}
     */
    generateAcademicYears: function(yearsBack, yearsForward) {
        yearsBack = yearsBack || 5;
        yearsForward = yearsForward || 3;
        var currentYear = new Date().getFullYear();
        var years = [];
        for (var i = currentYear - yearsBack; i <= currentYear + yearsForward; i++) {
            years.push(i + '-' + (i + 1));
        }
        return years.reverse();
    },
    /**
     * Validates form fields
     * @param {Object} formData
     * @returns {Object}
     */
    validateForm: function(formData) {
        var errors = [];
        if (!formData.season) errors.push('Season is required');
        if (!formData.year) {
            errors.push('Year is required');
        } else if (!/^\d{4}-\d{4}$/.test(formData.year)) {
            errors.push('Year must be in format YYYY-YYYY (e.g., 2024-2025)');
        }
        if (!formData.semester_number) errors.push('Semester number is required');
        if (!formData.start_date) errors.push('Start date is required');
        if (formData.start_date && formData.end_date) {
            var startDate = new Date(formData.start_date);
            var endDate = new Date(formData.end_date);
            if (endDate <= startDate) errors.push('End date must be after start date');
        }
        return { isValid: errors.length === 0, errors: errors };
    }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
    /**
     * Generic AJAX request wrapper
     * @param {Object} options
     * @returns {Promise}
     */
    request: function(options) {
        var defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };
        return $.ajax($.extend(defaultOptions, options));
    },
    /**
     * Fetches term statistics
     * @returns {Promise}
     */
    fetchTermStats: function() {
        return this.request({ url: ROUTES.terms.stats, method: 'GET' });
    },
    /**
     * Fetches a specific term
     * @param {number} id
     * @returns {Promise}
     */
    fetchTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.show, id), method: 'GET' });
    },
    /**
     * Fetches all terms
     * @returns {Promise}
     */
    fetchAll: function() {
        return this.request({ url: ROUTES.terms.all, method: 'GET' });
    },
    /**
     * Saves a term (create or update)
     * @param {FormData} formData
     * @param {number|null} id
     * @returns {Promise}
     */
    saveTerm: function(formData, id) {
        var url = id ? Utils.replaceRouteId(ROUTES.terms.show, id) : ROUTES.terms.store;
        if (id) {
            formData.append('_method', 'PUT');
        }
        var method = 'POST';
        return this.request({ url: url, method: method, data: formData, processData: false, contentType: false });
    },
    /**
     * Deletes a term
     * @param {number} id
     * @returns {Promise}
     */
    deleteTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.destroy, id), method: 'DELETE' });
    },
    /**
     * Starts a term
     * @param {number} id
     * @returns {Promise}
     */
    startTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.start, id), method: 'POST' });
    },
    /**
     * Ends a term
     * @param {number} id
     * @returns {Promise}
     */
    endTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.end, id), method: 'POST' });
    },
    /**
     * Activates a term
     * @param {number} id
     * @returns {Promise}
     */
    activateTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.activate, id), method: 'PATCH' });
    },
    /**
     * Deactivates a term
     * @param {number} id
     * @returns {Promise}
     */
    deactivateTerm: function(id) {
        return this.request({ url: Utils.replaceRouteId(ROUTES.terms.deactivate, id), method: 'PATCH' });
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
        ApiService.fetchTermStats()
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
            this.updateStatElement('terms', stats.total.total, stats.total.lastUpdateTime);
            this.updateStatElement('active', stats.active.total, stats.active.lastUpdateTime);
            this.updateStatElement('inactive', stats.inactive.total, stats.inactive.lastUpdateTime);
            this.updateStatElement('current', stats.current.total, stats.current.lastUpdateTime);
        } else {
            this.setAllStatsToNA();
        }
    },
    /**
     * Handle error in stats fetch
     */
    handleError: function() {
        this.setAllStatsToNA();
        Utils.showError('Failed to load term statistics');
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
        ['terms', 'active', 'inactive', 'current'].forEach(function(elementId) {
            $('#' + elementId + '-value').text('N/A');
            $('#' + elementId + '-last-updated').text('N/A');
        });
    },
    /**
     * Toggle loading state for all stat cards
     * @param {boolean} isLoading
     */
    toggleAllLoadingStates: function(isLoading) {
        ['terms', 'active', 'inactive', 'current'].forEach(function(elementId) {
            Utils.toggleLoadingState(elementId, isLoading);
        });
    }
};

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
    /**
     * Populate search dropdowns with data
     */
    populateSearchDropdowns: function() {
        var academicYears = Utils.generateAcademicYears();
        var $yearSelect = $('#search_year');
        $yearSelect.find('option:not(:first)').remove();
        academicYears.forEach(function(year) {
            $yearSelect.append('<option value="' + year + '">' + year + '</option>');
        });
        ApiService.fetchAll()
            .done(function(response) {
                var terms = response.data || response;
                if (Array.isArray(terms)) {
                    var uniqueCodes = [...new Set(terms.map(function(term) { return term.code; }).filter(Boolean))].sort();
                    var $codeSelect = $('#search_code');
                    $codeSelect.find('option:not(:first)').remove();
                    uniqueCodes.forEach(function(code) {
                        $codeSelect.append('<option value="' + code + '">' + code + '</option>');
                    });
                }
            })
            .fail(function(xhr) {
                console.warn('Failed to load term codes for search dropdown:', xhr.responseJSON?.message);
            });
    },
    /**
     * Bind search-related event handlers
     */
    bindEvents: function() {
        var searchTimeout;
        $('#search_year, #search_code').on('keyup change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#terms-table')) {
                    $('#terms-table').DataTable().ajax.reload();
                }
            }, 500);
        });
        $('#search_season, #search_active').on('change', function() {
            if ($.fn.DataTable.isDataTable('#terms-table')) {
                $('#terms-table').DataTable().ajax.reload();
            }
        });
        $('#clearTermFiltersBtn').on('click', function() {
            $('#search_season, #search_year, #search_code, #search_active').val('');
            if ($.fn.DataTable.isDataTable('#terms-table')) {
                $('#terms-table').DataTable().ajax.reload();
            }
        });
    },
    /**
     * Initialize SearchManager
     */
    init: function() {
        this.populateSearchDropdowns();
        this.bindEvents();
    }
};

// ===========================
// TERM MANAGER
// ===========================
var TermManager = {
    /**
     * Handles Add Term button click
     */
    handleAddTerm: function() {
        $('#addTermBtn').on('click', function() {
            $('#termForm')[0].reset();
            $('#term_id').val('');
            $('#termModal .modal-title').text('Add Term');
            $('#saveTermBtn').text('Save');
            $('#termModal').modal('show');
        });
    },
    /**
     * Handles term form submission
     */
    handleTermFormSubmit: function() {
        $('#termForm').on('submit', function(e) {
            e.preventDefault();
            var termId = $('#term_id').val();
            var formData = new FormData(e.target);
            var formObject = {};
            formData.forEach(function(value, key) { formObject[key] = value; });
            var validation = Utils.validateForm(formObject);
            if (!validation.isValid) {
                Utils.showError(validation.errors.join('<br>'));
                return;
            }
            var $submitBtn = $('#saveTermBtn');
            var originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Saving...');
            ApiService.saveTerm(formData, termId || null)
                .done(function(response) {
                    $('#termModal').modal('hide');
                    if ($.fn.DataTable.isDataTable('#terms-table')) {
                        $('#terms-table').DataTable().ajax.reload(null, false);
                    }
                    Utils.showSuccess(response.message || 'Term saved successfully.');
                    StatsManager.load();
                    SearchManager.populateSearchDropdowns();
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
                        Utils.showError(errorMessages.join('<br>'), 'termModal');
                    } else {
                        var message = response && response.message ? response.message : 'An error occurred. Please check your input.';
                        Utils.showError(message, 'termModal');
                    }
                })
                .always(function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                });
        });
    },
    /**
     * Handles Edit Term button click
     */
    handleEditTerm: function() {
        $(document).on('click', '.editTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            ApiService.fetchTerm(termId)
                .done(function(response) {
                    var term = response.data;
                    $('#term_id').val(term.id);
                    $('#season').val(term.season);
                    $('#year').val(term.year);
                    $('#semester_number').val(term.semester_number);
                    $('#start_date').val(term.start_date ? term.start_date.substring(0, 10) : '');
                    $('#end_date').val(term.end_date ? term.end_date.substring(0, 10) : '');
                    $('#active').prop('checked', Boolean(term.active));
                    $('#termModal .modal-title').text('Edit Term');
                    $('#saveTermBtn').text('Update');
                    $('#termModal').modal('show');
                })
                .fail(function(xhr) {
                    var response = xhr.responseJSON;
                    var message = response && response.message ? response.message : 'Failed to load term details.';
                    Utils.showError(message);
                });
        });
    },
    /**
     * Handles Delete Term button click
     */
    handleDeleteTerm: function() {
        $(document).on('click', '.deleteTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deleteTerm(termId)
                        .done(function(response) {
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            Utils.showSuccess(response.message || 'Term has been deleted.');
                            StatsManager.load();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            var response = xhr.responseJSON;
                            var message = response && response.message ? response.message : 'Failed to delete term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },
    /**
     * Handles Start Term button click
     */
    handleStartTerm: function() {
        $(document).on('click', '.startTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            Swal.fire({
                title: 'Start Academic Term?',
                text: 'Are you sure you want to start this academic term? Once started, all confirmed reservations for this term will be activated.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, start term!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.startTerm(termId)
                        .done(function(response) {
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            Utils.showSuccess(response.message || 'Term has been started successfully. Residents can now select this term for their reservations.');
                            StatsManager.load();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            var response = xhr.responseJSON;
                            var message = response && response.message ? response.message : 'Failed to start term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },
    /**
     * Handles End Term button click
     */
    handleEndTerm: function() {
        $(document).on('click', '.endTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            Swal.fire({
                title: 'End Academic Term?',
                text: 'Are you sure you want to end this academic term?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, end term!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.endTerm(termId)
                        .done(function(response) {
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            Utils.showSuccess(response.message || 'Term has been ended.');
                            StatsManager.load();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            var response = xhr.responseJSON;
                            var message = response && response.message ? response.message : 'Failed to end term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },
    /**
     * Handles Activate Term button click
     */
    handleActivateTerm: function() {
        $(document).on('click', '.activateTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            Swal.fire({
                title: 'Activate Academic Term?',
                text: 'Are you sure you want to activate this academic term? This will make the term available for reservations.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, activate term!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.activateTerm(termId)
                        .done(function(response) {
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            Utils.showSuccess(response.message || 'Term has been activated.');
                            StatsManager.load();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            var response = xhr.responseJSON;
                            var message = response && response.message ? response.message : 'Failed to activate term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },
    /**
     * Handles Deactivate Term button click
     */
    handleDeactivateTerm: function() {
        $(document).on('click', '.deactivateTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            Swal.fire({
                title: 'Deactivate Academic Term?',
                text: 'Are you sure you want to deactivate this academic term? This will make the term unavailable for new reservations.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, deactivate term!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deactivateTerm(termId)
                        .done(function(response) {
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            Utils.showSuccess(response.message || 'Term has been deactivated.');
                            StatsManager.load();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            var response = xhr.responseJSON;
                            var message = response && response.message ? response.message : 'Failed to deactivate term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },
    /**
     * Initializes all event listeners and modal handlers for TermManager
     */
    init: function() {
        this.handleAddTerm();
        this.handleTermFormSubmit();
        this.handleEditTerm();
        this.handleDeleteTerm();
        this.handleStartTerm();
        this.handleEndTerm();
        this.handleActivateTerm();
        this.handleDeactivateTerm();
    }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var AcademicTermApp = {
    /**
     * Initialize all managers
     */
    init: function() {
        StatsManager.init();
        TermManager.init();
        SearchManager.init();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
    AcademicTermApp.init();
});
</script>
@endpush 