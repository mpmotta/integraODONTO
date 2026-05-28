<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
require_once '../controller/pacienteController.php';

if(!isset($_GET['id_paciente'])) { exit; }

$pCtrl = new PacienteController();
$paciente = $pCtrl->consultaID($_GET['id_paciente']);

$cCtrl = new ConsultaController();
$historico = $cCtrl->listarProntuario($_GET['id_paciente']);
?>
<div class="card shadow-sm">
    <div class="card-header"><h5>Prontuário Clínico: <?php echo $paciente['nome']; ?></h5></div>
    <div class="card-body">
        <a href="pacientes.php" class="btn btn-secondary mb-3">Voltar</a>
        <table class="table table-bordered">
            <thead class="table-dark"><tr><th>Data</th><th>Tratamento Realizado</th><th>Dentista Responsável</th></tr></thead>
            <tbody>
                <?php foreach($historico as $h): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($h['data_consulta'])); ?></td>
                    <td><?php echo $h['nome_tratamento']; ?></td>
                    <td><?php echo $h['dentista']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($historico)): ?><tr><td colspan="3" class="text-center">Nenhum histórico clínico encontrado.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
