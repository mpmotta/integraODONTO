<?php
date_default_timezone_set('America/Sao_Paulo');

abstract class Conexao {
    private $servidor = 'localhost';
    private $user = 'root';
    private $pass = 'apple';
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
