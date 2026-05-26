<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntegraODONTO - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="views/css/style.css" rel="stylesheet">
</head>
<body class="login-body">

    <div class="card card-odonto login-card">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h4 style="color: #040;"><i class="fas fa-tooth"></i> IntegraODONTO</h4>
            </div>
            
            <?php if(isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form action="?url=login/autenticar" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="login" class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Entrar</button>
            </form>
        </div>
    </div>

</body>
</html>
