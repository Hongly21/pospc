@extends('layouts.guest')

@section('title', __('auth.reset_title'))
@section('body-class', 'auth-centered')

@section('content')
    <div class="card p-4">
        <h3 class="fw-bold text-center mb-3">{{ __('auth.reset_title') }}</h3>
        <p class="text-muted text-center small mb-4">{{ __('auth.reset_subtitle') }}</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="fw-bold small text-muted">{{ __('auth.otp_code') }}</label>
                <input type="text" name="otp" class="form-control text-center fw-bold letter-spacing-2"
                    style="letter-spacing: 5px; font-size: 1.2rem;" required>
            </div>

            <div class="mb-3">
                <label class="fw-bold small text-muted">{{ __('auth.new_password') }}</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="fw-bold small text-muted">{{ __('auth.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-outline-primary w-100">{{ __('auth.reset_btn') }}</button>
        </form>
    </div>
@endsection
