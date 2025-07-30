@extends('layouts.home-content-only')

@section('title', 'Complete Your Profile | NMU Campus')

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
    .invalid-feedback { display: block; }
    .nav-pills .nav-link { border-radius: 0.375rem; margin: 0 2px; }
    .d-none { display: none !important; }
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
</style>
@endpush

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-12 col-md-11 col-lg-9 position-relative">

            <!-- Start Loader -->
            <div id="form-loader" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 1050; background: rgba(255,255,255,0.7); backdrop-filter: blur(2px);">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <!-- End Loader -->

            <div class="card shadow-sm">
                <div class="card-header border-bottom">
                    <ul class="nav nav-pills nav-justified gap-2" id="profile-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active d-flex align-items-center" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1" type="button" role="tab" aria-controls="step1" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex flex-column align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-user fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 1</span>
                                        <span class="small">Personal Info</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2" type="button" role="tab" aria-controls="step2" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-phone fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 2</span>
                                        <span class="small">Contact Info</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3" type="button" role="tab" aria-controls="step3" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-book fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 3</span>
                                        <span class="small">Academic Info</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step4-tab" data-bs-toggle="pill" data-bs-target="#step4" type="button" role="tab" aria-controls="step4" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-user-voice fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 4</span>
                                        <span class="small">Parent Info</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step5-tab" data-bs-toggle="pill" data-bs-target="#step5" type="button" role="tab" aria-controls="step5" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-group fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 5</span>
                                        <span class="small">Sibling Info</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step6-tab" data-bs-toggle="pill" data-bs-target="#step6" type="button" role="tab" aria-controls="step6" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-first-aid fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 6</span>
                                        <span class="small">Emergency Contact</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="step7-tab" data-bs-toggle="pill" data-bs-target="#step7" type="button" role="tab" aria-controls="step7" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 d-flex align-items-center justify-content-center" style="width: 32px;">
                                        <i class='bx bx-check-shield fs-4'></i>
                                    </div>
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">Step 7</span>
                                        <span class="small">Terms</span>
                                    </div>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form id="profileForm" action="" method="POST" autocomplete="off">
                        @csrf
                        <div class="tab-content" id="profile-tabContent">
                            <!-- Step 1: Personal Information -->
                            <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab" tabindex="0">
                                <x-complete-profile.personal-info />
                            </div>
                            <!-- Step 2: Contact Information -->
                            <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab" tabindex="0">
                                <x-complete-profile.contact-info />
                            </div>
                            <!-- Step 3: Academic Information -->
                            <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab" tabindex="0">
                                <x-complete-profile.academic-info />
                            </div>
                            <!-- Step 4: Parent Information -->
                            <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab" tabindex="0">
                                <x-complete-profile.parent-info />
                            </div>
                            <!-- Step 5: Sibling Information -->
                            <div class="tab-pane fade" id="step5" role="tabpanel" aria-labelledby="step5-tab" tabindex="0">
                                <x-complete-profile.sibling-info />
                            </div>
                            <!-- Step 6: Emergency Contact Information -->
                            <div class="tab-pane fade" id="step6" role="tabpanel" aria-labelledby="step6-tab" tabindex="0">
                                <x-complete-profile.emergency-contact />
                            </div>
                            <!-- Step 7: Terms and Conditions -->
                            <div class="tab-pane fade" id="step7" role="tabpanel" aria-labelledby="step7-tab" tabindex="0">
                                <x-complete-profile.terms />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
/**
 * Complete Profile Page JS
 *
 * Structure:
 * - Utils: Common utility functions (from global public/js/utils.js)
 * - ApiService: Handles API requests
 * - ROUTES: API routes constants
 * - ValidationService: Handles form validation logic
 * - NavigationManager: Handles tab navigation
 * - ConditionalFieldsManager: Handles conditional field display
 * - FormManager: Handles form submission and field interactions
 * - CompleteProfileApp: Initializes all managers
 */
// ===========================
// ROUTES CONSTANTS
// ===========================
var ROUTES = {
  profile: {
    fetch: '{{ route('profile.fetch') }}',
    submit: '{{ route('profile.submit') }}',
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

// ===========================
// API SERVICE
// ===========================
var ApiService = {
  /**
   * Generic AJAX request
   * @param {object} options
   * @returns {jqXHR}
   */
  request: function(options) {
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    return $.ajax(options);
  },
  /**
   * Fetch the current user's profile data
   * @returns {jqXHR}
   */
  fetchProfile: function() {
    return this.request({ url: ROUTES.profile.fetch, method: 'GET' });
  },

  /**
   * Submit the completed profile (final submission)
   * @param {object} data
   * @returns {jqXHR}
   */
  submitProfile: function(data) {
    return this.request({ url: ROUTES.profile.submit, method: 'POST', data: data });
  },
  /**
   * Fetch faculty data
   * @returns {jqXHR}
   */
  fetchFaculty: function() {
    return this.request({ url: ROUTES.faculty.fetch, method: 'GET' });
  },
  /**
   * Fetch program data
   * @param {string} facultyId
   * @returns {jqXHR}
   */
  fetchProgram: function(facultyId) {
    return this.request({ url: ROUTES.program.fetch.replace(':facultyId', facultyId), method: 'GET' });
  },
  /**
   * Fetch governorate data
   * @returns {jqXHR}
   */
  fetchGovernorate: function() {
    return this.request({ url: ROUTES.governorate.fetch, method: 'GET' });
  },
  /**
   * Fetch city data
   * @param {string} governorateId
   * @returns {jqXHR}
   */
  fetchCity: function(governorateId) {
    return this.request({ url: ROUTES.city.fetch.replace(':governorateId', governorateId), method: 'GET' });
  },
  /**
   * Fetch countries data
   * @returns {jqXHR}
   */
  fetchCountries: function() {
    return this.request({ url: ROUTES.countries.fetch, method: 'GET' });
  },
  /**
   * Fetch nationalities data
   * @returns {jqXHR}
   */
  fetchNationalities: function() {
    return this.request({ url: ROUTES.nationalities.fetch, method: 'GET' });
  }
};

// ===========================
// PROFILE MANAGER
// ===========================
var ProfileManager = {
  /**
   * Initialize profile manager
   */
  init: async function() {
    // Wait for all dropdowns to be populated before fetching profile
    try {
      await Promise.all([
        this.populateGovernorates(),
        this.populateParentGovernorates(),
        this.populateFaculties(),
        this.populateCountries(),
        this.populateNationalities()
      ]);
    } catch (error) {
      // Log error but continue
      console.error('Error populating dropdowns:', error);
    }

    // Now fetch and populate profile
    await this.fetchAndPopulateProfile();

    // Hide loader after all done
    FormManager.hideLoader();
  },

  /**
   * Fetch profile data and populate form
   * Returns a Promise
   */
  fetchAndPopulateProfile: function() {
    var self = this;
    return new Promise(function(resolve, reject) {
      ApiService.fetchProfile()
        .done(function(response) {
          if (response.success && response.data) {
            self.populateProfileData(response.data);
            Utils.showSuccess('Profile data loaded successfully.', true);
          } else {
            console.log('No profile data found, keeping form empty.');
            Utils.showSuccess('Profile form ready. Please complete your information.');
          }
          resolve();
        })
        .fail(function(xhr) {
          if (xhr.status === 404) {
            console.log('Profile not found, keeping form empty.');
            Utils.showSuccess('Profile form ready. Please complete your information.');
            resolve();
          } else {
            Utils.handleAjaxError(xhr, 'Failed to load profile data.');
            reject(xhr);
          }
        });
    });
  },

  /**
   * Populate form with profile data
   * @param {object} data - Profile data
   */
  populateProfileData: function(data) {
    // Step 1: Personal Information
    this.populatePersonalInfo(data.personal_info || {});
    
    // Step 2: Contact Information
    this.populateContactInfo(data.contact_info || {});
    
    // Step 3: Academic Information
    this.populateAcademicInfo(data.academic_info || {});
    
    // Step 4: Parent Information
    this.populateParentInfo(data.parent_info || {});
    
    // Step 5: Sibling Information
    this.populateSiblingInfo(data.sibling_info || {});
    
    // Step 6: Emergency Contact Information
    this.populateEmergencyContact(data.emergency_contact || {});
    
    // Step 7: Terms (usually not populated from server)
    this.populateTerms(data.terms || {});
    
    // Trigger change events to update conditional fields
    this.triggerFieldChanges();
  },

  /**
   * Populate personal information fields
   * @param {object} data
   */
  populatePersonalInfo: function(data) {
    if (data.national_id) $('#nationalId').val(data.national_id);
    if (data.full_name_arabic) $('#fullNameArabic').val(data.full_name_arabic);
    if (data.full_name_english) $('#fullNameEnglish').val(data.full_name_english);
    if (data.birth_date) $('#birthDate').val(data.birth_date);
    if (data.gender) $('#gender').val(data.gender);
    if (data.nationality_id) $('#nationality').val(data.nationality_id);
  },

  /**
   * Populate contact information fields
   * @param {object} data
   */
  populateContactInfo: function(data) {
    if (data.email) $('#email').val(data.email);
    if (data.mobile_number) $('#mobileNumber').val(data.mobile_number);
    
    // Handle governorate and city population
    if (data.governorate_id) {
      // Set governorate value (dropdowns are already populated)
      $('#governorate').val(data.governorate_id);
      
      // Trigger change to load cities
      $('#governorate').trigger('change');
      
      // Wait for cities to load, then set city value
      setTimeout(function() {
        if (data.city_id) {
          $('#city').val(data.city_id);
        }
      }, 1000);
    }
  },

  /**
   * Populate academic information fields
   * @param {object} data
   */
  populateAcademicInfo: function(data) {
    if (data.student_id) $('#studentId').val(data.student_id);
    if (data.academic_year) $('#academicYear').val(data.academic_year);
    if (data.gpa) $('#gpa').val(data.gpa);
    
    // Handle faculty and program population
    if (data.faculty_id) {
      // Set faculty value (dropdowns are already populated)
      $('#faculty').val(data.faculty_id);
      
      // Trigger change to load programs
      $('#faculty').trigger('change');
      
      // Wait for programs to load, then set program value
      setTimeout(function() {
        if (data.program_id) {
          $('#program').val(data.program_id);
        }
      }, 1000);
    }
  },

  /**
   * Populate parent information fields
   * @param {object} data
   */
  populateParentInfo: function(data) {
    // Set parent name
    if (data.parent_name) $('#parentName').val(data.parent_name);

    // Set parent mobile
    if (data.parent_mobile) $('#parentPhone').val(data.parent_mobile);

    // Set parent email
    if (data.parent_email) $('#parentEmail').val(data.parent_email);

    // Set is parent abroad
    if (typeof data.is_abroad !== 'undefined') {
      $('#isParentAbroad').val(data.is_abroad ? 'yes' : 'no');
      $('#isParentAbroad').trigger('change');

      setTimeout(function() {
        if (data.is_abroad) {
          // Set abroad country if available
          if (data.parent_country_id) {
            $('#abroadCountry').val(data.parent_country_id);
          }
        } else {
          // If not abroad, show living with parent and address fields if available
          if (typeof data.living_with_parent !== 'undefined') {
            $('#livingWithParent').val(data.living_with_parent ? 'yes' : 'no');
            $('#livingWithParent').trigger('change');

            setTimeout(function() {
              if (data.living_with_parent === false || data.living_with_parent === 'no') {
                if (data.parent_governorate_id) {
                  $('#parentGovernorate').val(data.parent_governorate_id);
                  $('#parentGovernorate').trigger('change');
                  setTimeout(function() {
                    if (data.parent_city_id) {
                      $('#parentCity').val(data.parent_city_id);
                    }
                  }, 500);
                }
              }
            }, 300);
          }
        }
      }, 300);
    }
  },

  /**
   * Populate sibling information fields
   * @param {object} data
   */
  populateSiblingInfo: function(data) {
    if (data.has_sibling_in_dorm) {
      $('#hasSiblingInDorm').val(data.has_sibling_in_dorm);
      // Trigger change to show/hide sibling details
      $('#hasSiblingInDorm').trigger('change');
      
      // Set sibling details after a short delay
      setTimeout(function() {
        if (data.has_sibling_in_dorm === 'yes') {
          if (data.sibling_gender) $('#siblingGender').val(data.sibling_gender);
          if (data.sibling_name) $('#siblingName').val(data.sibling_name);
          if (data.sibling_national_id) $('#siblingNationalId').val(data.sibling_national_id);
          if (data.sibling_faculty_id) $('#siblingFaculty').val(data.sibling_faculty_id);
        }
      }, 300);
    }
  },

  /**
   * Populate emergency contact information fields
   * @param {object} data
   */
  populateEmergencyContact: function(data) {
    if (data.emergency_name) $('#emergencyName').val(data.emergency_name);
    if (data.emergency_relation) $('#emergencyRelation').val(data.emergency_relation);
    if (data.emergency_mobile) $('#emergencyMobile').val(data.emergency_mobile);
    if (data.emergency_address) $('#emergencyAddress').val(data.emergency_address);
  },

  /**
   * Populate terms information fields
   * @param {object} data
   */
  populateTerms: function(data) {
    if (data.terms_accepted) {
      $('#termsCheckbox').prop('checked', data.terms_accepted === true || data.terms_accepted === '1');
    }
  },

  /**
   * Trigger change events for fields that affect conditional display
   */
  triggerFieldChanges: function() {
    // Trigger changes for fields that affect conditional field visibility
    setTimeout(function() {
      $('#isParentAbroad').trigger('change');
      $('#livingWithParent').trigger('change');
      $('#hasSiblingInDorm').trigger('change');
    }, 1000);
  },

  /**
   * Get form data for submission
   * @returns {object}
   */
  getFormData: function() {
    var formData = new FormData($('#profileForm')[0]);
    var data = {};
    
    // Convert FormData to object
    for (var pair of formData.entries()) {
      data[pair[0]] = pair[1];
    }
    
    return data;
  },

  /**
   * Submit profile data
   * @param {function} callback - Success callback
   */
  submitProfile: function(callback) {
    var self = this;
    var formData = this.getFormData();
    
    // Show loading state
    Utils.setLoadingState($('#profileForm button[type="submit"]'), true, {
      loadingText: 'Submitting...',
      loadingIcon: 'bx bx-loader-alt bx-spin me-1'
    });

    FormManager.showLoader();

    ApiService.submitProfile(formData)
      .done(function(response) {
        if (response.success) {
          Utils.showSuccess(response.message || 'Profile submitted successfully!');
        } else {
          FormManager.hideLoader();
          Utils.showError(response.message || 'Failed to submit profile.');
        }
      })
      .fail(function(xhr) {
        Utils.handleAjaxError(xhr, 'Failed to submit profile.');
        FormManager.hideLoader();
      })
      .always(function() {
        // Hide loading state
        Utils.setLoadingState($('#profileForm button[type="submit"]'), false, {
          normalText: 'Submit Profile',
          normalIcon: 'bx bx-check me-1'
        });
        FormManager.hideLoader();

      });
  },

  /**
   * Populate governorates dropdown
   * @returns {Promise}
   */
  populateGovernorates: function() {
    var self = this;
    return new Promise(function(resolve, reject) {
      // Check if governorates are already populated
      if ($('#governorate option').length > 1) {
        resolve();
        return;
      }
      
      // Fetch governorates from API
      ApiService.fetchGovernorate()
        .done(function(response) {
          if (response.success && response.data) {
            Utils.populateSelect($('#governorate'), response.data, {
              placeholder: 'Select Governorate',
              valueField: 'id',
              textField: 'name'
            });
            resolve();
          } else {
            reject('Failed to load governorates');
          }
        })
        .fail(function(xhr) {
          reject('Failed to load governorates: ' + xhr.statusText);
        });
    });
  },

  /**
   * Populate parent governorates dropdown
   * @returns {Promise}
   */
  populateParentGovernorates: function() {
    return new Promise(function(resolve, reject) {
      // Check if parent governorates are already populated
      if ($('#parentGovernorate option').length > 1) {
        resolve();
        return;
      }

      // Fetch governorates from API
      ApiService.fetchGovernorate()
        .done(function(response) {
          if (response.success && response.data) {
            Utils.populateSelect($('#parentGovernorate'), response.data, {
              placeholder: 'Select Parent Governorate',
              valueField: 'id',
              textField: 'name'
            });
            resolve();
          } else {
            reject('Failed to load parent governorates');
          }
        })
        .fail(function(xhr) {
          reject('Failed to load parent governorates: ' + xhr.statusText);
        });
    });
  },

  /**
   * Populate faculties dropdown
   * @returns {Promise}
   */
  populateFaculties: function() {
    var self = this;
    return new Promise(function(resolve, reject) {
      // Check if faculties are already populated
      if ($('#faculty option').length > 1) {
        resolve();
        return;
      }
      
      // Fetch faculties from API
      ApiService.fetchFaculty()
        .done(function(response) {
          if (response.success && response.data) {
            Utils.populateSelect($('#faculty'), response.data, {
              placeholder: 'Select Faculty',
              valueField: 'id',
              textField: 'name'
            });
            Utils.populateSelect($('#siblingFaculty'), response.data, {
                placeholder: 'Select Sibling Faculty',
                valueField: 'id',
                textField: 'name'
              });
            resolve();
          } else {
            reject('Failed to load faculties');
          }
        })
        .fail(function(xhr) {
          reject('Failed to load faculties: ' + xhr.statusText);
        });
    });
  },

  /**
   * Populate countries dropdown
   * @returns {Promise}
   */
  populateCountries: function() {
    var self = this;
    return new Promise(function(resolve, reject) {
      // Check if countries are already populated
      if ($('#abroadCountry option').length > 1) {
        resolve();
        return;
      }
      
      // Fetch countries from API
      ApiService.fetchCountries()
        .done(function(response) {
          if (response.success && response.data) {
            Utils.populateSelect($('#abroadCountry'), response.data, {
              placeholder: 'Select Country',
              valueField: 'id',
              textField: 'name'
            });
            resolve();
          } else {
            reject('Failed to load countries');
          }
        })
        .fail(function(xhr) {
          reject('Failed to load countries: ' + xhr.statusText);
        });
    });
  },

  /**
   * Populate nationalities dropdown
   * @returns {Promise}
   */
  populateNationalities: function() {
    var self = this;
    return new Promise(function(resolve, reject) {
      // Check if nationalities are already populated
      if ($('#nationality option').length > 1) {
        resolve();
        return;
      }
      
      // Fetch nationalities from API
      ApiService.fetchNationalities()
        .done(function(response) {
          if (response.success && response.data) {
            Utils.populateSelect($('#nationality'), response.data, {
              placeholder: 'Select Nationality',
              valueField: 'id',
              textField: 'name'
            });
            resolve();
          } else {
            reject('Failed to load nationalities');
          }
        })
        .fail(function(xhr) {
          reject('Failed to load nationalities: ' + xhr.statusText);
        });
    });
  }
};

// ===========================
// VALIDATION SERVICE
// ===========================
var ValidationService = {
  validator: null,

  /**
   * Initialize jQuery Validation
   */
  init: function() {
    this.setupValidationRules();
    this.initializeValidator();
  },

  /**
   * Setup validation rules and messages
   */
  setupValidationRules: function() {
    // Add custom validation methods
    $.validator.addMethod('conditionalRequired', function(value, element, params) {
      var condition = params[0];
      var conditionValue = params[1];
      return $(condition).val() !== conditionValue || value.length > 0;
    }, 'This field is required based on your selection.');

    $.validator.addMethod('checkedRequired', function(value, element) {
      return $(element).is(':checked');
    }, 'You must accept the terms and conditions.');

    // Regex-based validations
    $.validator.addMethod('egyptianNationalId', function(value, element) {
      if (!value) return true;
      var regex = /^[0-9]{14}$/;
      return regex.test(value);
    }, 'Please enter a valid 14-digit Egyptian National ID.');

    $.validator.addMethod('egyptianMobile', function(value, element) {
      if (!value) return true;
      var regex = /^(010|011|012|015)[0-9]{8}$/;
      return regex.test(value);
    }, 'Please enter a valid Egyptian mobile number (010/011/012/015 followed by 8 digits).');

    $.validator.addMethod('studentId', function(value, element) {
      if (!value) return true;
      var regex = /^[0-9]{8,12}$/;
      return regex.test(value);
    }, 'Please enter a valid student ID (8-12 digits).');

    $.validator.addMethod('arabicName', function(value, element) {
      if (!value) return true;
      var regex = /^[\u0600-\u06FF\s]+$/;
      return regex.test(value);
    }, 'Please enter name in Arabic only.');

    $.validator.addMethod('englishName', function(value, element) {
      if (!value) return true;
      var regex = /^[A-Za-z\s]+$/;
      return regex.test(value);
    }, 'Please enter name in English only.');

    // Age validation
    $.validator.addMethod('minimumAge', function(value, element, params) {
      if (!value) return true;
      var birthDate = new Date(value);
      var today = new Date();
      var age = today.getFullYear() - birthDate.getFullYear();
      var monthDiff = today.getMonth() - birthDate.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }

      return age >= params;
    }, 'You must be at least {0} years old.');

    // GPA validation
    $.validator.addMethod('gpaRange', function(value, element, params) {
      if (!value) return true;
      var gpa = parseFloat(value);
      return gpa >= params[0] && gpa <= params[1];
    }, 'GPA must be between {0} and {1}.');

    // Dependency validations
    $.validator.addMethod('dependsOn', function(value, element, params) {
      var dependentField = params.field;
      var dependentValue = params.value;
      var operator = params.operator || 'equals';
      var dependentFieldValue = $(dependentField).val();

      switch (operator) {
        case 'equals':
          return dependentFieldValue !== dependentValue || value.length > 0;
        case 'not_equals':
          return dependentFieldValue === dependentValue || value.length > 0;
        case 'greater_than':
          return parseFloat(dependentFieldValue) <= parseFloat(dependentValue) || value.length > 0;
        case 'less_than':
          return parseFloat(dependentFieldValue) >= parseFloat(dependentValue) || value.length > 0;
        case 'contains':
          return dependentFieldValue.indexOf(dependentValue) === -1 || value.length > 0;
        default:
          return true;
      }
    }, 'This field is required based on your previous selection.');

    // Compare fields validation
    $.validator.addMethod('compareField', function(value, element, params) {
      if (!value) return true;
      var otherField = params.field;
      var operator = params.operator;
      var otherValue = $(otherField).val();

      switch (operator) {
        case 'equals':
          return value === otherValue;
        case 'not_equals':
          return value !== otherValue;
        case 'greater_than':
          return parseFloat(value) > parseFloat(otherValue);
        case 'less_than':
          return parseFloat(value) < parseFloat(otherValue);
        case 'greater_equal':
          return parseFloat(value) >= parseFloat(otherValue);
        case 'less_equal':
          return parseFloat(value) <= parseFloat(otherValue);
        default:
          return true;
      }
    }, 'Field comparison validation failed.');

    // Email domain validation
    $.validator.addMethod('emailDomain', function(value, element, params) {
      if (!value) return true;
      var allowedDomains = params;
      var emailDomain = value.split('@')[1];
      return allowedDomains.includes(emailDomain);
    }, 'Please use an email from allowed domains.');

    // Password strength validation
    $.validator.addMethod('strongPassword', function(value, element) {
      if (!value) return true;
      var strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])/;
      return strongRegex.test(value) && value.length >= 8;
    }, 'Password must be at least 8 characters and contain uppercase, lowercase, number, and special character.');
  },

  /**
   * Initialize the validator
   */
  initializeValidator: function() {
    var self = this;

    this.validator = $('#profileForm').validate({
      ignore: ':hidden:not(.validate-hidden)',
      errorClass: 'is-invalid',
      validClass: '',
      errorElement: 'div',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.after(error);
      },
      highlight: function(element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function(element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
      },
      rules: self.getValidationRules(),
      messages: self.getValidationMessages()
    });
  },

  /**
   * Get validation rules
   * @returns {object}
   */
  getValidationRules: function() {
    return {
      // Step 1: Personal Information
      nationalId: {
        required: true,
        egyptianNationalId: true
      },
      fullNameArabic: {
        required: true,
        arabicName: true,
        minlength: 2
      },
      fullNameEnglish: {
        required: true,
        englishName: true,
        minlength: 2
      },
      birthDate: {
        required: true,
        date: true,
        minimumAge: 17
      },
      gender: {
        required: true
      },
      nationality: {
        required: true
      },

      // Step 2: Contact Information
      email: {
        required: true,
        email: true,
        emailDomain: ['student.nmu.edu.eg', 'nmu.edu.eg']
      },
      mobileNumber: {
        required: true,
        egyptianMobile: true
      },
      governorate: {
        required: true
      },
      city: {
        required: true
      },

      // Step 3: Academic Information
      studentId: {
        required: true,
        studentId: true
      },
      faculty: {
        required: true
      },
      program: {
        required: true
      },
      academicYear: {
        required: true
      },
      gpa: {
        required: true,
        gpaRange: [0.0, 4.0]
      },

      // Step 4: Parent Information - Basic
      fatherName: {
        required: true,
        arabicName: true
      },
      motherName: {
        required: true,
        arabicName: true
      },
      parentMobile: {
        required: true,
        egyptianMobile: true
      },
      isParentAbroad: {
        required: true
      },

      // Step 4: Parent abroad conditional validation
      abroadCountry: {
        dependsOn: {
          field: '#isParentAbroad',
          value: 'yes',
          operator: 'equals'
        }
      },
      livingWithParent: {
        dependsOn: {
          field: '#isParentAbroad',
          value: 'no',
          operator: 'equals'
        }
      },
      parentGovernorate: {
        dependsOn: {
          field: '#livingWithParent',
          value: 'no',
          operator: 'equals'
        }
      },
      parentCity: {
        dependsOn: {
          field: '#livingWithParent',
          value: 'no',
          operator: 'equals'
        }
      },

      // Step 5: Sibling Information
      hasSiblingInDorm: {
        required: true
      },
      siblingGender: {
        dependsOn: {
          field: '#hasSiblingInDorm',
          value: 'yes',
          operator: 'equals'
        }
      },
      siblingName: {
        dependsOn: {
          field: '#hasSiblingInDorm',
          value: 'yes',
          operator: 'equals'
        },
        arabicName: true
      },
      siblingNationalId: {
        dependsOn: {
          field: '#hasSiblingInDorm',
          value: 'yes',
          operator: 'equals'
        },
        egyptianNationalId: true,
        compareField: {
          field: '#nationalId',
          operator: 'not_equals'
        }
      },
      siblingFaculty: {
        dependsOn: {
          field: '#hasSiblingInDorm',
          value: 'yes',
          operator: 'equals'
        }
      },

      // Step 6: Emergency Contact
      emergencyName: {
        dependsOn: {
          field: '#parentsAbroad',
          value: 'yes',
          operator: 'equals'
        },
        required: true,
        arabicName: true
      },
      emergencyRelation: {
        dependsOn: {
          field: '#parentsAbroad',
          value: 'yes',
          operator: 'equals'
        },
        required: true
      },
      emergencyMobile: {
        dependsOn: {
          field: '#parentsAbroad',
          value: 'yes',
          operator: 'equals'
        },
        required: true,
        egyptianMobile: true,
        compareField: {
          field: '#mobileNumber',
          operator: 'not_equals'
        }
      },
      emergencyAddress: {
        dependsOn: {
          field: '#parentsAbroad',
          value: 'yes',
          operator: 'equals'
        },
        required: true,
        minlength: 10
      },

      // Step 7: Terms validation
      termsCheckbox: {
        checkedRequired: true
      }
    };
  },

  /**
   * Get validation messages
   * @returns {object}
   */
  getValidationMessages: function() {
    return {
      // Step 1: Personal Information
      nationalId: {
        required: 'National ID is required.',
        egyptianNationalId: 'Please enter a valid 14-digit Egyptian National ID.'
      },
      fullNameArabic: {
        required: 'Arabic name is required.',
        arabicName: 'Please enter name in Arabic characters only.',
        minlength: 'Name must be at least 2 characters long.'
      },
      fullNameEnglish: {
        required: 'English name is required.',
        englishName: 'Please enter name in English characters only.',
        minlength: 'Name must be at least 2 characters long.'
      },
      birthDate: {
        required: 'Birth date is required.',
        date: 'Please enter a valid date.',
        minimumAge: 'You must be at least 17 years old.'
      },
      gender: 'Please select your gender.',
      nationality: 'Please select your nationality.',

      // Step 2: Contact Information
      email: {
        required: 'Email address is required.',
        email: 'Please enter a valid email address.',
        emailDomain: 'Please use your university email address.'
      },
      mobileNumber: {
        required: 'Mobile number is required.',
        egyptianMobile: 'Please enter a valid Egyptian mobile number.'
      },
      governorate: 'Please select your governorate.',
      city: 'Please select your city.',

      // Step 3: Academic Information
      studentId: {
        required: 'Student ID is required.',
        studentId: 'Please enter a valid student ID (8-12 digits).'
      },
      faculty: 'Please select your faculty.',
      program: 'Please select your program.',
      academicYear: 'Please select your academic year.',
      gpa: {
        required: 'GPA is required.',
        gpaRange: 'GPA must be between 0.0 and 4.0.'
      },

      // Step 4: Parent Information
      fatherName: {
        required: 'Father\'s name is required.',
        arabicName: 'Please enter father\'s name in Arabic.'
      },
      motherName: {
        required: 'Mother\'s name is required.',
        arabicName: 'Please enter mother\'s name in Arabic.'
      },
      parentMobile: {
        required: 'Parent\'s mobile number is required.',
        egyptianMobile: 'Please enter a valid Egyptian mobile number.'
      },
      isParentAbroad: 'Please specify if parent lives abroad.',
      abroadCountry: 'Please select the country where your parent lives.',
      livingWithParent: 'Please specify if you live with your parent.',
      parentGovernorate: 'Please select parent\'s governorate.',
      parentCity: 'Please select parent\'s city.',

      // Step 5: Sibling Information
      hasSiblingInDorm: 'Please specify if you have a sibling in the dorm.',
      siblingGender: 'Please select sibling\'s gender.',
      siblingName: {
        dependsOn: 'Please enter sibling\'s name.',
        arabicName: 'Please enter sibling\'s name in Arabic.'
      },
      siblingNationalId: {
        dependsOn: 'Please enter sibling\'s national ID.',
        egyptianNationalId: 'Please enter a valid 14-digit national ID.',
        compareField: 'Sibling\'s national ID must be different from yours.'
      },
      siblingFaculty: 'Please select sibling\'s faculty.',

      // Step 6: Emergency Contact
      emergencyName: {
        required: 'Emergency contact name is required.',
        arabicName: 'Please enter name in Arabic.'
      },
      emergencyRelation: 'Please specify your relation to emergency contact.',
      emergencyMobile: {
        required: 'Emergency contact mobile is required.',
        egyptianMobile: 'Please enter a valid Egyptian mobile number.',
        compareField: 'Emergency contact number must be different from your mobile.'
      },
      emergencyAddress: {
        required: 'Emergency contact address is required.',
        minlength: 'Address must be at least 10 characters long.'
      },

      // Step 7: Terms
      termsCheckbox: 'You must accept the terms and conditions to proceed.'
    };
  },

  /**
   * Validate a specific step
   * @param {string} tabSelector
   * @returns {boolean}
   */
  validateStep: function(tabSelector) {
    if (!this.validator) return true;

    var $step = $(tabSelector);
    var stepId = $step.attr('id');
    var $stepBtn = $('[data-bs-target="#' + stepId + '"]');
    var isValid = true;

    // Remove previous validation state
    $stepBtn.removeClass('is-valid is-invalid');

    // Validate all enabled and visible (or .validate-hidden) fields in the step
    $step.find('input, select, textarea').each(function() {
      var $field = $(this);
      var shouldValidate = !$field.is(':disabled') && 
        (!$field.is(':hidden') || $field.hasClass('validate-hidden'));

      if (shouldValidate) {
        if (!ValidationService.validator.element(this)) {
          isValid = false;
        }
      }
    });

    // Update step button state
    $stepBtn
      .toggleClass('is-valid', isValid)
      .toggleClass('is-invalid', !isValid);

    return isValid;
  },

  /**
   * Validate all steps
   * @returns {boolean}
   */
  validateAllSteps: function() {
    if (!this.validator) {
      return true;
    }

    return this.validator.form();
  },

  /**
   * Find first invalid step
   * @returns {number|null}
   */
  findFirstInvalidStep: function() {
    for (var i = 1; i <= 7; i++) {
      if (!this.validateStep('#step' + i)) {
        return i;
      }
    }
    return null;
  },

  /**
   * Reset validation for a field
   * @param {string} fieldSelector
   */
  resetField: function(fieldSelector) {
    if (this.validator) {
      this.validator.resetForm();
      $(fieldSelector).removeClass('is-invalid is-valid');
    }
  },

  /**
   * Update validation rules dynamically
   */
  updateConditionalValidation: function() {
    if (!this.validator) {
      return;
    }

    // Re-validate conditional fields when their dependencies change
    var conditionalFields = [
      '#abroadCountry',
      '#parentGovernorate',
      '#parentCity',
      '#siblingGender',
      '#siblingName',
      '#siblingNationalId',
      '#siblingFaculty'
    ];

    conditionalFields.forEach(function(field) {
      ValidationService.validator.element(field);
    });
  }
};

// ===========================
// NAVIGATION MANAGER
// ===========================
var NavigationManager = {
  // Configuration for steps to skip
  SkippedSteps: [
    { step: 6, selector: "isParentAbroad", condition: '=', value: 'no' },
  ],

  /**
   * Initialize navigation manager
   */
  init: function() {
    this.bindEvents();
  },

  /**
   * Bind all navigation events
   */
  bindEvents: function() {
    this.handleNextButton();
    this.handlePreviousButton();
    this.handleTabClick();
  },


  /**
   * Check if a step should be skipped based on conditions
   * @param {string} stepSelector - Step selector (e.g., '#step6')
   * @returns {boolean}
   */
  shouldSkipStep: function(stepSelector) {
    var self = this;
    var stepNumber = self.extractStepNumber(stepSelector.replace('#', ''));
    
    if (!stepNumber) return false;

    // Find matching skip rules for this step
    var matchingRules = self.SkippedSteps.filter(function(rule) {
      return rule.step === stepNumber;
    });

    // Check each rule
    for (var i = 0; i < matchingRules.length; i++) {
      var rule = matchingRules[i];
      var elementValue = $('#' + rule.selector).val();
      
      if (self.evaluateCondition(elementValue, rule.condition, rule.value)) {
        return true; // Skip this step
      }
    }

    return false;
  },

  /**
   * Evaluate condition based on operator
   * @param {string} leftValue - Current form value
   * @param {string} operator - Comparison operator
   * @param {string} rightValue - Expected value
   * @returns {boolean}
   */
  evaluateCondition: function(leftValue, operator, rightValue) {
    // Convert to appropriate types for comparison
    var left = this.convertValue(leftValue);
    var right = this.convertValue(rightValue);

    switch (operator) {
      case '=':
        return left == right;
      case '!=':
        return left != right;
      case '>':
        return parseFloat(left) > parseFloat(right);
      case '<':
        return parseFloat(left) < parseFloat(right);
      case '>=':
        return parseFloat(left) >= parseFloat(right);
      case '<=':
        return parseFloat(left) <= parseFloat(right);
      case 'contains':
        return String(left).toLowerCase().includes(String(right).toLowerCase());
      case 'empty':
        return !left || left.trim() === '';
      case 'not_empty':
        return left && left.trim() !== '';
      default:
        return false;
    }
  },

  /**
   * Convert value to appropriate type
   * @param {string} value
   * @returns {string|number}
   */
  convertValue: function(value) {
    if (value === null || value === undefined) return '';
    if (!isNaN(value) && !isNaN(parseFloat(value))) {
      return parseFloat(value);
    }
    return String(value).trim();
  },

  /**
   * Extract step number from step ID
   * @param {string} stepId - e.g., "step3"
   * @returns {number|null}
   */
  extractStepNumber: function(stepId) {
    var match = stepId.match(/^step(\d+)$/);
    return match ? parseInt(match[1], 10) : null;
  },

  /**
   * Generate step selector from step number
   * @param {number} stepNumber
   * @returns {string}
   */
  generateStepSelector: function(stepNumber) {
    return '#step' + stepNumber;
  },

  /**
   * Find next available step
   * @param {number} currentStep
   * @returns {string|null}
   */
  findNextAvailableStep: function(currentStep) {
    var nextStep = currentStep + 1;
    var maxSteps = 20;
    
    while (nextStep <= maxSteps) {
      var nextSelector = this.generateStepSelector(nextStep);
      
      // Check if step exists in DOM
      if ($(nextSelector).length === 0) {
        break;
      }
      
      // Check if step should be skipped
      if (!this.shouldSkipStep(nextSelector)) {
        return nextSelector;
      }
      ValidationService.validateStep(nextSelector)      
      nextStep++;
    }
    
    return null;
  },

  /**
   * Find previous available step
   * @param {number} currentStep
   * @returns {string|null}
   */
  findPreviousAvailableStep: function(currentStep) {
    var prevStep = currentStep - 1;
    
    while (prevStep > 0) {
      var prevSelector = this.generateStepSelector(prevStep);
      console.log('Checking previous step:', prevSelector);

      // Check if step exists in DOM
      if ($(prevSelector).length === 0) {
        console.log('Step does not exist in DOM:', prevSelector);
        prevStep--;
        continue;
      }
      
      // Check if step should be skipped
      if (!this.shouldSkipStep(prevSelector)) {
        console.log('Found previous available step:', prevSelector);
        return prevSelector;
      } else {
        console.log('Step should be skipped:', prevSelector);
      }
      
      prevStep--;
    }
    
    console.log('No previous available step found before step', currentStep);
    return null;
  },

  /**
   * Handle next button clicks
   */
  handleNextButton: function() {
    var self = this;

    $(document).on('click', '.next-Btn', function(e) {
      e.preventDefault();
      
      var $tabPane = $(this).closest('.tab-pane');
      var currentStepId = $tabPane.attr('id');
      var currentStepNumber = self.extractStepNumber(currentStepId);

      if (!currentStepNumber) {
        console.warn('Could not extract step number from:', currentStepId);
        return;
      }

      // Validate current step before proceeding
      if (!ValidationService.validateStep('#' + currentStepId)) {
        return;
      }

      // Find next available step
      var nextStepSelector = self.findNextAvailableStep(currentStepNumber);
      
      if (nextStepSelector) {
        self.showTab(nextStepSelector);
      } else {
        // No more steps available - could be end of form
        console.log('No more available steps after step', currentStepNumber);
      }
    });
  },

  /**
   * Handle previous button clicks
   */
  handlePreviousButton: function() {
    var self = this;

    $(document).on('click', '.prev-Btn', function(e) {
      e.preventDefault();

      var $tabPane = $(this).closest('.tab-pane');
      var currentStepId = $tabPane.attr('id');
      var currentStepNumber = self.extractStepNumber(currentStepId);

      console.log('Previous button clicked. Current step ID:', currentStepId, 'Current step number:', currentStepNumber);

      if (!currentStepNumber) {
        console.warn('Could not extract step number from:', currentStepId);
        return;
      }

      // Find previous available step
      var prevStepSelector = self.findPreviousAvailableStep(currentStepNumber);

      console.log('Previous available step selector:', prevStepSelector);

      if (prevStepSelector) {
        self.showTab(prevStepSelector);
        console.log('Navigated to previous step:', prevStepSelector);
      } else {
        console.log('No previous available steps before step', currentStepNumber);
      }
    });
  },

  /**
   * Handle tab click validation and restrictions
   */
  handleTabClick: function() {
    var self = this;
    
    $(document).on('show.bs.tab', 'button[data-bs-toggle="pill"]', function(event) {
      var $target = $(event.target);
      var targetTabId = $target.attr('data-bs-target');
      
      // Check if target tab should be skipped (not available)
      if (self.shouldSkipStep(targetTabId)) {
        event.preventDefault();
        var tabName = $target.text().trim();
        Utils.showError('Step "' + tabName + '" is not available based on your current selections.', true);
        return false;
      }

      // Check if user is trying to skip ahead without completing required steps
      var allTabs = $('button[data-bs-toggle="pill"]:not(.d-none)');
      var targetIndex = allTabs.index(event.target);
      var firstInvalidTabIndex = self.findFirstInvalidTabIndex(allTabs, targetIndex);

      if (firstInvalidTabIndex !== -1) {
        event.preventDefault();
        var invalidTabName = allTabs.eq(firstInvalidTabIndex).text().trim();
        Utils.showError('Please complete all required fields in "' + invalidTabName + '" before proceeding.', true);
        allTabs.eq(firstInvalidTabIndex).tab('show');
        return false;
      }
    });
  },

  /**
   * Find first invalid tab that needs completion
   * @param {jQuery} allTabs - All visible tabs
   * @param {number} targetIndex - Index of target tab
   * @returns {number} Index of first invalid tab, or -1 if none
   */
  findFirstInvalidTabIndex: function(allTabs, targetIndex) {
    var firstInvalidIndex = -1;

    for (var i = 0; i < targetIndex; i++) {
      var $tab = allTabs.eq(i);
      var tabPaneId = $tab.attr('data-bs-target');
      
      if (tabPaneId && !ValidationService.validateStep(tabPaneId)) {
        firstInvalidIndex = i;
        break;
      }
    }

    return firstInvalidIndex;
  },

  /**
   * Show specific tab and update navigation state
   * @param {string} tabId - Tab selector (e.g., '#step3')
   */
  showTab: function(tabId) {
    // Hide all tab panes
    $('.tab-pane').removeClass('show active');
    
    // Show target tab pane
    $(tabId).addClass('show active');
    
    // Update nav links
    $('.nav-link').removeClass('active');
    $('button[data-bs-target="' + tabId + '"]').addClass('active');
    
    // Scroll to top of form
    $('html, body').animate({ scrollTop: 0 }, 300);
    
    // Focus first input in the new step
    setTimeout(function() {
      $(tabId + ' input:visible:first, ' + tabId + ' select:visible:first, ' + tabId + ' textarea:visible:first').focus();
    }, 100);
  },

  /**
   * Get current active step number
   * @returns {number|null}
   */
  getCurrentStep: function() {
    var $activeTab = $('.tab-pane.active');
    if ($activeTab.length) {
      return this.extractStepNumber($activeTab.attr('id'));
    }
    return null;
  },

  /**
   * Check if step is available (not skipped)
   * @param {number} stepNumber
   * @returns {boolean}
   */
  isStepAvailable: function(stepNumber) {
    var stepSelector = this.generateStepSelector(stepNumber);
    return !this.shouldSkipStep(stepSelector);
  },

  /**
   * Get list of all available steps
   * @returns {Array<number>}
   */
  getAvailableSteps: function() {
    var self = this;
    var availableSteps = [];
    
    $('.tab-pane[id^="step"]').each(function() {
      var stepNumber = self.extractStepNumber($(this).attr('id'));
      if (stepNumber && self.isStepAvailable(stepNumber)) {
        availableSteps.push(stepNumber);
      }
    });
    
    return availableSteps.sort(function(a, b) { return a - b; });
  }
};

// ===========================
// CONDITIONAL FIELDS MANAGER
// ===========================
var ConditionalFieldsManager = {
  /**
   * Initialize conditional fields
   */
  init: function() {
    this.bindEvents();
    this.triggerInitialChanges();
  },
  /**
   * Bind conditional field events
   */
  bindEvents: function() {
    this.handleGovernorateChange();
    this.handleParentGovernorateChange();
    this.handleFacultyChange();
    this.handleParentAbroadChange();
    this.handleLivingWithParentChange();
    this.handleSiblingInDormChange();
  },
  /**
   * Handle governorate change
   */
  handleGovernorateChange: function() {
    $('#governorate').change(function() {
      var governorateId = $(this).val();
      
      if (governorateId) {
        // Fetch cities for selected governorate
        ApiService.fetchCity(governorateId)
          .done(function(response) {
            if (response.success && response.data) {
              Utils.populateSelect($('#city'), response.data, {
                placeholder: 'Select City',
                valueField: 'id',
                textField: 'name'
              });
            } else {
              $('#city').html('<option value="">No cities available</option>');
            }
          })
          .fail(function(xhr) {
            console.error('Failed to load cities:', xhr);
            $('#city').html('<option value="">Error loading cities</option>');
          });
      } else {
        $('#city').html('<option value="">Select City</option>');
      }
    });
  },
  /**
   * Handle parent governorate change (parent address)
   */
  handleParentGovernorateChange: function() {
    $('#parentGovernorate').change(function() {
      var governorateId = $(this).val();

      if (governorateId) {
        // Fetch cities for selected parent governorate
        ApiService.fetchCity(governorateId)
          .done(function(response) {
            if (response.success && response.data) {
              Utils.populateSelect($('#parentCity'), response.data, {
                placeholder: 'Select City',
                valueField: 'id',
                textField: 'name'
              });
            } else {
              $('#parentCity').html('<option value="">No cities available</option>');
            }
          })
          .fail(function(xhr) {
            console.error('Failed to load parent cities:', xhr);
            $('#parentCity').html('<option value="">Error loading cities</option>');
          });
      } else {
        $('#parentCity').html('<option value="">Select City</option>');
      }
    });
  },
  /**
   * Handle faculty change
   */
  handleFacultyChange: function() {
    $('#faculty').change(function() {
      var facultyId = $(this).val();

      if (facultyId) {
        // Fetch programs for selected faculty
        ApiService.fetchProgram(facultyId)
          .done(function(response) {
            if (response.success && response.data) {
              Utils.populateSelect($('#program'), response.data, {
                placeholder: 'Select Program',
                valueField: 'id',
                textField: 'name'
              });
            } else {
              $('#program').html('<option value="">No programs available</option>');
            }
          })
          .fail(function(xhr) {
            console.error('Failed to load programs:', xhr);
            $('#program').html('<option value="">Error loading programs</option>');
          });
      } else {
        $('#program').html('<option value="">Select Program</option>');
      }
    });
  },
  /**
   * Handle parent abroad change
   */
  handleParentAbroadChange: function() {
    $('#isParentAbroad').change(function() {
      var value = $(this).val();

      if (value === 'yes') {
        ConditionalFieldsManager.showAbroadFields();
      } else if (value === 'no') {
        ConditionalFieldsManager.showDomesticFields();
      } else {
        ConditionalFieldsManager.hideAllParentFields();
      }

      // Update validation after field visibility changes
      ValidationService.updateConditionalValidation();
    });
  },
  /**
   * Handle living with parent change
   */
  handleLivingWithParentChange: function() {
    $('#livingWithParent').change(function() {
      var value = $(this).val();

      if (value === 'no') {
        ConditionalFieldsManager.showParentAddressFields();
      } else {
        ConditionalFieldsManager.hideParentAddressFields();
      }

      // Update validation after field visibility changes
      ValidationService.updateConditionalValidation();
    });
  },
  /**
   * Handle sibling in dorm change
   */
  handleSiblingInDormChange: function() {
    $('#hasSiblingInDorm').change(function() {
      var value = $(this).val();

      if (value === 'yes') {
        ConditionalFieldsManager.showSiblingDetails();
      } else {
        ConditionalFieldsManager.hideSiblingDetails();
      }

      // Update validation after field visibility changes
      ValidationService.updateConditionalValidation();
    });
  },
  /**
   * Show abroad country fields
   */
  showAbroadFields: function() {
    $('#abroadCountryDiv').removeClass('d-none');
    $('#livingWithParentDiv, #parentAddressDiv').addClass('d-none');
  },
  /**
   * Show domestic parent fields
   */
  showDomesticFields: function() {
    $('#abroadCountryDiv').addClass('d-none');
    $('#livingWithParentDiv').removeClass('d-none');

    if ($('#livingWithParent').val() === 'no') {
      this.showParentAddressFields();
    } else {
      this.hideParentAddressFields();
    }
  },
  /**
   * Hide all parent fields
   */
  hideAllParentFields: function() {
    $('#abroadCountryDiv, #livingWithParentDiv, #parentAddressDiv').addClass('d-none');
  },
  /**
   * Show parent address fields
   */
  showParentAddressFields: function() {
    $('#parentAddressDiv').removeClass('d-none');
  },
  /**
   * Hide parent address fields
   */
  hideParentAddressFields: function() {
    $('#parentAddressDiv').addClass('d-none');
  },
  /**
   * Show sibling details
   */
  showSiblingDetails: function() {
    $('#siblingDetails').removeClass('d-none');
  },
  /**
   * Hide sibling details
   */
  hideSiblingDetails: function() {
    $('#siblingDetails').addClass('d-none');
  },
  /**
   * Trigger initial changes for all conditional fields
   */
  triggerInitialChanges: function() {
    $('#isParentAbroad').trigger('change');
    $('#livingWithParent').trigger('change');
    $('#hasSiblingInDorm').trigger('change');
    $('#parentGovernorate').trigger('change');
  }
};

// ===========================
// FORM MANAGER
// ===========================
var FormManager = {
  /**
   * Initialize form manager
   * Now async: waits for user to click "I Understand" before continuing.
   */
  init: async function() {
    await this.showInitialSwalAlert();
    this.bindEvents();
  },

  /**
   * Show initial SweetAlert to user about data responsibility
   * Returns a Promise that resolves only when user clicks "I Understand"
   */
  showInitialSwalAlert: function() {
    return Swal.fire({
      icon: 'warning',
      title: 'Important Notice',
      html: 'All data entered is your own responsibility. Any incorrect or false information may cause problems or lead to rejection of your application.<br><br>' +
            '<b>If you encounter a problem with your National ID, please do not submit the form and contact support.</b>',
      confirmButtonText: 'I Understand',
      allowOutsideClick: false,
      allowEscapeKey: false
    });
  },

  /**
   * Bind form events
   */
  bindEvents: function() {
    this.handleFormSubmission();
  },

  /**
   * Show the loader 
   */
  showLoader: function() {
    $('#form-loader').removeClass('d-none');
  },

  /**
   * Hide the loader
   */
  hideLoader: function() {
    $('#form-loader').addClass('d-none');
  },
  /**
   * Handle form submission
   */
  handleFormSubmission: function() {
    $('#profileForm').on('submit', function(e) {
      e.preventDefault(); // Prevent default form submission

      if (!FormManager.validateFormSubmission()) {
        Utils.showError('Please fill in all required fields correctly.');
        return false;
      }

      ProfileManager.submitProfile();
    });
  },
  /**
   * Validate form submission
   * @returns {boolean}
   */
  validateFormSubmission: function() {
    var isValid = ValidationService.validateAllSteps();

    if (!isValid) {
      var firstInvalidStep = ValidationService.findFirstInvalidStep();
      if (firstInvalidStep !== null) {
        NavigationManager.showTab(NavigationManager.generateStepSelector(firstInvalidStep));
      }
      return false;
    }

    return true;
  }
};

// ===========================
// MAIN APPLICATION
// ===========================
var CompleteProfileApp = {
  /**
   * Initialize the entire application
   * Now async: waits for FormManager.init() to complete before proceeding.
   */
  init: async function() {
    ValidationService.init();
    NavigationManager.init();
    ConditionalFieldsManager.init();
    await FormManager.init();
    await ProfileManager.init();
  }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function() {
  // Call async init and ignore returned promise
  CompleteProfileApp.init();
});
</script>
@endpush