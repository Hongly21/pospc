@if (session('success') || session('error') || $errors->any())
    <div class="app-alert-stack">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-2 mb-0" role="alert">
                <i class="fas fa-check-circle mt-1"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 mb-0" role="alert">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-exclamation-triangle mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>{{ __('Validation Error') }}</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
@endif
