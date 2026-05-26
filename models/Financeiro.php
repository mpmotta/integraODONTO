<?php
require_once 'config/Database.php';

class Financeiro {
    private $conn;
    private $table_name = "financeiro";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listar() {
        $query = "SELECT f.*, c.data_hora_inicio, c.tipo_agendamento, c.nome_tratamento, p.nome as paciente_nome, p.cpf as paciente_cpf
                  FROM " . $this->table_name . " f
                  INNER JOIN consultas c ON f.id_consulta = c.id
                  INNER JOIN pacientes p ON c.id_paciente = p.id
                  WHERE f.deleted_at IS NULL 
                  ORDER BY f.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmarPagamento($id, $forma_recebimento, $data_pagamento) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status_pagamento = 'Pago', 
                      forma_recebimento = :forma_recebimento, 
                      data_pagamento = :data_pagamento 
                  WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":forma_recebimento", $forma_recebimento);
        $stmt->bindParam(":data_pagamento", $data_pagamento);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    public function relatorioAnoFiscal($ano) {
        $query = "SELECT f.data_pagamento, p.nome as paciente_nome, p.cpf as paciente_cpf, p.responsavel_cpf, 
                         c.tipo_agendamento, c.nome_tratamento, f.valor
                  FROM " . $this->table_name . " f
                  INNER JOIN consultas c ON f.id_consulta = c.id
                  INNER JOIN pacientes p ON c.id_paciente = p.id
                  WHERE f.status_pagamento = 'Pago' 
                  AND YEAR(f.data_pagamento) = :ano 
                  AND f.deleted_at IS NULL
                  ORDER BY f.data_pagamento ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ano", $ano);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
