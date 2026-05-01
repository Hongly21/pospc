@extends('layouts.guest')

@section('title', __('auth.login_btn') . ' | POS System')

@section('content')

    <!-- Semantic <main> tag for SEO -->
    <main class="login-wrapper">

        <div class="brand-side" style="background-image: url('{{ asset('assets/images/bg.png') }}');">
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
                            <span class="input-group-text toggle-password" onclick="togglePassword()" style="cursor: pointer;">
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
                        <a href="{{ route('password.request') }}" class="small fw-bold text-decoration-none" style="color: var(--primary);">
                            {{ __('auth.forgot_password') }}
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 text-white shadow-sm mb-4">
                        {{ __('auth.login_btn') }}
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">{{ __('auth.no_account') }}</span>
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color: var(--bg-dark);">
                        {{ __('auth.create_account_link') }}
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

    @if (isset($login_success) && $login_success)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let timerInterval;
                Swal.fire({
                    title: "Login Successful!",
                    html: "Loading your dashboard in <b></b> milliseconds.",
                    icon: "success",
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getPopup().querySelector("b");
                        timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                        }, 100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then(() => {
                    window.location.href = "{!! $redirect_url !!}";
                });
            });
        </script>
    @endif
@endsection
