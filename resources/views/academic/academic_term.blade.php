@extends('layouts.home')

@section('title', __('academic_terms.page_title'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="terms"
                :label="__('academic_terms.stats.total_terms')"
                color="secondary"
                icon="bx bx-calendar"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="active"
                :label="__('academic_terms.stats.active_terms')"
                color="success"
                icon="bx bx-check-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="inactive"
                :label="__('academic_terms.stats.inactive_terms')"
                color="warning"
                icon="bx bx-x-circle"
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-ui.card.stat2
                id="current"
                :label="__('academic_terms.stats.current_term')"
                color="info"
                icon="bx bx-time"
            />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header
        :title="__('academic_terms.header.title')"
        :description="__('academic_terms.header.description')"
        icon="bx bx-calendar"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-primary"
                    id="addTermBtn"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#termModal">
                <i class="bx bx-plus me-1"></i> 
                <span class="d-none d-sm-inline">{{ __('academic_terms.buttons.add_term') }}</span>
                <span class="d-inline d-sm-none">{{ __('academic_terms.buttons.add') }}</span>
            </button>
            <button class="btn btn-secondary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#termSearchCollapse"
                    aria-expanded="false"
                    aria-controls="termSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> 
                <span class="d-none d-sm-inline">{{ __('academic_terms.buttons.search') }}</span>
                <span class="d-inline d-sm-none">{{ __('academic_terms.buttons.filter') }}</span>
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search
        :title="__('academic_terms.search.title')"
        formId="advancedTermSearch"
        collapseId="termSearchCollapse"
        :collapsed="false"
        :show-clear-button="true"
        :clear-button-text="__('academic_terms.buttons.clear_filters')"
        clear-button-id="clearTermFiltersBtn"
    >
        <div class="col-md-3">
            <label for="search_season" class="form-label">{{ __('academic_terms.search.labels.season') }}</label>
            <select class="form-select" id="search_season">
                <option value="">{{ __('academic_terms.search.placeholders.all_seasons') }}</option>
                <option value="fall">{{ __('academic_terms.search.options.fall') }}</option>
                <option value="spring">{{ __('academic_terms.search.options.spring') }}</option>
                <option value="summer">{{ __('academic_terms.search.options.summer') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_year" class="form-label">{{ __('academic_terms.search.labels.academic_year') }}</label>
            <select class="form-select" id="search_year">
                <option value="">{{ __('academic_terms.search.placeholders.all_years') }}</option>
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>
        <div class="col-md-3">
            <label for="search_active" class="form-label">{{ __('academic_terms.search.labels.status') }}</label>
            <select class="form-select" id="search_active">
                <option value="">{{ __('academic_terms.search.placeholders.all_status') }}</option>
                <option value="1">{{ __('general.active') }}</option>
                <option value="0">{{ __('general.inactive') }}</option>
            </select>
        </div>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table
        :headers="[
            __('academic_terms.table.headers.season'),
            __('academic_terms.table.headers.year'),
            __('academic_terms.table.headers.code'),
            __('academic_terms.table.headers.start_date'),
            __('academic_terms.table.headers.end_date'),
            __('academic_terms.table.headers.reservations'),
            __('academic_terms.table.headers.status'),
            __('academic_terms.table.headers.action')
        ]"
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
        :title="__('academic_terms.modal.add_term_title')"
        size="lg"
        :scrollable="false"
        class="term-modal"
    >
        <x-slot name="slot">
            <form id="termForm">
                <input type="hidden" id="term_id" name="term_id">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="season" class="form-label">{{ __('academic_terms.form.labels.season') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="season" name="season" required>
                            <option value="">{{ __('academic_terms.search.placeholders.select_season') }}</option>
                            <option value="fall">{{ __('academic_terms.search.options.fall') }}</option>
                            <option value="spring">{{ __('academic_terms.search.options.spring') }}</option>
                            <option value="summer">{{ __('academic_terms.search.options.summer') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="year" class="form-label">{{ __('academic_terms.form.labels.year') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="year" name="year" required placeholder="{{ __('academic_terms.form.placeholders.year') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="semester_number" class="form-label">{{ __('academic_terms.form.labels.semester_number') }} <span class="text-danger">*</span></label>
                        <select class="form-select" id="semester_number" name="semester_number" required>
                            <option value="">{{ __('academic_terms.search.placeholders.select_semester') }}</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">{{ __('academic_terms.form.labels.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">{{ __('academic_terms.form.labels.end_date') }}</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                {{ __('academic_terms.buttons.close') }}
            </button>
            <button type="submit" class="btn btn-primary" id="saveTermBtn" form="termForm">{{ __('academic_terms.modal.save_button') }}</button>
        </x-slot>
    </x-ui.modal>

    {{-- View Academic Term Modal --}}
    <x-ui.modal
        id="viewTermModal"
        :title="__('academic_terms.modal.view_term_title')"
        size="md"
        :scrollable="true"
        class="view-term-modal"
    >
        <x-slot name="slot">
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.season') }}:</label>
                    <p id="view-term-season" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.year') }}:</label>
                    <p id="view-term-year" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.code') }}:</label>
                    <p id="view-term-code" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.semester_number') }}:</label>
                    <p id="view-term-semester-number" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.start_date') }}:</label>
                    <p id="view-term-start-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.end_date') }}:</label>
                    <p id="view-term-end-date" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('general.active') }}:</label>
                    <p id="view-term-active" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.current') }}:</label>
                    <p id="view-term-current" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.activated_at') }}:</label>
                    <p id="view-term-activated-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.started_at') }}:</label>
                    <p id="view-term-started-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.ended_at') }}:</label>
                    <p id="view-term-ended-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.created_at') }}:</label>
                    <p id="view-term-created-at" class="mb-0"></p>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">{{ __('academic_terms.form.labels.updated_at') }}:</label>
                    <p id="view-term-updated-at" class="mb-0"></p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('academic_terms.buttons.close') }}</button>
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
    success: {
        termCreated: @json(__('academic_terms.messages.success.created')),
        termUpdated: @json(__('academic_terms.messages.success.updated')),
        termDeleted: @json(__('academic_terms.messages.success.deleted')),
        termActivated: @json(__('academic_terms.messages.success.activated')),
        termDeactivated: @json(__('academic_terms.messages.success.deactivated')),
        termStarted: @json(__('academic_terms.messages.success.started')), 
        termEnded: @json(__('academic_terms.messages.success.ended')) 
    },
    error: {
        statsLoadFailed: @json(__('academic_terms.messages.error.stats_load_failed')),
        loadFailed: @json(__('academic_terms.messages.error.load_failed')),
        saveFailed: @json(__('academic_terms.messages.error.save_failed')),
        deleteFailed: @json(__('academic_terms.messages.error.delete_failed')),
        termMissing: @json(__('academic_terms.messages.error.term_missing')), 
        startFailed: @json(__('academic_terms.messages.error.start_failed')), 
        endFailed: @json(__('academic_terms.messages.error.end_failed')), 
        activateFailed: @json(__('academic_terms.messages.error.activate_failed')), 
        deactivateFailed: @json(__('academic_terms.messages.error.deactivate_failed')),
        seasonRequired: @json(__('academic_terms.messages.error.season_required')),
        yearRequired: @json(__('academic_terms.messages.error.year_required')),
        semesterRequired: @json(__('academic_terms.messages.error.semester_required')),
        startDateRequired: @json(__('academic_terms.messages.error.start_date_required')),
        invalidYearFormat: @json(__('academic_terms.messages.error.invalid_year_format')),
        invalidDateRange: @json(__('academic_terms.messages.error.invalid_date_range')),
        validationError: @json(__('general.validation_error'))
    },
    confirm: {
        deleteTerm: {
            title: @json(__('academic_terms.confirm.delete.title')),
            text: @json(__('academic_terms.confirm.delete.text')),
            confirmButtonText: @json(__('academic_terms.confirm.delete.button')),
        },
        startTerm: {
            title: @json(__('academic_terms.confirm.start.title')),
            text: @json(__('academic_terms.confirm.start.text')),
            confirmButtonText: @json(__('academic_terms.confirm.start.button'))
        },
        endTerm: {
            title: @json(__('academic_terms.confirm.end.title')),
            text: @json(__('academic_terms.confirm.end.text')),
            confirmButtonText: @json(__('academic_terms.confirm.end.button'))
        },
        activateTerm: {
            title: @json(__('academic_terms.confirm.activate.title')), 
            text: @json(__('academic_terms.confirm.activate.text')), 
            confirmButtonText: @json(__('academic_terms.confirm.activate.button')) 
        },
        deactivateTerm: {
            title: @json(__('academic_terms.confirm.deactivate.title')), 
            text: @json(__('academic_terms.confirm.deactivate.text')), 
            confirmButtonText: @json(__('academic_terms.confirm.deactivate.button')) 
        },
    },
    placeholders: {
        allSeasons: @json(__('academic_terms.search.placeholders.all_seasons')),
        allYears: @json(__('academic_terms.search.placeholders.all_years')),
        allStatus: @json(__('academic_terms.search.placeholders.all_status')),
        selectSeason: @json(__('academic_terms.search.placeholders.select_season')), 
        selectYear: @json(__('academic_terms.search.placeholders.select_year')), 
        selectStatus: @json(__('academic_terms.search.placeholders.select_status')), 
        selectSemester: @json(__('academic_terms.search.placeholders.select_semester')) 
    },
    modal: {
        addTermTitle: @json(__('academic_terms.modal.add_term_title')), 
        editTermTitle: @json(__('academic_terms.modal.edit_term_title')), 
        saveButton: @json(__('academic_terms.modal.save_button')), 
        updateButton: @json(__('academic_terms.modal.update_button')),
        saving: @json(__('general.saving')),
        updating: @json(__('general.updating')),
        yes: @json(__('general.yes')),
        no: @json(__('general.no'))
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
    onError: TRANSLATION.error.statsLoadFailed
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
                loadingText: TRANSLATION.modal.saving, 
                normalText: $submitBtn.text()
            });

            ApiService.saveTerm(formData, termId || null)
                .done(function(response) {
                    $('#termModal').modal('hide');
                    Utils.reloadDataTable('#terms-table', null, true);
                    Utils.showSuccess(response.message || (termId ? TRANSLATION.success.termUpdated : TRANSLATION.success.termCreated)); 
                    StatsManager.refresh();
                    SearchManager.populateSearchDropdowns();
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, TRANSLATION.error.saveFailed); 
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
            ApiService.fetchTerm(termId)
                .done(function(response) {
                    var term = response.data;
                    TermManager.populateModal(term);
                    TermManager.prepareModal();
                    $('#termModal').modal('show');
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, TRANSLATION.error.loadFailed); 
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
                            Utils.showSuccess(response.message || TRANSLATION.success.termDeleted); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, TRANSLATION.error.deleteFailed); 
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
                            Utils.showSuccess(response.message || TRANSLATION.success.termStarted); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, TRANSLATION.error.startFailed); 
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
                            Utils.showSuccess(response.message || TRANSLATION.success.termEnded); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, TRANSLATION.error.endFailed); 
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
                            Utils.showSuccess(response.message || TRANSLATION.success.termActivated); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, TRANSLATION.error.activateFailed); 
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
                            Utils.showSuccess(response.message || TRANSLATION.success.termDeactivated); 
                            StatsManager.refresh();
                            SearchManager.populateSearchDropdowns();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, TRANSLATION.error.deactivateFailed); 
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
                    Utils.handleAjaxError(xhr, TRANSLATION.error.loadFailed); 
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