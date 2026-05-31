<?php 
require_once 'header.php';
require_once '../controller/clinicaController.php'; 
require_once '../controller/usuarioController.php';

$clinicaCtrl = new ClinicaController();
$dados = $clinicaCtrl->carregar();
?>
<div class="row g-4">
    <?php if($_SESSION['usuario_nivel'] == 1): ?>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">Dados da Clínica / Consultório</h5></div>
            <div class="card-body">
                <?php if(isset($_GET['salvo'])) echo "<div class='alert alert-success'>Dados atualizados!</div>"; ?>
                <form action="../controller/clinicaController.php?action=salvarClinica" method="POST" class="row g-3">
                    <div class="col-12"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?php echo isset($dados['nome']) ? $dados['nome'] : ''; ?>" required></div>
                    <div class="col-md-4"><label>Tipo Doc</label><select name="tipo_documento" class="form-select"><option value="CNPJ" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CNPJ') ? 'selected' : ''; ?>>CNPJ</option><option value="CPF" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CPF') ? 'selected' : ''; ?>>CPF</option></select></div>
                    <div class="col-md-8"><label>Documento</label><input type="text" name="documento" class="form-control" value="<?php echo isset($dados['documento']) ? $dados['documento'] : ''; ?>" required></div>
                    <div class="col-12"><label>Endereço Completo</label><input type="text" name="endereco" class="form-control" value="<?php echo isset($dados['endereco']) ? $dados['endereco'] : ''; ?>" required></div>
                    <div class="col-12"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo isset($dados['telefone']) ? $dados['telefone'] : ''; ?>" required></div>
                    <div class="col-12 mt-3 pt-2 border-top">
                        <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-4">
        <div class="card shadow-sm border-dark">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">Alterar Minha Senha de Acesso</h5></div>
            <div class="card-body">
                <?php if(isset($_GET['senha']) && $_GET['senha'] == 'alterada') echo "<div class='alert alert-success py-2'>Senha atualizada com sucesso!</div>"; ?>
                <?php if(isset($_GET['senha']) && $_GET['senha'] == 'erro_atual') echo "<div class='alert alert-danger py-2'>Erro: Senha atual incorreta.</div>"; ?>
                <form action="../controller/usuarioController.php?action=alterarMinhaSenha" method="POST">
                    <div class="mb-2"><label class="small">Senha Atual</label><input type="password" name="senha_atual" class="form-control form-control-sm" required></div>
                    <div class="mb-2"><label class="small">Nova Senha</label><input type="password" name="nova_senha" class="form-control form-control-sm" required></div>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Alterar Senha</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-12 mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>
</div>
<?php require_once 'footer.php'; ?>
