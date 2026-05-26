<?php
require_once 'core/Controller.php';
require_once 'models/Financeiro.php';

class FinanceiroController extends Controller {

    public function index() {
        $this->checkAcesso([1, 2]);
        $financeiroModel = new Financeiro();
        $lancamentos = $financeiroModel->listar();
        $this->view('financeiro/index', ['lancamentos' => $lancamentos]);
    }

    public function confirmar() {
        $this->checkAcesso([1, 2]);
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $forma_recebimento = $_POST['forma_recebimento'];
            $data_pagamento = $_POST['data_pagamento'];

            $financeiroModel = new Financeiro();
            $sucesso = $financeiroModel->confirmarPagamento($id, $forma_recebimento, $data_pagamento);

            if ($sucesso) {
                $this->registrarLog("Confirmou pagamento ID: " . $id . " via " . $forma_recebimento, "financeiro");
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit;
        }
    }
}
?>
