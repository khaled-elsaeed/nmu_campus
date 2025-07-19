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
        :headers="['Season', 'Year', 'Code', 'Reservations', 'Status', 'Action']"
        :columns="[
            ['data' => 'season', 'name' => 'season'],
            ['data' => 'year', 'name' => 'year'],
            ['data' => 'code', 'name' => 'code'],
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
 * Academic Term Management System JavaScript
 * Handles CRUD operations for academic terms
 */

// ===========================
// CONSTANTS & CONFIGURATION
// ===========================

const ROUTES = {
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

const Utils = {
    /**
     * Shows success notification
     * @param {string} message - Success message to display
     */
    showSuccess(message) {
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
     * @param {string} message - Error message to display
     * @param {string|null} modalId - Modal ID to close before showing error (optional)
     */
    showError(message, modalId = null) {
        // Close modal if specified
        if (modalId) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
            if (modal) {
                modal.hide();
            }
        }
        
        // Close any open modals automatically
        $('.modal.show').each(function() {
            const modal = bootstrap.Modal.getInstance(this);
            if (modal) {
                modal.hide();
            }
        });
        
        // Show error after a brief delay to ensure modal is closed
        setTimeout(() => {
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
     * @param {string} title - Title of the notification
     * @param {string} message - Information message to display
     */
    showInfo(title, message) {
        Swal.fire({
            title: title,
            html: message,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    },

    /**
     * Shows/hides loading spinners and content for stat2 component
     * @param {string} elementId - Base element ID
     * @param {boolean} isLoading - Whether to show loading state
     */
    toggleLoadingState(elementId, isLoading) {
        const $value = $(`#${elementId}-value`);
        const $loader = $(`#${elementId}-loader`);
        const $updated = $(`#${elementId}-last-updated`);
        const $updatedLoader = $(`#${elementId}-last-updated-loader`);

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
     * @param {string} route - Route URL with :id placeholder
     * @param {number} id - ID to replace placeholder with
     * @returns {string} - Updated URL
     */
    replaceRouteId(route, id) {
        return route.replace(':id', id);
    },

    /**
     * Generates academic years for dropdowns
     * @param {number} yearsBack - Number of years to go back
     * @param {number} yearsForward - Number of years to go forward
     * @returns {Array} - Array of academic year strings
     */
    generateAcademicYears(yearsBack = 5, yearsForward = 3) {
        const currentYear = new Date().getFullYear();
        const years = [];
        
        for (let i = currentYear - yearsBack; i <= currentYear + yearsForward; i++) {
            years.push(`${i}-${i + 1}`);
        }
        
        return years.reverse(); // Most recent first
    },

    /**
     * Validates form fields
     * @param {Object} formData - Form data object
     * @returns {Object} - Validation result with isValid flag and errors array
     */
    validateForm(formData) {
        const errors = [];
        
        if (!formData.season) {
            errors.push('Season is required');
        }
        
        if (!formData.year) {
            errors.push('Year is required');
        } else if (!/^\d{4}-\d{4}$/.test(formData.year)) {
            errors.push('Year must be in format YYYY-YYYY (e.g., 2024-2025)');
        }
        
        if (!formData.semester_number) {
            errors.push('Semester number is required');
        }
        
        if (!formData.start_date) {
            errors.push('Start date is required');
        }
        
        if (formData.start_date && formData.end_date) {
            const startDate = new Date(formData.start_date);
            const endDate = new Date(formData.end_date);
            
            if (endDate <= startDate) {
                errors.push('End date must be after start date');
            }
        }
        
        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }
};

// ===========================
// API SERVICE LAYER
// ===========================

const ApiService = {
    /**
     * Generic AJAX request wrapper
     * @param {Object} options - jQuery AJAX options
     * @returns {Promise} - jQuery promise
     */
    request(options) {
        const defaultOptions = {
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
    fetchTermStats() {
        return this.request({
            url: ROUTES.terms.stats,
            method: 'GET'
        });
    },

    /**
     * Fetches a specific term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    fetchTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.show, id),
            method: 'GET'
        });
    },

    /**
     * Fetches all terms
     * @returns {Promise}
     */
    fetchAll() {
        return this.request({
            url: ROUTES.terms.all,
            method: 'GET'
        });
    },

    /**
     * Saves a term (create or update)
     * @param {FormData} formData - Term form data
     * @param {number|null} id - Term ID for update, null for create
     * @returns {Promise}
     */
    saveTerm(formData, id = null) {
        const url = id ? Utils.replaceRouteId(ROUTES.terms.show, id) : ROUTES.terms.store;
        const method = id ? 'PUT' : 'POST';
        
        return this.request({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false
        });
    },

    /**
     * Deletes a term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    deleteTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.destroy, id),
            method: 'DELETE'
        });
    },

    /**
     * Starts a term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    startTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.start, id),
            method: 'POST'
        });
    },

    /**
     * Ends a term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    endTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.end, id),
            method: 'POST'
        });
    },

    /**
     * Activates a term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    activateTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.activate, id),
            method: 'POST'
        });
    },

    /**
     * Deactivates a term
     * @param {number} id - Term ID
     * @returns {Promise}
     */
    deactivateTerm(id) {
        return this.request({
            url: Utils.replaceRouteId(ROUTES.terms.deactivate, id),
            method: 'POST'
        });
    }
};

// ===========================
// STATISTICS MANAGEMENT
// ===========================

const StatsManager = {
    /**
     * Loads and displays term statistics
     */
    loadTermStats() {
        // Show loading state for all stats
        Utils.toggleLoadingState('terms', true);
        Utils.toggleLoadingState('active', true);
        Utils.toggleLoadingState('inactive', true);
        Utils.toggleLoadingState('current', true);
        
        ApiService.fetchTermStats()
            .done((response) => {
                const data = response.data;
                
                // Update term statistics
                $('#terms-value').text(data.total?.total ?? '--');
                $('#terms-last-updated').text(data.total?.lastUpdateTime ?? '--');
                $('#active-value').text(data.active?.total ?? '--');
                $('#active-last-updated').text(data.active?.lastUpdateTime ?? '--');
                $('#inactive-value').text(data.inactive?.total ?? '--');
                $('#inactive-last-updated').text(data.inactive?.lastUpdateTime ?? '--');
                $('#current-value').text(data.current?.total ?? '--');
                $('#current-last-updated').text(data.current?.lastUpdateTime ?? '--');
                
                // Hide loading state
                Utils.toggleLoadingState('terms', false);
                Utils.toggleLoadingState('active', false);
                Utils.toggleLoadingState('inactive', false);
                Utils.toggleLoadingState('current', false);
            })
            .fail((xhr) => {
                // Show error state
                $('#terms-value, #active-value, #inactive-value, #current-value').text('N/A');
                $('#terms-last-updated, #active-last-updated, #inactive-last-updated, #current-last-updated').text('N/A');
                
                Utils.toggleLoadingState('terms', false);
                Utils.toggleLoadingState('active', false);
                Utils.toggleLoadingState('inactive', false);
                Utils.toggleLoadingState('current', false);
                
                const response = xhr.responseJSON;
                const message = response?.message || 'Failed to load term statistics';
                console.error('Stats loading error:', message);
                Utils.showError(message);
            });
    },

    /**
     * Initialize StatsManager
     */
    init() {
        this.loadTermStats();
    }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================

const SearchManager = {
    /**
     * Populate search dropdowns with data
     */
    populateSearchDropdowns() {
        // Populate academic years dropdown
        const academicYears = Utils.generateAcademicYears();
        const $yearSelect = $('#search_year');
        
        // Clear and populate year options
        $yearSelect.find('option:not(:first)').remove();
        academicYears.forEach(year => {
            $yearSelect.append(`<option value="${year}">${year}</option>`);
        });

        // Populate term codes dropdown from existing terms
        ApiService.fetchAll()
            .done((response) => {
                const terms = response.data || response;
                if (Array.isArray(terms)) {
                    const uniqueCodes = [...new Set(terms.map(term => term.code).filter(code => code))].sort();
                    const $codeSelect = $('#search_code');
                    
                    // Clear and populate code options
                    $codeSelect.find('option:not(:first)').remove();
                    uniqueCodes.forEach(code => {
                        $codeSelect.append(`<option value="${code}">${code}</option>`);
                    });
                }
            })
            .fail((xhr) => {
                console.warn('Failed to load term codes for search dropdown:', xhr.responseJSON?.message);
            });
    },

    /**
     * Bind search-related event handlers
     */
    bindEvents() {
        let searchTimeout;

        // Real-time search for text inputs with debouncing
        $('#search_year, #search_code').on('keyup change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#terms-table')) {
                    $('#terms-table').DataTable().ajax.reload();
                }
            }, 500);
        });

        // Immediate search for select dropdowns
        $('#search_season, #search_active').on('change', function() {
            if ($.fn.DataTable.isDataTable('#terms-table')) {
                $('#terms-table').DataTable().ajax.reload();
            }
        });

        // Clear filters button
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
    init() {
        this.populateSearchDropdowns();
        this.bindEvents();
    }
};

// ===========================
// TERM CRUD OPERATIONS
// ===========================

const TermManager = {
    /**
     * Handles Add Term button click
     */
    handleAddTerm() {
        $('#addTermBtn').on('click', () => {
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
    handleTermFormSubmit() {
        $('#termForm').on('submit', (e) => {
            e.preventDefault();
            
            const termId = $('#term_id').val();
            const formData = new FormData(e.target);
            
            // Client-side validation
            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
            });
            
            const validation = Utils.validateForm(formObject);
            if (!validation.isValid) {
                Utils.showError(validation.errors.join('<br>'));
                return;
            }
            
            // Disable submit button during request
            const $submitBtn = $('#saveTermBtn');
            const originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Saving...');
            
            ApiService.saveTerm(formData, termId || null)
                .done((response) => {
                    $('#termModal').modal('hide');
                    
                    // Refresh data table if it exists
                    if ($.fn.DataTable.isDataTable('#terms-table')) {
                        $('#terms-table').DataTable().ajax.reload(null, false);
                    }
                    
                    Utils.showSuccess(response.message || 'Term saved successfully.');
                    StatsManager.loadTermStats();
                    SearchManager.populateSearchDropdowns();
                })
                .fail((xhr) => {
                    const response = xhr.responseJSON;
                    if (response?.errors && Object.keys(response.errors).length > 0) {
                        // Handle validation errors
                        const errorMessages = [];
                        Object.keys(response.errors).forEach(field => {
                            if (Array.isArray(response.errors[field])) {
                                errorMessages.push(...response.errors[field]);
                            } else {
                                errorMessages.push(response.errors[field]);
                            }
                        });
                        Utils.showError(errorMessages.join('<br>'), 'termModal');
                    } else {
                        // Handle general errors
                        const message = response?.message || 'An error occurred. Please check your input.';
                        Utils.showError(message, 'termModal');
                    }
                })
                .always(() => {
                    $submitBtn.prop('disabled', false).text(originalText);
                });
        });
    },

    /**
     * Handles Edit Term button click
     */
    handleEditTerm() {
        $(document).on('click', '.editTermBtn', function () {
            const termId = $(this).data('id');
            
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            
            ApiService.fetchTerm(termId)
                .done((response) => {
                    const term = response.data;
                    
                    // Populate form fields
                    $('#term_id').val(term.id);
                    $('#season').val(term.season);
                    $('#year').val(term.year);
                    $('#semester_number').val(term.semester_number);
                    $('#start_date').val(term.start_date ? term.start_date.substring(0, 10) : '');
                    $('#end_date').val(term.end_date ? term.end_date.substring(0, 10) : '');
                    $('#active').prop('checked', Boolean(term.active));
                    
                    // Update modal
                    $('#termModal .modal-title').text('Edit Term');
                    $('#saveTermBtn').text('Update');
                    $('#termModal').modal('show');
                })
                .fail((xhr) => {
                    const response = xhr.responseJSON;
                    const message = response?.message || 'Failed to load term details.';
                    Utils.showError(message);
                });
        });
    },

    /**
     * Handles Delete Term button click
     */
    handleDeleteTerm() {
        $(document).on('click', '.deleteTermBtn', function () {
            const termId = $(this).data('id');
            
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
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiService.deleteTerm(termId)
                        .done((response) => {
                            // Refresh data table if it exists
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            
                            Utils.showSuccess(response.message || 'Term has been deleted.');
                            StatsManager.loadTermStats();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail((xhr) => {
                            const response = xhr.responseJSON;
                            const message = response?.message || 'Failed to delete term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },

    /**
     * Handles Start Term button click with enhanced confirmation
     */
    handleStartTerm() {
        $(document).on('click', '.startTermBtn', function () {
            const termId = $(this).data('id');
            
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
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiService.startTerm(termId)
                        .done((response) => {
                            // Refresh data table if it exists
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            
                            // Show success message
                            Utils.showSuccess(response.message || 'Term has been started successfully. Residents can now select this term for their reservations.');
                            
                            StatsManager.loadTermStats();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail((xhr) => {
                            const response = xhr.responseJSON;
                            const message = response?.message || 'Failed to start term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },

    /**
     * Handles End Term button click
     */
    handleEndTerm() {
        $(document).on('click', '.endTermBtn', function () {
            const termId = $(this).data('id');
            
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
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiService.endTerm(termId)
                        .done((response) => {
                            // Refresh data table if it exists
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            
                            Utils.showSuccess(response.message || 'Term has been ended.');
                            StatsManager.loadTermStats();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail((xhr) => {
                            const response = xhr.responseJSON;
                            const message = response?.message || 'Failed to end term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },

    /**
     * Handles Activate Term button click
     */
    handleActivateTerm() {
        $(document).on('click', '.activateTermBtn', function () {
            const termId = $(this).data('id');
            
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
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiService.activateTerm(termId)
                        .done((response) => {
                            // Refresh data table if it exists
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            
                            Utils.showSuccess(response.message || 'Term has been activated.');
                            StatsManager.loadTermStats();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail((xhr) => {
                            const response = xhr.responseJSON;
                            const message = response?.message || 'Failed to activate term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },

    /**
     * Handles Deactivate Term button click
     */
    handleDeactivateTerm() {
        $(document).on('click', '.deactivateTermBtn', function () {
            const termId = $(this).data('id');
            
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
            }).then((result) => {
                if (result.isConfirmed) {
                    ApiService.deactivateTerm(termId)
                        .done((response) => {
                            // Refresh data table if it exists
                            if ($.fn.DataTable.isDataTable('#terms-table')) {
                                $('#terms-table').DataTable().ajax.reload(null, false);
                            }
                            
                            Utils.showSuccess(response.message || 'Term has been deactivated.');
                            StatsManager.loadTermStats();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail((xhr) => {
                            const response = xhr.responseJSON;
                            const message = response?.message || 'Failed to deactivate term.';
                            Utils.showError(message);
                        });
                }
            });
        });
    },

    /**
     * Initializes all event listeners and modal handlers for TermManager
     */
    init() {
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
// MAIN APPLICATION
// ===========================
const AcademicTermApp = {
    init() {
        StatsManager.init();
        TermManager.init();
        SearchManager.init();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(() => {
  AcademicTermApp.init();
});
</script>
@endpush 