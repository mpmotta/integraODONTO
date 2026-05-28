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
    public function inserir(Usuario $usuario) {
        if (strlen($usuario->getNomeCompleto()) == 0 || strlen($usuario->getCpf()) == 0 || strlen($usuario->getLogin()) == 0) {
            header("Location: ../view/usuarios.php?campoVazio");
            exit();
        } else {
            $usuarioModel = new Usuario();
            $resultado = $usuarioModel->inserir($usuario);
            if($resultado) {
                header('Location: ../view/usuarios.php?cadastro=ok');
            } else {
                header('Location: ../view/usuarios.php?erro=login_duplicado');
            }
            exit();
        }
    }
    public function logar($login, $senha) {
        $usuarioModel = new Usuario();
        $user = $usuarioModel->logar($login, $senha);
        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_login'] = $user['login'];
            $_SESSION['usuario_nome'] = $user['nome_completo'];
            $_SESSION['usuario_nivel'] = $user['nivel_acesso'];
            header('Location: ../view/dashboard.php');
            exit();
        } else {
            header('Location: ../view/login.php?erro=login_invalido');
            exit();
        }
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'logar') {
                $this->logar($_POST['login'], $_POST['senha']);
            }
            if ($_GET['action'] == 'logout') {
                session_destroy();
                header('Location: ../view/login.php');
                exit();
            }
            if ($_GET['action'] == 'cadastrarUsuario') {
                $usuario = new Usuario();
                $usuario->setNomeCompleto($_POST['nome_completo']);
                $cpf_limpo = preg_replace('/[^0-9]/', '', $_POST['cpf']);
                $usuario->setCpf($cpf_limpo);
                $usuario->setLogin($_POST['login']);
                $usuario->setSenha($_POST['senha']);
                $usuario->setNivelAcesso($_POST['nivel_acesso']);
                $this->inserir($usuario);
            }
            if ($_GET['action'] == 'excluirUsuario') {
                $usuarioModel = new Usuario();
                $usuarioModel->excluir($_GET['id']);
                header("Location: ../view/usuarios.php?delete=ok");
                exit();
            }
        }
    }
}
$UsuarioCtrl = new UsuarioController();
$UsuarioCtrl->handleRequest();
?>
