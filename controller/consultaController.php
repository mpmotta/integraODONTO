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
    public function listarProntuario($id_paciente) {
        $model = new Consulta();
        return $model->listarProntuario($id_paciente);
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'agendar') {
                $consulta = new Consulta();
                $consulta->setIdPaciente($_POST['id_paciente']);
                $consulta->setIdDentista($_POST['id_dentista']);
                $consulta->setNomeTratamento($_POST['nome_tratamento']);
                $consulta->setDataConsulta($_POST['data_consulta']);
                $consulta->setHoraConsulta($_POST['hora_consulta']);
                $consulta->setStatus('Agendado');
                $consulta->inserir($consulta);
                header("Location: ../view/agenda.php?sucesso=agendado");
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
