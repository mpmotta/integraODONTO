<?php 
require_once 'header.php'; 
require_once '../controller/usuarioController.php';
if($_SESSION['usuario_nivel'] != 1) { echo "Acesso negado."; exit; }
$controller = new UsuarioController();
$usuarios = $controller->consultar();
?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">Novo Usuário</h5></div>
            <div class="card-body">
                <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger py-2'>Erro: Login já existente.</div>"; ?>
                <?php if(isset($_GET['cadastro'])) echo "<div class='alert alert-success py-2'>Usuário cadastrado com sucesso!</div>"; ?>
                <form action="../controller/usuarioController.php?action=cadastrarUsuario" method="POST" class="row g-3">
                    <div class="col-md-6"><label class="small">Nome Completo</label><input type="text" name="nome_completo" class="form-control form-control-sm" required></div>
                    <div class="col-md-6"><label class="small">CPF</label><input type="text" name="cpf" class="form-control form-control-sm" required></div>
                    <div class="col-md-4"><label class="small">Login</label><input type="text" name="login" class="form-control form-control-sm" required></div>
                    <div class="col-md-4"><label class="small">Senha</label><input type="password" name="senha" class="form-control form-control-sm" required></div>
                    <div class="col-md-4">
                        <label class="small">Nível de Acesso</label>
                        <select name="nivel_acesso" class="form-select form-select-sm">
                            <option value="1">Administrador</option>
                            <option value="2">Dentista</option>
                            <option value="3">Recepção</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3 pt-2 border-top">
                        <button type="submit" class="btn btn-sm btn-primary">Cadastrar</button>
                        <a href="dashboard.php" class="btn btn-sm btn-secondary ms-2">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white"><h5 class="mb-0">Resetar Senha de Usuário (Admin)</h5></div>
            <div class="card-body">
                <?php if(isset($_GET['senha']) && $_GET['senha'] == 'resetada') echo "<div class='alert alert-success py-2'>Senha alterada pelo Admin!</div>"; ?>
                <form action="../controller/usuarioController.php?action=adminResetSenha" method="POST">
                    <div class="mb-2">
                        <label class="small">Selecionar Funcionário</label>
                        <select name="id_usuario" class="form-select form-select-sm" required>
                            <option value="">Selecione...</option>
                            <?php foreach($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo $u['nome_completo']; ?> (<?php echo $u['login']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2"><label class="small">Nova Senha Provisória</label><input type="password" name="nova_senha_admin" class="form-control form-control-sm" required></div>
                    <button type="submit" class="btn btn-sm btn-danger mt-2">Redefinir Senha</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">Usuários Cadastrados</h5></div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Nome</th><th>CPF</th><th>Login</th><th>Nível</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?php echo $u['nome_completo']; ?></td>
                            <td><?php echo $u['cpf']; ?></td>
                            <td><?php echo $u['login']; ?></td>
                            <td><?php echo $u['nivel_acesso'] == 1 ? 'Admin' : ($u['nivel_acesso'] == 2 ? 'Dentista' : 'Recepção'); ?></td>
                            <td>
                                <?php if($u['login'] !== 'admin'): ?>
                                    <a href="../controller/usuarioController.php?action=excluirUsuario&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este usuário do sistema?');">Excluir</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
