@extends('layouts.home')

@section('title', 'Admin Home | NMU Campus')

@section('page-content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Statistics Cards -->
  <div class="row g-4 mb-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
      <x-ui.card.stat 
        color="primary"
        icon="bx bx-user"
        label="Total Students"
        id="students"
      />
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
      <x-ui.card.stat 
        color="warning"
        icon="bx bx-chalkboard"
        label="Total Faculty"
        id="faculty"
      />
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
      <x-ui.card.stat 
        color="danger"
        icon="bx bx-book"
        label="Total Programs"
        id="programs"
      />
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-4">
      <x-ui.card.stat 
        color="info"
        icon="bx bx-library"
        label="Total Courses"
        id="courses"
      />
    </div>
  </div>

  <!-- Charts Section -->
  <div class="row g-4">
    <!-- Level-wise Student Distribution Bar Chart -->
    <div class="col-lg-6 col-12 mb-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom-0 pb-0">
          <div>
            <h4 class="card-title mb-1 fw-semibold text-dark">Students by Academic Level</h4>
            <p class="card-subtitle mb-0 text-muted">Distribution across academic levels</p>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="levelChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- CGPA Distribution Histogram -->
    <div class="col-lg-6 col-12 mb-4">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-header bg-transparent border-bottom-0 pb-0">
          <div>
            <h4 class="card-title mb-1 fw-semibold text-dark">CGPA Distribution</h4>
            <p class="card-subtitle mb-0 text-muted">Academic performance overview</p>
          </div>
        </div>
        <div class="card-body pt-0">
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="cgpaChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

