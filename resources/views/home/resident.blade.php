@extends('layouts.home')

@section('title', 'Resident Home | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Welcome Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow bg-light rounded-4">
        <div class="card-body p-3 d-flex flex-wrap flex-md-nowrap align-items-center justify-content-between gap-2">
          <div class="text-center text-md-start flex-fill">
            <h5 class="fw-bold text-primary mb-1">
              Welcome Back, {{ Auth::user()->name ?? 'Resident' }}!
            </h5>
            <div class="text-muted small mb-1 mt-2">
              <i class="bx bx-info-circle me-1 text-secondary"></i>
              Manage your housing, view assignments, and access resources.
            </div>
            <div class="text-muted small mt-2">
              <i class="bx bx-time me-1 text-secondary"></i>
              Last sign-in: {{ Auth::user()->last_login }}
            </div>
          </div>
          <div class="text-center flex-shrink-0">
            <img 
              src="{{ asset('img/svg/house_restyling_cuate.svg') }}" 
              alt="Welcome Home" 
              style="width:120px; height:120px; object-fit:contain; background:#fff; border-radius:.75rem;"
            >
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <!-- Information Cards -->
  @php
    // Fake resident data
    $resident = (object) [
      'building' => (object) ['name' => 'Maple Hall'],
      'room' => (object) ['number' => '312B', 'floor' => '3rd Floor'],
      'contract_period' => 'Fall 2024 - Spring 2025'
    ];

    // Fake neighbors data
    $neighbors = [
      (object) ['name' => 'Alex Johnson', 'room_number' => '312A'],
      (object) ['name' => 'Taylor Smith', 'room_number' => '312C'],
      (object) ['name' => 'Jordan Lee', 'room_number' => '312D'],
    ];
  @endphp
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="row">
        <!-- Current Housing Assignment Card -->
        <div class="col-12 col-lg-6 mb-4 mb-lg-0">
          <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header bg-white border-bottom-0 d-flex align-items-center justify-content-between py-3">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
                  <i class="bx bx-buildings fs-5 text-primary"></i>
                </div>
                <h6 class="mb-0 fw-semibold text-primary">Current Housing Assignment</h6>
              </div>
              <span class="badge bg-success rounded-pill px-3 py-2 d-flex align-items-center">
                <i class="bx bx-check-circle me-1"></i> Active
              </span>
            </div>
            <div class="card-body">
              <div class="d-flex flex-wrap flex-lg-nowrap gap-2 justify-content-between align-items-center">
                <!-- Details beside each other -->
                <div class="d-flex flex-row flex-wrap gap-3 align-items-center flex-fill min-w-0">
                  <div class="d-flex align-items-center">
                    <span class="text-primary fw-medium me-2"><i class="bx bx-buildings me-1"></i>Building:</span>
                    <span class="fw-semibold text-dark">{{ $resident->building->name ?? 'Lincoln Hall' }}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="text-primary fw-medium me-2"><i class="bx bx-door-open me-1"></i>Room:</span>
                    <span class="fw-semibold text-dark">{{ $resident->room->number ?? '204A' }}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="text-primary fw-medium me-2"><i class="bx bx-layer me-1"></i>Floor:</span>
                    <span class="fw-semibold text-dark">{{ $resident->room->floor ?? '2nd Floor' }}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="text-primary fw-medium me-2"><i class="bx bx-calendar me-1"></i>Contract Period:</span>
                    <span class="fw-semibold text-dark">{{ $resident->contract_period ?? 'Fall 2024 - Spring 2025' }}</span>
                  </div>
                </div>
                <div class="d-flex align-items-center ms-lg-4 mt-3 mt-lg-0 flex-shrink-0" style="min-width:180px;">
                  <button class="btn btn-outline-primary btn-sm w-100 shadow" type="button">
                    <i class="bx bx-map me-1"></i>View Floor Plan
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Neighbors Card -->
        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm h-100 bg-white">
            <div class="card-header bg-white border-bottom-0 d-flex align-items-center justify-content-between py-3">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center border border-1 border-primary">
                  <i class="bx bx-group fs-5 text-primary"></i>
                </div>
                <h6 class="mb-0 fw-semibold text-primary">Neighbors</h6>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex flex-row flex-wrap align-items-center justify-content-center gap-2 flex-fill min-w-0">
                @if(isset($neighbors) && count($neighbors))
                  @foreach($neighbors as $neighbor)
                    <div class="d-flex flex-column align-items-center mb-2 px-2 py-2" style="min-width:120px;">
                      <div class="avatar avatar-xs bg-light rounded-circle mb-2 d-flex align-items-center justify-content-center border border-1 border-secondary">
                        <i class="bx bx-user text-secondary"></i>
                      </div>
                      <span class="fw-semibold text-dark text-center d-flex align-items-center">
                        {{ $neighbor->name }}
                      </span>
                      <span class="text-muted small text-center d-flex align-items-center">
                        <i class="bx bx-phone me-1 text-primary"></i>{{ $neighbor->phone ?? 'No phone' }}
                      </span>
                      <span class="text-muted small text-center mt-1 d-flex align-items-center">
                        <i class="bx bx-door-open me-1 text-primary"></i>({{ $neighbor->room_number }})
                      </span>
                    </div>
                  @endforeach
                @else
                  <span class="mb-2 text-muted">No neighbors found.</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row g-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm bg-white">
        <div class="card-header bg-white border-bottom-0 py-3">
          <h6 class="mb-0 fw-semibold text-primary d-flex align-items-center">
            <i class="bx bx-link-alt me-2 text-primary"></i>
            Helpful Resources
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6 col-md-3">
              <a href="#" class="btn btn-outline-secondary btn-sm w-100 d-flex flex-column align-items-center py-3 shadow-sm">
                <i class="bx bx-coffee-togo mb-1 fs-4"></i>
                <span class="small">Dining Hours</span>
              </a>
            </div>
            <div class="col-6 col-md-3">
              <a href="#" class="btn btn-outline-secondary btn-sm w-100 d-flex flex-column align-items-center py-3 shadow-sm">
                <i class="bx bx-droplet mb-1  fs-4"></i>
                <span class="small ">Laundry Status</span>
              </a>
            </div>
            <div class="col-6 col-md-3">
              <a href="#" class="btn btn-outline-secondary btn-sm w-100 d-flex flex-column align-items-center py-3 shadow-sm">
                <i class="bx bx-calendar-event mb-1  fs-4"></i>
                <span class="small ">Events</span>
              </a>
            </div>
            <div class="col-6 col-md-3">
              <a href="#" class="btn btn-outline-secondary btn-sm w-100 d-flex flex-column align-items-center py-3 shadow-sm">
                <i class="bx bx-support mb-1  fs-4"></i>
                <span class="small ">Support</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection