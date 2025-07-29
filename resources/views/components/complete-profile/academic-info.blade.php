<h5 class="mb-4"><i class='bx bx-book me-2'></i>Academic Information</h5>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="faculty" class="form-label">Faculty</label>
        <select class="form-select" id="faculty" name="faculty">
            <option value="">Select Faculty</option>
            {{-- @foreach($faculties as $faculty) --}}
            {{-- <option value="{{ $faculty->id }}">{{ $faculty->name }}</option> --}}
            {{-- @endforeach --}}
        </select>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="program" class="form-label">Program</label>
        <select class="form-select" id="program" name="program">
            <option value="">Select Program</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="academicId" class="form-label">University ID</label>
        <input type="text" class="form-control" id="academicId" name="academicId" value="{{ old('academicId', Auth::user()->academic_id ?? '') }}" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label for="academicEmail" class="form-label">University Email</label>
        <input type="email" class="form-control" id="academicEmail" name="academicEmail" value="{{ old('academicEmail', Auth::user()->academic_email ?? '') }}" readonly>
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