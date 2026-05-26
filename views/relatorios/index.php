<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;">Relatório Ano-Fiscal</h1>
</div>

<div class="card card-odonto mb-4">
    <div class="card-body">
        <form id="formFiltroAno" class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Selecione o Ano Fiscal</label>
                <select id="filtro_ano" class="form-select">
                    <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                    <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                    <option value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" id="btn-processar-relatorio" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt"></i> Processar Dados
                </button>
            </div>
            <div class="col-md-6 text-end d-none" id="botoes-exportacao">
                <button type="button" id="btn-exportar-pdf" class="btn btn-danger me-2">
                    <i class="fas fa-file-pdf"></i> Exportar para PDF
                </button>
                <button type="button" id="btn-exportar-xls" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar para XLS
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card card-odonto d-none" id="card-resultados">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabela-relatorio">
                <thead class="table-light">
                    <tr>
                        <th>Data do Recebimento</th>
                        <th>Nome do Paciente</th>
                        <th>CPF do Declarante</th>
                        <th>Descrição do Serviço</th>
                        <th>Valor Recebido (R$)</th>
                    </tr>
                </thead>
                <tbody id="corpo-tabela-relatorio">
                </tbody>
                <tfoot>
                    <tr class="table-active fw-bold">
                        <td colspan="4" class="text-end">Valor Total Acumulado:</td>
                        <td id="valor-total-acumulado">R$ 0,00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="views/js/relatorios.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
