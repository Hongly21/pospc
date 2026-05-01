@extends('layouts.guest')

@section('title', __('auth.reset_title') . ' | POS System')
@section('body-class', 'auth-centered')

@section('content')
    <main class="container d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">
        <div class="card p-4 p-md-5 w-100">

            <div class="text-center mb-4">
                <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="fas fa-unlock-alt fa-2x"></i>
                </div>
                <h3 class="fw-bold" style="color: var(--bg-dark);">{{ __('auth.reset_title') }}</h3>
                <p class="text-muted small px-3">{{ __('auth.reset_subtitle') }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success bg-success bg-opacity-10 text-success small mb-4">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 text-danger small mb-4">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-4">
                    <label for="otp" class="form-label text-uppercase fw-bold">{{ __('auth.otp_code') }}</label>
                    <input type="text" name="otp" id="otp" class="form-control form-control-lg text-center fw-bold bg-light"
                        autocomplete="one-time-code" style="letter-spacing: 8px; font-size: 1.5rem;" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-uppercase fw-bold">{{ __('auth.new_password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password_confirmation" class="form-label text-uppercase fw-bold">{{ __('auth.confirm_password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 text-white shadow-sm py-2">
                    {{ __('auth.reset_btn') }}
                </button>
            </form>
        </div>
    </main>
@endsection
