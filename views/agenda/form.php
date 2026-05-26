<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;"><?php echo isset($consulta) ? 'Editar Agendamento' : 'Novo Agendamento'; ?></h1>
    <a href="?url=agenda/index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <form action="?url=agenda/salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <?php if (isset($consulta)): ?>
                <input type="hidden" name="id" value="<?php echo $consulta['id']; ?>">
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Paciente *</label>
                    <select name="id_paciente" class="form-select" required>
                        <option value="">Selecione um paciente...</option>
                        <?php foreach ($pacientes as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo (isset($consulta) && $consulta['id_paciente'] == $p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nome']); ?> (CPF: <?php echo htmlspecialchars($p['cpf']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Profissional (Dentista) *</label>
                    <select name="id_dentista" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($dentistas as $d): ?>
                            <option value="<?php echo $d['id']; ?>" <?php echo (isset($consulta) && $consulta['id_dentista'] == $d['id']) ? 'selected' : ''; ?>>
                                Dr(a). <?php echo htmlspecialchars($d['login']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Data/Hora Início *</label>
                    <input type="datetime-local" name="data_hora_inicio" class="form-control" required value="<?php echo isset($consulta) ? date('Y-m-d\TH:i', strtotime($consulta['data_hora_inicio'])) : ''; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Data/Hora Fim *</label>
                    <input type="datetime-local" name="data_hora_fim" class="form-control" required value="<?php echo isset($consulta) ? date('Y-m-d\TH:i', strtotime($consulta['data_hora_fim'])) : ''; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="Agendado" <?php echo (isset($consulta) && $consulta['status'] == 'Agendado') ? 'selected' : ''; ?>>Agendado</option>
                        <option value="Confirmado" <?php echo (isset($consulta) && $consulta['status'] == 'Confirmado') ? 'selected' : ''; ?>>Confirmado</option>
                        <option value="Aguardando" <?php echo (isset($consulta) && $consulta['status'] == 'Aguardando') ? 'selected' : ''; ?>>Aguardando na Recepção</option>
                        <option value="Em Atendimento" <?php echo (isset($consulta) && $consulta['status'] == 'Em Atendimento') ? 'selected' : ''; ?>>Em Atendimento</option>
                        <option value="Concluido" <?php echo (isset($consulta) && $consulta['status'] == 'Concluido') ? 'selected' : ''; ?>>Concluído</option>
                        <option value="Faltou" <?php echo (isset($consulta) && $consulta['status'] == 'Faltou') ? 'selected' : ''; ?>>Faltou</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo de Agendamento *</label>
                    <select name="tipo_agendamento" id="tipo_agendamento" class="form-select" required>
                        <option value="Unica" <?php echo (isset($consulta) && $consulta['tipo_agendamento'] == 'Unica') ? 'selected' : ''; ?>>Consulta Única</option>
                        <option value="Tratamento" <?php echo (isset($consulta) && $consulta['tipo_agendamento'] == 'Tratamento') ? 'selected' : ''; ?>>Tratamento</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3" id="div_procedimento_unico">
                    <label class="form-label">Procedimento (Consulta Única)</label>
                    <select name="procedimento_unico" class="form-select">
                        <option value="Avaliacao Inicial" <?php echo (isset($consulta) && $consulta['nome_tratamento'] == 'Avaliacao Inicial') ? 'selected' : ''; ?>>Avaliação Inicial</option>
                        <option value="Profilaxia/Limpeza" <?php echo (isset($consulta) && $consulta['nome_tratamento'] == 'Profilaxia/Limpeza') ? 'selected' : ''; ?>>Profilaxia/Limpeza</option>
                        <option value="Urgencia" <?php echo (isset($consulta) && $consulta['nome_tratamento'] == 'Urgencia') ? 'selected' : ''; ?>>Urgência / Dor</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="div_nome_tratamento" style="display: none;">
                    <label class="form-label">Nome do Tratamento</label>
                    <input type="text" name="nome_tratamento" class="form-control" placeholder="Ex: Tratamento de Canal - Dente 21" value="<?php echo (isset($consulta) && $consulta['tipo_agendamento'] == 'Tratamento') ? htmlspecialchars($consulta['nome_tratamento']) : ''; ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Valor (R$) <small class="text-muted">Apenas para envio ao financeiro</small></label>
                    <input type="text" name="valor_procedimento" class="form-control mask-dinheiro" placeholder="0,00">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-5"><i class="fas fa-save"></i> Salvar Agendamento</button>
            </div>
        </form>
    </div>
</div>

<script src="views/js/agenda.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
