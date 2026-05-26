<?php
require_once 'core/Controller.php';
require_once 'models/Usuario.php';

class UsuariosController extends Controller {

    public function index() {
        $this->checkAcesso([1]);
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listar();
        $this->view('usuarios/index', ['usuarios' => $usuarios]);
    }

    public function form($id = null) {
        $this->checkAcesso([1]);
        $usuario = null;
        if ($id) {
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->buscar($id);
        }
        $this->view('usuarios/form', ['usuario' => $usuario]);
    }

    public function salvar() {
        $this->checkAcesso([1]);
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuarioModel = new Usuario();
            
            $dados = [
                'login' => $_POST['login'],
                'senha' => $_POST['senha'],
                'nivel_acesso' => $_POST['nivel_acesso']
            ];

            $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;

            if ($id) {
                $usuarioModel->atualizar($id, $dados);
                $this->registrarLog("Atualizou usuario ID: " . $id, "usuarios");
            } else {
                $usuarioModel->salvar($dados);
                $this->registrarLog("Cadastrou novo usuario: " . $dados['login'], "usuarios");
            }

            header('Location: ?url=usuarios/index');
            exit;
        }
    }

    public function excluir() {
        $this->checkAcesso([1]);
        $this->verifyCSRF();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            if ($id == $_SESSION['usuario_id']) {
                echo json_encode(['status' => 'error', 'message' => 'Voce nao pode excluir seu proprio usuario ativo.']);
                exit;
            }

            $usuarioModel = new Usuario();
            $usuarioModel->excluir($id);
            
            $this->registrarLog("Excluiu usuario ID: " . $id, "usuarios");
            
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
}
?>
