<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Pacientes</h1>
    <a href="?url=pacientes/form" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Paciente
    </a>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pacientes)): ?>
                        <?php foreach ($pacientes as $p): ?>
                            <tr id="linha-paciente-<?php echo $p['id']; ?>">
                                <td>
                                    <?php if ($p['foto_path']): ?>
                                        <img src="<?php echo $p['foto_path']; ?>" alt="Foto" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td><?php echo htmlspecialchars(preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $p['cpf'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars(preg_replace("/(\d{2})(\d{4,5})(\d{4})/", "(\$1) \$2-\$3", $p['telefone'])); ?>
                                    <a href="https://api.whatsapp.com/send?phone=55<?php echo $p['telefone']; ?>" target="_blank" class="btn btn-sm btn-success ms-2" title="Chamar no WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </td>
                                <td>
                                    <?php if($_SESSION['usuario_nivel'] == 1 || $_SESSION['usuario_nivel'] == 2): ?>
                                    <a href="?url=prontuario/paciente/<?php echo $p['id']; ?>" class="btn btn-sm btn-info text-white" title="Prontuário">
                                        <i class="fas fa-file-medical"></i> Prontuário
                                    </a>
                                    <?php endif; ?>
                                    <a href="?url=pacientes/form/<?php echo $p['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-excluir-paciente" data-id="<?php echo $p['id']; ?>" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum paciente cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="views/js/pacientes.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
