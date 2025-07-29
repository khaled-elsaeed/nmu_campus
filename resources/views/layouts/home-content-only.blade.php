@extends('layouts.app')
@push('styles')
  <style>
    .layout-menu-fixed:not(.layout-menu-collapsed) .layout-page,
    .layout-menu-fixed-offcanvas:not(.layout-menu-collapsed) .layout-page {
      padding-inline-start: 0 !important;
    }
  </style>
@endpush

@section('content')
  <div class="layout-container">
    <div class="layout-page">
      <!-- Navbar -->
      <x-navigation.navbar />
      <!-- / Navbar -->

      <div class="content-wrapper">
        <!-- Content -->
        @yield('page-content')
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
      </div>
      <!-- / Content wrapper -->

      @include('layouts.partials.footer')
    </div>
    <!-- / Layout page -->
  </div>
@endsection