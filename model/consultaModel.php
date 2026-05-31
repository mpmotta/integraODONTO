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
        $sql = "SELECT c.id, c.nome_tratamento, c.data_consulta, c.hora_consulta, c.status, p.nome as paciente, p.telefone, u.nome_completo as dentista 
                FROM " . $this->tabela . " c
                INNER JOIN pacientes p ON c.id_paciente = p.id
                INNER JOIN usuarios u ON c.id_dentista = u.id
                WHERE c.deleted_at IS NULL AND p.deleted_at IS NULL AND u.deleted_at IS NULL
                ORDER BY c.data_consulta DESC, c.hora_consulta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaID($id) {
        $sql = "SELECT c.*, p.nome as paciente_nome FROM " . $this->tabela . " c INNER JOIN pacientes p ON c.id_paciente = p.id WHERE c.id = :id AND c.deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarProntuario($id_paciente) {
        $sql = "SELECT c.id, c.nome_tratamento, c.data_consulta, c.status, u.nome_completo as dentista 
                FROM " . $this->tabela . " c
                INNER JOIN usuarios u ON c.id_dentista = u.id
                WHERE c.id_paciente = :id_paciente AND c.status = 'Concluido' AND c.deleted_at IS NULL
                ORDER BY c.data_consulta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_paciente', $id_paciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodasConsultasCalendario() {
        $sql = "SELECT c.id, c.nome_tratamento, c.data_consulta, c.hora_consulta, p.nome as paciente 
                FROM " . $this->tabela . " c 
                INNER JOIN pacientes p ON c.id_paciente = p.id 
                WHERE c.deleted_at IS NULL AND p.deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inserir(Consulta $consulta) {
        $sql = "INSERT INTO " . $this->tabela . " (id_paciente, nome_tratamento, data_consulta, hora_consulta, status, id_dentista) 
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

    public function editar(Consulta $consulta) {
        $sql = "UPDATE " . $this->tabela . " SET id_paciente = :id_paciente, id_dentista = :id_dentista, nome_tratamento = :nome_tratamento, data_consulta = :data_consulta, hora_consulta = :hora_consulta WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_paciente', $consulta->getIdPaciente(), PDO::PARAM_INT);
        $stmt->bindParam(':id_dentista', $consulta->getIdDentista(), PDO::PARAM_INT);
        $stmt->bindParam(':nome_tratamento', $consulta->getNomeTratamento(), PDO::PARAM_STR);
        $stmt->bindParam(':data_consulta', $consulta->getDataConsulta(), PDO::PARAM_STR);
        $stmt->bindParam(':hora_consulta', $consulta->getHoraConsulta(), PDO::PARAM_STR);
        $stmt->bindParam(':id', $consulta->getId(), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function alterarStatus($id_consulta, $novo_status) {
        $sql = "UPDATE " . $this->tabela . " SET status = :status WHERE id = :id";
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

    public function inserirFotoTratamento($id_consulta, $caminho) {
        $sql = "INSERT INTO tratamento_fotos (id_consulta, caminho_foto) VALUES (:id_consulta, :caminho)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_consulta', $id_consulta, PDO::PARAM_INT);
        $stmt->bindParam(':caminho', $caminho, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function listarFotosTratamento($id_consulta) {
        $sql = "SELECT caminho_foto FROM tratamento_fotos WHERE id_consulta = :id_consulta ORDER BY data_upload ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_consulta', $id_consulta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>