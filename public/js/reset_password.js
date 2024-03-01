document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reset-password-form');
    const email = localStorage.getItem('email');
    if (email) {
        document.getElementById('email').value = email;
    }

    document.getElementById('reset-password-form').addEventListener('submit', function (event) {
        let newPassword = document.getElementById('newPassword').value;
        let password = document.getElementById('password').value;


        if (newPassword !== password) {
            event.preventDefault(); // Предотвращаем отправку формы
            const modal = new bootstrap.Modal(document.getElementById('myModal'));
            document.getElementById('modalTitle').textContent = 'Ошибка';
            document.getElementById('modalBody').textContent = 'Пароли не совпадают';
            modal.show();
        }
    });
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                token: formData.get('token'),
                password: formData.get('password')
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        };

        let responseStatus = null;
        fetch('/api/reset', requestOptions)
            .then(response => {
                responseStatus = response.status;
                return response.json();
            })
            .then(data => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                switch (responseStatus) {
                    case 200:
                        window.location.href = '/login';
                        break;

                    case 404:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Пользователь с такой почтой не найден';
                        modal.show();
                        break;

                    default:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent = 'Не удалось сбросить пароль';
                        modal.show();
                        break;
                }
            })
            .catch(error => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                document.getElementById('modalTitle').textContent = 'Ошибка';
                document.getElementById('modalBody').textContent = 'Сброс пароля невозможен';
                modal.show();
            });
    });
});