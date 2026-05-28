<?php 
require_once 'header.php';
require_once '../controller/financeiroController.php'; 
if($_SESSION['usuario_nivel'] == 3) { echo "Acesso negado."; exit; }
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$controller = new FinanceiroController();
$relatorio = $controller->listarIR($ano);
$total = 0;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Relatório Imposto de Renda</h2>
    <form method="GET" class="d-flex">
        <input type="number" name="ano" class="form-control me-2" value="<?php echo $ano; ?>" placeholder="Ano">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-hover">
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
<?php require_once 'footer.php'; ?>
