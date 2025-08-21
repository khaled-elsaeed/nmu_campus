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
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    if (toast === true) {
      Swal.fire({
        toast: true,
        position: position || (isRtl ? 'top-start' : 'top-end'),
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1800,
        timerProgressBar: true
      });
    } else {
      Swal.fire({
        icon: 'success',
        title: isRtl ? 'نجاح' : 'Success',
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
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    var isModalOpen = $('.modal.show').length > 0;

    if (toast === true) {
      Swal.fire({
        toast: true,
        position: position || (isRtl ? 'top-start' : 'top-end'),
        icon: 'error',
        title: message,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: isRtl ? 'خطأ' : 'Error',
        text: message,
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
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    const $btn = $(btn);
    const defaults = {
      loadingText: isRtl ? 'جاري التحميل...' : 'Loading...',
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
  handleAjaxError(xhr, defaultMessage = null) {
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    if (!defaultMessage) {
      defaultMessage = isRtl ? 'حدث خطأ. يرجى المحاولة مرة أخرى.' : 'An error occurred. Please try again.';
    }
    let msg = xhr.responseJSON?.message || defaultMessage;
    let errors = xhr.responseJSON?.errors || {};
    
    let html = `<strong>${msg}</strong>`;
    if (Object.keys(errors).length > 0) {
      html += this.formatValidationErrors(errors);
    }
    
    let errorTitle = isRtl ? 'خطأ' : 'Error';

    this.showErrorHtml(errorTitle, html);
  },

  /**
   * Show confirmation dialog
   * @param {Object} options - Configuration options
   * @returns {Promise} - Promise that resolves with user's choice
   */
  showConfirmDialog(options = {}) {
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    const defaults = {
      title: isRtl ? 'هل أنت متأكد؟' : 'Are you sure?',
      text: isRtl ? 'لا يمكن التراجع عن هذا الإجراء.' : 'This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: isRtl ? 'نعم، تابع!' : 'Yes, proceed!',
      cancelButtonText: isRtl ? 'إلغاء' : 'Cancel'
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
    const isRtl = document.documentElement.getAttribute('lang') === 'ar';
    const defaults = {
      apiMethod: null,           // Required: API method to call (e.g., ApiService.fetchTermStats)
      statsKeys: [],             // Required: Array of stat keys (e.g., ['terms', 'active', 'inactive'])
      subStatsConfig: {},
      urlParams: {},              // Optional: Object mapping stat keys to their sub-stats config
      onError: isRtl ? 'فشل تحميل الإحصائيات' : 'Failed to load statistics', // Error message
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
            let params = typeof settings.urlParams === 'function' ? settings.urlParams() : settings.urlParams;
            settings.apiMethod(params)
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
      debounce: function(func, wait) {
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

  populateSelect(select, items, options = {}, isSelect2 = false) {
  const $select = $(select);

  const {
    valueField = 'id',
    textField = 'name',
    dataAttributes = {}, // mapping: { htmlAttrName: fieldNameInItem }
    placeholder = 'Select',
    selected = null,
    includePlaceholder = true,
    triggerChange = true
  } = options;

  let html = '';

  if (includePlaceholder) {
    html += `<option value="">${placeholder}</option>`;
  }

  items.forEach(item => {
    let value = typeof item === 'object' ? item[valueField] : item;
    let text  = typeof item === 'object' ? item[textField]  : item;

    // Build data-* attributes from mapping
    let dataAttrs = '';
    if (typeof item === 'object') {
      Object.entries(dataAttributes).forEach(([attrName, fieldName]) => {
        if (item[fieldName] !== undefined && item[fieldName] !== null) {
          // Ensure camelCase keys become kebab-case for HTML attributes
          const kebabAttr = attrName.replace(/([A-Z])/g, '-$1').toLowerCase();
          dataAttrs += ` data-${kebabAttr}="${item[fieldName]}"`;
        }
      });
    }

    html += `<option value="${value}"${dataAttrs}${selected != null && value == selected ? ' selected' : ''}>${text}</option>`;
  });

  $select.html(html);

  // Trigger appropriate change event
  if (triggerChange) {
    if (isSelect2) {
      if ($select.hasClass('select2-hidden-accessible')) {
        $select.trigger('change.select2');
      } else {
        $select.trigger('change');
      }
    } else {
      $select.trigger('change');
    }
  }
}
,

  getElementData(element, attributes = null) {
    const $element = $(element);
    if ($element.length === 0) return null;

    if (attributes && Array.isArray(attributes) && attributes.length === 1 && attributes[0] === 'id') {
      return $element.data('id');
    }

    const data = {};
    if (attributes && Array.isArray(attributes)) {
      attributes.forEach(attr => {
        data[attr] = $element.data(attr);
      });
    } else {
      $.each($element.data(), (key, value) => {
        data[key] = value;
      });
    }
    return data;
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

  /**
   * Reset form fields and clear validation states
   * @param {string} formId - Form ID (without #)
   */
  resetForm(formId) {
    const $form = $(`#${formId}`);
    if ($form.length === 0) return;

    // Reset all form fields
    $form[0].reset();

    // Reset any select2 fields
    $form.find('select').each(function() {
      if ($(this).hasClass('select2-hidden-accessible')) {
        $(this).val('').trigger('change.select2');
      }
    });
  },
  /**
 * Show/hide loader elements
 * @param {string} loaderId - Loader element ID (without #)
 * @param {boolean} show - Whether to show (true) or hide (false)
 */
toggleLoader(loaderId, show = true) {
  const $loader = $(`#${loaderId}`);
  if (show) {
    $loader.removeClass('d-none');
  } else {
    $loader.addClass('d-none');
  }
},

/**
 * Show loader
 * @param {string} loaderId - Loader element ID (without #)
 */
showLoader(loaderId) {
  this.toggleLoader(loaderId, true);
},

/**
 * Hide loader
 * @param {string} loaderId - Loader element ID (without #)
 */
hideLoader(loaderId) {
  this.toggleLoader(loaderId, false);
},

/**
 * Toggle no-data message visibility
 * @param {string} noDataId - No-data element ID (without #)
 * @param {boolean} show - Whether to show (true) or hide (false)
 */
toggleNoData(noDataId, show = true) {
  const $noData = $(`#${noDataId}`);
  if (show) {
    $noData.removeClass('d-none');
  } else {
    $noData.addClass('d-none');
  }
},

/**
 * Show no-data message
 * @param {string} noDataId - No-data element ID (without #)
 */
showNoData(noDataId) {
  this.toggleNoData(noDataId, true);
},

/**
 * Hide no-data message
 * @param {string} noDataId - No-data element ID (without #)
 */
hideNoData(noDataId) {
  this.toggleNoData(noDataId, false);
},

/**
 * Create a chart loading state manager for analytics
 * @param {Array} chartIds - Array of chart IDs to manage
 * @returns {Object} - Chart loading manager
 */
createChartLoadingManager(chartIds = []) {
  return {
    charts: chartIds,
    
    showAll() {
      this.charts.forEach(chartId => {
        Utils.showLoader(`${chartId}-loader`);
        Utils.hideNoData(`${chartId}-no-data`);
      });
    },
    
    hideAll() {
      this.charts.forEach(chartId => {
        Utils.hideLoader(`${chartId}-loader`);
      });
    },
    
    showNoDataFor(chartId) {
      Utils.hideLoader(`${chartId}-loader`);
      Utils.showNoData(`${chartId}-no-data`);
    },
    
    hideNoDataFor(chartId) {
      Utils.hideNoData(`${chartId}-no-data`);
    }
  };
},

/**
 * Enhanced filter manager helper
 * @param {Object} config - Configuration object
 * @returns {Object} - Filter manager instance
 */
createFilterManager(config = {}) {
  const defaults = {
    filterSelectors: [],
    onFilterChange: null,
    debounceTime: 300,
    clearButtonSelector: null
  };
  
  const settings = { ...defaults, ...config };
  let filterTimeout = null;
  let isLoading = false;
  
  return {
    init() {
      this.bindEvents();
    },
    
    bindEvents() {
      const self = this;
      
      // Bind filter change events
      settings.filterSelectors.forEach(selector => {
        $(selector).on('change', function() {
          if (isLoading) return;
          
          clearTimeout(filterTimeout);
          filterTimeout = setTimeout(() => {
            if (!isLoading && settings.onFilterChange) {
              settings.onFilterChange();
            }
          }, settings.debounceTime);
        });
      });
      
      // Bind clear button if provided
      if (settings.clearButtonSelector) {
        $(settings.clearButtonSelector).on('click', function() {
          if (!isLoading) {
            self.clearFilters();
          }
        });
      }
    },
    
    getFilters() {
      const filters = {};
      settings.filterSelectors.forEach(selector => {
        const $element = $(selector);
        const name = $element.attr('name') || $element.attr('id');
        if (name) {
          filters[name] = $element.val();
        }
      });
      return filters;
    },
    
    clearFilters() {
      isLoading = true;
      
      settings.filterSelectors.forEach(selector => {
        const $element = $(selector);
        if ($element.is('select')) {
          $element.val('');
          if ($element.hasClass('select2-hidden-accessible')) {
            $element.trigger('change.select2');
          }
        } else {
          $element.val('');
        }
      });
      
      setTimeout(() => {
        if (settings.onFilterChange) {
          settings.onFilterChange();
        }
      }, 100);
    },
    
    setLoadingState(loading) {
      isLoading = loading;
    },
    
    isLoading() {
      return isLoading;
    }
  };
},

/**
 * Format numbers with thousands separator
 * @param {number|string} num - Number to format
 * @param {string} locale - Locale for formatting (default: 'en-US')
 * @returns {string} - Formatted number
 */
formatNumber(num, locale = 'en-US') {
  if (num === null || num === undefined || num === 'N/A') return 'N/A';
  const number = typeof num === 'string' ? parseFloat(num) : num;
  if (isNaN(number)) return 'N/A';
  return new Intl.NumberFormat(locale).format(number);
},

/**
 * Format percentage
 * @param {number} value - Value to format as percentage
 * @param {number} total - Total value for percentage calculation
 * @param {number} decimals - Number of decimal places (default: 1)
 * @returns {string} - Formatted percentage
 */
formatPercentage(value, total, decimals = 1) {
  if (!value || !total || total === 0) return '0%';
  const percentage = (value / total * 100).toFixed(decimals);
  return `${percentage}%`;
},

/**
 * Create a simple analytics data processor
 * @param {Array} data - Raw data array
 * @param {Object} config - Configuration object
 * @returns {Object} - Processed data for charts
 */
processAnalyticsData(data, config = {}) {
  const {
    labelField = 'name',
    valueField = 'count',
    colors = ['#696cff', '#03c3ec', '#ffab00', '#71dd37', '#ff3e1d', '#8592a3'],
    translations = {}
  } = config;
  
  if (!data || !Array.isArray(data) || data.length === 0) {
    return null;
  }
  
  return {
    labels: data.map(item => {
      const label = item[labelField];
      return translations[label] || label;
    }),
    datasets: [{
      data: data.map(item => item[valueField] || 0),
      backgroundColor: colors.slice(0, data.length),
      borderColor: '#fff',
      borderWidth: 2
    }]
  };
},

/**
 * Safe element text setter that handles different element types
 * @param {string} selector - Element selector
 * @param {string|number} value - Value to set
 * @param {boolean} fallbackToNA - Whether to fallback to 'N/A' for empty values
 */
safeSetText(selector, value, fallbackToNA = true) {
  const $element = $(selector);
  if ($element.length === 0) return;
  
  let displayValue = value;
  if ((value === null || value === undefined || value === '') && fallbackToNA) {
    displayValue = 'N/A';
  }
  
  if ($element.is('input, textarea')) {
    $element.val(displayValue);
  } else if ($element.is('select')) {
    $element.val(displayValue).trigger('change');
  } else {
    $element.text(displayValue);
  }
}

};