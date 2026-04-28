@extends('layouts.guest')

@section('title', __('auth.create_account'))

@section('content')

    <div class="login-wrapper">

        <div class="brand-side">
            <div class="circle c1"></div>
            <div class="circle c2"></div>

            <div class="brand-content">
                <i class="fas fa-user-plus brand-icon"></i>
                <h1 class="fw-bold mb-3">{{ __('auth.join_system') }}</h1>
                <p class="fs-5 text-white-50">{{ __('auth.register_description') }}</p>
            </div>
        </div>

        <div class="form-side">
            <div class="login-header">
                <h2>{{ __('auth.create_account') }}</h2>
                <p>{{ __('auth.register_subtitle') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.full_name') }}</label>
                    <div class="input-group">
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.email_label') }}</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.password_label') }}</label>
                    <div class="form-text small mb-2 text-muted" style="line-height: 1.2;">
                        {{ __('auth.password_requirements') }}
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="reg-password" class="form-control" required>
                        <span class="input-group-text toggle-password" onclick="toggleField('reg-password', 'reg-toggle-icon')" style="cursor: pointer; border-left: none;">
                            <i class="fas fa-eye" id="reg-toggle-icon"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">{{ __('auth.confirm_password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="reg-confirm-password" class="form-control" required>
                        <span class="input-group-text toggle-password" onclick="toggleField('reg-confirm-password', 'reg-confirm-icon')" style="cursor: pointer; border-left: none;">
                            <i class="fas fa-eye" id="reg-confirm-icon"></i>
                        </span>
                    </div>
                </div>

                <input type="hidden" name="role" value="2"> <button type="submit" class="btn btn-outline-primary w-100 mb-3">{{ __('auth.register_btn') }}</button>
            </form>

            <div class="text-center">
                <span class="text-muted small">{{ __('auth.has_account') }}</span>
                <a href="{{ route('login') }}" class="fw-bold text-dark text-decoration-none ms-1">{{ __('auth.login_link') }}</a>
            </div>
        </div>
    </div>

    <script>
        function toggleField(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
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
