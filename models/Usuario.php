<?php
require_once 'config/Database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($login, $senha) {
        $query = "SELECT id, login, senha, nivel_acesso FROM " . $this->table_name . " WHERE login = :login AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senha, $row['senha'])) {
                return $row;
            }
        }
        return false;
    }

    public function listar() {
        $query = "SELECT id, login, nivel_acesso FROM " . $this->table_name . " WHERE deleted_at IS NULL ORDER BY login ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $query = "SELECT id, login, nivel_acesso FROM " . $this->table_name . " WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        $query = "INSERT INTO " . $this->table_name . " (login, senha, nivel_acesso) VALUES (:login, :senha, :nivel_acesso)";
        $stmt = $this->conn->prepare($query);
        
        $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(":login", $dados['login']);
        $stmt->bindParam(":senha", $senha_hash);
        $stmt->bindParam(":nivel_acesso", $dados['nivel_acesso']);
        
        return $stmt->execute();
    }

    public function atualizar($id, $dados) {
        if (!empty($dados['senha'])) {
            $query = "UPDATE " . $this->table_name . " SET login = :login, senha = :senha, nivel_acesso = :nivel_acesso WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($query);
            $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $stmt->bindParam(":senha", $senha_hash);
        } else {
            $query = "UPDATE " . $this->table_name . " SET login = :login, nivel_acesso = :nivel_acesso WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(":login", $dados['login']);
        $stmt->bindParam(":nivel_acesso", $dados['nivel_acesso']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function excluir($id) {
        $query = "UPDATE " . $this->table_name . " SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
