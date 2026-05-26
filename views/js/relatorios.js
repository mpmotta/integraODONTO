$(document).ready(function() {
    function formatarDinheiro(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatarCPF(cpf) {
        if (!cpf) return '';
        cpf = cpf.replace(/\D/g, "");
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }

    function formatarData(dataString) {
        var partes = dataString.split(' ')[0].split('-');
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    $('#btn-processar-relatorio').on('click', function() {
        var ano = $('#filtro_ano').val();
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');

        $.post('?url=relatorios/buscarAnoFiscal', { ano: ano }, function(response) {
            var dados = JSON.parse(response);
            var tbody = $('#corpo-tabela-relatorio');
            tbody.empty();
            var total = 0;

            if (dados.length > 0) {
                $.each(dados, function(index, item) {
                    var cpfFinal = item.responsavel_cpf ? item.responsavel_cpf : item.paciente_cpf;
                    var nomeTratamento = item.tipo_agendamento === 'Unica' ? item.nome_tratamento : item.tipo_agendamento + ' - ' + item.nome_tratamento;
                    
                    var tr = $('<tr>');
                    tr.append($('<td>').text(formatarData(item.data_pagamento)));
                    tr.append($('<td>').text(item.paciente_nome));
                    tr.append($('<td>').text(formatarCPF(cpfFinal)));
                    tr.append($('<td>').text(nomeTratamento));
                    tr.append($('<td>').text(formatarDinheiro(item.valor)));
                    
                    tbody.append(tr);
                    total += parseFloat(item.valor);
                });

                $('#valor-total-acumulado').text('R$ ' + formatarDinheiro(total));
                $('#botoes-exportacao').removeClass('d-none');
                $('#card-resultados').removeClass('d-none');
            } else {
                tbody.append('<tr><td colspan="5" class="text-center">Nenhum recebimento encontrado para o ano selecionado.</td></tr>');
                $('#valor-total-acumulado').text('R$ 0,00');
                $('#botoes-exportacao').addClass('d-none');
                $('#card-resultados').removeClass('d-none');
            }
            
            btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Processar Dados');
        });
    });

    $('#btn-exportar-xls').on('click', function() {
        var ano = $('#filtro_ano').val();
        var tabela = document.getElementById("tabela-relatorio");
        var html = tabela.outerHTML;
        
        var blob = new Blob([html], {
            type: "application/vnd.ms-excel;charset=utf-8"
        });
        
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = "Relatorio_IR_" + ano + ".xls";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    $('#btn-exportar-pdf').on('click', function() {
        window.print();
    });
});
