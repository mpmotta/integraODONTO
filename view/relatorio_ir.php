<?php 
require_once 'header.php';
require_once '../controller/financeiroController.php'; 
if($_SESSION['usuario_nivel'] == 3) { echo "Acesso negado."; exit; }
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$controller = new FinanceiroController();
$relatorio = $controller->listarIR($ano);
$total = 0;
?>
<style>
@media print {
    body * { visibility: hidden; }
    #area-impressao, #area-impressao * { visibility: visible; }
    #area-impressao { position: absolute; left: 0; top: 0; width: 100%; }
    .sidebar, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h2>Relatório Imposto de Renda</h2>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex me-3">
            <input type="number" name="ano" class="form-control me-2" value="<?php echo $ano; ?>" placeholder="Ano">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <button onclick="exportarExcel()" class="btn btn-success"><i class="fas fa-file-excel"></i> XLS</button>
        <button onclick="window.print()" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</button>
    </div>
</div>

<div class="card shadow-sm" id="area-impressao">
    <div class="card-body">
        <h4 class="mb-4 d-none d-print-block">Relatório Imposto de Renda - <?php echo $ano; ?></h4>
        <table class="table table-bordered table-hover" id="tabelaIR">
            <thead class="table-dark"><tr><th>Data Pgto</th><th>Paciente</th><th>CPF Declarado</th><th>Tratamento</th><th>Valor</th></tr></thead>
            <tbody>
                <?php foreach ($relatorio as $r): 
                    $cpfFinal = !empty($r['responsavel_cpf']) ? $r['responsavel_cpf'] : $r['paciente_cpf'];
                    $total += $r['valor'];
                ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['data_pagamento'])); ?></td>
                        <td><?php echo $r['paciente_nome']; ?></td>
                        <td><?php echo $cpfFinal; ?></td>
                        <td><?php echo $r['nome_tratamento']; ?></td>
                        <td>R$ <?php echo number_format($r['valor'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end"><strong>TOTAL DO ANO FISCAL:</strong></td>
                    <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
function exportarExcel() {
    var tabela = document.getElementById("tabelaIR").outerHTML;
    var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"></head><body>' + tabela + '</body></html>';
    var blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'Relatorio_IR_<?php echo $ano; ?>.xls';
    a.click();
}
</script>
<?php require_once 'footer.php'; ?>