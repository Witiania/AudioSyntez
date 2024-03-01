document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reg-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                phone: formData.get('phone'),
                name: formData.get('name'),
                password: formData.get('password')
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        };
        let responseStatus = null;

        fetch('/api/register', requestOptions)
            .then(response => {
                responseStatus = response.status;
                return response.json();
            })
            .then(data => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                const error = data.message.split('\n');

                switch (responseStatus) {
                    case 200:
                        window.location.href = '/verify'//перенаправляем на главную страницу при успехе
                        break;

                    case 409:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Пользователь с данной почтой уже существует';
                        break;

                    case 400:
                        switch (error[0]) {
                            case 'Wrong email':
                                document.getElementById('modalTitle').textContent = 'Ошибка';
                                document.getElementById('modalBody').textContent =
                                    'Введен некорректный email';
                                break;

                            case 'Wrong name':
                                document.getElementById('modalTitle').textContent = 'Ошибка';
                                document.getElementById('modalBody').textContent =
                                    'Введено некорректное имя (минимальная длина 3 символа, маскимальная 20)';
                                break;

                            case 'Wrong password':
                                document.getElementById('modalTitle').textContent = 'Ошибка';
                                document.getElementById('modalBody').textContent =
                                    'Введен некорректный пароль';
                                break;

                            case 'Wrong phone':
                                document.getElementById('modalTitle').textContent = 'Ошибка';
                                document.getElementById('modalBody').textContent =
                                    'Введен некорретный номер телефона';
                                break;
                        }
                        break;

                    default:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Не удалось зарегистрировать пользователя';
                        break;
                }

                modal.show();
            })
            .catch(error => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                document.getElementById('modalTitle').textContent = 'Ошибка';
                document.getElementById('modalBody').textContent = 'Регистрация невозможна';
                modal.show();
            });
    });
});