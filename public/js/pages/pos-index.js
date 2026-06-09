(function() {
    const config = window.posIndexConfig || {};
    const messages = config.messages || {};
    const routes = config.routes || {};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function initSearch(element) {
        const $element = $(element);
        let placeholderText = messages.selectPlaceholder || 'Select';
        const emptyOptionText = $element.find('option[value=""]').first().text().trim();
        if (emptyOptionText) {
            placeholderText = emptyOptionText;
        }

        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholderText,
            dropdownParent: $element.parent()
        });
    }

    function showToast(options) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: t => {
                t.onmouseenter = Swal.stopTimer;
                t.onmouseleave = Swal.resumeTimer;
            }
        });
        Toast.fire(options);
    }

    function getPayType() {
        return $('#paymentType').val();
    }

    function updateProductCards(cart) {
        $('.btn-add-cart').each(function() {
            const id = $(this).data('id');
            const stock = parseInt($(this).data('stock'));
            const item = cart.find(i => i.id === id);

            if ((item && item.qty >= stock) || stock <= 0) {
                $(this).addClass('disabled-card').css({
                    opacity: '0.6',
                    cursor: 'not-allowed',
                    'background-color': '#f8f9fa'
                });
            } else {
                $(this).removeClass('disabled-card').css({
                    opacity: '1',
                    cursor: 'pointer',
                    'background-color': '#fff'
                });
            }
        });
    }


    function renderCart() {
        let html = '';
        let total = 0;
        const isTabletOrSmaller = window.innerWidth <= 992;

        cart.forEach((item, index) => {
            const base = item.price * item.qty;
            const taxAmount = (base * item.taxRate) / 100;
            const sub = base + taxAmount;
            total += sub;
            const attrHtml = item.attributes ? `<br><small class="text-muted" style="font-size:0.75rem;">${item.attributes}</small>` : '';
            const taxHtml = item.taxRate ? `<br><small class="text-muted" style="font-size:0.75rem;">Tax: ${item.taxRate.toFixed(2)}% (+$${taxAmount.toFixed(2)})</small>` : '';
            const rowClass = item.id === lastUpdatedCartId ? 'cart-row-anim' : '';

            if (isTabletOrSmaller) {
                html += `<tr class="${rowClass}"><td colspan="4">
                    <div class="cart-item-card">
                        <div class="cart-left">
                            <div class="fw-bold text-truncate">${item.name}${attrHtml}${taxHtml}</div>
                            <div><small>$${item.price}</small></div>
                        </div>
                        <div class="cart-right">
                            <div class="qty-control-group">
                                <button class="qty-btn btn-qty-dec" data-index="${index}" type="button">-</button>
                                <span class="qty-display">${item.qty}</span>
                                <button class="qty-btn btn-qty-inc" data-index="${index}" type="button">+</button>
                            </div>
                            <div class="text-end fw-bold">$${sub.toFixed(2)}</div>
                            <button class="btn btn-sm text-danger btn-remove" data-index="${index}" type="button"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </td></tr>`;
            } else {
                html += `<tr class="${rowClass}">
                    <td>${item.name}${attrHtml}${taxHtml}<br><small>$${item.price}</small></td>
                    <td class="text-center">
                        <div class="qty-control-group">
                            <button class="qty-btn btn-qty-dec" data-index="${index}" type="button">-</button>
                            <span class="qty-display">${item.qty}</span>
                            <button class="qty-btn btn-qty-inc" data-index="${index}" type="button">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-bold">$${sub.toFixed(2)}</td>
                    <td><button class="btn btn-sm text-danger btn-remove" data-index="${index}" type="button"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            }
        });

        $('#cartTable').html(html);
        $('#cartTotal').text('$' + total.toFixed(2));
        lastUpdatedCartId = null;
        updateProductCards(cart);
    }

    function processCheckout(paymentType, receivedAmount, customerId, paymentConfirmed) {
        $('#btnConfirmPayment').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + (messages.processing || 'Processing'));

        $.ajax({
            url: routes.checkout || '',
            type: 'POST',
            data: {
                _token: csrfToken,
                cart,
                customer_id: customerId,
                total_amount: currentTotal,
                payment_type: paymentType,
                paid_amount: receivedAmount,
                payment_confirmed: paymentConfirmed ? 1 : 0
            },
            success(res) {
                if (res.status === 'success') {
                    cancelQrPolling();
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                    Swal.fire({
                        title: messages.success || 'Success',
                        text: res.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.open('/pos/receipt/' + res.order_id, '_blank', 'width=620,height=800');
                        location.reload();
                    });
                } else {
                    Swal.fire(messages.error || 'Error', res.message, 'error');
                    $('#btnConfirmPayment').prop('disabled', false).html('<i class="fas fa-check me-2"></i> ' + (messages.confirm || 'Confirm'));
                }
            },
            error() {
                Swal.fire(messages.error || 'Error', messages.somethingWentWrong || 'Something went wrong', 'error');
                $('#btnConfirmPayment').prop('disabled', false).html('<i class="fas fa-check me-2"></i> ' + (messages.confirm || 'Confirm'));
            }
        });
    }

    function getPayUrlTemplate(amount) {
        return routes.addCustomer || '';
    }

    function showQrError(msg) {
        $('#qrLoading').addClass('d-none');
        $('#qrDisplay').addClass('d-none');
        $('#qrError').removeClass('d-none');
        $('#qrErrorMsg').text(msg);
    }

    function renderQrCanvas(qrString) {
        $('#qrLoading').addClass('d-none');
        $('#qrDisplay').removeClass('d-none');
        const canvas = document.getElementById('qrCanvas');
        QRCode.toCanvas(canvas, qrString, { width: 240, margin: 2 }, function(err) {
            if (err) showQrError(messages.qrRenderFailed || 'Failed to render QR code.');
        });
    }

    function cancelQrPolling() {
        clearInterval(qrPollingTimer);
        clearInterval(qrCountdownTimer);
        qrPollingTimer = null;
        qrCountdownTimer = null;
    }

    window.cancelQrPolling = cancelQrPolling;

    function expireQr() {
        cancelQrPolling();
        $('#qrWaiting').addClass('d-none');
        $('#qrExpired').removeClass('d-none');
    }

    function onQrPaid() {
        cancelQrPolling();
        qrConfirmed = true;
        $('#qrWaiting').addClass('d-none');
        $('#qrPaid').removeClass('d-none');
        $('#btnConfirmPayment').prop('disabled', false);
        setTimeout(function() {
            processCheckout('QR', currentTotal, $('#customer_id').val(), true);
        }, 1500);
    }

    function startPolling() {
        let secondsLeft = 300;
        $('#qrCountdown').text(secondsLeft);

        qrCountdownTimer = setInterval(function() {
            secondsLeft--;
            $('#qrCountdown').text(secondsLeft);
            if (secondsLeft <= 0) {
                expireQr();
            }
        }, 1000);

        qrPollingTimer = setInterval(function() {
            if (!qrMd5) return;
            $.ajax({
                url: routes.khqrCheck || '',
                type: 'POST',
                data: {
                    _token: csrfToken,
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

    function startKhqrFlow(amount) {
        cancelQrPolling();
        qrMd5 = null;
        qrConfirmed = false;

        $('#qrAmountDisplay').text('$' + amount.toFixed(2));
        $('#qrLoading').removeClass('d-none');
        $('#qrDisplay,#qrError').addClass('d-none');
        $('#qrWaiting').removeClass('d-none');
        $('#qrPaid,#qrExpired').addClass('d-none');

        $.ajax({
            url: routes.khqrGenerate || '',
            type: 'POST',
            data: {
                _token: csrfToken,
                amount: amount,
                currency: 'USD'
            },
            success(res) {
                if (res.status === 'success') {
                    qrMd5 = res.md5;
                    renderQrCanvas(res.qr);
                    startPolling();
                } else {
                    showQrError(res.message || (messages.cannotGenerateQr || 'Unable to generate QR code.'));
                }
            },
            error() {
                showQrError(messages.serverErrorQr || 'Server error generating QR.');
            }
        });
    }

    let cart = [];
    let currentTotal = 0;
    let lastUpdatedCartId = null;
    let qrMd5 = null;
    let qrPollingTimer = null;
    let qrCountdownTimer = null;
    let qrConfirmed = false;

    document.addEventListener('DOMContentLoaded', function() {
        $('.searchable-select').each(function() {
            initSearch(this);
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: t => {
                t.onmouseenter = Swal.stopTimer;
                t.onmouseleave = Swal.resumeTimer;
            }
        });

        $('.btn-add-cart').click(function() {
            const card = $(this);
            if (card.hasClass('disabled-card')) {
                card.removeClass('shake-anim');
                void card[0].offsetWidth;
                card.addClass('shake-anim');
                setTimeout(() => card.removeClass('shake-anim'), 400);
                return;
            }

            card.addClass('click-anim');
            setTimeout(() => card.removeClass('click-anim'), 200);

            const id = card.data('id');
            const name = card.data('name');
            const price = parseFloat(card.data('price'));
            const stock = parseInt(card.data('stock'));
            const taxRate = parseFloat(card.data('tax-rate')) || 0;
            const attributes = card.data('attributes');

            lastUpdatedCartId = id;
            const item = cart.find(i => i.id === id);
            if (item) {
                item.qty++;
            } else {
                cart.push({ id, name, price, qty: 1, stock, taxRate, attributes });
            }
            renderCart();
        });

        $(document).on('click', '.btn-remove', function() {
            cart.splice($(this).data('index'), 1);
            renderCart();
        });

        $(document).on('click', '.btn-qty-inc', function() {
            const index = Number($(this).data('index'));
            if (Number.isNaN(index) || !cart[index]) return;
            if (cart[index].qty >= cart[index].stock) {
                Swal.fire(messages.warning || 'Warning', `${cart[index].name} ${messages.hasOnlyStockOf || 'has only stock of'} ${cart[index].stock}`, 'warning');
                return;
            }
            cart[index].qty += 1;
            renderCart();
        });

        $(document).on('click', '.btn-qty-dec', function() {
            const index = Number($(this).data('index'));
            if (Number.isNaN(index) || !cart[index]) return;
            if (cart[index].qty <= 1) {
                cart.splice(index, 1);
            } else {
                cart[index].qty -= 1;
            }
            renderCart();
        });

        updateProductCards(cart);

        let _cartResizeTimer = null;
        $(window).on('resize', function() {
            clearTimeout(_cartResizeTimer);
            _cartResizeTimer = setTimeout(function() {
                renderCart();
            }, 150);
        });

        $('#btn_save_customer').click(function() {
            const name = $('#new_customer_name').val().trim();
            const phone = $('#new_customer_phone').val().trim();
            if (!name || !phone) {
                Swal.fire(messages.error || 'Error', messages.fillAllFields || 'Please fill all fields.', 'warning');
                return;
            }

            $.ajax({
                url: routes.addCustomer || '',
                type: 'POST',
                data: { name, phone, _token: csrfToken },
                success(res) {
                    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'))?.hide();
                    $('#customer_id').append(new Option(`${res.name} (${res.phone})`, res.id, true, true)).trigger('change');
                    $('#new_customer_name,#new_customer_phone').val('');
                    Swal.fire({
                        text: messages.added || 'Customer added successfully',
                        icon: 'success',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    // showToast({ icon: 'success', title: messages.added || 'Customer added successfully' });
                },
                error(xhr) {
                    Swal.fire(messages.error || 'Error', xhr.responseJSON?.message || messages.error || 'Error', 'error');
                }
            });
        });

        $('#customer_id').change(function() {
            const id = $(this).val();
            if (!id) return;
            $.get(`/pos/customer-debt/${id}`, function(res) {
                if (res.has_debt) {
                    Swal.fire({
                        title: messages.customerHasDebt || 'Customer has debt',
                        text: res.message,
                        icon: 'warning',
                        confirmButtonText: messages.ok || 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        $('#btnCheckout').click(function() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'warning', title: messages.cartEmpty || 'Cart is empty' });
                return;
            }

            currentTotal = parseFloat($('#cartTotal').text().replace('$', ''));
            qrConfirmed = false;
            const payType = getPayType();

            $('#modalTotalDisplay').text('$' + currentTotal.toFixed(2));
            $('#cashPaymentSection').addClass('d-none');
            $('#qrPaymentSection').addClass('d-none');
            $('#btnConfirmPayment').prop('disabled', false);

            if (payType === 'QR') {
                $('#qrPaymentSection').removeClass('d-none');
                $('#btnConfirmPayment').prop('disabled', true);
                startKhqrFlow(currentTotal);
            } else {
                $('#cashPaymentSection').removeClass('d-none');
                $('#txtReceivedAmount').val(currentTotal).trigger('keyup');
                setTimeout(() => $('#txtReceivedAmount').select(), 500);
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal')).show();
        });

        $('#txtReceivedAmount').on('keyup change', function() {
            const received = parseFloat($(this).val()) || 0;
            const diff = received - currentTotal;
            if (diff < 0) {
                $('#changeLabel').text(messages.debtAmountLabel || 'Debt Amount $');
                $('#txtChangeAmount').val(Math.abs(diff).toFixed(2)).addClass('text-danger').removeClass('text-success');
            } else {
                $('#changeLabel').text(messages.changeAmountLabel || 'Change Amount $');
                $('#txtChangeAmount').val(diff.toFixed(2)).removeClass('text-danger').addClass('text-success');
            }
            $('#btnConfirmPayment').prop('disabled', false);
        });

        $('#btnRetryQr,#btnRetryQrError').click(function(e) {
            e.preventDefault();
            startKhqrFlow(currentTotal);
        });

        $('#btnConfirmPayment').click(function() {
            const payType = getPayType();
            const received = parseFloat($('#txtReceivedAmount').val()) || 0;
            const customerId = $('#customer_id').val();

            if (payType === 'QR') {
                if (!qrConfirmed) {
                    showToast({ icon: 'warning', title: messages.waitKhqrPayment || 'Please wait for QR payment confirmation.' });
                    return;
                }
                processCheckout('QR', currentTotal, customerId, true);
                return;
            }

            if (received < currentTotal && !customerId) {
                Swal.fire({
                    title: messages.noCustomerDebt || 'No customer selected for debt',
                    text: messages.selectCustomerForDebt || 'Please select a customer if the payment is incomplete.',
                    icon: 'warning',
                    confirmButtonText: messages.ok || 'OK'
                });
                return;
            }

            if (received < currentTotal) {
                Swal.fire({
                    title: messages.insufficientPayment || 'Insufficient payment',
                    text: `${messages.customerWillOwe || 'Customer will owe'} $${(currentTotal - received).toFixed(2)}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: messages.yesSellDebt || 'Yes, sell on debt',
                    cancelButtonText: messages.cancel || 'Cancel'
                }).then(r => {
                    if (r.isConfirmed) processCheckout(payType, received, customerId, false);
                });
            } else {
                processCheckout(payType, received, customerId, false);
            }
        });

        $('#paymentModal').on('hide.bs.modal', function() {
            cancelQrPolling();
            $('#txtReceivedAmount').blur();
        });
    });
})();
