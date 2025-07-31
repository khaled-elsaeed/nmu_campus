@extends('layouts.auth')

@section('title', 'Register | AcadOps')

@section('content')
@if (
    session('status')
)
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Logo -->
<div class="app-brand justify-content-center">
    <a href="/" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
            <!-- SVG logo here (copy from layout if needed) -->
        </span>
        <span class="app-brand-text demo text-body fw-bolder text-primary">AcadOps</span>
    </a>
</div>
<!-- /Logo -->
<h4 class="mb-2">Create your AcadOps account! 👋</h4>
<p class="mb-4">Please enter your National ID to register</p>
<form id="formRegistration" class="mb-3" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="national_id" class="form-label">National ID</label>
        <input type="text" class="form-control" id="national_id" name="national_id" placeholder="Enter your National ID" value="{{ old('national_id') }}" required autofocus />
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit">Register</button>
    </div>
    <p class="text-center">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}">
            <span>Sign in instead</span>
        </a>
    </p>
</form>
@endsection 