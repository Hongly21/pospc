@extends('layouts.app')

@section('title', __('products.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-box-open text-primary me-2"></i>{{ __('products.list_header') }}</h5>
            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addProductModal">
                <i class="fas fa-plus me-1"></i> {{ __('products.btn_add') }}
            </button>
        </div>
        <div class="card-body bg-light rounded-bottom">
            {{-- Search and Filter Form --}}
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('products.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="CategoryID" class="form-select form-select-sm bg-light searchable-select">
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
                    <select name="status" class="form-select form-select-sm bg-light">
                        <option value="">{{ __('products.filter_all_status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                            {{ __('products.status_active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                            {{ __('products.status_inactive') }}</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-4 w-100">{{ __('products.btn_search') }}</button>
                    @if (request()->has('search') || request()->has('status') || request()->has('CategoryID'))
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('products.tbl_id') }}</th>
                            <th class="py-3">{{ __('products.tbl_image_name') }}</th>
                            <th class="py-3">{{ __('products.tbl_category') }}</th>
                            <th class="py-3">{{ __('products.tbl_price') }}</th>
                            <th class="py-3">{{ __('products.tbl_stock') }}</th>
                            <th class="text-center py-3">{{ __('products.tbl_status') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('products.tbl_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">#{{ $product->ProductID }}</td>
                                <td>
                                    <div class="d-flex align-items-center py-1">
                                        @if ($product->Image)
                                            @if(str_starts_with($product->Image, 'http'))
                                                <img src="{{ $product->Image }}" alt="{{ $product->Name }}"
                                                    class="rounded me-3 object-fit-cover shadow-sm border border-light" width="48" height="48">
                                            @else
                                                <img src="{{ asset('storage/' . $product->Image) }}" alt="{{ $product->Name }}"
                                                    class="rounded me-3 object-fit-cover shadow-sm border border-light" width="48" height="48">
                                            @endif
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" alt="No Image"
                                                class="rounded me-3 object-fit-cover shadow-sm border border-light" width="48" height="48">
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark mb-1">{{ $product->Name }}</div>
                                            @if ($product->attributes->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach ($product->attributes as $attribute)
                                                        <span class="badge bg-light text-secondary border border-secondary-subtle fw-normal px-2 py-1 badge-small">
                                                            {{ $attribute->AttributeName }}: <span class="text-dark fw-medium">{{ $attribute->AttributeValue }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">{{ $product->category->Name ?? __('products.uncategorized') }}</span></td>
                                <td class="fw-medium text-success">${{ number_format($product->SellPrice, 2) }}</td>
                                <td>
                                    <span
                                        class="badge rounded-pill {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                        {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($product->Status == 1)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ __('products.status_active') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> {{ __('products.status_inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        <button class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#editProductModal{{ $product->ProductID }}" title="{{ __('edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if (auth()->user()->hasPermission('manage_products'))
                                            @if ($product->order_details_count > 0)
                                                <button type="button" class="btn btn-sm btn-light text-secondary border" disabled
                                                    title="{{ __('products.msg_cannot_delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('products.destroy', $product->ProductID) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light text-danger border btn-delete" title="{{ __('delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>


                            {{-- Edit Product Modal --}}
                            <div class="modal fade" id="editProductModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>
                                                {{ __('products.modal_edit_title') }} {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="{{ route('products.update', $product->ProductID) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_name') }}</label>
                                                        <input type="text" name="Name"
                                                            class="form-control @error('Name') is-invalid @enderror"
                                                            value="{{ old('Name', $product->Name) }}" maxlength="255" required>
                                                        @error('Name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_category') }}</label>
                                                        <select name="CategoryID" class="form-select searchable-select @error('CategoryID') is-invalid @enderror"
                                                            required>
                                                            @foreach ($categories as $cat)
                                                                <option value="{{ $cat->CategoryID }}"
                                                                    {{ old('CategoryID', $product->CategoryID) == $cat->CategoryID ? 'selected' : '' }}>
                                                                    {{ $cat->Name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('CategoryID')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_brand') }}</label>
                                                        <input type="text" name="Brand"
                                                            class="form-control"
                                                            value="{{ $product->Brand }}">
                                                    </div>
                                                    <div class="col-6 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_model') }}</label>
                                                        <input type="text" name="Model"
                                                            class="form-control"
                                                            value="{{ $product->Model }}">
                                                    </div>

                                                    <div class="col-6 col-md-4">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_cost') }}</label>
                                                        <input type="number" step="0.01" min="0" name="CostPrice"
                                                            class="form-control @error('CostPrice') is-invalid @enderror"
                                                            value="{{ old('CostPrice', $product->CostPrice) }}" required>
                                                        @error('CostPrice')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_price') }}</label>
                                                        <input type="number" step="0.01" min="0" name="SellPrice"
                                                            class="form-control @error('SellPrice') is-invalid @enderror"
                                                            value="{{ old('SellPrice', $product->SellPrice) }}" required>
                                                        @error('SellPrice')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_warranty') }}</label>
                                                        <input type="number" min="0" name="WarrantyMonths"
                                                            class="form-control @error('WarrantyMonths') is-invalid @enderror"
                                                            value="{{ old('WarrantyMonths', $product->WarrantyMonths) }}">
                                                        @error('WarrantyMonths')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_barcode') }}</label>
                                                        <input type="text"
                                                            class="form-control"
                                                            value="{{ $product->Barcode }}"
                                                            disabled>
                                                        <small class="text-muted">Barcode cannot be changed after creation.</small>
                                                    </div>

                                                    <div class="col-6 col-md-6">
                                                        <label
                                                            class="form-label small fw-bold text-primary">{{ __('products.status') }}</label>
                                                        <select name="Status"
                                                            class="form-select border-primary bg-primary bg-opacity-10 @error('Status') is-invalid @enderror" required>
                                                            <option value="1"
                                                                {{ old('Status', $product->Status) == 1 ? 'selected' : '' }}>
                                                                {{ __('active') }}</option>
                                                            <option value="0"
                                                                {{ old('Status', $product->Status) == 0 ? 'selected' : '' }}>
                                                                {{ __('inactive') }}</option>
                                                        </select>
                                                        @error('Status')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_image') }}</label>
                                                        <input type="file" name="Image"
                                                            class="form-control @error('Image') is-invalid @enderror" accept="image/*">
                                                        @error('Image')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        @if ($product->Image)
                                                            <div class="mt-2 p-2 bg-light rounded d-inline-block border border-light-subtle">
                                                                <small
                                                                    class="text-muted d-block mb-1">{{ __('products.lbl_current_image') }}:</small>
                                                                @if(str_starts_with($product->Image, 'http'))
                                                                    <img src="{{ $product->Image }}" alt="{{ $product->Name }}"
                                                                        width="50" class="rounded border shadow-sm">
                                                                @else
                                                                    <img src="{{ asset('storage/' . $product->Image) }}" alt="{{ $product->Name }}"
                                                                        width="50" class="rounded border shadow-sm">
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="mt-2 p-2 bg-light rounded d-inline-block border border-light-subtle">
                                                                <small
                                                                    class="text-muted d-block mb-1">{{ __('products.lbl_current_image') }}:</small>
                                                                <img src="{{ asset('images/no-image.png') }}" alt="No Image"
                                                                    width="50" class="rounded border shadow-sm">
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label
                                                            class="form-label small fw-bold text-muted">{{ __('products.lbl_description') }}</label>
                                                        <textarea name="Description" class="form-control @error('Description') is-invalid @enderror" rows="2" maxlength="1000">{{ old('Description', $product->Description) }}</textarea>
                                                        @error('Description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="card border border-light-subtle shadow-sm mt-2">
                                                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                                <span class="fw-bold text-dark small"><i class="fas fa-tags text-primary me-1"></i> {{ __('products.lbl_attributes') }}</span>
                                                                <button type="button" class="btn btn-sm btn-primary py-0 px-2 btn-add-attribute" title="{{ __('products.btn_add_attribute') }}"><i class="fas fa-plus small"></i></button>
                                                            </div>
                                                            <div class="card-body p-2 bg-white">
                                                                <div class="attribute-rows">
                                                                    @forelse ($product->attributes as $attribute)
                                                                        <div class="row g-2 align-items-center mb-2 attribute-row">
                                                                            <div class="col-5">
                                                                                <input type="text" name="AttributeName[]"
                                                                                    class="form-control form-control-sm bg-light border-0"
                                                                                    placeholder="{{ __('products.lbl_attribute_name') }}"
                                                                                    value="{{ $attribute->AttributeName }}">
                                                                            </div>
                                                                            <div class="col-5">
                                                                                <input type="text" name="AttributeValue[]"
                                                                                    class="form-control form-control-sm bg-light border-0"
                                                                                    placeholder="{{ __('products.lbl_attribute_value') }}"
                                                                                    value="{{ $attribute->AttributeValue }}">
                                                                            </div>
                                                                            <div class="col-2 text-end">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-light text-danger w-100 border-0 btn-remove-attribute"><i class="fas fa-trash-alt"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                        <div class="row g-2 align-items-center mb-2 attribute-row">
                                                                            <div class="col-5">
                                                                                <input type="text" name="AttributeName[]"
                                                                                    class="form-control form-control-sm bg-light border-0"
                                                                                    placeholder="{{ __('products.lbl_attribute_name') }}">
                                                                            </div>
                                                                            <div class="col-5">
                                                                                <input type="text" name="AttributeValue[]"
                                                                                    class="form-control form-control-sm bg-light border-0"
                                                                                    placeholder="{{ __('products.lbl_attribute_value') }}">
                                                                            </div>
                                                                            <div class="col-2 text-end">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-light text-danger w-100 border-0 btn-remove-attribute"><i class="fas fa-trash-alt"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                                                    data-bs-dismiss="modal">{{ __('products.btn_cancel') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary fw-bold px-4">{{ __('products.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('products.no_data') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                <div class="mt-4">
                    {{-- Pagination --}}
                    @if(method_exists($products, 'links'))
                        <div class="d-flex justify-content-start mt-3">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i> {{ __('products.modal_add_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="Name" class="form-control @error('Name') is-invalid @enderror"
                                    value="{{ old('Name') }}" maxlength="255" required>
                                @error('Name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_category') }} <span
                                        class="text-danger">*</span></label>
                                <select name="CategoryID" class="form-select searchable-select @error('CategoryID') is-invalid @enderror" required>
                                    <option value="">{{ __('products.select_category') }}</option>
                                    @foreach ($categories->where('status', 1) as $cat)
                                        <option value="{{ $cat->CategoryID }}"
                                            {{ old('CategoryID') == $cat->CategoryID ? 'selected' : '' }}>
                                            {{ $cat->Name }}</option>
                                    @endforeach
                                </select>
                                @error('CategoryID')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_brand') }}</label>
                                <input type="text" name="Brand" class="form-control"
                                    value="{{ old('Brand') }}">
                            </div>
                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_model') }}</label>
                                <input type="text" name="Model" class="form-control"
                                    value="{{ old('Model') }}">
                            </div>

                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_cost') }} ($)</label>
                                <input type="number" step="0.01" min="0" name="CostPrice"
                                    class="form-control @error('CostPrice') is-invalid @enderror" value="{{ old('CostPrice') }}" required>
                                @error('CostPrice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_price') }} ($)</label>
                                <input type="number" step="0.01" min="0" name="SellPrice"
                                    class="form-control @error('SellPrice') is-invalid @enderror" value="{{ old('SellPrice') }}" required>
                                @error('SellPrice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_stock') }}</label>
                                <input type="number" min="0" name="StockQuantity" class="form-control @error('StockQuantity') is-invalid @enderror"
                                    value="{{ old('StockQuantity', 0) }}" required>
                                @error('StockQuantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_warranty') }}</label>
                                <input type="number" min="0" name="WarrantyMonths" class="form-control @error('WarrantyMonths') is-invalid @enderror"
                                    value="{{ old('WarrantyMonths') }}" placeholder="0">
                                @error('WarrantyMonths')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_barcode') }}</label>
                                <input type="text" name="Barcode"
                                    class="form-control @error('Barcode') is-invalid @enderror"
                                    value="{{ old('Barcode') }}"
                                    placeholder="Leave blank to auto-generate by category"
                                >
                                {{-- <small class="text-muted">Leave blank to auto-generate barcode by category, e.g. LAPT-ABC123.</small> --}}
                                @error('Barcode')
                                    <div class="invalid-feedback">
                                        {{ __('products.error_barcode_taken') }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_image') }}</label>
                                <input type="file" name="Image" class="form-control @error('Image') is-invalid @enderror" accept="image/*">
                                @error('Image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">{{ __('products.lbl_description') }}</label>
                                <textarea name="Description" class="form-control @error('Description') is-invalid @enderror" rows="2" maxlength="1000">{{ old('Description') }}</textarea>
                                @error('Description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="card border border-light-subtle shadow-sm mt-2">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark small"><i class="fas fa-tags text-primary me-1"></i> {{ __('products.lbl_attributes') }}</span>
                                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 btn-add-attribute" title="{{ __('products.btn_add_attribute') }}"><i class="fas fa-plus small"></i></button>
                                    </div>
                                    <div class="card-body p-2 bg-white">
                                        <div class="attribute-rows">
                                            @php
                                                $oldAttributeNames = old('AttributeName', ['']);
                                                $oldAttributeValues = old('AttributeValue', ['']);
                                                $attributeRowCount = max(count($oldAttributeNames), count($oldAttributeValues));
                                            @endphp
                                            @for ($i = 0; $i < $attributeRowCount; $i++)
                                                <div class="row g-2 align-items-center mb-2 attribute-row">
                                                    <div class="col-5">
                                                        <input type="text" name="AttributeName[]"
                                                            class="form-control form-control-sm bg-light border-0"
                                                            placeholder="{{ __('products.lbl_attribute_name') }}"
                                                            value="{{ $oldAttributeNames[$i] ?? '' }}">
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="text" name="AttributeValue[]"
                                                            class="form-control form-control-sm bg-light border-0"
                                                            placeholder="{{ __('products.lbl_attribute_value') }}"
                                                            value="{{ $oldAttributeValues[$i] ?? '' }}">
                                                    </div>
                                                    <div class="col-2 text-end">
                                                        <button type="button"
                                                            class="btn btn-sm btn-light text-danger w-100 border-0 btn-remove-attribute"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-outline-secondary fw-bold px-4"
                            data-bs-dismiss="modal">{{ __('products.btn_cancel') }}</button>
                        <button type="submit"
                            class="btn btn-primary fw-bold px-4">{{ __('products.btn_save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            window.productsPageConfig = {
                messages: {
                    selectPlaceholder: "{{ __('Select') }}",
                    deleteTitle: "{{ __('products.swal_delete_title') }}",
                    deleteText: "{{ __('products.swal_delete_text') }}",
                    deleteConfirmBtn: "{{ __('products.swal_confirm_btn') }}",
                    deleteCancelBtn: "{{ __('products.swal_cancel_btn') }}",
                    attributeNamePlaceholder: "{{ __('products.lbl_attribute_name') }}",
                    attributeValuePlaceholder: "{{ __('products.lbl_attribute_value') }}"
                },
                showAddModalOnError: {{ ($errors->any() && old('Name') && !$errors->has('Status')) ? 'true' : 'false' }}
            };
        </script>
        <script defer src="{{ asset('js/pages/products-index.js') }}"></script>
    @endpush
@endsection
