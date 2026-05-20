document.addEventListener('DOMContentLoaded', function() {
    const config = window.suppliersIndexConfig || {};
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: config.swalTitle || 'Delete Supplier',
                text: config.swalText || 'Are you sure you want to delete this supplier?',
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
