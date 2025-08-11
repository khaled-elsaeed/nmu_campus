<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                4
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Parent Information') }}</div>
            <div class="text-muted">
                {{ __('Please provide your parent or guardian\'s details as they appear on official documents.') }}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="parent-relationship" class="form-label">{{ __('Relationship') }}</label>
        <select class="form-select" id="parent-relationship" name="parent_relationship">
            <option value="">{{ __('Select Relationship') }}</option>
            <option value="father">{{ __('Father') }}</option>
            <option value="mother">{{ __('Mother') }}</option>
        </select>
    </div>
     <div class="col-md-6 mb-3">
        <label for="parent-national-id" class="form-label">{{ __('National ID') }}</label>
        <input 
            type="text" 
            class="form-control" 
            id="parent-national-id" 
            name="parent_national_id"
        >
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="parent-name-ar" class="form-label">{{ __('Name (Arabic)') }}</label>
            <input 
                type="text" 
                class="form-control" 
                id="parent-name-ar" 
                name="parent_name_ar"
            >
        </div>
        <div class="col-md-6 mb-3">
            <label for="parent-name-en" class="form-label">{{ __('Name (English)') }}</label>
            <input 
                type="text" 
                class="form-control" 
                id="parent-name-en" 
                name="parent_name_en"
            >
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="is-parent-abroad" class="form-label">{{ __('Is the parent abroad?') }}</label>
        <select class="form-select" id="is-parent-abroad" name="is_parent_abroad">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
    </div>
    <div class="col-md-6 mb-3 d-none" id="parent-abroad-country-div">
        <label for="parent-abroad-country" class="form-label">{{ __('Country') }}</label>
        <select class="form-select" id="parent-abroad-country" name="parent_abroad_country">
            {{-- Optionally populate countries dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3 d-none" id="living-with-parent-div">
        <label for="living-with-parent" class="form-label">{{ __('Do you live with them?') }}</label>
        <select class="form-select" id="living-with-parent" name="living_with_parent">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="parent-phone" class="form-label">{{ __('Phone Number') }}</label>
        <input 
            type="tel" 
            class="form-control" 
            id="parent-phone" 
            name="parent_phone" 
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="parent-email" class="form-label">{{ __('Email (optional)') }}</label>
        <input 
            type="email" 
            class="form-control" 
            id="parent-email" 
            name="parent_email" 
        >
    </div>
</div>


<div class="row d-none" id="parent-address-div">
    <div class="col-md-6 mb-3">
        <label for="parent-governorate" class="form-label">{{ __('Parent\'s Governorate') }}</label>
        <select class="form-select" id="parent-governorate" name="parent_governorate">
            {{-- Optionally populate governorates dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="parent-city" class="form-label">{{ __('Parent\'s City') }}</label>
        <select class="form-select" id="parent-city" name="parent_city">
            {{-- Optionally populate cities dynamically via JS --}}
        </select>
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