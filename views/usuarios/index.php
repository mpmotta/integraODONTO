<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Gestão de Usuários</h1>
    <a href="?url=usuarios/form" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Usuário
    </a>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Nível de Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr id="linha-usuario-<?php echo $u['id']; ?>">
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['login']); ?></td>
                                <td>
                                    <?php 
                                        if ($u['nivel_acesso'] == 1) echo '<span class="badge bg-danger">Administrador</span>';
                                        elseif ($u['nivel_acesso'] == 2) echo '<span class="badge bg-success">Dentista</span>';
                                        else echo '<span class="badge bg-info text-dark">Recepção</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="?url=usuarios/form/<?php echo $u['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                    <button class="btn btn-sm btn-danger btn-excluir-usuario" data-id="<?php echo $u['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="views/js/usuarios.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
