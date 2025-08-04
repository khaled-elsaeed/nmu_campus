<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                1
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">Personal Information</div>
            <div class="text-muted">Please provide your personal details as they appear on your official documents.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name-ar" class="form-label">Full Name (Arabic)</label>
        <input type="text" class="form-control" id="name-ar" name="name_ar" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="name-en" class="form-label">Full Name (English)</label>
        <input type="text" class="form-control" id="name-en" name="name_en" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="national-id" class="form-label">National ID</label>
        <input type="text" class="form-control" id="national-id" name="national_id" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="birth-date" class="form-label">Birth Date</label>
        <input type="text" class="form-control" id="birth-date" name="birth_date" placeholder="DD/MM/YYYY" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" id="gender" name="gender" disabled>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="nationality" class="form-label">Nationality</label>
        <select class="form-select" id="nationality" name="nationality">
            <option value="">Select Nationality</option>
        </select>
    </div>
</div>
<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-primary next-Btn">
        Next <i class='bx bx-chevron-right'></i>
    </button>
</div> 