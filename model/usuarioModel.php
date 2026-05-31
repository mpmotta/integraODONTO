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
        $sql = "SELECT id, nome_completo, cpf, login, nivel_acesso FROM " . $this->tabela . " WHERE deleted_at IS NULL ORDER BY nome_completo ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaID($id) {
        $sql = "SELECT * FROM " . $this->tabela . " WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultaDentistas() {
        $sql = "SELECT id, nome_completo FROM " . $this->tabela . " WHERE nivel_acesso = 2 AND deleted_at IS NULL ORDER BY nome_completo ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logar($login, $senha) {
        $sql = "SELECT id, nome_completo, login, senha, nivel_acesso FROM " . $this->tabela . " WHERE login = :login AND deleted_at IS NULL LIMIT 1";
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
        $sqlCheck = "SELECT COUNT(id) FROM " . $this->tabela . " WHERE login = :login AND deleted_at IS NULL";
        $check = $this->conn->prepare($sqlCheck);
        $l = $usuario->getLogin();
        $check->bindParam(':login', $l, PDO::PARAM_STR);
        $check->execute();
        if($check->fetchColumn() > 0) {
            return false;
        }

        $sql = "INSERT INTO " . $this->tabela . " (nome_completo, cpf, login, senha, nivel_acesso) VALUES (:nome_completo, :cpf, :login, :senha, :nivel_acesso)";
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

    public function alterarSenha($id, $nova_senha) {
        $sql = "UPDATE " . $this->tabela . " SET senha = :senha WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function excluir($id) {
        $sql = "UPDATE " . $this->tabela . " SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
