<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                5
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">Sibling Information</div>
            <div class="text-muted">Please provide details about your sibling in the dorm, if applicable.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="has-sibling-in-dorm" class="form-label">Do you have a sibling in the dorm?</label>
        <select class="form-select" id="has-sibling-in-dorm" name="has_sibling_in_dorm">
            <option value="">Select</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    </div>
</div>
<div id="siblingDetails" class="d-none">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-gender" class="form-label">Relationship</label>
            <select class="form-select" id="sibling-gender" name="sibling_gender">
                <option value="">Select</option>
                <option value="male">Brother</option>
                <option value="female">Sister</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="sibling-name-ar" class="form-label">Name (Arabic)</label>
            <input type="text" class="form-control" id="sibling-name-ar" name="sibling_name_ar">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-name-en" class="form-label">Name (English)</label>
            <input type="text" class="form-control" id="sibling-name-en" name="sibling_name_en">
        </div>
        <div class="col-md-6 mb-3">
            <label for="sibling-national-id" class="form-label">National ID</label>
            <input type="text" class="form-control" id="sibling-national-id" name="sibling_national_id">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="sibling-faculty" class="form-label">Faculty</label>
            <select class="form-select" id="sibling-faculty" name="sibling_faculty">
                <option value="">Select Faculty</option>
            </select>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-left'></i> Previous
    </button>
    <button type="button" class="btn btn-primary next-Btn">
        Next <i class='bx bx-chevron-right'></i>
    </button>
</div> 