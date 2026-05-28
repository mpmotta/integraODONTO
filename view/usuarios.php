<?php 
require_once 'header.php'; 
require_once '../controller/usuarioController.php';
if($_SESSION['usuario_nivel'] != 1) { echo "Acesso negado."; exit; }
$controller = new UsuarioController();
$usuarios = $controller->consultar();
?>
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5>Novo Usuário</h5></div>
    <div class="card-body">
        <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Erro: Login já existente.</div>"; ?>
        <form action="../controller/usuarioController.php?action=cadastrarUsuario" method="POST" class="row g-3">
            <div class="col-md-4"><input type="text" name="nome_completo" class="form-control" placeholder="Nome Completo" required></div>
            <div class="col-md-2"><input type="text" name="cpf" class="form-control" placeholder="CPF" required></div>
            <div class="col-md-2"><input type="text" name="login" class="form-control" placeholder="Login" required></div>
            <div class="col-md-2"><input type="password" name="senha" class="form-control" placeholder="Senha" required></div>
            <div class="col-md-2">
                <select name="nivel_acesso" class="form-select">
                    <option value="1">Administrador</option>
                    <option value="2">Dentista</option>
                    <option value="3">Recepção</option>
                </select>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Cadastrar</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><h5>Usuários Cadastrados</h5></div>
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
                    <td><a href="../controller/usuarioController.php?action=excluirUsuario&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?');">Excluir</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
