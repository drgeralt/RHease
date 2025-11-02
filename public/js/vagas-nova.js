document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-nova-vaga');
    const submitButton = document.getElementById('btn-salvar-vaga');
    const messageDiv = document.getElementById('api-message');

    // Se o formulário não existir nesta página, não faz nada.
    if (!form) return; 

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.textContent = 'Salvando...';
        messageDiv.style.display = 'none';

        const formData = new FormData(form);
        const dadosVaga = Object.fromEntries(formData.entries());

        try {
            // A variável 'BASE_URL' vem do script global na view HTML
            const response = await fetch(`${BASE_URL}/api/vagas/salvar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dadosVaga)
            });

            const resultado = await response.json();

            if (!response.ok) {
                let erroMsg = 'Ocorreu um erro ao salvar.';
                if (resultado.erros) {
                    erroMsg = Object.values(resultado.erros).join('<br>');
                } else if (resultado.erro) {
                    erroMsg = resultado.erro;
                }
                throw new Error(erroMsg);
            }

            messageDiv.innerHTML = 'Vaga criada com sucesso! Redirecionando para a lista...';
            messageDiv.className = 'msg-sucesso';
            messageDiv.style.display = 'block';

            setTimeout(() => {
                window.location.href = `${BASE_URL}/vagas/listar`;
            }, 2000);

        } catch (error) {
            messageDiv.innerHTML = error.message;
            messageDiv.className = 'msg-erro';
            messageDiv.style.display = 'block';
            submitButton.disabled = false;
            submitButton.textContent = 'Salvar Vaga';
        }
    });
});