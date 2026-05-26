<?php
require_once 'core/Controller.php';
require_once 'models/Financeiro.php';

class RelatoriosController extends Controller {

    public function index() {
        $this->checkAcesso([1, 2]);
        $this->view('relatorios/index');
    }

    public function buscarAnoFiscal() {
        $this->checkAcesso([1, 2]);
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ano'])) {
            $ano = $_POST['ano'];
            $financeiroModel = new Financeiro();
            $dados = $financeiroModel->relatorioAnoFiscal($ano);
            
            $this->registrarLog("Gerou relatorio fiscal do ano " . $ano, "financeiro");
            
            echo json_encode($dados);
            exit;
        }
    }
}
?>
