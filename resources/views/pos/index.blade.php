@extends('layouts.app')

@section('title', ' POS ផ្ទាំងលក់')

@section('content')
    <div class="row h-100">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <form action="{{ route('pos.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="ស្វែងរកតាមឈ្មោះ ឬ Barcode..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <select name="CategoryID" class="form-select">
                                <option value="">ប្រភេទទាំងអស់ (All Category)</option>
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
                                ស្វែងរក
                            </button>
                            {{-- @if (request()->has('search') && request('search') != '')
                                <a href="{{ route('pos.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-sync-alt"></i> សម្អាត
                                </a>
                            @endif --}}
                            @if (request()->filled('search') || request()->filled('CategoryID'))
                                <a href="{{ route('pos.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-sync-alt"></i> សម្អាត
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body overflow-auto" style="height: 70vh;">
                    <div class="row g-3" id="productGrid">
                        @foreach ($products as $product)
                            <div class="col-md-3 col-6 product-card" data-name="{{ strtolower($product->Name) }}">
                                <div class="card h-100 border-0 shadow-sm btn-add-cart" style="cursor: pointer;"
                                    data-id="{{ $product->ProductID }}" data-name="{{ $product->Name }}"
                                    data-price="{{ $product->SellPrice }}">
                                    <div class="card-body text-center p-2">
                                        <img src="{{ asset($product->Image) }}" alt="{{ $product->Name }}"
                                            class="img-fluid mb-2">
                                        <h6 class="card-title fw-bold">{{ $product->Name }}</h6>
                                        <div class="text-primary">តម្លៃ: ${{ number_format($product->SellPrice, 2) }}</div>
                                        <small class="text-muted">ស្តុក: {{ $product->inventory->Quantity }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-black py-3 fw-bold bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">បញ្ជីលក់ (Current Order)</h5>
                </div>
                <div class="card-body p-0 d-flex flex-column" style="height: 70vh;">
                    <div class="p-3 border-bottom bg-light">
                        <label class="small fw-bold text-muted mb-1">អតិថិជន (Customer)</label>
                        <div class="input-group">
                            <select class="form-select" id="customer_id">
                                <option value="">អតិថិជនទូទៅ (General)</option>
                                @foreach ($customers as $customer)
                                    @if ($customer->has_debt)
                                        <option value="{{ $customer->CustomerID }}"
                                            style="color: #dc3545; font-weight: bold;">
                                            {{ $customer->Name }} {{ $customer->PhoneNumber }} (នៅជំពាក់)
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
                                    <th>ទំនិញ </th>
                                    <th class="text-center" width="60">ចំនួន</th>
                                    <th class="text-end" width="80">តម្លៃ</th>
                                    <th width="30"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white p-3 border-top shadow-sm">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">សរុប (Total)</span>
                            <span class="fs-4 fw-bold text-primary" id="cartTotal">$0.00</span>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Method</label>
                            <select class="form-select" id="paymentType">
                                <option value="Cash"> ប្រាក់សុទ្ធ (Cash)</option>
                                <option value="QR"> ស្កេន QR (KHQR)</option>
                                <option value="Card"> កាត (Credit Card)</option>
                            </select>
                        </div>

                        <button class="btn btn-outline-success w-100 py-3 fw-bold text-uppercase" id="btnCheckout">
                            <i class="fas fa-print me-2"></i> គិតប្រាក់ (Checkout)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addCustomerModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-outline-primary text-dark">
                        <h6 class="modal-title">បញ្ចូលអតិថិជនថ្មី</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small">ឈ្មោះ </label>
                            <input type="text" id="new_customer_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">លេខទូរស័ព្ទ</label>
                            <input type="text" id="new_customer_phone" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100"
                            id="btn_save_customer">រក្សាទុក</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- payment modal  --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-outline-success text-dark">
                    <h5 class="modal-title">ដំណើរការនៃការទូទាត់ប្រាក់</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <small class="text-muted text-uppercase fw-bold">ចំនួនទឺកប្រាក់សរុបដែលទូទាត់</small>
                        <h1 class="display-4 fw-bold text-success" id="modalTotalDisplay">$0.00</h1>
                    </div>

                    <div id="cashPaymentSection" class="d-none">
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">ប្រាក់ទទួល ($)</label>
                            <input type="number" id="txtReceivedAmount"
                                class="form-control form-control-lg text-center fw-bold" placeholder="0.00"
                                step="0.01">
                        </div>
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1" id="changeLabel">លុយអាប់ ($)</label>
                            <input type="text" id="txtChangeAmount"
                                class="form-control form-control-lg text-center fw-bold text-success" value="0.00"
                                readonly>
                        </div>
                        <div id="qrPaymentSection" class="d-none text-center">
                            <p class="mb-2 fw-bold text-primary">សូមស្កេនដើម្បីទូទាត់</p>
                            <img src="{{ asset('Uploads/products/qr.jpg') }}" alt="QR Code"
                                class="img-fluid border p-2 rounded shadow-sm" style="max-height: 290px;">
                            <p class="mt-2 text-muted small">កំពុងរងចាំការទូទាត់...</p>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">បិទ</button>
                    <button type="button" class="btn btn-outline-success fw-bold px-4" id="btnConfirmPayment">
                        <i class="fas fa-check me-2"></i> បញ្ជាក់
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let cart = [];

            $(".btn-add-cart").click(function() {
                let id = $(this).data("id");
                let name = $(this).data("name");
                let price = parseFloat($(this).data("price"));
                let stock = parseInt($(this).data("stock"));

                if (stock <= 0) {
                    Toast.fire({
                        icon: "error",
                        title: "ទំនិញនេះអស់ស្តុកហើយ! (Out of Stock)"
                    });
                    return;
                }

                let item = cart.find(i => i.id === id);

                if (item) {
                    if (item.qty >= stock) {
                        Toast.fire({
                            icon: "warning",
                            title: `សុំទោស! ${name} មានស្តុកត្រឹមតែ ${stock} ប៉ុណ្ណោះ!`
                        });
                        return;
                    }

                    item.qty++;
                    Toast.fire({
                        icon: "success",
                        title: "បានបន្ថែមចំនួនទំនិញ"
                    });
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        qty: 1,
                        stock: stock
                    });
                    Toast.fire({
                        icon: "success",
                        title: "ទំនិញបានបញ្ចុលក្នុង Cart"
                    });
                }
                renderCart();
            });

            $('#btn_save_customer').click(function() {
                var name = $('#new_customer_name').val();
                var phone = $('#new_customer_phone').val();

                if (name.trim() == '' || phone.trim() == '') {
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
                        name: name,
                        phone: phone,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        var addCusModal = bootstrap.Modal.getInstance(document.getElementById(
                            'addCustomerModal'));
                        if (addCusModal) addCusModal.hide();

                        var newOption = new Option(response.name + ' (' + response.phone + ')',
                            response.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');

                        $('#new_customer_name').val('');
                        $('#new_customer_phone').val('');


                        Toast.fire({
                            icon: "success",
                            title: "បានបន្ថែមអតិថិជនជោគជ័យ"
                        });
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON.message || 'មានបញ្ហាក្នុងការបន្ថែម!!';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function renderCart() {
                let html = "";
                let total = 0;
                cart.forEach((item, index) => {
                    let subtotal = item.price * item.qty;
                    total += subtotal;
                    html += `<tr>
                    <td>${item.name}<br><small>$${item.price}</small></td>
                    <td>x${item.qty}</td>
                    <td class="text-end fw-bold">$${subtotal.toFixed(2)}</td>
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

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 900,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            let currentTotal = 0;
            $("#btnCheckout").click(function() {
                if (cart.length === 0) {
                    Toast.fire({
                        icon: "warning",
                        title: "មិនមានទំនិញក្នុង Cart ឡើយ"
                    });
                    return;
                }

                let totalText = $("#cartTotal").text().replace('$', '');
                currentTotal = parseFloat(totalText);
                let paymentType = $("#paymentType").val();

                $("#modalTotalDisplay").text("$" + currentTotal.toFixed(2));

                $("#cashPaymentSection").removeClass('d-none');

                $("#txtReceivedAmount").val(currentTotal).trigger('keyup');

                if (paymentType === 'QR') {
                    $("#qrPaymentSection").removeClass('d-none');
                } else {
                    $("#qrPaymentSection").addClass('d-none');
                }

                var paymentModal = bootstrap.Modal.getOrCreateInstance(document.getElementById(
                    'paymentModal'));
                paymentModal.show();

                setTimeout(() => {
                    $("#txtReceivedAmount").select();
                }, 500);
            });

            $(".btn-quick-cash").click(function() {
                let val = $(this).data('value');
                $("#txtReceivedAmount").val(val).trigger('keyup');
            });


            $('#customer_id').change(function() {
                let customerId = $(this).val();
                if (customerId) {
                    $.get(`/pos/customer-debt/${customerId}`, function(res) {
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
                }
            });

            $("#txtReceivedAmount").on('keyup change', function() {
                let received = parseFloat($(this).val()) || 0;
                let difference = received - currentTotal;

                if (difference < 0) {
                    $("#changeLabel").text("ជំពាក់ប្រាក់ (Debt) $");
                    $("#txtChangeAmount").val(Math.abs(difference).toFixed(2))
                        .addClass('text-danger').removeClass('text-success');

                    $("#btnConfirmPayment").prop('disabled', false);
                } else {
                    $("#changeLabel").text("លុយអាប់ (Change) $");
                    $("#txtChangeAmount").val(difference.toFixed(2))
                        .removeClass('text-danger').addClass('text-success');
                    $("#btnConfirmPayment").prop('disabled', false);
                }
            });

            $("#btnConfirmPayment").click(function() {
                let paymentType = $("#paymentType").val();
                let receivedAmount = parseFloat($("#txtReceivedAmount").val()) || 0;
                let customerId = $('#customer_id').val();

                if (receivedAmount < currentTotal && !customerId) {
                    Toast.fire({
                        icon: "error",
                        title: "សូមជ្រើសរើសឈ្មោះអតិថិជន ដើម្បីកត់ត្រាការជំពាក់ប្រាក់!"
                    });
                    return;
                }

                if (receivedAmount < currentTotal) {
                    Swal.fire({
                        title: 'ទូទាត់មិនគ្រប់ចំនួន?',
                        text: `អតិថិជននឹងជំពាក់ប្រាក់សរុប $${(currentTotal - receivedAmount).toFixed(2)}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'បាទ/ចាស លក់ជំពាក់',
                        cancelButtonText: 'បោះបង់'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processCheckout(paymentType, receivedAmount, customerId);
                        }
                    });
                } else {
                    processCheckout(paymentType, receivedAmount, customerId);
                }
            });

            function processCheckout(paymentType, receivedAmount, customerId) {
                $("#btnConfirmPayment").prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

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
                    },
                    success: function(res) {
                        if (res.status === 'success') {

                            var myModal = bootstrap.Modal.getInstance(document.getElementById(
                                'paymentModal'));
                            if (myModal) {
                                myModal.hide();
                            }

                            Swal.fire({
                                title: 'ជោគជ័យ',
                                text: res.message + (res.payment_status === 'Partial' ?
                                    ' (ជំពាក់)' : ''),
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.open("/pos/receipt/" + res.order_id, "_blank",
                                    "width=400,height=600");
                                location.reload();
                            });
                        } else {
                            Swal.fire("Error", res.message, "error");
                            $("#btnConfirmPayment").prop('disabled', false).html(
                                '<i class="fas fa-check me-2"></i> បញ្ជាក់');
                        }
                    },
                    error: function(err) {
                        Swal.fire("Error", "Something went wrong" . err.message, "error");
                        $("#btnConfirmPayment").prop('disabled', false).html(
                            '<i class="fas fa-check me-2"></i> បញ្ជាក់');
                    }
                });
            }

            $('#paymentModal').on('hide.bs.modal', function() {
                $('#txtReceivedAmount').blur();
            });

        });
    </script>
@endsection
