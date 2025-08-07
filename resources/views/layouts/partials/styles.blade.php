<!-- Favicon -->
 
<!-- <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}" /> -->

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}?v={{ config('app.version') }}" />


<link rel="stylesheet" href="{{ asset('vendor/libs/sweetalert2/sweetalert2.min.css') }}?v={{ config('app.version') }}">

<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@if(app()->getLocale() === 'ar')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
@else
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endif

<!-- Core CSS -->
@if (app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('vendor/css/core.rtl.css') }}?v={{ config('app.version') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor/css/theme-default.rtl.css') }}?v={{ config('app.version') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('css/demo.rtl.css') }}?v={{ config('app.version') }}" />
@else
    <link rel="stylesheet" href="{{ asset('vendor/css/core.css') }}?v={{ config('app.version') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor/css/theme-default.css') }}?v={{ config('app.version') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}?v={{ config('app.version') }}" />
@endif

<!-- Vendor CSS -->
<link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}?v={{ config('app.version') }}" />
<link rel="stylesheet" href="{{ asset('vendor/libs/apex-charts/apex-charts.css') }}?v={{ config('app.version') }}" />

<!-- Config (Mandatory theme config file: global vars & default theme options) -->
<script src="{{ asset('js/config.js') }}?v={{ config('app.version') }}"></script>
