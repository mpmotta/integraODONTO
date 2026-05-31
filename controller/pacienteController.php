<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once('../model/pacienteModel.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit();
}

class PacienteController {
    public function consultar() {
        $pacienteModel = new Paciente();
        return $pacienteModel->consulta();
    }
    public function consultaID($id) {
        $pacienteModel = new Paciente();
        return $pacienteModel->consultaID($id);
    }
    public function handleRequest() {
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'buscarPacientesAjax') {
                $pacienteModel = new Paciente();
                $dados = $pacienteModel->buscarPacientesAjax($_GET['q']);
                header('Content-Type: application/json');
                echo json_encode($dados);
                exit();
            }
            if ($_GET['action'] == 'cadastrarPaciente' || $_GET['action'] == 'editarPaciente') {
                $paciente = new Paciente();
                if($_GET['action'] == 'editarPaciente') $paciente->setId($_POST['meuid']);
                
                $paciente->setNome($_POST['nome']);
                $paciente->setDataNascimento($_POST['data_nascimento']);
                $paciente->setSexo($_POST['sexo']);
                $paciente->setRg($_POST['rg']);
                $paciente->setCpf(preg_replace('/[^0-9]/', '', $_POST['cpf']));
                $paciente->setEmail($_POST['email']);
                $paciente->setTelefone(preg_replace('/[^0-9]/', '', $_POST['telefone']));
                $paciente->setCep(preg_replace('/[^0-9]/', '', $_POST['cep']));
                $paciente->setLogradouro($_POST['logradouro']);
                $paciente->setNumero($_POST['numero']);
                $paciente->setComplemento($_POST['complemento']);
                $paciente->setBairro($_POST['bairro']);
                $paciente->setCidade($_POST['cidade']);
                $paciente->setUf($_POST['uf']);
                $paciente->setResponsavelNome($_POST['responsavel_nome']);
                $paciente->setResponsavelCpf(preg_replace('/[^0-9]/', '', $_POST['responsavel_cpf']));
                
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    $permitidos = array('jpg', 'jpeg', 'png');
                    if(in_array($extensao, $permitidos)) {
                        $tmp_nome = md5($_FILES['foto']['name'] . date('d-m-Y-h-i-s')) . "." . $extensao;
                        move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/pacientes/' . $tmp_nome);
                        $paciente->setFotoPath('../uploads/pacientes/' . $tmp_nome);
                    }
                }

                $pacienteModel = new Paciente();
                if($_GET['action'] == 'cadastrarPaciente') {
                    $resultado = $pacienteModel->inserir($paciente);
                    if($resultado) {
                        header('Location: ../view/pacientes.php?cadastro=ok');
                    } else {
                        header('Location: ../view/formPaciente.php?erro=cpf_duplicado');
                    }
                } else {
                    $resultado = $pacienteModel->editar($paciente, $paciente->getId());
                    if($resultado) {
                        header("Location: ../view/pacientes.php?edit=ok");
                    } else {
                        header("Location: ../view/formPaciente.php?id={$paciente->getId()}&erro=cpf_duplicado");
                    }
                }
                exit();
            }
            if ($_GET['action'] == 'excluirPaciente') {
                $pacienteModel = new Paciente();
                $pacienteModel->excluir($_GET['id']);
                header("Location: ../view/pacientes.php?delete=ok");
                exit();
            }
        }
    }
}
$PacienteCtrl = new PacienteController();
$PacienteCtrl->handleRequest();
?>
