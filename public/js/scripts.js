document.addEventListener('DOMContentLoaded', function() {
    const formDemissao = document.querySelector('.form-demissao');
    if (formDemissao) {
        formDemissao.addEventListener('submit', function(event) {
            const funcionarioSelect = document.getElementById('funcionario_id');
            const nomeFuncionario = funcionarioSelect.options[funcionarioSelect.selectedIndex].text;
            const confirmacao = confirm(`Você tem certeza que deseja processar a demissão para ${nomeFuncionario.trim()}?`);
            if (!confirmacao) {
                event.preventDefault();
            }
        });
    }

    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Você tem certeza?',
                text: "Esta ação não poderá ser revertida!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});