document.addEventListener('DOMContentLoaded', function() {
    const config = window.expensesIndexConfig || {};
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: config.swalTitle || 'Confirm Delete',
                text: config.swalText || 'Are you sure you want to delete this expense?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: config.swalConfirmBtn || 'Delete',
                cancelButtonText: config.swalCancelBtn || 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        });
    });
});
