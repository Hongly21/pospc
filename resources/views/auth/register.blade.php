@extends('layouts.guest')

@section('title', __('auth.create_account') . ' | POS System')

@section('content')

    <main class="login-wrapper">

        <div class="brand-side" style="background-image: url('{{ asset('assets/images/bg.png') }}');">
            <div class="brand-overlay">
                <i class="fas fa-user-plus fa-3x mb-4 text-info"></i>
                <h1>{{ __('auth.join_system') }}</h1>
                <p>{{ __('auth.register_description') }}</p>
            </div>
        </div>

        <div class="form-side">
            <div class="form-container">
                <div class="login-header">
                    <h2>{{ __('auth.create_account') }}</h2>
                    <p class="text-muted">{{ __('auth.register_subtitle') }}</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center mb-4 bg-danger bg-opacity-10 text-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div class="small fw-semibold">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label text-uppercase">{{ __('auth.full_name') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="username" id="username" class="form-control" autocomplete="name" required value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-uppercase">{{ __('auth.email_label') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="email" name="email" id="email" class="form-control" autocomplete="email" required value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reg-password" class="form-label text-uppercase">{{ __('auth.password_label') }} <span class="text-danger">*</span></label>
                        <div class="form-text small mb-2 text-muted">{{ __('auth.password_requirements') }}</div>
                        <div class="input-group">
                            <input type="password" name="password" id="reg-password" class="form-control" autocomplete="new-password" required>
                            <span class="input-group-text toggle-password" onclick="toggleField('reg-password', 'reg-toggle-icon')" style="cursor: pointer;">
                                <i class="fas fa-eye" id="reg-toggle-icon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reg-confirm-password" class="form-label text-uppercase">{{ __('auth.confirm_password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="reg-confirm-password" class="form-control" autocomplete="new-password" required>
                            <span class="input-group-text toggle-password" onclick="toggleField('reg-confirm-password', 'reg-confirm-icon')" style="cursor: pointer;">
                                <i class="fas fa-eye" id="reg-confirm-icon"></i>
                            </span>
                        </div>
                    </div>

                    <input type="hidden" name="role" value="2">

                    <button type="submit" class="btn btn-primary w-100 text-white shadow-sm mb-4">
                        {{ __('auth.register_btn') }}
                    </button>
                </form>

                <div class="text-center">
                    <span class="text-muted small">{{ __('auth.has_account') }}</span>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1" style="color: var(--bg-dark);">
                        {{ __('auth.login_link') }}
                    </a>
                </div>
            </div>
        </div>
    </main>

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
