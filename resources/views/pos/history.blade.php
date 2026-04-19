@extends('layouts.app')

@section('title', __('Sales History'))

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i> {{ __('Your Sales History') }}</span>
            <a href="{{ route('pos.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-calculator me-1"></i> {{ __('POS Terminal') }}
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('pos.history') }}" method="GET" class="row w-100 g-2 mb-4 d-print-none">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('Search Invoice or Customer...') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select border-primary text-dark">
                        <option value="" class="text-dark">{{ __('All Payment Status') }}</option>
                        <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }} class="text-success">
                            {{ __('Paid') }}</option>
                        <option value="Debt" {{ request('status') == 'Debt' ? 'selected' : '' }} class="text-danger">
                            {{ __('Unpaid/Debt') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="fas fa-filter fa-sm me-1"></i> {{ __('Search') }}
                    </button>
                    <a href="{{ route('pos.history') }}" class="btn btn-outline-danger px-3" title="{{ __('Clear Filter') }}">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('INV #') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-bold text-primary">
                                    #{{ str_pad($order->OrderID, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>{{ $order->created_at->format('d-M-Y h:i A') }}</td>
                                <td>{{ $order->customer->Name ?? __('General Customer') }}</td>
                                <td class="fw-bold text-success">${{ number_format($order->TotalAmount, 2) }}</td>
                                <td>
                                    @if ($order->PaymentType == 'Cash')
                                        <span class="badge bg-success"><i class="fas fa-money-bill-wave fa-sm me-1"></i> {{ __('Cash') }}</span>
                                    @elseif($order->PaymentType == 'QR')
                                        <span class="badge bg-info"><i class="fas fa-qrcode fa-sm me-1"></i> KHQR</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-credit-card fa-sm me-1"></i> {{ __('Card') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->Status == 'Paid')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="fas fa-check-circle fa-sm me-1"></i> {{ __('Completed') }}
                                        </span>
                                    @elseif($order->Status == 'Partial' || $order->Status == 'Unpaid')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            <i class="fas fa-exclamation-circle fa-sm me-1"></i> {{ __('Debt') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->Status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button onclick="window.open('{{ route('pos.receipt', $order->OrderID) }}', '_blank', 'width=620,height=800')"
                                        class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="fas fa-print fa-sm "></i> {{ __('Print Receipt') }}
                                    </button>

                                    @if ($order->Status == 'Partial' || $order->Status == 'Unpaid')
                                        @php
                                            $paidAlready = $order->receipts->sum('PaidAmount');
                                            $debt = max(0, $order->TotalAmount - $paidAlready);
                                        @endphp
                                        <button type="button" class="btn btn-sm btn-outline-warning mb-1 text-dark"
                                            onclick="openDebtModal({{ $order->OrderID }}, {{ $debt }}, '{{ $order->customer->Name ?? __('General Customer') }}')">
                                            <i class="fas fa-hand-holding-usd fa-sm me-2"></i> {{ __('Pay Debt') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-receipt fa-3x"></i></div>
                                    <h5 class="text-muted">{{ __('No sales history found') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="payDebtModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-dark">
                    <h6 class="modal-title fw-bold"><i class="fas fa-money-bill-wave me-2"></i> {{ __('Pay Remaining Debt') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center mt-2">
                    <p class="mb-1 text-muted">{{ __('Customer') }}: <strong id="debtCustomerName">...</strong></p>
                    <p class="mb-3">{{ __('Total Debt') }}: <strong class="text-danger fs-5" id="debtRemainingAmount">$0.00</strong></p>

                    <input type="hidden" id="debtOrderID">

                    <div class="form-group mb-3 text-start">
                        <label class="form-label small fw-bold">{{ __('Amount Received') }} ($)</label>
                        <input type="number" id="debtPaidAmount" class="form-control text-center fw-bold" placeholder="0.00" step="0.01">
                    </div>

                    <div class="form-group mb-3 text-start">
                        <label class="form-label small fw-bold">{{ __('Payment Method') }}</label>
                        <select id="debtPaymentMethod" class="form-select">
                            <option value="Cash">{{ __('Cash') }}</option>
                            <option value="QR">QR Scan</option>
                            <option value="Card">{{ __('Credit Card') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnSubmitDebtPayment">
                        {{ __('Confirm Payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openDebtModal(orderId, remainingDebt, customerName) {
            $('#debtOrderID').val(orderId);
            $('#debtCustomerName').text(customerName);
            $('#debtRemainingAmount').text('$' + parseFloat(remainingDebt).toFixed(2));
            $('#debtPaidAmount').val('');
            var myModal = new bootstrap.Modal(document.getElementById('payDebtModal'));
            myModal.show();
            setTimeout(() => { $('#debtPaidAmount').focus(); }, 500);
        }

        $('#btnSubmitDebtPayment').click(function() {
            let orderId = $('#debtOrderID').val();
            let paidAmount = $('#debtPaidAmount').val();
            let paymentMethod = $('#debtPaymentMethod').val();

            if (!paidAmount || paidAmount <= 0) {
                Swal.fire("{{ __('Warning') }}", "{{ __('Please enter a valid amount!') }}", 'warning');
                return;
            }

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}');

            $.ajax({
                url: "/pos/order/" + orderId + "/pay-debt",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    paid_amount: paidAmount,
                    payment_method: paymentMethod
                },
                success: function(res) {
                    if (res.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('payDebtModal')).hide();
                        Swal.fire({
                            title: "{{ __('Success!') }}",
                            text: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                        $('#btnSubmitDebtPayment').prop('disabled', false).text("{{ __('Confirm Payment') }}");
                    }
                }
            });
        });
    </script>
@endsection
