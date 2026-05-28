<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/clinicaModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class ClinicaController {
    public function carregar() {
        $clinicaModel = new Clinica();
        return $clinicaModel->carregar();
    }
    public function salvar(Clinica $clinica) {
        if (strlen($clinica->getNome()) == 0 || strlen($clinica->getDocumento()) == 0) {
            header("Location: ../view/config_clinica.php?campoVazio");
            exit();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clinicaModel = new Clinica();
            $clinicaModel->salvar($clinica);
            header("Location: ../view/config_clinica.php?salvo=ok");
            exit();
        }
    }
    public function handleRequest() {
        if (isset($_GET['action']) && $_GET['action'] == 'salvarClinica') {
            $clinica = new Clinica();
            $clinica->setNome($_POST['nome']);
            $clinica->setTipoDocumento($_POST['tipo_documento']);
            $clinica->setDocumento($_POST['documento']);
            $clinica->setEndereco($_POST['endereco']);
            $clinica->setTelefone($_POST['telefone']);
            $this->salvar($clinica);
        }
    }
}

$ClinicaCtrl = new ClinicaController();
$ClinicaCtrl->handleRequest();
?>
