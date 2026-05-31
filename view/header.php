<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/clinicaModel.php';
$clinicaModel = new Clinica();
$dadosClinica = $clinicaModel->carregar();
$nomeClinica = (isset($dadosClinica['nome']) && !empty($dadosClinica['nome'])) ? ' - ' . $dadosClinica['nome'] : '';

$dias_semana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$dia_atual = $dias_semana[date('w')] . ' - ' . date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntegraODONTO<?php echo $nomeClinica; ?></title>
    <link rel="icon" type="image/png" href="../assets/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; color: #004400; }
        .card { background-color: #ffffff; border: 2px solid #004400; border-radius: 12px; }
        .card-header { background-color: #004400; color: #ffffff; border-bottom: 2px solid #004400; }
        .card-header h1, .card-header h2, .card-header h3, .card-header h4, .card-header h5 { color: #ffffff; margin: 0; }
        h1, h2, h3, h4, h5, h6, label, p, span, td, th { color: #004400; }
        .btn-primary { background-color: #004400; border-color: #004400; color: #ffffff; }
        .btn-primary:hover { background-color: #003300; border-color: #003300; color: #ffffff; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .sidebar { height: 100vh; width: 180px; position: fixed; top: 0; left: 0; background-color: #ffffff; border-right: 2px solid #004400; padding-top: 20px; }
        .sidebar a { padding: 12px 15px; text-decoration: none; font-size: 14px; color: #004400; display: block; }
        .sidebar a:hover { background-color: #004400; color: #ffffff; }
        .topbar { position: fixed; top: 0; left: 180px; right: 0; height: 60px; background-color: #ffffff; border-bottom: 2px solid #004400; display: flex; align-items: center; padding: 0 20px; z-index: 1000; }
        .main-content { margin-left: 180px; padding: 80px 20px 20px 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="px-3 pb-3 border-bottom mb-2 text-center">
            <h6 class="mb-0 fw-bold text-success"><i class="fas fa-tooth"></i> IntegraODONTO</h6>
        </div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Início</a>
        <a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a>
        <a href="pacientes.php"><i class="fas fa-users"></i> Pacientes</a>
        <a href="balanco.php"><i class="fas fa-wallet"></i> Financeiro</a>
        <?php if($_SESSION['usuario_nivel'] != 3): ?>
        <a href="relatorio_ir.php"><i class="fas fa-file-invoice-dollar"></i> Relatório IR</a>
        <?php endif; ?>
        <?php if($_SESSION['usuario_nivel'] == 1): ?>
        <a href="usuarios.php"><i class="fas fa-user-shield"></i> Usuários</a>
        <?php endif; ?>
        <a href="config_clinica.php"><i class="fas fa-cogs"></i> Configurações</a>
        <a href="../controller/usuarioController.php?action=logout" onclick="return confirm('Tem certeza que deseja sair do sistema?');"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>
    <div class="topbar">
        <span class="fw-bold text-secondary"><?php echo $dia_atual; ?></span>
        <span class="ms-auto small"><i class="fas fa-user-md"></i> Olá, <?php echo $_SESSION['usuario_nome']; ?></span>
    </div>
    <div class="main-content">
