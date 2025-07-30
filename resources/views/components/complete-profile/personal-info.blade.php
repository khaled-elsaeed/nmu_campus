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
        <label for="fullNameArabic" class="form-label">Full Name (Arabic)</label>
        <input type="text" class="form-control" id="fullNameArabic" name="fullNameArabic" value="{{ old('fullNameArabic', Auth::user()->name_ar ?? '') }}" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="fullNameEnglish" class="form-label">Full Name (English)</label>
        <input type="text" class="form-control" id="fullNameEnglish" name="fullNameEnglish" value="{{ old('fullNameEnglish', Auth::user()->name_en ?? '') }}" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nationalId" class="form-label">National ID</label>
        <input type="text" class="form-control" id="nationalId" name="nationalId" value="{{ old('nationalId', Auth::user()->national_id ?? '') }}" readonly disabled>
    </div>
    <div class="col-md-6 mb-3">
        <label for="birthDate" class="form-label">Birth Date</label>
        <input type="text" class="form-control" id="birthDate" name="birthDate" placeholder="DD/MM/YYYY" value="{{ old('birthDate', Auth::user()->birth_date ?? '') }}" readonly disabled>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" id="gender" name="gender" disabled>
            <option value="">Select Gender</option>
            <option value="male" {{ old('gender', Auth::user()->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', Auth::user()->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
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