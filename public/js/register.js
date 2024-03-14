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

                if (responseStatus !== 200) {

                    document.getElementById('modalTitle').textContent = 'Ошибка';
                    switch (responseStatus) {

                        case 409:
                            document.getElementById('modalBody').textContent =
                                'A user with this email already exists';
                            break;

                        case 400:
                            const errorMessage = data['message'].split('\n').join('<br>');
                            document.getElementById('modalBody').innerHTML = errorMessage;
                            break;
                    }

                    modal.show();
                    return;
                }

                localStorage.setItem('emailForVerification', formData.get('email'));
                window.location.href = '/verify'

            })
    });
});