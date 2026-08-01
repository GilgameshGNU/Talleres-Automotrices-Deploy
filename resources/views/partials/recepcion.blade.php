{{-- recepcion.blade.php — Portal Recepcionista SisGest Pro --}}

<div id="recepcionPortal" class="op-portal" style="display: none;">

    {{-- ─── NAVBAR ─────────────────────────────────────────────────── --}}
    <nav class="op-navbar">
        <div class="op-navbar-brand">
            <div class="op-navbar-icon">
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div>
                <div class="op-navbar-title">SisGest Pro</div>
                <div class="op-navbar-sub">Portal de Recepción</div>
            </div>
        </div>

        <div class="op-nav-links">
            <button class="op-nav-btn active" id="rp-nav-dashboard" onclick="switchRecepTab('dashboard')">
                <i class="fa-solid fa-house"></i> <span>Dashboard</span>
            </button>
            <button class="op-nav-btn" id="rp-nav-clientes" onclick="switchRecepTab('clientes')">
                <i class="fa-solid fa-users"></i> <span>Clientes</span>
            </button>
            <button class="op-nav-btn" id="rp-nav-nueva-orden" onclick="switchRecepTab('nueva-orden')">
                <i class="fa-solid fa-file-signature"></i> <span>Nueva Orden</span>
            </button>
            <button class="op-nav-btn" id="rp-nav-citas" onclick="switchRecepTab('citas')">
                <i class="fa-regular fa-calendar-check"></i> <span>Citas</span>
            </button>
            <button class="op-nav-btn" id="rp-nav-servicios" onclick="switchRecepTab('servicios')">
                <i class="fa-solid fa-list-check"></i> <span>Servicios</span>
            </button>
            <button class="op-nav-btn" id="rp-nav-notif" onclick="switchRecepTab('notificaciones')" style="position:relative;">
                <i class="fa-regular fa-bell"></i> <span>Notif.</span>
                <span id="rp-notif-badge" style="
                    display:none; position:absolute; top:4px; right:4px;
                    background:var(--state-danger,#ef4444); color:#fff;
                    font-size:0.6rem; font-weight:800; border-radius:99px;
                    min-width:16px; height:16px; padding:0 4px;
                    display:flex; align-items:center; justify-content:center;
                ">0</span>
            </button>
        </div>

        <div class="op-nav-right">
            <div class="op-badge-dot" title="Conectado"></div>
            <div style="font-size:.82rem; color:var(--text-secondary,#9db8b0);">
                <strong id="rp-username" style="color:#fff;">Cargando...</strong><br>
                <span style="font-size:.72rem; color:var(--text-muted,#5f9c92);">Recepcionista</span>
            </div>
            <div id="rp-avatar" style="
                width:36px; height:36px; border-radius:50%;
                background:var(--gradient-primary,linear-gradient(100deg,#b6f24a,#22d3c5));
                color:#04100e; font-weight:900; font-size:1rem;
                display:flex; align-items:center; justify-content:center;
            ">R</div>
            <button class="op-btn-ghost" onclick="openLogoutModal()" style="padding:.4rem .8rem; font-size:.82rem;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </nav>

    {{-- ─── TAB: DASHBOARD ─────────────────────────────────────────── --}}
    <div id="rp-tab-dashboard" class="op-main rp-tab-content" style="display:block;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem;">
            <div>
                <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Panel de Recepción</h2>
                <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;" id="rp-date-label">Cargando fecha...</p>
            </div>
            <div style="display:flex; gap:.75rem;">
                <button class="op-btn-primary" onclick="switchRecepTab('nueva-orden')">
                    <i class="fa-solid fa-file-signature"></i> Nueva Orden
                </button>
                <button class="op-btn-ghost" onclick="abrirOffcanvasCliente()">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="op-stats-grid" style="margin-bottom:1.75rem;">
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(182,242,74,0.15); color:var(--accent-primary,#b6f24a);">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="kpi-rp-citas">—</div>
                    <div class="op-stat-lbl">Citas Hoy</div>
                </div>
            </div>
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(16,185,129,0.15); color:var(--state-success,#10b981);">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="kpi-rp-confirmadas">—</div>
                    <div class="op-stat-lbl">Confirmadas</div>
                </div>
            </div>
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(56,189,248,0.15); color:var(--state-info,#38bdf8);">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="kpi-rp-ordenes">—</div>
                    <div class="op-stat-lbl">Órdenes Activas</div>
                </div>
            </div>
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(245,158,11,0.15); color:var(--state-warning,#f59e0b);">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="kpi-rp-notif">—</div>
                    <div class="op-stat-lbl">No Leídas</div>
                </div>
            </div>
        </div>

        {{-- Citas de hoy --}}
        <div class="glass-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                <h3 style="color:#fff; font-size:1.05rem; font-weight:700; margin:0;">
                    <i class="fa-solid fa-calendar-day" style="color:var(--accent-primary,#b6f24a); margin-right:.5rem;"></i>
                    Citas de Hoy
                </h3>
                <button class="op-btn-ghost" onclick="loadRecepDashboard()" style="padding:.3rem .7rem; font-size:.8rem;">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Hora</th>
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Cliente</th>
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Vehículo</th>
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Motivo</th>
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Estado</th>
                            <th style="color:var(--text-muted,#5f9c92); padding:.6rem .75rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="rp-dashboard-citas-tbody">
                        <tr><td colspan="6" style="padding:2rem; text-align:center; color:var(--text-muted,#5f9c92);">Cargando citas...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─── TAB: CLIENTES ──────────────────────────────────────────── --}}
    <div id="rp-tab-clientes" class="op-main rp-tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem;">
            <div>
                <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Clientes</h2>
                <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;">Busca un cliente existente o registra uno nuevo</p>
            </div>
            <button class="op-btn-primary" onclick="abrirOffcanvasCliente()">
                <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
            </button>
        </div>

        {{-- Buscador prominente --}}
        <div class="glass-card" style="margin-bottom:1.5rem;">
            <div style="display:flex; gap:.75rem; align-items:center;">
                <div style="flex:1; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted,#5f9c92);"></i>
                    <input type="text" id="rp-search-input"
                        placeholder="Buscar por nombre, CI, teléfono o email..."
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                               color:#fff; border-radius:12px; padding:.85rem 1rem .85rem 2.75rem;
                               font-family:'Outfit',sans-serif; font-size:.95rem; outline:none;"
                        oninput="loadClientes(this.value)">
                </div>
                <button class="op-btn-primary" onclick="loadClientes(document.getElementById('rp-search-input').value)">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                <button class="op-btn-ghost" id="rp-btn-nuevo-cliente" onclick="abrirOffcanvasCliente()">
                    <i class="fa-solid fa-user-plus"></i> Registrar Nuevo
                </button>
            </div>
        </div>

        <div id="rp-search-results" style="display:flex; flex-direction:column; gap:1rem;">
            <div style="text-align:center; padding:3rem; color:var(--text-muted,#5f9c92);">
                <i class="fa-solid fa-magnifying-glass" style="font-size:2rem; margin-bottom:1rem; opacity:.4; display:block;"></i>
                Escribe para buscar un cliente
            </div>
        </div>
    </div>

    {{-- ─── TAB: NUEVA ORDEN ───────────────────────────────────────── --}}
    <div id="rp-tab-nueva-orden" class="op-main rp-tab-content" style="display:none;">
        <div style="margin-bottom:1.75rem;">
            <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Nueva Orden de Trabajo</h2>
            <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;">Completa los 3 pasos para abrir la orden</p>
        </div>

        {{-- Stepper --}}
        <div class="op-stepper" style="margin-bottom:2rem;">
            <div class="op-step active" id="rp-step-1">
                <div style="display:flex; flex-direction:column; align-items:center;">
                    <div class="op-step-bubble">1</div>
                    <div class="op-step-label">Cliente</div>
                </div>
                <div class="op-step-line"></div>
            </div>
            <div class="op-step" id="rp-step-2">
                <div style="display:flex; flex-direction:column; align-items:center;">
                    <div class="op-step-bubble">2</div>
                    <div class="op-step-label">Vehículo</div>
                </div>
                <div class="op-step-line"></div>
            </div>
            <div class="op-step" id="rp-step-3">
                <div style="display:flex; flex-direction:column; align-items:center;">
                    <div class="op-step-bubble">3</div>
                    <div class="op-step-label">Datos Orden</div>
                </div>
            </div>
        </div>

        {{-- Paso 1: Seleccionar Cliente --}}
        <div id="rp-paso-1" class="glass-card">
            <h3 style="color:#fff; font-weight:700; margin-bottom:1.25rem;">
                <span style="color:var(--accent-primary,#b6f24a);">Paso 1</span> — Selecciona el Cliente
            </h3>
            <div style="display:flex; gap:.75rem; margin-bottom:1rem;">
                <input type="text" id="rp-orden-search-cliente"
                    placeholder="Buscar por nombre, CI, teléfono o email..."
                    style="flex:1; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                           color:#fff; border-radius:10px; padding:.75rem 1rem;
                           font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;"
                    oninput="buscarClienteParaOrden(this.value)">
                <button class="op-btn-primary" onclick="buscarClienteParaOrden(document.getElementById('rp-orden-search-cliente').value)">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                <button class="op-btn-ghost" onclick="abrirOffcanvasCliente()" title="Registrar cliente nuevo">
                    <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
                </button>
            </div>
            <div id="rp-paso1-resultados" style="display:flex; flex-direction:column; gap:.75rem; max-height:320px; overflow-y:auto;"></div>
            <div id="rp-paso1-seleccionado" style="display:none; margin-top:1rem; padding:1rem; background:rgba(182,242,74,0.08); border:1px solid rgba(182,242,74,0.25); border-radius:10px;">
                <span style="color:var(--accent-primary,#b6f24a); font-weight:700;"><i class="fa-solid fa-circle-check"></i> Cliente seleccionado:</span>
                <span id="rp-paso1-nombre" style="color:#fff; margin-left:.5rem; font-weight:600;"></span>
                <button onclick="deseleccionarCliente()" style="float:right; background:none; border:none; color:var(--text-muted,#5f9c92); cursor:pointer; font-size:.8rem;">Cambiar</button>
            </div>
        </div>

        {{-- Paso 2: Seleccionar Vehículo --}}
        <div id="rp-paso-2" class="glass-card" style="display:none; margin-top:1.25rem;">
            <h3 style="color:#fff; font-weight:700; margin-bottom:1.25rem;">
                <span style="color:var(--accent-primary,#b6f24a);">Paso 2</span> — Selecciona el Vehículo
            </h3>
            <div id="rp-paso2-vehiculos" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; margin-bottom:1rem;"></div>
            <button class="op-btn-ghost" onclick="abrirModalNuevoVehiculo()">
                <i class="fa-solid fa-plus"></i> Registrar Vehículo Nuevo
            </button>
        </div>

        {{-- Paso 3: Datos de la Orden --}}
        <div id="rp-paso-3" class="glass-card" style="display:none; margin-top:1.25rem;">
            <h3 style="color:#fff; font-weight:700; margin-bottom:1.5rem;">
                <span style="color:var(--accent-primary,#b6f24a);">Paso 3</span> — Datos de la Orden
            </h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                <div style="grid-column:1/-1;">
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.5rem;">Servicio Solicitado *</label>
                    <textarea id="rp-orden-servicio" rows="3" placeholder="Describe el trabajo solicitado por el cliente..."
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                               color:#fff; border-radius:10px; padding:.75rem 1rem;
                               font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; resize:vertical;"></textarea>
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.5rem;">Hora de Inicio</label>
                    <input type="datetime-local" id="rp-orden-hora-inicio"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                               color:#fff; border-radius:10px; padding:.75rem 1rem;
                               font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.5rem;">Sucursal</label>
                    <input type="text" id="rp-orden-sucursal" placeholder="Ej: Sucursal Centro"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                               color:#fff; border-radius:10px; padding:.75rem 1rem;
                               font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.5rem;">Hora Estimada de Entrega</label>
                    <input type="datetime-local" id="rp-orden-hora-fin"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
                               color:#fff; border-radius:10px; padding:.75rem 1rem;
                               font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                </div>
                <div style="grid-column:1/-1;">
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.75rem;">Asignación del Mecánico</label>
                    <div style="display:flex; flex-direction:column; gap:.75rem;">
                        <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer; padding:.75rem 1rem; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.03);">
                            <input type="radio" name="rp-asignacion" id="rp-asign-especifico" value="especifico" onchange="toggleAsignacion()" style="accent-color:var(--accent-primary,#b6f24a);">
                            <span style="color:#fff; font-weight:600;">Asignar a mecánico específico</span>
                        </label>
                        <div id="rp-mecanico-select-wrap" style="display:none; padding:0 .5rem;">
                            <select id="rp-mecanicoSelect"
                                style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15);
                                       color:#fff; border-radius:10px; padding:.75rem 1rem;
                                       font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                                <option value="">Cargando mecánicos disponibles...</option>
                            </select>
                        </div>
                        <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer; padding:.75rem 1rem; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.03);">
                            <input type="radio" name="rp-asignacion" id="rp-asign-disponible" value="disponible" checked onchange="toggleAsignacion()" style="accent-color:var(--accent-primary,#b6f24a);">
                            <span style="color:#fff; font-weight:600;">Dejar disponible</span>
                            <span style="color:var(--text-muted,#5f9c92); font-size:.82rem;">(cualquier mecánico puede tomarla)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="margin-top:1.5rem; display:flex; gap:.75rem; justify-content:flex-end;">
                <button class="op-btn-ghost" onclick="reiniciarWizard()">
                    <i class="fa-solid fa-rotate-left"></i> Reiniciar
                </button>
                <button class="op-btn-primary" onclick="crearOrden()">
                    <i class="fa-solid fa-check"></i> Abrir Orden de Trabajo
                </button>
            </div>
        </div>
    </div>

    {{-- ─── TAB: CITAS ─────────────────────────────────────────────── --}}
    <div id="rp-tab-citas" class="op-main rp-tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem;">
            <div style="display:flex; align-items:center; gap:1rem;">
                <button class="op-btn-ghost" onclick="cambiarSemana(-1)" style="padding:.4rem .8rem;">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div>
                    <h2 style="font-size:1.3rem; font-weight:800; color:#fff; margin:0;" id="rp-semana-label">Semana actual</h2>
                </div>
                <button class="op-btn-ghost" onclick="cambiarSemana(1)" style="padding:.4rem .8rem;">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button class="op-btn-ghost" onclick="semanaOffset=0; loadCitasSemana()" style="padding:.4rem .8rem; font-size:.8rem;">Hoy</button>
            </div>
            <button class="op-btn-primary" onclick="abrirModalCita()">
                <i class="fa-solid fa-plus"></i> Nueva Cita
            </button>
        </div>

        {{-- Grid semanal --}}
        <div id="rp-semana-grid" style="display:grid; grid-template-columns:repeat(7,1fr); gap:.75rem; min-height:400px;"></div>
    </div>

    {{-- ─── TAB: SERVICIOS ─────────────────────────────────────────── --}}
    <div id="rp-tab-servicios" class="op-main rp-tab-content" style="display:none;">
        <div style="margin-bottom:1.75rem;">
            <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Catálogo de Servicios</h2>
            <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;">Vista de referencia — solo lectura</p>
        </div>
        <div id="rp-tipo-pills" style="display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.5rem;"></div>
        <div id="rp-servicios-grid" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem;"></div>
    </div>

    {{-- ─── TAB: NOTIFICACIONES ────────────────────────────────────── --}}
    <div id="rp-tab-notificaciones" class="op-main rp-tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem;">
            <div>
                <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Notificaciones</h2>
                <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;">Historial de alertas del sistema</p>
            </div>
            <button class="op-btn-ghost" onclick="abrirModalNotifManual()">
                <i class="fa-solid fa-paper-plane"></i> Enviar Notificación
            </button>
        </div>

        {{-- Chips filtro --}}
        <div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.5rem;">
            <button class="rp-chip-filtro active" data-filtro="todas" onclick="filtrarNotif(this)">Todas</button>
            <button class="rp-chip-filtro" data-filtro="cita" onclick="filtrarNotif(this)">Citas</button>
            <button class="rp-chip-filtro" data-filtro="listo" onclick="filtrarNotif(this)">Vehículos Listos</button>
            <button class="rp-chip-filtro" data-filtro="otro" onclick="filtrarNotif(this)">Otros</button>
        </div>

        <div class="glass-card">
            <div class="op-timeline" id="rp-notif-timeline">
                <div style="text-align:center; padding:2rem; color:var(--text-muted,#5f9c92);">
                    <i class="fa-regular fa-bell" style="font-size:2rem; display:block; margin-bottom:1rem; opacity:.4;"></i>
                    Cargando notificaciones...
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODALES FUERA DEL PORTAL
══════════════════════════════════════════════════════════════ --}}

{{-- OFFCANVAS: Nuevo Cliente --}}
<div id="rp-offcanvas-cliente" style="
    position:fixed; inset:0; z-index:1050; display:none;
">
    <div onclick="cerrarOffcanvasCliente()" style="position:absolute; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);"></div>
    <div style="
        position:absolute; right:0; top:0; bottom:0; width:420px; max-width:95vw;
        background:#071613; border-left:1px solid rgba(182,242,74,0.15);
        padding:2rem; overflow-y:auto; display:flex; flex-direction:column; gap:1.25rem;
        font-family:'Outfit',sans-serif;
    ">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="color:#fff; font-size:1.2rem; font-weight:800; margin:0;">
                <i class="fa-solid fa-user-plus" style="color:var(--accent-primary,#b6f24a); margin-right:.5rem;"></i>
                Nuevo Cliente
            </h3>
            <button onclick="cerrarOffcanvasCliente()" style="background:none; border:none; color:var(--text-muted,#5f9c92); cursor:pointer; font-size:1.2rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="rp-form-cliente" onsubmit="crearClienteConUsuario(event)">
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Nombre Completo *</label>
                    <input type="text" id="rp-cli-nombre" required placeholder="Ej: García López Juan"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">CI / NIT *</label>
                        <input type="text" id="rp-cli-ci" required placeholder="Ej: 12345678"
                            style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Teléfono *</label>
                        <input type="text" id="rp-cli-telefono" required placeholder="Ej: 70000000"
                            style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                    </div>
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Email (para portal del cliente) *</label>
                    <input type="email" id="rp-cli-email" required placeholder="cliente@email.com"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Dirección</label>
                    <input type="text" id="rp-cli-direccion" placeholder="Av. Principal 123..."
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Contraseña de Acceso (opcional)</label>
                    <input type="password" id="rp-cli-password" placeholder="Por defecto se usará su CI / NIT"
                        style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
                <button type="submit" class="op-btn-primary" style="margin-top:.5rem; justify-content:center;">
                    <i class="fa-solid fa-user-check"></i> Registrar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Credenciales (UNA SOLA VEZ) --}}
<div id="rp-modal-credenciales" style="
    position:fixed; inset:0; z-index:1100; display:none;
    align-items:center; justify-content:center;
    background:rgba(0,0,0,0.75); backdrop-filter:blur(6px);
">
    <div class="credencial-card" style="
        background:#071613; border:2px solid var(--accent-primary,#b6f24a);
        border-radius:20px; padding:2rem; max-width:440px; width:90%;
        font-family:'Outfit',sans-serif;
        box-shadow:0 0 40px rgba(182,242,74,0.2);
    ">
        <div style="text-align:center; margin-bottom:1.5rem;">
            <div style="width:60px; height:60px; border-radius:50%; background:rgba(182,242,74,0.15); color:var(--accent-primary,#b6f24a); font-size:1.6rem; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h3 style="color:#fff; font-size:1.2rem; font-weight:800; margin:0 0 .4rem;">Cliente Registrado</h3>
            <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:0;">Entrega estas credenciales al cliente. <strong style="color:var(--state-warning,#f59e0b);">No se volverán a mostrar.</strong></p>
        </div>
        <div style="background:rgba(0,0,0,0.4); border-radius:12px; padding:1.25rem; margin-bottom:1.25rem;">
            <div style="margin-bottom:1rem;">
                <div style="color:var(--text-muted,#5f9c92); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.35rem;">Usuario</div>
                <div id="rp-cred-usuario" style="color:var(--accent-primary,#b6f24a); font-family:monospace; font-size:1.2rem; font-weight:700; letter-spacing:.04em;"></div>
            </div>
            <div>
                <div style="color:var(--text-muted,#5f9c92); font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.35rem;">Contraseña Temporal</div>
                <div id="rp-cred-password" style="color:var(--accent-secondary,#22d3c5); font-family:monospace; font-size:1.2rem; font-weight:700; letter-spacing:.08em;"></div>
            </div>
        </div>
        <div style="display:flex; gap:.75rem;">
            <button class="op-btn-ghost" onclick="imprimirCredenciales()" style="flex:1; justify-content:center;">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
            <button class="op-btn-primary" onclick="cerrarModalCredenciales()" style="flex:1; justify-content:center;">
                <i class="fa-solid fa-check"></i> He anotado los datos
            </button>
        </div>
    </div>
</div>

{{-- MODAL: Crear/Editar Cita --}}
<div id="rp-modal-cita" style="
    position:fixed; inset:0; z-index:1050; display:none;
    align-items:center; justify-content:center;
    background:rgba(0,0,0,0.7); backdrop-filter:blur(6px);
">
    <div style="background:#071613; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:2rem; max-width:500px; width:90%; font-family:'Outfit',sans-serif;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="color:#fff; font-size:1.1rem; font-weight:800; margin:0;" id="rp-modal-cita-titulo">Nueva Cita</h3>
            <button onclick="cerrarModalCita()" style="background:none; border:none; color:var(--text-muted,#5f9c92); cursor:pointer; font-size:1.2rem;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <input type="hidden" id="rp-cita-id">
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Cliente *</label>
                <div style="display:flex; gap:.5rem;">
                    <input type="text" id="rp-cita-buscar-cliente" placeholder="Buscar cliente..." onkeyup="buscarClienteCita(this.value)"
                        style="flex:1; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                </div>
                <input type="hidden" id="rp-cita-cliente-id">
                <div id="rp-cita-cliente-resultados" style="max-height:160px; overflow-y:auto; margin-top:.5rem;"></div>
            </div>
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Vehículo *</label>
                <select id="rp-cita-vehiculo" required style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                    <option value="">— Selecciona un cliente primero —</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Fecha *</label>
                    <input type="date" id="rp-cita-fecha" style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
                <div>
                    <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Hora *</label>
                    <input type="time" id="rp-cita-hora" style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                </div>
            </div>
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Motivo</label>
                <input type="text" id="rp-cita-motivo" placeholder="Ej: Cambio de aceite, Revisión general..."
                    style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
            </div>
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Estado</label>
                <select id="rp-cita-estado" style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="Completada">Completada</option>
                </select>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end; margin-top:.5rem;">
                <button class="op-btn-ghost" onclick="cerrarModalCita()">Cancelar</button>
                <button class="op-btn-primary" onclick="guardarCita()">
                    <i class="fa-solid fa-check"></i> Guardar Cita
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Enviar Notificación Manual --}}
<div id="rp-modal-notif" style="
    position:fixed; inset:0; z-index:1050; display:none;
    align-items:center; justify-content:center;
    background:rgba(0,0,0,0.7); backdrop-filter:blur(6px);
">
    <div style="background:#071613; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:2rem; max-width:460px; width:90%; font-family:'Outfit',sans-serif;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="color:#fff; font-size:1.1rem; font-weight:800; margin:0;">Enviar Notificación</h3>
            <button onclick="cerrarModalNotif()" style="background:none; border:none; color:var(--text-muted,#5f9c92); cursor:pointer; font-size:1.2rem;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Destinatario (cliente)</label>
                <input type="text" id="rp-notif-destinatario" placeholder="Buscar cliente..."
                    style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; box-sizing:border-box;">
                <input type="hidden" id="rp-notif-usuario-id">
            </div>
            <div>
                <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Mensaje</label>
                <textarea id="rp-notif-mensaje" rows="3" placeholder="Escribe el mensaje para el cliente..."
                    style="width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; resize:vertical; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                <button class="op-btn-ghost" onclick="cerrarModalNotif()">Cancelar</button>
                <button class="op-btn-primary" onclick="enviarNotifManual()">
                    <i class="fa-solid fa-paper-plane"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Estilos extra del portal recepcionista --}}
<style>
.rp-tab-content { display: none; }
.rp-chip-filtro {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--text-secondary, #9db8b0);
    border-radius: 99px; padding: .35rem .9rem;
    font-family: 'Outfit', sans-serif; font-size: .82rem;
    font-weight: 600; cursor: pointer; transition: all .2s;
}
.rp-chip-filtro.active, .rp-chip-filtro:hover {
    background: rgba(182,242,74,0.15);
    border-color: rgba(182,242,74,0.4);
    color: var(--accent-primary, #b6f24a);
}
.rp-semana-col {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px; padding: .75rem; min-height: 200px;
}
.rp-semana-col-header {
    text-align: center; margin-bottom: .75rem;
    padding-bottom: .5rem; border-bottom: 1px solid rgba(255,255,255,0.08);
}
.rp-semana-col-header .dia { font-size: .72rem; text-transform: uppercase; color: var(--text-muted, #5f9c92); font-weight: 700; letter-spacing: .06em; }
.rp-semana-col-header .num { font-size: 1.4rem; font-weight: 900; color: #fff; }
.rp-semana-col-header.hoy .num { color: var(--accent-primary, #b6f24a); }
.rp-cita-bloque {
    border-radius: 8px; padding: .4rem .6rem; margin-bottom: .4rem;
    cursor: pointer; font-size: .78rem; font-weight: 600;
    border-left: 3px solid; transition: opacity .2s;
}
.rp-cita-bloque:hover { opacity: .85; }
.rp-cliente-card {
    padding: 1rem 1.25rem;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px; display: flex;
    align-items: center; justify-content: space-between;
    transition: border-color .2s;
}
.rp-cliente-card:hover { border-color: rgba(182,242,74,0.2); }
.rp-vehiculo-card {
    padding: 1rem; background: rgba(255,255,255,0.04);
    border: 2px solid rgba(255,255,255,0.08);
    border-radius: 12px; cursor: pointer; transition: all .2s; text-align: center;
}
.rp-vehiculo-card:hover { border-color: rgba(182,242,74,0.35); background: rgba(182,242,74,0.06); }
.rp-vehiculo-card.seleccionado { border-color: var(--accent-primary, #b6f24a); background: rgba(182,242,74,0.1); }
</style>
