@extends('layouts.app')

@section('title', __('categories.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-folder-open text-primary me-2"></i>{{ __('categories.list_header') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> {{ __('categories.btn_add') }}
            </button>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('categories.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('categories.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value=""> {{ __('categories.filter_all_status') }} </option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            {{ __('categories.status_active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            {{ __('categories.status_inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">
                        {{ __('categories.btn_search') }}
                    </button>
                    @if (request()->has('search') || request()->has('status'))
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('categories.tbl_id') }}</th>
                            <th class="py-3">{{ __('categories.tbl_name') }}</th>
                            <th class="text-center py-3">{{ __('categories.tbl_status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('categories.tbl_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($categories as $category)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $category->CategoryID }}</td>
                                <td class="fw-bold text-dark">{{ $category->Name }}</td>
                                <td class="text-center">
                                    @if ($category->status == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('categories.status_active') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> {{ __('categories.status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $category->CategoryID }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if ($category->products_count > 0)
                                            <button type="button" class="btn btn-sm btn-light text-secondary border" disabled
                                                    title="{{ __('categories.msg_cannot_delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('categories.destroy', $category->CategoryID) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light text-danger border btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editCategoryModal{{ $category->CategoryID }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>{{ __('categories.edit_category') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('categories.update', $category->CategoryID) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('categories.category_name') }}</label>
                                                        <input type="text" name="Name"
                                                            class="form-control @error('Name') is-invalid @enderror"
                                                            value="{{ $category->Name }}" required>
                                                        @error('Name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-primary">{{ __('categories.tbl_status') }}</label>
                                                        <select name="status" class="form-select border-primary bg-primary bg-opacity-10">
                                                            <option value="1"
                                                                {{ $category->status == 1 ? 'selected' : '' }}>
                                                                {{ __('categories.status_active') }}</option>
                                                            <option value="0"
                                                                {{ $category->status == 0 ? 'selected' : '' }}>
                                                                {{ __('categories.status_inactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                                                    data-bs-dismiss="modal">{{ __('categories.cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary fw-bold px-4">{{ __('categories.save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('categories.no_data') ?? 'No categories found.' }}</h5>
                                    </div>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>{{ __('categories.btn_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('categories.category_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Name"
                                    class="form-control @error('Name') is-invalid @enderror"
                                    value="{{ old('Name') }}" required>
                                @error('Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                            data-bs-dismiss="modal">{{ __('categories.cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('categories.save') }}</button>
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
