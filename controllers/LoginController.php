<?php
require_once 'core/Controller.php';
require_once 'models/Usuario.php';

class LoginController extends Controller {
    public function index() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ?url=dashboard/index');
            exit;
        }
        $this->view('login');
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $erro = "Token invalido.";
                $this->view('login', ['erro' => $erro]);
                return;
            }

            $login = $_POST['login'];
            $senha = $_POST['senha'];

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->login($login, $senha);

            if ($usuario) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_login'] = $usuario['login'];
                $_SESSION['usuario_nivel'] = $usuario['nivel_acesso'];
                
                $this->registrarLog("Login realizado", "usuarios");
                
                header('Location: ?url=dashboard/index');
            } else {
                $erro = "Credenciais invalidas.";
                $this->view('login', ['erro' => $erro]);
            }
        }
    }

    public function logout() {
        if(isset($_SESSION['usuario_id'])){
            $this->registrarLog("Logout realizado", "usuarios");
        }
        session_unset();
        session_destroy();
        header('Location: ?url=login/index');
    }
}
?>
