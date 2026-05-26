<?php
require_once 'config/Database.php';

class Paciente {
    private $conn;
    private $table_name = "pacientes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE deleted_at IS NULL ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, data_nascimento, sexo, rg, cpf, email, telefone, cep, logradouro, numero, complemento, bairro, cidade, uf, responsavel_nome, responsavel_cpf, foto_path) 
                  VALUES 
                  (:nome, :data_nascimento, :sexo, :rg, :cpf, :email, :telefone, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf, :responsavel_nome, :responsavel_cpf, :foto_path)";
        
        $stmt = $this->conn->prepare($query);
        $this->bindValores($stmt, $dados);
        return $stmt->execute();
    }

    public function atualizar($id, $dados) {
        $query = "UPDATE " . $this->table_name . " SET 
                  nome = :nome, data_nascimento = :data_nascimento, sexo = :sexo, rg = :rg, cpf = :cpf, 
                  email = :email, telefone = :telefone, cep = :cep, logradouro = :logradouro, numero = :numero, 
                  complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf, 
                  responsavel_nome = :responsavel_nome, responsavel_cpf = :responsavel_cpf";

        if (isset($dados['foto_path'])) {
            $query .= ", foto_path = :foto_path";
        }

        $query .= " WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $this->bindValores($stmt, $dados);
        
        return $stmt->execute();
    }

    public function excluir($id) {
        $query = "UPDATE " . $this->table_name . " SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    private function bindValores($stmt, $dados) {
        $stmt->bindParam(":nome", $dados['nome']);
        $stmt->bindParam(":data_nascimento", $dados['data_nascimento']);
        $stmt->bindParam(":sexo", $dados['sexo']);
        $stmt->bindParam(":rg", $dados['rg']);
        $stmt->bindParam(":cpf", $dados['cpf']);
        $stmt->bindParam(":email", $dados['email']);
        $stmt->bindParam(":telefone", $dados['telefone']);
        $stmt->bindParam(":cep", $dados['cep']);
        $stmt->bindParam(":logradouro", $dados['logradouro']);
        $stmt->bindParam(":numero", $dados['numero']);
        $stmt->bindParam(":complemento", $dados['complemento']);
        $stmt->bindParam(":bairro", $dados['bairro']);
        $stmt->bindParam(":cidade", $dados['cidade']);
        $stmt->bindParam(":uf", $dados['uf']);
        $stmt->bindParam(":responsavel_nome", $dados['responsavel_nome']);
        $stmt->bindParam(":responsavel_cpf", $dados['responsavel_cpf']);

        if (isset($dados['foto_path'])) {
            $stmt->bindParam(":foto_path", $dados['foto_path']);
        }
    }
}
?>
