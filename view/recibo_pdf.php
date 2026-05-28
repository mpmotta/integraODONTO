<?php 
require_once '../controller/financeiroController.php';
if (!isset($_GET['id'])) { exit; }
$controller = new FinanceiroController();
$dados = $controller->gerarRecibo($_GET['id']);

if(!$dados) {
    echo "<h2 style='color:red;'>Erro: Os dados da clinica nao foram configurados no sistema. Acesse as configuracoes e preencha o nome e documento da clinica antes de emitir recibos.</h2>";
    exit;
}

$recibo = $dados['recibo'];
$clinica = $dados['clinica'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
</head>
<body onload="window.print()">
<div style="border: 2px solid #000; padding: 40px; width: 700px; margin: 0 auto; font-family: Arial, sans-serif;">
    <h2 style="text-align: center;">RECIBO ODONTOLÓGICO</h2>
    <hr>
    <p><strong><?php echo $clinica['nome']; ?></strong></p>
    <p><?php echo $clinica['tipo_documento']; ?>: <?php echo $clinica['documento']; ?></p>
    <p>Endereço: <?php echo $clinica['endereco']; ?></p>
    <p>Telefone: <?php echo $clinica['telefone']; ?></p>
    <hr>
    <p style="font-size: 16px; line-height: 1.6;">
        Recebi(emos) de <strong><?php echo $recibo['paciente_nome']; ?></strong>, portador(a) do CPF/CNPJ nº <strong><?php echo $recibo['paciente_cpf']; ?></strong>,
        a importância de <strong>R$ <?php echo number_format($recibo['valor'], 2, ',', '.'); ?></strong>, referente aos serviços odontológicos prestados no tratamento de <strong><?php echo $recibo['nome_tratamento']; ?></strong>.
    </p>
    <br><br>
    <p style="text-align: right;">Data: <?php echo date('d/m/Y', strtotime($recibo['data_pagamento'])); ?></p>
    <br><br><br><br>
    <div style="text-align: center; border-top: 1px solid #000; width: 300px; margin: 0 auto; padding-top: 10px;">
        Assinatura do Profissional / Clínica
    </div>
</div>
</body>
</html>
