<?php
require_once 'config/Database.php';

class Consulta {
    private $conn;
    private $table_name = "consultas";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listar() {
        $query = "SELECT c.*, p.nome as paciente_nome, p.telefone as paciente_telefone, u.login as dentista_nome 
                  FROM " . $this->table_name . " c
                  INNER JOIN pacientes p ON c.id_paciente = p.id
                  INNER JOIN usuarios u ON c.id_dentista = u.id
                  WHERE c.deleted_at IS NULL 
                  ORDER BY c.data_hora_inicio ASC";
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

    public function listarPorPaciente($id_paciente) {
        $query = "SELECT c.*, u.login as dentista_nome 
                  FROM " . $this->table_name . " c
                  INNER JOIN usuarios u ON c.id_dentista = u.id
                  WHERE c.id_paciente = :id_paciente AND c.deleted_at IS NULL 
                  ORDER BY c.data_hora_inicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_paciente", $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_paciente, tipo_agendamento, nome_tratamento, data_hora_inicio, data_hora_fim, status, id_dentista) 
                  VALUES 
                  (:id_paciente, :tipo_agendamento, :nome_tratamento, :data_hora_inicio, :data_hora_fim, :status, :id_dentista)";
        
        $stmt = $this->conn->prepare($query);
        $this->bindValores($stmt, $dados);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function atualizar($id, $dados) {
        $query = "UPDATE " . $this->table_name . " SET 
                  id_paciente = :id_paciente, tipo_agendamento = :tipo_agendamento, 
                  nome_tratamento = :nome_tratamento, data_hora_inicio = :data_hora_inicio, 
                  data_hora_fim = :data_hora_fim, status = :status, id_dentista = :id_dentista 
                  WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $this->bindValores($stmt, $dados);
        
        return $stmt->execute();
    }

    public function atualizarStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function excluir($id) {
        $query = "UPDATE " . $this->table_name . " SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function listarPacientes() {
        $query = "SELECT id, nome, cpf FROM pacientes WHERE deleted_at IS NULL ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarDentistas() {
        $query = "SELECT id, login FROM usuarios WHERE nivel_acesso IN (1, 2) AND deleted_at IS NULL ORDER BY login ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarLancamentoFinanceiro($id_consulta) {
        $query = "SELECT id FROM financeiro WHERE id_consulta = :id_consulta AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_consulta", $id_consulta);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function gerarLancamentoFinanceiro($id_consulta, $valor) {
        $query = "INSERT INTO financeiro (id_consulta, valor, status_pagamento) VALUES (:id_consulta, :valor, 'Pendente')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_consulta", $id_consulta);
        $stmt->bindParam(":valor", $valor);
        return $stmt->execute();
    }

    private function bindValores($stmt, $dados) {
        $stmt->bindParam(":id_paciente", $dados['id_paciente']);
        $stmt->bindParam(":tipo_agendamento", $dados['tipo_agendamento']);
        $stmt->bindParam(":nome_tratamento", $dados['nome_tratamento']);
        $stmt->bindParam(":data_hora_inicio", $dados['data_hora_inicio']);
        $stmt->bindParam(":data_hora_fim", $dados['data_hora_fim']);
        $stmt->bindParam(":status", $dados['status']);
        $stmt->bindParam(":id_dentista", $dados['id_dentista']);
    }
}
?>
