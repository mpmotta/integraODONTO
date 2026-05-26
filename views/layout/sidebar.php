            <nav class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="?url=dashboard/index">
                                <i class="fas fa-home"></i> Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?url=agenda/index">
                                <i class="fas fa-calendar-alt"></i> Agenda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?url=pacientes/index">
                                <i class="fas fa-users"></i> Pacientes
                            </a>
                        </li>
                        
                        <?php if($_SESSION['usuario_nivel'] == 1 || $_SESSION['usuario_nivel'] == 2): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?url=financeiro/index">
                                <i class="fas fa-dollar-sign"></i> Gestão Financeira
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?url=relatorios/index">
                                <i class="fas fa-chart-bar"></i> Relatórios
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if($_SESSION['usuario_nivel'] == 1): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?url=usuarios/index">
                                <i class="fas fa-cog"></i> Configurações
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            <main class="col-md-10 ms-sm-auto px-md-4 pt-4">
