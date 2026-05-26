<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        require_once 'views/' . $view . '.php';
    }

    public function checkAuth() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ?url=login/index');
            exit;
        }
    }

    public function checkAcesso($niveisPermitidos) {
        $this->checkAuth();
        if (!in_array($_SESSION['usuario_nivel'], $niveisPermitidos)) {
            header('Location: ?url=dashboard/index');
            exit;
        }
    }

    public function verifyCSRF() {
        $headers = apache_request_headers();
        $token = isset($headers['X-CSRF-TOKEN']) ? $headers['X-CSRF-TOKEN'] : (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '');
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Token CSRF invalido.']);
            exit;
        }
    }

    public function registrarLog($acao, $tabela) {
        require_once 'config/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $id_usuario = $_SESSION['usuario_id'];
        
        $query = "INSERT INTO logs_auditoria (id_usuario, acao_realizada, tabela_afetada, ip_origem) VALUES (:id_usuario, :acao, :tabela, :ip)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':acao', $acao);
        $stmt->bindParam(':tabela', $tabela);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    }
}
?>
