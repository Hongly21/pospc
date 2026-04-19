@extends('layouts.guest')

@section('title', __('auth.forgot_title'))
@section('body-class', 'auth-centered')

@section('content')
    <div class="card p-4">
        <h3 class="fw-bold text-center mb-3">{{ __('auth.forgot_title') }}</h3>
        <p class="text-muted text-center small mb-4">{{ __('auth.forgot_subtitle') }}</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="fw-bold small text-muted">{{ __('auth.email_label') }}</label>
                <input type="email" name="email" class="form-control" placeholder="" required>
            </div>
            <button type="submit" class="btn btn-outline-primary w-100 mb-3">{{ __('auth.send_otp') }}</button>
            <a href="{{ route('login') }}"
                class="d-block text-center text-decoration-none text-muted small">{{ __('auth.back_to_login') }}</a>
        </form>
    </div>
@endsection
