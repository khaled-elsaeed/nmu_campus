<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                7                                
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Terms and Conditions') }}</div>
            <div class="text-muted">{{ __('Please review and accept the terms and conditions to continue.') }}</div>
        </div>
    </div>
</div>
<div class="row">
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="terms-checkbox" name="terms_checkbox" >
        <label class="form-check-label" for="terms-checkbox">
            {{ __('I agree to the') }} <a href="#" target="_blank">{{ __('terms and conditions') }}</a>.
        </label>
    </div>
</div>
</div>
<div class="d-flex justify-content-between">
<button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-left'></i> {{ __('Previous') }}
    </button>
    <button type="submit" class="btn btn-success">
        <i class='bx bx-check me-2'></i>{{ __('Complete Profile') }}
    </button>
</div>