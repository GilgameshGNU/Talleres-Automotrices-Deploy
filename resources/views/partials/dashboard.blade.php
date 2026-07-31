<!-- WRAPPER PRINCIPAL DEL DASHBOARD -->
<div class="wrapper" id="dashboardWrapper" style="display: none;">
    <!-- TOP HEADER -->
    <header>
        <div class="header-title-box">
            <h1 id="viewTitle">Panel principal</h1>
            <p id="viewSubtitle">Sistema de Gestión Empresarial</p>
        </div>
        <div class="header-actions">
            <button class="icon-btn" onclick="checkAuth()"><i class="fa-solid fa-bell"></i></button>
            <div class="profile-avatar" style="width: 32px; height: 32px; background-color: var(--accent);" id="headerAvatar">?</div>
        </div>
    </header>

    <!-- CONTENT VIEW -->
    <div class="content">
        <!-- PANEL DASHBOARD (shown when view = panel) -->
        <div id="panelDashboard" style="display:none;">

            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: linear-gradient(135deg,#2563eb,#7c3aed);"><i class="fa-solid fa-users"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">Usuarios</span>
                        <span class="kpi-value" id="kpi-usuarios">—</span>
                    </div>
                    <div class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: linear-gradient(135deg,#0891b2,#06b6d4);"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">Roles</span>
                        <span class="kpi-value" id="kpi-roles">—</span>
                    </div>
                    <div class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: linear-gradient(135deg,#d97706,#f59e0b);"><i class="fa-solid fa-wrench"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">Servicios</span>
                        <span class="kpi-value" id="kpi-servicios">—</span>
                    </div>
                    <div class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon" style="background: linear-gradient(135deg,#059669,#10b981);"><i class="fa-solid fa-truck"></i></div>
                    <div class="kpi-info">
                        <span class="kpi-label">Proveedores</span>
                        <span class="kpi-value" id="kpi-proveedores">—</span>
                    </div>
                    <div class="kpi-trend up"><i class="fa-solid fa-arrow-trend-up"></i></div>
                </div>
            </div>

            <!-- Chart + Quick Actions -->
            <div class="dash-mid-row">
                <!-- Bar Chart -->
                <div class="dash-card chart-card">
                    <div class="dash-card-header">
                        <span class="dash-card-title"><i class="fa-solid fa-chart-bar"></i> Resumen del Sistema</span>
                    </div>
                    <div class="bar-chart-wrap" id="barChartWrap">
                        <!-- injected by JS -->
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <span class="dash-card-title"><i class="fa-solid fa-bolt"></i> Accesos rápidos</span>
                    </div>
                    <div class="quick-actions-grid">
                        <button class="qa-btn" onclick="switchView('usuarios')">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Nuevo usuario</span>
                        </button>
                        <button class="qa-btn" onclick="switchView('roles')">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Ver roles</span>
                        </button>
                        <button class="qa-btn" onclick="switchView('servicios')">
                            <i class="fa-solid fa-wrench"></i>
                            <span>Servicios</span>
                        </button>
                        <button class="qa-btn" onclick="switchView('proveedores')">
                            <i class="fa-solid fa-truck"></i>
                            <span>Proveedores</span>
                        </button>
                        <button class="qa-btn" onclick="switchView('permisos')">
                            <i class="fa-solid fa-key"></i>
                            <span>Permisos</span>
                        </button>
                        <button class="qa-btn" onclick="switchView('tipos-servicio')">
                            <i class="fa-solid fa-tags"></i>
                            <span>Categorías</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Users + Modules status -->
            <div class="dash-mid-row">
                <!-- Recent Users -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <span class="dash-card-title"><i class="fa-solid fa-users"></i> Usuarios recientes</span>
                        <button class="dash-card-link" onclick="switchView('usuarios')">Ver todos <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                    <div id="recentUsersWrap">
                        <p style="color:#94a3b8;font-size:0.85rem;padding:1rem 0;">Cargando...</p>
                    </div>
                </div>

                <!-- Modules Status -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <span class="dash-card-title"><i class="fa-solid fa-circle-nodes"></i> Estado de módulos</span>
                    </div>
                    <div class="modules-list">
                        <div class="module-row">
                            <div class="module-dot" style="background:#10b981;"></div>
                            <span class="module-name">Autenticación</span>
                            <span class="module-status ok">Activo</span>
                        </div>
                        <div class="module-row">
                            <div class="module-dot" style="background:#10b981;"></div>
                            <span class="module-name">Usuarios & Roles</span>
                            <span class="module-status ok">Activo</span>
                        </div>
                        <div class="module-row">
                            <div class="module-dot" style="background:#10b981;"></div>
                            <span class="module-name">Servicios</span>
                            <span class="module-status ok">Activo</span>
                        </div>
                        <div class="module-row">
                            <div class="module-dot" style="background:#10b981;"></div>
                            <span class="module-name">Proveedores</span>
                            <span class="module-status ok">Activo</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE VIEW (all other views) -->
        <div id="tableView" style="display:none;">
            <div class="main-card">
                <div class="card-header-bar">
                    <div class="card-title-group">
                        <h2 id="cardTitle">—</h2>
                        <p id="cardSubtitle">—</p>
                    </div>
                    <button class="btn-primary" id="cardActionBtn" onclick="handleNewAction()">
                        <i class="fa-solid fa-plus"></i>
                        <span id="cardActionText">Nuevo</span>
                    </button>
                </div>
                <div class="search-container" id="searchContainerBox">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="search-input" id="searchFieldInput" placeholder="Buscar..." oninput="handleSearch(this.value)">
                </div>
                <div class="table-responsive">
                    <table id="dataTable">
                        <thead id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
