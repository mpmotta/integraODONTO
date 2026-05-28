#!/bin/bash

mkdir -p config model controller view uploads/pacientes
chmod 777 uploads/pacientes

cat << 'EOF' > config/database.sql
DROP DATABASE IF EXISTS integra_odonto;
CREATE DATABASE integra_odonto;
USE integra_odonto;

CREATE TABLE clinica_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo_documento ENUM('CNPJ', 'CPF') NOT NULL,
    documento VARCHAR(20) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    login VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('M', 'F', 'O') NOT NULL,
    rg VARCHAR(20),
    cpf VARCHAR(14) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20) NOT NULL,
    cep VARCHAR(10),
    logradouro VARCHAR(100),
    numero VARCHAR(10),
    complemento VARCHAR(50),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    uf CHAR(2),
    responsavel_nome VARCHAR(100),
    responsavel_cpf VARCHAR(14),
    foto_path VARCHAR(255),
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    nome_tratamento VARCHAR(100) NOT NULL,
    data_consulta DATE NOT NULL,
    hora_consulta TIME NOT NULL,
    status ENUM('Agendado', 'Aguardando', 'Concluido', 'Faltou', 'Cancelado') NOT NULL DEFAULT 'Agendado',
    id_dentista INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_dentista) REFERENCES usuarios(id)
);

CREATE TABLE financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status_pagamento ENUM('Pendente', 'Pago') NOT NULL DEFAULT 'Pendente',
    forma_recebimento ENUM('Dinheiro', 'PIX', 'Cartao de Credito', 'Cartao de Debito', 'Boleto') NULL,
    data_pagamento DATETIME NULL,
    incluir_ir TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_consulta) REFERENCES consultas(id)
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(255) NOT NULL,
    tabela VARCHAR(50) NOT NULL,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome_completo, cpf, login, senha, nivel_acesso) 
VALUES ('Administrador', '00000000000', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 1);
EOF

cat << 'EOF' > index.php
<?php
header('Location: view/login.php');
exit();
?>
EOF

cat << 'EOF' > config/conexao.php
<?php
date_default_timezone_set('America/Sao_Paulo');

abstract class Conexao {
    private $servidor = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $banco = 'integra_odonto';
    protected $conn;

    public function __construct() {
        $this->conexao();
    }

    private function conexao() {
        $this->conn = new PDO("mysql:host=$this->servidor;dbname=$this->banco", $this->user, $this->pass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("set names utf8");
        $this->conn->exec("SET time_zone = '-03:00'");
    }
}
?>
EOF

cat << 'EOF' > model/clinicaModel.php
<?php
require_once '../config/conexao.php';

class Clinica extends Conexao {
    private $id, $nome, $tipo_documento, $documento, $endereco, $telefone;
    private $tabela = 'clinica_config';

    public function __construct() { parent::__construct(); }

    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getTipoDocumento() { return $this->tipo_documento; }
    public function setTipoDocumento($tipo_documento) { $this->tipo_documento = $tipo_documento; }
    public function getDocumento() { return $this->documento; }
    public function setDocumento($documento) { $this->documento = $documento; }
    public function getEndereco() { return $this->endereco; }
    public function setEndereco($endereco) { $this->endereco = $endereco; }
    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function carregar() {
        $sql = "SELECT * FROM $this->tabela LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar(Clinica $clinica) {
        $sqlCheck = "SELECT count(id) as total FROM $this->tabela";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute();
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            $sql = "UPDATE $this->tabela SET nome = :nome, tipo_documento = :tipo_documento, documento = :documento, endereco = :endereco, telefone = :telefone";
        } else {
            $sql = "INSERT INTO $this->tabela (nome, tipo_documento, documento, endereco, telefone) VALUES (:nome, :tipo_documento, :documento, :endereco, :telefone)";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nome', $clinica->getNome(), PDO::PARAM_STR);
        $stmt->bindParam(':tipo_documento', $clinica->getTipoDocumento(), PDO::PARAM_STR);
        $stmt->bindParam(':documento', $clinica->getDocumento(), PDO::PARAM_STR);
        $stmt->bindParam(':endereco', $clinica->getEndereco(), PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $clinica->getTelefone(), PDO::PARAM_STR);
        $stmt->execute();
    }
}
?>
EOF

cat << 'EOF' > controller/clinicaController.php
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
EOF

cat << 'EOF' > model/usuarioModel.php
<?php
require_once '../config/conexao.php';

class Usuario extends Conexao {
    private $id, $nome_completo, $cpf, $login, $senha, $nivel_acesso;
    private $tabela = 'usuarios';

    public function __construct() { parent::__construct(); }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNomeCompleto() { return $this->nome_completo; }
    public function setNomeCompleto($nome_completo) { $this->nome_completo = $nome_completo; }
    public function getCpf() { return $this->cpf; }
    public function setCpf($cpf) { $this->cpf = $cpf; }
    public function getLogin() { return $this->login; }
    public function setLogin($login) { $this->login = $login; }
    public function getSenha() { return $this->senha; }
    public function setSenha($senha) { $this->senha = $senha; }
    public function getNivelAcesso() { return $this->nivel_acesso; }
    public function setNivelAcesso($nivel_acesso) { $this->nivel_acesso = $nivel_acesso; }

    public function consulta() {
        $sql = "SELECT id, nome_completo, cpf, login, nivel_acesso FROM $this->tabela WHERE deleted_at IS NULL ORDER BY nome_completo ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaDentistas() {
        $sql = "SELECT id, nome_completo FROM $this->tabela WHERE nivel_acesso = 2 AND deleted_at IS NULL ORDER BY nome_completo ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logar($login, $senha) {
        $sql = "SELECT id, nome_completo, login, senha, nivel_acesso FROM $this->tabela WHERE login = :login AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":login", $login, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senha, $row['senha'])) {
                return $row;
            }
        }
        return false;
    }

    public function inserir(Usuario $usuario) {
        $check = $this->conn->prepare("SELECT COUNT(id) FROM $this->tabela WHERE login = :login AND deleted_at IS NULL");
        $l = $usuario->getLogin();
        $check->bindParam(':login', $l, PDO::PARAM_STR);
        $check->execute();
        if($check->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO $this->tabela (nome_completo, cpf, login, text_senha, nivel_acesso) VALUES (:nome_completo, :cpf, :login, :senha, :nivel_acesso)";
        $sql = "INSERT INTO $this->tabela (nome_completo, cpf, login, senha, nivel_acesso) VALUES (:nome_completo, :cpf, :login, :senha, :nivel_acesso)";
        $stmt = $this->conn->prepare($sql);
        $senha_hash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
        $stmt->bindParam(':nome_completo', $usuario->getNomeCompleto(), PDO::PARAM_STR);
        $stmt->bindParam(':cpf', $usuario->getCpf(), PDO::PARAM_STR);
        $stmt->bindParam(':login', $usuario->getLogin(), PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(':nivel_acesso', $usuario->getNivelAcesso(), PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    public function excluir($id) {
        $sql = "UPDATE $this->tabela SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
EOF

cat << 'EOF' > controller/usuarioController.php
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
EOF

cat << 'EOF' > model/pacienteModel.php
<?php
require_once '../config/conexao.php';

class Paciente extends Conexao {
    private $id, $nome, $data_nascimento, $sexo, $rg, $cpf, $email, $telefone;
    private $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf;
    private $responsavel_nome, $responsavel_cpf, $foto_path;
    private $tabela = 'pacientes';

    public function __construct() { parent::__construct(); }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getDataNascimento() { return $this->data_nascimento; }
    public function setDataNascimento($data_nascimento) { $this->data_nascimento = $data_nascimento; }
    public function getSexo() { return $this->sexo; }
    public function setSexo($sexo) { $this->sexo = $sexo; }
    public function getRg() { return $this->rg; }
    public function setRg($rg) { $this->rg = $rg; }
    public function getCpf() { return $this->cpf; }
    public function setCpf($cpf) { $this->cpf = $cpf; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getTelefone() { return $this->telefone; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function getCep() { return $this->cep; }
    public function setCep($cep) { $this->cep = $cep; }
    public function getLogradouro() { return $this->logradouro; }
    public function setLogradouro($logradouro) { $this->logradouro = $logradouro; }
    public function getNumero() { return $this->numero; }
    public function setNumero($numero) { $this->numero = $numero; }
    public function getComplemento() { return $this->complemento; }
    public function setComplemento($complemento) { $this->complemento = $complemento; }
    public function getBairro() { return $this->bairro; }
    public function setBairro($bairro) { $this->bairro = $bairro; }
    public function getCidade() { return $this->cidade; }
    public function setCidade($cidade) { $this->cidade = $cidade; }
    public function getUf() { return $this->uf; }
    public function setUf($uf) { $this->uf = $uf; }
    public function getResponsavelNome() { return $this->responsavel_nome; }
    public function setResponsavelNome($responsavel_nome) { $this->responsavel_nome = $responsavel_nome; }
    public function getResponsavelCpf() { return $this->responsavel_cpf; }
    public function setResponsavelCpf($responsavel_cpf) { $this->responsavel_cpf = $responsavel_cpf; }
    public function getFotoPath() { return $this->foto_path; }
    public function setFotoPath($foto_path) { $this->foto_path = $foto_path; }

    public function consulta() {
        $sql = "SELECT id, nome, cpf, telefone, email FROM $this->tabela WHERE deleted_at IS NULL ORDER BY nome ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaID($id) {
        $sql = "SELECT * FROM $this->tabela WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inserir(Paciente $paciente) {
        $check = $this->conn->prepare("SELECT COUNT(id) FROM $this->tabela WHERE cpf = :cpf AND deleted_at IS NULL");
        $c = $paciente->getCpf();
        $check->bindParam(':cpf', $c, PDO::PARAM_STR);
        $check->execute();
        if($check->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO $this->tabela (nome, data_nascimento, sexo, rg, cpf, email, telefone, cep, logradouro, numero, complemento, bairro, cidade, uf, responsavel_nome, responsavel_cpf, foto_path) 
                VALUES (:nome, :data_nascimento, :sexo, :rg, :cpf, :email, :telefone, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :responsavel_nome, :responsavel_cpf, :foto_path)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nome', $paciente->getNome(), PDO::PARAM_STR);
        $stmt->bindParam(':data_nascimento', $paciente->getDataNascimento(), PDO::PARAM_STR);
        $stmt->bindParam(':sexo', $paciente->getSexo(), PDO::PARAM_STR);
        $stmt->bindParam(':rg', $paciente->getRg(), PDO::PARAM_STR);
        $stmt->bindParam(':cpf', $paciente->getCpf(), PDO::PARAM_STR);
        $stmt->bindParam(':email', $paciente->getEmail(), PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $paciente->getTelefone(), PDO::PARAM_STR);
        $stmt->bindParam(':cep', $paciente->getCep(), PDO::PARAM_STR);
        $stmt->bindParam(':logradouro', $paciente->getLogradouro(), PDO::PARAM_STR);
        $stmt->bindParam(':numero', $paciente->getNumero(), PDO::PARAM_STR);
        $stmt->bindParam(':complemento', $paciente->getComplemento(), PDO::PARAM_STR);
        $stmt->bindParam(':bairro', $paciente->getBairro(), PDO::PARAM_STR);
        $stmt->bindParam(':cidade', $paciente->getCidade(), PDO::PARAM_STR);
        $stmt->bindParam(':uf', $paciente->getUf(), PDO::PARAM_STR);
        $stmt->bindParam(':responsavel_nome', $paciente->getResponsavelNome(), PDO::PARAM_STR);
        $stmt->bindParam(':responsavel_cpf', $paciente->getResponsavelCpf(), PDO::PARAM_STR);
        $stmt->bindParam(':foto_path', $paciente->getFotoPath(), PDO::PARAM_STR);
        $stmt->execute();
        return true;
    }

    public function editar(Paciente $paciente, $id) {
        $check = $this->conn->prepare("SELECT COUNT(id) FROM $this->tabela WHERE cpf = :cpf AND id != :id AND deleted_at IS NULL");
        $c = $paciente->getCpf();
        $check->bindParam(':cpf', $c, PDO::PARAM_STR);
        $check->bindParam(':id', $id, PDO::PARAM_INT);
        $check->execute();
        if($check->fetchColumn() > 0) {
            return false;
        }

        $sql = "UPDATE $this->tabela SET nome=:nome, data_nascimento=:data_nascimento, sexo=:sexo, rg=:rg, cpf=:cpf, email=:email, telefone=:telefone, cep=:cep, logradouro=:logradouro, numero=:numero, complemento=:complemento, bairro=:bairro, cidade=:cidade, uf=:uf, responsavel_nome=:responsavel_nome, responsavel_cpf=:responsavel_cpf";
        if ($paciente->getFotoPath() != null) {
            $getOld = $this->conn->prepare("SELECT foto_path FROM $this->tabela WHERE id = :id");
            $getOld->bindParam(':id', $id, PDO::PARAM_INT);
            $getOld->execute();
            $oldPath = $getOld->fetchColumn();
            if($oldPath && file_exists($oldPath)) {
                unlink($oldPath);
            }
            $sql .= ", foto_path=:foto_path";
        }
        $sql .= " WHERE id=:id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nome', $paciente->getNome(), PDO::PARAM_STR);
        $stmt->bindParam(':data_nascimento', $paciente->getDataNascimento(), PDO::PARAM_STR);
        $stmt->bindParam(':sexo', $paciente->getSexo(), PDO::PARAM_STR);
        $stmt->bindParam(':rg', $paciente->getRg(), PDO::PARAM_STR);
        $stmt->bindParam(':cpf', $paciente->getCpf(), PDO::PARAM_STR);
        $stmt->bindParam(':email', $paciente->getEmail(), PDO::PARAM_STR);
        $stmt->bindParam(':telefone', $paciente->getTelefone(), PDO::PARAM_STR);
        $stmt->bindParam(':cep', $paciente->getCep(), PDO::PARAM_STR);
        $stmt->bindParam(':logradouro', $paciente->getLogradouro(), PDO::PARAM_STR);
        $stmt->bindParam(':numero', $paciente->getNumero(), PDO::PARAM_STR);
        $stmt->bindParam(':complemento', $paciente->getComplemento(), PDO::PARAM_STR);
        $stmt->bindParam(':bairro', $paciente->getBairro(), PDO::PARAM_STR);
        $stmt->bindParam(':cidade', $paciente->getCidade(), PDO::PARAM_STR);
        $stmt->bindParam(':uf', $paciente->getUf(), PDO::PARAM_STR);
        $stmt->bindParam(':responsavel_nome', $paciente->getResponsavelNome(), PDO::PARAM_STR);
        $stmt->bindParam(':responsavel_cpf', $paciente->getResponsavelCpf(), PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($paciente->getFotoPath() != null) $stmt->bindParam(':foto_path', $paciente->getFotoPath(), PDO::PARAM_STR);
        $stmt->execute();
        return true;
    }

    public function excluir($id) {
        $sql = "UPDATE $this->tabela SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
EOF

cat << 'EOF' > controller/pacienteController.php
<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/pacienteModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class PacienteController {
    public function consultar() {
        $pacienteModel = new Paciente();
        return $pacienteModel->consulta();
    }
    public function consultaID($id) {
        $pacienteModel = new Paciente();
        return $pacienteModel->consultaID($id);
    }
    public function inserir(Paciente $paciente) {
        if (strlen($paciente->getNome()) == 0 || strlen($paciente->getCpf()) == 0 || strlen($paciente->getTelefone()) == 0) {
            header("Location: ../view/formPaciente.php?campoVazio");
            exit();
        } else {
            $pacienteModel = new Paciente();
            $resultado = $pacienteModel->inserir($paciente);
            if($resultado) {
                header('Location: ../view/pacientes.php?cadastro=ok');
            } else {
                header('Location: ../view/formPaciente.php?erro=cpf_duplicado');
            }
            exit();
        }
    }
    public function editar(Paciente $paciente) {
        if (strlen($paciente->getNome()) == 0 || strlen($paciente->getCpf()) == 0 || strlen($paciente->getTelefone()) == 0) {
            header("Location: ../view/formPaciente.php?id={$paciente->getId()}&campoVazio");
            exit();
        } else {
            $pacienteModel = new Paciente();
            $resultado = $pacienteModel->editar($paciente, $paciente->getId());
            if($resultado) {
                header("Location: ../view/pacientes.php?edit=ok");
            } else {
                header("Location: ../view/formPaciente.php?id={$paciente->getId()}&erro=cpf_duplicado");
            }
            exit();
        }
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'cadastrarPaciente' || $_GET['action'] == 'editarPaciente') {
                $paciente = new Paciente();
                if($_GET['action'] == 'editarPaciente') $paciente->setId($_POST['meuid']);
                
                $paciente->setNome($_POST['nome']);
                $paciente->setDataNascimento($_POST['data_nascimento']);
                $paciente->setSexo($_POST['sexo']);
                $paciente->setRg($_POST['rg']);
                $paciente->setCpf(preg_replace('/[^0-9]/', '', $_POST['cpf']));
                $paciente->setEmail($_POST['email']);
                $paciente->setTelefone(preg_replace('/[^0-9]/', '', $_POST['telefone']));
                $paciente->setCep(preg_replace('/[^0-9]/', '', $_POST['cep']));
                $paciente->setLogradouro($_POST['logradouro']);
                $paciente->setNumero($_POST['numero']);
                $paciente->setComplemento($_POST['complemento']);
                $paciente->setBairro($_POST['bairro']);
                $paciente->setCidade($_POST['cidade']);
                $paciente->setUf($_POST['uf']);
                $paciente->setResponsavelNome($_POST['responsavel_nome']);
                $paciente->setResponsavelCpf(preg_replace('/[^0-9]/', '', $_POST['responsavel_cpf']));
                
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    $permitidos = array('jpg', 'jpeg', 'png');
                    if(in_array($extensao, $permitidos)) {
                        $tmp_nome = md5($_FILES['foto']['name'] . date('d-m-Y-h-i-s'));
                        $foto_path = $tmp_nome . "." . $extensao;
                        move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/pacientes/' . $foto_path);
                        $paciente->setFotoPath('../uploads/pacientes/' . $foto_path);
                    }
                }

                if($_GET['action'] == 'cadastrarPaciente') {
                    $this->inserir($paciente);
                } else {
                    $this->editar($paciente);
                }
            }
            if ($_GET['action'] == 'excluirPaciente') {
                $pacienteModel = new Paciente();
                $pacienteModel->excluir($_GET['id']);
                header("Location: ../view/pacientes.php?delete=ok");
                exit();
            }
        }
    }
}
$PacienteCtrl = new PacienteController();
$PacienteCtrl->handleRequest();
?>
EOF

cat << 'EOF' > model/consultaModel.php
<?php
require_once '../config/conexao.php';

class Consulta extends Conexao {
    private $id, $id_paciente, $nome_tratamento, $data_consulta, $hora_consulta, $status, $id_dentista;
    private $tabela = 'consultas';

    public function __construct() { parent::__construct(); }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getIdPaciente() { return $this->id_paciente; }
    public function setIdPaciente($id_paciente) { $this->id_paciente = $id_paciente; }
    public function getNomeTratamento() { return $this->nome_tratamento; }
    public function setNomeTratamento($nome_tratamento) { $this->nome_tratamento = $nome_tratamento; }
    public function getDataConsulta() { return $this->data_consulta; }
    public function setDataConsulta($data_consulta) { $this->data_consulta = $data_consulta; }
    public function getHoraConsulta() { return $this->hora_consulta; }
    public function setHoraConsulta($hora_consulta) { $this->hora_consulta = $hora_consulta; }
    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
    public function getIdDentista() { return $this->id_dentista; }
    public function setIdDentista($id_dentista) { $this->id_dentista = $id_dentista; }

    public function listarAgenda() {
        $sql = "SELECT c.id, c.nome_tratamento, c.data_consulta, c.hora_consulta, c.status, p.nome as paciente, u.nome_completo as dentista 
                FROM $this->tabela c
                INNER JOIN pacientes p ON c.id_paciente = p.id
                INNER JOIN usuarios u ON c.id_dentista = u.id
                WHERE c.deleted_at IS NULL AND p.deleted_at IS NULL AND u.deleted_at IS NULL
                ORDER BY c.data_consulta DESC, c.hora_consulta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProntuario($id_paciente) {
        $sql = "SELECT c.nome_tratamento, c.data_consulta, c.status, u.nome_completo as dentista 
                FROM $this->tabela c
                INNER JOIN usuarios u ON c.id_dentista = u.id
                WHERE c.id_paciente = :id_paciente AND c.status = 'Concluido' AND c.deleted_at IS NULL
                ORDER BY c.data_consulta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_paciente', $id_paciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inserir(Consulta $consulta) {
        $sql = "INSERT INTO $this->tabela (id_paciente, nome_tratamento, data_consulta, hora_consulta, status, id_dentista) 
                VALUES (:id_paciente, :nome_tratamento, :data_consulta, :hora_consulta, :status, :id_dentista)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_paciente', $consulta->getIdPaciente(), PDO::PARAM_INT);
        $stmt->bindParam(':nome_tratamento', $consulta->getNomeTratamento(), PDO::PARAM_STR);
        $stmt->bindParam(':data_consulta', $consulta->getDataConsulta(), PDO::PARAM_STR);
        $stmt->bindParam(':hora_consulta', $consulta->getHoraConsulta(), PDO::PARAM_STR);
        $stmt->bindParam(':status', $consulta->getStatus(), PDO::PARAM_STR);
        $stmt->bindParam(':id_dentista', $consulta->getIdDentista(), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function alterarStatus($id_consulta, $novo_status) {
        $sql = "UPDATE $this->tabela SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $novo_status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_consulta, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function concluirTratamento($id_consulta, $valor) {
        $this->alterarStatus($id_consulta, 'Concluido');
        $sqlFin = "INSERT INTO financeiro (id_consulta, valor, status_pagamento) VALUES (:id_consulta, :valor, 'Pendente')";
        $stmtFin = $this->conn->prepare($sqlFin);
        $stmtFin->bindParam(':id_consulta', $id_consulta, PDO::PARAM_INT);
        $stmtFin->bindParam(':valor', $valor, PDO::PARAM_STR);
        $stmtFin->execute();
    }
}
?>
EOF

cat << 'EOF' > controller/consultaController.php
<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/consultaModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class ConsultaController {
    public function listar() {
        $model = new Consulta();
        return $model->listarAgenda();
    }
    public function listarProntuario($id_paciente) {
        $model = new Consulta();
        return $model->listarProntuario($id_paciente);
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'agendar') {
                $consulta = new Consulta();
                $consulta->setIdPaciente($_POST['id_paciente']);
                $consulta->setIdDentista($_POST['id_dentista']);
                $consulta->setNomeTratamento($_POST['nome_tratamento']);
                $consulta->setDataConsulta($_POST['data_consulta']);
                $consulta->setHoraConsulta($_POST['hora_consulta']);
                $consulta->setStatus('Agendado');
                $consulta->inserir($consulta);
                header("Location: ../view/agenda.php?sucesso=agendado");
                exit();
            }
            if ($_GET['action'] == 'concluir') {
                $model = new Consulta();
                $valorLimpo = str_replace('.', '', $_POST['valor']);
                $valorFinal = str_replace(',', '.', $valorLimpo);
                $model->concluirTratamento($_POST['id_consulta'], $valorFinal);
                header("Location: ../view/agenda.php?sucesso=concluido");
                exit();
            }
            if ($_GET['action'] == 'faltou') {
                $model = new Consulta();
                $model->alterarStatus($_GET['id'], 'Faltou');
                header("Location: ../view/agenda.php?sucesso=status_alterado");
                exit();
            }
            if ($_GET['action'] == 'cancelar') {
                $model = new Consulta();
                $model->alterarStatus($_GET['id'], 'Cancelado');
                header("Location: ../view/agenda.php?sucesso=status_alterado");
                exit();
            }
        }
    }
}
$ConsultaCtrl = new ConsultaController();
$ConsultaCtrl->handleRequest();
?>
EOF

cat << 'EOF' > model/financeiroModel.php
<?php
require_once '../config/conexao.php';

class Financeiro extends Conexao {
    private $tabela = 'financeiro';
    public function __construct() { parent::__construct(); }

    public function consultaPendentes() {
        $sql = "SELECT f.id, f.valor, c.nome_tratamento, p.nome as paciente_nome, c.data_consulta
                FROM $this->tabela f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.deleted_at IS NULL AND f.status_pagamento = 'Pendente' AND p.deleted_at IS NULL
                ORDER BY c.data_consulta ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receberPagamento($id, $forma) {
        $sql = "UPDATE $this->tabela SET status_pagamento = 'Pago', forma_recebimento = :forma, data_pagamento = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':forma', $forma, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function consultaBalanco() {
        $sql = "SELECT f.id, f.valor, f.status_pagamento, f.forma_recebimento, f.data_pagamento, f.incluir_ir, 
                       c.nome_tratamento, p.nome as paciente_nome
                FROM $this->tabela f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.deleted_at IS NULL AND f.status_pagamento = 'Pago'
                ORDER BY f.data_pagamento DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarIR($id) {
        $sql = "UPDATE $this->tabela SET incluir_ir = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function consultaIR($ano) {
        $sql = "SELECT f.valor, f.data_pagamento, p.nome as paciente_nome, p.cpf as paciente_cpf, p.responsavel_cpf, c.nome_tratamento
                FROM $this->tabela f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.incluir_ir = 1 AND YEAR(f.data_pagamento) = :ano AND f.deleted_at IS NULL
                ORDER BY f.data_pagamento ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaRecibo($id) {
        $sql = "SELECT f.valor, f.data_pagamento, f.forma_recebimento, p.nome as paciente_nome, p.cpf as paciente_cpf, p.telefone, c.nome_tratamento,
                       CONCAT(p.logradouro, ', ', p.numero, ' ', p.complemento, ' - ', p.bairro, ' - ', p.cidade, '/', p.uf) AS endereco
                FROM $this->tabela f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.id = :id AND f.deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
EOF

cat << 'EOF' > controller/financeiroController.php
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
EOF

cat << 'EOF' > view/header.php
<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntegraODONTO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; color: #004400; }
        .card { background-color: #ffffff; border: 2px solid #004400; border-radius: 12px; }
        .card-header { background-color: #004400; color: #ffffff; border-bottom: 2px solid #004400; }
        .card-header h1, .card-header h2, .card-header h3, .card-header h4, .card-header h5 { color: #ffffff; margin: 0; }
        h1, h2, h3, h4, h5, h6, label, p, span, td, th { color: #004400; }
        .btn-primary { background-color: #004400; border-color: #004400; color: #ffffff; }
        .btn-primary:hover { background-color: #003300; border-color: #003300; color: #ffffff; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #ffffff; border-right: 2px solid #004400; padding-top: 70px; }
        .sidebar a { padding: 15px 20px; text-decoration: none; font-size: 16px; color: #004400; display: block; }
        .sidebar a:hover { background-color: #004400; color: #ffffff; }
        .topbar { position: fixed; top: 0; left: 250px; right: 0; height: 60px; background-color: #ffffff; border-bottom: 2px solid #004400; display: flex; align-items: center; padding: 0 20px; z-index: 1000; }
        .main-content { margin-left: 250px; padding: 80px 20px 20px 20px; }
        .main-content-full { margin-left: 0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-home"></i> Início</a>
        <a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a>
        <a href="pacientes.php"><i class="fas fa-users"></i> Pacientes</a>
        <a href="balanco.php"><i class="fas fa-wallet"></i> Financeiro</a>
        <?php if($_SESSION['usuario_nivel'] != 3): ?>
        <a href="relatorio_ir.php"><i class="fas fa-file-invoice-dollar"></i> Relatório IR</a>
        <?php endif; ?>
        <?php if($_SESSION['usuario_nivel'] == 1): ?>
        <a href="usuarios.php"><i class="fas fa-user-shield"></i> Usuários</a>
        <a href="config_clinica.php"><i class="fas fa-cogs"></i> Configurações</a>
        <?php endif; ?>
        <a href="../controller/usuarioController.php?action=logout"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>
    <div class="topbar">
        <h4 class="mb-0"><i class="fas fa-tooth"></i> IntegraODONTO</h4>
        <span class="ms-auto"><i class="fas fa-user-md"></i> Olá, <?php echo $_SESSION['usuario_nome']; ?></span>
    </div>
    <div class="main-content">
EOF

cat << 'EOF' > view/footer.php
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOF

cat << 'EOF' > view/login.php
<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntegraODONTO - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; color: #004400; }
        .card { background-color: #ffffff; border: 2px solid #004400; border-radius: 12px; }
        .card-header { background-color: #004400; color: #ffffff; border-bottom: 2px solid #004400; }
        .btn-primary { background-color: #004400; border-color: #004400; color: #ffffff; }
        .btn-primary:hover { background-color: #003300; border-color: #003300; color: #ffffff; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4 class="text-white"><i class="fas fa-tooth"></i> IntegraODONTO</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Login ou senha inválidos.</div>"; ?>
                    <form action="../controller/usuarioController.php?action=logar" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOF

cat << 'EOF' > view/dashboard.php
<?php require_once 'header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h1 class="display-5">Bem-vindo ao IntegraODONTO</h1>
                <p class="lead">Selecione uma opção no menu lateral para começar.</p>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/agenda.php
<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
$controller = new ConsultaController();
$consultas = $controller->listar();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Agenda de Consultas</h2>
    <a href="formConsulta.php" class="btn btn-primary">Nova Consulta</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data/Hora</th><th>Paciente</th><th>Dentista</th><th>Tratamento</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($consultas as $c): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($c['data_consulta'])) . ' ' . substr($c['hora_consulta'],0,5); ?></td>
                    <td><?php echo $c['paciente']; ?></td>
                    <td><?php echo $c['dentista']; ?></td>
                    <td><?php echo $c['nome_tratamento']; ?></td>
                    <td><?php echo $c['status']; ?></td>
                    <td>
                        <?php if($c['status'] == 'Agendado' || $c['status'] == 'Aguardando'): ?>
                            <a href="../controller/consultaController.php?action=faltou&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">Faltou</a>
                            <a href="../controller/consultaController.php?action=cancelar&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger">Cancelar</a>
                        <?php endif; ?>
                        
                        <?php if($c['status'] != 'Concluido' && $c['status'] != 'Cancelado'): ?>
                            <form action="../controller/consultaController.php?action=concluir" method="POST" class="d-inline mt-1">
                                <input type="hidden" name="id_consulta" value="<?php echo $c['id']; ?>">
                                <input type="text" name="valor" placeholder="R$ 0,00" required class="form-control form-control-sm d-inline" style="width: 100px;">
                                <button type="submit" class="btn btn-sm btn-success">Concluir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/formConsulta.php
<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
require_once '../controller/usuarioController.php';

$pacienteCtrl = new PacienteController();
$pacientes = $pacienteCtrl->consultar();

$usuarioCtrl = new UsuarioController();
$dentistas = $usuarioCtrl->listarDentistas();
?>
<div class="card shadow-sm w-50 mx-auto">
    <div class="card-header"><h5>Agendar Nova Consulta</h5></div>
    <div class="card-body">
        <form action="../controller/consultaController.php?action=agendar" method="POST" class="row g-3">
            <div class="col-md-12">
                <label>Paciente</label>
                <select name="id_paciente" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['nome']; ?> (CPF: <?php echo $p['cpf']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12">
                <label>Dentista Responsável</label>
                <select name="id_dentista" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach($dentistas as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo $d['nome_completo']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-12"><label>Tratamento Previsto</label><input type="text" name="nome_tratamento" class="form-control" required></div>
            <div class="col-md-6"><label>Data</label><input type="date" name="data_consulta" class="form-control" required></div>
            <div class="col-md-6"><label>Hora</label><input type="time" name="hora_consulta" class="form-control" required></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Agendar</button></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/prontuario.php
<?php 
require_once 'header.php'; 
require_once '../controller/consultaController.php';
require_once '../controller/pacienteController.php';

if(!isset($_GET['id_paciente'])) { exit; }

$pCtrl = new PacienteController();
$paciente = $pCtrl->consultaID($_GET['id_paciente']);

$cCtrl = new ConsultaController();
$historico = $cCtrl->listarProntuario($_GET['id_paciente']);
?>
<div class="card shadow-sm">
    <div class="card-header"><h5>Prontuário Clínico: <?php echo $paciente['nome']; ?></h5></div>
    <div class="card-body">
        <a href="pacientes.php" class="btn btn-secondary mb-3">Voltar</a>
        <table class="table table-bordered">
            <thead class="table-dark"><tr><th>Data</th><th>Tratamento Realizado</th><th>Dentista Responsável</th></tr></thead>
            <tbody>
                <?php foreach($historico as $h): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($h['data_consulta'])); ?></td>
                    <td><?php echo $h['nome_tratamento']; ?></td>
                    <td><?php echo $h['dentista']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($historico)): ?><tr><td colspan="3" class="text-center">Nenhum histórico clínico encontrado.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/usuarios.php
<?php 
require_once 'header.php'; 
require_once '../controller/usuarioController.php';
if($_SESSION['usuario_nivel'] != 1) { echo "Acesso negado."; exit; }
$controller = new UsuarioController();
$usuarios = $controller->consultar();
?>
<div class="card shadow-sm mb-4">
    <div class="card-header"><h5>Novo Usuário</h5></div>
    <div class="card-body">
        <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Erro: Login já existente.</div>"; ?>
        <form action="../controller/usuarioController.php?action=cadastrarUsuario" method="POST" class="row g-3">
            <div class="col-md-4"><input type="text" name="nome_completo" class="form-control" placeholder="Nome Completo" required></div>
            <div class="col-md-2"><input type="text" name="cpf" class="form-control" placeholder="CPF" required></div>
            <div class="col-md-2"><input type="text" name="login" class="form-control" placeholder="Login" required></div>
            <div class="col-md-2"><input type="password" name="senha" class="form-control" placeholder="Senha" required></div>
            <div class="col-md-2">
                <select name="nivel_acesso" class="form-select">
                    <option value="1">Administrador</option>
                    <option value="2">Dentista</option>
                    <option value="3">Recepção</option>
                </select>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Cadastrar</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><h5>Usuários Cadastrados</h5></div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Nome</th><th>CPF</th><th>Login</th><th>Nível</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['nome_completo']; ?></td>
                    <td><?php echo $u['cpf']; ?></td>
                    <td><?php echo $u['login']; ?></td>
                    <td><?php echo $u['nivel_acesso'] == 1 ? 'Admin' : ($u['nivel_acesso'] == 2 ? 'Dentista' : 'Recepção'); ?></td>
                    <td><a href="../controller/usuarioController.php?action=excluirUsuario&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?');">Excluir</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/pacientes.php
<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
$controller = new PacienteController();
$pacientes = $controller->consultar();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pacientes</h2>
    <a href="formPaciente.php" class="btn btn-primary">Novo Paciente</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Nome</th><th>CPF</th><th>Telefone</th><th>Email</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($pacientes as $p): ?>
                <tr>
                    <td><?php echo $p['nome']; ?></td>
                    <td><?php echo $p['cpf']; ?></td>
                    <td><?php echo $p['telefone']; ?></td>
                    <td><?php echo $p['email']; ?></td>
                    <td>
                        <a href="prontuario.php?id_paciente=<?php echo $p['id']; ?>" class="btn btn-sm btn-success">Prontuário</a>
                        <a href="formPaciente.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="../controller/pacienteController.php?action=excluirPaciente&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/formPaciente.php
<?php 
require_once 'header.php'; 
require_once '../controller/pacienteController.php';
$p = null;
if(isset($_GET['id'])){
    $controller = new PacienteController();
    $p = $controller->consultaID($_GET['id']);
}
$action = $p ? "editarPaciente" : "cadastrarPaciente";
?>
<div class="card shadow-sm">
    <div class="card-header"><h5><?php echo $p ? 'Editar Paciente' : 'Novo Paciente'; ?></h5></div>
    <div class="card-body">
        <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Erro: CPF duplicado.</div>"; ?>
        <form action="../controller/pacienteController.php?action=<?php echo $action; ?>" method="POST" enctype="multipart/form-data" class="row g-3">
            <?php if($p): ?><input type="hidden" name="meuid" value="<?php echo $p['id']; ?>"><?php endif; ?>
            <div class="col-md-6"><label>Nome Completo</label><input type="text" name="nome" class="form-control" value="<?php echo $p ? $p['nome'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Data Nasc.</label><input type="date" name="data_nascimento" class="form-control" value="<?php echo $p ? $p['data_nascimento'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Sexo</label><select name="sexo" class="form-select"><option value="M" <?php if($p && $p['sexo']=='M') echo 'selected';?>>M</option><option value="F" <?php if($p && $p['sexo']=='F') echo 'selected';?>>F</option><option value="O" <?php if($p && $p['sexo']=='O') echo 'selected';?>>Outro</option></select></div>
            <div class="col-md-3"><label>RG</label><input type="text" name="rg" class="form-control" value="<?php echo $p ? $p['rg'] : ''; ?>"></div>
            <div class="col-md-3"><label>CPF</label><input type="text" name="cpf" class="form-control" value="<?php echo $p ? $p['cpf'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo $p ? $p['telefone'] : ''; ?>" required></div>
            <div class="col-md-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?php echo $p ? $p['email'] : ''; ?>"></div>
            <div class="col-md-2"><label>CEP</label><input type="text" name="cep" class="form-control" value="<?php echo $p ? $p['cep'] : ''; ?>"></div>
            <div class="col-md-5"><label>Logradouro</label><input type="text" name="logradouro" class="form-control" value="<?php echo $p ? $p['logradouro'] : ''; ?>" required></div>
            <div class="col-md-2"><label>Número</label><input type="text" name="numero" class="form-control" value="<?php echo $p ? $p['numero'] : ''; ?>"></div>
            <div class="col-md-3"><label>Complemento</label><input type="text" name="complemento" class="form-control" value="<?php echo $p ? $p['complemento'] : ''; ?>"></div>
            <div class="col-md-4"><label>Bairro</label><input type="text" name="bairro" class="form-control" value="<?php echo $p ? $p['bairro'] : ''; ?>"></div>
            <div class="col-md-6"><label>Cidade</label><input type="text" name="cidade" class="form-control" value="<?php echo $p ? $p['cidade'] : ''; ?>"></div>
            <div class="col-md-2"><label>UF</label><input type="text" name="uf" class="form-control" value="<?php echo $p ? $p['uf'] : ''; ?>"></div>
            <div class="col-md-6"><label>Nome Responsável (Menor)</label><input type="text" name="responsavel_nome" class="form-control" value="<?php echo $p ? $p['responsavel_nome'] : ''; ?>"></div>
            <div class="col-md-6"><label>CPF Responsável</label><input type="text" name="responsavel_cpf" class="form-control" value="<?php echo $p ? $p['responsavel_cpf'] : ''; ?>"></div>
            <div class="col-md-12"><label>Foto (.jpg, .png)</label><input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png"></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Salvar</button> <a href="pacientes.php" class="btn btn-danger">Cancelar</a></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/config_clinica.php
<?php 
require_once 'header.php';
require_once '../controller/clinicaController.php'; 
if($_SESSION['usuario_nivel'] != 1) { echo "Acesso negado."; exit; }
$controller = new ClinicaController();
$dados = $controller->carregar();
?>
<div class="card shadow-sm w-50 mx-auto">
    <div class="card-header"><h5>Dados da Clínica / Consultório</h5></div>
    <div class="card-body">
        <?php if(isset($_GET['salvo'])) echo "<div class='alert alert-success'>Dados atualizados!</div>"; ?>
        <form action="../controller/clinicaController.php?action=salvarClinica" method="POST" class="row g-3">
            <div class="col-12"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?php echo isset($dados['nome']) ? $dados['nome'] : ''; ?>" required></div>
            <div class="col-md-4"><label>Tipo Doc</label><select name="tipo_documento" class="form-select"><option value="CNPJ" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CNPJ') ? 'selected' : ''; ?>>CNPJ</option><option value="CPF" <?php echo (isset($dados['tipo_documento']) && $dados['tipo_documento'] == 'CPF') ? 'selected' : ''; ?>>CPF</option></select></div>
            <div class="col-md-8"><label>Documento</label><input type="text" name="documento" class="form-control" value="<?php echo isset($dados['documento']) ? $dados['documento'] : ''; ?>" required></div>
            <div class="col-12"><label>Endereço Completo</label><input type="text" name="endereco" class="form-control" value="<?php echo isset($dados['endereco']) ? $dados['endereco'] : ''; ?>" required></div>
            <div class="col-12"><label>Telefone</label><input type="text" name="telefone" class="form-control" value="<?php echo isset($dados['telefone']) ? $dados['telefone'] : ''; ?>" required></div>
            <div class="col-12"><button type="submit" class="btn btn-primary">Salvar Configurações</button></div>
        </form>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/balanco.php
<?php 
require_once 'header.php';
require_once '../controller/financeiroController.php'; 
$controller = new FinanceiroController();
$pendentes = $controller->listarPendentes();
$balanco = $controller->listarBalanco();
?>
<h2>Gestão Financeira</h2>

<div class="card shadow-sm mt-3 border-danger">
    <div class="card-header bg-danger text-white"><h5>Lançamentos Pendentes</h5></div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data Consulta</th><th>Paciente</th><th>Tratamento</th><th>Valor (R$)</th><th>Baixa</th></tr></thead>
            <tbody>
                <?php foreach ($pendentes as $p): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($p['data_consulta'])); ?></td>
                        <td><?php echo $p['paciente_nome']; ?></td>
                        <td><?php echo $p['nome_tratamento']; ?></td>
                        <td><?php echo number_format($p['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <form action="../controller/financeiroController.php?action=receberPagamento" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <select name="forma_recebimento" class="form-select form-select-sm d-inline w-auto" required>
                                    <option value="">Forma...</option>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="PIX">PIX</option>
                                    <option value="Cartao de Credito">Cartão de Crédito</option>
                                    <option value="Cartao de Debito">Cartão de Débito</option>
                                    <option value="Boleto">Boleto</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Receber</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4 border-success">
    <div class="card-header bg-success text-white"><h5>Recebimentos Concluídos</h5></div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead><tr><th>Data Pgto</th><th>Paciente</th><th>Tratamento</th><th>Valor</th><th>Ação</th></tr></thead>
            <tbody>
                <?php foreach ($balanco as $b): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($b['data_pagamento'])); ?></td>
                        <td><?php echo $b['paciente_nome']; ?></td>
                        <td><?php echo $b['nome_tratamento']; ?></td>
                        <td>R$ <?php echo number_format($b['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <?php if($_SESSION['usuario_nivel'] != 3): ?>
                                <?php if ($b['incluir_ir'] == 0): ?>
                                    <a href="../controller/financeiroController.php?action=incluirIR&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-success">Incluir no IR</a>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No IR</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="recibo_pdf.php?id=<?php echo $b['id']; ?>" target="_blank" class="btn btn-sm btn-primary">Gerar Recibo</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/relatorio_ir.php
<?php 
require_once 'header.php';
require_once '../controller/financeiroController.php'; 
if($_SESSION['usuario_nivel'] == 3) { echo "Acesso negado."; exit; }
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$controller = new FinanceiroController();
$relatorio = $controller->listarIR($ano);
$total = 0;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Relatório Imposto de Renda</h2>
    <form method="GET" class="d-flex">
        <input type="number" name="ano" class="form-control me-2" value="<?php echo $ano; ?>" placeholder="Ano">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-dark"><tr><th>Data Pgto</th><th>Paciente</th><th>CPF Declarado</th><th>Tratamento</th><th>Valor</th></tr></thead>
            <tbody>
                <?php foreach ($relatorio as $r): 
                    $cpfFinal = !empty($r['responsavel_cpf']) ? $r['responsavel_cpf'] : $r['paciente_cpf'];
                    $total += $r['valor'];
                ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['data_pagamento'])); ?></td>
                        <td><?php echo $r['paciente_nome']; ?></td>
                        <td><?php echo $cpfFinal; ?></td>
                        <td><?php echo $r['nome_tratamento']; ?></td>
                        <td>R$ <?php echo number_format($r['valor'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end"><strong>TOTAL DO ANO FISCAL:</strong></td>
                    <td><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php require_once 'footer.php'; ?>
EOF

cat << 'EOF' > view/recibo_pdf.php
<?php 
require_once '../controller/financeiroController.php';
if (!isset($_GET['id'])) { exit; }
$controller = new FinanceiroController();
$dados = $controller->gerarRecibo($_GET['id']);

if(!$dados) {
    echo "<h2 style='color:red;'>Erro: Os dados da clinica nao foram configurados no sistema. Acesse as configuracoes e preencha o nome e documento da clinica antes de emitir recibos.</h2>";
    exit;
}

$recibo = $dados['recibo'];
$clinica = $dados['clinica'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
</head>
<body onload="window.print()">
<div style="border: 2px solid #000; padding: 40px; width: 700px; margin: 0 auto; font-family: Arial, sans-serif;">
    <h2 style="text-align: center;">RECIBO ODONTOLÓGICO</h2>
    <hr>
    <p><strong><?php echo $clinica['nome']; ?></strong></p>
    <p><?php echo $clinica['tipo_documento']; ?>: <?php echo $clinica['documento']; ?></p>
    <p>Endereço: <?php echo $clinica['endereco']; ?></p>
    <p>Telefone: <?php echo $clinica['telefone']; ?></p>
    <hr>
    <p style="font-size: 16px; line-height: 1.6;">
        Recebi(emos) de <strong><?php echo $recibo['paciente_nome']; ?></strong>, portador(a) do CPF/CNPJ nº <strong><?php echo $recibo['paciente_cpf']; ?></strong>,
        a importância de <strong>R$ <?php echo number_format($recibo['valor'], 2, ',', '.'); ?></strong>, referente aos serviços odontológicos prestados no tratamento de <strong><?php echo $recibo['nome_tratamento']; ?></strong>.
    </p>
    <br><br>
    <p style="text-align: right;">Data: <?php echo date('d/m/Y', strtotime($recibo['data_pagamento'])); ?></p>
    <br><br><br><br>
    <div style="text-align: center; border-top: 1px solid #000; width: 300px; margin: 0 auto; padding-top: 10px;">
        Assinatura do Profissional / Clínica
    </div>
</div>
</body>
</html>
EOF