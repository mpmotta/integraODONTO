$(document).ready(function() {
    var modalPagamento = new bootstrap.Modal(document.getElementById('modalPagamento'));

    $('.btn-abrir-modal-pagamento').on('click', function() {
        var id = $(this).data('id');
        $('#pagamento_id').val(id);
        modalPagamento.show();
    });

    $('#btn-salvar-pagamento').on('click', function() {
        var id = $('#pagamento_id').val();
        var data_pagamento = $('#pagamento_data').val();
        var forma_recebimento = $('#pagamento_forma').val();

        if (!data_pagamento) {
            alert('A data de pagamento e obrigatoria.');
            return;
        }

        $.post('?url=financeiro/confirmar', {
            id: id,
            data_pagamento: data_pagamento,
            forma_recebimento: forma_recebimento
        }, function(response) {
            var res = JSON.parse(response);
            if (res.status === 'success') {
                modalPagamento.hide();
                location.reload();
            } else {
                alert('Erro ao confirmar pagamento.');
            }
        });
    });

    $('.btn-gerar-recibo').on('click', function() {
        alert('Funcionalidade de geracao de PDF em desenvolvimento.');
    });
});
