<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
$controller = new ConsultaController();
$consultas = $controller->listar();
$dias_semana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
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
                <?php foreach($consultas as $c): 
                    $telefoneLimpo = preg_replace('/[^0-9]/', '', $c['telefone']);
                    if (substr($telefoneLimpo, 0, 2) !== '55' && strlen($telefoneLimpo) > 0) {
                        $telefoneLimpo = '55' . $telefoneLimpo;
                    }
                    $diaSemanaConsulta = $dias_semana[date('w', strtotime($c['data_consulta']))];
                    $dataZap = date('d/m/Y', strtotime($c['data_consulta']));
                    $horaZap = substr($c['hora_consulta'], 0, 5);
                    $mensagem = "Prezado *" . $c['paciente'] . "*: Sua consulta com o Dr. " . $c['dentista'] . " está marcada para *" . $diaSemanaConsulta . ", " . $dataZap . " às " . $horaZap . "*. Aguardamos a sua presença!";
                    $linkZap = "https://wa.me/" . $telefoneLimpo . "?text=" . urlencode($mensagem);
                ?>
                <tr>
                    <td class="align-middle"><?php echo date('d/m/Y', strtotime($c['data_consulta'])) . ' ' . substr($c['hora_consulta'],0,5); ?></td>
                    <td class="align-middle"><?php echo $c['paciente']; ?></td>
                    <td class="align-middle"><?php echo $c['dentista']; ?></td>
                    <td class="align-middle"><?php echo $c['nome_tratamento']; ?></td>
                    <td class="align-middle"><?php echo $c['status']; ?></td>
                    <td style="white-space: nowrap;">
                        <div class="d-flex align-items-center gap-1">
                            <?php if($c['status'] == 'Agendado' || $c['status'] == 'Aguardando'): ?>
                                <a href="formConsulta.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="../controller/consultaController.php?action=faltou&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Confirmar falta deste paciente?');">Faltou</a>
                                <a href="../controller/consultaController.php?action=cancelar&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja cancelar esta consulta?');">Cancelar</a>
                            <?php endif; ?>
                            
                            <?php if($c['status'] != 'Concluido' && $c['status'] != 'Cancelado'): ?>
                                <form action="../controller/consultaController.php?action=concluir" method="POST" class="d-flex align-items-center gap-1 m-0">
                                    <input type="hidden" name="id_consulta" value="<?php echo $c['id']; ?>">
                                    <input type="text" name="valor" placeholder="R$ 0,00" required class="form-control form-control-sm" style="width: 80px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Concluir</button>
                                </form>
                                <?php if($c['status'] == 'Agendado' || $c['status'] == 'Aguardando'): ?>
                                    <a href="<?php echo $linkZap; ?>" target="_blank" class="btn btn-sm btn-success" title="Avisar via WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>