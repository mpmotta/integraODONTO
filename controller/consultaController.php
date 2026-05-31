<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/consultaModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class ConsultaController {
    public function listar() {
        $model = new Consulta();
        return $model->listarAgenda();
    }
    public function consultaID($id) {
        $model = new Consulta();
        return $model->consultaID($id);
    }
    public function listarProntuario($id_paciente) {
        $model = new Consulta();
        return $model->listarProntuario($id_paciente);
    }
    public function listarFotos($id_consulta) {
        $model = new Consulta();
        return $model->listarFotosTratamento($id_consulta);
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'gerarJsonCalendario') {
                $model = new Consulta();
                $dados = $model->listarTodasConsultasCalendario();
                $eventos = [];
                foreach($dados as $d) {
                    $inicio = $d['data_consulta'] . 'T' . $d['hora_consulta'];
                    $eventos[] = [
                        'id' => $d['id'],
                        'title' => $d['paciente'] . ' - ' . $d['nome_tratamento'],
                        'start' => $inicio
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($eventos);
                exit();
            }
            if ($_GET['action'] == 'uploadFoto') {
                if (isset($_FILES['foto_tratamento']) && $_FILES['foto_tratamento']['error'] == 0) {
                    $extensao = strtolower(pathinfo($_FILES['foto_tratamento']['name'], PATHINFO_EXTENSION));
                    if(in_array($extensao, ['jpg', 'jpeg', 'png'])) {
                        $tmp_nome = md5($_FILES['foto_tratamento']['name'] . date('d-m-Y-h-i-s')) . '.' . $extensao;
                        move_uploaded_file($_FILES['foto_tratamento']['tmp_name'], '../uploads/tratamentos/' . $tmp_nome);
                        $model = new Consulta();
                        $model->inserirFotoTratamento($_POST['id_consulta'], '../uploads/tratamentos/' . $tmp_nome);
                    }
                }
                header("Location: ../view/prontuario.php?id_paciente=" . $_POST['id_paciente']);
                exit();
            }
            if ($_GET['action'] == 'agendar' || $_GET['action'] == 'editarConsulta') {
                $consulta = new Consulta();
                $consulta->setIdPaciente($_POST['id_paciente']);
                $consulta->setIdDentista($_POST['id_dentista']);
                $consulta->setNomeTratamento($_POST['nome_tratamento']);
                $consulta->setDataConsulta($_POST['data_consulta']);
                $consulta->setHoraConsulta($_POST['hora_consulta']);
                
                if ($_GET['action'] == 'editarConsulta') {
                    $consulta->setId($_POST['meuid']);
                    $consulta->editar($consulta);
                    header("Location: ../view/agenda.php?sucesso=editado");
                } else {
                    $consulta->setStatus('Agendado');
                    $consulta->inserir($consulta);
                    header("Location: ../view/agenda.php?sucesso=agendado");
                }
                exit();
            }
            if ($_GET['action'] == 'concluir') {
                $model = new Consulta();
                $valorLimpo = str_replace('.', '', $_POST['valor']);
                $valorFinal = str_replace(',', '.', $valorLimpo);
                $model->concluirTratamento($_POST['id_consulta'], $valorFinal);
                header("Location: ../view/agenda.php?sucesso=concluido");
                exit();
            }
            if ($_GET['action'] == 'faltou') {
                $model = new Consulta();
                $model->alterarStatus($_GET['id'], 'Faltou');
                header("Location: ../view/agenda.php?sucesso=status_alterado");
                exit();
            }
            if ($_GET['action'] == 'cancelar') {
                $model = new Consulta();
                $model->alterarStatus($_GET['id'], 'Cancelado');
                header("Location: ../view/agenda.php?sucesso=status_alterado");
                exit();
            }
        }
    }
}
$ConsultaCtrl = new ConsultaController();
$ConsultaCtrl->handleRequest();
?>
