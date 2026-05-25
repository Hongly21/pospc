@extends('layouts.guest')

@section('title', __('auth.reset_title') . ' | POS System')
@section('body-class', 'auth-centered')

@section('content')
    <main class="container d-flex align-items-center justify-content-center py-5 auth-fullscreen">
        <div class="card p-4 p-md-5 w-100">

            <div class="text-center mb-4">
                <div class="auth-icon-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3">
                    <i class="fas fa-unlock-alt fa-2x"></i>
                </div>
                <h3 class="fw-bold auth-title-dark">{{ __('auth.reset_title') }}</h3>
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
                    <label for="otp" class="form-label text-uppercase">{{ __('auth.otp_code') }}</label>
                    <input type="text" name="otp" id="otp" class="form-control form-control-lg text-center fw-bold bg-light otp-input"
                        autocomplete="one-time-code" required autofocus placeholder="------">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label text-uppercase">{{ __('auth.new_password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted px-3">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 px-0" autocomplete="new-password" required placeholder="••••••••">
                        <span class="input-group-text bg-transparent border-start-0 text-muted toggle-password px-3" onclick="toggleResetField('password', 'toggle-icon-1')">
                            <i class="fas fa-eye" id="toggle-icon-1"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password_confirmation" class="form-label text-uppercase">{{ __('auth.confirm_password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted px-3">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0 px-0" autocomplete="new-password" required placeholder="••••••••">
                        <span class="input-group-text bg-transparent border-start-0 text-muted toggle-password px-3" onclick="toggleResetField('password_confirmation', 'toggle-icon-2')">
                            <i class="fas fa-eye" id="toggle-icon-2"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 text-white shadow-sm py-2">
                    {{ __('auth.reset_btn') }}
                </button>
            </form>
        </div>
    </main>

    @push('scripts')
        <script>
            function toggleResetField(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);
                if (field.type === "password") {
                    field.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    field.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }
        </script>
    @endpush
@endsection
