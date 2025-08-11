@extends('layouts.home')

@section('title', __('Academic Terms'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="terms"
                :label="__('Total Terms')"
                color="secondary"
                icon="bx bx-calendar"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="active"
                :label="__('Active Terms')"
                color="success"
                icon="bx bx-check-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="inactive"
                :label="__('Inactive Terms')"
                color="warning"
                icon="bx bx-x-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="current"
                :label="__('Current Term')"
                color="info"
                icon="bx bx-time"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header
        :title="__('Academic Terms')"
        :description="__('Manage academic terms and semesters')"
        icon="bx bx-calendar"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-primary"
                    id="addTermBtn"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#termModal">
                <i class="bx bx-plus me-1"></i> 
                <span class="d-none d-sm-inline">{{ __('Add Term') }}</span>
                <span class="d-inline d-sm-none">{{ __('Add') }}</span>
            </button>
            <button class="btn btn-secondary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#termSearchCollapse"
                    aria-expanded="false"
                    aria-controls="termSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> 
                <span class="d-none d-sm-inline">{{ __('Search') }}</span>
                <span class="d-inline d-sm-none">{{ __('Filter') }}</span>
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search
        :title="__('Search Academic Terms')"
        formId="advancedTermSearch"
        collapseId="termSearchCollapse"
        :collapsed="false"
        :show-clear-button="true"
        :clear-button-text="__('Clear Filters')"
        clear-button-id="clearTermFiltersBtn"
    >
        <div class="col-md-3">
            <label for="search_season" class="form-label">{{ __('Season') }}</label>
            <select class="form-select" id="search_season">
                <option value="">{{ __('All') }}</option>
                <option value="fall">{{ __('Fall') }}</option>
                <option value="spring">{{ __('Spring') }}</option>
                <option value="summer">{{ __('Summer') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_year" class="form-label">{{ __('Academic Year') }}</label>
            <select class="form-select" id="search_year">
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_active" class="form-label">{{ __('Status') }}</label>
            <select class="form-select" id="search_active">
                <option value="">{{ __('All') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </select>
        </div>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table
        :headers=" [
            __('Season'),
            __('Year'),
            __('Code'),
            __('Start Date'),
            __('End Date'),
            __('Reservations'),
            __('Status'),
            __('Action')
        ]"
        :columns=" [
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
        :title="__('Add Academic Term')"
        size="lg"
        :scrollable="false"
        class="term-modal"
    >
        <x-slot name="slot">
            <form id="termForm">
                <input type="hidden" id="term_id" name="term_id">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="season" class="form-label">{{ __('Season') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="season" name="season" required>
                            <option value="">{{ __('Select Season') }}</option>
                            <option value="fall">{{ __('Fall') }}</option>
                            <option value="spring">{{ __('Spring') }}</option>
                            <option value="summer">{{ __('Summer') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="year" class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="year" name="year" required placeholder="{{ __('Enter Year') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="semester_number" class="form-label">{{ __('Semester') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester_number" name="semester_number" required>
                            <option value="">{{ __('Select Semester') }}</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                {{ __('Close') }}
            </button>
            <button type="submit" class="btn btn-primary" id="saveTermBtn" form="termForm">{{ __('Save') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Academic Term Modal --}}
    <x-ui.modal
        id="viewTermModal"
        :title="__('Academic Term Details')"
        size="md"
        :scrollable="true"
        class="view-term-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Season') }}:</label>
                    <p id="view-term-season" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Year') }}:</label>
                    <p id="view-term-year" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Code') }}:</label>
                    <p id="view-term-code" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Semester') }}:</label>
                    <p id="view-term-semester-number" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Start Date') }}:</label>
                    <p id="view-term-start-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('End Date') }}:</label>
                    <p id="view-term-end-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Active') }}:</label>
                    <p id="view-term-active" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Current') }}:</label>
                    <p id="view-term-current" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Activated At') }}:</label>
                    <p id="view-term-activated-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Started At') }}:</label>
                    <p id="view-term-started-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Ended At') }}:</label>
                    <p id="view-term-ended-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}:</label>
                    <p id="view-term-created-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('Updated At') }}:</label>
                    <p id="view-term-updated-at" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
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
// TRANSLATION CONSTANTS
// ===========================
var TRANSLATION = {
    error: {
        termMissing: @json(__('The term is missing')),
        seasonRequired: @json(__('Season is required')),
        yearRequired: @json(__('Year is required')),
        semesterRequired: @json(__('Semester is required')),
        startDateRequired: @json(__('Start Date is required')),
        invalidYearFormat: @json(__('Year format is invalid')),
        invalidDateRange: @json(__('Date range is invalid')),
        validationError: @json(__('Validation Error'))
    },
    confirm: {
        deleteTerm: {
            title: @json(__('Delete Academic Term')),
            text: @json(__('Are you sure you want to delete this academic term?')),
            confirmButtonText: @json(__('Yes, Delete')),
        },
        startTerm: {
            title: @json(__('Start Academic Term')),
            text: @json(__('Are you sure you want to start this academic term?')),
            confirmButtonText: @json(__('Yes, Start'))
        },
        endTerm: {
            title: @json(__('End Academic Term')),
            text: @json(__('Are you sure you want to end this academic term?')),
            confirmButtonText: @json(__('Yes, End'))
        },
        activateTerm: {
            title: @json(__('Activate Academic Term')),
            text: @json(__('Are you sure you want to activate this academic term?')),
            confirmButtonText: @json(__('Yes, Activate'))
        },
        deactivateTerm: {
            title: @json(__('Deactivate Academic Term')),
            text: @json(__('Are you sure you want to deactivate this academic term?')),
            confirmButtonText: @json(__('Yes, Deactivate'))
        },
    },
    placeholders: {
        selectSeason: @json(__('Select Season')),
        selectYear: @json(__('Select Year')),
        selectStatus: @json(__('Select Status')),
        selectSemester: @json(__('Select Semester'))
    },
    modal: {
        addTermTitle: @json(__('Add Academic Term')),
        editTermTitle: @json(__('Edit Academic Term')),
        saveButton: @json(__('Save')),
        updateButton: @json(__('Update')),
        savingButton: @json(__('Saving...')),
        cancelButton: @json(__('Cancel')),
        updatingButton: @json(__('Updating...')),
        yes: @json(__('Yes')),
        no: @json(__('No'))
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
        var url = id ? Utils.replaceRouteId(ROUTES.terms.show, id) : ROUTES.terms.store;
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
});

// ===========================
// SELECT2 MANAGER
// ===========================
var Select2Manager = {
    /**
     * Configuration for all Select2 elements
     */
    config: {
        search: {
            '#search_season': { placeholder: TRANSLATION.placeholders.selectSeason }, 
            '#search_year': { placeholder: TRANSLATION.placeholders.selectYear }, 
            '#search_active': { placeholder: TRANSLATION.placeholders.selectStatus } 
        },
        modal: {
            '#season': { placeholder: TRANSLATION.placeholders.selectSeason, dropdownParent: $('#termModal') }, 
            '#semester_number': { placeholder: TRANSLATION.placeholders.selectSemester, dropdownParent: $('#termModal') } 
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
        this.clearSelect2(['#season', '#semester_number']);
    },

    /**
     * Reset search Select2 elements
     */
    resetSearchSelect2: function() {
        this.clearSelect2(['#search_season', '#search_year', '#search_active']);
    }
};

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
        // Populate academic years using Utils
        Utils.populateSelect('#search_year', academicYears, {
            valueField: null,
            textField: null,
            includePlaceholder: true,
            placeholder: TRANSLATION.placeholders.allYears 
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
            Select2Manager.resetSearchSelect2();
            Utils.reloadDataTable('#terms-table');
        });
    },

    /**
     * Initialize SearchManager
     */
    init: function() {
        SearchManager.populateSearchDropdowns();
        Select2Manager.initSearchSelect2();
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
            Select2Manager.resetModalSelect2();
            $('#termModal .modal-title').text(TRANSLATION.modal.addTermTitle); 
            $('#saveTermBtn').text(TRANSLATION.modal.saveButton); 
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

        if (Utils.isEmpty(formData.season)) errors.push(TRANSLATION.error.seasonRequired); 
        if (Utils.isEmpty(formData.year)) {
            errors.push(TRANSLATION.error.yearRequired); 
        } else if (!/^\d{4}-\d{4}$/.test(formData.year)) {
            errors.push(TRANSLATION.error.invalidYearFormat); 
        }
        if (Utils.isEmpty(formData.semester_number)) errors.push(TRANSLATION.error.semesterRequired); 
        if (Utils.isEmpty(formData.start_date)) errors.push(TRANSLATION.error.startDateRequired); 

        if (!Utils.isEmpty(formData.start_date) && !Utils.isEmpty(formData.end_date)) {
            var startDate = new Date(formData.start_date);
            var endDate = new Date(formData.end_date);
            if (endDate <= startDate) errors.push(TRANSLATION.error.invalidDateRange); 
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

            var formObject = {};
            formData.forEach(function(value, key) { formObject[key] = value; });

            var validation = TermManager.validateForm(formObject);
            if (!validation.isValid) {
                Utils.showErrorHtml(TRANSLATION.error.validationError, Utils.formatValidationErrors({errors: validation.errors})); 
                return;
            }

            var $submitBtn = $('#saveTermBtn');
            Utils.setLoadingState($submitBtn, true, {
                loadingText: TRANSLATION.modal.savingButton, 
                normalText: $submitBtn.text()
            });

            ApiService.saveTerm(formData, termId || null)
                .done(function(response) {
                    $('#termModal').modal('hide');
                    Utils.reloadDataTable('#terms-table', null, true);
                    Utils.showSuccess(response.message); 
                    StatsManager.refresh();
                    SearchManager.populateSearchDropdowns();
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
                })
                .always(function() {
                    Utils.setLoadingState($submitBtn, false, {
                        normalText: termId ? TRANSLATION.modal.updateButton : TRANSLATION.modal.saveButton 
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }
            Utils.setLoadingState($('.editTermBtn'), true, {
                loadingText: TRANSLATION.modal.updatingButton,
                normalText: TRANSLATION.modal.editTermTitle
            });
            ApiService.fetchTerm(termId)
                .done(function(response) {
                    var term = response.data;
                    TermManager.populateModal(term);
                    TermManager.prepareModal();
                    $('#termModal').modal('show');
                    Utils.setLoadingState($('.editTermBtn'), false, {
                        normalText: TRANSLATION.modal.editTermTitle
                    });
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
                    Utils.setLoadingState($('.editTermBtn'), false, {
                        normalText: TRANSLATION.modal.editTermTitle
                    });
                });
        });
    },

    /**
     * Populates the term modal with data
     */
    populateModal: function(term) {
        $('#term_id').val(term.id);
        $('#season').val(term.season_en).trigger('change');
        $('#year').val(term.year);
        $('#semester_number').val(term.semester_number).trigger('change');
        $('#start_date').val(term.start_date ? term.start_date.substring(0, 10) : '');
        $('#end_date').val(term.end_date ? term.end_date.substring(0, 10) : '');
        $('#active').prop('checked', Boolean(term.active));
    },

    /**
     * Prepares the term modal for editing
     */
    prepareModal: function() {
        $('#termModal .modal-title').text(TRANSLATION.modal.editTermTitle); 
        $('#saveTermBtn').text(TRANSLATION.modal.updateButton); 
    },

    /**
     * Handles Delete Term button click
     */
    handleDeleteTerm: function() {
        $(document).on('click', '.deleteTermBtn', function() {
            var termId = $(this).data('id');
            if (!termId) {
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }

            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.deleteTerm.title,
                text: TRANSLATION.confirm.deleteTerm.text,
                confirmButtonText: TRANSLATION.confirm.deleteTerm.confirmButtonText,
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deleteTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }
            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.startTerm.title,
                text: TRANSLATION.confirm.startTerm.text,
                confirmButtonText: TRANSLATION.confirm.startTerm.confirmButtonText,
                
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.startTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }

            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.endTerm.title,
                text: TRANSLATION.confirm.endTerm.text,
                confirmButtonText: TRANSLATION.confirm.endTerm.confirmButtonText,
                
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.endTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }

            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.activateTerm.title,
                text: TRANSLATION.confirm.activateTerm.text,
                confirmButtonText: TRANSLATION.confirm.activateTerm.confirmButtonText,
                icon: 'question',
                
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.activateTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }

            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.deactivateTerm.title,
                text: TRANSLATION.confirm.deactivateTerm.text,
                confirmButtonText: TRANSLATION.confirm.deactivateTerm.confirmButtonText,
                
            }).then(function(result) {
                if (result.isConfirmed) {
                    ApiService.deactivateTerm(termId)
                        .done(function(response) {
                            Utils.reloadDataTable('#terms-table', null, true);
                            Utils.showSuccess(response.message); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
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
                Utils.showError(TRANSLATION.error.termMissing); 
                return;
            }

            ApiService.fetchTerm(termId)
                .done(function(response) {
                    var term = response.data;
                    TermManager.populateViewModal(term);
                    $('#viewTermModal').modal('show');
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr.responseJSON?.message || xhr.statusText);
                });
        });
    },

    /**
     * Populates the view modal with term details
     */
    populateViewModal: (term) => {
        $('#view-term-season').text(term.season ?? '--');
        $('#view-term-year').text(term.year ?? '--');
        $('#view-term-code').text(term.code ?? '--');
        $('#view-term-semester-number').text(term.semester_number ?? '--');
        $('#view-term-start-date').text(term.start_date_formatted ?? '--');
        $('#view-term-end-date').text(term.end_date_formatted ?? '--');
        $('#view-term-active').text(term.active ? TRANSLATION.modal.yes : TRANSLATION.modal.no); 
        $('#view-term-current').text(term.current ? TRANSLATION.modal.yes : TRANSLATION.modal.no); 
        $('#view-term-activated-at').text(term.activated_at_formatted ?? '--');
        $('#view-term-started-at').text(term.started_at_formatted ?? '--');
        $('#view-term-ended-at').text(term.ended_at_formatted ?? '--');
        $('#view-term-created-at').text(term.created_at_formatted ?? '--');
        $('#view-term-updated-at').text(term.updated_at_formatted ?? '--');
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
        Select2Manager.initAll();
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