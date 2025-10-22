document.addEventListener('DOMContentLoaded', function() {
    const formCadastro = document.getElementById('cadastro-form');

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio padrão

            const button = formCadastro.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Aguarde...';

            const formData = new FormData(formCadastro);

            fetch('/RHease/public/register', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        window.location.href = '/RHease/public/registro-sucesso';
                    } else {
                        alert('Erro no cadastro: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na comunicação:', error);
                    alert('Ocorreu um erro de comunicação. Tente novamente.');
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'Cadastrar';
                });
        });
    }
    const togglePasswordIcons = document.querySelectorAll('.password-toggle-icon');

    // Para cada ícone encontrado, adiciona a funcionalidade
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordInput = this.closest('.input-wrapper').querySelector('input');
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeOffIcon = this.querySelector('.eye-off-icon');

            if (passwordInput && eyeIcon && eyeOffIcon) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'password') {
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                } else {
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                }
            }
        });
    });

});