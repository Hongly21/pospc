@extends('layouts.app')

@section('title', __('dead_stock.title'))
@section('content')
    <div class="card shadow mb-4 border-0 no-print">
        <div class="card-body">
            <form action="{{ route('reports.dead_stock') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ __('dead_stock.months_label') }}</label>
                    <input type="number" name="months" class="form-control form-control-sm" min="1" max="60"
                        value="{{ $months }}" required>
                </div>
                <div class="col-md-7">
                    <p class="small text-muted mb-0">
                        {{ __('dead_stock.cutoff_hint', ['date' => $cutoffDate->format('d-M-Y')]) }}
                    </p>
                </div>
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('taxes.btn_search') }}</button>
                    <a href="{{ route('reports.dead_stock') }}" class="btn btn-sm btn-outline-secondary px-3 ms-2">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('dead_stock.dead_items') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($deadStockCount) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-box-open fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100 py-2 border-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('dead_stock.total_capital') }}
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-danger">
                                ${{ number_format($totalCapitalTiedUp, 2) }}
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-piggy-bank fa-2x text-danger opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">{{ __('dead_stock.title') }}</h6>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print">
                <i class="fas fa-print"></i> {{ __('print') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">{{ __('Product Name') }}</th>
                            <th class="text-center">{{ __('Stock Left') }}</th>
                            <th class="text-end">{{ __('dead_stock.cost_price') }}</th>
                            <th class="text-end">{{ __('dead_stock.sell_price') }}</th>
                            <th class="text-end">{{ __('dead_stock.capital_tied_up') }}</th>
                            <th class="text-center pe-3 no-print">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                $qty = $product->inventory->Quantity ?? 0;
                                $capitalTiedUp = $product->CostPrice * $qty;
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold">{{ $product->Name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2">
                                        {{ number_format($qty) }}
                                    </span>
                                </td>
                                <td class="text-end text-muted">${{ number_format($product->CostPrice, 2) }}</td>
                                <td class="text-end text-success fw-medium">${{ number_format($product->SellPrice, 2) }}</td>
                                <td class="text-end fw-bold text-danger">${{ number_format($capitalTiedUp, 2) }}</td>
                                <td class="text-center pe-3 no-print">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger markdown-price-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#markdownPriceModal"
                                        data-product-id="{{ $product->ProductID }}"
                                        data-product-name="{{ $product->Name }}"
                                        data-sell-price="{{ $product->SellPrice }}"
                                        data-cost-price="{{ $product->CostPrice }}">
                                        <i class="fas fa-tags"></i> {{ __('dead_stock.markdown_price') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">{{ __('no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($products, 'links'))
                <div class="d-flex justify-content-start mt-3 px-3 pb-3">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade no-print" id="markdownPriceModal" tabindex="-1" aria-labelledby="markdownPriceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="markdownPriceForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="markdownPriceModalLabel">{{ __('dead_stock.modal_title') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('cancel') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1 fw-bold" id="markdownProductName"></p>
                        <p class="small text-muted mb-3">{{ __('dead_stock.modal_hint') }}</p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">{{ __('dead_stock.cost_price') }}</label>
                                <div class="fw-medium" id="markdownCostPrice"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-0">{{ __('dead_stock.sell_price') }}</label>
                                <div class="fw-medium text-success" id="markdownCurrentPrice"></div>
                            </div>
                        </div>
                        <label for="markdownSellPrice" class="form-label fw-bold">{{ __('dead_stock.new_sell_price') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="SellPrice" id="markdownSellPrice"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-check"></i> {{ __('dead_stock.save_price') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('markdownPriceForm');
                const sellPriceInput = document.getElementById('markdownSellPrice');

                document.querySelectorAll('.markdown-price-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.productId;
                        const productName = this.dataset.productName;
                        const sellPrice = parseFloat(this.dataset.sellPrice);
                        const costPrice = parseFloat(this.dataset.costPrice);

                        form.action = "{{ url('/products') }}/" + productId + "/sell-price";
                        document.getElementById('markdownProductName').textContent = productName;
                        document.getElementById('markdownCostPrice').textContent = '$' + costPrice.toFixed(2);
                        document.getElementById('markdownCurrentPrice').textContent = '$' + sellPrice.toFixed(2);
                        sellPriceInput.value = sellPrice.toFixed(2);
                    });
                });
            });
        </script>
    @endpush
@endsection
