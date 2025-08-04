<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                2
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">Contact Information</div>
            <div class="text-muted">Please provide your contact details so we can reach you if needed.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="governorate" class="form-label">Governorate <span class="text-danger">*</span></label>
        <select class="form-select" id="governorate" name="governorate" >
            {{-- Optionally populate governorates dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
        <select class="form-select" id="city" name="city" >
        {{-- Optionally populate cities dynamically via JS --}}
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="street" class="form-label">Street</label>
    <input type="text" class="form-control" id="street" name="street">
    <div class="invalid-feedback"></div>
</div>
<div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" id="phone" name="phone"">
    <div class="invalid-feedback"></div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-left'></i> Previous
    </button>
    <button type="button" class="btn btn-primary next-Btn">
        Next <i class='bx bx-chevron-right'></i>
    </button>
</div> 