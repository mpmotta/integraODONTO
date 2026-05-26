$(document).ready(function() {
    $('.btn-excluir-usuario').on('click', function() {
        if(confirm('Tem certeza que deseja desativar este usuario? Ele perdera o acesso ao sistema.')) {
            var id = $(this).data('id');

            $.post('?url=usuarios/excluir', { id: id }, function(response) {
                var res = JSON.parse(response);
                if(res.status === 'success') {
                    $('#linha-usuario-' + id).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert(res.message || 'Erro ao excluir usuario.');
                }
            });
        }
    });
});
