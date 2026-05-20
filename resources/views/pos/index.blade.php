@extends('layouts.app')

@section('title', __('POS Terminal'))

@section('content')
    <div class="row h-100 ">
        {{-- =================== LEFT: PRODUCT GRID =================== --}}
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <form action="{{ route('pos.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('pos.search_placeholder') }}" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <select name="CategoryID" class="form-select searchable-select">
                                <option value="">{{ __('pos.all_categories') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->CategoryID }}"
                                        {{ request('CategoryID') == $cat->CategoryID ? 'selected' : '' }}>
                                        {{ $cat->Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 d-flex gap-2">
                            <button type="submit"
                                class="btn btn-outline-primary px-4 flex-grow-1">{{ __('pos.search_btn') }}</button>
                            @if (request()->filled('search') || request()->filled('CategoryID'))
                                <a href="{{ route('pos.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body overflow-auto pos-scrollable-area">
                    <div class="row g-3" id="productGrid">
                        @foreach ($products as $product)
                            <div class="col-6 col-sm-4 col-md-4 col-lg-3 product-card">
                                <div class="card h-100 border-0 shadow-sm btn-add-cart"
                                    data-id="{{ $product->ProductID }}" data-name="{{ $product->Name }}"
                                    data-price="{{ $product->SellPrice }}"
                                    data-stock="{{ $product->inventory->Quantity }}"
                                    data-tax-rate="{{ $product->tax ? $product->tax->Rate : $product->category->tax->Rate ?? 0 }}"
                                    data-attributes="{{ $product->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }}">
                                    <div class="card-body text-center p-3 p-sm-2">
                                        <img src="{{ asset('storage/' . $product->Image) }}" alt="{{ $product->Name }}"
                                            class="img-fluid mb-2 pos-product-img">
                                        <h6 class="card-title fw-bold">{{ $product->Name }}</h6>
                                        @if ($product->attributes->isNotEmpty())
                                            <small class="text-muted d-block mb-1">
                                                @foreach ($product->attributes->take(2) as $attribute)
                                                    <span>{{ $attribute->AttributeName }}:
                                                        {{ $attribute->AttributeValue }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                                @if ($product->attributes->count() > 2)
                                                    <span>...</span>
                                                @endif
                                            </small>
                                        @endif
                                        <div class="text-primary">{{ __('pos.price') }}:
                                            ${{ number_format($product->SellPrice, 2) }}</div>
                                        @if ($product->tax || $product->category->tax)
                                            <small class="text-muted d-block">{{ __('pos.tax_rate') }}:
                                                {{ number_format($product->tax?->Rate ?? ($product->category->tax?->Rate ?? 0), 2) }}%</small>
                                        @endif
                                        <small class="text-muted">{{ __('pos.stock') }}:
                                            {{ $product->inventory->Quantity }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{-- =================== RIGHT: CART =================== --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-black py-3 fw-bold bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('pos.current_order') }}</h5>
                </div>
                <div class="card-body p-0 d-flex flex-column pos-cart-body pos-scrollable-area">
                    <div class="p-3 border-bottom bg-light">
                        <label class="small fw-bold text-muted mb-1">{{ __('pos.customer_label') }}</label>
                        <div class="input-group flex-nowrap">
                            <select class="form-select searchable-select" id="customer_id">
                                <option value="">{{ __('pos.general_customer') }}</option>
                                @foreach ($customers as $customer)
                                    @if ($customer->has_debt)
                                        <option value="{{ $customer->CustomerID }}" class="debt-customer-option">
                                            {{ $customer->Name }} {{ $customer->PhoneNumber }} ({{ __('pos.has_debt') }})
                                        </option>
                                    @else
                                        <option value="{{ $customer->CustomerID }}">
                                            {{ $customer->Name }} ({{ $customer->PhoneNumber }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#addCustomerModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive flex-grow-1 p-2">
                        <table class="table align-middle table-hover pos-cart-table  ">
                            <thead class="bg-light small text-muted">
                                <tr>
                                    <th>{{ __('pos.product_col') }}</th>
                                    <th class="text-center" width="60">{{ __('pos.qty_col') }}</th>
                                    <th class="text-end" width="80">{{ __('pos.price_col') }}</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">

                            </tbody>
                        </table>
                    </div>
                    <div class="bg-white p-3 border-top shadow-sm">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('pos.total_label') }}</span>
                            <span class="fs-4 fw-bold text-primary" id="cartTotal">$0.00</span>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">{{ __('pos.method_label') }}</label>
                            <select class="form-select" id="paymentType">
                                <option value="Cash">{{ __('pos.cash') }}</option>
                                <option value="QR">{{ __('pos.qr_khqr') }}</option>
                                <option value="Card">{{ __('pos.card') }}</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-success w-100 py-3 fw-bold text-uppercase" id="btnCheckout">
                            <i class="fas fa-print me-2"></i> {{ __('pos.checkout_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== ADD CUSTOMER MODAL =================== --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('pos.add_new_customer') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">{{ __('pos.customer_name') }}</label>
                        <input type="text" id="new_customer_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">{{ __('pos.customer_phone') }}</label>
                        <input type="text" id="new_customer_phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100"
                        id="btn_save_customer">{{ __('pos.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== PAYMENT MODAL =================== --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('pos.payment_process') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnClosePaymentModal"></button>
                </div>
                <div class="modal-body">
                    {{-- <div class="text-center mb-4">
                        <small class="text-muted text-uppercase fw-bold">{{ __('pos.total_to_pay') }}</small>
                        <h1 class="display-4 fw-bold text-success" id="modalTotalDisplay">$0.00</h1>
                    </div> --}}

                    {{-- CASH section --}}
                    <div id="cashPaymentSection" class="d-none">
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">{{ __('pos.received_amount') }}</label>
                            <input type="number" id="txtReceivedAmount"
                                class="form-control form-control-lg text-center fw-bold" placeholder="0.00"
                                step="0.01">
                        </div>
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1" id="changeLabel">{{ __('pos.change_amount') }}</label>
                            <input type="text" id="txtChangeAmount"
                                class="form-control form-control-lg text-center fw-bold text-success" value="0.00"
                                readonly>
                        </div>
                    </div>

                    {{-- QR / KHQR section --}}
                    <div id="qrPaymentSection" class="d-none d-flex justify-content-center">
                        <div class="khqr-card text-center">
                            <div class="khqr-card-header">
                                <span class="khqr-brand">KHQR</span>
                            </div>
                            <div class="khqr-card-body">
                                <div id="qrLoading" class="py-5">
                                    <div class="spinner-border text-primary mb-3" role="status"></div>
                                    <p class="text-muted mb-0">{{ __('pos.generating_qr') }}</p>
                                </div>

                                <div id="qrDisplay" class="d-none">
                                    <p class="text-muted fw-bold mb-2 qr-merchant-name" id="qrMerchantName">{{ config('khqr.merchant_name') }}</p>
                                    {{-- <p class="text-muted fw-bold small mb-1" id="qrMerchantName">{{ config('khqr.merchant_name') }}</p> --}}
                                    <div class="khqr-amount mb-3 text-dark" id="qrAmountDisplay">$0.00</div>
                                    <div class="khqr-qr-shell d-inline-block mb-3 p-3 bg-white rounded-4 shadow-sm">
                                        <canvas id="qrCanvas"></canvas>
                                    </div>
                                    {{-- <p class="text-muted small mb-2" id="qrAccountDisplay"></p> --}}

                                    <div id="qrWaiting" class="khqr-status d-flex align-items-center justify-content-center mx-auto">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        <span>{{ __('pos.waiting_payment') }} (<span id="qrCountdown">60</span>s)</span>
                                    </div>
                                    <div id="qrPaid" class="alert alert-success py-2 mb-0 d-none">
                                        <i class="fas fa-check-circle me-2"></i> {{ __('pos.payment_success') }}
                                    </div>
                                    <div id="qrExpired" class="alert alert-warning py-2 mb-0 d-none">
                                        QR Code {{ __('pos.qr_expired') }} — <a href="#" id="btnRetryQr">{{ __('pos.click_to_regenerate') }}</a>
                                    </div>
                                </div>

                                <div id="qrError" class="d-none">
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <span id="qrErrorMsg">{{ __('pos.cannot_generate_qr') }}</span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" id="btnRetryQrError">{{ __('pos.try_again') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        onclick="cancelQrPolling()">{{ __('pos.close') }}</button>
                    <button type="button" class="btn btn-outline-success fw-bold px-4" id="btnConfirmPayment">
                        <i class="fas fa-check me-2"></i> {{ __('pos.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet" href="{{ asset('css/pages/pos-index.css') }}" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        {{-- QR code renderer (qrcode.js) --}}
        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>

        <script>
            window.posIndexConfig = {
                routes: {
                    addCustomer: "{{ route('customers.store.ajax') }}",
                    khqrGenerate: "{{ route('pos.khqr.generate') }}",
                    khqrCheck: "{{ route('pos.khqr.check') }}",
                    checkout: "{{ route('pos.store') }}"
                },
                messages: {
                    selectPlaceholder: "{{ __('Select') }}",
                    warning: "{{ __('pos.warning') }}",
                    hasOnlyStockOf: "{{ __('pos.has_only_stock_of') }}",
                    cartEmpty: "{{ __('pos.cart_empty') }}",
                    added: "{{ __('pos.added') }}",
                    error: "{{ __('pos.error') }}",
                    fillAllFields: "{{ __('pos.fill_all_fields') }}",
                    customerHasDebt: "{{ __('pos.customer_has_debt') }}",
                    ok: "{{ __('pos.ok') }}",
                    waitKhqrPayment: "{{ __('pos.wait_khqr_payment') }}",
                    noCustomerDebt: "{{ __('pos.no_customer_debt') }}",
                    selectCustomerForDebt: "{{ __('pos.select_customer_for_debt') }}",
                    insufficientPayment: "{{ __('pos.insufficient_payment') }}",
                    customerWillOwe: "{{ __('pos.customer_will_owe') }}",
                    yesSellDebt: "{{ __('pos.yes_sell_debt') }}",
                    cancel: "{{ __('cancel') }}",
                    processing: "{{ __('pos.processing') }}",
                    success: "{{ __('pos.success') }}",
                    confirm: "{{ __('pos.confirm') }}",
                    cannotGenerateQr: "{{ __('pos.cannot_generate_qr') }}",
                    serverErrorQr: "{{ __('pos.server_error_qr') }}",
                    qrRenderFailed: "{{ __('pos.qr_render_failed') }}",
                    somethingWentWrong: "{{ __('pos.something_went_wrong') }}",
                    debtAmountLabel: "{{ __('pos.debt_amount') }} $",
                    changeAmountLabel: "{{ __('pos.change_amount') }} $"
                }
            };
        </script>
        <script src="{{ asset('js/pages/pos-index.js') }}"></script>
    @endpush
@endsection
