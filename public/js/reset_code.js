document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reset-code-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const email = formData.get('email');
        localStorage.setItem('email', email);
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        };

        let responseStatus = null;
        fetch('/api/send_for_reset', requestOptions)
            .then(response => {
                responseStatus = response.status;
                return response.json();
            })
            .then(data => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                switch (responseStatus) {
                    case 200:
                        window.location.href = '/reset_password';
                        break;

                    case 404:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Не удалось найти пользователя с такой почтой'
                        modal.show();
                        break;

                    default:
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Не удалось отправить код на вашу почту';
                        modal.show();
                        break;
                }
            })
            .catch(error => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                document.getElementById('modalTitle').textContent = 'Ошибка';
                document.getElementById('modalBody').textContent = 'Отправка кода невозможна';
                modal.show();
            });
    });
});
