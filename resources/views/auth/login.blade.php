@extends('layouts.guest')

@section('title', __('auth.login_btn') . ' | POS System')

@section('content')

    <!-- Semantic <main> tag for SEO -->
    <main class="login-wrapper">

        <div class="brand-side">
            <div class="brand-overlay">
                <i class="fas fa-layer-group fa-3x mb-4 text-info"></i>
                <h1>{{ __('auth.pos_system') }}</h1>
                <p>{{ __('auth.pos_description') }}</p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="form-side">
            <div class="form-container">
                <div class="login-header">
                    <h2>{{ __('auth.welcome') }}</h2>
                    <p class="text-muted">{{ __('auth.login_subtitle') }}</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4 bg-success bg-opacity-10 text-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-4 bg-danger bg-opacity-10 text-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <!-- SEO: 'for' matches 'id' -->
                        <label for="email" class="form-label text-uppercase">{{ __('auth.email_label') }}</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email" class="form-control" autocomplete="email" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label text-uppercase">{{ __('auth.password_label') }}</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
                            <span class="input-group-text toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                {{ __('auth.remember_me') }}
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small fw-bold text-decoration-none text-primary auth-link-primary">
                            {{ __('auth.forgot_password') }}
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 text-white shadow-sm mb-4">
                        {{ __('auth.login_btn') }}
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">{{ __('auth.no_account') }}</span>
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1 text-dark auth-link-dark">
                        {{ __('auth.create_account_link') }}
                    </a>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.authLoginConfig = {
                loginSuccess: {{ isset($login_success) && $login_success ? 'true' : 'false' }},
                redirectUrl: "{{ $redirect_url ?? url('/') }}",
                successTitle: "Login Successful!",
                successMessage: "Loading your dashboard in <b></b> milliseconds."
            };
        </script>
        <script src="{{ asset('js/pages/auth-login.js') }}"></script>
    @endpush
@endsection
