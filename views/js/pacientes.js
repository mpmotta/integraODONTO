$(document).ready(function() {
    $('.btn-excluir-paciente').on('click', function() {
        if(confirm('Tem certeza que deseja excluir este paciente?')) {
            var id = $(this).data('id');
            var btn = $(this);

            $.post('?url=pacientes/excluir', { id: id }, function(response) {
                var res = JSON.parse(response);
                if(res.status === 'success') {
                    $('#linha-paciente-' + id).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Erro ao excluir paciente.');
                }
            });
        }
    });

    $('.mask-cpf').on('input', function() {
        var v = $(this).val().replace(/\D/g,"");
        v = v.replace(/(\d{3})(\d)/,"$1.$2");
        v = v.replace(/(\d{3})(\d)/,"$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/,"$1-$2");
        $(this).val(v);
    });

    $('.mask-telefone').on('input', function() {
        var v = $(this).val().replace(/\D/g,"");
        v = v.replace(/^(\d{2})(\d)/g,"($1) $2");
        v = v.replace(/(\d)(\d{4})$/,"$1-$2");
        $(this).val(v);
    });

    $('.mask-cep').on('input', function() {
        var v = $(this).val().replace(/\D/g,"");
        v = v.replace(/^(\d{5})(\d)/,"$1-$2");
        $(this).val(v);
    });
});
