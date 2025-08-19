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
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearAnalyticsFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== OVERVIEW STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Total Requests') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2" id="total-requests">0</h4>
                            </div>
                            <small class="mb-0">{{ __('All time requests') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-calendar bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Pending Requests') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2" id="pending-requests">0</h4>
                                <span class="text-warning" id="pending-percentage">0%</span>
                            </div>
                            <small class="mb-0">{{ __('Awaiting review') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Approved Requests') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2" id="approved-requests">0</h4>
                                <span class="text-success" id="approved-percentage">0%</span>
                            </div>
                            <small class="mb-0">{{ __('Successfully approved') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Rejected Requests') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2" id="rejected-requests">0</h4>
                                <span class="text-danger" id="rejected-percentage">0%</span>
                            </div>
                            <small class="mb-0">{{ __('Declined requests') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-x-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ACCOMMODATION PREFERENCES ANALYSIS ===== --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Accommodation Type Preferences') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="accommodation-chart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Room Type Preferences') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="room-type-chart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FACULTY AND PROGRAM ANALYSIS ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Faculty Distribution') }}</h5>
                    <button class="btn btn-sm btn-primary" id="toggleFacultyView">
                        <i class="bx bx-transfer-alt me-1"></i> {{ __('Toggle View') }}
                    </button>
                </div>
                <div class="card-body">
                    <div id="faculty-chart-container">
                        <canvas id="faculty-chart" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DETAILED FACULTY BREAKDOWN ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Faculty and Program Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="facultyAccordion">
                        <!-- Faculty details will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== GENDER DISTRIBUTION ===== --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Gender Distribution') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="gender-chart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Monthly Request Trends') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthly-trends-chart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * Reservation Requests Analytics Page JS
 *
 * Structure:
 * - ApiService: Handles all AJAX requests for analytics data
 * - ChartManager: Manages all chart rendering and updates
 * - AnalyticsManager: Main controller for analytics functionality
 * - FilterManager: Handles analytics filters
 * - AnalyticsApp: Main app initializer
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
        overview: '{{ route("reservation-requests.analytics.overview") }}',
        accommodationTypes: '{{ route("reservation-requests.analytics.accommodation-types") }}',
        roomTypes: '{{ route("reservation-requests.analytics.room-types") }}',
        bedCounts: '{{ route("reservation-requests.analytics.bed-counts") }}',
        faculties: '{{ route("reservation-requests.analytics.faculties") }}',
        programs: '{{ route("reservation-requests.analytics.programs") }}',
        genders: '{{ route("reservation-requests.analytics.genders") }}',
        monthlyTrends: '{{ route("reservation-requests.analytics.monthly-trends") }}',
    },
    academicTerms: {
        all: '{{ route("academic.academic_terms.all") }}'
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
    
    fetchOverview: function(filters = {}) {
        return this.request(ROUTES.analytics.overview, filters);
    },
    
    fetchAccommodationTypes: function(filters = {}) {
        return this.request(ROUTES.analytics.accommodationTypes, filters);
    },
    
    fetchRoomTypes: function(filters = {}) {
        return this.request(ROUTES.analytics.roomTypes, filters);
    },
    
    fetchBedCounts: function(filters = {}) {
        return this.request(ROUTES.analytics.bedCounts, filters);
    },
    
    fetchFaculties: function(filters = {}) {
        return this.request(ROUTES.analytics.faculties, filters);
    },
    
    fetchPrograms: function(filters = {}) {
        return this.request(ROUTES.analytics.programs, filters);
    },
    
    fetchGenders: function(filters = {}) {
        return this.request(ROUTES.analytics.genders, filters);
    },
    
    fetchMonthlyTrends: function(filters = {}) {
        return this.request(ROUTES.analytics.monthlyTrends, filters);
    },
    
    fetchAcademicTerms: function() {
        return this.request(ROUTES.academicTerms.all);
    },
};

// ===========================
// CHART MANAGER
// ===========================
// ===========================
// IMPROVED CHART MANAGER
// ===========================
var ChartManager = {
    charts: {},
    
    init: function() {
        // Chart.js default configuration
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.plugins.legend.position = 'bottom';
        Chart.defaults.elements.arc.borderWidth = 2;
        Chart.defaults.elements.bar.borderRadius = 4;
        Chart.defaults.elements.line.tension = 0.4;
    },
    
    // Helper method to safely get canvas context
    getCanvasContext: function(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn(`Canvas with ID '${canvasId}' not found`);
            return null;
        }
        
        // If chart exists, destroy it first
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
                        }
                    },
                    ...options
                }
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
                        }
                    },
                    ...options
                }
            });
            
            this.charts[canvasId] = chart;
            return chart;
        } catch (error) {
            console.error(`Error creating bar chart for ${canvasId}:`, error);
            return null;
        }
    },
    
    createLineChart: function(canvasId, data, options = {}) {
        const canvas = this.getCanvasContext(canvasId);
        if (!canvas) return null;
        
        try {
            const chart = new Chart(canvas, {
                type: 'line',
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
                        }
                    },
                    ...options
                }
            });
            
            this.charts[canvasId] = chart;
            return chart;
        } catch (error) {
            console.error(`Error creating line chart for ${canvasId}:`, error);
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
                // If update fails, try to recreate the chart
                this.recreateChart(chartId, newData);
            }
        }
    },
    
    recreateChart: function(chartId, newData) {
        // Store chart type and options before destroying
        const existingChart = this.charts[chartId];
        if (!existingChart) return;
        
        const chartType = existingChart.config.type;
        const chartOptions = existingChart.config.options;
        
        // Destroy existing chart
        this.destroyChart(chartId);
        
        // Recreate chart based on type
        switch (chartType) {
            case 'pie':
                this.createPieChart(chartId, newData, chartOptions);
                break;
            case 'bar':
                this.createBarChart(chartId, newData, chartOptions);
                break;
            case 'line':
                this.createLineChart(chartId, newData, chartOptions);
                break;
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
        const chartIds = Object.keys(this.charts);
        chartIds.forEach(chartId => {
            this.destroyChart(chartId);
        });
        
        // Double-check that all charts are actually destroyed
        setTimeout(() => {
            chartIds.forEach(chartId => {
                const canvas = document.getElementById(chartId);
                if (canvas && canvas.chart) {
                    try {
                        canvas.chart.destroy();
                    } catch (error) {
                        console.warn(`Additional cleanup for ${chartId}:`, error);
                    }
                }
            });
        }, 100);
    },
    
    // Helper method to check if chart exists
    chartExists: function(chartId) {
        return this.charts[chartId] !== undefined;
    },
    
    // Method to get chart instance
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
        this.bindEvents();
    },
    
    bindEvents: function() {
        const self = this;
        const filterFields = ['#filter_academic_term', '#filter_status', '#filter_date_from', '#filter_date_to'];
        
        // Add debouncing to prevent rapid successive calls
        filterFields.forEach(field => {
            $(field).on('change', function() {
                // Prevent multiple rapid changes
                if (self.isLoading) return;
                
                clearTimeout(self.filterTimeout);
                self.filterTimeout = setTimeout(() => {
                    if (!self.isLoading) {
                        self.isLoading = true;
                        AnalyticsManager.refreshAllData();
                    }
                }, 300);
            });
        });
        
        $('#clearAnalyticsFiltersBtn').on('click', function() {
            if (!self.isLoading) {
                FilterManager.clearFilters();
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
    
    getFilters: function() {
        return {
            academic_term: $('#filter_academic_term').val(),
            status: $('#filter_status').val(),
            date_from: $('#filter_date_from').val(),
            date_to: $('#filter_date_to').val()
        };
    },
    
    clearFilters: function() {
        // Temporarily disable change events
        this.isLoading = true;
        
        $('#filter_academic_term, #filter_status').val('');
        $('#filter_date_from, #filter_date_to').val('');
        
        // Manually trigger refresh
        setTimeout(() => {
            AnalyticsManager.refreshAllData();
        }, 100);
    },
    
    setLoadingState: function(loading) {
        this.isLoading = loading;
    }
};

// ===========================
// IMPROVED ANALYTICS MANAGER
// ===========================
var AnalyticsManager = {
    isLoading: false,
    
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
    },

    
    loadAllData: function() {
        if (this.isLoading) {
            console.warn('Already loading data, skipping...');
            return;
        }
        
        const filters = FilterManager.getFilters();
        
        const promises = [
            ApiService.fetchOverview(filters),
            ApiService.fetchAccommodationTypes(filters),
            ApiService.fetchRoomTypes(filters),
            ApiService.fetchBedCounts(filters),
            ApiService.fetchFaculties(filters),
            ApiService.fetchGenders(filters),
            ApiService.fetchMonthlyTrends(filters),
        ];
        
        Promise.all(promises)
            .then(responses => {
                try {
                    this.renderOverview(responses[0]);
                    this.renderAccommodationTypes(responses[1]);
                    this.renderRoomTypes(responses[2]);
                    this.renderBedCounts(responses[3]);
                    this.renderFaculties(responses[4]);
                    this.renderGenders(responses[5]);
                    this.renderMonthlyTrends(responses[6]);
                } catch (error) {
                    console.error('Error rendering analytics data:', error);
                    Utils.showError('Error rendering analytics data: ' + error.message);
                }
            })
            .catch(error => {
                console.error('Error loading analytics data:', error);
                Utils.showError(TRANSLATIONS.messages.errorLoadingData);
            })

    },
    
    refreshAllData: function() {
        if (this.isLoading) {
            console.warn('Already refreshing data, skipping...');
            return;
        }
        
        console.log('Starting data refresh...');
        
        // Destroy all existing charts first
        ChartManager.destroyAllCharts();
        
        // Small delay to ensure cleanup is complete
        setTimeout(() => {
            this.loadAllData();
        }, 200);
    },
    
    renderOverview: function(response) {
        if (response.success && response.data) {
            const data = response.data;
            $('#total-requests').text(data.total || 0);
            $('#pending-requests').text(data.pending || 0);
            $('#approved-requests').text(data.approved || 0);
            $('#rejected-requests').text(data.rejected || 0);
            
            const total = data.total || 1;
            $('#pending-percentage').text(((data.pending || 0) / total * 100).toFixed(1) + '%');
            $('#approved-percentage').text(((data.approved || 0) / total * 100).toFixed(1) + '%');
            $('#rejected-percentage').text(((data.rejected || 0) / total * 100).toFixed(1) + '%');
        }
    },
    
    renderAccommodationTypes: function(response) {
        if (response.success && response.data && response.data.length > 0) {
            const chartData = {
                labels: response.data.map(item => TRANSLATIONS.labels[item.accommodation_type] || item.accommodation_type),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            
            const chart = ChartManager.createPieChart('accommodation-chart', chartData);
            if (!chart) {
                console.warn('Failed to create accommodation types chart');
                this.showNoDataMessage('accommodation-chart');
            }
        } else {
            this.showNoDataMessage('accommodation-chart');
        }
    },
    
    renderRoomTypes: function(response) {
        if (response.success && response.data && response.data.length > 0) {
            const chartData = {
                labels: response.data.map(item => TRANSLATIONS.labels[item.room_type] || item.room_type),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#696cff', '#03c3ec', '#ffab00', '#71dd37'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            
            const chart = ChartManager.createPieChart('room-type-chart', chartData);
            if (!chart) {
                this.showNoDataMessage('room-type-chart');
            }
        } else {
            this.showNoDataMessage('room-type-chart');
        }
    },
    
    
    renderFaculties: function(response) {
        if (response.success && response.data && response.data.length > 0) {
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
        } else {
            this.showNoDataMessage('faculty-chart');
            $('#facultyAccordion').html('<div class="text-center text-muted">No faculty data available</div>');
        }
    },
    
    renderFacultyAccordion: function(facultyData) {
        const accordion = $('#facultyAccordion');
        accordion.empty();
        
        facultyData.forEach((faculty, index) => {
            const accordionItem = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse${index}" 
                                aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapse${index}">
                            <div class="d-flex justify-content-between w-100 me-3">
                                <span>${faculty.faculty_name}</span>
                                <span class="badge bg-primary">${faculty.count} requests</span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                         aria-labelledby="heading${index}" data-bs-parent="#facultyAccordion">
                        <div class="accordion-body">
                            <div class="row g-3" id="faculty-programs-${index}">
                                <div class="col-12">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            accordion.append(accordionItem);
            
            // Load programs for this faculty
            this.loadFacultyPrograms(faculty.faculty_id, index);
        });
    },
    
    loadFacultyPrograms: function(facultyId, index) {
        const filters = {...FilterManager.getFilters(), faculty_id: facultyId};
        
        ApiService.fetchPrograms(filters)
            .done(function(response) {
                const container = $(`#faculty-programs-${index}`);
                container.empty();
                
                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(program => {
                        const programCard = `
                            <div class="col-md-6 col-lg-4">
                                <div class="card bg-light">
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
                        container.append(programCard);
                    });
                } else {
                    container.html('<div class="col-12"><p class="text-muted">No programs found</p></div>');
                }
            })
            .fail(function() {
                $(`#faculty-programs-${index}`).html('<div class="col-12"><p class="text-danger">Error loading programs</p></div>');
            });
    },
    
    renderGenders: function(response) {
        if (response.success && response.data && response.data.length > 0) {
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
        } else {
            this.showNoDataMessage('gender-chart');
        }
    },
    
    renderMonthlyTrends: function(response) {
        if (response.success && response.data && response.data.length > 0) {
            const chartData = {
                labels: response.data.map(item => item.month_name),
                datasets: [{
                    label: 'Requests',
                    data: response.data.map(item => item.count),
                    borderColor: '#696cff',
                    backgroundColor: 'rgba(105, 108, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            };
            
            ChartManager.createLineChart('monthly-trends-chart', chartData);
        } else {
            this.showNoDataMessage('monthly-trends-chart');
        }
    },
    
    showNoDataMessage: function(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            const container = canvas.parentElement;
            container.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 chart-no-data">
                    <div class="text-center text-muted">
                        <i class="bx bx-info-circle bx-lg mb-2"></i>
                        <p>No data available</p>
                    </div>
                </div>
            `;
        }
    },
    
    toggleFacultyView: function() {
        if (this.isLoading) return;
        
        const currentChart = ChartManager.getChart('faculty-chart');
        if (currentChart) {
            const isBar = currentChart.config.type === 'bar';
            const newType = isBar ? 'pie' : 'bar';
            
            // Store the current data before destroying
            const currentData = currentChart.data;
            
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
                
                if (newType === 'pie') {
                    ChartManager.createPieChart('faculty-chart', chartData);
                } else {
                    ChartManager.createBarChart('faculty-chart', chartData, {
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    });
                }
            }, 100);
        }
    }
};

// ===========================
// ANALYTICS MANAGER
// ===========================
var AnalyticsManager = {
    init: function() {
        this.bindEvents();
        this.loadAllData();
    },
    
    bindEvents: function() {
        $('#refreshAnalyticsBtn').on('click', function() {
            AnalyticsManager.refreshAllData();
        });
        
        $('#toggleFacultyView').on('click', function() {
            AnalyticsManager.toggleFacultyView();
        });
    },
    
    loadAllData: function() {
        const filters = FilterManager.getFilters();
        
        Promise.all([
            ApiService.fetchOverview(filters),
            ApiService.fetchAccommodationTypes(filters),
            ApiService.fetchRoomTypes(filters),
            ApiService.fetchBedCounts(filters),
            ApiService.fetchFaculties(filters),
            ApiService.fetchGenders(filters),
            ApiService.fetchMonthlyTrends(filters),
        ]).then(responses => {
            this.renderOverview(responses[0]);
            this.renderAccommodationTypes(responses[1]);
            this.renderRoomTypes(responses[2]);
            this.renderBedCounts(responses[3]);
            this.renderFaculties(responses[4]);
            this.renderGenders(responses[5]);
            this.renderMonthlyTrends(responses[6]);
        }).catch(error => {
            console.error('Error loading analytics data:', error);
            Utils.showError(TRANSLATIONS.messages.errorLoadingData);
        });
    },
    
    refreshAllData: function() {
        ChartManager.destroyAllCharts();
        this.loadAllData();
    },
    
    renderOverview: function(response) {
        if (response.success && response.data) {
            const data = response.data;
            $('#total-requests').text(data.total || 0);
            $('#pending-requests').text(data.pending || 0);
            $('#approved-requests').text(data.approved || 0);
            $('#rejected-requests').text(data.rejected || 0);
            
            const total = data.total || 1;
            $('#pending-percentage').text(((data.pending || 0) / total * 100).toFixed(1) + '%');
            $('#approved-percentage').text(((data.approved || 0) / total * 100).toFixed(1) + '%');
            $('#rejected-percentage').text(((data.rejected || 0) / total * 100).toFixed(1) + '%');
        }
    },
    
    renderAccommodationTypes: function(response) {
        if (response.success && response.data) {
            const chartData = {
                labels: response.data.map(item => TRANSLATIONS.labels[item.accommodation_type] || item.accommodation_type),
                datasets: [{
                    data: response.data.map(item => item.count),
                    backgroundColor: ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };
            
            ChartManager.createPieChart('accommodation-chart', chartData);
        }
    },
    
    renderRoomTypes: function(response) {
        if (response.success && response.data) {
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
        }
    },
    
    
    renderFaculties: function(response) {
        if (response.success && response.data) {
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
        }
    },
    
    renderFacultyAccordion: function(facultyData) {
        const accordion = $('#facultyAccordion');
        accordion.empty();
        
        facultyData.forEach((faculty, index) => {
            const accordionItem = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${index}">
                        <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse${index}" 
                                aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapse${index}">
                            <div class="d-flex justify-content-between w-100 me-3">
                                <span>${faculty.faculty_name}</span>
                                <span class="badge bg-primary">${faculty.count} {{ __('requests') }}</span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                         aria-labelledby="heading${index}" data-bs-parent="#facultyAccordion">
                        <div class="accordion-body">
                            <div class="row g-3" id="faculty-programs-${index}">
                                <div class="col-12">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            accordion.append(accordionItem);
            
            // Load programs for this faculty
            this.loadFacultyPrograms(faculty.faculty_id, index);
        });
    },
    
    loadFacultyPrograms: function(facultyId, index) {
        const filters = {...FilterManager.getFilters(), faculty_id: facultyId};
        
        ApiService.fetchPrograms(filters)
            .done(function(response) {
                if (response.success && response.data) {
                    const container = $(`#faculty-programs-${index}`);
                    container.empty();
                    
                    response.data.forEach(program => {
                        const programCard = `
                            <div class="col-md-6 col-lg-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">${program.program_name}</h6>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ __('Total Requests') }}</small>
                                            <span class="badge bg-info">${program.count}</span>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-success">{{ __('Approved') }}</small>
                                                <span class="text-success">${program.approved || 0}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-warning">{{ __('Pending') }}</small>
                                                <span class="text-warning">${program.pending || 0}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-danger">{{ __('Rejected') }}</small>
                                                <span class="text-danger">${program.rejected || 0}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(programCard);
                    });
                } else {
                    $(`#faculty-programs-${index}`).html('<div class="col-12"><p class="text-muted">{{ __("No programs found") }}</p></div>');
                }
            })
            .fail(function() {
                $(`#faculty-programs-${index}`).html('<div class="col-12"><p class="text-danger">{{ __("Error loading programs") }}</p></div>');
            });
    },
    
    renderGenders: function(response) {
        if (response.success && response.data) {
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
        }
    },
    
    renderMonthlyTrends: function(response) {
        if (response.success && response.data) {
            const chartData = {
                labels: response.data.map(item => item.month_name),
                datasets: [{
                    label: '{{ __("Requests") }}',
                    data: response.data.map(item => item.count),
                    borderColor: '#696cff',
                    backgroundColor: 'rgba(105, 108, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            };
            
            ChartManager.createLineChart('monthly-trends-chart', chartData);
        }
    },
    
    toggleFacultyView: function() {
        // Toggle between bar chart and pie chart for faculty data
        const currentChart = ChartManager.charts['faculty-chart'];
        if (currentChart) {
            const isBar = currentChart.config.type === 'bar';
            const newType = isBar ? 'pie' : 'bar';
            
            ChartManager.destroyChart('faculty-chart');
            
            // Re-fetch and render with new chart type
            const filters = FilterManager.getFilters();
            ApiService.fetchFaculties(filters)
                .done(function(response) {
                    if (response.success && response.data) {
                        const chartData = {
                            labels: response.data.map(item => item.faculty_name),
                            datasets: [{
                                label: '{{ __("Requests") }}',
                                data: response.data.map(item => item.count),
                                backgroundColor: newType === 'pie' ? 
                                    ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d', '#8592a3'] :
                                    '#696cff',
                                borderColor: newType === 'pie' ? '#fff' : '#696cff',
                                borderWidth: newType === 'pie' ? 2 : 1
                            }]
                        };
                        
                        if (newType === 'pie') {
                            ChartManager.createPieChart('faculty-chart', chartData);
                        } else {
                            ChartManager.createBarChart('faculty-chart', chartData, {
                                indexAxis: 'y',
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                }
                            });
                        }
                    }
                });
        }
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
    // Load Chart.js from CDN
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
        script.onload = function() {
            AnalyticsApp.init();
        };
        document.head.appendChild(script);
    } else {
        AnalyticsApp.init();
    }
});

</script>
@endpush