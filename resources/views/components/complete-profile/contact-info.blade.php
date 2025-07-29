<h5 class="mb-4"><i class='bx bx-phone me-2'></i>Contact Information</h5>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="governorate" class="form-label">Governorate <span class="text-danger">*</span></label>
        <select class="form-select" id="governorate" name="governorate" >
            <option value="">Select Governorate</option>
            {{-- @foreach($governorates as $gov) --}}
            {{-- <option value="{{ $gov->id }}">{{ $gov->name }}</option> --}}
            {{-- @endforeach --}}
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
        <select class="form-select" id="city" name="city" >
            <option value="">Select City</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="mb-3">
    <label for="street" class="form-label">Street</label>
    <input type="text" class="form-control" id="street" name="street" value="{{ old('street', Auth::user()->street ?? '') }}">
    <div class="invalid-feedback"></div>
</div>
<div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone ?? '') }}">
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