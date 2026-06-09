(function() {
    const config = window.posHistoryConfig || {};

    function getPayDebtUrl(orderId) {
        return (config.payDebtUrlTemplate || '').replace('{orderId}', orderId);
    }

    function openDebtModal(orderId, remainingDebt, customerName) {
        $('#debtOrderID').val(orderId);
        $('#debtCustomerName').text(customerName);
        $('#debtRemainingAmount').text('$' + parseFloat(remainingDebt).toFixed(2));
        $('#debtPaidAmount').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('payDebtModal')).show();
        setTimeout(() => {
            $('#debtPaidAmount').focus();
        }, 500);
    }

    window.openDebtModal = openDebtModal;

    document.addEventListener('DOMContentLoaded', function() {
        $('#btnSubmitDebtPayment').click(function() {
            const orderId = $('#debtOrderID').val();
            const paidAmount = $('#debtPaidAmount').val();
            const paymentMethod = $('#debtPaymentMethod').val();

            if (!paidAmount || paidAmount <= 0) {
                Swal.fire(config.messages?.invalidAmount || 'Please enter a valid amount!', '', 'warning');
                return;
            }

            const $button = $(this);
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + (config.messages?.processing || 'Processing...'));

            $.ajax({
                url: getPayDebtUrl(orderId),
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    paid_amount: paidAmount,
                    payment_method: paymentMethod
                },
                success(res) {
                    if (res.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('payDebtModal')).hide();
                        Swal.fire({
                            title: config.messages?.successTitle || 'Success!',
                            html: res.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            title: config.messages?.errorTitle || 'Error',
                            html: res.message,
                            icon: 'error'
                        });
                        $button.prop('disabled', false).text(config.messages?.confirmBtn || 'Confirm Payment');
                    }
                }
            });
        });
    });
})();
