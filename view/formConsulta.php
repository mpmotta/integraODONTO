<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
require_once '../controller/usuarioController.php';

$pacienteCtrl = new PacienteController();
$pacientes = $pacienteCtrl->consultar();

$usuarioCtrl = new UsuarioController();
$dentistas = $usuarioCtrl->listarDentistas();
?>
<div class="card shadow-sm w-50 mx-auto">
    <div class="card-header"><h5>Agendar Nova Consulta</h5></div>
    <div class="card-body">
        <form action="../controller/consultaController.php?action=agendar" method="POST" class="row g-3">
            <div class="col-md-12">
                <label>Paciente</label>
                <select name="id_paciente" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['nome']; ?> (CPF: <?php echo $p['cpf']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label>Dentista Responsável</label>
                <select name="id_dentista" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach($dentistas as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo $d['nome_completo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12"><label>Tratamento Previsto</label><input type="text" name="nome_tratamento" class="form-control" required></div>
            <div class="col-md-6"><label>Data</label><input type="date" name="data_consulta" class="form-control" required></div>
            <div class="col-md-6"><label>Hora</label><input type="time" name="hora_consulta" class="form-control" required></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Agendar</button></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
