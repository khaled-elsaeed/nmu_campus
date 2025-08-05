<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                3
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">Academic Information</div>
            <div class="text-muted">Please provide your academic details as they appear on your university records.</div>
        </div>
    </div>
</div>
<input type="hidden" name="is_new_comer" id="is-new-comer" value="" >
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="faculty" class="form-label">Faculty</label>
        <select class="form-select" id="faculty" name="faculty">
            {{-- Optionally populate faculties dynamically via JS --}}
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="program" class="form-label">Program</label>
        <select class="form-select" id="program" name="program">
            {{-- Optionally populate programs dynamically via JS --}}
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="academic-year" class="form-label">Level</label>
        <select class="form-select" id="academic-year" name="academic_year">
            <option value="">Select Level</option>
            <option value="1">First Year</option>
            <option value="2">Second Year</option>
            <option value="3">Third Year</option>
            <option value="4">Fourth Year</option>
            <option value="5">Fifth Year</option>
        </select>
    </div>
    <div class="col-md-6 mb-3" id="gpa-div">
        <label for="gpa" class="form-label">Cumulative GPA</label>
        <input type="number" step="0.01" min="0" max="4" class="form-control" id="gpa" name="gpa" value="0.00" readonly>
    </div>
    <div class="col-md-6 mb-3 d-none" id="score-div">
        <label for="score" class="form-label">Score</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" id="score" name="score" value="0.00" readonly>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="academic-id" class="form-label">University ID</label>
        <input type="text" class="form-control" id="academic-id" name="academic_id" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label for="academic-email" class="form-label">University Email</label>
        <input type="email" class="form-control" id="academic-email" name="academic_email" readonly>
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