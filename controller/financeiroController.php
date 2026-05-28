<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/financeiroModel.php');
require_once('../model/clinicaModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class FinanceiroController {
    public function listarPendentes() {
        $financeiroModel = new Financeiro();
        return $financeiroModel->consultaPendentes();
    }
    public function listarBalanco() {
        $financeiroModel = new Financeiro();
        return $financeiroModel->consultaBalanco();
    }
    public function listarIR($ano) {
        $financeiroModel = new Financeiro();
        return $financeiroModel->consultaIR($ano);
    }
    public function gerarRecibo($id_financeiro) {
        $financeiroModel = new Financeiro();
        $clinicaModel = new Clinica();
        $clinica = $clinicaModel->carregar();
        
        if(empty($clinica['nome']) || empty($clinica['documento'])) {
            return false;
        }

        return [
            'recibo' => $financeiroModel->consultaRecibo($id_financeiro),
            'clinica' => $clinica
        ];
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'receberPagamento') {
                $financeiroModel = new Financeiro();
                $financeiroModel->receberPagamento($_POST['id'], $_POST['forma_recebimento']);
                header("Location: ../view/balanco.php?sucesso=pago");
                exit();
            }
            if ($_GET['action'] == 'incluirIR') {
                $financeiroModel = new Financeiro();
                $financeiroModel->marcarIR($_GET['id']);
                header("Location: ../view/balanco.php?sucesso=ir_incluido");
                exit();
            }
        }
    }
}
$FinanceiroCtrl = new FinanceiroController();
$FinanceiroCtrl->handleRequest();
?>
