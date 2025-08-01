// ===========================
// UTILITY FUNCTIONS
// ===========================
const Utils = {

  /**
   * Show success notification (normal alert or toast)
   * @param {string} message - Success message to display
   * @param {boolean|null} toast - Show as toast (true), normal alert (false), or auto (null)
   * @param {string|null} position - Toast position (e.g., 'top-end'), or null for default
   */
  showSuccess(message, toast = null, position = null) {
    if (toast === true) {
      Swal.fire({
        toast: true,
        position: position || 'top-end',
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1800,
        timerProgressBar: true
      });
    } else {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        showConfirmButton: true
      });
    }
  },

  /**
   * Show error alert (with option for toast)
   * @param {string} message - Error message to display
   * @param {boolean|null} toast - Show as toast (true), normal alert (false), or auto (null)
   * @param {string|null} position - Toast position (e.g., 'top-end'), or null for default
   */
  showError(message, toast = null, position = null) {
    if (toast === true) {
      Swal.fire({
        toast: true,
        position: position || 'top-end',
        icon: 'error',
        title: message,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
      });
    }
  },

  /**
   * Show error alert with HTML content
   * @param {string} title - Error title
   * @param {string} htmlContent - HTML content for the error
   */
  showErrorHtml(title, htmlContent) {
    Swal.fire({
      icon: 'error',
      title: title,
      html: htmlContent
    });
  },

  /**
   * Set loading state for a button
   * @param {string} btn - Button selector
   * @param {boolean} isLoading - Whether button is in loading state
   * @param {Object} options - Configuration options
   */
  setLoadingState(btn, isLoading, options = {}) {
    const $btn = $(btn);
    const defaults = {
      loadingText: 'Loading...',
      loadingIcon: 'bx bx-loader-alt bx-spin me-1',
      normalText: '',
      normalIcon: ''
    };
    const config = { ...defaults, ...options };

    if (isLoading) {
      Utils.disable(btn, true);
      $btn.html(`<i class="${config.loadingIcon}"></i>${config.loadingText}`);
    } else {
      Utils.disable(btn, false);
      $btn.html(`<i class="${config.normalIcon}"></i>${config.normalText}`);
    }
  },

  /**
   * Set the text content or value of an element
   * If the element is a <select>, set its value; otherwise, set its text content.
   * @param {string} selector - Selector string
   * @param {string} text - Text to set
   */
  setElementText(selector, text) {
    const $el = $(selector);
    if ($el.is('select')) {
      $el.val(text).trigger('change');
    } else {
      $el.text(text);
    }
  },

  /**
   * Disable or enable a button
   * @param {string} btn - Selector string for the button
   * @param {boolean} disabled - true to disable, false to enable
   */
  disableButton(btn, disabled = true) {
    Utils.disable(btn, disabled);
  },

  /**
   * Disable or enable an element (generic utility)
   * @param {string} el - Selector string for the element
   * @param {boolean} disabled - true to disable, false to enable
   */
  disable(el, disabled = true) {
    const $el = $(el);
    $el.prop('disabled', !!disabled);
  },

  /**
   * Replace route parameter with actual ID
   * @param {string} route - Route with placeholder
   * @param {string|number} id - ID to replace placeholder with
   * @returns {string} - Route with ID replaced
   */
  replaceRouteId(route, id) {
    return route.replace(':id', id);
  },

  /**
   * Debounce function to limit function calls
   * @param {Function} func - Function to debounce
   * @param {number} wait - Wait time in milliseconds
   * @returns {Function} - Debounced function
   */
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Format validation errors for display
   * @param {Object} errors - Validation errors object
   * @returns {string} - Formatted HTML string
   */
  formatValidationErrors(errors) {
    let html = '<ul class="mb-0">';
    for (let key in errors) {
      if (Array.isArray(errors[key])) {
        errors[key].forEach(err => {
          html += `<li>${err}</li>`;
        });
      } else {
        html += `<li>${errors[key]}</li>`;
      }
    }
    html += '</ul>';
    return html;
  },

  /**
   * Handle AJAX error responses
   * @param {Object} xhr - XMLHttpRequest object
   * @param {string} defaultMessage - Default error message
   */
  handleAjaxError(xhr, defaultMessage = 'An error occurred. Please try again.') {
    let msg = xhr.responseJSON?.message || defaultMessage;
    let errors = xhr.responseJSON?.errors || {};
    
    let html = `<strong>${msg}</strong>`;
    if (Object.keys(errors).length > 0) {
      html += this.formatValidationErrors(errors);
    }
    
    this.showErrorHtml('Error', html);
  },

  /**
   * Show confirmation dialog
   * @param {Object} options - Configuration options
   * @returns {Promise} - Promise that resolves with user's choice
   */
  showConfirmDialog(options = {}) {
    const defaults = {
      title: 'Are you sure?',
      text: 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, proceed!',
      cancelButtonText: 'Cancel'
    };
    
    const config = { ...defaults, ...options };
    return Swal.fire(config);
  },

  /**
   * Validate form field
   * @param {string} field - Field selector
   * @param {string} message - Validation message
   * @param {boolean} isValid - Whether field is valid
   */
  validateField(field, message, isValid) {
    const $field = $(field);
    const $feedback = $field.siblings('.invalid-feedback');
    
    if (isValid) {
      $field.removeClass('is-invalid').addClass('is-valid');
      $feedback.text('');
    } else {
      $field.removeClass('is-valid').addClass('is-invalid');
      if ($feedback.length === 0) {
        $field.after(`<div class="invalid-feedback">${message}</div>`);
      } else {
        $feedback.text(message);
      }
    }
  },

  /**
   * Clear validation states from form
  * @param {string} form - Form selector
   */
  clearValidation(form) {
    const $form = $(form);
    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    $form.find('.invalid-feedback').text('');
  },

  /**
   * Check if value is empty
   * @param {*} value - Value to check
   * @returns {boolean} - Whether value is empty
   */
  isEmpty(value) {
    if (value === null || value === undefined) return true;
    if (typeof value === 'string') return value.trim() === '';
    if (Array.isArray(value)) return value.length === 0;
    if (typeof value === 'object') return Object.keys(value).length === 0;
    return false;
  },

  /**
   * Redirect after delay
   * @param {string} url - URL to redirect to
   * @param {number} delay - Delay in milliseconds
   */
  redirectAfterDelay(url, delay = 2000) {
    setTimeout(() => {
      window.location.href = url;
    }, delay);
  },

  /**
   * Get URL parameter value
   * @param {string} name - Parameter name
   * @returns {string|null} - Parameter value or null
   */
  getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  },

  /**
   * Generic Statistics Manager
   * Creates reusable stats management functionality with optional sub-statistics support
   * @param {Object} config - Configuration object
   * @returns {Object} - Stats manager instance
   */
  createStatsManager(config) {
    const defaults = {
      apiMethod: null,           // Required: API method to call (e.g., ApiService.fetchTermStats)
      statsKeys: [],             // Required: Array of stat keys (e.g., ['terms', 'active', 'inactive'])
      subStatsConfig: {},        // Optional: Object mapping stat keys to their sub-stats config
      onError: 'Failed to load statistics', // Error message
      dataPath: 'data',          // Path to data in response (default: response.data)
      successCheck: (response) => response.success !== false // Function to check if response is successful
    };

    const settings = { ...defaults, ...config };

    if (!settings.apiMethod || !Array.isArray(settings.statsKeys) || settings.statsKeys.length === 0) {
      console.error('StatsManager: apiMethod and statsKeys are required');
      return null;
    }

    return {
      /**
       * Initialize statistics cards
       */
      init() {
        this.load();
      },

      /**
       * Load statistics data
       */
      load() {
        this.toggleAllLoadingStates(true);
        settings.apiMethod()
          .done(this.handleSuccess.bind(this))
          .fail(this.handleError.bind(this))
          .always(() => this.toggleAllLoadingStates(false));
      },

      /**
       * Handle successful stats fetch
       * @param {Object} response - API response
       */
      handleSuccess(response) {
        if (settings.successCheck(response)) {
          const stats = this.getStatsData(response);
          this.updateAllStats(stats);
        } else {
          this.setAllStatsToNA();
        }
      },

      /**
       * Extract stats data from response
       * @param {Object} response - API response
       * @returns {Object} - Stats data
       */
      getStatsData(response) {
        const pathParts = settings.dataPath.split('.');
        let data = response;
        for (const part of pathParts) {
          data = data[part];
          if (!data) break;
        }
        return data || {};
      },

      /**
       * Update all stat elements
       * @param {Object} stats - Stats data object
       */
      updateAllStats(stats) {
        settings.statsKeys.forEach(key => {
          const statData = stats[key];
          if (statData) {
            // Handle different stat data structures
            let value, lastUpdateTime;
            
            if (typeof statData === 'object') {
              value = statData.count ?? statData.value ?? statData.title ?? statData;
              lastUpdateTime = statData.lastUpdateTime ?? statData.updated_at ?? '--';
            } else {
              value = statData;
              lastUpdateTime = '--';
            }
            
            this.updateStatElement(key, value, lastUpdateTime);
            
            // Update sub-stats if configured
            if (settings.subStatsConfig[key]) {
              this.updateSubStats(key, statData);
            }
          } else {
            this.updateStatElement(key, 'N/A', 'N/A');
            
            // Set sub-stats to N/A if configured
            if (settings.subStatsConfig[key]) {
              this.setSubStatsToNA(key);
            }
          }
        });
      },

      /**
       * Update sub-statistics for a given stat key
       * @param {string} statKey - Main stat key
       * @param {Object} statData - Main stat data object
       */
      updateSubStats(statKey, statData) {
        const subStatsKeys = settings.subStatsConfig[statKey];
        if (!subStatsKeys || !Array.isArray(subStatsKeys)) return;

        subStatsKeys.forEach(subStatKey => {
          let subValue = 'N/A';
          
          // Try to extract sub-stat value from different possible paths
          if (statData && typeof statData === 'object') {
            subValue = statData[subStatKey] ?? 
                      statData.subStats?.[subStatKey] ?? 
                      statData.breakdown?.[subStatKey] ?? 
                      statData.details?.[subStatKey] ?? 
                      'N/A';
          }
          
          this.updateSubStatElement(statKey, subStatKey, subValue);
        });
      },

      /**
       * Set sub-statistics to N/A for a given stat key
       * @param {string} statKey - Main stat key
       */
      setSubStatsToNA(statKey) {
        const subStatsKeys = settings.subStatsConfig[statKey];
        if (!subStatsKeys || !Array.isArray(subStatsKeys)) return;

        subStatsKeys.forEach(subStatKey => {
          this.updateSubStatElement(statKey, subStatKey, 'N/A');
        });
      },

      /**
       * Update a single sub-stat element
       * @param {string} statKey - Main stat key
       * @param {string} subStatKey - Sub-stat key
       * @param {string|number} value - Sub-stat value
       */
      updateSubStatElement(statKey, subStatKey, value) {
        Utils.setElementText(`#${statKey}-${subStatKey}-value`, value ?? 'N/A');
      },

      /**
       * Handle error in stats fetch
       */
      handleError() {
        this.setAllStatsToNA();
        Utils.showError(settings.onError);
      },

      /**
       * Update a single stat card
       * @param {string} elementId - Element ID
       * @param {string|number} value - Stat value
       * @param {string} lastUpdateTime - Last update time
       */
      updateStatElement(elementId, value, lastUpdateTime) {
        Utils.setElementText(`#${elementId}-value`, value ?? '0');
        Utils.setElementText(`#${elementId}-last-updated`, lastUpdateTime ?? '--');
      },

      /**
       * Set all stat cards to N/A
       */
      setAllStatsToNA() {
        settings.statsKeys.forEach(elementId => {
          Utils.setElementText(`#${elementId}-value`, 'N/A');
          Utils.setElementText(`#${elementId}-last-updated`, 'N/A');
          
          // Set sub-stats to N/A if configured
          if (settings.subStatsConfig[elementId]) {
            this.setSubStatsToNA(elementId);
          }
        });
      },

      /**
       * Toggle loading state for a single stat card
       * @param {string} elementId - Element ID
       * @param {boolean} isLoading - Loading state
       */
      toggleLoadingState(elementId, isLoading) {
        const $value = $(`#${elementId}-value`);
        const $loader = $(`#${elementId}-loader`);
        const $updated = $(`#${elementId}-last-updated`);
        const $updatedLoader = $(`#${elementId}-last-updated-loader`);

        if (isLoading) {
          $value.addClass('d-none');
          $loader.removeClass('d-none');
          $updated.addClass('d-none');
          $updatedLoader.removeClass('d-none');
        } else {
          $value.removeClass('d-none');
          $loader.addClass('d-none');
          $updated.removeClass('d-none');
          $updatedLoader.addClass('d-none');
        }

        // Toggle sub-stats loading states if configured
        if (settings.subStatsConfig[elementId]) {
          this.toggleSubStatsLoadingState(elementId, isLoading);
        }
      },

      /**
       * Toggle loading state for sub-statistics
       * @param {string} statKey - Main stat key
       * @param {boolean} isLoading - Loading state
       */
      toggleSubStatsLoadingState(statKey, isLoading) {
        const subStatsKeys = settings.subStatsConfig[statKey];
        if (!subStatsKeys || !Array.isArray(subStatsKeys)) return;

        subStatsKeys.forEach(subStatKey => {
          const $subValue = $(`#${statKey}-${subStatKey}-value`);
          const $subLoader = $(`#${statKey}-${subStatKey}-loader`);

          if (isLoading) {
            $subValue.addClass('d-none');
            $subLoader.removeClass('d-none');
          } else {
            $subValue.removeClass('d-none');
            $subLoader.addClass('d-none');
          }
        });
      },

      /**
       * Toggle loading state for all stat cards
       * @param {boolean} isLoading - Loading state
       */
      toggleAllLoadingStates(isLoading) {
        settings.statsKeys.forEach(elementId => {
          this.toggleLoadingState(elementId, isLoading);
        });
      },

      /**
       * Refresh stats (alias for load)
       */
      refresh() {
        this.load();
      },

      /**
       * Get current configuration
       * @returns {Object} - Current settings
       */
      getConfig() {
        return { ...settings };
      },

      /**
       * Update sub-stats configuration dynamically
       * @param {Object} newSubStatsConfig - New sub-stats configuration
       */
      updateSubStatsConfig(newSubStatsConfig) {
        settings.subStatsConfig = { ...settings.subStatsConfig, ...newSubStatsConfig };
      }
    };
  },

  /**
   * Populate a <select> element with options.
   * @param {string} select - Selector string for the select element
   * @param {Array} items - Array of items to populate. Each item can be an object or a string/number.
   * @param {Object} options - Optional config:
   *   - valueField: property name for option value (default: 'id')
   *   - textField: property name for option text (default: 'name')
   *   - placeholder: placeholder text for the first option (default: 'Select')
   *   - selected: value to be selected by default
   *   - includePlaceholder: whether to include placeholder (default: true)
   * @param {boolean} isSelect2 - true if select2 is used, false for normal select (default: false)
   */
  populateSelect(select, items, options = {}, isSelect2 = false) {
    // Always expect a selector string for select
    const $select = $(select);

    const {
      valueField = 'id',
      textField = 'name',
      placeholder = 'Select',
      selected = null,
      includePlaceholder = true
    } = options;

    let html = '';
    if (includePlaceholder) {
      html += `<option value="">${placeholder}</option>`;
    }

    items.forEach(item => {
      let value, text;
      if (typeof item === 'object') {
        value = item[valueField];
        text = item[textField];
      } else {
        value = item;
        text = item;
      }
      html += `<option value="${value}"${selected != null && value == selected ? ' selected' : ''}>${text}</option>`;
    });

    $select.html(html);

    // If select2 is requested, trigger the appropriate event
    if (isSelect2) {
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.trigger('change.select2');
      } else {
        $select.trigger('change');
      }
    } else {
      $select.trigger('change');
    }
  },

  /**
   * Initialize select2 on a select element with optional config.
   * @param {string} select - Selector string for the select element
   * @param {Object} options - select2 options (optional)
   */
  initSelect2(select, options = {}) {
    const $select = $(select);
    if ($select.length > 0) {

      // If select2 is already applied, destroy it first
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
      }

      $select.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder:  options.placeholder,
        allowClear: true,
        dropdownParent: options.dropdownParent,
        ...options
      });
    }
  },

  /**
   * Reload DataTable if it exists
   * @param {string} tableSelector - Table selector 
   * @param {Function|null} callback - Callback function after reload (optional)
   * @param {boolean} resetPaging - Whether to reset paging (default: false)
   * @param {boolean} holdPosition - Whether to hold current position (default: true)
   * @returns {boolean} - Returns true if table was reloaded, false if not a DataTable
   */
  reloadDataTable(tableSelector, callback = null, resetPaging = false, holdPosition = true) {
    try {
      let $table = $(tableSelector);

      // Check if element exists and is a DataTable
      if ($table.length === 0) {
        console.warn('DataTable reload: Table element not found');
        return false;
      }

      if (!$.fn.DataTable.isDataTable($table)) {
        console.warn('DataTable reload: Element is not a DataTable');
        return false;
      }

      // Reload the DataTable
      $table.DataTable().ajax.reload(callback, !resetPaging);
      return true;

    } catch (error) {
      console.error('DataTable reload error:', error);
      return false;
    }
  },

  /**
   * Check if element is a DataTable
   * @param {string} tableSelector - Table selector (string)
   * @returns {boolean} - Whether element is a DataTable
   */
  isDataTable(tableSelector) {
    try {
      let $table = $(tableSelector);
      return $table.length > 0 && $.fn.DataTable.isDataTable($table);
    } catch (error) {
      return false;
    }
  },

  /**
   * Destroy DataTable if it exists
   * @param {string} tableSelector - Table selector (string)
   * @param {boolean} remove - Whether to remove from DOM (default: false)
   * @returns {boolean} - Whether DataTable was destroyed
   */
  destroyDataTable(tableSelector, remove = false) {
    try {
      let $table = $(tableSelector);

      if ($table.length > 0 && $.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy(remove);
        return true;
      }

      return false;
    } catch (error) {
      console.error('DataTable destroy error:', error);
      return false;
    }
  },

};