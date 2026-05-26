<?php
require_once 'core/Controller.php';
require_once 'models/Paciente.php';

class PacientesController extends Controller {

    public function index() {
        $this->checkAuth();
        $pacienteModel = new Paciente();
        $pacientes = $pacienteModel->listar();
        $this->view('pacientes/index', ['pacientes' => $pacientes]);
    }

    public function form($id = null) {
        $this->checkAuth();
        $paciente = null;
        if ($id) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->buscar($id);
        }
        $this->view('pacientes/form', ['paciente' => $paciente]);
    }

    public function salvar() {
        $this->checkAuth();
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pacienteModel = new Paciente();
            
            $dados = [
                'nome' => $_POST['nome'],
                'data_nascimento' => $_POST['data_nascimento'],
                'sexo' => $_POST['sexo'],
                'rg' => $_POST['rg'],
                'cpf' => preg_replace('/[^0-9]/', '', $_POST['cpf']),
                'email' => $_POST['email'],
                'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone']),
                'cep' => preg_replace('/[^0-9]/', '', $_POST['cep']),
                'logradouro' => $_POST['logradouro'],
                'numero' => $_POST['numero'],
                'complemento' => $_POST['complemento'],
                'bairro' => $_POST['bairro'],
                'cidade' => $_POST['cidade'],
                'uf' => $_POST['uf'],
                'responsavel_nome' => $_POST['responsavel_nome'],
                'responsavel_cpf' => preg_replace('/[^0-9]/', '', $_POST['responsavel_cpf'])
            ];

            $foto_path = $this->uploadFoto();
            if ($foto_path) {
                $dados['foto_path'] = $foto_path;
            }

            $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;

            if ($id) {
                $pacienteModel->atualizar($id, $dados);
                $this->registrarLog("Atualizou paciente ID: " . $id, "pacientes");
            } else {
                $pacienteModel->salvar($dados);
                $this->registrarLog("Cadastrou novo paciente: " . $dados['cpf'], "pacientes");
            }

            header('Location: ?url=pacientes/index');
            exit;
        }
    }

    public function excluir() {
        $this->checkAuth();
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $pacienteModel = new Paciente();
            $pacienteModel->excluir($id);
            
            $this->registrarLog("Excluiu paciente ID: " . $id, "pacientes");
            
            echo json_encode(['status' => 'success']);
            exit;
        }
    }

    private function uploadFoto() {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $diretorio = 'uploads/pacientes/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $extensoesPermitidas = ['jpg', 'jpeg', 'png'];

            if (in_array($extensao, $extensoesPermitidas)) {
                $novoNome = uniqid() . '.' . $extensao;
                $caminhoCompleto = $diretorio . $novoNome;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
                    return $caminhoCompleto;
                }
            }
        }
        return null;
    }
}
?>
