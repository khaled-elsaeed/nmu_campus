<div class="mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <span class="step-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 2rem; height: 2rem; font-size: 1.1rem;">
                4
            </span>
        </div>
        <div class="col">
            <div class="fw-bold text-primary mb-1">{{ __('Reservation Information') }}</div>
            <div class="text-muted">
                {{ __('Please provide your reservation details.') }}
            </div>
        </div>
    </div>
</div>

{{-- Step 1: Stay Preference (only show if has sibling with same gender) --}}
<div class="row d-none" id="stay-preference-div">
    <div class="col-md-6 mb-3">
        <label for="stay-preference" class="form-label">{{ __('Stay Preference') }}</label>
        <select class="form-select" id="stay-preference" name="stay_preference">
            <option value="">{{ __('Select Stay Preference') }}</option>
            <option value="stay_with_sibling">{{ __('Stay with Sibling') }}</option>
            <option value="stay_alone">{{ __('Stay Alone') }}</option>
        </select>
        
    </div>
</div>

{{-- Sibling Selection (when staying with sibling) --}}
<div class="row d-none" id="sibling-details-div">
    <div class="col-md-6 mb-3">
        <label for="sibling-to-stay-with" class="form-label">{{ __('Select Sibling to Stay With') }}</label>
        <select class="form-select" id="sibling-to-stay-with" name="sibling_to_stay_with">
            <option value="">{{ __('Select Sibling') }}</option>
        </select>
        
    </div>
</div>

{{-- Step 2: Room Type Selection --}}
<div class="row d-none" id="room-type-div">
    <div class="col-md-6 mb-3">
        <label for="room-type" class="form-label">{{ __('Room Type') }}</label>
        <select class="form-select" id="room-type" name="room_type">
            <option value="">{{ __('Select Room Type') }}</option>
            <option value="single">{{ __('Single Room') }}</option>
            <option value="double">{{ __('Double Room') }}</option>
        </select>
        
    </div>
</div>

{{-- Step 3a: Single Room Options --}}
<div class="row d-none" id="single-room-options-div">
    <div class="col-md-6 mb-3">
        <label for="single-room-preference" class="form-label">{{ __('Single Room Preference') }}</label>
        <select class="form-select" id="single-room-preference" name="single_room_preference">
            <option value="">{{ __('Select Preference') }}</option>
            <option value="old_room" id="old-room-option">{{ __('Stay in Previous Room') }}</option>
            <option value="random">{{ __('Random Assignment') }}</option>
        </select>
        
    </div>
</div>

{{-- Step 3b: Double Room Options --}}
<div class="row d-none" id="double-room-options-div">
    <div class="col-md-6 mb-3">
        <label for="double-room-preference" class="form-label">{{ __('Double Room Preference') }}</label>
        <select class="form-select" id="double-room-preference" name="double_room_preference">
            <option value="">{{ __('Select Bed Type') }}</option>
            <option value="single_beds">{{ __('Two Single Beds') }}</option>
            <option value="double_bed">{{ __('One Double Bed') }}</option>
        </select>
        
    </div>
</div>

{{-- Old Room Details Display --}}
<div class="row d-none" id="old-room-details-div">
    <div class="col-12 mb-3">
        <div class="alert alert-info">
            <h6 class="mb-2">{{ __('Your Previous Room Details:') }}</h6>
            <div id="old-room-info">
                {{-- Will be populated by JavaScript --}}
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary prev-Btn">
        <i class='bx bx-chevron-left'></i> {{ __('Previous') }}
    </button>
    <button type="button" class="btn btn-primary next-Btn">
        {{ __('Next') }} <i class='bx bx-chevron-right'></i>
    </button>
</div>