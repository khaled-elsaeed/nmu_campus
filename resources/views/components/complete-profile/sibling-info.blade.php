<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                5
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Sibling Information') }}</div>
            <div class="text-muted">{{ __('Please provide details about your sibling in the dorm, if applicable.') }}</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="has-sibling-in-dorm" class="form-label">{{ __('Do you have a sibling in the dorm?') }}</label>
        <select class="form-select" id="has-sibling-in-dorm" name="has_sibling_in_dorm">
            <option value="">{{ __('Select') }}</option>
            <option value="yes">{{ __('Yes') }}</option>
            <option value="no">{{ __('No') }}</option>
        </select>
    </div>
</div>
<div id="siblingDetails" class="d-none">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-gender" class="form-label">{{ __('Relationship') }}</label>
            <select class="form-select" id="sibling-gender" name="sibling_gender">
                <option value="">{{ __('Select') }}</option>
                <option value="male">{{ __('Brother') }}</option>
                <option value="female">{{ __('Sister') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="sibling-national-id" class="form-label">{{ __('National ID') }}</label>
            <input type="text" class="form-control" id="sibling-national-id" name="sibling_national_id">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-name-ar" class="form-label">{{ __('Name (Arabic)') }}</label>
            <input type="text" class="form-control" id="sibling-name-ar" name="sibling_name_ar">
        </div>
        <div class="col-md-6 mb-3">
            <label for="sibling-name-en" class="form-label">{{ __('Name (English)') }}</label>
            <input type="text" class="form-control" id="sibling-name-en" name="sibling_name_en">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-faculty" class="form-label">{{ __('Faculty') }}</label>
            <select class="form-select" id="sibling-faculty" name="sibling_faculty">
                <option value="">{{ __('Select Faculty') }}</option>
            </select>
        </div>
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