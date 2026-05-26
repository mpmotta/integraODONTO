<?php
require_once 'core/Controller.php';
require_once 'models/Paciente.php';
require_once 'models/Consulta.php';

class ProntuarioController extends Controller {

    public function paciente($id_paciente) {
        $this->checkAcesso([1, 2]);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->buscar($id_paciente);

        if (!$paciente) {
            header('Location: ?url=pacientes/index');
            exit;
        }

        $consultaModel = new Consulta();
        $historico = $consultaModel->listarPorPaciente($id_paciente);

        $this->registrarLog("Acessou prontuario do paciente ID: " . $id_paciente, "pacientes");

        $this->view('prontuario/index', [
            'paciente' => $paciente,
            'historico' => $historico
        ]);
    }
}
?>
