<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Gestão Financeira</h1>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Data Consulta</th>
                        <th>Paciente / CPF</th>
                        <th>Serviço / Tratamento</th>
                        <th>Valor (R$)</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lancamentos)): ?>
                        <?php foreach ($lancamentos as $l): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($l['data_hora_inicio'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($l['paciente_nome']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $l['paciente_cpf'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $l['tipo_agendamento']; ?></span><br>
                                    <?php echo htmlspecialchars($l['nome_tratamento']); ?>
                                </td>
                                <td><strong>R$ <?php echo number_format($l['valor'], 2, ',', '.'); ?></strong></td>
                                <td>
                                    <?php if ($l['status_pagamento'] == 'Pago'): ?>
                                        <span class="badge bg-success">Pago</span><br>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($l['data_pagamento'])); ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($l['status_pagamento'] == 'Pendente'): ?>
                                        <button class="btn btn-sm btn-success btn-abrir-modal-pagamento" data-id="<?php echo $l['id']; ?>">
                                            <i class="fas fa-check-circle"></i> Confirmar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-danger btn-gerar-recibo">
                                            <i class="fas fa-file-pdf"></i> Recibo
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum lançamento financeiro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPagamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #040;">Confirmar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfirmarPagamento">
                    <input type="hidden" id="pagamento_id">
                    <div class="mb-3">
                        <label class="form-label">Data do Pagamento</label>
                        <input type="date" id="pagamento_data" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Forma de Recebimento</label>
                        <select id="pagamento_forma" class="form-select" required>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="PIX">PIX</option>
                            <option value="Cartao de Credito">Cartão de Crédito</option>
                            <option value="Cartao de Debito">Cartão de Débito</option>
                            <option value="Boleto">Boleto</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-salvar-pagamento">Confirmar Recebimento</button>
            </div>
        </div>
    </div>
</div>

<script src="views/js/financeiro.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
