(function() {
    const config = window.usersPageConfig || {};
    const routes = config.routes || {};
    const messages = config.messages || {};

    function showErrorMessages(errors) {
        let errorMsg = '';
        Object.keys(errors).forEach(key => {
            if (errors[key] && errors[key][0]) {
                errorMsg += errors[key][0] + '<br>';
            }
        });
        Swal.fire({
            icon: 'error',
            title: messages.validationError || 'Validation Error',
            html: errorMsg
        });
    }

    function toggleField(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function deleteUser(id) {
        Swal.fire({
            title: messages.confirmDelete || 'Are you sure?',
            text: messages.deleteWarning || 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: messages.deleteConfirmBtn || 'Yes, delete it!',
            cancelButtonText: messages.cancelBtn || 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(routes.delete || '', { id: id }, function(res) {
                    Swal.fire(messages.deletedMsg || 'Deleted', messages.deletedMsg || 'Deleted', 'success')
                        .then(() => location.reload());
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const email = $(this).data('email');
            const roleID = $(this).data('roleid');

            $('#update_id').val(id);
            $('#update_name').val(name);
            $('#update_email').val(email);
            $('#update_role').val(roleID);
        });

        $('.btn_save_user').click(function() {
            const data = {
                name: $('#add_name').val(),
                email: $('#add_email').val(),
                password: $('#add_password').val(),
                password_confirmation: $('#add_password_confirmation').val(),
                role: $('#add_role').val()
            };

            $.post(routes.store || '', data, function(res) {
                if (res.status === 'success' || res === 'success') {
                    Swal.fire(messages.success || 'Success', messages.successAdd || 'User added successfully', 'success')
                        .then(() => location.reload());
                }
            }).fail(function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showErrorMessages(xhr.responseJSON.errors);
                } else {
                    Swal.fire(messages.error || 'Error', xhr.responseJSON?.message || messages.error || 'An error occurred', 'error');
                }
            });
        });

        window.deleteUser = deleteUser;
        window.toggleField = toggleField;
    });
})();
