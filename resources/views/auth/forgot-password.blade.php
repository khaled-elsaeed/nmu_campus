@extends('layouts.auth')

@section('title', __('Forgot Password'))

@section('content')
<!-- Logo -->
<div class="app-brand justify-content-center">
    <a href="/" class="app-brand-link d-flex flex-column align-items-center gap-2">
        <span class="app-brand-logo demo">
            <img src="{{ asset('img/logo/logo-round.png') }}" alt="" style="width: 50px; height: 50px;">
        </span>
        <span class="app-brand-text demo text-body fw-bolder text-primary">NMU Housing</span>
    </a>
</div>
<!-- /Logo -->

<h4 class="mb-2">{{ __('Forgot Your Password?') }}</h4>
<p class="mb-4">
    {{ __('Just enter your email address and we’ll send you a link to reset your password.') }}
</p>

@if (session('status'))
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

<form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email" required autofocus />
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100 d-flex justify-content-center align-items-center gap-2" type="submit" id="submitButton">
            <span id="buttonText">Send Reset Link</span>
            <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>

<p class="text-center">
    <span>Remembered your password?</span>
    <a href="{{ route('login') }}">
        <span>Back to Login</span>
    </a>
</p>

<script>
    document.getElementById('formAuthentication').addEventListener('submit', function() {
        const submitButton = document.getElementById('submitButton');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        submitButton.disabled = true;
        loadingSpinner.classList.remove('d-none');
    });
</script>
@endsection
