<?php 
require_once 'header.php';
require_once '../controller/financeiroController.php'; 
$controller = new FinanceiroController();
$pendentes = $controller->listarPendentes();
$balanco = $controller->listarBalanco();
?>
<h2>Gestão Financeira</h2>

<div class="card shadow-sm mt-3 border-danger">
    <div class="card-header bg-danger text-white"><h5>Lançamentos Pendentes</h5></div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data Consulta</th><th>Paciente</th><th>Tratamento</th><th>Valor (R$)</th><th>Baixa</th></tr></thead>
            <tbody>
                <?php foreach ($pendentes as $p): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($p['data_consulta'])); ?></td>
                        <td><?php echo $p['paciente_nome']; ?></td>
                        <td><?php echo $p['nome_tratamento']; ?></td>
                        <td><?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <form action="../controller/financeiroController.php?action=receberPagamento" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <select name="forma_recebimento" class="form-select form-select-sm d-inline w-auto" required>
                                    <option value="">Forma...</option>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="PIX">PIX</option>
                                    <option value="Cartao de Credito">Cartão de Crédito</option>
                                    <option value="Cartao de Debito">Cartão de Débito</option>
                                    <option value="Boleto">Boleto</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Receber</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4 border-success">
    <div class="card-header bg-success text-white"><h5>Recebimentos Concluídos</h5></div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data Pgto</th><th>Paciente</th><th>Tratamento</th><th>Valor</th><th>Ação</th></tr></thead>
            <tbody>
                <?php foreach ($balanco as $b): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($b['data_pagamento'])); ?></td>
                        <td><?php echo $b['paciente_nome']; ?></td>
                        <td><?php echo $b['nome_tratamento']; ?></td>
                        <td>R$ <?php echo number_format($b['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <?php if($_SESSION['usuario_nivel'] != 3): ?>
                                <?php if ($b['incluir_ir'] == 0): ?>
                                    <a href="../controller/financeiroController.php?action=incluirIR&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-success">Incluir no IR</a>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No IR</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="recibo_pdf.php?id=<?php echo $b['id']; ?>" target="_blank" class="btn btn-sm btn-primary">Gerar Recibo</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
