<?php 
require_once 'header.php';
require_once '../controller/clinicaController.php'; 
if($_SESSION['usuario_nivel'] != 1) { echo "Acesso negado."; exit; }
$controller = new ClinicaController();
$dados = $controller->carregar();
?>
<div class="card shadow-sm w-50 mx-auto">
    <div class="card-header"><h5>Dados da Clínica / Consultório</h5></div>
    <div class="card-body">
        <?php if(isset($_GET['salvo'])) echo "<div class='alert alert-success'>Dados atualizados!</div>"; ?>
        <form action="../controller/clinicaController.php?action=salvarClinica" method="POST" class="row g-3">
            <div class="col-12"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?php echo isset($dados['nome']) ? $dados['nome'] : ''; ?>" required></div>
            <div class="col-md-4"><label>Tipo Doc</label><select name="tipo_documento" class="form-select"><option value="CNPJ" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CNPJ') ? 'selected' : ''; ?>>CNPJ</option><option value="CPF" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CPF') ? 'selected' : ''; ?>>CPF</option></select></div>
            <div class="col-md-8"><label>Documento</label><input type="text" name="documento" class="form-control" value="<?php echo isset($dados['documento']) ? $dados['documento'] : ''; ?>" required></div>
            <div class="col-12"><label>Endereço Completo</label><input type="text" name="endereco" class="form-control" value="<?php echo isset($dados['endereco']) ? $dados['endereco'] : ''; ?>" required></div>
            <div class="col-12"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo isset($dados['telefone']) ? $dados['telefone'] : ''; ?>" required></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Salvar Configurações</button></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
