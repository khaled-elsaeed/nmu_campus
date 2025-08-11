@extends('layouts.home-content-only')

@section('title', __('Complete Your Profile | Housing'))

@push('styles')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.7.7-1.5 1.5 1.5 1.5-.7.7L4.3 6l1.5-1.5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .invalid-feedback { 
        display: block; 
    }
    
    .nav-pills .nav-link { 
        border-radius: 0.375rem; 
        margin: 0 2px; 
    }
    
    .d-none { 
        display: none !important; 
    }
    
    /* Disabled field styles */
    .field-disabled {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
    }
    
    .field-disabled:hover {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
    }
    
    /* Navigation tab validation styles */
    .nav-link.is-valid {
        background-color: var(--bs-success) !important;
        color: #fff !important;
        border-color: var(--bs-success) !important;
        box-shadow: 0 2px 4px 0 rgba(var(--bs-success-rgb), 0.4);
    }
    
    .nav-link.is-invalid {
        background-color: var(--bs-danger) !important;
        color: #fff !important;
        border-color: var(--bs-danger) !important;
        box-shadow: 0 2px 4px 0 rgba(var(--bs-danger-rgb), 0.4);
    }
    
    .nav-link.is-invalid.active,
    .nav-link.is-valid.active {
        background-color: var(--bs-primary) !important;
        color: #fff !important;
        border-color: var(--bs-primary) !important;
        box-shadow: 0 2px 4px 0 rgba(var(--bs-primary-rgb), 0.4);
    }
</style>
@endpush

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-12 col-md-11 col-lg-9 position-relative">
            <!-- Loading overlay -->
            <div id="form-loader" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 1050; background: rgba(255,255,255,0.7); backdrop-filter: blur(2px);">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
            </div> <!-- End of Loading overlay -->

            <!-- Profile form card -->
            <div class="card shadow-sm">
                <!-- Navigation tabs -->
                <div class="card-header border-bottom">
                    <ul class="nav nav-pills nav-justified gap-2" id="profile-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active d-flex align-items-center" id="step1-tab" data-bs-target="#step1" type="button" role="tab" aria-controls="step1" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex flex-column align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-user fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 1') }}</span>
                                        <span class="small">{{ __('Personal Info') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step2-tab" data-bs-target="#step2" type="button" role="tab" aria-controls="step2" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-phone fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 2') }}</span>
                                        <span class="small">{{ __('Contact Info') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step3-tab" data-bs-target="#step3" type="button" role="tab" aria-controls="step3" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-book fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 3') }}</span>
                                        <span class="small">{{ __('Academic Info') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step4-tab" data-bs-target="#step4" type="button" role="tab" aria-controls="step4" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-user-voice fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 4') }}</span>
                                        <span class="small">{{ __('Parent Info') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step5-tab" data-bs-target="#step5" type="button" role="tab" aria-controls="step5" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-group fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 5') }}</span>
                                        <span class="small">{{ __('Sibling Info') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step6-tab" data-bs-target="#step6" type="button" role="tab" aria-controls="step6" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-first-aid fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 6') }}</span>
                                        <span class="small">{{ __('Emergency Contact') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step7-tab" data-bs-target="#step7" type="button" role="tab" aria-controls="step7" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-check-shield fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ __('Step 7') }}</span>
                                        <span class="small">{{ __('Terms') }}</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div> <!-- End of Navigation tabs -->   
                <!-- Form content -->
                <div class="card-body">
                    <form id="profileForm" action="" method="POST" autocomplete="off">
                        @csrf
                        <!-- Tab content -->
                        <div class="tab-content" id="profile-tabContent">
                            <!-- Personal Information step -->
                            <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab" tabindex="0">
                                <x-complete-profile.personal-info />
                            </div>

                            <!-- Contact Information step -->
                            <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab" tabindex="0">
                                <x-complete-profile.contact-info />
                            </div>

                            <!-- Academic Information step -->
                            <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab" tabindex="0">
                                <x-complete-profile.academic-info />
                            </div>

                            <!-- Parent Information step -->
                            <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab" tabindex="0">
                                <x-complete-profile.parent-info />
                            </div>

                            <!-- Sibling Information step -->
                            <div class="tab-pane fade" id="step5" role="tabpanel" aria-labelledby="step5-tab" tabindex="0">
                                <x-complete-profile.sibling-info />
                            </div>

                            <!-- Emergency Contact Information step -->
                            <div class="tab-pane fade" id="step6" role="tabpanel" aria-labelledby="step6-tab" tabindex="0">
                                <x-complete-profile.emergency-contact />
                            </div>

                            <!-- Terms and Conditions step -->
                            <div class="tab-pane fade" id="step7" role="tabpanel" aria-labelledby="step7-tab" tabindex="0">
                                <x-complete-profile.terms />
                            </div>
                        </div> <!-- End of Tab content -->
                    </form>
                </div> <!-- End of Form content -->
            </div> <!-- End of Profile form card -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
// Route constants for AJAX requests
var ROUTES = {
    profile: {
        fetch: '{{ route('profile.resident.student.fetch') }}',
        submit: '{{ route('profile.resident.student.submit') }}',
    },
    faculty: {
        fetch: '{{ route('academic.faculties.all') }}',
    },
    program: {
        fetch: '{{ route('academic.programs.all', ':facultyId') }}',
    },
    governorate: {
        fetch: '{{ route('governorates.all') }}',
    },
    city: {
        fetch: '{{ route('cities.all', ':governorateId') }}',
    },
    countries: {
        fetch: '{{ route('countries.all') }}',
    },
    nationalities: {
        fetch: '{{ route('nationalities.all') }}',
    }
};
</script>
<script src="{{ asset('js/pages/profile/resident/student/complete.js') }}"></script>
@endpush