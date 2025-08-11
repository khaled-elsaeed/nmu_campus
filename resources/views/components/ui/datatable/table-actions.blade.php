<div class="d-flex gap-2">
    @if($mode === 'dropdown' || $mode === 'both')
        <!-- Dropdown Mode -->
        <div class="btn-group">
            <button
                type="button"
                class="btn btn-primary btn-icon rounded-pill dropdown-toggle hide-arrow"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach($actions as $actionItem)
                    @if($actionItem === 'view')
                        <li>
                            <a class="dropdown-item view{{ $type }}Btn" href="javascript:void(0);" data-id="{{ $id }}">
                                <i class="bx bx-show me-1"></i> {{ __('app.general.buttons.view') }}
                            </a>
                        </li>
                    @elseif($actionItem === 'edit')
                        <li>
                            <a class="dropdown-item edit{{ $type }}Btn" href="javascript:void(0);" data-id="{{ $id }}">
                                <i class="bx bx-edit-alt me-1"></i> {{ __('app.general.buttons.edit') }}
                            </a>
                        </li>
                    @elseif($actionItem === 'delete')
                        <li>
                            <a class="dropdown-item text-danger delete{{ $type }}Btn" href="javascript:void(0);" data-id="{{ $id }}">
                                <i class="bx bx-trash me-1"></i> {{ __('app.general.buttons.delete') }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    @if($mode === 'single' || $mode === 'both')
        <!-- Multiple Single Action Buttons -->
        @foreach($singleActions as $singleAction)
            <div>
                <button 
                    type="button" 
                    class="btn rounded-pill btn-icon {{ $singleAction['class'] }} {{ $singleAction['action'] }}{{ $type }}Btn"
                    @if($id) data-id="{{ $id }}" @endif
                    title="{{ __('app.general.' . $singleAction['label']) }}"
                >
                    <i class="{{ $singleAction['icon'] }}"></i>
                </button>
            </div>
        @endforeach
    @endif
</div>