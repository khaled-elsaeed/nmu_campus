<nav
    id="layout-navbar"
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
>
    <!-- Menu Toggle (Mobile) -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <!-- Navbar Right -->
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
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
                            data-language="en"
                            data-text-direction="ltr"
                        >
                            <span>{{ __('English') }}</span>
                        </a>
                    </li>
                    <li>
                        <a
                            class="dropdown-item @if(app()->getLocale() == 'ar') active @endif"
                            href="{{ route('language.switch', ['locale' => 'ar']) }}"
                            data-language="ar"
                            data-text-direction="rtl"
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
                                @if(app()->getLocale() == 'ar')
                                    {{ __('الإشعارات') }}
                                @else
                                    {{ __('Notification') }}
                                @endif
                            </h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span class="badge bg-label-primary me-2">
                                    8 
                                    @if(app()->getLocale() == 'ar')
                                        {{ __('جديد') }}
                                    @else
                                        {{ __('New') }}
                                    @endif
                                </span>
                                <a
                                    href="javascript:void(0)"
                                    class="dropdown-notifications-all p-2"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    aria-label="@if(app()->getLocale() == 'ar') {{ __('تعليم الكل كمقروء') }} @else {{ __('Mark all as read') }} @endif"
                                    data-bs-original-title="@if(app()->getLocale() == 'ar') {{ __('تعليم الكل كمقروء') }} @else {{ __('Mark all as read') }} @endif"
                                >
                                    <i class="icon-base bx bx-envelope-open text-heading"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    <!-- /Header -->

                    <!-- Notification List -->
                    <li class="dropdown-notifications-list" style="max-height: 350px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            <!-- Notification Item 1 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <img src="{{ asset('img/avatars/default.png') }}" alt="" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                {{ __('تهانينا ليتي 🎉') }}
                                            @else
                                                {{ __('Congratulation Lettie 🎉') }}
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                {{ __('فزت بشارة أفضل بائع شهريًا') }}
                                            @else
                                                {{ __('Won the monthly best seller gold badge') }}
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                {{ __('منذ ساعة') }}
                                            @else
                                                {{ __('1h ago') }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 2 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                تشارلز فرانكلين
                                            @else
                                                Charles Franklin
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                قبل طلب الاتصال الخاص بك
                                            @else
                                                Accepted your connection
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ 12 ساعة
                                            @else
                                                12hr ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 3 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <img src="{{ asset('img/avatars/default.png') }}" alt="" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                رسالة جديدة ✉️
                                            @else
                                                New Message ✉️
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                لديك رسالة جديدة من ناتالي
                                            @else
                                                You have new message from Natalie
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                {{ __('منذ ساعة') }}
                                            @else
                                                {{ __('1h ago') }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 4 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle bg-label-success">
                                                <i class="icon-base bx bx-cart"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                لديك طلب جديد 🛒
                                            @else
                                                Whoo! You have new order 🛒
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                قامت ACME Inc. بعمل طلب جديد $1,154
                                            @else
                                                ACME Inc. made new order $1,154
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ يوم
                                            @else
                                                1 day ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 5 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <img src="{{ asset('img/avatars/default.png') }}" alt="" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                تم الموافقة على الطلب 🚀
                                            @else
                                                Application has been approved 🚀
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                تم الموافقة على طلب مشروع ABC الخاص بك.
                                            @else
                                                Your ABC project application has been approved.
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ يومين
                                            @else
                                                2 days ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 6 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle bg-label-success">
                                                <i class="icon-base bx bx-pie-chart-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                تم إنشاء التقرير الشهري
                                            @else
                                                Monthly report is generated
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                تم إنشاء التقرير المالي الشهري لشهر يوليو
                                            @else
                                                July monthly financial report is generated 
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ 3 أيام
                                            @else
                                                3 days ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 7 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <img src="{{ asset('img/avatars/default.png') }}" alt="" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                إرسال طلب اتصال
                                            @else
                                                Send connection request
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                أرسل لك بيتر طلب اتصال
                                            @else
                                                Peter sent you connection request
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ 4 أيام
                                            @else
                                                4 days ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 8 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <img src="{{ asset('img/avatars/default.png') }}" alt="" class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                رسالة جديدة من جين
                                            @else
                                                New message from Jane
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                لديك رسالة جديدة من جين
                                            @else
                                                Your have new message from Jane
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ 5 أيام
                                            @else
                                                5 days ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <!-- Notification Item 9 -->
                            <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 @if(app()->getLocale() == 'ar') ms-3 @else me-3 @endif">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle bg-label-warning">
                                                <i class="icon-base bx bx-error"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-0">
                                            @if(app()->getLocale() == 'ar')
                                                وحدة المعالجة المركزية تعمل بنسبة عالية
                                            @else
                                                CPU is running high
                                            @endif
                                        </h6>
                                        <small class="mb-1 d-block text-body">
                                            @if(app()->getLocale() == 'ar')
                                                نسبة استخدام وحدة المعالجة المركزية حالياً 88.63%
                                            @else
                                                CPU Utilization Percent is currently at 88.63%,
                                            @endif
                                        </small>
                                        <small class="text-body-secondary">
                                            @if(app()->getLocale() == 'ar')
                                                منذ 5 أيام
                                            @else
                                                5 days ago
                                            @endif
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 dropdown-notifications-actions">
                                        <a href="javascript:void(0)" class="dropdown-notifications-read">
                                            <span class="badge badge-dot"></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                            <span class="icon-base bx bx-x"></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- /Notification List -->

                    <!-- View All Notifications -->
                    <li class="border-top">
                        <div class="d-grid p-4">
                            <a class="btn btn-primary btn-sm d-flex" href="javascript:void(0);">
                                <small class="align-middle">
                                    @if(app()->getLocale() == 'ar')
                                        {{ __('عرض جميع الإشعارات') }}
                                    @else
                                        {{ __('View all notifications') }}
                                    @endif
                                </small>
                            </a>
                        </div>
                    </li>
                    <!-- /View All Notifications -->
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
                    <!-- Account Settings -->
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">{{ __('Account Settings') }}</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <!-- Logout -->
                    <li>
                        <a
                            class="dropdown-item"
                            href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        >
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">{{ __('Log Out') }}</span>
                        </a>
                        <form id="logout-form" action="" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!-- /User Dropdown -->

        </ul>
    </div>
</nav>
