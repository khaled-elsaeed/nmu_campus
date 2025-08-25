<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                1
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Personal Information') }}</div>
            <div class="text-muted">{{ __('Please provide your personal details as they appear on your official documents.') }}</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name-ar" class="form-label">{{ __('Full Name (Arabic)') }}</label>
        <input type="text" class="form-control" id="name-ar" name="name_ar" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="name-en" class="form-label">{{ __('Full Name (English)') }}</label>
        <input type="text" class="form-control" id="name-en" name="name_en" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="national-id" class="form-label">{{ __('National ID') }}</label>
        <input type="text" class="form-control" id="national-id" name="national_id" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="birth-date" class="form-label">{{ __('Birth Date') }}</label>
        <input type="text" class="form-control" id="birth-date" name="birth_date" placeholder="{{ __('DD/MM/YYYY') }}" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="gender" class="form-label">{{ __('Gender') }}</label>
        <select class="form-select" id="gender" name="gender" disabled>
            <option value="">{{ __('Select Gender') }}</option>
            <option value="male">{{ __('Male') }}</option>
            <option value="female">{{ __('Female') }}</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="nationality" class="form-label">{{ __('Nationality') }}</label>
        <select class="form-select" id="nationality" name="nationality">
        </select>
    </div>
</div>
<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-primary next-Btn">
        {{ __('Next') }} <i class='bx bx-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}'></i>
    </button>
</div>