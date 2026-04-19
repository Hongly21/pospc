@extends('layouts.app')

@section('title', __('suppliers.title'))

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i>{{ __('suppliers.title') }}</span>
            <button class="btn btn-outline-primary shadow-sm btn-sm" data-bs-toggle="modal"
                data-bs-target="#addSupplierModal">
                <i class="fas fa-plus fa-sm text-dark-50"></i> {{ __('suppliers.add_new') }}
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <form action="{{ route('suppliers.index') }}" method="GET" class="row w-100 g-2 mb-4">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="{{ __('suppliers.search_placeholder') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary px-4 flex-grow-1">
                            {{ __('search_btn') }}
                        </button>
                        @if (request()->has('search') && request('search') != '')
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-sync-alt"></i> {{ __('suppliers.clear_btn') }}
                            </a>
                        @endif
                    </div>
                </form>
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('suppliers.id') }}</th>
                            <th>{{ __('suppliers.name') }}</th>
                            <th>{{ __('suppliers.contact') }}</th>
                            <th>{{ __('suppliers.address') }}</th>
                            <th>{{ __('suppliers.status') }}</th>
                            <th class="text-center">{{ __('suppliers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->SupplierID }}</td>
                                <td class="fw-bold">{{ $supplier->Name }}</td>
                                <td>{{ $supplier->Contact }}</td>
                                <td>{{ $supplier->Address ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $supplier->status == 1 ? 'success' : 'danger' }}">
                                        {{ $supplier->status == 1 ? __('suppliers.active') : __('suppliers.inactive') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning mt-1" data-bs-toggle="modal"
                                        data-bs-target="#updateSupplierModal{{ $supplier->SupplierID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if ($supplier->purchases_count > 0)
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1" disabled
                                                title="{{ __('suppliers.msg_cannot_delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('suppliers.destroy', $supplier->SupplierID) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="updateSupplierModal{{ $supplier->SupplierID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header fw-bold text-dark">
                                            <h5 class="modal-title fw-bold">{{ __('suppliers.edit_title') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('suppliers.update', $supplier->SupplierID) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('suppliers.name') }}</label>
                                                    <input type="text" class="form-control" name="Name" value="{{ $supplier->Name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('suppliers.contact') }}</label>
                                                    <input type="text" class="form-control" name="Contact" value="{{ $supplier->Contact }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('suppliers.address') }}</label>
                                                    <input type="text" class="form-control" name="Address" value="{{ $supplier->Address }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('suppliers.status') }}</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="1" {{ $supplier->status == 1 ? 'selected' : '' }}>{{ __('suppliers.active') }}</option>
                                                        <option value="0" {{ $supplier->status == 0 ? 'selected' : '' }}>{{ __('suppliers.inactive') }}</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer px-0 pb-0">
                                                    <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">{{ __('suppliers.cancel') }}</button>
                                                    <button type="submit" class="btn btn-outline-primary fw-bold">{{ __('suppliers.update') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">{{ __('suppliers.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-dark fw-bold">
                    <h5 class="modal-title fw-bold">{{ __('suppliers.add_new') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>{{ __('suppliers.name') }}<span class="text-danger">*</span></label>
                            <input type="text" name="Name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>{{ __('suppliers.contact') }} <span class="text-danger">*</span></label>
                            <input type="text" name="Contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>{{ __('suppliers.address') }}</label>
                            <input type="text" name="Address" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">{{ __('suppliers.cancel') }}</button>
                        <button type="submit" class="btn btn-outline-primary fw-bold">{{ __('suppliers.save') }}</button>
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
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: "{{ __('suppliers.delete_confirm') }}",
                        text: "{{ __('suppliers.delete_text') }}",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "{{ __('suppliers.delete_btn') }}",
                        cancelButtonText: "{{ __('suppliers.cancel') }}"
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
