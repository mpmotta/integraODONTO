<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/usuarioModel.php');

if (!isset($_SESSION['usuario_id']) && (!isset($_GET['action']) || $_GET['action'] !== 'logar')) {
    header("Location: ../view/login.php");
    exit();
}

class UsuarioController {
    public function consultar() {
        $usuarioModel = new Usuario();
        return $usuarioModel->consulta();
    }
    public function listarDentistas() {
        $usuarioModel = new Usuario();
        return $usuarioModel->consultaDentistas();
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'logar') {
                $usuarioModel = new Usuario();
                $user = $usuarioModel->logar($_POST['login'], $_POST['senha']);
                if ($user) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario_login'] = $user['login'];
                    $_SESSION['usuario_nome'] = $user['nome_completo'];
                    $_SESSION['usuario_nivel'] = $user['nivel_acesso'];
                    header('Location: ../view/dashboard.php');
                } else {
                    header('Location: ../view/login.php?erro=login_invalido');
                }
                exit();
            }
            if ($_GET['action'] == 'logout') {
                session_destroy();
                header('Location: ../view/login.php');
                exit();
            }
            if ($_GET['action'] == 'cadastrarUsuario') {
                $usuario = new Usuario();
                $usuario->setNomeCompleto($_POST['nome_completo']);
                $usuario->setCpf(preg_replace('/[^0-9]/', '', $_POST['cpf']));
                $usuario->setLogin($_POST['login']);
                $usuario->setSenha($_POST['senha']);
                $usuario->setNivelAcesso($_POST['nivel_acesso']);
                
                $usuarioModel = new Usuario();
                if($usuarioModel->inserir($usuario)) {
                    header('Location: ../view/usuarios.php?cadastro=ok');
                } else {
                    header('Location: ../view/usuarios.php?erro=login_duplicado');
                }
                exit();
            }
            if ($_GET['action'] == 'alterarMinhaSenha') {
                $usuarioModel = new Usuario();
                $userAtual = $usuarioModel->consultaID($_SESSION['usuario_id']);
                if (password_verify($_POST['senha_atual'], $userAtual['senha'])) {
                    $usuarioModel->alterarSenha($_SESSION['usuario_id'], $_POST['nova_senha']);
                    header("Location: ../view/config_clinica.php?senha=alterada");
                } else {
                    header("Location: ../view/config_clinica.php?senha=erro_atual");
                }
                exit();
            }
            if ($_GET['action'] == 'adminResetSenha') {
                if ($_SESSION['usuario_nivel'] == 1) {
                    $usuarioModel = new Usuario();
                    $usuarioModel->alterarSenha($_POST['id_usuario'], $_POST['nova_senha_admin']);
                    header("Location: ../view/usuarios.php?senha=resetada");
                } else {
                    header("Location: ../view/dashboard.php");
                }
                exit();
            }
            if ($_GET['action'] == 'excluirUsuario') {
                if ($_SESSION['usuario_nivel'] == 1) {
                    $usuarioModel = new Usuario();
                    $usuarioModel->excluir($_GET['id']);
                    header("Location: ../view/usuarios.php?delete=ok");
                }
                exit();
            }
        }
    }
}
$UsuarioCtrl = new UsuarioController();
$UsuarioCtrl->handleRequest();
?>
