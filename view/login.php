<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntegraODONTO - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; color: #004400; }
        .card { background-color: #ffffff; border: 2px solid #004400; border-radius: 12px; }
        .card-header { background-color: #004400; color: #ffffff; border-bottom: 2px solid #004400; }
        .btn-primary { background-color: #004400; border-color: #004400; color: #ffffff; }
        .btn-primary:hover { background-color: #003300; border-color: #003300; color: #ffffff; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4 class="text-white"><i class="fas fa-tooth"></i> IntegraODONTO</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['erro'])) echo "<div class='alert alert-danger'>Login ou senha inválidos.</div>"; ?>
                    <form action="../controller/usuarioController.php?action=logar" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
