<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
$controller = new PacienteController();
$pacientes = $controller->consultar();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pacientes</h2>
    <a href="formPaciente.php" class="btn btn-primary">Novo Paciente</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Nome</th><th>CPF</th><th>Telefone</th><th>Email</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($pacientes as $p): ?>
                <tr>
                    <td><?php echo $p['nome']; ?></td>
                    <td><?php echo $p['cpf']; ?></td>
                    <td><?php echo $p['telefone']; ?></td>
                    <td><?php echo $p['email']; ?></td>
                    <td>
                        <a href="prontuario.php?id_paciente=<?php echo $p['id']; ?>" class="btn btn-sm btn-success">Prontuário</a>
                        <a href="formPaciente.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="../controller/pacienteController.php?action=excluirPaciente&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este paciente?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
