<h5 class="mb-4"><i class='bx bx-check-shield me-2'></i>Terms and Conditions</h5>
<div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="termsCheckbox" name="termsCheckbox" {{ old('termsCheckbox') ? 'checked' : '' }} >
        <label class="form-check-label" for="termsCheckbox">
            I agree to the <a href="#" target="_blank">terms and conditions</a>.
        </label>
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary" id="backToStep6">
        <i class='bx bx-chevron-left'></i> Previous
    </button>
    <button type="submit" class="btn btn-success">
        <i class='bx bx-check me-2'></i>Complete Profile
    </button>
</div> 