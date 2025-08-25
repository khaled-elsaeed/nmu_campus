@extends('layouts.auth')

@section('title', __('Login'))

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
<h4 class="mb-2">{{ __('Welcome !') }}</h4>
<p class="mb-4">{{ __('Enter your credentials to access your account') }}</p>

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

<form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus required />
    </div>
    <div class="mb-3 form-password-toggle">
        {{-- <div class="d-flex justify-content-between">
            <label class="form-label" for="password">Password</label>
            <a href="{{ route('password.request') }}">
                <small>Forgot Password?</small>
            </a>
        </div> --}}
        <div class="input-group input-group-merge">
            <input type="password" id="password" class="form-control" name="password" placeholder="••••••••••••" required />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
            <label class="form-check-label" for="remember-me">Remember Me</label>
        </div>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100 d-flex justify-content-center align-items-center gap-2" type="submit" id="submitButton">
            <span id="buttonText">Sign in</span>
            <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>

</form>

<p class="text-center">
    <span>Don't have an account?</span>
    <a href="{{ route('register') }}">
        <span>Register</span>
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
