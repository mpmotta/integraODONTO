<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <h1 class="h2" style="color: #040;">Seu dia hoje</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-odonto">
            <div class="card-header">Consultas do Dia</div>
            <div class="card-body">
                <p class="text-muted">Selecione o módulo de agenda para gerenciar os horários.</p>
            </div>
        </div>
    </div>
    
    <?php if($_SESSION['usuario_nivel'] == 1 || $_SESSION['usuario_nivel'] == 2): ?>
    <div class="col-md-4">
        <div class="card card-odonto">
            <div class="card-header">Controle Financeiro</div>
            <div class="card-body text-center">
                <i class="fas fa-chart-pie fa-3x mb-3" style="color: #040;"></i>
                <p>Monitore as finanças da sua clínica.</p>
                <a href="?url=financeiro/index" class="btn btn-primary w-100"><i class="fas fa-plus"></i> Adicionar lançamento</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'views/layout/footer.php'; ?>
