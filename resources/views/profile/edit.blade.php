@extends('layouts.app')

@section('title', 'ប្រវត្តិរបស់ខ្ញុំ')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i> កែប្រែប្រវត្តិរបស់ខ្ញុំ
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            @if ($user->UserImage)
                                <img src="{{ asset('storage/' . $user->UserImage) }}" alt="Profile Picture"
                                    class="rounded-circle mb-2"
                                    style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #4e73df;">
                            @else
                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2"
                                    style="width: 120px; height: 120px; border: 3px solid #4e73df;">
                                    <i class="fas fa-user text-white fa-3x"></i>
                                </div>
                            @endif
                            <div class="mt-2">
                                <label for="imageUpload" class="btn btn-sm btn-outline-primary" style="cursor: pointer;">
                                    <i class="fas fa-camera me-1"></i> ប្តូររូបថត
                                </label>
                                <input type="file" name="user_image" id="imageUpload" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ឈ្មោះពេញ (Username) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" value="{{ $user->Username }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">លេខទូរស័ព្ទ (Phone Number)</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->PhoneNumber }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">អាសយដ្ឋានអ៊ីមែល (Email) <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $user->Email }}" required>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-lock me-1"></i> ប្តូរពាក្យសម្ងាត់ (ជម្រើស)
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ពាក្យសម្ងាត់ថ្មី (New Password)</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="ទុកឲ្យទទេដើម្បីរក្សាទុកដដែល">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">បញ្ជាក់ពាក្យសម្ងាត់ (Confirm Password)</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="បញ្ចូលពាក្យសម្ងាត់ម្ដងទៀត">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-outline-primary px-4 fw-bold">
                                <i class="fas fa-save me-2"></i> រក្សាទុកការកែប្រែ
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 900,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            if (e.target.files.length > 0) {
                Toast.fire({
                    icon: "success",
                    text: "រូបថតបានជ្រើសរើសរួច",
                    title: e.target.files[0].name
                });
            }
        });
    </script>
@endsection
