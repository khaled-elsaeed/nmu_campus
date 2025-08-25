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
                                        <span class="small">{{ __('Guardian Info') }}</span>
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

                            <!-- Guardian Information step -->
                            <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab" tabindex="0">
                                <x-complete-profile.guardian-info />
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

var TRANSLATIONS = {
  placeholders: {
    select_governorate: @json(__("Select Governorate")),
    select_guardian_governorate: @json(__("Select Guardian Governorate")),
    select_emergency_contact_governorate: @json(__("Select Emergency Contact Governorate")),
    select_faculty: @json(__("Select Faculty")),
    select_sibling_faculty: @json(__("Select Sibling Faculty")),
    select_country: @json(__("Select Country")),
    select_nationality: @json(__("Select Nationality")),
    select_academic_year: @json(__("Select Academic Year")),
    select_sibling_relationship: @json(__("Select Sibling Relationship")),
    select: @json(__("Select")),

    city: {
      select: @json(__('Select City')),
      noCities: @json(__('No cities available')),
      errorLoading: @json(__('Error loading cities')),
      disableField: @json(__('Select a governorate first'))
    },

    guardianCity: {
      select: @json(__('Select Guardian City')),
      noCities: @json(__('No cities available')),
      errorLoading: @json(__('Error loading guardian cities')),
      disableField: @json(__('Select a guardian governorate first'))
    },

    emergencyContactCity: {
      select: @json(__('Select Emergency Contact City')),
      noCities: @json(__('No cities available')),
      errorLoading: @json(__('Error loading emergency contact cities')),
      disableField: @json(__('Select an emergency contact governorate first'))
    },

    program: {
      select: @json(__('Select Program')),
      noPrograms: @json(__('No programs available')),
      errorLoading: @json(__('Error loading programs')),
      disableField: @json(__('Select a faculty first'))
    },
    academic_year: {
      select: @json(__('Select Level')),
    },
    sibling: {
        select: @json(__('Select Sibling')),
    },
    siblingToStayWith: {
      select: @json(__('Select Sibling to Stay With')),
      noSiblings: @json(__('No siblings available')),
      errorLoading: @json(__('Error loading siblings')),
    }
  },

  validation: {
    required: @json(__('This field is required')),
    required_conditional: @json(__('This field is required based on your previous selection')),
    required_checked: @json(__('You must agree to the terms')),
    egyptian_national_id: @json(__('Please enter a valid 14-digit Egyptian National ID')),
    international_phone: @json(__('Please enter a valid international phone number')),
    egyptian_phone: @json(__('Please enter a valid Egyptian phone number starting with 010 or 011 or 012 or 015 followed by 8 digits')),
    arabic_name: @json(__('Please enter a valid Arabic name using Arabic characters only')),
    english_name: @json(__('Please enter a valid English name using English letters only')),
    minimum_age: @json(__('You must be at least {0} years old')),
    gpa_range: @json(__('GPA must be between {0} and {1}')),
    dependency_required: @json(__('This field is required based on your previous selection')),
    field_comparison_failed: @json(__('Field must be different from the primary field')),
    email_domain: @json(__('Please use an email address with a valid domain (e.g., nmu.edu.eg)')),
    date: @json(__('Please enter a valid date')),
    number: @json(__('Please enter a valid number')),
    minlength: @json(__('Field must be at least {min} characters long')),
    email: @json(__('Please enter a valid email address')),
    gender: @json(__('Please select a gender')),
    nationality: @json(__('Please select a nationality')),
    governorate: @json(__('Please select a governorate')),
    city: @json(__('Please select a city')),
    faculty: @json(__('Please select a faculty')),
    program: @json(__('Please select a program')),
    academic_year: @json(__('Please select an academic year')),
    guardian_relationship: @json(__('Please select a relationship')),
    is_guardianan_abroad: @json(__('Please specify if the guardian is abroad')),
    guardian_abroad_country: @json(__('Please select a country')),
    living_with_guardian: @json(__('Please specify if you live with the guardian')),
    guardian_governorate: @json(__('Please select a guardian governorate')),
    guardian_city: @json(__('Please select a guardian city')),
    guardian_country: @json(__('Please select a guardian country')),
    has_sibling_in_dorm: @json(__('Please specify if you have a sibling in the dorm')),
    sibling_gender: @json(__('Please select the sibling\'s gender')),
    sibling_faculty: @json(__('Please select the sibling\'s faculty')),
    emergency_contact_relationship: @json(__('Please select the emergency contact relationship')),
    emergency_contact_governorate: @json(__('Please select the emergency contact governorate')),
    emergency_contact_city: @json(__('Please select the emergency contact city')),
    emergency_contact_street: @json(__('Please enter the emergency contact street address')),
    terms_checkbox: @json(__('You must accept the terms and conditions')),
    default: @json(__('This field is invalid')),

    gpa: {
      dependsOn: @json(__('GPA is required when available')),
      gpaRange: @json(__('GPA must be between 0.0 and 4.0'))
    },

    score: {
      dependsOn: @json(__('Score is required for new students')),
      number: @json(__('Please enter a valid score'))
    },

    depends_on: @json(__('This field is required based on your previous selection')),
    compare_field: @json(__('This field must be different from the primary field'))
  },

  // Field labels for validation messages
  fields: {
    national_id: @json(__('National ID')),
    name_ar: @json(__('Arabic Name')),
    name_en: @json(__('English Name')),
    birth_date: @json(__('Birth Date')),
    phone: @json(__('Phone Number')),
    street: @json(__('Street Address')),
    academic_id: @json(__('Academic ID')),
    academic_email: @json(__('Academic Email')),
    guardian_name_ar: @json(__('Guardian Arabic Name')),
    guardian_name_en: @json(__('Guardian English Name')),
    guardian_phone: @json(__('Guardian Phone Number')),
    guardian_national_id: @json(__('Guardian National ID')),
    sibling_name_ar: @json(__('Sibling Arabic Name')),
    sibling_name_en: @json(__('Sibling English Name')),
    sibling_national_id: @json(__('Sibling National ID')),
    emergency_contact_name_ar: @json(__('Emergency Contact Arabic Name')),
    emergency_contact_name_en: @json(__('Emergency Contact English Name')),
    emergency_contact_phone: @json(__('Emergency Contact Phone Number'))
  },

  // Action button texts
  actions: {
    submitting: @json(__('Submitting...')),
    submit: @json(__('Submit'))
  },

  // Messages for dialogs and alerts
  messages: {
    stay_in_old_room_not_available: @json(__('Stay in Previous Room (Not Available)')),
    validationError: @json(__('Please complete all required fields correctly')),
    dataResponsibility: {
      title: @json(__('Data Responsibility')),
      message: @json(__('You are responsible for providing accurate information. If any data is incorrect, you will not be able to complete the form. Submitting false or misleading information may result in your account being suspended in accordance with university policies.')),
      confirmButtonText: @json(__('I Understand'))
    }
  }
};

</script>
<script src="{{ asset('js/pages/profile/resident/student/complete.js') }}"></script>
@endpush