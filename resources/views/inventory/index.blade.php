@extends('layouts.app')

@section('title', __('inventory.page_title'))

@section('content')
    @include('partials.alerts')
 
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-boxes text-primary me-2"></i>{{ __('inventory.current_stock_status') }}
            </h5>

            <a href="{{ route('inventory.history') }}" class="btn btn-primary btn-sm fw-medium px-3 shadow-sm">
                <i class="fas fa-history me-1"></i> {{ __('inventory.view_history') }}
            </a>
        </div>

        <div class="card-body bg-light rounded-bottom">
            {{-- Filter Form --}}
            <form action="{{ route('inventory.index') }}" method="GET" class="row g-2 align-items-center mb-4 bg-white p-2 rounded shadow-sm mx-0">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 bg-light"
                            placeholder="{{ __('inventory.search_placeholder') }}" value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <select name="CategoryID" class="form-select form-select-sm bg-light">
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
                    <button type="submit" class="btn btn-sm btn-primary px-4 flex-grow-1">
                        {{ __('inventory.search_button') }}
                    </button>

                    @if (request()->filled('search') || request()->filled('CategoryID'))
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-sync-alt"></i> {{ __('inventory.clear_button') }}
                        </a>
                    @endif
                </div>
            </form>

            {{-- Inventory Table --}}
            <div class="table-responsive bg-white rounded shadow-sm border border-light-subtle">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">{{ __('inventory.product') }}</th>
                            <th class="py-3">{{ __('inventory.category') }}</th>
                            <th class="py-3">{{ __('inventory.current_stock') }}</th>
                            <th class="py-3">{{ __('inventory.reorder_level') }}</th>
                            <th class="text-end pe-3 py-3">{{ __('inventory.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <div class="d-flex align-items-center py-1">
                                        @if ($product->Image)
                                            <img src="{{ asset('storage/' . $product->Image) }}"
                                                class="rounded me-3 object-fit-cover shadow-sm border border-light" width="48" height="48"
                                                alt="{{ $product->Name }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center me-3 shadow-sm border border-light" style="width: 48px; height: 48px;">
                                                <i class="fas fa-box text-secondary fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark mb-1">{{ $product->Name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">{{ $product->category->Name ?? '-' }}</span></td>
                                <td>
                                    <span class="badge rounded-pill {{ ($product->inventory->Quantity ?? 0) <= ($product->inventory->ReorderLevel ?? 0) ? 'bg-danger' : 'bg-success' }} px-2 py-1">
                                        {{ __('inventory.qty') }}: {{ $product->inventory->Quantity ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark px-2 py-1 border border-warning-subtle">
                                        {{ $product->inventory->ReorderLevel ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm">
                                        {{-- Edit Reorder Level Button --}}
                                        <button class="btn btn-sm btn-light text-warning border" data-bs-toggle="modal"
                                            data-bs-target="#adjustStockreorderModal{{ $product->ProductID }}" title="{{ __('inventory.btn_change_reorder') }}">
                                            <i class="fas fa-sliders-h"></i>
                                        </button>

                                        {{-- Adjust Stock Button --}}
                                        <button class="btn btn-sm btn-light text-primary border" data-bs-toggle="modal"
                                            data-bs-target="#adjustStockModal{{ $product->ProductID }}" title="{{ __('inventory.btn_adjust_stock') }}">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Adjust Stock Modal --}}
                            <div class="modal fade" id="adjustStockModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-boxes text-primary me-2"></i>{{ __('inventory.modal_adjust_title') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">
                                            <div class="modal-body p-4">
                                                <div class="alert alert-info border-info-subtle bg-info bg-opacity-10 text-info-emphasis mb-4">
                                                    {{ __('inventory.current_stock') }}: <strong>{{ $product->inventory->Quantity ?? 0 }}</strong>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('inventory.modal_action_label') }} <span class="text-danger">*</span></label>
                                                        <select name="action" class="form-select">
                                                            <option value="add">{{ __('inventory.modal_add_stock') }} (+)</option>
                                                            <option value="subtract">{{ __('inventory.modal_subtract_stock') }} (-)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('inventory.modal_qty_label') }} <span class="text-danger">*</span></label>
                                                        <input type="number" name="quantity" class="form-control" min="1" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted">{{ __('inventory.modal_reason_label') }}</label>
                                                        <input type="text" name="reason" class="form-control" placeholder="{{ __('inventory.modal_reason_placeholder') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('inventory.btn_cancel') }}</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('inventory.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Reorder Level Modal --}}
                            <div class="modal fade" id="adjustStockreorderModal{{ $product->ProductID }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-light border-bottom-0">
                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('inventory.btn_change_reorder') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('inventory.updatereorder') }}" method="GET">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->ProductID }}">
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">{{ __('inventory.reorder_level') }} <span class="text-danger">*</span></label>
                                                    <input type="number" name="reorder_level" class="form-control"
                                                           value="{{ $product->inventory->ReorderLevel ?? 0 }}" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top-0">
                                                <button type="button" class="btn btn-outline-secondary fw-bold px-4" data-bs-dismiss="modal">{{ __('inventory.btn_cancel') }}</button>
                                                <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('inventory.btn_save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted bg-white">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <i class="fas fa-boxes fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-medium text-dark">{{ __('inventory.no_products') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-4">
                {{-- Pagination Placeholder if needed later --}}
            </div>
        </div>
    </div>
@endsection
