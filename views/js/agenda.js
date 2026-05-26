$(document).ready(function() {
    function toggleTipoAgendamento() {
        if ($('#tipo_agendamento').val() === 'Tratamento') {
            $('#div_procedimento_unico').hide();
            $('#div_nome_tratamento').show();
            $('input[name="nome_tratamento"]').attr('required', true);
        } else {
            $('#div_procedimento_unico').show();
            $('#div_nome_tratamento').hide();
            $('input[name="nome_tratamento"]').removeAttr('required');
        }
    }

    $('#tipo_agendamento').on('change', toggleTipoAgendamento);
    toggleTipoAgendamento();

    $('.btn-excluir-consulta').on('click', function() {
        if(confirm('Tem certeza que deseja cancelar e excluir este agendamento?')) {
            var id = $(this).data('id');
            
            $.post('?url=agenda/excluir', { id: id }, function(response) {
                var res = JSON.parse(response);
                if(res.status === 'success') {
                    $('#linha-consulta-' + id).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Erro ao excluir agendamento.');
                }
            });
        }
    });

    $('.select-status-consulta').on('change', function() {
        var id = $(this).data('id');
        var novoStatus = $(this).val();
        var selectElement = $(this);
        
        selectElement.prop('disabled', true);
        
        $.post('?url=agenda/alterarStatus', { id: id, status: novoStatus }, function(response) {
            var res = JSON.parse(response);
            if(res.status === 'success') {
                selectElement.prop('disabled', false);
                if (novoStatus === 'Concluido') {
                    alert('Consulta concluida. Se ainda nao houver lancamento, o registro financeiro pendente foi gerado automaticamente.');
                }
            } else {
                alert('Erro ao atualizar status.');
                selectElement.prop('disabled', false);
            }
        });
    });

    $('.mask-dinheiro').on('input', function() {
        var v = $(this).val().replace(/\D/g, '');
        v = (v/100).toFixed(2) + '';
        v = v.replace(".", ",");
        v = v.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
        v = v.replace(/(\d)(\d{3}),/g, "$1.$2,");
        $(this).val(v);
    });
});
