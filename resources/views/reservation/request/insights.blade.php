@extends('layouts.home')

@section('title', __('Reservation Requests Analysis'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- ===== PAGE HEADER ===== --}}
    <x-ui.page-header 
        :title="__('Reservation Requests Analysis')"
        :description="__('Comprehensive analysis and insights of reservation requests.')"
        icon="bx bx-bar-chart-alt-2"
    >
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-primary" id="refreshAnalyticsBtn" type="button">
                <i class="bx bx-refresh me-1"></i> {{ __('Refresh Data') }}
            </button>
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#analyticsFiltersCollapse" aria-expanded="false">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Filters') }}
            </button>
        </div>
    </x-ui.page-header>
    

    {{-- ===== ANALYTICS FILTERS ===== --}}
    <x-ui.advanced-search 
        :title="__('Analytics Filters')" 
        formId="analyticsFilters" 
        collapseId="analyticsFiltersCollapse"
        :collapsed="true"
    >
        <div class="row">
            <div class="col-md-3">
                <label for="filter_academic_term" class="form-label">{{ __('Academic Term') }}:</label>
                <select class="form-control" id="filter_academic_term" name="filter_academic_term">
                    <option value="">{{ __('All Terms') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_status" class="form-label">{{ __('Status') }}:</label>
                <select class="form-control" id="filter_status" name="filter_status">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="confirmed">{{ __('Confirmed') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_date_from" class="form-label">{{ __('Date From') }}:</label>
                <input type="date" class="form-control" id="filter_date_from" name="filter_date_from">
            </div>
            <div class="col-md-3">
                <label for="filter_date_to" class="form-label">{{ __('Date To') }}:</label>
                <input type="date" class="form-control" id="filter_date_to" name="filter_date_to">
            </div>
            <div class="col-md-3">
                <label for="filter_gender" class="form-label">{{ __('Gender') }}:</label>
                <select class="form-control" id="filter_gender" name="filter_gender">
                    <option value="">{{ __('All Genders') }}</option>
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_faculty" class="form-label">{{ __('Faculty') }}:</label>
                <select class="form-control" id="filter_faculty" name="filter_faculty">
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_governorate" class="form-label">{{ __('Governorate') }}:</label>
                <select class="form-control" id="filter_governorate" name="filter_governorate">
                </select>
            </div>
            <div class="w-100"></div>
            <div class="col-12">
                <button class="btn btn-outline-secondary mt-2 ms-2" id="clearAnalyticsFiltersBtn" type="button">
                    <i class="bx bx-x"></i> {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </x-ui.advanced-search>


    {{-- ===== ANALYTICS STATUS CARDS ===== --}}
    <div class="row mb-4 g-2">
        <div class="col-12 col-sm-6 col-lg-3">
            <x-ui.card.stat2 
                color="warning"
                icon="bx bx-time"
                :label="__('Total')"
                id="reservation-requests"
                :subStats="[
                    'male' => [
                        'label' => __('Total Male'),
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => __('Total Female'), 
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <x-ui.card.stat2 
                color="warning"
                icon="bx bx-time"
                :label="__('Pending')"
                id="reservation-requests-pending"
                :subStats="[
                    'male' => [
                        'label' => __('Male Pending'),
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => __('Female Pending'), 
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <x-ui.card.stat2 
                color="success"
                icon="bx bx-check-circle"
                :label="__('Approved')"
                id="reservation-requests-approved"
                :subStats="[
                    'male' => [
                        'label' => __('Male Approved'),
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => __('Female Approved'), 
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <x-ui.card.stat2 
                color="danger"
                icon="bx bx-x-circle"
                :label="__('Rejected')"
                id="reservation-requests-rejected"
                :subStats="[
                    'male' => [
                        'label' => __('Male Rejected'),
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => __('Female Rejected'), 
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
    </div>
    <div class="row mb-4 g-2">
        <div class="col-12 col-sm-6 col-lg-3">
            <x-ui.card.stat2 
                color="secondary"
                icon="bx bx-block"
                :label="__('Cancelled')"
                id="reservation-requests-cancelled"
                :subStats="[
                    'male' => [
                        'label' => __('Male Cancelled'),
                        'icon' => 'bx bx-male-sign',
                        'color' => 'info'
                    ],
                    'female' => [
                        'label' => __('Female Cancelled'), 
                        'icon' => 'bx bx-female-sign',
                        'color' => 'danger'
                    ]
                ]"
            />
        </div>
    </div>

    {{-- ===== ANALYTICS CARDS ROW ===== --}}
    <div class="row mb-4">
        {{-- GENDER CHART --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Gender Distribution') }}</h5>
                    <div id="gender-loader" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <canvas id="gender-chart" height="300"></canvas>
                    <div id="gender-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROOM TYPE PREFERENCE CHART --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Room Type Preferences') }}</h5>
                    <div id="room-type-loader" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <canvas id="room-type-chart" height="300"></canvas>
                    <div id="room-type-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- STAY WITH SIBLING CHART --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Stay With Sibling') }}</h5>
                    <div id="sibling-loader" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <canvas id="sibling-chart" height="300"></canvas>
                    <div id="sibling-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PARENT ABROAD CHART --}}
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Parent Abroad') }}</h5>
                    <div id="parent-abroad-loader" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <canvas id="parent-abroad-chart" height="300"></canvas>
                    <div id="parent-abroad-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FACULTY ANALYSIS ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Faculty Distribution') }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div id="faculty-loader" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" id="toggleFacultyView">
                            <i class="bx bx-transfer-alt me-1"></i> {{ __('Toggle View') }}
                        </button>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <div id="faculty-chart-container">
                        <canvas id="faculty-chart" height="400"></canvas>
                    </div>
                    <div id="faculty-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- ===== GOVERNORATE ANALYSIS ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Governorate Distribution') }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div id="governorate-loader" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <button class="btn btn-sm btn-primary" id="toggleGovernorateView">
                            <i class="bx bx-transfer-alt me-1"></i> {{ __('Toggle View') }}
                        </button>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <div id="governorate-chart-container">
                        <canvas id="governorate-chart" height="400"></canvas>
                    </div>
                    <div id="governorate-no-data" class="chart-no-data d-none">
                        <div class="text-center text-muted">
                            <i class="bx bx-info-circle bx-lg mb-2"></i>
                            <p>{{ __('No data available') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PROGRAMS OF FACULTY ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Faculty and Program Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row" id="facultyList">
                        <!-- Faculty list will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Programs --}}
    <x-ui.modal id="programsModal" title="{{ __('Programs') }}" size="lg">
        <div id="programsModalBody">
            <div class="text-center text-muted py-4">{{ __('Loading...') }}</div>
        </div>
    </x-ui.modal>
    </div>

<style>
.chart-no-data {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
}


.chart-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 20;
}
</style>
@endsection

@push('scripts')
<script>
/**
 * Reservation Requests Analytics Page JS
 */

// ===========================
// TRANSLATION CONSTANTS
// ===========================
var TRANSLATIONS = {
    labels: {
        room: @json(__('Room')),
        apartment: @json(__('Apartment')),
        single: @json(__('Single')),
        double: @json(__('Double')),
        male: @json(__('Male')),
        female: @json(__('Female')),
        pending: @json(__('Pending')),
        confirmed: @json(__('Confirmed')),
        rejected: @json(__('Rejected')),
        withSibling: @json(__('With Siblings')),
        withoutSibling: @json(__('Individual')),
        semester: @json(__('Semester')),
        year: @json(__('Full Year')),
        summer: @json(__('Summer')),
        singleBed: @json(__('Single Bed')),
        doubleBed: @json(__('Both Beds'))
    },
    messages: {
        loadingData: @json(__('Loading analytics data...')),
        errorLoadingData: @json(__('Error loading analytics data')),
    }
};

// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    analytics: {
        stats: '{{ route("reservation-requests.analytics.stats") }}',
        roomTypes: '{{ route("reservation-requests.analytics.room-types") }}',
        faculties: '{{ route("reservation-requests.analytics.faculties") }}',
        governorates: '{{ route("reservation-requests.analytics.governorates") }}',
        programs: '{{ route("reservation-requests.analytics.programs") }}',
        genders: '{{ route("reservation-requests.analytics.genders") }}',
        siblingPreferences: '{{ route("reservation-requests.analytics.sibling-preferences") }}',
        parentAbroad: '{{ route("reservation-requests.analytics.parent-abroad") }}',
    },
    academicTerms: {
        all: '{{ route("academic.academic_terms.all") }}'
    },
    governorates: {
        all: '{{ route("governorates.all") }}'
    },
    faculties: {
        all: '{{ route("academic.faculties.all") }}'
    }
};


// ===========================
// API SERVICE
// ===========================
var ApiService = {
    request: function(url, params = {}) {
        return $.ajax({
            url: url,
            method: 'GET',
            data: params
        });
    },
    fetchStats: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.stats, filters);
    },
    fetchRoomTypes: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.roomTypes, filters);
    },
    fetchGovernorates: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.governorates, filters);
    },
    fetchFaculties: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.faculties, filters);
    },
    fetchPrograms: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.programs, filters);
    },
    fetchGenders: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.genders, filters);
    },
    fetchSiblingPreferences: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.siblingPreferences, filters);
    },
    fetchParentAbroad: function(filters = {}) {
        return ApiService.request(ROUTES.analytics.parentAbroad, filters);
    },
    fetchAcademicTerms: function() {
        return ApiService.request(ROUTES.academicTerms.all);
    },
    fetchGovernoratesForSelect: function() {
        return ApiService.request(ROUTES.governorates.all);
    },
    fetchFacultiesForSelect: function() {
        return ApiService.request(ROUTES.faculties.all);
    }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
  apiMethod: ApiService.fetchStats,
  statsKeys: [
    'reservation-requests',
    'reservation-requests-pending',
    'reservation-requests-approved',
    'reservation-requests-rejected',
    'reservation-requests-cancelled'
  ],
  subStatsConfig: {
    'reservation-requests': ['male', 'female'],
    'reservation-requests-pending': ['male', 'female'],
    'reservation-requests-approved': ['male', 'female'],
    'reservation-requests-rejected': ['male', 'female'],
    'reservation-requests-cancelled': ['male', 'female']
  },
    urlParams: function() {
        return FilterManager.getFilters();
    }
});

// ===========================
// CHART MANAGER
// ===========================
var ChartManager = {
    charts: {},
    
    init: function() {
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.plugins.legend.position = 'bottom';
        Chart.defaults.elements.arc.borderWidth = 2;
        Chart.defaults.elements.bar.borderRadius = 4;
        Chart.defaults.elements.line.tension = 0.4;
    },
    
    getCanvasContext: function(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn(`Canvas with ID '${canvasId}' not found`);
            return null;
        }
        
        if (this.charts[canvasId]) {
            this.destroyChart(canvasId);
        }
        
        return canvas;
    },
    
    createPieChart: function(canvasId, data, options = {}) {
        const canvas = this.getCanvasContext(canvasId);
        if (!canvas) return null;
        
        try {
            const chart = new Chart(canvas, {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: function(value, context) {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${value} (${percentage}%)`;
                            },
                            color: '#333',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    ...options
                },
                plugins: window.ChartDataLabels ? [ChartDataLabels] : []
            });
            this.charts[canvasId] = chart;
            return chart;
        } catch (error) {
            console.error(`Error creating pie chart for ${canvasId}:`, error);
            return null;
        }
    },
    
    createBarChart: function(canvasId, data, options = {}) {
        const canvas = this.getCanvasContext(canvasId);
        if (!canvas) return null;
        
        try {
            const chart = new Chart(canvas, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${context.parsed.y} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            formatter: function(value, context) {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${value} (${percentage}%)`;
                            },
                            color: '#333',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    ...options
                },
                plugins: window.ChartDataLabels ? [ChartDataLabels] : []
            });
            this.charts[canvasId] = chart;
            return chart;
        } catch (error) {
            console.error(`Error creating bar chart for ${canvasId}:`, error);
            return null;
        }
    },
    
    updateChart: function(chartId, newData) {
        if (this.charts[chartId]) {
            try {
                this.charts[chartId].data = newData;
                this.charts[chartId].update();
            } catch (error) {
                console.error(`Error updating chart ${chartId}:`, error);
            }
        }
    },
    
    destroyChart: function(chartId) {
        if (this.charts[chartId]) {
            try {
                this.charts[chartId].destroy();
            } catch (error) {
                console.error(`Error destroying chart ${chartId}:`, error);
            } finally {
                delete this.charts[chartId];
            }
        }
    },
    
    destroyAllCharts: function() {
        Object.keys(this.charts).forEach(chartId => {
            this.destroyChart(chartId);
        });
    },
    
    getChart: function(chartId) {
        return this.charts[chartId] || null;
    }
};

// ===========================
// FILTER MANAGER
// ===========================
var FilterManager = {
    isLoading: false,
    filterTimeout: null,
    
    init: function() {
        this.loadAcademicTerms();
        this.loadGovernorates();
        this.loadFaculties();
        this.bindEvents();
    },
    
    bindEvents: function() {
        const self = this;
        const filterFields = ['#filter_academic_term', '#filter_status', '#filter_date_from', '#filter_date_to', '#filter_gender', '#filter_faculty', '#filter_governorate'];
        
        filterFields.forEach(field => {
            $(field).on('change', function() {
                if (self.isLoading) return;
                
                clearTimeout(self.filterTimeout);
                self.filterTimeout = setTimeout(() => {
                    if (!self.isLoading) {
                        AnalyticsManager.refreshAllData();
                    }
                }, 300);
            });
        });
        
        $('#clearAnalyticsFiltersBtn').on('click', function() {
            if (!self.isLoading) {
                self.clearFilters();
            }
        });
    },
    
    loadAcademicTerms: function() {
        ApiService.fetchAcademicTerms()
            .done(function(response) {
                if (response.success && response.data) {
                    Utils.populateSelect('#filter_academic_term', response.data, {
                        valueField: 'id',
                        textField: 'name',
                        placeholder: '{{ __("All Terms") }}'
                    }, true);
                }
            });
    },

    loadGovernorates: function(){
        ApiService.fetchGovernoratesForSelect()
            .done(function(response) {
                if (response.success && response.data) {
                    Utils.populateSelect('#filter_governorate', response.data, {
                        valueField: 'id',
                        textField: 'name',
                        placeholder: '{{ __("All Governorates") }}'
                    }, true);
                }
            });
    },

    loadFaculties: function(){
        ApiService.fetchFacultiesForSelect()
            .done(function(response) {
                if (response.success && response.data) {
                    Utils.populateSelect('#filter_faculty', response.data, {
                        valueField: 'id',
                        textField: 'name',
                        placeholder: '{{ __("All Faculties") }}'
                    }, true);
                }
            });
    },



    getFilters: function() {
        return {
            academic_term: $('#filter_academic_term').val(),
            status: $('#filter_status').val(),
            date_from: $('#filter_date_from').val(),
            date_to: $('#filter_date_to').val(),
            gender: $('#filter_gender').val(),
            faculty: $('#filter_faculty').val(),
            governorate: $('#filter_governorate').val()
        };
    },
    
    clearFilters: function() {
        this.isLoading = true;
        
        $('#filter_academic_term, #filter_status').val('');
        $('#filter_date_from, #filter_date_to').val('');
        $('#filter_gender, #filter_faculty, #filter_governorate').val('');
        
        setTimeout(() => {
            AnalyticsManager.refreshAllData();
        }, 100);
    },
    
    setLoadingState: function(loading) {
        this.isLoading = loading;
    }
};

// ===========================
// ANALYTICS MANAGER
// ===========================
var AnalyticsManager = {

    isAllCountsZero: function(data, countKey = 'count') {

        if (!Array.isArray(data) || data.length === 0) {
            return true;
        }

        const result = data.every(item => {
            const value = Number(item[countKey]) || 0; // convert to number
            return value === 0;
        });
        return result;
    },



    isLoading: false,
    currentFacultyView: 'bar', // Track current view type
    currentGovernorateView: 'bar', // Track current view type
    
    init: function() {
        this.bindEvents();
        this.loadAllData();
    },
    
    bindEvents: function() {
        const self = this;
        
        $('#refreshAnalyticsBtn').on('click', function() {
            if (!self.isLoading) {
                self.refreshAllData();
            }
        });
        
        $('#toggleFacultyView').on('click', function() {
            if (!self.isLoading) {
                self.toggleFacultyView();
            }
        });
        
        // ADD MISSING GOVERNORATE TOGGLE HANDLER
        $('#toggleGovernorateView').on('click', function() {
            if (!self.isLoading) {
                self.toggleGovernorateView();
            }
        });
    },
    
    loadAllData: function() {
        if (this.isLoading) {
            console.warn('Already loading data, skipping...');
            return;
        }
        
        this.isLoading = true;
        FilterManager.setLoadingState(true);
        
        const filters = FilterManager.getFilters();
        
        // Show all loaders
        Utils.showLoader('gender-loader');
        Utils.showLoader('room-type-loader');
        Utils.showLoader('sibling-loader');
        Utils.showLoader('parent-abroad-loader');
        Utils.showLoader('faculty-loader');
        Utils.showLoader('governorate-loader');

        // Hide all no-data messages
        Utils.hideNoData('gender-no-data');
        Utils.hideNoData('room-type-no-data');
        Utils.hideNoData('sibling-no-data');
        Utils.hideNoData('parent-abroad-no-data');
        Utils.hideNoData('faculty-no-data');
        Utils.hideNoData('governorate-no-data');
        
        let pending = 6;
        const checkAllLoaded = () => {
            pending--;
            if (pending === 0) {
                this.isLoading = false;
                FilterManager.setLoadingState(false);
                StatsManager.refresh();
            }
        };
        
        ApiService.fetchGenders(filters)
            .done(response => {
                try {
                    this.renderGenders(response);
                } catch (error) {
                    console.error('Error rendering genders:', error);
                    Utils.showNoData('gender-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading genders:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('gender-no-data');
            })
            .always(() => {
                Utils.hideLoader('gender-loader');
                checkAllLoaded();
            });
        
        ApiService.fetchRoomTypes(filters)
            .done(response => {
                try {
                    this.renderRoomTypes(response);
                } catch (error) {
                    console.error('Error rendering room types:', error);
                    Utils.showNoData('room-type-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading room types:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('room-type-no-data');
            })
            .always(() => {
                Utils.hideLoader('room-type-loader');
                checkAllLoaded();
            });
        
        ApiService.fetchSiblingPreferences(filters)
            .done(response => {
                try {
                    this.renderSiblingPreferences(response);
                } catch (error) {
                    console.error('Error rendering sibling preferences:', error);
                    Utils.showNoData('sibling-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading sibling preferences:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('sibling-no-data');
            })
            .always(() => {
                Utils.hideLoader('sibling-loader');
                checkAllLoaded();
            });
        
        ApiService.fetchParentAbroad(filters)
            .done(response => {
                try {
                    this.renderParentAbroad(response);
                } catch (error) {
                    console.error('Error rendering parent abroad:', error);
                    Utils.showNoData('parent-abroad-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading parent abroad:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('parent-abroad-no-data');
            })
            .always(() => {
                Utils.hideLoader('parent-abroad-loader');
                checkAllLoaded();
            });
        
        ApiService.fetchFaculties(filters)
            .done(response => {
                try {
                    this.renderFaculties(response);
                } catch (error) {
                    console.error('Error rendering faculties:', error);
                    Utils.showNoData('faculty-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading faculties:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('faculty-no-data');
            })
            .always(() => {
                Utils.hideLoader('faculty-loader');
                checkAllLoaded();
            });
        
        ApiService.fetchGovernorates(filters)
            .done(response => {
                try {
                    this.renderGovernorates(response);
                } catch (error) {
                    console.error('Error rendering governorates:', error);
                    Utils.showNoData('governorate-no-data');
                }
            })
            .fail(error => {
                console.error('Error loading governorates:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
                Utils.showNoData('governorate-no-data');
            })
            .always(() => {
                Utils.hideLoader('governorate-loader');
                checkAllLoaded();
            });
    },
    
    refreshAllData: function() {
        if (this.isLoading) {
            console.warn('Already refreshing data, skipping...');
            return;
        }
        
        console.log('Starting data refresh...');
        ChartManager.destroyAllCharts();
        
        // Reset view types
        this.currentFacultyView = 'bar';
        this.currentGovernorateView = 'bar';
        
        setTimeout(() => {
            this.loadAllData();
        }, 200);
    },
    
    renderGenders: function(response) {
        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => TRANSLATIONS.labels[item.gender] || item.gender),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#03c3ec', '#ff3e1d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            ChartManager.createPieChart('gender-chart', chartData);
            Utils.hideNoData('gender-no-data');
        } else {
            Utils.showNoData('gender-no-data');
        }
    },
    
    renderRoomTypes: function(response) {
        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => TRANSLATIONS.labels[item.room_type] || item.room_type),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#696cff', '#03c3ec', '#ffab00', '#71dd37'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            ChartManager.createPieChart('room-type-chart', chartData);
            Utils.hideNoData('room-type-no-data');
        } else {
            Utils.showNoData('room-type-no-data');
        }
    },
    
    renderSiblingPreferences: function(response) {
        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => item.stay_with_sibling ? 'Yes' : 'No'),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#71dd37', '#ff3e1d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            ChartManager.createPieChart('sibling-chart', chartData);
            Utils.hideNoData('sibling-no-data');
        } else {
            Utils.showNoData('sibling-no-data');
        }
    },

    renderParentAbroad: function(response) {
        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => item.parent_abroad ? 'Yes' : 'No'),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#03c3ec', '#ffab00'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            ChartManager.createPieChart('parent-abroad-chart', chartData);
            Utils.hideNoData('parent-abroad-no-data');
        } else {
            Utils.showNoData('parent-abroad-no-data');
        }
    },
    
    renderFaculties: function(response) {
        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => item.faculty_name),
                datasets: [{
                    label: '{{ __("Requests") }}',
                    data: response.data.map(item => item.count),
                    backgroundColor: '#696cff',
                    borderColor: '#696cff',
                    borderWidth: 1
                }]
            };
            ChartManager.createBarChart('faculty-chart', chartData, {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            });
            this.renderFacultyAccordion(response.data);
            Utils.hideNoData('faculty-no-data');
        } else {
            Utils.showNoData('faculty-no-data');
            $('#facultyList').html('<div class="col-12"><div class="text-center text-muted">No faculty data available</div></div>');
        }
    },

    // FIXED GOVERNORATE RENDERING
    renderGovernorates: function(response) {
        console.log('Governorate response:', response); // Debug log

        if (response.success && response.data && response.data.length > 0 && !this.isAllCountsZero(response.data, 'count')) {
            const chartData = {
                labels: response.data.map(item => item.governorate || item.name || 'Unknown'),
                datasets: [{
                    label: 'Requests', // Use static text or translation
                    data: response.data.map(item => item.count || item.total || 0),
                    backgroundColor: '#03c3ec',
                    borderColor: '#03c3ec',
                    borderWidth: 1
                }]
            };
            const success = ChartManager.createBarChart('governorate-chart', chartData, {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide legend for cleaner look
                    }
                }
            });
            if (success) {
                Utils.hideNoData('governorate-no-data');
                console.log('Governorate chart created successfully');
            } else {
                Utils.showNoData('governorate-no-data');
                console.error('Failed to create governorate chart');
            }
        } else {
            console.log('No governorate data available:', response);
            Utils.showNoData('governorate-no-data');
        }
    },

    // ADD MISSING GOVERNORATE TOGGLE METHOD
    toggleGovernorateView: function() {
        if (this.isLoading) return;
        
        const currentChart = ChartManager.getChart('governorate-chart');
        if (!currentChart) {
            console.warn('No governorate chart found to toggle');
            return;
        }
        
        const isBar = this.currentGovernorateView === 'bar';
        const newType = isBar ? 'pie' : 'bar';
        
        // Store the current data before destroying
        const currentData = currentChart.data;
        
        // Show loader
        Utils.showLoader('governorate-loader');
        
        ChartManager.destroyChart('governorate-chart');
        
        // Small delay before recreating
        setTimeout(() => {
            const chartData = {
                labels: currentData.labels,
                datasets: [{
                    label: 'Requests',
                    data: currentData.datasets[0].data,
                    backgroundColor: newType === 'pie' ? 
                        ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d', '#8592a3', '#233446', '#28a745', '#dc3545', '#ffc107'] :
                        '#03c3ec',
                    borderColor: newType === 'pie' ? '#fff' : '#03c3ec',
                    borderWidth: newType === 'pie' ? 2 : 1
                }]
            };
            
            let success = false;
            if (newType === 'pie') {
                success = ChartManager.createPieChart('governorate-chart', chartData);
            } else {
                success = ChartManager.createBarChart('governorate-chart', chartData, {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                });
            }
            
            if (success) {
                this.currentGovernorateView = newType;
                console.log('Governorate view toggled to:', newType);
            }
            
            // Hide loader
            Utils.hideLoader('governorate-loader');
        }, 100);
    },
    
    toggleFacultyView: function() {
        if (this.isLoading) return;
        
        const currentChart = ChartManager.getChart('faculty-chart');
        if (!currentChart) {
            console.warn('No faculty chart found to toggle');
            return;
        }
        
        const isBar = this.currentFacultyView === 'bar';
        const newType = isBar ? 'pie' : 'bar';
        
        // Store the current data before destroying
        const currentData = currentChart.data;
        
        // Show loader
        Utils.showLoader('faculty-loader');
        
        ChartManager.destroyChart('faculty-chart');
        
        // Small delay before recreating
        setTimeout(() => {
            const chartData = {
                labels: currentData.labels,
                datasets: [{
                    label: 'Requests',
                    data: currentData.datasets[0].data,
                    backgroundColor: newType === 'pie' ? 
                        ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d', '#8592a3'] :
                        '#696cff',
                    borderColor: newType === 'pie' ? '#fff' : '#696cff',
                    borderWidth: newType === 'pie' ? 2 : 1
                }]
            };
            
            let success = false;
            if (newType === 'pie') {
                success = ChartManager.createPieChart('faculty-chart', chartData);
            } else {
                success = ChartManager.createBarChart('faculty-chart', chartData, {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                });
            }
            
            if (success) {
                this.currentFacultyView = newType;
            }
            
            // Hide loader
            Utils.hideLoader('faculty-loader');
        }, 100);
    },
    
    // Helper methods using Utils object
    showLoader: function(loaderId) {
        Utils.showLoader(loaderId);
    },
    
    hideLoader: function(loaderId) {
        Utils.hideLoader(loaderId);
    },
    
    showNoData: function(noDataId) {
        Utils.showNoData(noDataId);
    },
    
    hideNoData: function(noDataId) {
        Utils.hideNoData(noDataId);
    },
    
    renderFacultyAccordion: function(facultyData) {
        const facultyList = $('#facultyList');
        facultyList.empty();
        facultyData.forEach((faculty, index) => {
            const facultyCard = `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-title">${faculty.faculty_name}</h6>
                                <span class="badge bg-primary mb-2">${faculty.count} requests</span>
                            </div>
                            <button class="btn btn-outline-info btn-sm mt-2 view-programs-btn" data-faculty-id="${faculty.faculty_id}" data-faculty-name="${faculty.faculty_name}">
                                <i class="bx bx-list-ul me-1"></i> View Programs
                            </button>
                        </div>
                    </div>
                </div>
            `;
            facultyList.append(facultyCard);
        });

        // Attach click event for modal
        $('.view-programs-btn').off('click').on('click', function() {
            const facultyId = $(this).data('faculty-id');
            const facultyName = $(this).data('faculty-name');
            $('#programsModal .modal-title').text(facultyName + ' - Programs');
            $('#programsModalBody').html('<div class="text-center text-muted py-4">Loading...</div>');
            $('#programsModal').modal('show');
            AnalyticsManager.loadFacultyProgramsModal(facultyId);
        });
    },
    
    loadFacultyProgramsModal: function(facultyId) {
        const filters = {...FilterManager.getFilters(), faculty_id: facultyId};
        ApiService.fetchPrograms(filters)
            .done(function(response) {
                let html = '';
                if (response.success && response.data && response.data.length > 0) {
                    html += '<div class="row g-3">';
                    response.data.forEach(program => {
                        html += `
                            <div class="col-md-6 col-lg-4">
                                <div class="card bg-light mb-2">
                                    <div class="card-body">
                                        <h6 class="card-title">${program.program_name}</h6>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Total Requests</small>
                                            <span class="badge bg-info">${program.count}</span>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-success">Approved</small>
                                                <span class="text-success">${program.approved || 0}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-warning">Pending</small>
                                                <span class="text-warning">${program.pending || 0}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-danger">Rejected</small>
                                                <span class="text-danger">${program.rejected || 0}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html = '<div class="text-center text-muted py-4">No programs found</div>';
                }
                $('#programsModalBody').html(html);
            })
            .fail(function() {
                $('#programsModalBody').html('<div class="text-center text-danger py-4">Error loading programs</div>');
            });
    }
};

// ===========================
// MAIN APP INITIALIZER
// ===========================
var AnalyticsApp = {
    init: function() {
        ChartManager.init();
        FilterManager.init();
        AnalyticsManager.init();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
    // Load Chart.js from CDN if not available
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
        script.onload = function() {
            AnalyticsApp.init();
        };
        script.onerror = function() {
            console.error('Failed to load Chart.js');
            Utils.showError('Failed to load Chart.js library');
        };
        document.head.appendChild(script);
    } else {
        AnalyticsApp.init();
    }
});
</script>
@endpush