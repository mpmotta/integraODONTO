<?php require_once 'views/layout/header.php'; ?>
<?php require_once 'views/layout/sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2" style="color: #040;"><?php echo isset($paciente) ? 'Editar Paciente' : 'Novo Paciente'; ?></h1>
    <a href="?url=pacientes/index" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="card card-odonto">
    <div class="card-body">
        <form action="?url=pacientes/salvar" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <?php if (isset($paciente)): ?>
                <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
            <?php endif; ?>

            <h5 class="text-success border-bottom pb-2 mb-3">Dados Pessoais</h5>
            <div class="row mb-3">
                <div class="col-md-2 text-center">
                    <?php if (isset($paciente) && $paciente['foto_path']): ?>
                        <img src="<?php echo $paciente['foto_path']; ?>" class="img-thumbnail rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-5x text-secondary mb-2"></i>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control form-control-sm" accept="image/jpeg, image/png">
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" name="nome" class="form-control" required value="<?php echo isset($paciente) ? htmlspecialchars($paciente['nome']) : ''; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data de Nascimento *</label>
                            <input type="date" name="data_nascimento" class="form-control" required value="<?php echo isset($paciente) ? $paciente['data_nascimento'] : ''; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo *</label>
                            <select name="sexo" class="form-select" required>
                                <option value="M" <?php echo (isset($paciente) && $paciente['sexo'] == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo (isset($paciente) && $paciente['sexo'] == 'F') ? 'selected' : ''; ?>>Feminino</option>
                                <option value="O" <?php echo (isset($paciente) && $paciente['sexo'] == 'O') ? 'selected' : ''; ?>>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CPF *</label>
                            <input type="text" name="cpf" class="form-control mask-cpf" required value="<?php echo isset($paciente) ? htmlspecialchars($paciente['cpf']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" name="rg" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['rg']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone / WhatsApp *</label>
                            <input type="text" name="telefone" class="form-control mask-telefone" required value="<?php echo isset($paciente) ? htmlspecialchars($paciente['telefone']) : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="text-success border-bottom pb-2 mb-3 mt-4">Endereço</h5>
            <div class="row mb-3">
                <div class="col-md-2 mb-3">
                    <label class="form-label">CEP</label>
                    <input type="text" name="cep" class="form-control mask-cep" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['cep']) : ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logradouro</label>
                    <input type="text" name="logradouro" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['logradouro']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Número</label>
                    <input type="text" name="numero" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['numero']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Complemento</label>
                    <input type="text" name="complemento" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['complemento']) : ''; ?>">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Bairro</label>
                    <input type="text" name="bairro" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['bairro']) : ''; ?>">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['cidade']) : ''; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">UF</label>
                    <input type="text" name="uf" class="form-control" maxlength="2" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['uf']) : ''; ?>">
                </div>
            </div>

            <h5 class="text-success border-bottom pb-2 mb-3 mt-4">Responsável Legal (Para Menores)</h5>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome do Responsável</label>
                    <input type="text" name="responsavel_nome" class="form-control" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['responsavel_nome']) : ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">CPF do Responsável</label>
                    <input type="text" name="responsavel_cpf" class="form-control mask-cpf" value="<?php echo isset($paciente) ? htmlspecialchars($paciente['responsavel_cpf']) : ''; ?>">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success px-5"><i class="fas fa-save"></i> Salvar Cadastro</button>
            </div>
        </form>
    </div>
</div>

<script src="views/js/pacientes.js"></script>

<?php require_once 'views/layout/footer.php'; ?>
