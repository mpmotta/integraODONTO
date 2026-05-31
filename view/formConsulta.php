<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
require_once '../controller/usuarioController.php';

$dadosConsulta = null;
if (isset($_GET['id'])) {
    $controller = new ConsultaController();
    $dadosConsulta = $controller->consultaID($_GET['id']);
}

$action = $dadosConsulta ? "editarConsulta" : "agendar";

$usuarioCtrl = new UsuarioController();
$dentistas = $usuarioCtrl->listarDentistas();
?>
<div class="card shadow-sm w-50 mx-auto">
    <div class="card-header bg-dark text-white"><h5 class="mb-0"><?php echo $dadosConsulta ? 'Editar Consulta' : 'Agendar Nova Consulta'; ?></h5></div>
    <div class="card-body">
        <form action="../controller/consultaController.php?action=<?php echo $action; ?>" method="POST" class="row g-3">
            <?php if($dadosConsulta): ?><input type="hidden" name="meuid" value="<?php echo $dadosConsulta['id']; ?>"><?php endif; ?>
            
            <div class="col-md-12 position-relative">
                <label>Paciente (Busca por Nome ou CPF)</label>
                <input type="text" id="busca_paciente" class="form-control" autocomplete="off" value="<?php echo $dadosConsulta ? $dadosConsulta['paciente_nome'] : ''; ?>" placeholder="Digite nome ou CPF para buscar..." required>
                <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $dadosConsulta ? $dadosConsulta['id_paciente'] : ''; ?>" required>
                <div id="lista_resultado" class="list-group position-absolute w-100 mt-1 shadow" style="z-index: 9999;"></div>
            </div>

            <div class="col-md-12">
                <label>Dentista Responsável</label>
                <select name="id_dentista" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach($dentistas as $d): ?>
                        <option value="<?php echo $d['id']; ?>" <?php if($dadosConsulta && $dadosConsulta['id_dentista'] == $d['id']) echo 'selected'; ?>><?php echo $d['nome_completo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12"><label>Tratamento Previsto</label><input type="text" name="nome_tratamento" class="form-control" value="<?php echo $dadosConsulta ? $dadosConsulta['nome_tratamento'] : ''; ?>" required></div>
            <div class="col-md-6"><label>Data da Consulta</label><input type="date" name="data_consulta" class="form-control" value="<?php echo $dadosConsulta ? $dadosConsulta['data_consulta'] : ''; ?>" required></div>
            <div class="col-md-6"><label>Hora da Consulta</label><input type="time" name="hora_consulta" class="form-control" value="<?php echo $dadosConsulta ? $dadosConsulta['hora_consulta'] : ''; ?>" required></div>
            
            <div class="col-12 mt-4 border-top pt-3">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="agenda.php" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let inputBusca = document.getElementById('busca_paciente');
    let inputId = document.getElementById('id_paciente');
    let listaRes = document.getElementById('lista_resultado');

    inputBusca.addEventListener('input', function() {
        let txt = this.value;
        if (txt.length >= 2) {
            fetch('../controller/pacienteController.php?action=buscarPacientesAjax&q=' + encodeURIComponent(txt))
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(p => {
                    html += `<button type="button" class="list-group-item list-group-item-action text-start" onclick="escolherPaciente(${p.id}, '${p.nome}')">${p.nome} (CPF: ${p.cpf})</button>`;
                });
                listaRes.innerHTML = html;
            });
        } else {
            listaRes.innerHTML = '';
        }
    });
});

function escolherPaciente(id, nome) {
    document.getElementById('id_paciente').value = id;
    document.getElementById('busca_paciente').value = nome;
    document.getElementById('lista_resultado').innerHTML = '';
}
</script>
<?php require_once 'footer.php'; ?>
