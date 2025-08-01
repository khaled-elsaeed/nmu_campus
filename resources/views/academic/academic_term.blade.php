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
                color="white"
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
        :filter-fields="['search_season','search_year','search_active']"
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

    {{-- View Academic Term Modal --}}
    <x-ui.modal 
        id="viewTermModal"
        title="Academic Term Details"
        size="md"
        :scrollable="true"
        class="view-term-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Season:</label>
                    <p id="view-term-season" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Year:</label>
                    <p id="view-term-year" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Code:</label>
                    <p id="view-term-code" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Semester Number:</label>
                    <p id="view-term-semester-number" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Start Date:</label>
                    <p id="view-term-start-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">End Date:</label>
                    <p id="view-term-end-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Active:</label>
                    <p id="view-term-active" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Current:</label>
                    <p id="view-term-current" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Activated At:</label>
                    <p id="view-term-activated-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Started At:</label>
                    <p id="view-term-started-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Ended At:</label>
                    <p id="view-term-ended-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Created At:</label>
                    <p id="view-term-created-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">Updated At:</label>
                    <p id="view-term-updated-at" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
 * - ApiService: Handles all AJAX requests
 * - StatsManager: Handles statistics cards (using Utils.createStatsManager)
 * - SearchManager: Handles advanced search
 * - TermManager: Handles CRUD and actions for terms
 * - AcademicTermApp: Initializes all managers
 * 
 * NOTE: Uses global Utils from public/js/utils.js
 */

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    terms: {
        start: '{{ route('academic.academic_terms.start', ':id') }}',
        end: '{{ route('academic.academic_terms.end', ':id') }}',
        activate: '{{ route('academic.academic_terms.activate', ':id') }}',
        deactivate: '{{ route('academic.academic_terms.deactivate', ':id') }}',
        stats: '{{ route('academic.academic_terms.stats') }}',
        store: '{{ route('academic.academic_terms.store') }}',
        show: '{{ route('academic.academic_terms.show', ':id') }}',
        destroy: '{{ route('academic.academic_terms.destroy', ':id') }}',
        all: '{{ route('academic.academic_terms.all') }}'
    }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
    request: function(options) {
        var defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        };
        return $.ajax($.extend(defaultOptions, options));
    },

    fetchTermStats: function() {
        return ApiService.request({ url: ROUTES.terms.stats, method: 'GET' });
    },

    fetchTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.show, id), method: 'GET' });
    },

    fetchAll: function() {
        return ApiService.request({ url: ROUTES.terms.all, method: 'GET' });
    },

    saveTerm: function(formData, id) {
        var url = id ? Utils.replaceRouteId(ROUTES.terms.show,academicTermId, id) : ROUTES.terms.store;
        if (id) {
            formData.append('_method', 'PUT');
        }
        return ApiService.request({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false
        });
    },

    deleteTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.destroy, id), method: 'DELETE' });
    },

    startTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.start, id), method: 'POST' });
    },

    endTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.end, id), method: 'POST' });
    },

    activateTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.activate, id), method: 'PATCH' });
    },

    deactivateTerm: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.terms.deactivate, id), method: 'PATCH' });
    }
};


// ===========================
// STATISTICS MANAGER (Using Utils)
// ===========================
const StatsManager = Utils.createStatsManager({
    apiMethod: ApiService.fetchTermStats,
    statsKeys: ['terms', 'active', 'inactive', 'current'],
    onError: 'Failed to load term statistics'
});

// ===========================
// SEARCH MANAGER
// ===========================
var SearchManager = {
    /**
     * Generate academic years array
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
     * Populate search dropdowns with data
     */
    populateSearchDropdowns: function() {
        var academicYears = SearchManager.generateAcademicYears();
        var $yearSelect = $('#search_year');
        
        // Clear all options except the first
        $yearSelect.find('option:not(:first)').remove();

        // Populate academic years using Utils
        Utils.populateSelect($yearSelect, academicYears, {
            valueField: null,
            textField: null,
            includePlaceholder: true,
            placeholder: 'Select Year',
        });

        // Fetch terms for codes dropdown
        ApiService.fetchAll()
            .done(function(response) {
                var terms = response.data || response;

                if (Array.isArray(terms)) {
                    var uniqueCodes = [...new Set(
                        terms.map(function(term) {
                            return term.code;
                        }).filter(Boolean)
                    )].sort();

                    var $codeSelect = $('#search_code');
                    Utils.populateSelect($codeSelect, uniqueCodes, {
                        includePlaceholder: false
                    });
                }
            })
            .fail(function(xhr) {
                console.warn('Failed to load term codes for search dropdown:', xhr.responseJSON?.message || xhr.statusText);
            });
    },

    /**
     * Bind search-related event handlers
     */
    bindEvents: function() {
        // Immediate search for dropdowns
        $('#search_season, #search_year, #search_active').on('change', function() {
            Utils.reloadDataTable('#terms-table');
        });
        
        // Clear filters
        $('#clearTermFiltersBtn').on('click', function() {
            $('#search_season, #search_year, #search_active').val('');
            Utils.reloadDataTable('#terms-table');
        });
    },
    
    /**
     * Initialize SearchManager
     */
    init: function() {
        SearchManager.populateSearchDropdowns();
        SearchManager.bindEvents();
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
            Utils.clearValidation($('#termForm'));
            $('#termModal').modal('show');
        });
    },
    
    /**
     * Validates form fields using Utils
     * @param {Object} formData
     * @returns {Object}
     */
    validateForm: function(formData) {
        var errors = [];
        
        if (Utils.isEmpty(formData.season)) errors.push('Season is required');
        if (Utils.isEmpty(formData.year)) {
            errors.push('Year is required');
        } else if (!/^\d{4}-\d{4}$/.test(formData.year)) {
            errors.push('Year must be in format YYYY-YYYY (e.g., 2024-2025)');
        }
        if (Utils.isEmpty(formData.semester_number)) errors.push('Semester number is required');
        if (Utils.isEmpty(formData.start_date)) errors.push('Start date is required');
        
        if (!Utils.isEmpty(formData.start_date) && !Utils.isEmpty(formData.end_date)) {
            var startDate = new Date(formData.start_date);
            var endDate = new Date(formData.end_date);
            if (endDate <= startDate) errors.push('End date must be after start date');
        }
        
        return { isValid: errors.length === 0, errors: errors };
    },
    
    /**
     * Handles term form submission
     */
    handleTermFormSubmit: function() {
        $('#termForm').on('submit', function(e) {
            e.preventDefault();
            
            var termId = $('#term_id').val();
            var formData = new FormData(e.target);
            
            // Convert FormData to object for validation
            var formObject = {};
            formData.forEach(function(value, key) { formObject[key] = value; });
            
            var validation = TermManager.validateForm(formObject);
            if (!validation.isValid) {
                Utils.showErrorHtml('Validation Error', Utils.formatValidationErrors({errors: validation.errors}));
                return;
            }
            
            var $submitBtn = $('#saveTermBtn');
            Utils.setLoadingState($submitBtn, true, {
                loadingText: 'Saving...',
                normalText: $submitBtn.text()
            });
            
            ApiService.saveTerm(formData, termId || null)
                .done(function(response) {
                    $('#termModal').modal('hide');
                    Utils.reloadDataTable('#terms-table', null, true);
                    Utils.showSuccess(response.message || 'Term saved successfully.');
                    StatsManager.refresh();
                    SearchManager.populateSearchDropdowns();
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to save term. Please check your input.');
                })
                .always(function() {
                    Utils.setLoadingState($submitBtn, false, {
                        normalText: termId ? 'Update' : 'Save'
                    });
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
                    Utils.clearValidation($('#termForm'));
                    $('#termModal').modal('show');
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to load term details.');
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
            
            Utils.showConfirmDialog({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                confirmButtonText: 'Yes, delete it!',
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deleteTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message || 'Term has been deleted.');
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, 'Failed to delete term.');
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
            Utils.showConfirmDialog({
                title: 'Start Academic Term?',
                text: 'Are you sure you want to start this academic term? Once started, all confirmed reservations for this term will be activated.',
                confirmButtonText: 'Yes, start term!',
                confirmButtonColor: '#28a745'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.startTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message || 'Term has been started successfully.');
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, 'Failed to start term.');
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
            
            Utils.showConfirmDialog({
                title: 'End Academic Term?',
                text: 'Are you sure you want to end this academic term?',
                confirmButtonText: 'Yes, end term!',
                confirmButtonColor: '#dc3545'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.endTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message || 'Term has been ended.');
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, 'Failed to end term.');
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
            
            Utils.showConfirmDialog({
                title: 'Activate Academic Term?',
                text: 'Are you sure you want to activate this academic term? This will make the term available for reservations.',
                icon: 'question',
                confirmButtonText: 'Yes, activate term!',
                confirmButtonColor: '#28a745'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.activateTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message || 'Term has been activated.');
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, 'Failed to activate term.');
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
            
            Utils.showConfirmDialog({
                title: 'Deactivate Academic Term?',
                text: 'Are you sure you want to deactivate this academic term? This will make the term unavailable for new reservations.',
                confirmButtonText: 'Yes, deactivate term!',
                confirmButtonColor: '#ffc107'
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deactivateTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message || 'Term has been deactivated.');
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, 'Failed to deactivate term.');
                        });
                }
            });
        });
    },
    
    /**
     * Handles View Term button click
     */
    handleViewTerm: function() {
        $(document).on('click', '.viewTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError('Term ID is missing');
                return;
            }
            
            ApiService.fetchTerm(termId)
                .done(function(response) {
                    var term = response.data;
                    $('#view-term-season').text(term.season ?? '--');
                    $('#view-term-year').text(term.year ?? '--');
                    $('#view-term-code').text(term.code ?? '--');
                    $('#view-term-semester-number').text(term.semester_number ?? '--');
                    $('#view-term-start-date').text(term.start_date_formatted ?? '--');
                    $('#view-term-end-date').text(term.end_date_formatted ?? '--');
                    $('#view-term-active').text(term.active ? 'Yes' : 'No');
                    $('#view-term-current').text(term.current ? 'Yes' : 'No');
                    $('#view-term-activated-at').text(term.activated_at_formatted ?? '--');
                    $('#view-term-started-at').text(term.started_at_formatted ?? '--');
                    $('#view-term-ended-at').text(term.ended_at_formatted ?? '--');
                    $('#view-term-created-at').text(term.created_at_formatted ?? '--');
                    $('#view-term-updated-at').text(term.updated_at_formatted ?? '--');
                    $('#viewTermModal').modal('show');
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to load term details.');
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
        this.handleViewTerm();
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