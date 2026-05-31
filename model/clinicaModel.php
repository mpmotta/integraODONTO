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
        $sql = "SELECT * FROM " . $this->tabela . " LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar(Clinica $clinica) {
        $sqlCheck = "SELECT COUNT(id) as total FROM " . $this->tabela;
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute();
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            $sql = "UPDATE " . $this->tabela . " SET nome = :nome, tipo_documento = :tipo_documento, documento = :documento, endereco = :endereco, telefone = :telefone";
        } else {
            $sql = "INSERT INTO " . $this->tabela . " (nome, tipo_documento, documento, endereco, telefone) VALUES (:nome, :tipo_documento, :documento, :endereco, :telefone)";
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
