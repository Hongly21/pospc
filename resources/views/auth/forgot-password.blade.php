@extends('layouts.guest')

@section('title', __('auth.forgot_title') . ' | POS System')
@section('body-class', 'auth-centered')

@section('content')
    <main class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card p-4 p-md-5 w-100">

            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="fas fa-key fa-2x"></i>
                </div>
                <h3 class="fw-bold" style="color: var(--bg-dark);">{{ __('auth.forgot_title') }}</h3>
                <p class="text-muted small px-3">{{ __('auth.forgot_subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success bg-success bg-opacity-10 text-success small mb-4">
                    <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 text-danger small mb-4">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label text-uppercase fw-bold">{{ __('auth.email_label') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" autocomplete="email" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 text-white shadow-sm mb-4 py-2">
                    {{ __('auth.send_otp') }}
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted small fw-semibold">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('auth.back_to_login') }}
                    </a>
                </div>
            </form>
        </div>
    </main>
@endsection
