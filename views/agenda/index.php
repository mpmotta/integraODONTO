<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Agenda de Consultas</h1>
    <a href="?url=agenda/form" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Agendamento
    </a>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Paciente</th>
                        <th>Procedimento/Tratamento</th>
                        <th>Dentista</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($consultas)): ?>
                        <?php foreach ($consultas as $c): ?>
                            <tr id="linha-consulta-<?php echo $c['id']; ?>">
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($c['data_hora_inicio'])); ?></strong><br>
                                    <small><?php echo date('H:i', strtotime($c['data_hora_inicio'])) . ' às ' . date('H:i', strtotime($c['data_hora_fim'])); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($c['paciente_nome']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars(preg_replace("/(\d{2})(\d{4,5})(\d{4})/", "(\$1) \$2-\$3", $c['paciente_telefone'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $c['tipo_agendamento']; ?></span><br>
                                    <?php echo htmlspecialchars($c['nome_tratamento']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($c['dentista_nome']); ?></td>
                                <td>
                                    <select class="form-select form-select-sm select-status-consulta" data-id="<?php echo $c['id']; ?>">
                                        <option value="Agendado" <?php echo $c['status'] == 'Agendado' ? 'selected' : ''; ?>>Agendado</option>
                                        <option value="Confirmado" <?php echo $c['status'] == 'Confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                        <option value="Aguardando" <?php echo $c['status'] == 'Aguardando' ? 'selected' : ''; ?>>Aguardando</option>
                                        <option value="Em Atendimento" <?php echo $c['status'] == 'Em Atendimento' ? 'selected' : ''; ?>>Em Atendimento</option>
                                        <option value="Concluido" <?php echo $c['status'] == 'Concluido' ? 'selected' : ''; ?>>Concluído</option>
                                        <option value="Faltou" <?php echo $c['status'] == 'Faltou' ? 'selected' : ''; ?>>Faltou</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="https://api.whatsapp.com/send?phone=55<?php echo $c['paciente_telefone']; ?>&text=Ol%C3%A1%20<?php echo urlencode($c['paciente_nome']); ?>%2C%20entramos%20em%20contato%20para%20falar%20sobre%20sua%20consulta%20no%20dia%20<?php echo date('d/m/Y', strtotime($c['data_hora_inicio'])); ?>." target="_blank" class="btn btn-sm btn-success" title="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a href="?url=agenda/form/<?php echo $c['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-excluir-consulta" data-id="<?php echo $c['id']; ?>" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma consulta agendada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="views/js/agenda.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
