document.addEventListener('DOMContentLoaded', function() {
    const formCadastro = document.getElementById('cadastro-form');

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio padrão

            const button = formCadastro.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Aguarde...';

            // FormData pega todos os campos do formulário com o atributo "name"
            const formData = new FormData(formCadastro);

            // Usamos fetch para enviar os dados para a nossa rota da API
            fetch('/RHease/public/register', {
                method: 'POST',
                body: formData // Enviando como FormData
            })
                .then(response => response.json()) // Esperamos uma resposta JSON
                .then(result => {
                    if (result.status === 'success') {
                        // Se o PHP retornar sucesso, redirecionamos
                        window.location.href = '/RHease/public/registro-sucesso';
                    } else {
                        // Se der erro, mostramos a mensagem do PHP
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
});