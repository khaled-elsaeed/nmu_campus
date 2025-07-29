<h5 class="mb-4"><i class='bx bx-user-voice me-2'></i>Parent Information</h5>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="parentRelationship" class="form-label">Relationship</label>
        <select class="form-select" id="parentRelationship" name="parentRelationship">
            <option value="">Select Relationship</option>
            <option value="father" {{ old('parentRelationship') == 'father' ? 'selected' : '' }}>Father</option>
            <option value="mother" {{ old('parentRelationship') == 'mother' ? 'selected' : '' }}>Mother</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="parentName" class="form-label">Name</label>
        <input type="text" class="form-control" id="parentName" name="parentName" value="{{ old('parentName') }}">
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="parentPhone" class="form-label">Phone Number</label>
        <input type="tel" class="form-control" id="parentPhone" name="parentPhone" value="{{ old('parentPhone') }}">
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="parentEmail" class="form-label">Email (optional)</label>
        <input type="email" class="form-control" id="parentEmail" name="parentEmail" value="{{ old('parentEmail') }}">
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="isParentAbroad" class="form-label">Is the parent abroad?</label>
        <select class="form-select" id="isParentAbroad" name="isParentAbroad">
            <option value="">Select</option>
            <option value="yes" {{ old('isParentAbroad') == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('isParentAbroad') == 'no' ? 'selected' : '' }}>No</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3 d-none" id="abroadCountryDiv">
        <label for="abroadCountry" class="form-label">Country</label>
        <select class="form-select" id="abroadCountry" name="abroadCountry">
            <option value="">Select Country</option>
            {{-- @foreach($countries as $country) --}}
            {{-- <option value="{{ $country->id }}">{{ $country->name }}</option> --}}
            {{-- @endforeach --}}
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="row d-none" id="livingWithParentDiv">
    <div class="col-md-6 mb-3">
        <label for="livingWithParent" class="form-label">Do you live with them?</label>
        <select class="form-select" id="livingWithParent" name="livingWithParent">
            <option value="">Select</option>
            <option value="yes" {{ old('livingWithParent') == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('livingWithParent') == 'no' ? 'selected' : '' }}>No</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
</div>
<div class="row d-none" id="parentAddressDiv">
    <div class="col-md-6 mb-3">
        <label for="parentGovernorate" class="form-label">Parent's Governorate</label>
        <select class="form-select" id="parentGovernorate" name="parentGovernorate">
            <option value="">Select Governorate</option>
            {{-- @foreach($governorates as $gov) --}}
            {{-- <option value="{{ $gov->id }}">{{ $gov->name }}</option> --}}
            {{-- @endforeach --}}
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="parentCity" class="form-label">Parent's City</label>
        <select class="form-select" id="parentCity" name="parentCity">
            <option value="">Select City</option>
        </select>
        <div class="invalid-feedback"></div>
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