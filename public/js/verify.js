document.addEventListener('DOMContentLoaded', function () {
    const emailField = document.getElementById('email');
    const storedEmail = localStorage.getItem('emailForVerification');
    if (storedEmail) {
        emailField.value = storedEmail; // Устанавливаем email из localStorage как значение по умолчанию
    }
    const form = document.getElementById('verify-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const requestOptions = {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                token: formData.get('token'),
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        };

        let responseStatus = null;
        fetch('/api/verify', requestOptions)
            .then(response => {
                responseStatus = response.status;
                return response.json();
            })
            .then(data => {
                switch (responseStatus) {
                    case 200:
                        window.location.href = '/login'
                        break;

                    default:
                        const modal = new bootstrap.Modal(document.getElementById('myModal'));
                        document.getElementById('modalTitle').textContent = 'Ошибка';
                        document.getElementById('modalBody').textContent =
                            'Ошибочная почта или токен, проверьте данные';
                        modal.show();
                        break;
                }
            })
            .catch(error => {
                const modal = new bootstrap.Modal(document.getElementById('myModal'));
                document.getElementById('modalTitle').textContent = 'Ошибка';
                document.getElementById('modalBody').textContent = 'Верификация невозможна';
                modal.show();
            });
    });
});

