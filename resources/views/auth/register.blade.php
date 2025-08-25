@extends('layouts.auth')

@section('title', __('Register'))

@section('content')
<!-- Logo -->
<div class="app-brand justify-content-center">
    <a href="/" class="app-brand-link d-flex flex-column align-items-center gap-2">
        <span class="app-brand-logo demo">
            <img src="{{ asset('img/logo/logo-round.png') }}" alt="" style="width: 50px; height: 50px;">
        </span>
        <span class="app-brand-text demo text-body fw-bolder text-primary">{{ __('NMU Housing') }}</span>
    </a>
</div>
<!-- /Logo -->

<h4 class="mb-2">{{ __('Create Your Account') }}</h4>
<p class="mb-4">{{ __('To get started, please provide your National ID to register for NMU Housing.') }}</p>

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

<form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="national_id" class="form-label">{{ __('National ID') }}</label>
        <input type="text" class="form-control" id="national_id" name="national_id" placeholder="Enter your National ID" autofocus required />
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100 d-flex justify-content-center align-items-center gap-2" type="submit" id="submitButton">
            <span id="buttonText">{{ __('Register') }}</span>
            <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>

<p class="text-center">
    <span>{{ __('Already registered?') }}</span>
    <a href="{{ route('login') }}">
        <span>{{ __('Sign in here') }}</span>
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
