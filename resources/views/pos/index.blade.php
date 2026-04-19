@extends('layouts.app')

@section('title', __('POS Terminal'))

@section('content')
    <div class="row h-100">
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
                            <select name="CategoryID" class="form-select">
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
                                    <i class="fas fa-sync-alt"></i> {{ __('pos.clear_btn') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body overflow-auto" style="height: 70vh;">
                    <div class="row g-3" id="productGrid">
                        @foreach ($products as $product)
                            <div class="col-md-3 col-6 product-card">
                                <div class="card h-100 border-0 shadow-sm btn-add-cart" style="cursor: pointer;"
                                    data-id="{{ $product->ProductID }}" data-name="{{ $product->Name }}"
                                    data-price="{{ $product->SellPrice }}"
                                    data-stock="{{ $product->inventory->Quantity }}">
                                    <div class="card-body text-center p-2">
                                        <img src="{{ asset('storage/' . $product->Image) }}" alt="{{ $product->Name }}"
                                            class="img-fluid mb-2">
                                        <h6 class="card-title fw-bold">{{ $product->Name }}</h6>
                                        <div class="text-primary">{{ __('pos.price') }}:
                                            ${{ number_format($product->SellPrice, 2) }}</div>
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
                <div class="card-body p-0 d-flex flex-column" style="height: 70vh;">
                    <div class="p-3 border-bottom bg-light">
                        <label class="small fw-bold text-muted mb-1">{{ __('pos.customer_label') }}</label>
                        <div class="input-group">
                            <select class="form-select" id="customer_id">
                                <option value="">{{ __('pos.general_customer') }}</option>
                                @foreach ($customers as $customer)
                                    @if ($customer->has_debt)
                                        <option value="{{ $customer->CustomerID }}"
                                            style="color:#dc3545;font-weight:bold;">
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
                        <table class="table align-middle table-hover">
                            <thead class="bg-light small text-muted">
                                <tr>
                                    <th>{{ __('pos.product_col') }}</th>
                                    <th class="text-center" width="60">{{ __('pos.qty_col') }}</th>
                                    <th class="text-end" width="80">{{ __('pos.price_col') }}</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable"></tbody>
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
                    <div class="text-center mb-4">
                        <small class="text-muted text-uppercase fw-bold">{{ __('pos.total_to_pay') }}</small>
                        <h1 class="display-4 fw-bold text-success" id="modalTotalDisplay">$0.00</h1>
                    </div>

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
                    <div id="qrPaymentSection" class="d-none text-center">
                        {{-- Loading state --}}
                        <div id="qrLoading" class="py-4">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <p class="text-muted">{{ __('pos.generating_qr') }}</p>
                        </div>

                        {{-- QR display --}}
                        <div id="qrDisplay" class="d-none">
                            <p class="mb-1 fw-bold text-primary">{{ __('pos.scan_khqr_to_pay') }}</p>
                            <div class="border p-2 rounded shadow-sm d-inline-block mb-2">
                                <canvas id="qrCanvas"></canvas>
                            </div>
                            <p class="text-muted small mb-1">
                                <span id="qrAccountDisplay"></span>
                            </p>
                            {{-- Polling status --}}
                            <div id="qrWaiting" class="alert alert-info py-2 mb-0">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                {{ __('pos.waiting_payment') }} (<span id="qrCountdown">60</span>s)
                            </div>
                            <div id="qrPaid" class="alert alert-success py-2 mb-0 d-none">
                                <i class="fas fa-check-circle me-2"></i> {{ __('pos.payment_success') }}
                            </div>
                            <div id="qrExpired" class="alert alert-warning py-2 mb-0 d-none">
                                QR Code {{ __('pos.qr_expired') }} — <a href="#"
                                    id="btnRetryQr">{{ __('pos.click_to_regenerate') }}</a>
                            </div>
                        </div>

                        {{-- Error --}}
                        <div id="qrError" class="d-none">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <span id="qrErrorMsg">{{ __('pos.cannot_generate_qr') }}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-primary"
                                id="btnRetryQrError">{{ __('pos.try_again') }}</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- QR code renderer (qrcode.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>

    <script>
        $(document).ready(function() {
            let cart = [];
            let currentTotal = 0;
            let qrMd5 = null; // MD5 hash from Bakong for polling
            let qrPollingTimer = null; // setInterval reference
            let qrCountdownTimer = null;
            let qrConfirmed = false; // becomes true once Bakong confirms payment

            // ─────────────────────────────────────────────────────────
            // Toast helper
            // ─────────────────────────────────────────────────────────
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 900,
                timerProgressBar: true,
                didOpen: t => {
                    t.onmouseenter = Swal.stopTimer;
                    t.onmouseleave = Swal.resumeTimer;
                }
            });

            // ─────────────────────────────────────────────────────────
            // Add to cart
            // ─────────────────────────────────────────────────────────
            $(".btn-add-cart").click(function() {
                let id = $(this).data("id");
                let name = $(this).data("name");
                let price = parseFloat($(this).data("price"));
                let stock = parseInt($(this).data("stock"));

                if (stock <= 0) {
                    Toast.fire({
                        icon: "error",
                        title: "{{ __('pos.out_of_stock') }}"
                    });
                    return;
                }

                let item = cart.find(i => i.id === id);
                if (item) {
                    if (item.qty >= stock) {
                        Toast.fire({
                            icon: "warning",
                            title: `${name} {{ __('pos.stock_limit') }} ${stock}`
                        });
                        return;
                    }
                    item.qty++;
                    Toast.fire({
                        icon: "success",
                        title: "{{ __('pos.added') }}"
                    });
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        qty: 1,
                        stock
                    });
                    Toast.fire({
                        icon: "success",
                        title: "{{ __('pos.add_product') }}"
                    });
                }
                renderCart();
            });

            // ─────────────────────────────────────────────────────────
            // Render cart
            // ─────────────────────────────────────────────────────────
            function renderCart() {
                let html = "",
                    total = 0;
                cart.forEach((item, index) => {
                    let sub = item.price * item.qty;
                    total += sub;
                    html += `<tr>
                    <td>${item.name}<br><small>$${item.price}</small></td>
                    <td class="text-center">x${item.qty}</td>
                    <td class="text-end fw-bold">$${sub.toFixed(2)}</td>
                    <td><button class="btn btn-sm text-danger btn-remove" data-index="${index}">X</button></td>
                </tr>`;
                });
                $("#cartTable").html(html);
                $("#cartTotal").text("$" + total.toFixed(2));
            }

            $(document).on("click", ".btn-remove", function() {
                cart.splice($(this).data("index"), 1);
                renderCart();
            });

            // ─────────────────────────────────────────────────────────
            // Add customer
            // ─────────────────────────────────────────────────────────
            $('#btn_save_customer').click(function() {
                let name = $('#new_customer_name').val().trim();
                let phone = $('#new_customer_phone').val().trim();
                if (!name || !phone) {
                    Toast.fire({
                        icon: "warning",
                        title: "Please fill in all fields"
                    });
                    return;
                }
                $.ajax({
                    url: "{{ route('customers.store.ajax') }}",
                    type: "POST",
                    data: {
                        name,
                        phone,
                        _token: "{{ csrf_token() }}"
                    },
                    success(res) {
                        bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'))
                            ?.hide();
                        $('#customer_id').append(new Option(`${res.name} (${res.phone})`, res.id,
                            true, true));
                        $('#new_customer_name,#new_customer_phone').val('');
                        Toast.fire({
                            icon: "success",
                            title: "{{ __('pos.added') }}"
                        });
                    },
                    error(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error', 'error');
                    }
                });
            });

            // ─────────────────────────────────────────────────────────
            // Customer debt check
            // ─────────────────────────────────────────────────────────
            $('#customer_id').change(function() {
                let id = $(this).val();
                if (!id) return;
                $.get(`/pos/customer-debt/${id}`, function(res) {
                    if (res.has_debt) {
                        Swal.fire({
                            title: 'អតិថិជននេះមានជំពាក់ប្រាក់!',
                            text: res.message,
                            icon: 'warning',
                            confirmButtonText: 'យល់ព្រម',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // ─────────────────────────────────────────────────────────
            // Checkout button
            // ─────────────────────────────────────────────────────────
            $("#btnCheckout").click(function() {
                if (cart.length === 0) {
                    Toast.fire({
                        icon: "warning",
                        title: "មិនមានទំនិញ"
                    });
                    return;
                }

                currentTotal = parseFloat($("#cartTotal").text().replace('$', ''));
                qrConfirmed = false;
                let payType = $("#paymentType").val();

                $("#modalTotalDisplay").text("$" + currentTotal.toFixed(2));
                $("#cashPaymentSection").addClass('d-none');
                $("#qrPaymentSection").addClass('d-none');
                $("#btnConfirmPayment").prop('disabled', false);

                if (payType === 'QR') {
                    // KHQR flow
                    $("#qrPaymentSection").removeClass('d-none');
                    $("#btnConfirmPayment").prop('disabled', true); // disabled until payment confirmed
                    startKhqrFlow(currentTotal);
                } else {
                    // Cash / Card flow
                    $("#cashPaymentSection").removeClass('d-none');
                    $("#txtReceivedAmount").val(currentTotal).trigger('keyup');
                    setTimeout(() => $("#txtReceivedAmount").select(), 500);
                }

                bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal')).show();
            });

            // ─────────────────────────────────────────────────────────
            // Cash: change calculation
            // ─────────────────────────────────────────────────────────
            $("#txtReceivedAmount").on('keyup change', function() {
                let received = parseFloat($(this).val()) || 0;
                let diff = received - currentTotal;
                if (diff < 0) {
                    $("#changeLabel").text("ជំពាក់ប្រាក់ (Debt) $");
                    $("#txtChangeAmount").val(Math.abs(diff).toFixed(2)).addClass('text-danger')
                        .removeClass('text-success');
                } else {
                    $("#changeLabel").text("លុយអាប់ (Change) $");
                    $("#txtChangeAmount").val(diff.toFixed(2)).removeClass('text-danger').addClass(
                        'text-success');
                }
                $("#btnConfirmPayment").prop('disabled', false);
            });

            // ─────────────────────────────────────────────────────────
            // KHQR: generate QR and start polling
            // ─────────────────────────────────────────────────────────
            function startKhqrFlow(amount) {
                cancelQrPolling();
                qrMd5 = null;
                qrConfirmed = false;

                // Reset UI
                $("#qrLoading").removeClass('d-none');
                $("#qrDisplay,#qrError").addClass('d-none');
                $("#qrWaiting").removeClass('d-none');
                $("#qrPaid,#qrExpired").addClass('d-none');

                $.ajax({
                    url: "{{ route('pos.khqr.generate') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        amount: amount,
                        currency: 'USD'
                    },
                    success(res) {
                        if (res.status === 'success') {
                            qrMd5 = res.md5;
                            renderQrCanvas(res.qr);
                            startPolling();
                        } else {
                            showQrError(res.message || 'មិនអាចបង្កើត QR');
                        }
                    },
                    error() {
                        showQrError('Server error — could not generate QR');
                    }
                });
            }

            function renderQrCanvas(qrString) {
                $("#qrLoading").addClass('d-none');
                $("#qrDisplay").removeClass('d-none');
                $("#qrAccountDisplay").text("{{ config('khqr.account') }}");

                let canvas = document.getElementById('qrCanvas');
                QRCode.toCanvas(canvas, qrString, {
                    width: 240,
                    margin: 2
                }, function(err) {
                    if (err) showQrError('QR render failed');
                });
            }

            function showQrError(msg) {
                $("#qrLoading").addClass('d-none');
                $("#qrDisplay").addClass('d-none');
                $("#qrError").removeClass('d-none');
                $("#qrErrorMsg").text(msg);
            }

            // Poll every 3 seconds for up to 60 seconds
            function startPolling() {
                let secondsLeft = 60;
                $("#qrCountdown").text(secondsLeft);

                qrCountdownTimer = setInterval(function() {
                    secondsLeft--;
                    $("#qrCountdown").text(secondsLeft);
                    if (secondsLeft <= 0) expireQr();
                }, 1000);

                qrPollingTimer = setInterval(function() {
                    if (!qrMd5) return;
                    $.ajax({
                        url: "{{ route('pos.khqr.check') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            md5: qrMd5
                        },
                        success(res) {
                            if (res.paid) {
                                onQrPaid();
                            }
                        }
                    });
                }, 3000);
            }

            function onQrPaid() {
                cancelQrPolling();
                qrConfirmed = true;
                $("#qrWaiting").addClass('d-none');
                $("#qrPaid").removeClass('d-none');
                $("#btnConfirmPayment").prop('disabled', false);

                // Auto-confirm after 1.5s
                setTimeout(function() {
                    processCheckout('QR', currentTotal, $('#customer_id').val(), true);
                }, 1500);
            }

            function expireQr() {
                cancelQrPolling();
                $("#qrWaiting").addClass('d-none');
                $("#qrExpired").removeClass('d-none');
            }

            window.cancelQrPolling = function() {
                clearInterval(qrPollingTimer);
                clearInterval(qrCountdownTimer);
                qrPollingTimer = null;
                qrCountdownTimer = null;
            };

            $('#btnRetryQr,#btnRetryQrError').click(function(e) {
                e.preventDefault();
                startKhqrFlow(currentTotal);
            });

            // ─────────────────────────────────────────────────────────
            // Confirm button (Cash / Card only — QR auto-confirms)
            // ─────────────────────────────────────────────────────────
            $("#btnConfirmPayment").click(function() {
                let payType = $("#paymentType").val();
                let received = parseFloat($("#txtReceivedAmount").val()) || 0;
                let customerId = $('#customer_id').val();

                // QR should auto-confirm, but allow manual confirm if already paid
                if (payType === 'QR') {
                    if (!qrConfirmed) {
                        Toast.fire({
                            icon: "warning",
                            title: "សូមរង់ចាំការទូទាត់ KHQR!"
                        });
                        return;
                    }
                    processCheckout('QR', currentTotal, customerId, true);
                    return;
                }

                if (received < currentTotal && !customerId) {
                    Toast.fire({
                        icon: "error",
                        title: "ជ្រើសរើសអតិថិជន ដើម្បីកត់ត្រាការជំពាក់!"
                    });
                    return;
                }

                if (received < currentTotal) {
                    Swal.fire({
                        title: 'ទូទាត់មិនគ្រប់?',
                        text: `អតិថិជននឹងជំពាក់ $${(currentTotal - received).toFixed(2)}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'បាទ/ចាស លក់ជំពាក់',
                        cancelButtonText: 'បោះបង់'
                    }).then(r => {
                        if (r.isConfirmed) processCheckout(payType, received, customerId, false);
                    });
                } else {
                    processCheckout(payType, received, customerId, false);
                }
            });

            // ─────────────────────────────────────────────────────────
            // Submit order to server
            // ─────────────────────────────────────────────────────────
            function processCheckout(paymentType, receivedAmount, customerId, paymentConfirmed) {
                $("#btnConfirmPayment").prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: "{{ route('pos.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cart: cart,
                        customer_id: customerId,
                        total_amount: currentTotal,
                        payment_type: paymentType,
                        paid_amount: receivedAmount,
                        payment_confirmed: paymentConfirmed ? 1 : 0,
                    },
                    success(res) {
                        if (res.status === 'success') {
                            cancelQrPolling();
                            bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                            Swal.fire({
                                title: 'ជោគជ័យ',
                                text: res.message + (res.payment_status === 'Partial' ?
                                    ' (ជំពាក់)' : ''),
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.open("/pos/receipt/" + res.order_id, "_blank",
                                    "width=620,height=800");
                                location.reload();
                            });
                        } else {
                            Swal.fire("Error", res.message, "error");
                            $("#btnConfirmPayment").prop('disabled', false)
                                .html('<i class="fas fa-check me-2"></i> បញ្ជាក់');
                        }
                    },
                    error() {
                        Swal.fire("Error", "Something went wrong", "error");
                        $("#btnConfirmPayment").prop('disabled', false)
                            .html('<i class="fas fa-check me-2"></i> បញ្ជាក់');
                    }
                });
            }

            // Stop polling when modal is closed
            $('#paymentModal').on('hide.bs.modal', function() {
                cancelQrPolling();
                $('#txtReceivedAmount').blur();
            });
        });
    </script>
@endsection
