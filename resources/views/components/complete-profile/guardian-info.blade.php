<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                4
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Guardian Information') }}</div>
            <div class="text-muted">
                {{ __('Please provide your guardian or guardian\'s details as they appear on official documents.') }}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="guardian-relationship" class="form-label">{{ __('Relationship') }}</label>
        <select class="form-select" id="guardian-relationship" name="guardian_relationship">
            <option value="">{{ __('Select Relationship') }}</option>
            <option value="father">{{ __('Father') }}</option>
            <option value="mother">{{ __('Mother') }}</option>
        </select>
    </div>
     <div class="col-md-6 mb-3">
        <label for="guardian-national-id" class="form-label">{{ __('National ID') }}</label>
        <input 
            type="text" 
            class="form-control" 
            id="guardian-national-id" 
            name="guardian_national_id"
        >
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="guardian-name-ar" class="form-label">{{ __('Name (Arabic)') }}</label>
            <input 
                type="text" 
                class="form-control" 
                id="guardian-name-ar" 
                name="guardian_name_ar"
            >
        </div>
        <div class="col-md-6 mb-3">
            <label for="guardian-name-en" class="form-label">{{ __('Name (English)') }}</label>
            <input 
                type="text" 
                class="form-control" 
                id="guardian-name-en" 
                name="guardian_name_en"
            >
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="is-guardian-abroad" class="form-label">{{ __('Is the guardian abroad?') }}</label>
        <select class="form-select" id="is-guardian-abroad" name="is_guardian_abroad">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
    </div>
    <div class="col-md-6 mb-3 d-none" id="guardian-abroad-country-div">
        <label for="guardian-abroad-country" class="form-label">{{ __('Country') }}</label>
        <select class="form-select" id="guardian-abroad-country" name="guardian_abroad_country">
            {{-- Optionally populate countries dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3 d-none" id="living-with-guardian-div">
        <label for="living-with-guardian" class="form-label">{{ __('Do you live with them?') }}</label>
        <select class="form-select" id="living-with-guardian" name="living_with_guardian">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="guardian-phone" class="form-label">{{ __('Phone Number') }}</label>
        <input 
            type="tel" 
            class="form-control" 
            id="guardian-phone" 
            name="guardian_phone" 
        >
    </div>
    <div class="col-md-6 mb-3">
        <label for="guardian-email" class="form-label">{{ __('Email (optional)') }}</label>
        <input 
            type="email" 
            class="form-control" 
            id="guardian-email" 
            name="guardian_email" 
        >
    </div>
</div>


<div class="row d-none" id="guardian-address-div">
    <div class="col-md-6 mb-3">
        <label for="guardian-governorate" class="form-label">{{ __('Guardian\'s Governorate') }}</label>
        <select class="form-select" id="guardian-governorate" name="guardian_governorate">
            {{-- Optionally populate governorates dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="guardian-city" class="form-label">{{ __('Guardian\'s City') }}</label>
        <select class="form-select" id="guardian-city" name="guardian_city">
            {{-- Optionally populate cities dynamically via JS --}}
        </select>
    </div>
</div>

<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}'></i> {{ __('Previous') }}
    </button>
    <button type="button" class="btn btn-primary next-Btn">
        {{ __('Next') }} <i class='bx bx-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}'></i>
    </button>
</div>