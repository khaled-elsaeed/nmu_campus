<nav
    id="layout-navbar"
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
>
    <!-- Navbar -->
    <div class="d-flex align-items-center w-100" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Language Switcher -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="icon-base bx bx-globe icon-md"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a
                            class="dropdown-item @if(app()->getLocale() == 'en') active @endif"
                            href="{{ route('language.switch', ['locale' => 'en']) }}"
                        >
                            <span>{{ __('English') }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="dropdown-item @if(app()->getLocale() == 'ar') active @endif"
                            href="{{ route('language.switch', ['locale' => 'ar']) }}"
                        >
                            <span>{{ __('Arabic') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- /Language Switcher -->

            <!-- Notifications -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                >
                    <span class="position-relative">
                        <i class="icon-base bx bx-bell icon-md"></i>
                        <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0"
                    @if(app()->getLocale() == 'ar') style="direction: rtl; text-align: right;" @else style="direction: ltr; text-align: left;" @endif>
                    <!-- Header -->
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">
                                {{ app()->getLocale() == 'ar' ? __('الإشعارات') : __('Notifications') }}
                            </h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span class="badge bg-label-primary me-2">
                                    8 {{ app()->getLocale() == 'ar' ? __('جديد') : __('New') }}
                                </span>
                                <a
                                    href="javascript:void(0)"
                                    class="dropdown-notifications-all p-2"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    aria-label="{{ app()->getLocale() == 'ar' ? __('تعليم الكل كمقروء') : __('Mark all as read') }}"
                                    data-bs-original-title="{{ app()->getLocale() == 'ar' ? __('تعليم الكل كمقروء') : __('Mark all as read') }}"
                                >
                                    <i class="icon-base bx bx-envelope-open text-heading"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    <!-- Notification List -->
                    <li class="dropdown-notifications-list" style="max-height: 350px; overflow-y: auto;">
                        <ul class="list-group list-group-flush"></ul>
                    </li>
                    <!-- View All -->
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                <small class="align-middle">
                                    {{ app()->getLocale() == 'ar' ? __('عرض جميع الإشعارات') : __('View all notifications') }}
                                </small>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- /Notifications -->

            <!-- User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                >
                    <div class="avatar avatar-online">
                        <img src="{{ asset('img/avatars/default.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- User Info -->
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('img/avatars/default.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ optional(Auth::user())->name ?? __('Guest') }}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <!-- Logout -->
                    <li>
                        <a class="dropdown-item"
                           href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">{{ __('Log Out') }}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!-- /User Dropdown -->

        </ul>
    </div>
</nav>
