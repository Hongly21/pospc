


@extends('layouts.app')

@section('title', __('settings.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-cogs me-2 text-secondary"></i> {{ __('settings.settings') }}</h5>
                </div>
                <div class="card-body p-4">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('settings.shop_name') }}</label>
                            <input type="text" name="shop_name" class="form-control" value="{{ $setting->shop_name }}"
                                required>
                            <small class="text-muted">{{ __('settings.shop_name_help') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('settings.phone') }}</label>
                            <input type="text" name="shop_phone" class="form-control" value="{{ $setting->shop_phone }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('settings.address') }}</label>
                            <textarea name="shop_address" class="form-control" rows="3">{{ $setting->shop_address }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-outline-dark w-100">{{ __('settings.save_changes') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
