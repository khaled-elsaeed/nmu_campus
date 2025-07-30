<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                6
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-danger mb-1">Emergency Contact Information</div>
            <div class="text-muted">Please provide details for someone we can contact in case of emergency.</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="emergencyContactRelationship" class="form-label">Relationship</label>
        <select class="form-select" id="emergencyContactRelationship" name="emergencyContactRelationship">
            <option value="">Select</option>
            <option value="father" {{ old('emergencyContactRelationship') == 'father' ? 'selected' : '' }}>Father</option>
            <option value="mother" {{ old('emergencyContactRelationship') == 'mother' ? 'selected' : '' }}>Mother</option>
            <option value="brother" {{ old('emergencyContactRelationship') == 'brother' ? 'selected' : '' }}>Brother</option>
            <option value="sister" {{ old('emergencyContactRelationship') == 'sister' ? 'selected' : '' }}>Sister</option>
            <option value="other" {{ old('emergencyContactRelationship') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="emergencyContactName" class="form-label">Name</label>
        <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" value="{{ old('emergencyContactName') }}">
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="mb-3">
    <label for="emergencyContactPhone" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" id="emergencyContactPhone" name="emergencyContactPhone" value="{{ old('emergencyContactPhone') }}">
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