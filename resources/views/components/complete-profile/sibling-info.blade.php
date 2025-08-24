<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                5
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Sibling Information') }}</div>
            <div class="text-muted">{{ __('Please provide details about your sibling(s) in the dorm, if applicable.') }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="has-sibling-in-dorm" class="form-label">{{ __('Do you have sibling(s) in the dorm?') }}</label>
        <select class="form-select" id="has-sibling-in-dorm" name="has_sibling_in_dorm">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
        
    </div>
</div>

<div id="siblingDetails" class="d-none">
    <!-- Siblings Container -->
    <div id="siblings-container">
        <!-- First sibling form (template) -->
        <div class="sibling-group border rounded p-3 mb-3" data-sibling-index="0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-secondary">{{ __('Sibling') }} <span class="sibling-number">1</span></h6>
                <button type="button" class="btn btn-outline-danger btn-sm remove-sibling-btn d-none">
                    <i class='bx bx-trash'></i> {{ __('Remove') }}
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-relationship-0" class="form-label">{{ __('Relationship') }}</label>
                    <select class="form-select sibling-relationship" id="sibling-relationship-0" name="siblings[0][relationship]" data-name="relationship">
                        <option value="">{{ __('Select') }}</option>
                        <option value="brother">{{ __('Brother') }}</option>
                        <option value="sister">{{ __('Sister') }}</option>
                    </select>
                    
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-national-id-0" class="form-label">{{ __('National ID') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control sibling-national-id" id="sibling-national-id-0" name="siblings[0][national_id]" maxlength="14" data-name="national_id" required>
                    
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-name-ar-0" class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control sibling-name-ar" id="sibling-name-ar-0" name="siblings[0][name_ar]" data-name="name_ar" required>
                    
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-name-en-0" class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control sibling-name-en" id="sibling-name-en-0" name="siblings[0][name_en]" data-name="name_en" required>
                    
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-faculty-0" class="form-label">{{ __('Faculty') }}</label>
                    <select class="form-select sibling-faculty" id="sibling-faculty-0" name="siblings[0][faculty]" data-name="faculty">
                        <option value="">{{ __('Select Faculty') }}</option>
                    </select>
                    
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="sibling-academic-level-0" class="form-label">{{ __('Academic Level') }}</label>
                    <select class="form-select sibling-academic-level" id="sibling-academic-level-0" name="siblings[0][academic_level]" data-name="academic_level">
                        <option value="">{{ __('Select Academic Level') }}</option>
                        <option value="0">{{ __('level 0 (For Engineering)') }}</option>
                        <option value="1">{{ __('level 1') }}</option>
                        <option value="2">{{ __('level 2') }}</option>
                        <option value="3">{{ __('level 3') }}</option>
                        <option value="4">{{ __('level 4') }}</option>
                        <option value="5">{{ __('level 5') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Sibling Button -->
    <div class="mb-3">
        <button type="button" class="btn btn-outline-primary" id="add-sibling-btn">
            <i class='bx bx-plus'></i> {{ __('Add Another Sibling') }}
        </button>
    </div>
</div>

<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-left'></i> {{ __('Previous') }}
    </button>
    <button type="button" class="btn btn-primary next-Btn">
        {{ __('Next') }} <i class='bx bx-chevron-right'></i>
    </button>
</div>

<!-- Hidden template for cloning new sibling forms -->
<template id="sibling-template">
    <div class="sibling-group border rounded p-3 mb-3" data-sibling-index="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-secondary">{{ __('Sibling') }} <span class="sibling-number"></span></h6>
            <button type="button" class="btn btn-outline-danger btn-sm remove-sibling-btn">
                <i class='bx bx-trash'></i> {{ __('Remove') }}
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('Relationship') }}</label>
                <select class="form-select sibling-relationship" name="" data-name="relationship">
                    <option value="">{{ __('Select') }}</option>
                    <option value="brother">{{ __('Brother') }}</option>
                    <option value="sister">{{ __('Sister') }}</option>
                </select>
                
            </div>
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('National ID') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control sibling-national-id" name="" maxlength="14" data-name="national_id" required>
                
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control sibling-name-ar" name="" data-name="name_ar" required>
                
            </div>
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control sibling-name-en" name="" data-name="name_en" required>
                
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('Faculty') }}</label>
                <select class="form-select sibling-faculty" name="" data-name="faculty">
                    <option value="">{{ __('Select Faculty') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 form-group">
                <label class="form-label">{{ __('Academic Level') }}</label>
                <select class="form-select sibling-academic-level" name="" data-name="academic_level">
                    <option value="">{{ __('Select Academic Level') }}</option>
                    <option value="0">{{ __('level 0 (For Engineering)') }}</option>
                    <option value="1">{{ __('level 1') }}</option>
                    <option value="2">{{ __('level 2') }}</option>
                    <option value="3">{{ __('level 3') }}</option>
                    <option value="4">{{ __('level 4') }}</option>
                    <option value="5">{{ __('level 5') }}</option>
                </select>
            </div>
        </div>
    </div>
</template>