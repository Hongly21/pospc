(function() {
    const config = window.posIndexConfig || {};
    const messages = config.messages || {};
    const routes = config.routes || {};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function getSelectedTaxRate() {
        const selectedOption = $('#tax_id option:selected');
        return parseFloat(selectedOption.data('rate')) || 0;
    }

    function getSelectedTaxId() {
        const taxId = $('#tax_id').val();
        return taxId ? Number(taxId) : null;
    }

    function getCartSubtotal() {
        return cart.reduce((total, item) => total + (parseFloat(item.price) * parseInt(item.qty)), 0);
    }

    function getCheckoutTotals() {
        const subtotal = getCartSubtotal();
        const rate = getSelectedTaxRate();
        const taxAmount = subtotal * (rate / 100);
        const grandTotal = subtotal + taxAmount;

        return { subtotal, taxAmount, grandTotal, rate };
    }

    function initSearch(element) {
        const $element = $(element);
        let placeholderText = messages.selectPlaceholder || 'Select';
        const emptyOptionText = $element.find('option[value=""]').first().text().trim();
        if (emptyOptionText) {
            placeholderText = emptyOptionText;
        }
        const allowClear = $element.is('#tax_id');

        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholderText,
            allowClear,
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
        const isTabletOrSmaller = window.innerWidth <= 992;
        const totals = getCheckoutTotals();
        const taxRate = totals.rate;
        const grandTotal = totals.grandTotal;

        cart.forEach((item, index) => {
            const base = item.price * item.qty;
            const taxAmount = (base * taxRate) / 100;
            const sub = base + taxAmount;
            const attrHtml = item.attributes ? `<br><small class="text-muted" style="font-size:0.75rem;">${item.attributes}</small>` : '';
            const taxHtml = taxRate ? `<br><small class="text-muted" style="font-size:0.75rem;">Tax: ${taxRate.toFixed(2)}% (+$${taxAmount.toFixed(2)})</small>` : '';
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
        $('#summarySubtotal').text('$' + totals.subtotal.toFixed(2));
        $('#summaryTaxAmount').text('$' + totals.taxAmount.toFixed(2));
        $('#summaryGrandTotal').text('$' + grandTotal.toFixed(2));
        $('#cartTotal').text('$' + grandTotal.toFixed(2));
        lastUpdatedCartId = null;
        updateProductCards(cart);
    }

    function resetPosAfterSale() {
        cart = [];
        currentTotal = 0;
        lastUpdatedCartId = null;
        qrMd5 = null;
        qrConfirmed = false;
        qrCheckFailures = 0;
        $('#customer_id').val('');
        $('#paymentType').val('Cash');
        $('#tax_id').val('').trigger('change');
        renderCart();
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
                tax_id: getSelectedTaxId(),
                payment_type: paymentType,
                paid_amount: receivedAmount,
                payment_confirmed: paymentConfirmed ? 1 : 0
            },
            success(res) {
                if (res.status === 'success') {
                    cancelQrPolling();
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                    resetPosAfterSale();
                    Swal.fire({
                        title: messages.success || 'Success',
                        text: res.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.open('/pos/receipt/' + res.order_id, '_blank', 'width=620,height=800');
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
        cancelQrPolling();
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
        let secondsLeft = 60;
        qrCheckFailures = 0;
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
                global: false,
                data: {
                    _token: csrfToken,
                    md5: qrMd5
                },
                success(res) {
                    if (res.error) {
                        qrCheckFailures++;
                        if (qrCheckFailures >= 3) {
                            showQrError(res.error || (messages.serverErrorQr || 'Unable to check QR payment.'));
                        }
                        return;
                    }

                    if (res.paid) {
                        onQrPaid();
                    }
                },
                error() {
                    qrCheckFailures++;
                    if (qrCheckFailures >= 3) {
                        showQrError(messages.serverErrorQr || 'Server error checking QR payment.');
                    }
                }
            });
        }, 3000);
    }

    function startKhqrFlow(amount) {
        cancelQrPolling();
        qrMd5 = null;
        qrConfirmed = false;
        qrCheckFailures = 0;

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
    let qrCheckFailures = 0;
    let searchDebounceTimer = null;
    let searchRequest = null;

    function toggleSearchReset(hasFilters) {
        $('#posSearchReset').toggleClass('d-none', !hasFilters);
    }

    function fetchProducts(params) {
        if (searchRequest) {
            searchRequest.abort();
        }

        searchRequest = $.ajax({
            url: routes.search || '',
            type: 'GET',
            data: params,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        return searchRequest;
    }

    function searchProducts() {
        const search = $('#posSearchInput').val().trim();
        const categoryId = $('#posCategoryFilter').val();
        const params = {};

        if (search) {
            params.search = search;
        }
        if (categoryId) {
            params.CategoryID = categoryId;
        }

        toggleSearchReset(Boolean(search || categoryId));
        $('#productGrid').addClass('opacity-50');

        fetchProducts(params)
            .done(function(res) {
                $('#productGrid').html(res.html);
                updateProductCards(cart);
            })
            .fail(function(xhr) {
                if (xhr.statusText === 'abort') {
                    return;
                }
                showToast({ icon: 'error', title: messages.somethingWentWrong || 'Something went wrong.' });
            })
            .always(function() {
                $('#productGrid').removeClass('opacity-50');
                searchRequest = null;
            });
    }

    function resetProductSearch() {
        $('#posSearchInput').val('');
        $('#posCategoryFilter').val('');
        if ($('#posCategoryFilter').hasClass('select2-hidden-accessible')) {
            $('#posCategoryFilter').trigger('change.select2');
        }
        toggleSearchReset(false);
        searchProducts();
    }

    function addProductToCart(card) {
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
        const attributes = card.data('attributes');

        lastUpdatedCartId = id;
        const item = cart.find(i => i.id === id);
        if (item) {
            item.qty++;
        } else {
            cart.push({ id, name, price, qty: 1, stock, attributes });
        }
        renderCart();
    }

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

        $(document).on('click', '.btn-add-cart', function() {
            addProductToCart($(this));
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

        $('#posSearchForm').on('submit', function(e) {
            e.preventDefault();
            searchProducts();
        });

        $('#posSearchInput').on('keyup', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(searchProducts, 400);
        });

        $('#posCategoryFilter').on('change', function() {
            searchProducts();
        });

        $('#tax_id').on('change', function() {
            renderCart();
        });

        $('#posSearchReset').on('click', function() {
            resetProductSearch();
        });

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

            const totals = getCheckoutTotals();
            currentTotal = totals.grandTotal;
            qrConfirmed = false;
            const payType = getPayType();
            const subtotal = totals.subtotal;
            const taxTotal = totals.taxAmount;

            $('#modalSubtotalDisplay').text('$' + subtotal.toFixed(2));
            $('#modalTaxDisplay').text('$' + taxTotal.toFixed(2));
            $('#modalTotalDisplay').text('$' + currentTotal.toFixed(2));
            $('#cashPaymentSection').addClass('d-none');
            $('#qrPaymentSection').addClass('d-none');
            $('#btnConfirmPayment').prop('disabled', false);

            if (payType === 'QR') {
                $('#qrPaymentSection').removeClass('d-none');
                $('#btnConfirmPayment').prop('disabled', true);
                $('#paymentSummary').css('display', 'none');
                startKhqrFlow(currentTotal);
            } else {
                $('#cashPaymentSection').removeClass('d-none');
                $('#paymentSummary').css('display', 'block');
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
