document.addEventListener('DOMContentLoaded', function() {
    const formCadastro = document.getElementById('cadastro-form');

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(event) {
            event.preventDefault();

            const data = {
                nome_completo: document.getElementById('fullname').value,
                cpf: document.getElementById('cpf').value,
                email_profissional: document.getElementById('email').value,
                senha: document.getElementById('password').value
            };

            fetch('/RHease/public/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        // Redireciona para a nova página de sucesso
                        window.location.href = '/RHease/public/registro-sucesso';
                    } else {
                        // Se der erro, mostra o alerta
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Ocorreu um erro de comunicação.');
                });
        });
    }
});