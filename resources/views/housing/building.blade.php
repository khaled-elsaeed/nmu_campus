@extends('layouts.home')

@section('title', __('Building Management'))

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ===== STATISTICS CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="secondary" icon="bx bx-buildings" :label="__('Total Buildings')" id="buildings" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="info" icon="bx bx-male" :label="__('Male Buildings')" id="buildings-male" />
        </div>
        <div class="col-sm-6 col-xl-4">
            <x-ui.card.stat2 color="danger" icon="bx bx-female" :label="__('Female Buildings')" id="buildings-female" />
        </div>
    </div>

    {{-- ===== PAGE HEADER & ACTION BUTTONS ===== --}}
    <x-ui.page-header :title="__('Buildings')" :description="__('Manage all campus buildings and their details')" icon="bx bx-buildings">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
            <button class="btn btn-primary mx-2" id="addBuildingBtn" type="button" data-bs-toggle="modal" data-bs-target="#createBuildingModal">
                <i class="bx bx-plus me-1"></i> {{ __('Add Building') }}
            </button>
            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#buildingSearchCollapse" aria-expanded="false" aria-controls="buildingSearchCollapse">
                <i class="bx bx-filter-alt me-1"></i> {{ __('Search') }}
            </button>
        </div>
    </x-ui.page-header>

    {{-- ===== ADVANCED SEARCH SECTION ===== --}}
    <x-ui.advanced-search :title="__('Advanced Building Search')" formId="advancedBuildingSearch" collapseId="buildingSearchCollapse" :collapsed="false">
        <div class="col-md-4">
            <label for="search_gender_restriction" class="form-label">{{ __('Gender Restriction') }}:</label>
            <select class="form-control" id="search_gender_restriction">
                <option value="">{{ __('Select Gender') }}</option>
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_active" class="form-label">{{ __('Active Status') }}:</label>
            <select class="form-control" id="search_active">
                <option value="">{{ __('Select Status') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="w-100"></div>
        <button class="btn btn-outline-secondary mt-2 ms-2" id="clearBuildingFiltersBtn" type="button">
            <i class="bx bx-x"></i> {{ __('Clear Filters') }}
        </button>
    </x-ui.advanced-search>

    {{-- ===== DATA TABLE ===== --}}
    <x-ui.datatable.table 
        :headers="[
            __('Number'), __('Apartments'), __('Rooms'), __('Double Rooms'), __('Gender'), __('Status'), __('Current Capacity'), __('Actions')
        ]"
        :columns=" [
            ['data' => 'number', 'name' => 'number'],
            ['data' => 'total_apartments', 'name' => 'total_apartments'],
            ['data' => 'total_rooms', 'name' => 'total_rooms'],
            ['data' => 'has_double_rooms', 'name' => 'has_double_rooms'],
            ['data' => 'gender_restriction', 'name' => 'gender_restriction'],
            ['data' => 'status', 'name' => 'status'],
            ['data' => 'current_occupancy', 'name' => 'current_occupancy', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
        :ajax-url="route('housing.buildings.datatable')"
        table-id="buildings-table"
        :filter-fields="['search_gender_restriction','search_active']"
    />

    {{-- ===== MODALS SECTION ===== --}}
    {{-- Create Building Modal --}}
    <x-ui.modal id="createBuildingModal" :title="__('Add Building')" scrollable="true" class="create-building-modal">
        <x-slot name="slot">
            <form id="createBuildingForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="create_building_number" class="form-label">{{ __('Building Number') }}</label>
                        <input type="number" id="create_building_number" name="number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="create_building_total_apartments" class="form-label">{{ __('Total Apartments') }}</label>
                        <input type="number" id="create_building_total_apartments" name="total_apartments" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="create_building_rooms_per_apartment" class="form-label">{{ __('Rooms per Apartment') }}</label>
                        <input type="number" id="create_building_rooms_per_apartment" name="rooms_per_apartment" class="form-control" required min="1">
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_has_double_rooms" name="has_double_rooms">
                            <label class="form-check-label" for="create_has_double_rooms">
                                {{ __('Has Double Rooms') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3" id="create-apartments-double-rooms-section" style="display: none;"></div>
                    <div class="col-md-12 mb-3">
                        <label for="create_building_gender_restriction" class="form-label">{{ __('Gender Restriction') }}</label>
                        <select id="create_building_gender_restriction" name="gender_restriction" class="form-control" required>
                            <option value="">{{ __('Select Gender Restriction') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary" form="createBuildingForm" id="createBuildingSubmit">
                <span class="normal-text">{{ __('Save') }}</span>
                <span class="loading-text" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Saving...') }}
                </span>
            </button>
        </x-slot>
    </x-ui.modal>

    {{-- Edit Building Modal --}}
    <x-ui.modal id="editBuildingModal" :title="__('Edit Building')" scrollable="true" class="edit-building-modal">
        <x-slot name="slot">
            <form id="editBuildingForm">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="edit_building_number" class="form-label">{{ __('Building Number') }}</label>
                        <input type="number" id="edit_building_number" name="number" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="edit_building_gender_restriction" class="form-label">{{ __('Gender Restriction') }}</label>
                        <select id="edit_building_gender_restriction" name="gender_restriction" class="form-control" required>
                            <option value="">{{ __('Select Gender Restriction') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                        </select>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="submit" class="btn btn-primary" form="editBuildingForm" id="editBuildingSubmit">
                <span class="normal-text">{{ __('Save') }}</span>
                <span class="loading-text" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    {{ __('Saving...') }}
                </span>
            </button>
        </x-slot>
    </x-ui.modal>

    {{-- View Building Modal --}}
    <x-ui.modal id="viewBuildingModal" :title="__('Building Details')" scrollable="true" class="view-building-modal">
        <x-slot name="slot">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Number') }}:</label>
                    <p id="view-building-number" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Total Apartments') }}:</label>
                    <p id="view-building-total-apartments" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Total Rooms') }}:</label>
                    <p id="view-building-total-rooms" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Gender Restriction') }}:</label>
                    <p id="view-building-gender-restriction" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Active') }}:</label>
                    <p id="view-building-is-active" class="mb-0"></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">{{ __('Created At') }}:</label>
                    <p id="view-building-created" class="mb-0"></p>
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
// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
    buildings: {
        stats: '{{ route('housing.buildings.stats') }}',
        show: '{{ route('housing.buildings.show', ':id') }}',
        store: '{{ route('housing.buildings.store') }}',
        update: '{{ route('housing.buildings.update', ':id') }}',
        destroy: '{{ route('housing.buildings.destroy', ':id') }}',
        activate: '{{ route('housing.buildings.activate', ':id') }}',
        deactivate: '{{ route('housing.buildings.deactivate', ':id') }}'
    }
};

// ===========================
// API SERVICE
// ===========================
var ApiService = {
    request(options) {
        return $.ajax(options);
    },
    fetchStats: function() {
        return ApiService.request({ url: ROUTES.buildings.stats, method: 'GET' });
    },
    fetchBuilding: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.show, id), method: 'GET' });
    },
    saveBuilding: function(data, id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.update, id), method: 'PUT', data: data });
    },
    createBuilding: function(data) {
        return ApiService.request({ url: ROUTES.buildings.store, method: 'POST', data: data });
    },
    deleteBuilding: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.destroy, id), method: 'DELETE' });
    },
    activateBuilding: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.activate, id), method: 'PATCH' });
    },
    deactivateBuilding: function(id) {
        return ApiService.request({ url: Utils.replaceRouteId(ROUTES.buildings.deactivate, id), method: 'PATCH' });
    }
};

// ===========================
// TRANSLATION CONSTANTS
// ===========================
const TRANSLATION = {
    confirm: {
        activate: {
            title: @json(__('Activate Building')),
            text: @json(__('Are you sure you want to activate this building?')),
            button: @json(__('Activate'))
        },
        deactivate: {
            title: @json(__('Deactivate Building')),
            text: @json(__('Are you sure you want to deactivate this building?')),
            button: @json(__('Deactivate'))
        },
        delete: {
            title: @json(__('Delete Building')),
            text: @json(__('Are you sure you want to delete this building? This action cannot be undone.')),
            button: @json(__('Delete'))
        }
    },
    modal: {
        addTitle: @json(__('Add Building')),
        editTitle: @json(__('Edit Building')),
        saving: @json(__('Saving...'))
    },
    status: {
        active: @json(__('Active')),
        inactive: @json(__('Inactive')),
        activating: @json(__('Activating...')),
        deactivating: @json(__('Deactivating...'))
    },
    gender: {
        male: @json(__('Male')),
        female: @json(__('Female'))
    },
    apartment: {
        title: @json(__('Apartment')),
        doubleRooms: @json(__('Double Rooms')),
        room: @json(__('Room'))
    },
    placeholders: {
        selectGenderRestriction: @json(__('Select Gender')),
        selectStatus: @json(__('Select Status'))
    }
};

// ===========================
// SELECT2 MANAGER
// ===========================
var Select2Manager = {
    config: {
        search: {
            '#search_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGenderRestriction },
            '#search_active': { placeholder: TRANSLATION.placeholders.selectStatus }
        },
        modal: {
            '#create_building_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGenderRestriction, dropdownParent: $('#createBuildingModal') },
            '#edit_building_gender_restriction': { placeholder: TRANSLATION.placeholders.selectGenderRestriction, dropdownParent: $('#editBuildingModal') }
        }
    },

    initSearchSelect2: function() {
        Object.keys(this.config.search).forEach(function(selector) {
            Utils.initSelect2(selector, Select2Manager.config.search[selector]);
        });
    },

    initModalSelect2: function() {
        Object.keys(this.config.modal).forEach(function(selector) {
            Utils.initSelect2(selector, Select2Manager.config.modal[selector]);
        });
    },

    initAll: function() {
        this.initSearchSelect2();
        this.initModalSelect2();
    },

    resetSearchSelect2: function() {
        $('#search_gender_restriction').val('').trigger('change');
        $('#search_active').val('').trigger('change');
    },

    resetCreateModalSelect2: function() {
        $('#create_building_gender_restriction').val('').trigger('change');
    },

    resetEditModalSelect2: function() {
        $('#edit_building_gender_restriction').val('').trigger('change');
    }
};

// ===========================
// STATISTICS MANAGER
// ===========================
var StatsManager = Utils.createStatsManager({
    apiMethod: ApiService.fetchStats,
    statsKeys: ['buildings', 'buildings-male', 'buildings-female'],
});

// ===========================
// BUILDING MANAGER
// ===========================
var BuildingManager = {
    currentBuildingId: null,

    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        this.handleAddBuilding();
        this.handleEditBuilding();
        this.handleViewBuilding();
        this.handleDeleteBuilding();
        this.handleCreateFormSubmit();
        this.handleEditFormSubmit();
        this.handleActivateDeactivate();
    },

    handleAddBuilding: function() {
        $(document).on('click', '#addBuildingBtn', function() {
            BuildingManager.openCreateModal();
        });
    },

    handleEditBuilding: function() {
        $(document).on('click', '.editBuildingBtn', function(e) {
            var buildingId = $(e.currentTarget).data('id');
            BuildingManager.openEditModal(buildingId);
        });
    },

    handleViewBuilding: function() {
        $(document).on('click', '.viewBuildingBtn', function(e) {
            var buildingId = $(e.currentTarget).data('id');
            BuildingManager.viewBuilding(buildingId);
        });
    },

    handleDeleteBuilding: function() {
        $(document).on('click', '.deleteBuildingBtn', function(e) {
            var buildingId = $(e.currentTarget).data('id');
            BuildingManager.deleteBuilding(buildingId);
        });
    },

    handleCreateFormSubmit: function() {
        $('#createBuildingForm').on('submit', function(e) {
            e.preventDefault();
            BuildingManager.createBuilding();
        });
    },

    handleEditFormSubmit: function() {
        $('#editBuildingForm').on('submit', function(e) {
            e.preventDefault();
            BuildingManager.updateBuilding();
        });
    },

    handleActivateDeactivate: function() {
        $(document).on('click', '.activateBuildingBtn', function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var id = $btn.data('id');
            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.activate.title,
                text: TRANSLATION.confirm.activate.text,
                icon: 'question',
                confirmButtonText: TRANSLATION.confirm.activate.button,
            }).then(function(result) {
                if (result.isConfirmed) {
                    Utils.setLoadingState($btn, true, { loadingText: TRANSLATION.status.activating });
                    ApiService.activateBuilding(id)
                        .done(function(response) {
                            Utils.showSuccess(response.message);
                            Utils.reloadDataTable('#buildings-table', null, true);
                            StatsManager.refresh();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
                        })
                        .always(function() {
                            Utils.setLoadingState($btn, false, { normalText: TRANSLATION.status.active, normalIcon: 'bx bx-check' });
                        });
                }
            });
        });

        $(document).on('click', '.deactivateBuildingBtn', function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var id = $btn.data('id');
            Utils.showConfirmDialog({
                title: TRANSLATION.confirm.deactivate.title,
                text: TRANSLATION.confirm.deactivate.text,
                icon: 'warning',
                confirmButtonText: TRANSLATION.confirm.deactivate.button,
            }).then(function(result) {
                if (result.isConfirmed) {
                    Utils.setLoadingState($btn, true, { loadingText: TRANSLATION.status.deactivating });
                    ApiService.deactivateBuilding(id)
                        .done(function(response) {
                            Utils.showSuccess(response.message);
                            Utils.reloadDataTable('#buildings-table', null, true);
                            StatsManager.refresh();
                        })
                        .fail(function(xhr) {
                            Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
                        })
                        .always(function() {
                            Utils.setLoadingState($btn, false, { normalText: TRANSLATION.status.inactive, normalIcon: 'bx bx-x' });
                        });
                }
            });
        });
    },

    openCreateModal: function() {
        this.resetCreateModalState();
        Select2Manager.initModalSelect2();
        $('#createBuildingModal').modal('show');
    },

    openEditModal: function(buildingId) {
        this.currentBuildingId = buildingId;
        this.resetEditModalState();
        ApiService.fetchBuilding(buildingId)
            .done(function(response) {
                if (response.success) {
                    BuildingManager.populateEditForm(response.data);
                    Select2Manager.initModalSelect2();
                    $('#editBuildingModal').modal('show');
                }
            })
            .fail(function(xhr) {
                $('#editBuildingModal').modal('hide');
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            });
    },

    resetForm: function(formId) {
        var $form = $('#' + formId);
        $form[0].reset();
        Utils.clearValidation($form);
    },

    resetCreateModalState: function() {
        this.resetForm('createBuildingForm');
        $('#create_has_double_rooms').prop('checked', false);
        Select2Manager.resetCreateModalSelect2();
    },

    resetEditModalState: function() {
        this.resetForm('editBuildingForm');
        Select2Manager.resetEditModalSelect2();
    },

    resetCreateModalState: function() {
        this.resetForm('createBuildingForm');
        $('#create_has_double_rooms').prop('checked', false);
        DoubleRoomManager.hideDoubleRoomSection();
        Select2Manager.resetCreateModalSelect2();
    },

    populateEditForm: function(building) {
        $('#edit_building_number').val(building.number);
        Utils.populateSelect('#edit_building_gender_restriction', [
            { id: 'male', name: TRANSLATION.gender.male },
            { id: 'female', name: TRANSLATION.gender.female }
        ], {
            valueField: 'id',
            textField: 'name',
            placeholder: TRANSLATION.placeholders.selectGenderRestriction,
            selected: building.gender,
            includePlaceholder: true
        });
    },

    viewBuilding: function(buildingId) {
        ApiService.fetchBuilding(buildingId)
            .done(function(response) {
                if (response.success) {
                    BuildingManager.populateViewModal(response.data);
                    $('#viewBuildingModal').modal('show');
                }
            })
            .fail(function(xhr) {
                $('#viewBuildingModal').modal('hide');
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            });
    },

    populateViewModal: function(building) {
        $('#view-building-number').text(building.number);
        $('#view-building-total-apartments').text(building.total_apartments);
        $('#view-building-total-rooms').text(building.total_rooms);
        $('#view-building-gender-restriction').text(building.gender_restriction);
        $('#view-building-is-active').text(building.active ? TRANSLATION.status.active : TRANSLATION.status.inactive);
        $('#view-building-created').text(new Date(building.created_at).toLocaleString());
    },

    createBuilding: function() {
        var $submitBtn = $('#createBuildingSubmit');
        var formData = $('#createBuildingForm').serialize();
        
        $submitBtn.find('.normal-text').hide();
        $submitBtn.find('.loading-text').show();
        $submitBtn.prop('disabled', true);
        
        ApiService.createBuilding(formData)
            .done(function(response) {
                $('#createBuildingModal').modal('hide');
                Utils.reloadDataTable('#buildings-table', null, true);
                Utils.showSuccess(response.message);
                StatsManager.refresh();
            })
            .fail(function(xhr) {
                $('#createBuildingModal').modal('hide');
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            })
            .always(function() {
                $submitBtn.find('.normal-text').show();
                $submitBtn.find('.loading-text').hide();
                $submitBtn.prop('disabled', false);
            });
    },

    updateBuilding: function() {
        var $submitBtn = $('#editBuildingSubmit');
        var formData = $('#editBuildingForm').serialize();
        
        $submitBtn.find('.normal-text').hide();
        $submitBtn.find('.loading-text').show();
        $submitBtn.prop('disabled', true);
        
        ApiService.saveBuilding(formData, this.currentBuildingId)
            .done(function(response) {
                $('#editBuildingModal').modal('hide');
                Utils.reloadDataTable('#buildings-table', null, true);
                Utils.showSuccess(response.message);
                StatsManager.refresh();
            })
            .fail(function(xhr) {
                $('#editBuildingModal').modal('hide');
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            })
            .always(function() {
                $submitBtn.find('.normal-text').show();
                $submitBtn.find('.loading-text').hide();
                $submitBtn.prop('disabled', false);
            });
    },

    deleteBuilding: function(buildingId) {
        Utils.showConfirmDialog({
            title: TRANSLATION.confirm.delete.title,
            text: TRANSLATION.confirm.delete.text,
            confirmButtonText: TRANSLATION.confirm.delete.button
        }).then(function(result) {
            if (result.isConfirmed) {
                BuildingManager.performDelete(buildingId);
            }
        });
    },

    performDelete: function(buildingId) {
        ApiService.deleteBuilding(buildingId)
            .done(function(response) {
                Utils.reloadDataTable('#buildings-table', null, true);
                Utils.showSuccess(response.message);
                StatsManager.refresh();
            })
            .fail(function(xhr) {
                Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
            });
    }
};

// ===========================
// SEARCH FUNCTIONALITY
// ===========================
var SearchManager = {
    init: function() {
        this.bindEvents();
        Select2Manager.initSearchSelect2();
    },

    bindEvents: function() {
        this.initializeAdvancedSearch();
        this.handleClearFilters();
    },

    initializeAdvancedSearch: function() {
        $('#search_gender_restriction, #search_active').on('change', function() {
            Utils.reloadDataTable('#buildings-table');
        });
        
        var searchTimeout;
        $('#search_gender_restriction, #search_active').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                Utils.reloadDataTable('#buildings-table');
            }, 300);
        });
    },

    handleClearFilters: function() {
        $('#clearBuildingFiltersBtn').on('click', function() {
            $('#search_gender_restriction').val('').trigger('change');
            $('#search_active').val('').trigger('change');
            Select2Manager.resetSearchSelect2();
            Utils.reloadDataTable('#buildings-table');
        });
    }
};

// ===========================
// DOUBLE ROOM MANAGER
// ===========================
var DoubleRoomManager = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        this.handleDoubleRoomToggle();
        this.handleModalShow();
        this.handleInputChange();
        this.handleDocumentReady();
    },

    handleDoubleRoomToggle: function() {
        $('#create_has_double_rooms').on('change', function(e) {
            if ($(e.currentTarget).is(':checked')) {
                DoubleRoomManager.showDoubleRoomSection();
            } else {
                DoubleRoomManager.hideDoubleRoomSection();
            }
        });
    },

    handleModalShow: function() {
        $('#createBuildingModal').on('show.bs.modal', function() {
            if ($('#create_has_double_rooms').is(':checked')) {
                DoubleRoomManager.showDoubleRoomSection();
            } else {
                DoubleRoomManager.hideDoubleRoomSection();
            }
        });
    },

    handleInputChange: function() {
        $('#create_building_total_apartments, #create_building_rooms_per_apartment').on('input', function() {
            if ($('#create_has_double_rooms').is(':checked')) {
                DoubleRoomManager.renderDoubleRoomSelectors();
            }
        });
    },

    handleDocumentReady: function() {
        $(document).ready(function() {
            if ($('#create_has_double_rooms').is(':checked')) {
                DoubleRoomManager.showDoubleRoomSection();
            } else {
                DoubleRoomManager.hideDoubleRoomSection();
            }
        });
    },

    showDoubleRoomSection: function() {
        $('#create-apartments-double-rooms-section').show();
        DoubleRoomManager.renderDoubleRoomSelectors();
    },

    hideDoubleRoomSection: function() {
        $('#create-apartments-double-rooms-section').hide().empty(); // Ensure section is cleared
    },

    renderDoubleRoomSelectors: function() {
        var totalApartments = parseInt($('#create_building_total_apartments').val()) || 0;
        var roomsPerApartment = parseInt($('#create_building_rooms_per_apartment').val()) || 0;
        var $section = $('#create-apartments-double-rooms-section');
        $section.empty();
        if (!totalApartments || !roomsPerApartment) return;
        var accordionHtml = DoubleRoomManager.generateAccordionHtml(totalApartments, roomsPerApartment);
        var boxHtml = DoubleRoomManager.wrapInScrollableBox(accordionHtml);
        $section.append(boxHtml);
    },

    generateAccordionHtml: function(totalApartments, roomsPerApartment) {
        var accordionHtml = '<div class="accordion" id="apartmentsAccordion">';
        for (var i = 1; i <= totalApartments; i++) {
            accordionHtml += DoubleRoomManager.generateApartmentAccordionItem(i, roomsPerApartment);
        }
        accordionHtml += '</div>';
        return accordionHtml;
    },

    generateApartmentAccordionItem: function(apartmentNumber, roomsPerApartment) {
        var collapseId = 'apartment' + apartmentNumber + 'Collapse';
        var headingId = 'apartment' + apartmentNumber + 'Heading';
        var checkboxes = DoubleRoomManager.generateRoomCheckboxes(apartmentNumber, roomsPerApartment);
        return `
            <div class="accordion-item">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                        ${TRANSLATION.apartment.title} ${apartmentNumber} - ${TRANSLATION.apartment.doubleRooms}
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#apartmentsAccordion">
                    <div class="accordion-body">
                        ${checkboxes}
                    </div>
                </div>
            </div>
        `;
    },

    generateRoomCheckboxes: function(apartmentNumber, roomsPerApartment) {
        var checkboxes = '';
        for (var j = 1; j <= roomsPerApartment; j++) {
            checkboxes += `
                <label class="me-2">
                    <input type="checkbox" name="apartments[${apartmentNumber-1}][double_rooms][]" value="${j}"> 
                    ${TRANSLATION.apartment.room} ${j}
                </label>
            `;
        }
        return checkboxes;
    },

    wrapInScrollableBox: function(content) {
        return `
            <div class="card" style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd;">
                <div class="card-body p-2">
                    ${content}
                </div>
            </div>
        `;
    }
};

// ===========================
// MAIN APPLICATION
// ===========================
var BuildingApp = {
    init: function() {
        StatsManager.init();
        BuildingManager.init();
        SearchManager.init();
        DoubleRoomManager.init();
        Select2Manager.initAll();
    }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
    try {
        BuildingApp.init();
    } catch (error) {
        console.error('Error initializing BuildingApp:', error);
    }
});
</script>
@endpush