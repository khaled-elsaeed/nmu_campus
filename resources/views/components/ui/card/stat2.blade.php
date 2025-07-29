<div 
    @if($id) id="{{ $id }}" @endif 
    class="card card-border-shadow-{{ $color }} h-100 card-hover-effect"
    @if(app()->getLocale() === 'ar') dir="rtl" @else dir="ltr" @endif
>
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
                <span class="text-heading">{{ $label }}</span>
                <div class="d-flex align-items-center my-1">
                    <span class="stat-loader @if(app()->getLocale() === 'ar') ms-2 @else me-2 @endif" id="{{ $id ? $id . '-loader' : '' }}">
                        <span class="spinner-border spinner-border-sm text-{{ $color }}" role="status" ></span>
                    </span>
                    <h4 class="mb-0 @if(app()->getLocale() === 'ar') ms-2 @else me-2 @endif stat-value d-none" id="{{ $id ? $id . '-value' : '' }}">--</h4>
                </div>
                <small class="mb-0 text-body-secondary">
                    @if(app()->getLocale() === 'ar')
                        آخر تحديث: 
                    @else
                        Last update: 
                    @endif
                    <span class="stat-last-updated-loader" id="{{ $id ? $id . '-last-updated-loader' : '' }}">
                        <span class="spinner-border spinner-border-sm text-{{ $color }}" role="status" ></span>
                    </span>
                    <span class="stat-last-updated d-none" id="{{ $id ? $id . '-last-updated' : '' }}">--</span>
                </small>
            </div>
            <div class="avatar @if(app()->getLocale() === 'ar') me-0 ms-3 @else ms-0 me-3 @endif">
                <span class="avatar-initial rounded bg-label-{{ $color }}">
                    <i class="icon-base {{ $icon }} icon-lg"></i>
                </span>
            </div>
        </div>
    </div>
</div> 