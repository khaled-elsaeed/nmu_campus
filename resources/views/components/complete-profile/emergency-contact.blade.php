<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                6
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">Emergency Contact Information</div>
            <div class="text-muted">Please provide details for someone we can contact in case of emergency.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="emergency-contact-relationship" class="form-label">Relationship</label>
        <select class="form-select" id="emergency-contact-relationship" name="emergency_contact_relationship">
            <option value="">Select</option>
            <option value="father">Father</option>
            <option value="mother">Mother</option>
            <option value="brother">Brother</option>
            <option value="sister">Sister</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="emergency-contact-name" class="form-label">Name</label>
        <input type="text" class="form-control" id="emergency-contact-name" name="emergency_contact_name">
    </div>
</div>
<div class="mb-3">
    <label for="emergency-contact-phone" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" id="emergency-contact-phone" name="emergency_contact_phone">
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