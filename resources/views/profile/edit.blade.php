@extends('layouts.app')

@section('title', __('profile.page_title'))

@section('content')
    @include('partials.alerts')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i> {{ __('profile.edit_header') }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            @if ($user->UserImage)
                                <img src="{{ str_starts_with($user->UserImage, 'http') ? $user->UserImage : asset('storage/' . $user->UserImage) }}" alt="Profile Picture"
                                    class="rounded-circle profile-avatar rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2 profile-avatar-placeholder">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2 profile-avatar-placeholder">
                                    {{ strtoupper(substr($user->Username, 0, 1)) }}
                                </div>
                            @endif
                            <div class="mt-2">
                                <label for="imageUpload" class="btn btn-sm btn-outline-primary cursor-pointer">
                                    <i class="fas fa-camera me-1"></i> {{ __('profile.change_photo') }}
                                </label>
                                <input type="file" name="user_image" id="imageUpload" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('profile.full_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="{{ $user->Username }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('profile.phone') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->PhoneNumber }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('profile.email') }} <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $user->Email }}" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-lock me-1"></i> {{ __('profile.change_password') }}
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('profile.new_password') }}</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="{{ __('profile.keep_blank') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('profile.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="{{ __('profile.enter_again') }}">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-outline-primary px-4 fw-bold">
                                <i class="fas fa-save me-2"></i> {{ __('profile.save_changes') }}
                            </button>
                        </div>
                    </form>





                    
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script defer src="{{ asset('js/pages/profile-edit.js') }}"></script>
    @endpush

@endsection
