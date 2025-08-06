/**
 * Structure:
 * - Utils: Common utility functions (from global public/js/utils.js)
 * - ApiService: Handles API requests
 * - ValidationService: Handles form validation logic
 * - NavigationManager: Handles tab navigation
 * - ConditionalFieldsManager: Handles conditional field display
 * - FormManager: Handles form submission and field interactions
 * - CompleteProfileApp: Initializes all managers
 */
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
		return this.request({
			url: ROUTES.profile.fetch,
			method: 'GET'
		});
	},

	/**
	 * Submit the completed profile (final submission)
	 * @param {object} data
	 * @returns {jqXHR}
	 */
	submitProfile: function(data) {
		return this.request({
			url: ROUTES.profile.submit,
			method: 'POST',
			data: data
		});
	},
	/**
	 * Fetch faculty data
	 * @returns {jqXHR}
	 */
	fetchFaculty: function() {
		return this.request({
			url: ROUTES.faculty.fetch,
			method: 'GET'
		});
	},
	/**
	 * Fetch program data
	 * @param {string} facultyId
	 * @returns {jqXHR}
	 */
	fetchProgram: function(facultyId) {
		return this.request({
			url: ROUTES.program.fetch.replace(':facultyId', facultyId),
			method: 'GET'
		});
	},
	/**
	 * Fetch governorate data
	 * @returns {jqXHR}
	 */
	fetchGovernorate: function() {
		return this.request({
			url: ROUTES.governorate.fetch,
			method: 'GET'
		});
	},
	/**
	 * Fetch city data
	 * @param {string} governorateId
	 * @returns {jqXHR}
	 */
	fetchCity: function(governorateId) {
		return this.request({
			url: ROUTES.city.fetch.replace(':governorateId', governorateId),
			method: 'GET'
		});
	},
	/**
	 * Fetch countries data
	 * @returns {jqXHR}
	 */
	fetchCountries: function() {
		return this.request({
			url: ROUTES.countries.fetch,
			method: 'GET'
		});
	},
	/**
	 * Fetch nationalities data
	 * @returns {jqXHR}
	 */
	fetchNationalities: function() {
		return this.request({
			url: ROUTES.nationalities.fetch,
			method: 'GET'
		});
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
				this.populateFaculties(),
				this.populateCountries(),
				this.populateNationalities()
			]);
		} catch (error) {

			console.error('Error populating dropdowns:', error);
		}

		await this.fetchAndPopulateProfile();

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

		this.populatePersonalInfo(data.personal_info || {});

		this.populateContactInfo(data.contact_info || {});

		this.populateAcademicInfo(data.academic_info || {});

		this.populateParentInfo(data.parent_info || {});

		this.populateSiblingInfo(data.sibling_info || {});

		this.populateEmergencyContact(data.emergency_contact || {});

		this.populateTerms(data.terms || {});

		this.triggerFieldChanges();
	},

	/**
	 * Populate personal information fields
	 * @param {object} data
	 */
	populatePersonalInfo: function(data) {
		if (data.national_id) $('#national-id').val(data.national_id);
		if (data.name_ar) $('#name-ar').val(data.name_ar);
		if (data.name_en) $('#name-en').val(data.name_en);
		if (data.birthdate) $('#birth-date').val(data.birthdate);
		if (data.gender) $('#gender').val(data.gender);
		if (data.nationality_id) $('#nationality').val(data.nationality_id);
	},

	/**
	 * Populate contact information fields
	 * @param {object} data
	 */
	populateContactInfo: function(data) {
		if (data.phone) $('#phone').val(data.phone);

		if (data.governorate_id) {
			$('#governorate').val(data.governorate_id);

			// Store city_id to set after cities are loaded
			if (data.city_id) {
				$('#governorate').data('pending-city-id', data.city_id);
			}

			$('#governorate').trigger('change');
		}
		if (data.street) $('#street').val(data.street);
	},

	/**
	 * Populate academic information fields
	 * @param {object} data
	 */
	populateAcademicInfo: function(data) {

		if (data.academic_id) $('#academic-id').val(data.academic_id);
		if (data.academic_email) $('#academic-email').val(data.academic_email);

		if (data.academic_year) $('#academic-year').val(data.academic_year);
		if (data.gpa) $('#gpa').val(data.gpa);

		if (data.faculty_id) {
			$('#faculty').val(data.faculty_id);
			if (data.program_id) {
				$('#faculty').data('pending-program-id', data.program_id);
			}
			$('#faculty').trigger('change');
		}
		if (data.score) $('#score').val(data.score);

		if (data.is_new_comer !== undefined && data.is_new_comer !== null) {
			$('#is-new-comer').val(data.is_new_comer.toString());
		}
	},

	/**
	 * Populate parent information fields
	 * @param {object} data
	 */
	populateParentInfo: function(data) {

		if (data.name_ar) $('#parent-name-ar').val(data.name_ar);
		if (data.name_en) $('#parent-name-en').val(data.name_en);
		if (data.phone) $('#parent-phone').val(data.phone);
		if (data.email) $('#parent-email').val(data.email);
		if (data.national_id) $('#parent-national-id').val(data.national_id);
		if (data.relationship) $('#parent-relationship').val(data.relationship);

		var isAbroad = (data.is_abroad) ? data.is_abroad : false;
		$('#is-parent-abroad').val(isAbroad ? 'yes' : 'no');
		$('#is-parent-abroad').trigger('change');

		setTimeout(function() {
			if (isAbroad) {
				if (data.country_id) {
					$('#parent-abroad-country').val(data.country_id);
				}
			} else if (data.governorate_id) {
				$('#parent-governorate').val(data.governorate_id);

				// Store city_id to set after cities are loaded
				if (data.city_id) {
					$('#parent-governorate').data('pending-city-id', data.city_id);
				}

				$('#parent-governorate').trigger('change');
			}
		}, 300);
	},

	/**
	 * Populate sibling information fields
	 * @param {object} data
	 */
	populateSiblingInfo: function(data) {

		if (data.has_sibling_in_dorm) {
			$('#has-sibling-in-dorm').val(data.has_sibling_in_dorm);
			$('#has-sibling-in-dorm').trigger('change');
			setTimeout(function() {
				if (data.has_sibling_in_dorm === 'yes') {
					if (data.name_ar) $('#sibling-name-ar').val(data.name_ar);
					if (data.name_en) $('#sibling-name-en').val(data.name_en);
					if (data.gender) $('#sibling-gender').val(data.gender);
					if (data.national_id) $('#sibling-national-id').val(data.national_id);
					if (data.faculty_id) $('#sibling-faculty').val(data.faculty_id);
				}
			}, 300);
		}
	},

	/**
	 * Populate emergency contact information fields
	 * @param {object} data
	 */
	populateEmergencyContact: function(data) {
		if (data.name_en) $('#emergency-contact-name-en').val(data.name_en);
		if (data.name_ar) $('#emergency-contact-name-ar').val(data.name_ar);
		if (data.phone) $('#emergency-contact-phone').val(data.phone);
		if (data.relationship) $('#emergency-contact-relationship').val(data.relationship);
		if (data.governorate_id) {
			$('#emergency-contact-governorate').val(data.governorate_id);

			if (data.city_id) {
				$('#emergency-contact-governorate').data('pending-city-id', data.city_id);
			}

			$('#emergency-contact-governorate').trigger('change');
		}
		if (data.street) $('#emergency-contact-street').val(data.street);
		if (data.notes) $('#emergency-contact-notes').val(data.notes);
	},

	/**
	 * Populate terms information fields
	 * @param {object} data
	 */
	populateTerms: function(data) {
		if (data.terms_accepted) {
			$('#terms-Checkbox').prop('checked', data.terms_accepted === true || data.terms_accepted === '1');
		}
	},

	/**
	 * Trigger change events for fields that affect conditional display
	 */
	triggerFieldChanges: function() {

		setTimeout(function() {
			$('#is-parent-abroad').trigger('change');
			$('#living-with-parent').trigger('change');
			$('#has-sibling-in-dorm').trigger('change');
			$('#is-new-comer').trigger('change');
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

			ApiService.fetchGovernorate()
				.done(function(response) {
					if (response.success && response.data) {
						if ($('#governorate option').length <= 1) {
							Utils.populateSelect($('#governorate'), response.data, {
								placeholder: 'Select Governorate',
								valueField: 'id',
								textField: 'name'
							});
						}
						if ($('#parent-governorate option').length <= 1) {
							Utils.populateSelect($('#parent-governorate'), response.data, {
								placeholder: 'Select Parent Governorate',
								valueField: 'id',
								textField: 'name'
							});
						}
						if ($('#emergency-contact-governorate option').length <= 1) {
							Utils.populateSelect($('#emergency-contact-governorate'), response.data, {
								placeholder: 'Select Emergency Contact Governorate',
								valueField: 'id',
								textField: 'name'
							});
						}
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
			if ($('#parent-governorate option').length > 1) {
				resolve();
				return;
			}

			ApiService.fetchGovernorate()
				.done(function(response) {
					if (response.success && response.data) {
						Utils.populateSelect($('#parent-governorate'), response.data, {
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

			ApiService.fetchFaculty()
				.done(function(response) {
					if (response.success && response.data) {
						if ($('#faculty option').length <= 1) {
							Utils.populateSelect($('#faculty'), response.data, {
								placeholder: 'Select Faculty',
								valueField: 'id',
								textField: 'name'
							});
						}
						if ($('#sibling-faculty option').length <= 1) {
							Utils.populateSelect($('#sibling-faculty'), response.data, {
								placeholder: 'Select Sibling Faculty',
								valueField: 'id',
								textField: 'name'
							});
						}
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
			if ($('#parent-abroad-country option').length > 1) {
				resolve();
				return;
			}

			ApiService.fetchCountries()
				.done(function(response) {
					if (response.success && response.data) {
						Utils.populateSelect($('#parent-abroad-country'), response.data, {
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

			if ($('#nationality option').length > 1) {
				resolve();
				return;
			}

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

		// Updated international phone validation
		$.validator.addMethod('internationalPhone', function(value, element) {
			if (!value) return false;
			var cleanValue = value.replace(/[\s\-\(\)]/g, '');
			var regex = /^\+?[1-9]\d{6,15}$/;
			return regex.test(cleanValue);
		}, 'Please enter a valid international phone number (e.g., +966501234567 or +12025550123).');

		// Updated egyptian phone validation
		$.validator.addMethod('egyptianPhone', function(value, element) {
			if (!value) return false;
			var cleanValue = value.replace(/[\s\-\(\)]/g, '');
			var regex = /^(010|011|012|015)[0-9]{8}$/;
			return regex.test(cleanValue);
		}, 'Please enter a valid Egyptian mobile number (010/011/012/015 followed by 8 digits).');

		/**
		 * Unified phone validation method that checks if the parent is abroad.
		 * If isAbroad is 'yes', uses international regex, else uses Egyptian regex.
		 * Usage: add to rules as: phone: { required: true, parentPhoneConditional: '#is-parent-abroad' }
		 */
		$.validator.addMethod('parentPhoneConditional', function(value, element, param) {
			var isAbroad = $(param).val();
			if (!value) return false;
			var cleanValue = value.replace(/[\s\-\(\)]/g, '');
			if (isAbroad === 'yes') {

				var intlRegex = /^\+?[1-9]\d{6,15}$/;
				return intlRegex.test(cleanValue);
			} else {

				var egyRegex = /^(010|011|012|015)[0-9]{8}$/;
				return egyRegex.test(cleanValue);
			}
		}, function(params, element) {
			var isAbroad = $(params).val();
			if (isAbroad === 'yes') {
				return 'Please enter a valid international phone number (e.g., +966501234567 or +12025550123).';
			} else {
				return 'Please enter a valid Egyptian mobile number (010/011/012/015 followed by 8 digits).';
			}
		});

		$.validator.addMethod('academicId', function(value, element) {
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
			var $dependentField = $(dependentField);
			var dependentFieldValue;

			// If the dependent field is a select, get its value properly
			if ($dependentField.is('select')) {
				dependentFieldValue = $dependentField.find('option:selected').val();
			} else {
				dependentFieldValue = $dependentField.val();
			}

			// Log the relevant values for debugging
			console.log('dependsOn validation:', {
				value: value,
				element: element,
				params: params,
				dependentField: dependentField,
				dependentValue: dependentValue,
				operator: operator,
				dependentFieldValue: dependentFieldValue
			});

			switch (operator) {
				case 'equals':
					return dependentFieldValue !== dependentValue || (value != null && value.length > 0);
				case 'not_equals':
					return dependentFieldValue === dependentValue || (value != null && value.length > 0);
				case 'greater_than':
					return parseFloat(dependentFieldValue) <= parseFloat(dependentValue) || (value != null && value.length > 0);
				case 'less_than':
					return parseFloat(dependentFieldValue) >= parseFloat(dependentValue) || (value != null && value.length > 0);
				case 'contains':
					return dependentFieldValue.indexOf(dependentValue) === -1 || (value != null && value.length > 0);
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
			name_ar: {
				required: true,
				arabicName: true,
				minlength: 2
			},
			name_en: {
				required: true,
				englishName: true,
				minlength: 2
			},
			national_id: {
				required: true,
				egyptianNationalId: true
			},
			birth_date: {
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
			governorate: {
				required: true
			},
			city: {
				required: true
			},
			street: {
				required: true,
				minlength: 10
			},
			phone: {
				required: true,
				egyptianPhone: true
			},

			// Step 3: Academic Information
			faculty: {
				required: true
			},
			program: {
				required: true
			},
			academic_year: {
				required: true
			},
			gpa: {
				dependsOn: {
					field: '#gpa-available',
					value: 'yes',
					operator: 'equals'
				},
				gpaRange: [0.0, 4.0]
			},
			score: {
				dependsOn: {
					field: '#gpa-available',
					value: 'no',
					operator: 'equals'
				},
				number: true
			},
			academic_id: {
				required: true,
				academicId: true
			},
			academic_email: {
				required: true,
				email: true,
				emailDomain: ['nmu.edu.eg']
			},

			// Step 4: Parent Information - Basic
			parent_relationship: {
				required: true
			},
			parent_name_ar: {
				required: true,
				arabicName: true
			},
			parent_name_en: {
				required: true,
				englishName: true
			},
			parent_phone: {
				required: true,
				parentPhoneConditional: '#is-parent-abroad'
			},
			parent_national_id: {
				required: true,
				egyptianNationalId: true
			},
			parent_email: {
				required: false,
				email: true
			},
			is_parent_abroad: {
				required: true
			},

			// Step 4: Parent abroad conditional fields
			parent_abroad_country: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				}
			},
			living_with_parent: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'no',
					operator: 'equals'
				}
			},
			parent_governorate: {
				dependsOn: {
					field: '#living-with-parent',
					value: 'no',
					operator: 'equals'
				}
			},
			parent_city: {
				dependsOn: {
					field: '#living-with-parent',
					value: 'no',
					operator: 'equals'
				}
			},

			// Step 5: Sibling Information
			has_sibling_in_dorm: {
				required: true
			},
			sibling_gender: {
				dependsOn: {
					field: '#has-sibling-in-dorm',
					value: 'yes',
					operator: 'equals'
				}
			},
			sibling_name_ar: {
				dependsOn: {
					field: '#has-sibling-in-dorm',
					value: 'yes',
					operator: 'equals'
				},
				arabicName: true
			},
			sibling_name_en: {
				dependsOn: {
					field: '#has-sibling-in-dorm',
					value: 'yes',
					operator: 'equals'
				},
				englishName: true
			},
			sibling_national_id: {
				dependsOn: {
					field: '#has-sibling-in-dorm',
					value: 'yes',
					operator: 'equals'
				},
				egyptianNationalId: true,
				compareField: {
					field: '#national-id',
					operator: 'not_equals'
				}
			},
			sibling_faculty: {
				dependsOn: {
					field: '#has-sibling-in-dorm',
					value: 'yes',
					operator: 'equals'
				}
			},

			// Step 6: Emergency Contact (only when parents are abroad)
			emergency_contact_name_ar: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				},
				arabicName: true
			},
			emergency_contact_name_en: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				},
				englishName: true
			},
			emergency_contact_relationship: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				}
			},
			emergency_contact_phone: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				},
				egyptianPhone: true,
				compareField: {
					field: '#phone',
					operator: 'not_equals'
				}
			},
			emergency_contact_governorate: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				}
			},
			emergency_contact_city: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				}
			},
			emergency_contact_street: {
				dependsOn: {
					field: '#is-parent-abroad',
					value: 'yes',
					operator: 'equals'
				}
			},

			// Step 7: Terms validation
			terms_checkbox: {
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
			national_id: {
				required: 'National ID is required.',
				egyptianNationalId: 'Please enter a valid 14-digit Egyptian National ID.'
			},
			name_ar: {
				required: 'Arabic name is required.',
				arabicName: 'Please enter name in Arabic characters only.',
				minlength: 'Name must be at least 2 characters long.'
			},
			name_en: {
				required: 'English name is required.',
				englishName: 'Please enter name in English characters only.',
				minlength: 'Name must be at least 2 characters long.'
			},
			birth_date: {
				required: 'Birth date is required.',
				date: 'Please enter a valid date.',
				minimumAge: 'You must be at least 17 years old.'
			},
			gender: 'Please select your gender.',
			nationality: 'Please select your nationality.',

			// Step 2: Contact Information
			governorate: 'Please select your governorate.',
			city: 'Please select your city.',
			street: {
				required: 'Street address is required.',
				minlength: 'Street address must be at least 5 characters long.'
			},
			phone: {
				required: 'Phone number is required.',
				egyptianPhone: 'Please enter a valid Egyptian mobile number. (Starts with 01, 11 digits, e.g., 010xxxxxxxx)',
			},

			// Step 3: Academic Information
			faculty: 'Please select your faculty.',
			program: 'Please select your program.',
			academic_year: 'Please select your academic year.',
			gpa: {
				dependsOn: 'GPA is required when available.',
				gpaRange: 'GPA must be between 0.0 and 4.0.'
			},
			score: {
				dependsOn: 'Actual score is required when GPA is not available.',
				number: 'Please enter a valid score.'
			},
			academic_id: {
				required: 'Student ID is required.',
				academicId: 'Please enter a valid student ID (8-12 digits).'
			},
			academic_email: {
				required: 'University email is required.',
				email: 'Please enter a valid university email address.',
				emailDomain: 'Please use your university email address.'
			},

			// Step 4: Parent Information
			parent_relationship: 'Please select your relationship to the parent.',

			parent_name_ar: {
				required: 'Parent\'s name is required.',
				arabicName: 'Please enter parent\'s name in Arabic.'
			},
			parent_name_en: {
				required: 'Parent\'s name is required.',
				englishName: 'Please enter parent\'s name in English.'
			},
			parent_phone: {
				required: 'Parent\'s phone number is required.',
				egyptianPhone: 'Please enter a valid Egyptian mobile number (e.g., 01012345678).',
				internationalPhone: 'Please enter a valid international phone number (e.g., +966501234567 or +12025550123).'
			},
			parent_email: {
				email: 'Please enter a valid email address.'
			},
			parent_national_id: {
				required: 'Parent\'s national ID is required.',
				egyptianNationalId: 'Please enter a valid 14-digit Egyptian National ID.'
			},
			is_parent_abroad: 'Please specify if parent lives abroad.',
			parent_abroad_country: 'Please select the country where your parent lives.',
			living_with_parent: 'Please specify if you live with your parent.',
			parent_governorate: 'Please select parent\'s governorate.',
			parent_city: 'Please select parent\'s city.',
			parent_country: 'Please select parent\'s country.',

			// Step 5: Sibling Information
			has_sibling_in_dorm: 'Please specify if you have a sibling in the dorm.',
			sibling_gender: 'Please select sibling\'s gender.',
			sibling_name_ar: {
				dependsOn: 'Please enter sibling\'s name.',
				arabicName: 'Please enter sibling\'s name in Arabic.'
			},
			sibling_name_en: {
				dependsOn: 'Please enter sibling\'s name.',
				englishName: 'Please enter sibling\'s name in English.'
			},
			sibling_national_id: {
				dependsOn: 'Please enter sibling\'s national ID.',
				egyptianNationalId: 'Please enter a valid 14-digit national ID.',
				compareField: 'Sibling\'s national ID must be different from yours.'
			},
			sibling_faculty: 'Please select sibling\'s faculty.',

			// Step 6: Emergency Contact
			emergency_contact_name_ar: {
				required: 'Emergency contact name is required.',
				arabicName: 'Please enter name in Arabic.'
			},
			emergency_contact_name_en: {
				required: 'Emergency contact name is required.',
				englishName: 'Please enter name in English.'
			},
			emergency_contact_relationship: 'Please specify your relationship to emergency contact.',
			emergency_contact_phone: {
				required: 'Emergency contact phone is required.',
				egyptianPhone: 'Please enter a valid Egyptian mobile number. (Starts with 01, 11 digits, e.g., 010xxxxxxxx)',
				compareField: 'Emergency contact number must be different from your mobile.'
			},
			emergency_contact_governorate: 'Please select emergency contact\'s governorate.',
			emergency_contact_city: 'Please select emergency contact\'s city.',
			emergency_contact_street: 'Please enter emergency contact\'s street address.',

			// Step 7: Terms
			terms_checkbox: 'You must accept the terms and conditions to proceed.',

			// Fallback for missing messages
			default: 'This field is required.'
		};
	},

	/**
	 * Validate a specific step
	 * @param {string} tabSelector
	 * @returns {boolean}
	 */
	validateStep: function(tabSelector) {
		if (!this.validator) {
			console.log('Validator not initialized, skipping validation for', tabSelector);
			return true;
		}

		var $step = $(tabSelector);
		var stepId = $step.attr('id');
		var $stepBtn = $('[data-bs-target="#' + stepId + '"]');
		var isValid = true;

		$stepBtn.removeClass('is-valid is-invalid');

		// Validate all enabled and visible (or .validate-hidden) fields in the step
		$step.find('input, select, textarea').each(function() {
			var $field = $(this);
			var fieldValid = ValidationService.validator.element(this);
			console.log('Validating field:', this.name || this.id || this, 'Result:', fieldValid);
			if (!fieldValid) {
				isValid = false;
			}
		});

		// Update step button state
		$stepBtn
			.toggleClass('is-valid', isValid)
			.toggleClass('is-invalid', !isValid);

		console.log('Step', stepId, 'validation result:', isValid);

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

		var $steps = $('.tab-pane[id^="step"]');
		for (var i = 0; i < $steps.length; i++) {
			var $step = $steps.eq(i);
			var stepId = $step.attr('id');

			var match = stepId.match(/^step(\d+)$/);
			var stepNumber = match ? parseInt(match[1], 10) : null;
			if (stepNumber && !this.validateStep('#' + stepId)) {
				return stepNumber;
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
			'#gpa',
			'#score',
			'#parent-abroad-country',
			'#parent-governorate',
			'#parent-city',
			'#sibling-gender',
			'#sibling-name-ar',
			'#sibling-name-en',
			'#sibling-national-id',
			'#sibling-faculty',
			'#emergency-contact-name_ar',
			'#emergency-contact-name_en',
			'#emergency-contact-relationship',
			'#emergency-contact-phone',
			'#emergency-contact-governorate',
			'#emergency-contact-city',
			'#emergency-contact-street'
		];

		conditionalFields.forEach(function(field) {
			var $field = $(field);
			if ($field.length > 0) {
				try {
					ValidationService.validator.element(field);
				} catch (error) {
					console.warn('Could not validate field:', field, error);
				}
			}
		});
	}
};

// ===========================
// NAVIGATION MANAGER
// ===========================
var NavigationManager = {
	// Configuration for steps to skip
	SkippedSteps: [{
		step: 6,
		selector: "is-parent-abroad",
		condition: '=',
		value: 'no'
	}, ],
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
	 * Find first invalid tab that needs completion
	 * @param {jQuery} allTabs - All visible tabs
	 * @param {number} targetIndex - Index of target tab
	 * @returns {number} Index of first invalid tab, or -1 if none
	 */
	findFirstInvalidTabIndex: function(allTabs, targetIndex) {
		console.log('findFirstInvalidTabIndex called with:', {
			allTabs: allTabs,
			targetIndex: targetIndex
		});
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
		$('html, body').animate({
			scrollTop: 0
		}, 300);

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

		return availableSteps.sort(function(a, b) {
			return a - b;
		});
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
		this.initializeFieldStates();
		this.bindEvents();
		this.triggerInitialChanges();
	},

	/**
	 * Initialize field states (disable dependent fields)
	 */
	initializeFieldStates: function() {
		// Disable dependent fields initially
		this.disableField('#city', 'Please select governorate first');
		this.disableField('#parent-city', 'Please select parent governorate first');
		this.disableField('#emergency-contact-city', 'Please select emergency contact governorate first');
		this.disableField('#program', 'Please select faculty first');
	},

	/**
	 * Disable a field with placeholder text
	 * @param {string} selector - Field selector
	 * @param {string} placeholder - Placeholder text
	 */
	disableField: function(selector, placeholder) {
		var $field = $(selector);
		if ($field.length) {
			$field.prop('disabled', true);
			if ($field.is('select')) {
				$field.html('<option value="">' + placeholder + '</option>');
			} else {
				$field.attr('placeholder', placeholder);
			}
			$field.addClass('field-disabled');
		}
	},

	/**
	 * Enable a field
	 * @param {string} selector - Field selector
	 */
	enableField: function(selector) {
		var $field = $(selector);
		if ($field.length) {
			$field.prop('disabled', false);
			$field.removeClass('field-disabled');
		}
	},

	/**
	 * Bind conditional field events
	 */
	bindEvents: function() {
		this.handleGovernorateChange();
		this.handleParentGovernorateChange();
		this.handleEmergencyContactGovernorateChange();
		this.handleFacultyChange();
		this.handleGpaAndScoreChange();
		this.handleParentAbroadChange();
		this.handleLivingWithParentChange();
		this.handleSiblingInDormChange();
	},
	/**
	 * Handle governorate change
	 */
	handleGovernorateChange: function() {
		$('#governorate').change(function() {
			var $governorate = $(this);
			var governorateId = $governorate.val();

			if (governorateId) {

				ConditionalFieldsManager.enableField('#city');

				ApiService.fetchCity(governorateId)
					.done(function(response) {
						if (response.success && response.data) {
							Utils.populateSelect($('#city'), response.data, {
								placeholder: 'Select City',
								valueField: 'id',
								textField: 'name'
							});

							var pendingCityId = $governorate.data('pending-city-id');
							if (pendingCityId) {
								$('#city').val(pendingCityId);
								$governorate.removeData('pending-city-id');
							}
						} else {
							$('#city').html('<option value="">No cities available</option>');
						}
					})
					.fail(function(xhr) {
						console.error('Failed to load cities:', xhr);
						$('#city').html('<option value="">Error loading cities</option>');
					});
			} else {
				ConditionalFieldsManager.disableField('#city', 'Please select governorate first');
			}
		});
	},
	/**
	 * Handle parent governorate change (parent address)
	 */
	handleParentGovernorateChange: function() {
		$('#parent-governorate').change(function() {
			var $governorate = $(this);
			var governorateId = $governorate.val();

			if (governorateId) {

				ConditionalFieldsManager.enableField('#parent-city');

				ApiService.fetchCity(governorateId)
					.done(function(response) {
						if (response.success && response.data) {
							Utils.populateSelect($('#parent-city'), response.data, {
								placeholder: 'Select City',
								valueField: 'id',
								textField: 'name'
							});

							var pendingCityId = $governorate.data('pending-city-id');
							if (pendingCityId) {
								$('#parent-city').val(pendingCityId);
								$governorate.removeData('pending-city-id');
							}
						} else {
							$('#parent-city').html('<option value="">No cities available</option>');
						}
					})
					.fail(function(xhr) {
						console.error('Failed to load parent cities:', xhr);
						$('#parent-city').html('<option value="">Error loading cities</option>');
					});
			} else {

				ConditionalFieldsManager.disableField('#parent-city', 'Please select parent governorate first');
			}
		});
	},
	/**
	 * Handle emergency contact governorate change
	 */
	handleEmergencyContactGovernorateChange: function() {
		$('#emergency-contact-governorate').change(function() {
			var $governorate = $(this);
			var governorateId = $governorate.val();
			if (governorateId) {

				ConditionalFieldsManager.enableField('#emergency-contact-city');

				ApiService.fetchCity(governorateId)
					.done(function(response) {
						if (response.success && response.data) {
							Utils.populateSelect($('#emergency-contact-city'), response.data, {
								placeholder: 'Select City',
								valueField: 'id',
								textField: 'name'
							});

							var pendingCityId = $governorate.data('pending-city-id');
							if (pendingCityId) {
								$('#emergency-contact-city').val(pendingCityId);
								$governorate.removeData('pending-city-id');
							}
						} else {
							$('#emergency-contact-city').html('<option value="">No cities available</option>');
						}
					})
					.fail(function(xhr) {
						console.error('Failed to load emergency contact cities:', xhr);
						$('#emergency-contact-city').html('<option value="">Error loading cities</option>');
					});
			} else {

				ConditionalFieldsManager.disableField('#emergency-contact-city', 'Please select emergency contact governorate first');
			}
		});
	},
	/**
	 * Handle faculty change
	 */
	handleFacultyChange: function() {
		$('#faculty').change(function() {
			var $faculty = $(this);
			var facultyId = $faculty.val();

			if (facultyId) {

				ConditionalFieldsManager.enableField('#program');

				ApiService.fetchProgram(facultyId)
					.done(function(response) {
						if (response.success && response.data) {
							Utils.populateSelect($('#program'), response.data, {
								placeholder: 'Select Program',
								valueField: 'id',
								textField: 'name'
							});
							var pendingProgramId = $faculty.data('pending-program-id');
							if (pendingProgramId) {
								$('#program').val(pendingProgramId);
								$faculty.removeData('pending-program-id');
							}
						} else {
							$('#program').html('<option value="">No programs available</option>');
						}
					})
					.fail(function(xhr) {
						console.error('Failed to load programs:', xhr);
						$('#program').html('<option value="">Error loading programs</option>');
					});
			} else {

				ConditionalFieldsManager.disableField('#program', 'Please select faculty first');
			}
		});
	},
	/**
	 * Handle GPA and score change
	 */
	handleGpaAndScoreChange: function() {
		$('#is-new-comer').change(function() {
			var value = $(this).val();
			console.log('New comer status changed:', value);

			var isNewComer = (value === 'true' || value === true);

			if (!isNewComer) {
				console.log('Showing GPA field, hiding score field');
				$('#gpa-div').removeClass('d-none');
				$('#score-div').addClass('d-none');
			} else {
				console.log('Showing score field, hiding GPA field');
				$('#gpa-div').addClass('d-none');
				$('#score-div').removeClass('d-none');
			}
		});
	},
	/**
	 * Handle parent abroad change
	 */
	handleParentAbroadChange: function() {
		$('#is-parent-abroad').change(function() {
			var value = $(this).val();

			if (value === 'yes') {
				ConditionalFieldsManager.showAbroadFields();
			} else if (value === 'no') {
				ConditionalFieldsManager.showDomesticFields();
			} else {
				ConditionalFieldsManager.hideAllParentFields();
			}
		});
	},
	/**
	 * Handle living with parent change
	 */
	handleLivingWithParentChange: function() {
		$('#living-with-parent').change(function() {
			var value = $(this).val();

			if (value === 'no') {
				ConditionalFieldsManager.showParentAddressFields();
			} else {
				ConditionalFieldsManager.hideParentAddressFields();
			}
		});
	},
	/**
	 * Handle sibling in dorm change
	 */
	handleSiblingInDormChange: function() {
		$('#has-sibling-in-dorm').change(function() {
			var value = $(this).val();

			if (value === 'yes') {
				ConditionalFieldsManager.showSiblingDetails();
			} else {
				ConditionalFieldsManager.hideSiblingDetails();
			}
		});
	},
	/**
	 * Show abroad country fields
	 */
	showAbroadFields: function() {
		$('#parent-abroad-country-div').removeClass('d-none');
		$('#living-with-parent-div, #parent-address-div').addClass('d-none');
	},
	/**
	 * Show domestic parent fields
	 */
	showDomesticFields: function() {
		$('#parent-abroad-country-div').addClass('d-none');
		$('#living-with-parent-div').removeClass('d-none');

		if ($('#living-with-parent').val() === 'no') {
			this.showParentAddressFields();
		} else {
			this.hideParentAddressFields();
		}
	},
	/**
	 * Hide all parent fields
	 */
	hideAllParentFields: function() {
		$('#parent-abroad-country-div, #living-with-parent-div, #parent-address-div').addClass('d-none');
	},
	/**
	 * Show parent address fields
	 */
	showParentAddressFields: function() {
		$('#parent-address-div').removeClass('d-none');
	},
	/**
	 * Hide parent address fields
	 */
	hideParentAddressFields: function() {
		$('#parent-address-div').addClass('d-none');
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
	 * Check and enable fields based on their dependencies when data is loaded
	 */
	checkAndEnableFields: function() {

		if ($('#governorate').val()) {
			this.enableField('#city');
		}
		if ($('#parent-governorate').val()) {
			this.enableField('#parent-city');
		}
		if ($('#emergency-contact-governorate').val()) {
			this.enableField('#emergency-contact-city');
		}

		if ($('#faculty').val()) {
			this.enableField('#program');
		}
	},

	/**
	 * Trigger initial changes for all conditional fields
	 */
	triggerInitialChanges: function() {
		$('#is-parent-abroad').trigger('change');
		$('#living-with-parent').trigger('change');
		$('#has-sibling-in-dorm').trigger('change');
		$('#parent-governorate').trigger('change');
		$('#is-new-comer').trigger('change');

		this.checkAndEnableFields();
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
		this.handleEnterKeyPress();
	},

	/**
	 * Handle Enter key press to act as Next button instead of form submission
	 */
	handleEnterKeyPress: function() {
		$(document).on('keypress', '#profileForm input, #profileForm select', function(e) {

			if (e.which === 13) {
				e.preventDefault();

				var $activeTabPane = $('.tab-pane.active');
				if ($activeTabPane.length) {

					$activeTabPane.find('.next-Btn').click();
				}

				return false;
			}
		});
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
			e.preventDefault();

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
	CompleteProfileApp.init();
});