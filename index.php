<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard/index';
$url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));

$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DashboardController';
$methodName = isset($url[1]) ? $url[1] : 'index';

$controllerFile = 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        call_user_func_array([$controller, $methodName], array_slice($url, 2));
    } else {
        echo "Erro: Metodo nao encontrado.";
    }
} else {
    echo "Erro: Controlador nao encontrado.";
}
?>
