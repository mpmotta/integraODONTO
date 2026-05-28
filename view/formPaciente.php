<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
$p = null;
if(isset($_GET['id'])){
    $controller = new PacienteController();
    $p = $controller->consultaID($_GET['id']);
}
$action = $p ? "editarPaciente" : "cadastrarPaciente";
?>
<div class="card shadow-sm">
    <div class="card-header"><h5><?php echo $p ? 'Editar Paciente' : 'Novo Paciente'; ?></h5></div>
    <div class="card-body">
        <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Erro: CPF duplicado.</div>"; ?>
        <form action="../controller/pacienteController.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="row g-3">
            <?php if($p): ?><input type="hidden" name="meuid" value="<?php echo $p['id']; ?>"><?php endif; ?>
            <div class="col-md-6"><label>Nome Completo</label><input type="text" name="nome" class="form-control" value="<?php echo $p ? $p['nome'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Data Nasc.</label><input type="date" name="data_nascimento" class="form-control" value="<?php echo $p ? $p['data_nascimento'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Sexo</label><select name="sexo" class="form-select"><option value="M" <?php if($p && $p['sexo']=='M') echo 'selected';?>>M</option><option value="F" <?php if($p && $p['sexo']=='F') echo 'selected';?>>F</option><option value="O" <?php if($p && $p['sexo']=='O') echo 'selected';?>>Outro</option></select></div>
            <div class="col-md-3"><label>RG</label><input type="text" name="rg" class="form-control" value="<?php echo $p ? $p['rg'] : ''; ?>"></div>
            <div class="col-md-3"><label>CPF</label><input type="text" name="cpf" class="form-control" value="<?php echo $p ? $p['cpf'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo $p ? $p['telefone'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo $p ? $p['email'] : ''; ?>"></div>
            <div class="col-md-2"><label>CEP</label><input type="text" name="cep" class="form-control" value="<?php echo $p ? $p['cep'] : ''; ?>"></div>
            <div class="col-md-5"><label>Logradouro</label><input type="text" name="logradouro" class="form-control" value="<?php echo $p ? $p['logradouro'] : ''; ?>" required></div>
            <div class="col-md-2"><label>Número</label><input type="text" name="numero" class="form-control" value="<?php echo $p ? $p['numero'] : ''; ?>"></div>
            <div class="col-md-3"><label>Complemento</label><input type="text" name="complemento" class="form-control" value="<?php echo $p ? $p['complemento'] : ''; ?>"></div>
            <div class="col-md-4"><label>Bairro</label><input type="text" name="bairro" class="form-control" value="<?php echo $p ? $p['bairro'] : ''; ?>"></div>
            <div class="col-md-6"><label>Cidade</label><input type="text" name="cidade" class="form-control" value="<?php echo $p ? $p['cidade'] : ''; ?>"></div>
            <div class="col-md-2"><label>UF</label><input type="text" name="uf" class="form-control" value="<?php echo $p ? $p['uf'] : ''; ?>"></div>
            <div class="col-md-6"><label>Nome Responsável (Menor)</label><input type="text" name="responsavel_nome" class="form-control" value="<?php echo $p ? $p['responsavel_nome'] : ''; ?>"></div>
            <div class="col-md-6"><label>CPF Responsável</label><input type="text" name="responsavel_cpf" class="form-control" value="<?php echo $p ? $p['responsavel_cpf'] : ''; ?>"></div>
            <div class="col-md-12"><label>Foto (.jpg, .png)</label><input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Salvar</button> <a href="pacientes.php" class="btn btn-danger">Cancelar</a></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
