<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title>IntegraODONTO - Sistema de Gerenciamento Odontológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="views/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <a class="navbar-brand" href="?url=dashboard/index">
                <i class="fas fa-tooth"></i> IntegraODONTO
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3" style="color: #040; font-weight: 500;">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario_login']); ?>
                </span>
                <button id="btn-logout" class="btn btn-sm btn-outline-danger">Sair</button>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
