(function() {
    const config = window.authLoginConfig || {};

    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
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

    document.addEventListener('DOMContentLoaded', function() {
        if (config.loginSuccess) {
            let timerInterval;
            Swal.fire({
                title: config.successTitle || 'Login Successful!',
                html: config.successMessage || 'Loading your dashboard in <b></b> milliseconds.',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector('b');
                    timerInterval = setInterval(() => {
                        if (timer) {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                        }
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then(() => {
                window.location.href = config.redirectUrl || '/';
            });
        }
    });

    window.togglePassword = togglePassword;
})();
