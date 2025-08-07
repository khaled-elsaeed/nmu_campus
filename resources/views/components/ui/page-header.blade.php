<div class="card border-0 shadow-sm mb-4" @if(app()->getLocale() === 'ar') dir="rtl" @else dir="ltr" @endif>
  <div class="card-body py-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <div class="d-flex align-items-center mb-2">
          @if(isset($icon))
            <div class="icon-wrapper me-3">
              <i class="{{ $icon }} fs-2 text-primary"></i>
            </div>
          @endif
          <div>
            <h1 class="h3 fw-semibold mb-1 text-dark">{{ $title }}</h1>
            @if(isset($description))
              <p class="text-muted mb-0 fs-6">{{ $description }}</p>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-4 text-md-end">
        <div class="d-flex justify-content-md-end gap-2">
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</div>