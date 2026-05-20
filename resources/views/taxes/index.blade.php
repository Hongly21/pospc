@extends('layouts.app')

@section('title', __('taxes.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-percentage text-primary me-2"></i>{{ __('taxes.list_header') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addTaxModal">
                <i class="fas fa-plus me-1"></i> {{ __('taxes.btn_add') }}
            </button>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('taxes.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('taxes.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('taxes.filter_all_status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('taxes.status_active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('taxes.status_inactive') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">{{ __('taxes.btn_search') }}</button>
                    @if (request()->has('search') || request()->has('status'))
                        <a href="{{ route('taxes.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('taxes.tbl_id') }}</th>
                            <th class="py-3">{{ __('taxes.tbl_name') }}</th>
                            <th class="py-3">{{ __('taxes.tbl_rate') }}</th>
                            <th class="py-3">{{ __('taxes.tbl_description') }}</th>
                            <th class="text-center py-3">{{ __('taxes.tbl_status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('taxes.tbl_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($taxes as $tax)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $tax->TaxID }}</td>
                                <td class="fw-bold text-dark">{{ $tax->Name }}</td>
                                <td>{{ number_format($tax->Rate, 2) }}%</td>
                                <td>{{ $tax->Description ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($tax->Status == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">{{ __('taxes.status_active') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">{{ __('taxes.status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#editTaxModal{{ $tax->TaxID }}" title="{{ __('edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('taxes.destroy', $tax->TaxID) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light text-danger border btn-delete" title="{{ __('delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editTaxModal{{ $tax->TaxID }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>{{ __('taxes.edit_tax') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('taxes.update', $tax->TaxID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_name') }}</label>
                                                        <input type="text" name="Name" class="form-control" value="{{ $tax->Name }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_rate') }}</label>
                                                        <input type="number" step="0.01" name="Rate" class="form-control" value="{{ $tax->Rate }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_description') }}</label>
                                                        <textarea name="Description" class="form-control" rows="3">{{ $tax->Description }}</textarea>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold text-primary">{{ __('taxes.tbl_status') }}</label>
                                                        <select name="Status" class="form-select border-primary bg-primary bg-opacity-10">
                                                            <option value="1" {{ $tax->Status == 1 ? 'selected' : '' }}>{{ __('taxes.status_active') }}</option>
                                                            <option value="0" {{ $tax->Status == 0 ? 'selected' : '' }}>{{ __('taxes.status_inactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('taxes.cancel') }}</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('taxes.save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-percentage fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('taxes.no_data') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $taxes->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addTaxModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>{{ __('taxes.btn_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('taxes.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="Name" class="form-control @error('Name') is-invalid @enderror" value="{{ old('Name') }}" required>
                                @error('Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_rate') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="Rate" class="form-control @error('Rate') is-invalid @enderror" value="{{ old('Rate') }}" required>
                                @error('Rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('taxes.tax_description') }}</label>
                                <textarea name="Description" class="form-control" rows="3">{{ old('Description') }}</textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-primary">{{ __('taxes.tbl_status') }}</label>
                                <select name="Status" class="form-select border-primary bg-primary bg-opacity-10">
                                    <option value="1" selected>{{ __('taxes.status_active') }}</option>
                                    <option value="0">{{ __('taxes.status_inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('taxes.cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('taxes.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.taxesIndexConfig = {
                swalTitle: "{{ __('taxes.swal_delete_title') }}",
                swalText: "{{ __('taxes.swal_delete_text') }}",
                swalConfirmBtn: "{{ __('taxes.swal_confirm_btn') }}",
                swalCancelBtn: "{{ __('taxes.swal_cancel_btn') }}"
            };
        </script>
        <script src="{{ asset('js/pages/taxes-index.js') }}"></script>
    @endpush
@endsection
