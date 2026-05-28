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
