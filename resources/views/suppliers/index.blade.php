@extends('layouts.app')

@section('title', __('suppliers.title'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-truck text-primary me-2"></i>{{ __('suppliers.title') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addSupplierModal">
                <i class="fas fa-plus me-1"></i> {{ __('suppliers.add_new') }}
            </button>
        </div>

        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('suppliers.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('suppliers.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">
                        {{ __('search_btn') }}
                    </button>
                    @if (request()->has('search') && request('search') != '')
                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('suppliers.id') }}</th>
                            <th class="py-3">{{ __('suppliers.name') }}</th>
                            <th class="py-3">{{ __('suppliers.contact') }}</th>
                            <th class="py-3">{{ __('suppliers.address') }}</th>
                            <th class="py-3">{{ __('suppliers.status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('suppliers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $supplier->SupplierID }}</td>
                                <td class="fw-bold text-dark">{{ $supplier->Name }}</td>
                                <td>{{ $supplier->Contact }}</td>
                                <td>{{ $supplier->Address ?? '-' }}</td>
                                <td>
                                    @if ($supplier->status == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('suppliers.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> {{ __('suppliers.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#updateSupplierModal{{ $supplier->SupplierID }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if ($supplier->purchases_count > 0)
                                            <button type="button" class="btn btn-sm btn-light text-secondary border" disabled
                                                    title="{{ __('suppliers.msg_cannot_delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('suppliers.destroy', $supplier->SupplierID) }}" method="POST"
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
                            <div class="modal fade" id="updateSupplierModal{{ $supplier->SupplierID }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>{{ __('suppliers.edit_title') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('suppliers.update', $supplier->SupplierID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('suppliers.name') }}</label>
                                                        <input type="text" class="form-control" name="Name" value="{{ $supplier->Name }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('suppliers.contact') }}</label>
                                                        <input type="text" class="form-control" name="Contact" value="{{ $supplier->Contact }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-12">
                                                        <label class="form-label small fw-bold text-primary">{{ __('suppliers.status') }}</label>
                                                        <select class="form-select border-primary bg-primary bg-opacity-10" name="status" required>
                                                            <option value="1" {{ $supplier->status == 1 ? 'selected' : '' }}>{{ __('suppliers.active') }}</option>
                                                            <option value="0" {{ $supplier->status == 0 ? 'selected' : '' }}>{{ __('suppliers.inactive') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('suppliers.address') }}</label>
                                                        <input type="text" class="form-control" name="Address" value="{{ $supplier->Address }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('suppliers.cancel') }}</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('suppliers.update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-truck fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('suppliers.no_records_found') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-4">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>{{ __('suppliers.add_new') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('suppliers.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="Name" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('suppliers.contact') }} <span class="text-danger">*</span></label>
                                <input type="text" name="Contact" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-12">
                                <label class="form-label small fw-bold text-muted">{{ __('suppliers.address') }}</label>
                                <input type="text" name="Address" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('suppliers.cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('suppliers.save') }}</button>
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
