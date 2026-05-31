<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
require_once '../controller/pacienteController.php';

if(!isset($_GET['id_paciente'])) { exit; }

$pCtrl = new PacienteController();
$paciente = $pCtrl->consultaID($_GET['id_paciente']);
$idade = date_diff(date_create($paciente['data_nascimento']), date_create('today'))->y;

$cCtrl = new ConsultaController();
$historico = $cCtrl->listarProntuario($_GET['id_paciente']);
?>
<div class="card shadow-sm mb-4">
    <div class="card-body d-flex align-items-center">
        <?php if(!empty($paciente['foto_path']) && file_exists($paciente['foto_path'])): ?>
            <img src="<?php echo $paciente['foto_path']; ?>" alt="Foto" class="rounded-circle me-4" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #004400;">
        <?php else: ?>
            <div class="rounded-circle me-4 bg-secondary d-flex justify-content-center align-items-center" style="width: 100px; height: 100px; color: white; border: 3px solid #004400;">
                <i class="fas fa-user fa-3x"></i>
            </div>
        <?php endif; ?>
        <div>
            <h3 class="mb-1"><?php echo $paciente['nome']; ?></h3>
            <p class="mb-0 text-muted"><strong>CPF:</strong> <?php echo $paciente['cpf']; ?> | <strong>Idade:</strong> <?php echo $idade; ?> anos</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white"><h5 class="mb-0">Histórico de Tratamentos</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-light"><tr><th>Data</th><th>Tratamento Realizado</th><th>Dentista Responsável</th><th>Imagens do Tratamento</th></tr></thead>
            <tbody>
                <?php foreach($historico as $h): ?>
                <tr>
                    <td class="align-middle"><?php echo date('d/m/Y', strtotime($h['data_consulta'])); ?></td>
                    <td class="align-middle"><?php echo $h['nome_tratamento']; ?></td>
                    <td class="align-middle"><?php echo $h['dentista']; ?></td>
                    <td>
                        <form action="../controller/consultaController.php?action=uploadFoto" method="POST" enctype="multipart/form-data" class="d-flex mb-2">
                            <input type="hidden" name="id_consulta" value="<?php echo $h['id']; ?>">
                            <input type="hidden" name="id_paciente" value="<?php echo $paciente['id']; ?>">
                            <input type="file" name="foto_tratamento" class="form-control form-control-sm me-2" accept=".jpg,.jpeg,.png" required>
                            <button type="submit" class="btn btn-sm btn-success">Salvar</button>
                        </form>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php 
                            $fotos = $cCtrl->listarFotos($h['id']);
                            foreach($fotos as $f): 
                            ?>
                                <a href="<?php echo $f['caminho_foto']; ?>" target="_blank">
                                    <img src="<?php echo $f['caminho_foto']; ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc;">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($historico)): ?><tr><td colspan="4" class="text-center">Nenhum histórico clínico encontrado.</td></tr><?php endif; ?>
            </tbody>
        </table>
        
        <div class="mt-4 border-top pt-3">
            <a href="pacientes.php" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
