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
     * Generic AJAX request wrapper.
     * @param {object} options - jQuery AJAX options.
     * @returns {jqXHR}
     */
    request: function(options) {
        return $.ajax(options);
    },

    /**
     * Fetch the current user's profile data.
     * @returns {jqXHR}
     */
    fetchProfile: function() {
        return this.request({
            url: ROUTES.profile.fetch,
            method: 'GET'
        });
    },

    /**
     * Submit the completed profile.
     * @param {object} data - The form data to submit.
     * @returns {jqXHR}
     */
    submitProfile: function(data) {
        return this.request({
            url: ROUTES.profile.submit,
            method: 'POST',
            data: data,
            processData: false, // Required for FormData
            contentType: false // Required for FormData
        });
    },

    /**
     * Fetch faculty data.
     * @returns {jqXHR}
     */
    fetchFaculty: function() {
        return this.request({
            url: ROUTES.faculty.fetch,
            method: 'GET'
        });
    },

    /**
     * Fetch program data for a specific faculty.
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
     * Fetch governorate data.
     * @returns {jqXHR}
     */
    fetchGovernorate: function() {
        return this.request({
            url: ROUTES.governorate.fetch,
            method: 'GET'
        });
    },

    /**
     * Fetch city data for a specific governorate.
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
     * Fetch countries data.
     * @returns {jqXHR}
     */
    fetchCountries: function() {
        return this.request({
            url: ROUTES.countries.fetch,
            method: 'GET'
        });
    },

    /**
     * Fetch nationalities data.
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
     * Initialize the profile manager by populating dropdowns and then fetching user data.
     */
    init: function() {
        var self = this;

        // Fetch data for all dropdowns simultaneously.
        Promise.all([
                this.populateGovernorates(),
                this.populateFaculties(),
                this.populateCountries(),
                this.populateNationalities()
            ])
            .then(function() {
                // Once dropdowns are ready, fetch and populate the user's profile.
                return self.fetchAndPopulateProfile();
            })
            .then(function() {
                // Hide the main loader after all data is loaded and populated.
                FormManager.hideLoader();
            })
            .catch(function(error) {
                console.error('Error initializing profile:', error);
                FormManager.hideLoader();
            });
    },

    /**
     * Fetch profile data from the API and populate the form fields.
     * @returns {Promise} A promise that resolves when the profile is populated.
     */
    fetchAndPopulateProfile: function() {
        var self = this;
        return new Promise(function(resolve, reject) {
            ApiService.fetchProfile()
                .done(function(response) {
                    if (response.success && response.data) {
                        self.populateProfileData(response.data);
                        Utils.showSuccess(response.message, true);
                    } else {
                        Utils.showSuccess(response.message);
                    }
                    resolve(response);
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
                    reject(xhr);
                });
        });
    },

    /**
     * Populate the entire form with data from the API.
     * @param {object} data - The complete profile data object.
     */
    populateProfileData: function(data) {
        this.populatePersonalInfo(data.personal_info || {});
        this.populateContactInfo(data.contact_info || {});
        this.populateAcademicInfo(data.academic_info || {});
        this.populateGuardianInfo(data.guardian_info || {});
        this.populateSiblingInfo(data.sibling_info || {});
        this.populateEmergencyContact(data.emergency_contact || {});
        this.populateTerms(data.terms || {});

        // Trigger change events to ensure conditional logic is applied correctly.
        this.triggerFieldChanges();
    },

    /**
     * Populate personal information fields.
     * @param {object} data - personal_info object.
     */
    populatePersonalInfo: function(data) {
        if (data.national_id) $('#national-id').val(data.national_id);
        if (data.name_ar) $('#name-ar').val(data.name_ar);
        if (data.name_en) $('#name-en').val(data.name_en);
        if (data.birthdate) $('#birth-date').val(data.birthdate);
        if (data.gender) $('#gender').val(data.gender).trigger('change');
        if (data.nationality_id) $('#nationality').val(data.nationality_id).trigger('change');
    },

    /**
     * Populate contact information fields.
     * @param {object} data - contact_info object.
     */
    populateContactInfo: function(data) {
        if (data.phone) $('#phone').val(data.phone);
        if (data.governorate_id) {
            $('#governorate').val(data.governorate_id).trigger('change');
            if (data.city_id) {
                $('#governorate').data('pending-city-id', data.city_id);
            }
        }
        if (data.street) $('#street').val(data.street);
    },

    /**
     * Populate academic information fields.
     * @param {object} data - academic_info object.
     */
    populateAcademicInfo: function(data) {
        if (data.academic_id) $('#academic-id').val(data.academic_id);
        if (data.academic_email) $('#academic-email').val(data.academic_email);
        if (data.academic_year) $('#academic-year').val(data.academic_year).trigger('change');
        if (data.gpa) $('#gpa').val(data.gpa);
        if (data.faculty_id) {
            $('#faculty').val(data.faculty_id).trigger('change');
            if (data.program_id) {
                $('#faculty').data('pending-program-id', data.program_id);
            }
        }
        if (data.score) $('#score').val(data.score);
        if (data.is_new_comer !== undefined && data.is_new_comer !== null) {
            $('#is-new-comer').val(data.is_new_comer.toString()).trigger('change');
        }
    },

    /**
     * Populate guardian information fields.
     * @param {object} data - guardian_info object.
     */
    populateGuardianInfo: function(data) {
        if (data.name_ar) $('#guardian-name-ar').val(data.name_ar);
        if (data.name_en) $('#guardian-name-en').val(data.name_en);
        if (data.phone) $('#guardian-phone').val(data.phone);
        if (data.email) $('#guardian-email').val(data.email);
        if (data.national_id) $('#guardian-national-id').val(data.national_id);
        if (data.relationship) $('#guardian-relationship').val(data.relationship).trigger('change');
        if (data.living_with_guardian) $('#living-with-guardian').val(data.living_with_guardian ? 'yes' : 'no').trigger('change');

        var isAbroad = !!data.is_abroad;
        if (isAbroad) {
            if (data.country_id) {
                $('#guardian-abroad-country').val(data.country_id).trigger('change');
            }
        } else {
            if (data.governorate_id) {
                $('#guardian-governorate').val(data.governorate_id).trigger('change');
                if (data.city_id) {
                    $('#guardian-governorate').data('pending-city-id', data.city_id);
                }
            }
        }
        $('#is-guardian-abroad').val(isAbroad ? 'yes' : 'no').trigger('change');
    },

    /**
     * Populate sibling information fields.
     * @param {object} data - sibling_info object.
     */
    populateSiblingInfo: function(data) {
      if (data.has_sibling_in_dorm) {
         $('#has-sibling-in-dorm').val(data.has_sibling_in_dorm).trigger('change');

         var $container = $('#siblings-container');
         $container.find('.sibling-group').remove();

         var siblings = Array.isArray(data.siblings) ? data.siblings : (data.sibling ? [data.sibling] : []);
         if (siblings.length === 0 && data.relationship) {
            siblings = [{
               relationship: data.relationship,
               name_ar: data.name_ar,
               name_en: data.name_en,
               national_id: data.national_id,
               academic_level: data.academic_level,
               faculty_id: data.faculty_id
            }];
         }

         // Add sibling groups as needed
         for (var i = 0; i < siblings.length; i++) {
            SiblingsManager.addSibling();
         }

         // Populate each sibling group
       $container.find('.sibling-group').each(function(idx) {
         var sibling = siblings[idx];
         if (!sibling) return;
         var $group = $(this);
         if (sibling.relationship) $group.find('.sibling-relationship').val(sibling.relationship).trigger('change');
         if (sibling.name_ar) $group.find('.sibling-name-ar').val(sibling.name_ar);
         if (sibling.name_en) $group.find('.sibling-name-en').val(sibling.name_en);
         if (sibling.national_id) $group.find('.sibling-national-id').val(sibling.national_id);
         if (sibling.academic_level) $group.find('.sibling-academic-level').val(sibling.academic_level).trigger('change');
         if (sibling.faculty_id) {
            $group.find('.sibling-faculty').val(sibling.faculty_id).trigger('change');
         }
       });
      }
    },

    /**
     * Populate emergency contact information fields.
     * @param {object} data - emergency_contact object.
     */
    populateEmergencyContact: function(data) {
        if (data.name_en) $('#emergency-contact-name-en').val(data.name_en);
        if (data.name_ar) $('#emergency-contact-name-ar').val(data.name_ar);
        if (data.phone) $('#emergency-contact-phone').val(data.phone);
        if (data.relationship) $('#emergency-contact-relationship').val(data.relationship).trigger('change');
        if (data.governorate_id) {
            $('#emergency-contact-governorate').val(data.governorate_id).trigger('change');
            if (data.city_id) {
                $('#emergency-contact-governorate').data('pending-city-id', data.city_id);
            }
        }
        if (data.street) $('#emergency-contact-street').val(data.street);
        if (data.notes) $('#emergency-contact-notes').val(data.notes);
    },

    /**
     * Populate terms and conditions checkbox.
     * @param {object} data - terms object.
     */
    populateTerms: function(data) {
            $('#terms-Checkbox').prop('checked', data.terms_accepted === true || data.terms_accepted === '1');
    },

    /**
     * Trigger change events for fields that control conditional visibility after a short delay,
     * ensuring all data is populated first.
     */
    triggerFieldChanges: function() {
        // A minimal delay can help ensure the DOM is fully ready.
        setTimeout(function() {
            $('#is-guardian-abroad, #living-with-guardian, #has-sibling-in-dorm, #is-new-comer').trigger('change');
        }, 200);
    },

    /**
     * Gathers all form data into a FormData object for submission.
     * @returns {FormData}
     */
    getFormData: function() {
        return new FormData($('#profileForm')[0]);
    },

    /**
     * Handles the final profile submission.
     * @returns {Promise} A promise that resolves on success or rejects on failure.
     */
    submitProfile: function() {
        var self = this;
        var formData = this.getFormData();
        var $submitButton = $('#profileForm button[type="submit"]');

        return new Promise(function(resolve, reject) {
            Utils.setLoadingState($submitButton, true, {
                loadingText: TRANSLATIONS.actions.submitting,
                loadingIcon: 'bx bx-loader-alt bx-spin me-1'
            });
            FormManager.showLoader();

            ApiService.submitProfile(formData)
                .done(function(response) {
                    if (response.success) {
                        Utils.showSuccess(response.message);
                        resolve(response); // Resolve the promise on success
                    } else {
                        FormManager.hideLoader();
                        Utils.showError(response.message);
                        reject(response); // Reject on API-level failure
                    }
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, xhr.responseJSON?.message);
                    FormManager.hideLoader();
                    reject(xhr); // Reject on AJAX failure
                })
                .always(function() {
                    Utils.setLoadingState($submitButton, false, {
                        normalText: TRANSLATIONS.actions.submit,
                        normalIcon: 'bx bx-check me-1'
                    });
                });
        });
    },

    /**
     * Populate all governorate-related dropdowns from a single API call.
     * @returns {Promise}
     */
    populateGovernorates: function() {
        return new Promise(function(resolve, reject) {
            ApiService.fetchGovernorate()
                .done(function(response) {
                    if (response.success && response.data) {
                        var governorateSelectors = [{
                            id: '#governorate',
                            placeholder: TRANSLATIONS.placeholders.select_governorate
                        }, {
                            id: '#guardian-governorate',
                            placeholder: TRANSLATIONS.placeholders.select_guardian_governorate
                        }, {
                            id: '#emergency-contact-governorate',
                            placeholder: TRANSLATIONS.placeholders.select_emergency_contact_governorate
                        }];

                        governorateSelectors.forEach(function(selectorInfo) {
                            var $select = $(selectorInfo.id);
                            if ($select.length && $select.children('option').length <= 1) {
                                Utils.populateSelect($select, response.data, {
                                    placeholder: selectorInfo.placeholder,
                                    valueField: 'id',
                                    textField: 'name'
                                });
                            }
                        });
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Failed to load governorates.'));
                    }
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to fetch governorates.');
                    reject(xhr);
                });
        });
    },

    /**
     * Populate all faculty-related dropdowns from a single API call.
     * @returns {Promise}
     */
    populateFaculties: function() {
      return new Promise(function(resolve, reject) {
         ApiService.fetchFaculty()
            .done(function(response) {
               if (response.success && response.data) {
                  // Store in SiblingsManager for all sibling dropdowns
                  SiblingsManager._facultyData = response.data;
                  var facultySelectors = [{
                     id: '#faculty',
                     placeholder: TRANSLATIONS.placeholders.select_faculty
                  }, {
                     id: '#sibling-faculty',
                     placeholder: TRANSLATIONS.placeholders.select_sibling_faculty
                  }];

                  facultySelectors.forEach(function(selectorInfo) {
                     var $select = $(selectorInfo.id);
                     if ($select.length && $select.children('option').length <= 1) {
                        Utils.populateSelect($select, response.data, {
                           placeholder: selectorInfo.placeholder,
                           valueField: 'id',
                           textField: 'name'
                        });
                     }
                  });
                  resolve(response);
               } else {
                  reject(new Error(response.message || 'Failed to load faculties.'));
               }
            })
            .fail(function(xhr) {
               Utils.handleAjaxError(xhr, 'Failed to fetch faculties.');
               reject(xhr);
            });
      });
    },

    /**
     * Populate the countries dropdown.
     * @returns {Promise}
     */
    populateCountries: function() {
        return new Promise(function(resolve, reject) {
            var $countrySelect = $('#guardian-abroad-country');
            if ($countrySelect.children('option').length > 1) {
                return resolve(); // Already populated
            }

            ApiService.fetchCountries()
                .done(function(response) {
                    if (response.success && response.data) {
                        Utils.populateSelect($countrySelect, response.data, {
                            placeholder: TRANSLATIONS.placeholders.select_country,
                            valueField: 'id',
                            textField: 'name'
                        });
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Failed to load countries.'));
                    }
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to fetch countries.');
                    reject(xhr);
                });
        });
    },

    /**
     * Populate the nationalities dropdown.
     * @returns {Promise}
     */
    populateNationalities: function() {
        return new Promise(function(resolve, reject) {
            var $nationalitySelect = $('#nationality');
            if ($nationalitySelect.children('option').length > 1) {
                return resolve(); // Already populated
            }

            ApiService.fetchNationalities()
                .done(function(response) {
                    if (response.success && response.data) {
                        Utils.populateSelect($nationalitySelect, response.data, {
                            placeholder: TRANSLATIONS.placeholders.select_nationality,
                            valueField: 'id',
                            textField: 'name'
                        });
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Failed to load nationalities.'));
                    }
                })
                .fail(function(xhr) {
                    Utils.handleAjaxError(xhr, 'Failed to fetch nationalities.');
                    reject(xhr);
                });
        });
    }
};

// ===========================
// SIBLINGS MANAGER
// ===========================
var SiblingsManager = {
    siblingCounter: 1,
    maxSiblings: 5,
    _facultyData: [], // Cache faculty data to prevent multiple API calls.

    /**
     * Initialize the siblings manager.
     */
    init: function() {
        this.bindEvents();
        this.fetchAndStoreFacultyOptions();

        // Register validation rules for the default sibling group if present
        if (ValidationService.validator) {
            $('#siblings-container .sibling-group').first().find('input, select').each(function() {
                var $field = $(this);
                var nameAttr = $field.attr('name');
                if (nameAttr && !ValidationService.validator.settings.rules[nameAttr]) {
                    var ruleObj = { required: $field.is('[required]') };
                    ValidationService.validator.settings.rules[nameAttr] = ruleObj;
                    $field.rules && $field.rules('add', ruleObj);
                }
            });
        }
    },

    /**
     * Bind all necessary event handlers for sibling management.
     */
    bindEvents: function() {
        var self = this;
        // Use event delegation for dynamically added elements.
        var $document = $(document);

        $document.on('click', '#add-sibling-btn', function() {
            self.addSibling();
        });

        $document.on('click', '.remove-sibling-btn', function() {
            self.removeSibling($(this).closest('.sibling-group'));
        });

        $document.on('input', '.sibling-national-id', function() {
            self.validateNationalId($(this));
        });

        $document.on('blur', '.sibling-national-id', function() {
            self.checkDuplicateNationalId($(this));
        });

        $document.on('change', '.sibling-relationship', function() {
            self.autoSetGender($(this));
            ConditionalFieldsManager.updateReservationOptions();
        });

        $document.on('input', '.sibling-name-en', function() {
            self.validateEnglishName($(this));
        });

        $document.on('input', '.sibling-name-ar', function() {
            self.validateArabicName($(this));
        });

        // Listen for direct gender changes (if selectable)
        $document.on('change', '.sibling-gender', function() {
            if (typeof ConditionalFieldsManager !== 'undefined' && typeof ConditionalFieldsManager.updateReservationOptions === 'function') {
                ConditionalFieldsManager.updateReservationOptions();
            }
        });
    },

    /**
     * Fetches faculty data from the API and caches it.
     */
    fetchAndStoreFacultyOptions: function() {
        var self = this;
      if (self._facultyData && self._facultyData.length) {
         $('.sibling-faculty').each(function() {
            self.populateFacultySelect($(this), self._facultyData);
            if (typeof Select2Manager !== 'undefined' && Select2Manager.config && Select2Manager.config.form && Select2Manager.config.form['.sibling-faculty']) {
               Utils.initSelect2($(this), Select2Manager.config.form['.sibling-faculty']);
            }
         });
         return;
      }
      ApiService.fetchFaculty()
         .done(function(response) {
            if (response.success && response.data) {
               self._facultyData = response.data;
               $('.sibling-faculty').each(function() {
                  self.populateFacultySelect($(this), self._facultyData);
                  if (typeof Select2Manager !== 'undefined' && Select2Manager.config && Select2Manager.config.form && Select2Manager.config.form['.sibling-faculty']) {
                     Utils.initSelect2($(this), Select2Manager.config.form['.sibling-faculty']);
                  }
               });
            }
         })
         .fail(function(xhr) {
            console.error('Failed to load faculties for siblings:', xhr);
         });
    },

    /**
     * Add a new sibling form group to the container.
     */
    addSibling: function() {
        if (this.siblingCounter >= this.maxSiblings) {
            Utils.showAlert('warning', TRANSLATIONS.messages.maxSiblingsReached || 'Maximum number of siblings reached.');
            return;
        }

        var template = $('#sibling-template').html();
        var $newSibling = $(template);

        $('#siblings-container').append($newSibling);
        this.reorderSiblings();

      // Populate the new faculty dropdown from cached data and initialize Select2
      this.populateFacultySelect($newSibling.find('.sibling-faculty'), this._facultyData);
      if (typeof Select2Manager !== 'undefined' && Select2Manager.config && Select2Manager.config.form && Select2Manager.config.form['.sibling-faculty']) {
         Utils.initSelect2($newSibling.find('.sibling-faculty'), Select2Manager.config.form['.sibling-faculty']);
      }

        // Animate the new sibling form into view.
        $newSibling.hide().slideDown(300);

        // Update validation rules for new fields
        if (ValidationService.validator) {
            $newSibling.find('input, select').each(function() {
                var $field = $(this);
                var nameAttr = $field.attr('name');
                if (nameAttr) {
                    var ruleObj = { required: $field.is('[required]') };
                    ValidationService.validator.settings.rules[nameAttr] = ruleObj;
                    $field.rules && $field.rules('add', ruleObj);
                }
            });
        }

        // Re-evaluate reservation options after adding a sibling
         ConditionalFieldsManager.updateReservationOptions();
    },

    /**
     * Remove a sibling form group.
     * @param {jQuery} $siblingGroup - The jQuery object for the sibling group to remove.
     */
    removeSibling: function($siblingGroup) {
        var self = this;
        $siblingGroup.slideUp(300, function() {
            $(this).remove();
            self.reorderSiblings();

            // Remove associated validation rules
            if (ValidationService.validator) {
                $(this).find('input, select').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr && ValidationService.validator.settings.rules[nameAttr]) {
                        delete ValidationService.validator.settings.rules[nameAttr];
                    }
                });
            }

            if (ConditionalFieldsManager.updateReservationOptions) {
                ConditionalFieldsManager.updateReservationOptions();
            }
        });
    },

    /**
     * Re-index all sibling forms after an add or remove operation.
     * This updates labels, IDs, and names to maintain correct form submission.
     */
    reorderSiblings: function() {
        var self = this;
        var $siblingGroups = $('#siblings-container .sibling-group');
        this.siblingCounter = $siblingGroups.length;

        $siblingGroups.each(function(index) {
            var $sibling = $(this);
            $sibling.attr('data-sibling-index', index);
            $sibling.find('.sibling-number').text(index + 1);
            self.updateFieldAttributes($sibling, index);
        });

        // Toggle add/remove buttons based on the current count.
        $('#add-sibling-btn').toggle(this.siblingCounter < this.maxSiblings);
        $('.remove-sibling-btn').toggleClass('d-none', this.siblingCounter <= 1);
    },

    /**
     * Update the `id` and `name` attributes for fields within a sibling group.
     * @param {jQuery} $siblingForm - The sibling form jQuery object.
     * @param {number} index - The new index for the sibling.
     */
    updateFieldAttributes: function($siblingForm, index) {
        $siblingForm.find('input, select').each(function() {
            var $field = $(this);
            var baseName = $field.data('name');
            var oldNameAttr = $field.attr('name');
            if (baseName) {
                var newId = 'sibling-' + baseName.replace('_', '-') + '-' + index;
                var newName = 'siblings[' + index + '][' + baseName + ']';
                $field.attr({
                    id: newId,
                    name: newName
                });
                $field.closest('.form-group').find('label').attr('for', newId);

                // Update validation rules to use new names
                if (ValidationService.validator) {
                    var rules = ValidationService.validator.settings.rules;
                    if (oldNameAttr && rules[oldNameAttr]) {
                        rules[newName] = rules[oldNameAttr];
                        delete rules[oldNameAttr];
                    }
                }
            }
        });
    },

    /**
     * Populate a select dropdown with faculty options.
     * @param {jQuery} $select - The select element to populate.
     * @param {Array} faculties - The array of faculty data.
     */
    populateFacultySelect: function($select, faculties) {
        var currentValue = $select.val();
        $select.empty().append($('<option>', {
            value: '',
            text: TRANSLATIONS.placeholders.select_sibling_faculty
        }));

        $.each(faculties, function(index, faculty) {
            $select.append($('<option>', {
                value: faculty.id,
                text: faculty.name
            }));
        });

        if (currentValue) {
            $select.val(currentValue);
        }
    },

    /**
     * Validate national ID format (14 digits).
     * @param {jQuery} $input - The national ID input field.
     */
    validateNationalId: function($input) {
        var value = $input.val().replace(/\D/g, ''); // Remove non-digits
        $input.val(value);
        var isValid = value.length === 14;
        this.setFieldValidation($input, isValid, TRANSLATIONS.validation.nationalIdInvalid || 'National ID must be 14 digits.');
    },

    /**
     * Check for duplicate national IDs among siblings and the user.
     * @param {jQuery} $input - The national ID input field to check.
     */
    checkDuplicateNationalId: function($input) {
        var currentValue = $input.val();
        if (!currentValue || currentValue.length !== 14) return;

        var isDuplicate = false;

        $('.sibling-national-id').not($input).each(function() {
            if ($(this).val() === currentValue) {
                isDuplicate = true;
                return false; 
            }
        });

        var userNationalId = $('#national-id').val();
        if (userNationalId && currentValue === userNationalId) {
            isDuplicate = true;
        }

        this.setFieldValidation($input, !isDuplicate, TRANSLATIONS.validation.duplicateNationalId || 'This National ID is already used.');
    },

    /**
     * Automatically set the hidden gender field based on the selected relationship (brother/sister).
     * @param {jQuery} $relationshipSelect - The relationship select field.
     */
    autoSetGender: function($relationshipSelect) {
        var relationship = $relationshipSelect.val();
        var $genderSelect = $relationshipSelect.closest('.sibling-group').find('.sibling-gender');
        if (relationship === 'brother') {
            $genderSelect.val('male');
        } else if (relationship === 'sister') {
            $genderSelect.val('female');
        } else {
            $genderSelect.val('');
        }
    },

    /**
     * Validate that the input contains only English characters and spaces.
     * @param {jQuery} $input - The English name input field.
     */
    validateEnglishName: function($input) {
        var value = $input.val();
        var englishOnly = /^[a-zA-Z\s]*$/.test(value);
        this.setFieldValidation($input, englishOnly, TRANSLATIONS.validation.englishOnly || 'Please use English characters only.');
    },

    /**
     * Validate that the input contains only Arabic characters and spaces.
     * @param {jQuery} $input - The Arabic name input field.
     */
    validateArabicName: function($input) {
        var value = $input.val();
        var arabicOnly = /^[\u0600-\u06FF\s]*$/.test(value);
        this.setFieldValidation($input, arabicOnly, TRANSLATIONS.validation.arabicOnly || 'Please use Arabic characters only.');
    },

    /**
     * Set the validation state (valid/invalid) for a form field and display a message.
     * @param {jQuery} $field - The form field jQuery object.
     * @param {boolean} isValid - Whether the field is valid.
     * @param {string} message - The error message to display if invalid.
     */
    setFieldValidation: function($field, isValid, message) {
        var $feedback = $field.closest('.form-group').find('.invalid-feedback');
        if (isValid) {
            $field.removeClass('is-invalid').addClass('is-valid');
            if ($feedback.length) {
                $feedback.text('');
            }
        } else {
            $field.removeClass('is-valid').addClass('is-invalid');
            if ($feedback.length) {
                $feedback.text(message);
            }
        }
    },

    /**
     * Retrieve data for all entered siblings.
     * @returns {Array<object>} An array of sibling data objects.
     */
    getAllSiblings: function() {
        var siblings = [];
        $('.sibling-group').each(function() {
            var $group = $(this);
            var siblingData = {
                relationship: $group.find('.sibling-relationship').val() || '',
                national_id: $group.find('.sibling-national-id').val() || '',
                name_ar: $group.find('.sibling-name-ar').val() || '',
                name_en: $group.find('.sibling-name-en').val() || '',
                faculty: $group.find('.sibling-faculty').val() || '',
                academic_level: $group.find('.sibling-academic-level').val() || ''
            };
            siblings.push(siblingData);
        });
        return siblings;
    },

   /**
    * Get all siblings whose relationship matches the provided gender.
    * @param {string} gender - The gender to filter by ('male' or 'female').
    * @returns {Array<object>} An array of matching sibling objects.
    */
   getSiblingsByGender: function(gender) {
      if (!gender) return [];
      // Map gender to relationship: 'male' => 'brother', 'female' => 'sister'
      var relationship = gender === 'male' ? 'brother' : (gender === 'female' ? 'sister' : '');
      if (!relationship) return [];
      return this.getAllSiblings().filter(function(sibling) {
         return sibling.relationship === relationship;
      });
   },

    /**
     * Validate all sibling form groups.
     * @returns {boolean} True if all sibling forms are valid, otherwise false.
     */
    validateAll: function() {
        var allValid = true;
        var self = this;

        $('.sibling-group').each(function() {
            var $group = $(this);
            // Check all inputs and selects with the 'required' attribute within this group.
            $group.find('input[required], select[required]').each(function() {
                var $field = $(this);
                if (!$field.val()) {
                    self.setFieldValidation($field, false, TRANSLATIONS.validation.required || 'This field is required.');
                    allValid = false;
                } else if ($field.hasClass('is-invalid')) {
                    // If the field already has an invalid state from other validations
                    allValid = false;
                }
            });
        });
        return allValid;
    }
};

// ===========================
// SELECT2 MANAGER
// ===========================
var Select2Manager = {
   /**
    * Configuration for all Select2 elements
    */
   config: {
      form: {
         '#gender': {
            placeholder: TRANSLATIONS.placeholders.gender,
            allowClear: true
         },
         '#nationality': {
            placeholder: TRANSLATIONS.placeholders.select_nationality,
            allowClear: true
         },
         '#governorate': {
            placeholder: TRANSLATIONS.placeholders.select_governorate,
            allowClear: true
         },
         '#city': {
            placeholder: TRANSLATIONS.placeholders.city?.select,
            allowClear: true
         },
         '#faculty': {
            placeholder: TRANSLATIONS.placeholders.select_faculty,
            allowClear: true
         },
         '#program': {
            placeholder: TRANSLATIONS.placeholders.program?.select,
            allowClear: true
         },
         '#academic-year': {
            placeholder: 'Select Level',
            allowClear: true
         },
         '#guardian-relationship': {
            placeholder: TRANSLATIONS.validation.guardian_relationship,
            allowClear: true
         },
         '#is-guardian-abroad': {
            placeholder: 'Select',
            allowClear: true
         },
         '#guardian-abroad-country': {
            placeholder: TRANSLATIONS.placeholders.select_country,
            allowClear: true
         },
         '#living-with-guardian': {
            placeholder: 'Select',
            allowClear: true
         },
         '#guardian-governorate': {
            placeholder: TRANSLATIONS.placeholders.select_guardian_governorate,
            allowClear: true
         },
         '#guardian-city': {
            placeholder: TRANSLATIONS.placeholders.guardianCity?.select,
            allowClear: true
         },
         '.sibling-faculty':{
            placeholder: TRANSLATIONS.placeholders.select_sibling_faculty,
            allowClear: true
         },
         '#emergency-contact-relationship': {
            placeholder: TRANSLATIONS.validation.emergency_contact_relationship,
            allowClear: true
         },
         '#emergency-contact-governorate': {
            placeholder: TRANSLATIONS.placeholders.select_emergency_contact_governorate,
            allowClear: true
         },
         '#emergency-contact-city': {
            placeholder: TRANSLATIONS.placeholders.emergencyContactCity?.select,
            allowClear: true
         },
      }
   },

   /**
    * Initialize all form Select2 elements
    */
   initFormSelect2: function () {
      Object.keys(this.config.form).forEach(function (selector) {
         Utils.initSelect2(selector, Select2Manager.config.form[selector]);
      });
   },

   /**
    * Initialize all Select2 elements
    */
   initAll: function () {
      this.initFormSelect2();
   },
};

// ===========================
// VALIDATION SERVICE
// ===========================
var ValidationService = {
   validator: null,

   /**
    * Initialize jQuery Validation
    */
   init: function () {
      this.setupValidationRules();
      this.initializeValidator();
   },

   /**
    * Setup validation rules and messages
    */
   setupValidationRules: function () {
      // Add custom validation methods
      $.validator.addMethod('conditionalRequired', function (value, element, params) {
         var condition = params[0];
         var conditionValue = params[1];
         return $(condition).val() !== conditionValue || value.length > 0;
      }, TRANSLATIONS.validation.required_conditional);

      $.validator.addMethod('checkedRequired', function (value, element) {
         return $(element).is(':checked');
      }, TRANSLATIONS.validation.required_checked);

      // Regex-based validations
      $.validator.addMethod('egyptianNationalId', function (value, element) {
         if (!value) return true;
         var regex = /^[0-9]{14}$/;
         return regex.test(value);
      }, TRANSLATIONS.validation.egyptian_national_id);

      // International phone validation
      $.validator.addMethod('internationalPhone', function (value, element) {
         if (!value) return true; // allow empty, "required" handles it
         var cleanValue = value.replace(/[\s\-\(\)]/g, '');
         var regex = /^\+?[1-9]\d{6,15}$/;
         return regex.test(cleanValue);
      }, TRANSLATIONS.validation.international_phone);

      // Egyptian phone validation
      $.validator.addMethod('egyptianPhone', function (value, element) {
         if (!value) return true;
         var cleanValue = value.replace(/[\s\-\(\)]/g, '');
         var regex = /^(010|011|012|015)[0-9]{8}$/;
         return regex.test(cleanValue);
      }, TRANSLATIONS.validation.egyptian_phone);

      /**
       * Unified phone validation: if guardian is abroad, use international format, else Egyptian.
       */
      $.validator.addMethod('guardianPhoneConditional', function (value, element, param) {
         if (!value) return true;
         var isAbroad = $(param).val();
         var cleanValue = value.replace(/[\s\-\(\)]/g, '');

         if (isAbroad === 'yes') {
            var intlRegex = /^\+?[1-9]\d{6,15}$/;
            return intlRegex.test(cleanValue);
         } else {
            var egyRegex = /^(010|011|012|015)[0-9]{8}$/;
            return egyRegex.test(cleanValue);
         }
      }, function (param) {
         var isAbroad = $(param).val();
         return isAbroad === 'yes' ?
            TRANSLATIONS.validation.international_phone :
            TRANSLATIONS.validation.egyptian_phone;
      });

      $.validator.addMethod('academicId', function (value, element) {
         if (!value) return true;
         var regex = /^[0-9]{8,12}$/;
         return regex.test(value);
      }, TRANSLATIONS.validation.academic_id);

      $.validator.addMethod('arabicName', function (value, element) {
         if (!value) return true;
         var regex = /^[\u0600-\u06FF\s]+$/;
         return regex.test(value);
      }, TRANSLATIONS.validation.arabic_name);

      $.validator.addMethod('englishName', function (value, element) {
         if (!value) return true;
         var regex = /^[A-Za-z\s]+$/;
         return regex.test(value);
      }, TRANSLATIONS.validation.english_name);

      // Age validation
      $.validator.addMethod('minimumAge', function (value, element, param) {
         if (!value) return true;
         var birthDate = new Date(value);
         if (isNaN(birthDate)) return false;

         var today = new Date();
         var age = today.getFullYear() - birthDate.getFullYear();
         var monthDiff = today.getMonth() - birthDate.getMonth();

         if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
         }
         return age >= param;
      }, function (param) {
         return TRANSLATIONS.validation.minimum_age.replace('{0}', param);
      });

      // GPA validation
      $.validator.addMethod('gpaRange', function (value, element, param) {
         if (!value) return true;
         var gpa = parseFloat(value);
         return gpa >= param[0] && gpa <= param[1];
      }, function (param) {
         return TRANSLATIONS.validation.gpa_range
            .replace('{0}', param[0])
            .replace('{1}', param[1]);
      });

      // Dependency validations
      $.validator.addMethod('dependsOn', function (value, element, param) {
         var dependentField = param.field;
         var dependentValue = param.value;
         var operator = param.operator || 'equals';
         var $dependentField = $(dependentField);
         var dependentFieldValue = $dependentField.is('select') ?
            $dependentField.find('option:selected').val() :
            $dependentField.val();

         console.log('dependsOn validation:', {
            value,
            element,
            param,
            dependentField,
            dependentValue,
            operator,
            dependentFieldValue
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
      }, function (param) {
         return TRANSLATIONS.validation.dependency_required
            .replace('{field}', $(param.field).attr('name') || 'this field');
      });

      // Compare fields validation
      $.validator.addMethod('compareField', function (value, element, param) {
         if (!value) return true;
         var otherValue = $(param.field).val();

         switch (param.operator) {
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
      }, function (param) {
         return TRANSLATIONS.validation.field_comparison_failed
            .replace('{field}', $(param.field).attr('name') || 'the other field');
      });

      // Email domain validation
      $.validator.addMethod('emailDomain', function (value, element, param) {
         if (!value) return true;
         var parts = value.split('@');
         if (parts.length < 2) return false;
         var emailDomain = parts[1].toLowerCase();
         return param.map(d => d.toLowerCase()).includes(emailDomain);
      }, TRANSLATIONS.validation.email_domain);
   },


   /**
    * Initialize the validator
    */
   initializeValidator: function () {
      var self = this;

      this.validator = $('#profileForm').validate({
         ignore: ':hidden:not(.validate-hidden)', // skip hidden fields except ones you mark
         errorClass: 'is-invalid',
         validClass: 'is-valid',
         errorElement: 'div',

         errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');

            if (element.hasClass('select2-hidden-accessible')) {
               // place error after Select2 container, not the hidden <select>
               error.insertAfter(element.next('.select2'));
            } else {
               error.insertAfter(element);
            }
         },

         highlight: function (element) {
            var $el = $(element);

            if ($el.hasClass('select2-hidden-accessible')) {
               // add invalid class to Select2's visible selection
               $el.next('.select2').find('.select2-selection')
                  .addClass('is-invalid')
                  .removeClass('is-valid');
            } else {
               $el.addClass('is-invalid').removeClass('is-valid');
            }
         },

         unhighlight: function (element) {
            var $el = $(element);

            if ($el.hasClass('select2-hidden-accessible')) {
               // add valid class to Select2's visible selection
               $el.next('.select2').find('.select2-selection')
                  .removeClass('is-invalid')
                  .addClass('is-valid');
            } else {
               $el.removeClass('is-invalid').addClass('is-valid');
            }
         },

         rules: self.getValidationRules(),
         messages: self.getValidationMessages()
      });

      $('#profileForm .select2-hidden-accessible').on('change', function () {
         $(this).valid();
      });
   },

   /**
    * Get validation rules
    * @returns {object}
    */
   getValidationRules: function () {
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

         // Step 4: Guardian Information - Basic
         guardian_relationship: {
            required: true
         },
         guardian_name_ar: {
            required: true,
            arabicName: true
         },
         guardian_name_en: {
            required: true,
            englishName: true
         },
         guardian_phone: {
            required: true,
            guardianPhoneConditional: '#is-guardian-abroad'
         },
         guardian_national_id: {
            required: true,
            egyptianNationalId: true
         },
         guardian_email: {
            required: false,
            email: true
         },
         is_guardian_abroad: {
            required: true
         },

         // Step 4: Guardian abroad conditional fields
         guardian_abroad_country: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            }
         },
         living_with_guardian: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'no',
               operator: 'equals'
            }
         },
         guardian_governorate: {
            dependsOn: {
               field: '#living-with-guardian',
               value: 'no',
               operator: 'equals'
            }
         },
         guardian_city: {
            dependsOn: {
               field: '#living-with-guardian',
               value: 'no',
               operator: 'equals'
            }
         },

         // Step 5: Sibling Information
         has_sibling_in_dorm: {
            required: true
         },

         // Step 6: Emergency Contact (only when guardians are abroad)
         emergency_contact_name_ar: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            },
            arabicName: true
         },
         emergency_contact_name_en: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            },
            englishName: true
         },
         emergency_contact_relationship: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            }
         },
         emergency_contact_phone: {
            dependsOn: {
               field: '#is-guardian-abroad',
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
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            }
         },
         emergency_contact_city: {
            dependsOn: {
               field: '#is-guardian-abroad',
               value: 'yes',
               operator: 'equals'
            }
         },
         emergency_contact_street: {
            dependsOn: {
               field: '#is-guardian-abroad',
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
   getValidationMessages: function () {
      return {
         // Step 1: Personal Information
         national_id: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.national_id),
            egyptianNationalId: TRANSLATIONS.validation.egyptian_national_id
         },
         name_ar: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.name_ar),
            arabicName: TRANSLATIONS.validation.arabic_name,
            minlength: TRANSLATIONS.validation.minlength.replace('{field}', TRANSLATIONS.fields.name_ar).replace('{min}', 2)
         },
         name_en: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.name_en),
            englishName: TRANSLATIONS.validation.english_name,
            minlength: TRANSLATIONS.validation.minlength.replace('{field}', TRANSLATIONS.fields.name_en).replace('{min}', 2)
         },
         birth_date: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.birth_date),
            date: TRANSLATIONS.validation.date,
            minimumAge: TRANSLATIONS.validation.minimum_age
         },
         gender: TRANSLATIONS.validation.gender,
         nationality: TRANSLATIONS.validation.nationality,

         // Step 2: Contact Information
         governorate: TRANSLATIONS.validation.governorate,
         city: TRANSLATIONS.validation.city,
         street: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.street),
            minlength: TRANSLATIONS.validation.minlength.replace('{field}', TRANSLATIONS.fields.street).replace('{min}', 5)
         },
         phone: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.phone),
            egyptianPhone: TRANSLATIONS.validation.egyptian_phone
         },

         // Step 3: Academic Information
         faculty: TRANSLATIONS.validation.faculty,
         program: TRANSLATIONS.validation.program,
         academic_year: TRANSLATIONS.validation.academic_year,
         gpa: {
            dependsOn: TRANSLATIONS.validation.gpa.dependsOn,
            gpaRange: TRANSLATIONS.validation.gpa.gpaRange
         },
         score: {
            dependsOn: TRANSLATIONS.validation.score.dependsOn,
            number: TRANSLATIONS.validation.number
         },
         // Step 4: Guardian Information
         guardian_relationship: TRANSLATIONS.validation.guardian_relationship,

         guardian_name_ar: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.guardian_name_ar),
            arabicName: TRANSLATIONS.validation.arabic_name
         },
         guardian_name_en: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.guardian_name_en),
            englishName: TRANSLATIONS.validation.english_name
         },
         guardian_phone: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.guardian_phone),
            egyptianPhone: TRANSLATIONS.validation.egyptian_phone,
            internationalPhone: TRANSLATIONS.validation.international_phone
         },
         guardian_email: {
            email: TRANSLATIONS.validation.email
         },
         guardian_national_id: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.guardian_national_id),
            egyptianNationalId: TRANSLATIONS.validation.egyptian_national_id
         },
         is_guardian_abroad: TRANSLATIONS.validation.is_guardian_abroad,
         guardian_abroad_country: TRANSLATIONS.validation.guardian_abroad_country,
         living_with_guardian: TRANSLATIONS.validation.living_with_guardian,
         guardian_governorate: TRANSLATIONS.validation.guardian_governorate,
         guardian_city: TRANSLATIONS.validation.guardian_city,
         guardian_country: TRANSLATIONS.validation.guardian_country,

         // Step 5: Sibling Information
         has_sibling_in_dorm: TRANSLATIONS.validation.has_sibling_in_dorm,

         // Step 6: Emergency Contact
         emergency_contact_name_ar: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.emergency_contact_name_ar),
            arabicName: TRANSLATIONS.validation.arabic_name
         },
         emergency_contact_name_en: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.emergency_contact_name_en),
            englishName: TRANSLATIONS.validation.english_name
         },
         emergency_contact_relationship: TRANSLATIONS.validation.emergency_contact_relationship,
         emergency_contact_phone: {
            required: TRANSLATIONS.validation.required.replace('{field}', TRANSLATIONS.fields.emergency_contact_phone),
            egyptianPhone: TRANSLATIONS.validation.egyptian_phone,
            compareField: TRANSLATIONS.validation.compare_field.replace('{field}', TRANSLATIONS.fields.emergency_contact_phone)
         },
         emergency_contact_governorate: TRANSLATIONS.validation.emergency_contact_governorate,
         emergency_contact_city: TRANSLATIONS.validation.emergency_contact_city,
         emergency_contact_street: TRANSLATIONS.validation.emergency_contact_street,

         // Step 7: Terms
         terms_checkbox: TRANSLATIONS.validation.terms_checkbox,

         // Fallback for missing messages
         default: TRANSLATIONS.validation.default
      };
   },

   /**
    * Validate a specific step
    * @param {string} tabSelector
    * @returns {boolean}
    */
   validateStep: function (tabSelector) {
      if (!this.validator) {
         return true;
      }

      var $step = $(tabSelector);
      var stepId = $step.attr('id');
      var $stepBtn = $('[data-bs-target="#' + stepId + '"]');
      var isValid = true;

      $stepBtn.removeClass('is-valid is-invalid');

      // Validate all enabled and visible (or .validate-hidden) fields in the step
      $step.find('input, select, textarea').each(function () {
         var fieldValid = ValidationService.validator.element(this);
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
   validateAllSteps: function () {
      if (!this.validator) {
         return true;
      }

      return this.validator.form();
   },

   /**
    * Find first invalid step
    * @returns {number|null}
    */
   findFirstInvalidStep: function () {

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
   resetField: function (fieldSelector) {
      if (this.validator) {
         this.validator.resetForm();
         $(fieldSelector).removeClass('is-invalid is-valid');
      }
   },

   /**
    * Update validation rules dynamically
    */
   updateConditionalValidation: function () {
      if (!this.validator) {
         return;
      }

      // Re-validate conditional fields when their dependencies change
      var conditionalFields = [
         '#gpa',
         '#score',
         '#guardian-abroad-country',
         '#guardian-governorate',
         '#guardian-city',
         '#emergency-contact-name_ar',
         '#emergency-contact-name_en',
         '#emergency-contact-relationship',
         '#emergency-contact-phone',
         '#emergency-contact-governorate',
         '#emergency-contact-city',
         '#emergency-contact-street'
      ];

      conditionalFields.forEach(function (field) {
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
      selector: "is-guardian-abroad",
      condition: '=',
      value: 'no'
   }, ],
   /**
    * Initialize navigation manager
    */
   init: function () {
      this.bindEvents();
   },

   /**
    * Bind all navigation events
    */
   bindEvents: function () {
      this.handleNextButton();
      this.handlePreviousButton();
   },

   /**
    * Check if a step should be skipped based on conditions
    * @param {string} stepSelector - Step selector (e.g., '#step6')
    * @returns {boolean}
    */
   shouldSkipStep: function (stepSelector) {
      var self = this;
      var stepNumber = self.extractStepNumber(stepSelector.replace('#', ''));

      if (!stepNumber) return false;

      // Find matching skip rules for this step
      var matchingRules = self.SkippedSteps.filter(function (rule) {
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
   evaluateCondition: function (leftValue, operator, rightValue) {
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
   convertValue: function (value) {
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
   extractStepNumber: function (stepId) {
      var match = stepId.match(/^step(\d+)$/);
      return match ? parseInt(match[1], 10) : null;
   },

   /**
    * Generate step selector from step number
    * @param {number} stepNumber
    * @returns {string}
    */
   generateStepSelector: function (stepNumber) {
      return '#step' + stepNumber;
   },

   /**
    * Find next available step
    * @param {number} currentStep
    * @returns {string|null}
    */
   findNextAvailableStep: function (currentStep) {
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
   findPreviousAvailableStep: function (currentStep) {
      var prevStep = currentStep - 1;

      while (prevStep > 0) {
         var prevSelector = this.generateStepSelector(prevStep);

         // Check if step exists in DOM
         if ($(prevSelector).length === 0) {
            prevStep--;
            continue;
         }

         // Check if step should be skipped
         if (!this.shouldSkipStep(prevSelector)) {
            return prevSelector;
         }

         prevStep--;
      }

      return null;
   },

   /**
    * Handle next button clicks
    */
   handleNextButton: function () {
      var self = this;

      $(document).on('click', '.next-Btn', function (e) {
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
         }
      });
   },

   /**
    * Handle previous button clicks
    */
   handlePreviousButton: function () {
      var self = this;

      $(document).on('click', '.prev-Btn', function (e) {
         e.preventDefault();

         var $tabPane = $(this).closest('.tab-pane');
         var currentStepId = $tabPane.attr('id');
         var currentStepNumber = self.extractStepNumber(currentStepId);


         if (!currentStepNumber) {
            console.warn('Could not extract step number from:', currentStepId);
            return;
         }

         // Find previous available step
         var prevStepSelector = self.findPreviousAvailableStep(currentStepNumber);

         if (prevStepSelector) {
            self.showTab(prevStepSelector);
         }
      });
   },

   /**
    * Find first invalid tab that needs completion
    * @param {jQuery} allTabs - All visible tabs
    * @param {number} targetIndex - Index of target tab
    * @returns {number} Index of first invalid tab, or -1 if none
    */
   findFirstInvalidTabIndex: function (allTabs, targetIndex) {
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
   showTab: function (tabId) {
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
      setTimeout(function () {
         $(tabId + ' input:visible:first, ' + tabId + ' select:visible:first, ' + tabId + ' textarea:visible:first').focus();
      }, 100);
   },

   /**
    * Get current active step number
    * @returns {number|null}
    */
   getCurrentStep: function () {
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
   isStepAvailable: function (stepNumber) {
      var stepSelector = this.generateStepSelector(stepNumber);
      return !this.shouldSkipStep(stepSelector);
   },

   /**
    * Get list of all available steps
    * @returns {Array<number>}
    */
   getAvailableSteps: function () {
      var self = this;
      var availableSteps = [];

      $('.tab-pane[id^="step"]').each(function () {
         var stepNumber = self.extractStepNumber($(this).attr('id'));
         if (stepNumber && self.isStepAvailable(stepNumber)) {
            availableSteps.push(stepNumber);
         }
      });

      return availableSteps.sort(function (a, b) {
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
   init: function () {
      this.initializeFieldStates();
      this.bindEvents();
      this.triggerInitialChanges();
   },

   /**
    * Initialize field states (disable dependent fields)
    */
   initializeFieldStates: function () {
      // Disable dependent fields initially
      this.disableField('#city', TRANSLATIONS.placeholders.city.disableField);
      this.disableField('#guardian-city', TRANSLATIONS.placeholders.guardianCity.disableField);
      this.disableField('#emergency-contact-city', TRANSLATIONS.placeholders.emergencyContactCity.disableField);
      this.disableField('#program', TRANSLATIONS.placeholders.program.disableField);
      this.disableField('#sibling-to-stay-with', TRANSLATIONS.placeholders.siblingToStayWith.select);
   },

   /**
    * Disable a field with placeholder text
    * @param {string} selector - Field selector
    * @param {string} placeholder - Placeholder text
    */
   disableField: function (selector, placeholder) {
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
   enableField: function (selector) {
      var $field = $(selector);
      if ($field.length) {
         $field.prop('disabled', false);
         $field.removeClass('field-disabled');
      }
   },

   /**
    * Bind conditional field events
    */
   bindEvents: function () {
      this.handleGovernorateChange();
      this.handleGuardianGovernorateChange();
      this.handleEmergencyContactGovernorateChange();
      this.handleFacultyChange();
      this.handleGpaAndScoreChange();
      this.handleGuardianAbroadChange();
      this.handleLivingWithGuardianChange();
      this.handleSiblingInDormChange();
      this.handleReservationStayPreferenceChange();
      this.handleReservationRoomTypeChange();
      this.handleReservationSingleRoomOptionsChange();
   },

   /**
    * Handle governorate change
    */
   handleGovernorateChange: function () {
      $('#governorate').change(function () {
         var $governorate = $(this);
         var governorateId = $governorate.val();

         if (governorateId) {
            ConditionalFieldsManager.enableField('#city');

            ApiService.fetchCity(governorateId)
               .done(function (response) {
                  if (response.success && response.data) {
                     Utils.populateSelect($('#city'), response.data, {
                        placeholder: TRANSLATIONS.placeholders.city.select,
                        valueField: 'id',
                        textField: 'name'
                     });

                     var pendingCityId = $governorate.data('pending-city-id');
                     if (pendingCityId) {
                        $('#city').val(pendingCityId);
                        $governorate.removeData('pending-city-id');
                     }
                  } else {
                     $('#city').html('<option value="">' + TRANSLATIONS.placeholders.city.noCities + '</option>');
                  }
               })
               .fail(function (xhr) {
                  console.error('Failed to load cities:', xhr);
                  $('#city').html('<option value="">' + TRANSLATIONS.placeholders.city.errorLoading + '</option>');
               });
         } else {
            ConditionalFieldsManager.disableField('#city', TRANSLATIONS.placeholders.city.disableField);
         }
      });
   },

   /**
    * Handle guardian governorate change (guardian address)
    */
   handleGuardianGovernorateChange: function () {
      $('#guardian-governorate').change(function () {
         var $governorate = $(this);
         var governorateId = $governorate.val();

         if (governorateId) {
            ConditionalFieldsManager.enableField('#guardian-city');

            ApiService.fetchCity(governorateId)
               .done(function (response) {
                  if (response.success && response.data) {
                     Utils.populateSelect($('#guardian-city'), response.data, {
                        placeholder: TRANSLATIONS.placeholders.city.select,
                        valueField: 'id',
                        textField: 'name'
                     });

                     var pendingCityId = $governorate.data('pending-city-id');
                     if (pendingCityId) {
                        $('#guardian-city').val(pendingCityId);
                        $governorate.removeData('pending-city-id');
                     }
                  } else {
                     $('#guardian-city').html('<option value="">' + TRANSLATIONS.placeholders.city.noCities + '</option>');
                  }
               })
               .fail(function (xhr) {
                  console.error('Failed to load guardian cities:', xhr);
                  $('#guardian-city').html('<option value="">' + TRANSLATIONS.placeholders.city.errorLoading + '</option>');
               });
         } else {
            ConditionalFieldsManager.disableField('#guardian-city', TRANSLATIONS.placeholders.guardianCity.disableField);
         }
      });
   },

   /**
    * Handle emergency contact governorate change
    */
   handleEmergencyContactGovernorateChange: function () {
      $('#emergency-contact-governorate').change(function () {
         var $governorate = $(this);
         var governorateId = $governorate.val();
         if (governorateId) {
            ConditionalFieldsManager.enableField('#emergency-contact-city');

            ApiService.fetchCity(governorateId)
               .done(function (response) {
                  if (response.success && response.data) {
                     Utils.populateSelect($('#emergency-contact-city'), response.data, {
                        placeholder: TRANSLATIONS.placeholders.city.select,
                        valueField: 'id',
                        textField: 'name'
                     });

                     var pendingCityId = $governorate.data('pending-city-id');
                     if (pendingCityId) {
                        $('#emergency-contact-city').val(pendingCityId);
                        $governorate.removeData('pending-city-id');
                     }
                  } else {
                     $('#emergency-contact-city').html('<option value="">' + TRANSLATIONS.placeholders.city.noCities + '</option>');
                  }
               })
               .fail(function (xhr) {
                  console.error('Failed to load emergency contact cities:', xhr);
                  $('#emergency-contact-city').html('<option value="">' + TRANSLATIONS.placeholders.city.errorLoading + '</option>');
               });
         } else {
            ConditionalFieldsManager.disableField('#emergency-contact-city', TRANSLATIONS.placeholders.emergencyContactCity.disableField);
         }
      });
   },

   /**
    * Handle faculty change
    */
   handleFacultyChange: function () {
      $('#faculty').change(function () {
         var $faculty = $(this);
         var facultyId = $faculty.val();

         if (facultyId) {
            ConditionalFieldsManager.enableField('#program');

            ApiService.fetchProgram(facultyId)
               .done(function (response) {
                  if (response.success && response.data) {
                     Utils.populateSelect($('#program'), response.data, {
                        placeholder: TRANSLATIONS.placeholders.program.select,
                        valueField: 'id',
                        textField: 'name'
                     });
                     var pendingProgramId = $faculty.data('pending-program-id');
                     if (pendingProgramId) {
                        $('#program').val(pendingProgramId);
                        $faculty.removeData('pending-program-id');
                     }
                  } else {
                     $('#program').html('<option value="">' + TRANSLATIONS.placeholders.program.noPrograms + '</option>');
                  }
               })
               .fail(function (xhr) {
                  console.error('Failed to load programs:', xhr);
                  $('#program').html('<option value="">' + TRANSLATIONS.placeholders.program.errorLoading + '</option>');
               });
         } else {
            ConditionalFieldsManager.disableField('#program', TRANSLATIONS.placeholders.program.disableField);
         }
      });
   },

   /**
    * Handle GPA and score change
    */
   handleGpaAndScoreChange: function () {
      $('#is-new-comer').change(function () {
         var value = $(this).val();
         var isNewComer = (value === 'true' || value === true);

         if (!isNewComer) {
            $('#gpa-div').removeClass('d-none');
            $('#score-div').addClass('d-none');
         } else {
            $('#gpa-div').addClass('d-none');
            $('#score-div').removeClass('d-none');
         }
      });
   },

   /**
    * Handle guardian abroad change
    */
   handleGuardianAbroadChange: function () {
      $('#is-guardian-abroad').change(function () {
         var value = $(this).val();

         if (value === 'yes') {
            ConditionalFieldsManager.showAbroadFields();
         } else if (value === 'no') {
            ConditionalFieldsManager.showDomesticFields();
         } else {
            ConditionalFieldsManager.hideAllGuardianFields();
         }
      });
   },

   /**
    * Handle living with guardian change
    */
   handleLivingWithGuardianChange: function () {
      $('#living-with-guardian').change(function () {
         var value = $(this).val();

         if (value === 'no') {
            ConditionalFieldsManager.showGuardianAddressFields();
         } else {
            ConditionalFieldsManager.hideGuardianAddressFields();
         }
      });
   },

   /**
    * Handle sibling in dorm change (updated for multiple siblings)
    */
   handleSiblingInDormChange: function () {
      $('#has-sibling-in-dorm').change(function () {
         var value = $(this).val();

         if (value === 'yes') {
            ConditionalFieldsManager.showSiblingDetails();
               SiblingsManager.init();
         } else {
            ConditionalFieldsManager.hideSiblingDetails();
            // Hide reservation stay preference fields
            ConditionalFieldsManager.hideReservationStayPreferenceFields();
            ConditionalFieldsManager.hideReservationSiblingDetails();
            ConditionalFieldsManager.showReservationRoomType();
         }

         // Update reservation options based on sibling availability
         ConditionalFieldsManager.updateReservationOptions();
      });
   },

   /**
    * Update reservation options based on available siblings
    */
   updateReservationOptions: function () {
      var hasSiblings = $('#has-sibling-in-dorm').val() === 'yes';
      var currentUserGender = $('#gender').val();

      if (hasSiblings) {
         var sameGenderSiblings = SiblingsManager.getSiblingsByGender(currentUserGender);

         if (sameGenderSiblings.length > 0) {
            ConditionalFieldsManager.showReservationStayPreferenceFields();
            this.updateReservationSiblingDropdown(sameGenderSiblings);
         } else {
            ConditionalFieldsManager.hideReservationStayPreferenceFields();
            ConditionalFieldsManager.showReservationRoomType();
         }
      } else {
         ConditionalFieldsManager.hideReservationStayPreferenceFields();
         ConditionalFieldsManager.showReservationRoomType();
      }
   },

   /**
    * Update reservation sibling dropdown with available siblings
    * @param {Array} siblings - Array of same-gender siblings
    */
   updateReservationSiblingDropdown: function (siblings) {
      var $siblingSelect = $('#sibling-to-stay-with');
      $siblingSelect.empty().append('<option value="">' + (TRANSLATIONS.placeholders.sibling.select || 'Select Sibling') + '</option>');
      $.each(siblings, function (index, sibling) {
         var displayName = sibling.name_en + (sibling.name_ar ? ' (' + sibling.name_ar + ')' : '');
         $siblingSelect.append('<option value="' + sibling.national_id + '">' + displayName + '</option>');
      });
      ConditionalFieldsManager.enableField('#sibling-to-stay-with');
   },

   /**
    * Handle reservation stay preference change (updated)
    */
   handleReservationStayPreferenceChange: function () {
      $('#stay-preference').change(function () {
         var value = $(this).val();
         if (value === 'stay_with_sibling') {
            ConditionalFieldsManager.showReservationSiblingDetails();
            ConditionalFieldsManager.hideReservationRoomType();
            ConditionalFieldsManager.hideReservationSingleRoomOptions();
            ConditionalFieldsManager.hideReservationDoubleRoomOptions();

            // Update sibling dropdown with current siblings
            ConditionalFieldsManager.updateReservationOptions();
         } else if (value === 'stay_alone') {
            ConditionalFieldsManager.hideReservationSiblingDetails();
            ConditionalFieldsManager.showReservationRoomType();
         } else {
            ConditionalFieldsManager.hideReservationSiblingDetails();
            ConditionalFieldsManager.hideReservationRoomType();
            ConditionalFieldsManager.hideReservationSingleRoomOptions();
            ConditionalFieldsManager.hideReservationDoubleRoomOptions();
         }
      });
   },

   /**
    * Handle reservation room type change
    */
   handleReservationRoomTypeChange: function () {
      $('#room-type').change(function () {
         var value = $(this).val();
         if (value === 'single') {
            ConditionalFieldsManager.showReservationSingleRoomOptions();
            ConditionalFieldsManager.hideReservationDoubleRoomOptions();
         } else if (value === 'double') {
            ConditionalFieldsManager.hideReservationSingleRoomOptions();
            ConditionalFieldsManager.hideReservationSingleRoomOldRoomDetails();
            ConditionalFieldsManager.showReservationDoubleRoomOptions();
         } else {
            ConditionalFieldsManager.hideReservationSingleRoomOptions();
            ConditionalFieldsManager.hideReservationDoubleRoomOptions();
            ConditionalFieldsManager.hideReservationSingleRoomOldRoomDetails();
         }
      });
   },

   /**
    * Handle reservation single room options change
    */
   handleReservationSingleRoomOptionsChange: function () {
      $('#single-room-preference').change(function () {
         var value = $(this).val();
         if (value === 'old_room') {
            ConditionalFieldsManager.showReservationSingleRoomOldRoomDetails();
            ConditionalFieldsManager.loadOldRoomDetails();
         } else {
            ConditionalFieldsManager.hideReservationSingleRoomOldRoomDetails();
         }
      });
   },

   /**
    * Load and display old room details
    */
   loadOldRoomDetails: function () {
      // This should fetch the user's previous room details from the server
      var oldRoomInfo = ProfileManager.getOldRoomDetails();
      if (oldRoomInfo) {
         var roomInfoHtml = '<strong>' + TRANSLATIONS.labels.roomNumber + ':</strong> ' + oldRoomInfo.roomNumber + '<br>' +
            '<strong>' + TRANSLATIONS.labels.building + ':</strong> ' + oldRoomInfo.building + '<br>' +
            '<strong>' + TRANSLATIONS.labels.floor + ':</strong> ' + oldRoomInfo.floor;
         $('#old-room-info').html(roomInfoHtml);
      } else {
         $('#old-room-info').html('<span class="text-muted">' + TRANSLATIONS.messages.noOldRoomFound + '</span>');
      }
   },

   /**
    * Show abroad country fields
    */
   showAbroadFields: function () {
      $('#guardian-abroad-country-div').removeClass('d-none');
      $('#living-with-guardian-div, #guardian-address-div').addClass('d-none');
   },

   /**
    * Show domestic guardian fields
    */
   showDomesticFields: function () {
      $('#guardian-abroad-country-div').addClass('d-none');
      $('#living-with-guardian-div').removeClass('d-none');

      if ($('#living-with-guardian').val() === 'no') {
         this.showGuardianAddressFields();
      } else {
         this.hideGuardianAddressFields();
      }
   },

   /**
    * Hide all guardian fields
    */
   hideAllGuardianFields: function () {
      $('#guardian-abroad-country-div, #living-with-guardian-div, #guardian-address-div').addClass('d-none');
   },

   /**
    * Show guardian address fields
    */
   showGuardianAddressFields: function () {
      $('#guardian-address-div').removeClass('d-none');
   },

   /**
    * Hide guardian address fields
    */
   hideGuardianAddressFields: function () {
      $('#guardian-address-div').addClass('d-none');
   },

   /**
    * Show sibling details
    */
   showSiblingDetails: function () {
      $('#siblingDetails').removeClass('d-none');
   },

   /**
    * Hide sibling details
    */
   hideSiblingDetails: function () {
      $('#siblingDetails').addClass('d-none');
   },

   /**
    * Show reservation stay preference fields
    */
   showReservationStayPreferenceFields: function () {
      $('#stay-preference-div').removeClass('d-none');
   },

   /**
    * Hide reservation stay preference fields
    */
   hideReservationStayPreferenceFields: function () {
      $('#stay-preference-div').addClass('d-none');
   },

   /**
    * Show reservation sibling details
    */
   showReservationSiblingDetails: function () {
      $('#sibling-details-div').removeClass('d-none');
   },

   /**
    * Hide reservation sibling details
    */
   hideReservationSiblingDetails: function () {
      $('#sibling-details-div').addClass('d-none');
   },

   /**
    * Show reservation room type
    */
   showReservationRoomType: function () {
      $('#room-type-div').removeClass('d-none');
   },

   /**
    * Hide reservation room type
    */
   hideReservationRoomType: function () {
      $('#room-type-div').addClass('d-none');
   },

   /**
    * Show reservation single room type options
    */
   showReservationSingleRoomOptions: function () {
      $('#single-room-options-div').removeClass('d-none');
   },

   /**
    * Hide reservation single room type options
    */
   hideReservationSingleRoomOptions: function () {
      $('#single-room-options-div').addClass('d-none');
   },

   /**
    * Show reservation double room type options
    */
   showReservationDoubleRoomOptions: function () {
      $('#double-room-options-div').removeClass('d-none');
   },

   /**
    * Hide reservation double room type options
    */
   hideReservationDoubleRoomOptions: function () {
      $('#double-room-options-div').addClass('d-none');
   },

   /**
    * Show reservation single room old room details
    */
   showReservationSingleRoomOldRoomDetails: function () {
      $('#old-room-details-div').removeClass('d-none');
   },

   /**
    * Hide reservation single room old room details
    */
   hideReservationSingleRoomOldRoomDetails: function () {
      $('#old-room-details-div').addClass('d-none');
   },

   /**
    * Check and enable fields based on their dependencies when data is loaded
    */
   checkAndEnableFields: function () {
      if ($('#governorate').val()) {
         this.enableField('#city');
      }
      if ($('#guardian-governorate').val()) {
         this.enableField('#guardian-city');
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
   triggerInitialChanges: function () {
      $('#is-guardian-abroad').trigger('change');
      $('#living-with-guardian').trigger('change');
      $('#has-sibling-in-dorm').trigger('change');
      $('#guardian-governorate').trigger('change');
      $('#is-new-comer').trigger('change');
      $('#stay-preference').trigger('change');
      $('#room-type').trigger('change');
      $('#single-room-preference').trigger('change');

      this.checkAndEnableFields();
   },

   /**
    * Reset reservation fields
    */
   resetReservationFields: function () {
      $('#stay-preference').val('');
      $('#sibling-to-stay-with').val('');
      $('#room-type').val('');
      $('#single-room-preference').val('');
      $('#double-room-preference').val('');

      this.hideReservationStayPreferenceFields();
      this.hideReservationSiblingDetails();
      this.hideReservationRoomType();
      this.hideReservationSingleRoomOptions();
      this.hideReservationDoubleRoomOptions();
      this.hideReservationSingleRoomOldRoomDetails();
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
   init: async function () {
      await this.showInitialSwalAlert();
      this.bindEvents();
      this.handleEnterKeyPress();
   },

   /**
    * Handle Enter key press to act as Next button instead of form submission
    */
   handleEnterKeyPress: function () {
      $(document).on('keypress', '#profileForm input, #profileForm select', function (e) {

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
   showInitialSwalAlert: function () {
      return new Promise(function (resolve) {
         Utils.showConfirmDialog({
            title: TRANSLATIONS.messages.dataResponsibility.title,
            text: TRANSLATIONS.messages.dataResponsibility.message,
            confirmButtonText: TRANSLATIONS.messages.dataResponsibility.confirmButtonText
         }).then(function (result) {
            if (result.isConfirmed) {
               resolve();
            } else {
               reject();
            }
         });
      });
   },

   /**
    * Bind form events
    */
   bindEvents: function () {
      this.handleFormSubmission();
   },

   /**
    * Show the loader 
    */
   showLoader: function () {
      $('#form-loader').removeClass('d-none');
   },

   /**
    * Hide the loader
    */
   hideLoader: function () {
      $('#form-loader').addClass('d-none');
   },
   /**
    * Handle form submission
    */
   handleFormSubmission: function () {
      $('#profileForm').on('submit', function (e) {
         e.preventDefault();

         if (!FormManager.validateFormSubmission()) {
            Utils.showError(TRANSLATIONS.messages.validationError);
            return false;
         }

         ProfileManager.submitProfile();
      });
   },
   /**
    * Validate form submission
    * @returns {boolean}
    */
   validateFormSubmission: function () {
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
   init: async function () {
      ValidationService.init();
      NavigationManager.init();
      Select2Manager.initAll();
      ConditionalFieldsManager.init();
      await FormManager.init();
      await ProfileManager.init();
   }
};

// ===========================
// DOCUMENT READY
// ===========================
$(document).ready(function () {
   CompleteProfileApp.init();
});