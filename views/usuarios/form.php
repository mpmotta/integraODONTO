<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;"><?php echo isset($usuario) ? 'Editar Usuário' : 'Novo Usuário'; ?></h1>
    <a href="?url=usuarios/index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card card-odonto" style="max-width: 600px;">
    <div class="card-body">
        <form action="?url=usuarios/salvar" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <?php if (isset($usuario)): ?>
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Login *</label>
                <input type="text" name="login" class="form-control" required value="<?php echo isset($usuario) ? htmlspecialchars($usuario['login']) : ''; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Senha <?php echo isset($usuario) ? '<small class="text-muted">(Deixe em branco para manter a atual)</small>' : '*'; ?></label>
                <input type="password" name="senha" class="form-control" <?php echo isset($usuario) ? '' : 'required'; ?>>
            </div>

            <div class="mb-4">
                <label class="form-label">Nível de Acesso *</label>
                <select name="nivel_acesso" class="form-select" required>
                    <option value="1" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 1) ? 'selected' : ''; ?>>Administrador (Acesso Total)</option>
                    <option value="2" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 2) ? 'selected' : ''; ?>>Dentista (Clínico e Relatórios)</option>
                    <option value="3" <?php echo (isset($usuario) && $usuario['nivel_acesso'] == 3) ? 'selected' : ''; ?>>Recepção (Apenas Agenda e Cadastros)</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-5"><i class="fas fa-save"></i> Salvar Usuário</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
