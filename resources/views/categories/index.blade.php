@extends('layouts.app')

@section('title', __('categories.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <span class="fw-bold text-black"><i class="fas fa-table me-2"></i> {{ __('categories.list_header') }}</span>
            <button class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> {{ __('categories.btn_add') }}
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center mb-4">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="{{ __('categories.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value=""> {{ __('categories.filter_all_status') }} </option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            {{ __('categories.status_active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            {{ __('categories.status_inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4">
                        {{ __('categories.btn_search') }}
                    </button>
                    @if (request()->has('search') || request()->has('status'))
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> {{ __('categories.btn_clear') }}
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('categories.tbl_id') }}</th>
                            <th>{{ __('categories.tbl_name') }}</th>
                            <th>{{ __('categories.tbl_status') }}</th>
                            <th class="text-center">{{ __('categories.tbl_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->CategoryID }}</td>
                                <td class="fw-bold">{{ $category->Name }}</td>
                                <td class="text-center">
                                    @if ($category->status == 1)
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ __('categories.status_active') }}</span>
                                    @else
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">{{ __('categories.status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->CategoryID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if ($category->products_count > 0)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                title="{{ __('categories.msg_cannot_delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('categories.destroy', $category->CategoryID) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCategoryModal{{ $category->CategoryID }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">{{ __('categories.edit_category') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('categories.update', $category->CategoryID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label fw-bold">{{ __('categories.category_name') }}</label>
                                                    <input type="text" name="Name"
                                                        class="form-control @error('Name') is-invalid @enderror"
                                                        value="{{ $category->Name }}" required>
                                                    @error('Name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label
                                                        class="form-label fw-bold">{{ __('categories.tbl_status') }}</label>
                                                    <select name="status" class="form-select">
                                                        <option value="1"
                                                            {{ $category->status == 1 ? 'selected' : '' }}>
                                                            {{ __('categories.status_active') }}</option>
                                                        <option value="0"
                                                            {{ $category->status == 0 ? 'selected' : '' }}>
                                                            {{ __('categories.status_inactive') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">{{ __('categories.cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary px-4">{{ __('categories.save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                    {{ __('categories.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">{{ __('categories.btn_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('categories.category_name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="Name"
                                class="form-control @error('Name') is-invalid @enderror"
                                value="{{ old('Name') }}" required>
                            @error('Name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('categories.cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4">{{ __('categories.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('categories.swal_delete_title') }}",
                        text: "{{ __('categories.swal_delete_text') }}",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "{{ __('categories.swal_confirm_btn') }}",
                        cancelButtonText: "{{ __('categories.swal_cancel_btn') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
