<?php
require_once 'core/Controller.php';
require_once 'models/Consulta.php';

class AgendaController extends Controller {

    public function index() {
        $this->checkAuth();
        $consultaModel = new Consulta();
        $consultas = $consultaModel->listar();
        $this->view('agenda/index', ['consultas' => $consultas]);
    }

    public function form($id = null) {
        $this->checkAuth();
        $consultaModel = new Consulta();
        $consulta = null;
        
        if ($id) {
            $consulta = $consultaModel->buscar($id);
        }
        
        $pacientes = $consultaModel->listarPacientes();
        $dentistas = $consultaModel->listarDentistas();
        
        $this->view('agenda/form', [
            'consulta' => $consulta,
            'pacientes' => $pacientes,
            'dentistas' => $dentistas
        ]);
    }

    public function salvar() {
        $this->checkAuth();
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $consultaModel = new Consulta();
            
            $dados = [
                'id_paciente' => $_POST['id_paciente'],
                'tipo_agendamento' => $_POST['tipo_agendamento'],
                'nome_tratamento' => $_POST['tipo_agendamento'] == 'Tratamento' ? $_POST['nome_tratamento'] : $_POST['procedimento_unico'],
                'data_hora_inicio' => $_POST['data_hora_inicio'],
                'data_hora_fim' => $_POST['data_hora_fim'],
                'status' => $_POST['status'],
                'id_dentista' => $_POST['id_dentista']
            ];

            $valor = isset($_POST['valor_procedimento']) ? str_replace(',', '.', str_replace('.', '', $_POST['valor_procedimento'])) : 0.00;
            $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;

            if ($id) {
                $consultaModel->atualizar($id, $dados);
                $this->registrarLog("Atualizou consulta ID: " . $id, "consultas");
                $id_consulta = $id;
            } else {
                $id_consulta = $consultaModel->salvar($dados);
                $this->registrarLog("Cadastrou nova consulta ID: " . $id_consulta, "consultas");
            }

            if ($dados['status'] == 'Concluido') {
                if (!$consultaModel->verificarLancamentoFinanceiro($id_consulta)) {
                    $consultaModel->gerarLancamentoFinanceiro($id_consulta, $valor);
                    $this->registrarLog("Gerou lancamento financeiro automatico para consulta ID: " . $id_consulta, "financeiro");
                }
            }

            header('Location: ?url=agenda/index');
            exit;
        }
    }

    public function alterarStatus() {
        $this->checkAuth();
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = $_POST['id'];
            $status = $_POST['status'];
            
            $consultaModel = new Consulta();
            $consultaModel->atualizarStatus($id, $status);
            
            if ($status == 'Concluido') {
                if (!$consultaModel->verificarLancamentoFinanceiro($id)) {
                    $consultaModel->gerarLancamentoFinanceiro($id, 0.00); 
                }
            }
            
            $this->registrarLog("Alterou status da consulta ID: " . $id . " para " . $status, "consultas");
            
            echo json_encode(['status' => 'success']);
            exit;
        }
    }

    public function excluir() {
        $this->checkAuth();
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $consultaModel = new Consulta();
            $consultaModel->excluir($id);
            
            $this->registrarLog("Excluiu consulta ID: " . $id, "consultas");
            
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
}
?>
