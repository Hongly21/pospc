@extends('layouts.app')

@section('title', __('inventory.page_title'))

@section('content')
    @include('partials.alerts')

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-boxes me-2"></i>{{ __('inventory.current_stock_status') }}
            </h6>

            <a href="{{ route('inventory.history') }}" class="btn btn-outline-primary btn-sm ">
                <i class="fas fa-history me-1"></i> {{ __('inventory.view_history') }}
            </a>
        </div>

        <div class="card-body">
            {{-- Filter Form --}}
            <form action="{{ route('inventory.index') }}" method="GET" class="row w-100 g-2 mb-4">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('inventory.search_placeholder') }}..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <select name="CategoryID" class="form-select">
                        <option value="">{{ __('inventory.all_categories') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->CategoryID }}"
                                {{ request('CategoryID') == $cat->CategoryID ? 'selected' : '' }}>
                                {{ $cat->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary px-4 flex-grow-1">
                        {{ __('inventory.search_button') }}
                    </button>

                    @if (request()->filled('search') || request()->filled('CategoryID'))
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-sync-alt"></i> {{ __('inventory.clear_button') }}
                        </a>
                    @endif
                </div>
            </form>

            {{-- Inventory Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('inventory.product') }}</th>
                            <th>{{ __('inventory.category') }}</th>
                            <th>{{ __('inventory.current_stock') }}</th>
                            <th>{{ __('inventory.reorder_level') }}</th>
                            <th class="text-center">{{ __('inventory.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="fw-bold">
                                    <div class="d-flex align-items-center">
                                        @if ($product->Image)
                                            <img src="{{ asset('storage/' . $product->Image) }}"
                                                class="rounded me-2 border product-img-thumb"
                                                alt="{{ $product->Name }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-2 border product-img-placeholder">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                        {{ $product->Name }}
                                    </div>
                                </td>
                                <td>{{ $product->category->Name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }} fs-8">
                                        {{ __('inventory.qty') }}: {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark fs-8">
                                        {{ $product->inventory->ReorderLevel ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- Edit Reorder Level Button --}}
                                    <button class="btn btn-outline-secondary btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#adjustStockreorderModal{{ $product->ProductID }}">
                                        <i class="fas fa-sliders-h"></i> {{ __('inventory.btn_change_reorder') }}
                                    </button>

                                    {{-- Adjust Stock Button --}}
                                    <button class="btn btn-outline-primary btn-sm mb-1" data-bs-toggle="modal"
                                        data-bs-target="#adjustStockModal{{ $product->ProductID }}">
                                        <i class="fas fa-sliders-h"></i> {{ __('inventory.btn_adjust_stock') }}
                                    </button>
                                </td>
                            </tr>

                            {{-- Adjust Stock Modal --}}
                            <div class="modal fade" id="adjustStockModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">{{ __('inventory.modal_adjust_title') }}: {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    {{ __('inventory.current_stock') }}: <strong>{{ $product->inventory->Quantity ?? 0 }}</strong>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('inventory.modal_action_label') }}</label>
                                                    <select name="action" class="form-select">
                                                        <option value="add">{{ __('inventory.modal_add_stock') }} (+)</option>
                                                        <option value="subtract">{{ __('inventory.modal_subtract_stock') }} (-)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('inventory.modal_qty_label') }}</label>
                                                    <input type="number" name="quantity" class="form-control" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('inventory.modal_reason_label') }}</label>
                                                    <input type="text" name="reason" class="form-control" placeholder="{{ __('inventory.modal_reason_placeholder') }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('inventory.btn_cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('inventory.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Reorder Level Modal --}}
                            <div class="modal fade" id="adjustStockreorderModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">{{ __('inventory.btn_change_reorder') }}: {{ $product->Name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.updatereorder') }}" method="GET">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label text-warning-emphasis">{{ __('inventory.reorder_level') }}</label>
                                                    <input type="number" name="reorder_level" class="form-control"
                                                           value="{{ $product->inventory->ReorderLevel ?? 0 }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('inventory.btn_cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('inventory.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('inventory.no_products') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
