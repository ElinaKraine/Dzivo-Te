document.addEventListener('DOMContentLoaded', function () {
    const icon1 = document.getElementById('parslegtParole1');
    const icon2 = document.getElementById('parslegtParole2');
    const icon3 = document.getElementById('parslegtParole3');

    if (icon1) {
        icon1.addEventListener('click', function () {
            const parole1 = document.getElementById('parole1');
            if (parole1.type === 'password') {
                parole1.type = 'text';
                icon1.classList.add('fa-eye-slash');
                icon1.classList.remove('fa-eye');
            } else {
                parole1.type = 'password';
                icon1.classList.add('fa-eye');
                icon1.classList.remove('fa-eye-slash');
            }
        });
    }

    if (icon2) {
        icon2.addEventListener('click', function () {
            const parole2 = document.getElementById('parole2');
            if (parole2.type === 'password') {
                parole2.type = 'text';
                icon2.classList.add('fa-eye-slash');
                icon2.classList.remove('fa-eye');
            } else {
                parole2.type = 'password';
                icon2.classList.add('fa-eye');
                icon2.classList.remove('fa-eye-slash');
            }
        });
    }

    if (icon3) {
        icon3.addEventListener('click', function () {
            const parole3 = document.getElementById('parole3');
            if (parole3.type === 'password') {
                parole3.type = 'text';
                icon3.classList.add('fa-eye-slash');
                icon3.classList.remove('fa-eye');
            } else {
                parole3.type = 'password';
                icon3.classList.add('fa-eye');
                icon3.classList.remove('fa-eye-slash');
            }
        });
    }
});
