<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
$controller = new ConsultaController();
$consultas = $controller->listar();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Agenda de Consultas</h2>
    <a href="formConsulta.php" class="btn btn-primary">Nova Consulta</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data/Hora</th><th>Paciente</th><th>Dentista</th><th>Tratamento</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($consultas as $c): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($c['data_consulta'])) . ' ' . substr($c['hora_consulta'],0,5); ?></td>
                    <td><?php echo $c['paciente']; ?></td>
                    <td><?php echo $c['dentista']; ?></td>
                    <td><?php echo $c['nome_tratamento']; ?></td>
                    <td><?php echo $c['status']; ?></td>
                    <td>
                        <?php if($c['status'] == 'Agendado' || $c['status'] == 'Aguardando'): ?>
                            <a href="../controller/consultaController.php?action=faltou&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">Faltou</a>
                            <a href="../controller/consultaController.php?action=cancelar&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger">Cancelar</a>
                        <?php endif; ?>
                        
                        <?php if($c['status'] != 'Concluido' && $c['status'] != 'Cancelado'): ?>
                            <form action="../controller/consultaController.php?action=concluir" method="POST" class="d-inline mt-1">
                                <input type="hidden" name="id_consulta" value="<?php echo $c['id']; ?>">
                                <input type="text" name="valor" placeholder="R$ 0,00" required class="form-control form-control-sm d-inline" style="width: 100px;">
                                <button type="submit" class="btn btn-sm btn-success">Concluir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
