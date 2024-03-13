document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('[name="reset"]').addEventListener('click', function () {
        window.location.href = '/reset_code';
    });
    document.querySelector('[name="reg"]').addEventListener('click', function () {
        window.location.href = '/register';
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('auth-form');
    const email = localStorage.getItem('email');
    if (email) {
        document.getElementById('email').value = email;
    }
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
            }),

            headers: {
                'Content-Type': 'application/json'
            }
        };

        let responseStatus = null;
        fetch('/api/login', requestOptions)
            .then(response => {
                responseStatus = response.status;
                return response.json();
            })
            .then(data => {
                switch (responseStatus) {
                    case 200:
                        document.cookie = `token=${data.token}`;
                        window.location.href = '/home_page'
                        break;

                    default:
                        const modal = new bootstrap.Modal(document.getElementById('myModal'));
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Не удалось зайти под данной почтой и паролем, пожалуйста проверьте данные';
                        modal.show();
                        break;
                }
            })
            .catch(error => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                document.getElementById('modalTitle').textContent = 'Ошибка';
                document.getElementById('modalBody').textContent = 'Авторизация невозможна';
                modal.show();
            });
    });
});

