<h5 class="mb-4"><i class='bx bx-group me-2'></i>Sibling Information</h5>
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
                <option value="brother" {{ old('siblingGender') == 'brother' ? 'selected' : '' }}>Brother</option>
                <option value="sister" {{ old('siblingGender') == 'sister' ? 'selected' : '' }}>Sister</option>
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