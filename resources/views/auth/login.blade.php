@extends('layouts.guest')

@section('title', __('auth.login_btn'))

@section('content')

    <div class="login-wrapper">
        <div class="brand-side">
            <div class="circle c1">
                <img src="{{ asset('Uploads/products/Yotta_Icon.png') }}" alt="POS Logo">
            </div>

            <div class="circle c2"></div>
            <div class="brand-content">
                <i class="fas fa-layer-group brand-icon"></i>
                <h1 class="fw-bold mb-3">{{ __('auth.pos_system') }}</h1>
                <p class="fs-5 text-white-50">{{ __('auth.pos_description') }}</p>
            </div>
        </div>

        <div class="form-side">

            <div class="login-header">
                <h2>{{ __('auth.welcome') }}</h2>
                <p>{{ __('auth.login_subtitle') }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.email_label') }}</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label
                        class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.password_label') }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="input-group-text toggle-password" onclick="togglePassword()"
                            style="cursor: pointer; border-left: none;">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label small text-muted"
                            for="remember">{{ __('auth.remember_me') }}</label>
                    </div>
                    <a href="{{ route('password.request') }}"
                        class="small text-primary text-decoration-none">{{ __('auth.forgot_password') }}</a>
                </div>

                <button type="submit" class="btn btn-outline-primary w-100 fw-bold">{{ __('auth.login_btn') }}</button>
            </form>

            <div class="text-center mt-5">
                <span class="text-muted small">{{ __('auth.no_account') }}</span>
                <a href="{{ route('register') }}"
                    class="fw-bold text-dark text-decoration-none ms-1">{{ __('auth.create_account_link') }}</a>
            </div>

            {{-- <div class="text-center mt-4 text-muted small">
                &copy; {{ date('Y') }} ប្រព័ន្ធ POS. រក្សាសិទ្ធិគ្រប់យ៉ាង។
            </div> --}}
        </div>
    </div>

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
@endsection
