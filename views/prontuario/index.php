<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Prontuário Clínico</h1>
    <a href="?url=pacientes/index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar para Pacientes
    </a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-odonto text-center p-3">
            <div class="mb-3">
                <?php if ($paciente['foto_path']): ?>
                    <img src="<?php echo $paciente['foto_path']; ?>" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user-circle fa-7x text-secondary"></i>
                <?php endif; ?>
            </div>
            <h5 style="color: #040;"><?php echo htmlspecialchars($paciente['nome']); ?></h5>
            <p class="text-muted mb-1">CPF: <?php echo htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $paciente['cpf'])); ?></p>
            <p class="text-muted">Nasc: <?php echo date('d/m/Y', strtotime($paciente['data_nascimento'])); ?></p>
            <hr>
            <div class="text-start px-3">
                <p><strong><i class="fas fa-phone"></i></strong> <?php echo htmlspecialchars(preg_replace("/(\d{2})(\d{4,5})(\d{4})/", "(\$1) \$2-\$3", $paciente['telefone'])); ?></p>
                <p><strong><i class="fas fa-map-marker-alt"></i></strong> <?php echo htmlspecialchars($paciente['cidade'] . ' - ' . $paciente['uf']); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card card-odonto">
            <div class="card-header">
                <i class="fas fa-history"></i> Histórico de Consultas e Tratamentos
            </div>
            <div class="card-body">
                <?php if (!empty($historico)): ?>
                    <div class="timeline">
                        <?php foreach ($historico as $h): ?>
                            <div class="border-start border-success border-3 ps-3 mb-4 ms-2">
                                <h6 style="color: #040;" class="mb-1">
                                    <?php echo date('d/m/Y', strtotime($h['data_hora_inicio'])); ?> às <?php echo date('H:i', strtotime($h['data_hora_inicio'])); ?>
                                </h6>
                                <p class="mb-1">
                                    <span class="badge bg-secondary"><?php echo $h['tipo_agendamento']; ?></span>
                                    <strong><?php echo htmlspecialchars($h['nome_tratamento']); ?></strong>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-user-md"></i> Atendimento por: Dr(a). <?php echo htmlspecialchars($h['dentista_nome']); ?> | 
                                    Status: <?php echo $h['status']; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Nenhum registro clínico encontrado para este paciente.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
