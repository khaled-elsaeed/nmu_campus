@extends('layouts.home')

@section('title', __('Student Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-user" :label="__('Total Students')" id="students" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('Male Students')" id="students-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="pink" icon="bx bx-female" :label="__('Female Students')" id="students-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header 
        :title="__('Students')"
        :description="__('Manage all student residents and their data')"
        icon="bx bx-user"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#studentSearchCollapse" aria-expanded="false" aria-controls="studentSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search 
        :title="__('Advanced Student Search')" 
        formId="advancedStudentSearch" 
        collapseId="studentSearchCollapse"
        collapsed="false"
    >
        <div class="col-md-4">
            <label for="search_id" class="form-label">{{ __('Student ID') }}</label>
            <input type="text" class="form-control" id="search_id" placeholder="{{ __('Enter ID') }}">
        </div>
        <div class="col-md-4">
            <label for="search_name" class="form-label">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="search_name" placeholder="{{ __('Enter name') }}">
        </div>
        <div class="col-md-4">
            <label for="search_gender" class="form-label">{{ __('Gender') }}</label>
            <select class="form-control" id="search_gender">
                <option value="">{{ __('All Genders') }}</option>
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
                <option value="other">{{ __('Other') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_governorate_id" class="form-label">{{ __('Governorate') }}</label>
            <select class="form-control" id="search_governorate_id">
                <option value="">{{ __('All Governorates') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="faculty_id" class="form-label">{{ __('Faculty') }}</label>
            <select class="form-control" id="faculty_id">
                <option value="">{{ __('All Faculties') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearStudentFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[ __('Academic ID'), __('Name'), __('Phone'), __('Gender'), __('Level'), __('Faculty'), __('Actions') ]"
        :columns=" [
            ['data' => 'academic_id', 'name' => 'academic_id'],
            ['data' => 'name_en', 'name' => 'name_en'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'gender', 'name' => 'gender'],
            ['data' => 'level', 'name' => 'level'],
            ['data' => 'faculty', 'name' => 'faculty'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('resident.students.datatable')"
        :table-id="'students-table'"
        :filter-fields="['search_id', 'search_name', 'search_gender', 'faculty_id', 'search_governorate_id']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- View Student Modal --}}
    <x-ui.modal id="viewStudentModal" :title="__('Student Details')" :scrollable="true" class="view-student-modal">
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Academic ID') }}</label>
                    <p id="view-student-academic-id" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Name') }}</label>
                    <p id="view-student-name" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Academic Email') }}</label>
                    <p id="view-student-academic-email" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Phone') }}</label>
                    <p id="view-student-phone" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Gender') }}</label>
                    <p id="view-student-gender" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Level') }}</label>
                    <p id="view-level-year" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Faculty') }}</label>
                    <p id="view-student-faculty" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Program') }}</label>
                    <p id="view-student-program" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Profile Complete') }}</label>
                    <p id="view-student-profile-complete" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}</label>
                    <p id="view-student-created" class="mb-0"></p>
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
 * Student Management Page JS
 *
 * Structure:
 * - Utils: Common utility functions (from utils.js)
 * - ApiService: Handles all AJAX requests
 * - SelectManager: Handles dropdown population
 * - StudentManager: Handles actions for students (view only)
 * - SearchManager: Handles advanced search
 * - StatsManager: Handles statistics cards
 * - StudentApp: Initializes all managers
 */

// ===========================
// TRANSLATIONS
// ===========================
var TRANSLATION = {
    placeholders: {
        all_governorates: @json(__('All Governorates')),
        all_faculties: @json(__('All Faculties')),
    },
    general: {
        na: @json(__('N/A')),
        yes: @json(__('Yes')),
        no: @json(__('No')),
    }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    students: {
        show: '{{ route('resident.students.show', ':id') }}',
        datatable: '{{ route('resident.students.datatable') }}',
        stats: '{{ route('resident.students.stats') }}'
    },
    governorates: {
        all: '{{ route('governorates.all') }}'
    },
    programs: {
        all: '{{ route('academic.programs.all', ':id') }}'
    },
    cities: {
        all: '{{ route('cities.all', ':governorateId') }}'
    },
    faculties: {
        all: '{{ route('academic.faculties.all') }}'
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
    request: function(options) { return $.ajax(options); },
    /**
     * Fetch a student by ID
     * @param {string|number} id
     * @returns {jqXHR}
     */
    fetchStudent: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.students.show, id), method: 'GET' });
    },
    /**
     * Fetch all governorates
     * @returns {jqXHR}
     */
    fetchGovernorates: function() {
        return ApiService.request({ url: ROUTES.governorates.all, method: 'GET' });
    },
    /**
     * Fetch all cities for a governorate
     * @param {string|number} governorateId
     * @returns {jqXHR}
     */
    fetchCities: function(governorateId) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.cities.all, governorateId), method: 'GET' });
    },
    /**
     * Fetch all faculties
     * @returns {jqXHR}
     */
    fetchFaculties: function() {
        return ApiService.request({ url: ROUTES.faculties.all, method: 'GET' });
    },
    /**
     * Fetch all programs for a faculty
     * @param {string|number} facultyId
     * @returns {jqXHR}
     */
    fetchPrograms: function(facultyId) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.programs.all, facultyId), method: 'GET', data: { faculty_id: facultyId } });
    },
    /**
     * Fetch student statistics
     * @returns {jqXHR}
     */
    fetchStats: function() {
        return ApiService.request({ url: ROUTES.students.stats, method: 'GET' });
    }
};

// ===========================
// SELECT MANAGER
// ===========================
var SelectManager = {
    /**
     * Populate governorates in search
     */
    populateSearchGovernorates: function() {
        var $select = $('#search_governorate_id');
        $select.empty();
        ApiService.fetchGovernorates()
            .done(function(response) {
                if (response.success) {
                    Utils.populateSelect($select, response.data, { 
                        valueField: 'id', 
                        textField: 'name', 
                        placeholder: TRANSLATION.placeholders.all_governorates 
                    });
                }
            })
            .fail(function(xhr) {
                Utils.populateSelect($select, [], { placeholder: TRANSLATION.error.load_governorates });
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            });
    },
    /**
     * Populate faculties in search
     */
    populateSearchFaculties: function() {
        var $select = $('#faculty_id');
        $select.empty();
        ApiService.fetchFaculties()
            .done(function(response) {
                if (response.success) {
                    Utils.populateSelect($select, response.data, { 
                        valueField: 'id', 
                        textField: 'name', 
                        placeholder: TRANSLATION.placeholders.all_faculties 
                    });
                }
            })
            .fail(function(xhr) {
                Utils.populateSelect($select, [], { placeholder: TRANSLATION.error.load_faculties });
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            });
    },
    /**
     * Initialize select manager
     */
    init: function() {
        this.populateSearchGovernorates();
        this.populateSearchFaculties();
    }
};

// ===========================
// STUDENT MANAGER
// ===========================
var StudentManager = {
    /**
     * Bind view student button
     */
    handleView: function() {
        $(document).on('click', '.viewStudentBtn', function() {
            var studentId = $(this).data('id');
            ApiService.fetchStudent(studentId)
                .done(function(response) {
                    if (response.success) {
                        var student = response.data;
                        $('#view-student-academic-id').text(student.academic_id || TRANSLATION.general.na);
                        $('#view-student-name').text(student.name_en || TRANSLATION.general.na);
                        $('#view-student-academic-email').text(student.academic_email || TRANSLATION.general.na);
                        $('#view-student-phone').text(student.phone || TRANSLATION.general.na);
                        $('#view-student-gender').text(student.gender || TRANSLATION.general.na);
                        $('#view-level-year').text(student.level || TRANSLATION.general.na);
                        $('#view-student-faculty').text(student.faculty || TRANSLATION.general.na);
                        $('#view-student-program').text(student.program || TRANSLATION.general.na);
                        $('#view-student-profile-complete').text(student.is_profile_complete ? TRANSLATION.general.yes : TRANSLATION.general.no);
                        $('#view-student-created').text(student.created_at ? new Date(student.created_at).toLocaleString() : TRANSLATION.general.na);
                        $('#viewStudentModal').modal('show');
                    }
                })
                .fail(function(xhr) {
                    $('#viewStudentModal').modal('hide');
                    Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
                });
        });
    },
    /**
     * Initialize all student manager handlers
     */
    init: function() {
        this.handleView();
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
    },
    /**
     * Bind search and clear events
     */
    bindEvents: function() {
        $('#search_id, #search_name, #search_gender, #search_governorate_id, #faculty_id').on('keyup change', function() {
            Utils.reloadDataTable('#students-table');
        });
        $('#clearStudentFiltersBtn').on('click', function() {
            $('#search_id, #search_name, #search_gender, #search_governorate_id, #faculty_id').val('');
            Utils.reloadDataTable('#students-table');
        });
    }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
    apiMethod: ApiService.fetchStats,
    statsKeys: ['students', 'students-male', 'students-female'],
});

// ===========================
// MAIN APP INITIALIZER
// ===========================
var StudentApp = {
    /**
     * Initialize all managers
     */
    init: function() {
        StudentManager.init();
        SearchManager.init();
        SelectManager.init();
        StatsManager.init();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
    StudentApp.init();
});
</script>
@endpush
