<?php
require_once '../config/conexao.php';

class Financeiro extends Conexao {
    private $tabela = 'financeiro';
    public function __construct() { parent::__construct(); }

    public function consultaPendentes() {
        $sql = "SELECT f.id, f.valor, c.nome_tratamento, p.nome as paciente_nome, c.data_consulta
                FROM " . $this->tabela . " f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.deleted_at IS NULL AND f.status_pagamento = 'Pendente' AND p.deleted_at IS NULL
                ORDER BY c.data_consulta ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receberPagamento($id, $forma) {
        $sql = "UPDATE " . $this->tabela . " SET status_pagamento = 'Pago', forma_recebimento = :forma, data_pagamento = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':forma', $forma, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function consultaBalanco() {
        $sql = "SELECT f.id, f.valor, f.status_pagamento, f.forma_recebimento, f.data_pagamento, f.incluir_ir, 
                       c.nome_tratamento, p.nome as paciente_nome
                FROM " . $this->tabela . " f
                INNER JOIN consultas c ON f.id_consulta = c.id
                INNER JOIN pacientes p ON c.id_paciente = p.id
                WHERE f.deleted_at IS NULL AND f.status_pagamento = 'Pago'
                ORDER BY f.data_pagamento DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarIR($id) {
        $sql = "UPDATE " . $this->tabela . " SET incluir_ir = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function consultaIR($ano) {
        $sql = "SELECT f.valor, f.data_pagamento, p.nome as paciente_nome, p.cpf as paciente_cpf, p.responsavel_cpf, c.nome_tratamento
                FROM " . $this->tabela . " f
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
                FROM " . $this->tabela . " f
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
