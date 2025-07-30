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
        <label for="hasSiblingInDorm" class="form-label">Do you have a sibling in the dorm?</label>
        <select class="form-select" id="hasSiblingInDorm" name="hasSiblingInDorm">
            <option value="">Select</option>
            <option value="yes" {{ old('hasSiblingInDorm') == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('hasSiblingInDorm') == 'no' ? 'selected' : '' }}>No</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
<div id="siblingDetails" class="d-none">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="siblingGender" class="form-label">Relationship</label>
            <select class="form-select" id="siblingGender" name="siblingGender">
                <option value="">Select</option>
                <option value="male" {{ old('siblingGender') == 'male' ? 'selected' : '' }}>Brother</option>
                <option value="female" {{ old('siblingGender') == 'female' ? 'selected' : '' }}>Sister</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="siblingName" class="form-label">Name</label>
            <input type="text" class="form-control" id="siblingName" name="siblingName" value="{{ old('siblingName') }}">
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="siblingNationalId" class="form-label">National ID</label>
            <input type="text" class="form-control" id="siblingNationalId" name="siblingNationalId" value="{{ old('siblingNationalId') }}">
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="siblingFaculty" class="form-label">Faculty</label>
            <select class="form-select" id="siblingFaculty" name="siblingFaculty">
                <option value="">Select Faculty</option>
                {{-- @foreach($faculties as $faculty) --}}
                {{-- <option value="{{ $faculty->id }}">{{ $faculty->name }}</option> --}}
                {{-- @endforeach --}}
            </select>
            <div class="invalid-feedback"></div>
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