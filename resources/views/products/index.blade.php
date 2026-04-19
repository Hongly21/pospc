@extends('layouts.app')

@section('title', __('products.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i>{{ __('products.list_header') }}</span>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addProductModal">
                <i class="fas fa-plus me-1"></i> {{ __('products.btn_add') }}
            </button>
        </div>
        <div class="card-body">
            {{-- Search and Filter Form --}}
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('products.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="CategoryID" class="form-select">
                        <option value="">{{ __('products.filter_all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->CategoryID }}"
                                {{ request('CategoryID') == $category->CategoryID ? 'selected' : '' }}>
                                {{ $category->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('products.filter_all_status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            {{ __('products.status_active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            {{ __('products.status_inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4">{{ __('products.btn_search') }}</button>
                    @if (request()->has('search') || request()->has('status') || request()->has('CategoryID'))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> {{ __('products.btn_clear') }}
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('products.tbl_id') }}</th>
                            <th>{{ __('products.tbl_image_name') }}</th>
                            <th>{{ __('products.tbl_category') }}</th>
                            <th>{{ __('products.tbl_price') }}</th>
                            <th>{{ __('products.tbl_stock') }}</th>
                            <th class="text-center">{{ __('products.tbl_status') }}</th>
                            <th class="text-end">{{ __('products.tbl_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->ProductID }}</td>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center">
                                        @if ($product->Image)
                                            <img src="{{ asset('storage/' . $product->Image) }}"
                                                class="rounded me-2 product-img-thumb"
                                                alt="{{ $product->Name }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-2 product-img-placeholder">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        {{ $product->Name }}
                                    </div>
                                </td>
                                <td>{{ $product->category->Name ?? __('products.uncategorized') }}</td>
                                <td>${{ number_format($product->SellPrice, 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($product->Status == 1)
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ __('products.status_active') }}</span>
                                    @else
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">{{ __('products.status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning text-yellow me-1" data-bs-toggle="modal"
                                        data-bs-target="#editProductModal{{ $product->ProductID }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if (auth()->user()->hasPermission('manage_products'))
                                        @if ($product->order_details_count > 0)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                    title="{{ __('products.msg_cannot_delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <form action="{{ route('products.destroy', $product->ProductID) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>


                            {{-- Edit Product Modal --}}
                            <div class="modal fade" id="editProductModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header text-dark fw-bold">
                                            <h5 class="modal-title fw-bold"><i
                                                    class="fas fa-edit me-2 fw-bold"></i> {{ __('products.modal_edit_title') }}
                                                {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="{{ route('products.update', $product->ProductID) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_name') }}</label>
                                                        <input type="text" name="Name"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Name }}" required>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_category') }}</label>
                                                        <select name="CategoryID" class="form-select form-select-sm"
                                                            required>
                                                            @foreach ($categories as $cat)
                                                                <option value="{{ $cat->CategoryID }}"
                                                                    {{ $product->CategoryID == $cat->CategoryID ? 'selected' : '' }}>
                                                                    {{ $cat->Name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_brand') }}</label>
                                                        <input type="text" name="Brand"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Brand }}">
                                                    </div>
                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_model') }}</label>
                                                        <input type="text" name="Model"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Model }}">
                                                    </div>

                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_cost') }}</label>
                                                        <input type="number" step="0.01" name="CostPrice"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->CostPrice }}" required>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_price') }}</label>
                                                        <input type="number" step="0.01" name="SellPrice"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->SellPrice }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_warranty') }}</label>
                                                        <input type="number" name="WarrantyMonths"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->WarrantyMonths }}">
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_barcode') }}</label>
                                                        <input type="text" name="Barcode"
                                                            class="form-control form-control-sm"
                                                            value="{{ $product->Barcode }}">
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label class="form-label small fw-bold text-primary">
                                                           {{ __('products.status') }}
                                                        </label>
                                                        <select name="status"
                                                            class="form-select form-select-sm border-primary">
                                                            <option value="1"
                                                                {{ $product->Status == 1 ? 'selected' : '' }}>{{ __('active') }}</option>
                                                            <option value="0"
                                                                {{ $product->Status == 0 ? 'selected' : '' }}>{{ __('inactive') }}
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_image') }}</label>
                                                        <input type="file" name="Image"
                                                            class="form-control form-control-sm">
                                                        @if ($product->Image)
                                                            <div class="mt-2 p-2 bg-light rounded d-inline-block">
                                                                <small
                                                                    class="text-muted d-block mb-1">{{ __('products.lbl_current_image') }}:</small>
                                                                <img src="{{ asset('storage/' . $product->Image) }}" width="50"
                                                                    class="rounded border shadow-sm">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label small fw-bold">{{ __('products.lbl_description') }}</label>
                                                        <textarea name="Description" class="form-control form-control-sm" rows="2">{{ $product->Description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-outline-secondary fw-bold"
                                                    data-bs-dismiss="modal">{{ __('products.btn_cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-outline-success fw-bold">{{ __('products.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
                                    <h5>{{ __('products.no_data') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('products.swal_delete_title') }}",
                        text: "{{ __('products.swal_delete_text') }}",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "{{ __('products.swal_confirm_btn') }}",
                        cancelButtonText: "{{ __('products.swal_cancel_btn') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>


    {{-- Add Modal --}}
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-dark">
                    <h5 class="modal-title fw-bold">{{ __('products.modal_add_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Name" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_category') }} <span
                                        class="text-danger">*</span></label>
                                <select name="CategoryID" class="form-select form-select-sm" required>
                                    <option value="">{{ __('products.select_category') }}</option>
                                    @foreach ($categories->where('status', 1) as $cat)
                                        <option value="{{ $cat->CategoryID }}">{{ $cat->Name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_brand') }}</label>
                                <input type="text" name="Brand" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_model') }}</label>
                                <input type="text" name="Model" class="form-control form-control-sm">
                            </div>

                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">{{ __('products.lbl_cost') }} ($)</label>
                                <input type="number" step="0.01" name="CostPrice"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">{{ __('products.lbl_price') }} ($)</label>
                                <input type="number" step="0.01" name="SellPrice"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold">{{ __('products.lbl_stock') }}</label>
                                <input type="number" name="StockQuantity" class="form-control form-control-sm"
                                    value="0" required>
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_warranty') }}</label>
                                <input type="number" name="WarrantyMonths" class="form-control form-control-sm"
                                    placeholder="0">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">{{ __('products.lbl_barcode') }}</label>
                                <input type="text" name="Barcode"
                                    class="form-control form-control-sm @error('Barcode') is-invalid @enderror"
                                    value="{{ old('Barcode') }}">
                                @error('Barcode')
                                    <div class="invalid-feedback">
                                        {{ __('products.error_barcode_taken') }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">{{ __('products.lbl_image') }}</label>
                                <input type="file" name="Image" class="form-control form-control-sm">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">{{ __('products.lbl_description') }}</label>
                                <textarea name="Description" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary fw-bold"
                            data-bs-dismiss="modal">{{ __('products.btn_cancel') }}</button>
                        <button type="submit"
                            class="btn btn-outline-success fw-bold">{{ __('products.btn_save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
