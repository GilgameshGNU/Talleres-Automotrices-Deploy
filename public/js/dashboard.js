const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

// FASE 6.F: apiFetch genérico
async function apiFetch(url, options = {}) {
    options.headers = { ...headers, ...options.headers };
    options.credentials = options.credentials ?? 'same-origin';
    const res = await fetch(url, options);
    if (res.status === 401) {
        window.location.href = '/login';
    } else if (res.status === 403) {
        if (typeof triggerToast === 'function') triggerToast('⚠ Acceso denegado (403)');
    } else if (res.status === 429) {
        if (typeof triggerToast === 'function') triggerToast('⚠ Demasiadas peticiones. Espera un momento (429)');
    }
    return res;
}

function normalizeRoleName(rolNombre) {
    if (!rolNombre) return '';
    return String(rolNombre)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}

// FASE 4.B: activarPortalPorRol
function activarPortalPorRol(rolNombre) {
    const adminPortal = document.getElementById('dashboardWrapper');
    const clientPortal = document.getElementById('clientPortal');
    const recepPortal = document.getElementById('recepcionPortal');
    const mecPortal = document.getElementById('mecanicoPortal');
    const sidebar = document.getElementById('dashboardSidebar');
    const roleKey = normalizeRoleName(rolNombre);

    [adminPortal, clientPortal, recepPortal, mecPortal].forEach(el => {
        if (el) el.style.display = 'none';
    });
    if (sidebar) sidebar.style.display = 'none';

    if (roleKey === 'cliente') {
        if (clientPortal) clientPortal.style.display = 'flex';
        if (typeof initClientPortal === 'function') initClientPortal();
    } else if (roleKey === 'recepcionista') {
        if (recepPortal) recepPortal.style.display = 'block';
        if (typeof initRecepcionPortal === 'function') initRecepcionPortal();
    } else if (roleKey === 'mecanico') {
        if (mecPortal) mecPortal.style.display = 'block';
        if (typeof initMecanicoPortal === 'function') initMecanicoPortal();
    } else {
        if (adminPortal) adminPortal.style.display = 'flex';
        if (sidebar) sidebar.style.display = 'flex';

        const isAdmin = roleKey === 'administrador';
        document.querySelectorAll('.admin-only').forEach(item => {
            item.style.display = isAdmin ? '' : 'none';
        });
    }
}

let currentView = 'panel';
let currentData = [];
let allRoles = [];
let allTiposServicio = [];
let allClientes = [];
let allVehiculos = [];
let allMecanicos = [];
let allModelos = [];
let allSucursales = [];
let allEspecialidades = [];
let allProveedores = [];
let allRepuestos = [];
let allInventarios = [];
let editingId = null;

// View Configurations
const viewConfigs = {
    'ordenes-trabajo': {
        title: "Órdenes de Trabajo", subtitle: "Operaciones", cardTitle: "Gestión de Órdenes", cardSubtitle: "Órdenes",
        actionText: "Nueva orden", actionFn: () => openAddModal('ordenes-trabajo'), search: false,
        cols: ["ID", "ID Cliente", "Estado", "Total", "Acciones"],
        loadData: async () => { const r = await fetch('/api/ordenes-trabajo', { headers }); return r.ok ? await r.json() : []; }
    },
    'diagnosticos': {
        title: "Diagnósticos", subtitle: "Operaciones", cardTitle: "Gestión de Diagnósticos", cardSubtitle: "diagnósticos",
        actionText: "Nuevo", actionFn: () => openAddModal('diagnosticos'), search: false,
        cols: ["ID", "ID Orden", "Descripción", "Acciones"],
        loadData: async () => { const r = await fetch('/api/diagnosticos', { headers }); return r.ok ? await r.json() : []; }
    },
    'detalles-orden': {
        title: "Detalles", subtitle: "Operaciones", cardTitle: "Gestión de Detalles", cardSubtitle: "detalles",
        actionText: "Nuevo", actionFn: () => openAddModal('detalles-orden'), search: false,
        cols: ["ID", "ID Orden", "Subtotal", "Acciones"],
        loadData: async () => { const r = await fetch('/api/detalles-orden', { headers }); return r.ok ? await r.json() : []; }
    },
    'notificaciones': {
        title: "Notificaciones", subtitle: "Operaciones", cardTitle: "Gestión de Notificaciones", cardSubtitle: "notificaciones",
        actionText: "Nueva", actionFn: () => openAddModal('notificaciones'), search: false,
        cols: ["ID", "Mensaje", "Fecha", "Acciones"],
        loadData: async () => { const r = await fetch('/api/notificaciones', { headers }); return r.ok ? await r.json() : []; }
    },
    'pagos': {
        title: "Pagos", subtitle: "Operaciones", cardTitle: "Gestión de Pagos", cardSubtitle: "pagos",
        actionText: "Nuevo", actionFn: () => openAddModal('pagos'), search: false,
        cols: ["ID", "Monto", "Estado", "Acciones"],
        loadData: async () => { const r = await fetch('/api/pagos', { headers }); return r.ok ? await r.json() : []; }
    },
    'facturas': {
        title: "Facturas", subtitle: "Operaciones", cardTitle: "Gestión de Facturas", cardSubtitle: "facturas",
        actionText: "Nueva", actionFn: () => openAddModal('facturas'), search: false,
        cols: ["ID", "Nro Factura", "Total", "Acciones"],
        loadData: async () => { const r = await fetch('/api/facturas', { headers }); return r.ok ? await r.json() : []; }
    },
    'reportes': {
        title: "Reportes", subtitle: "Operaciones", cardTitle: "Gestión de Reportes", cardSubtitle: "reportes",
        actionText: "Nuevo", actionFn: () => openAddModal('reportes'), search: false,
        cols: ["ID", "Tipo", "Fecha", "Acciones"],
        loadData: async () => { const r = await fetch('/api/reportes', { headers }); return r.ok ? await r.json() : []; }
    },

    panel: {
        title: "Panel principal",
        subtitle: "Sistema de Gestión Empresarial",
        search: false,
        cols: [],
        loadData: async () => []
    },
    usuarios: {
        title: "Usuarios",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Usuarios",
        cardSubtitle: "usuarios registrados",
        actionText: "Nuevo usuario",
        actionFn: () => openAddModal('usuarios'),
        search: true,
        cols: ["#", "Nombre completo", "Correo electrónico", "Rol", "Estado", "Creado", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/usuarios', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    roles: {
        title: "Roles",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Roles",
        cardSubtitle: "roles configurados",
        actionText: "Nuevo rol",
        actionFn: () => openAddModal('roles'),
        search: true,
        cols: ["#", "NOMBRE DEL ROL", "DESCRIPCIÓN", "PERMISOS", "USUARIOS", "ACCIONES"],
        loadData: async () => {
            const res = await fetch('/api/roles', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    permisos: {
        title: "Permisos",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Permisos",
        cardSubtitle: "permisos registrados",
        actionText: "Nuevo permiso",
        actionFn: () => openAddModal('permisos'),
        search: true,
        cols: ["#", "Permiso", "Módulo", "ID Rol Relacionado", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/permisos', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    'tipos-servicio': {
        title: "Tipos de Servicio",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Categorias de Servicio",
        cardSubtitle: "categorias para catalogo",
        actionText: "Nuevo tipo",
        actionFn: () => openAddModal('tipos-servicio'),
        search: true,
        cols: ["#", "Nombre Categoría", "Descripción", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/tipos-servicio', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    servicios: {
        title: "Servicios",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Servicios",
        cardSubtitle: "servicios del catálogo",
        actionText: "Nuevo servicio",
        actionFn: () => openAddModal('servicios'),
        search: true,
        cols: ["#", "Nombre Servicio", "Precio Base", "Duración Estimada", "Categoría", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/servicios', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    proveedores: {
        title: "Proveedores",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Proveedores",
        cardSubtitle: "proveedores registrados",
        actionText: "Nuevo proveedor",
        actionFn: () => openAddModal('proveedores'),
        search: true,
        cols: ["#", "Razón Social", "NIT", "Teléfono", "Email", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/proveedores', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    clientes: {
        title: "Clientes",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Clientes",
        cardSubtitle: "clientes registrados",
        actionText: "Nuevo cliente",
        actionFn: () => openAddModal('clientes'),
        search: true,
        cols: ["#", "Nombre completo", "CI/NIT", "Teléfono", "Dirección", "Vehículos", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/clientes', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    vehiculos: {
        title: "Vehículos",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Vehículos",
        cardSubtitle: "vehículos registrados",
        actionText: "Nuevo vehículo",
        actionFn: () => openAddModal('vehiculos'),
        search: true,
        cols: ["#", "Placa", "Cliente", "Marca / Modelo", "Año", "Color", "Kilometraje", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/vehiculos', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    citas: {
        title: "Citas",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Citas",
        cardSubtitle: "citas agendadas",
        actionText: "Nueva cita",
        actionFn: () => openAddModal('citas'),
        search: true,
        cols: ["#", "Fecha", "Hora", "Cliente", "Vehículo", "Mecánico", "Estado", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/citas', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    mecanicos: {
        title: "Mecánicos",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Mecánicos",
        cardSubtitle: "Mecánicos registrados",
        actionText: "Nuevo mecánico",
        actionFn: () => openAddModal('mecanicos'),
        search: true,
        cols: ["#", "Nombre completo", "CI", "Teléfono", "Sucursal", "Especialidades", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/mecanicos', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    repuestos: {
        title: "Repuestos",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Gestión de Repuestos",
        cardSubtitle: "repuestos en catálogo",
        actionText: "Nuevo repuesto",
        actionFn: () => openAddModal('repuestos'),
        search: true,
        cols: ["#", "Código", "Nombre", "Marca", "Precio Venta", "Proveedor", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/repuestos', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    inventario: {
        title: "Inventario",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Control de Inventario",
        cardSubtitle: "registros de inventario",
        actionText: "Nuevo registro",
        actionFn: () => openAddModal('inventario'),
        search: true,
        cols: ["#", "Repuesto", "Sucursal", "Stock Actual", "Stock Mínimo", "Ubicación", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/inventario', { headers });
            return res.ok ? await res.json() : [];
        }
    },
    'movimientos-inventario': {
        title: "Movimientos de Inventario",
        subtitle: "Sistema de Gestión Empresarial",
        cardTitle: "Movimientos de Inventario",
        cardSubtitle: "movimientos registrados",
        actionText: "Nuevo movimiento",
        actionFn: () => openAddModal('movimientos-inventario'),
        search: true,
        cols: ["#", "Fecha", "Repuesto", "Sucursal", "Tipo", "Cantidad", "Motivo", "Acciones"],
        loadData: async () => {
            const res = await fetch('/api/movimientos-inventario', { headers });
            return res.ok ? await res.json() : [];
        }
    }
};

// Preload all catalog data needed for the current view's add/edit modal
async function preloadCatalogos(viewName) {
    const fetches = [];

    // Always preload roles and tipos-servicio (used by usuarios, permisos, servicios)
    if (!allRoles.length || ['usuarios','permisos','roles'].includes(viewName)) {
        fetches.push(fetch('/api/roles', { headers }).then(r => r.ok ? r.json() : []).then(d => { allRoles = d; }));
    }
    if (!allTiposServicio.length || viewName === 'servicios') {
        fetches.push(fetch('/api/tipos-servicio', { headers }).then(r => r.ok ? r.json() : []).then(d => { allTiposServicio = d; }));
    }

    if (['vehiculos','citas'].includes(viewName)) {
        fetches.push(fetch('/api/clientes', { headers }).then(r => r.ok ? r.json() : []).then(d => { allClientes = d; }));
    }
    if (viewName === 'vehiculos') {
        fetches.push(fetch('/api/modelos-vehiculo', { headers }).then(r => r.ok ? r.json() : []).then(d => { allModelos = d; }));
    }
    if (viewName === 'citas') {
        fetches.push(fetch('/api/vehiculos', { headers }).then(r => r.ok ? r.json() : []).then(d => { allVehiculos = d; }));
        fetches.push(fetch('/api/mecanicos', { headers }).then(r => r.ok ? r.json() : []).then(d => { allMecanicos = d; }));
    }
    if (['mecanicos','inventario'].includes(viewName)) {
        fetches.push(fetch('/api/sucursales', { headers }).then(r => r.ok ? r.json() : []).then(d => { allSucursales = d; }));
    }
    if (viewName === 'mecanicos') {
        fetches.push(fetch('/api/especialidades', { headers }).then(r => r.ok ? r.json() : []).then(d => { allEspecialidades = d; }));
    }
    if (viewName === 'repuestos') {
        fetches.push(fetch('/api/proveedores', { headers }).then(r => r.ok ? r.json() : []).then(d => { allProveedores = d; }));
    }
    if (viewName === 'inventario') {
        fetches.push(fetch('/api/repuestos', { headers }).then(r => r.ok ? r.json() : []).then(d => { allRepuestos = d; }));
    }
    if (viewName === 'movimientos-inventario') {
        fetches.push(fetch('/api/inventario', { headers }).then(r => r.ok ? r.json() : []).then(d => { allInventarios = d; }));
    }
    if (['diagnosticos','detalles-orden','facturas'].includes(viewName)) {
        fetches.push(fetch('/api/ordenes-trabajo', { headers }).then(r => r.ok ? r.json() : []).then(d => { window.allOrdenes = d; }));
    }
    if (viewName === 'detalles-orden') {
        fetches.push(fetch('/api/servicios', { headers }).then(r => r.ok ? r.json() : []).then(d => { window.allServicios = d; }));
        fetches.push(fetch('/api/repuestos', { headers }).then(r => r.ok ? r.json() : []).then(d => { window.allRepuestos = d; }));
    }
    if (viewName === 'notificaciones') {
        fetches.push(fetch('/api/usuarios', { headers }).then(r => r.ok ? r.json() : []).then(d => { window.allUsuarios = d; }));
    }
    if (viewName === 'pagos') {
        fetches.push(fetch('/api/facturas', { headers }).then(r => r.ok ? r.json() : []).then(d => { window.allFacturas = d; }));
    }

    // Run all fetches in parallel
    try { await Promise.all(fetches); } catch(e) { console.error('Error precargando catálogos:', e); }
}

// Switch View controller
async function switchView(viewName, pushState = true) {
    currentView = viewName;
    editingId = null;

    if (pushState) {
        const urlPath = viewName === 'panel' ? '/' : '/' + viewName;
        window.history.pushState({ view: viewName }, '', urlPath);
    }

    document.querySelectorAll('.menu-item').forEach(el => el.classList.remove('active'));
    const activeNav = document.getElementById(`nav-${viewName}`);
    if (activeNav) activeNav.classList.add('active');

    const conf = viewConfigs[viewName];
    if (!conf) return; // Prevent errors on invalid routes

    document.getElementById('viewTitle').textContent = conf.title;
    document.getElementById('viewSubtitle').textContent = conf.subtitle;

    const panelEl = document.getElementById('panelDashboard');
    const tableEl = document.getElementById('tableView');

    if (viewName === 'panel') {
        panelEl.style.display = 'block';
        tableEl.style.display = 'none';
        await loadPanelDashboard();
    } else {
        panelEl.style.display = 'none';
        tableEl.style.display = 'block';
        document.getElementById('cardTitle').textContent = conf.cardTitle;
        document.getElementById('cardSubtitle').textContent = conf.cardSubtitle;
        document.getElementById('cardActionText').textContent = conf.actionText;
        document.getElementById('searchContainerBox').style.display = conf.search ? 'block' : 'none';
        const head = document.getElementById('tableHead');
        head.innerHTML = '<tr>' + conf.cols.map(c => `<th>${c}</th>`).join('') + '</tr>';
        // Load table data AND catalog data in parallel
        await Promise.all([ refreshData(), preloadCatalogos(viewName) ]);
    }
}

// Handle Browser Back/Forward buttons
window.addEventListener('popstate', (event) => {
    if (event.state && event.state.view) {
        switchView(event.state.view, false);
    } else {
        switchView('panel', false);
    }
});


// Load all dashboard KPI data
async function loadPanelDashboard() {
    try {
        const [usuRes, rolRes, servRes, provRes] = await Promise.all([
            fetch('/api/usuarios', { headers }),
            fetch('/api/roles', { headers }),
            fetch('/api/servicios', { headers }),
            fetch('/api/proveedores', { headers })
        ]);
        const usuarios = usuRes.ok ? await usuRes.json() : [];
        const roles = rolRes.ok ? await rolRes.json() : [];
        const servicios = servRes.ok ? await servRes.json() : [];
        const proveedores = provRes.ok ? await provRes.json() : [];

        // KPI counters
        document.getElementById('kpi-usuarios').textContent = usuarios.length;
        document.getElementById('kpi-roles').textContent = roles.length;
        document.getElementById('kpi-servicios').textContent = servicios.length;
        document.getElementById('kpi-proveedores').textContent = proveedores.length;

        // Bar chart
        const bars = [
            { label: 'Usuarios', val: usuarios.length, color: 'linear-gradient(90deg,#6366f1,#818cf8)' },
            { label: 'Roles', val: roles.length, color: 'linear-gradient(90deg,#06b6d4,#22d3ee)' },
            { label: 'Servicios', val: servicios.length, color: 'linear-gradient(90deg,#f59e0b,#fcd34d)' },
            { label: 'Proveedores', val: proveedores.length, color: 'linear-gradient(90deg,#10b981,#34d399)' }
        ];
        const maxVal = Math.max(...bars.map(b => b.val), 1);
        const chartWrap = document.getElementById('barChartWrap');
        chartWrap.innerHTML = bars.map(b => {
            const pct = Math.round((b.val / maxVal) * 100);
            return `
                        <div class="bar-item">
                            <span class="bar-label">${b.label}</span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:${pct}%;background:${b.color};" data-pct="${pct}"></div>
                            </div>
                            <span class="bar-count">${b.val}</span>
                        </div>`;
        }).join('');
        // Animate bars
        setTimeout(() => {
            document.querySelectorAll('.bar-fill').forEach(el => {
                el.style.width = el.dataset.pct + '%';
            });
        }, 50);

        // Recent users
        const recentWrap = document.getElementById('recentUsersWrap');
        if (usuarios.length === 0) {
            recentWrap.innerHTML = `<p style="color:#94a3b8;font-size:0.85rem;padding:1rem 0;">Sin usuarios registrados.</p>`;
        } else {
            recentWrap.innerHTML = usuarios.slice(0, 5).map(u => {
                const initials = (u.nombreUsuario || 'U').substring(0, 2).toUpperCase();
                const rol = u.rol ? u.rol.nombre : 'Sin rol';
                return `
                        <div class="recent-user-row">
                            <div class="recent-user-avatar">${initials}</div>
                            <div class="recent-user-info">
                                <span class="recent-user-name">${u.nombreUsuario}</span>
                                <span class="recent-user-email">${u.email}</span>
                            </div>
                            <span class="recent-user-role">${rol}</span>
                        </div>`;
            }).join('');
        }
    } catch (e) {
        console.error('Error cargando dashboard:', e);
    }
}

// Refresh database info and populate table
async function refreshData() {
    const conf = viewConfigs[currentView];
    try {
        currentData = await conf.loadData();

        // Update subtitle counts if applicable
        if (currentView === 'usuarios') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} usuarios registrados`;
        } else if (currentView === 'roles') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} roles configurados`;
        } else if (currentView === 'permisos') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} permisos registrados`;
        } else if (currentView === 'tipos-servicio') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} categorías para catálogo`;
        } else if (currentView === 'servicios') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} servicios del catálogo`;
        } else if (currentView === 'proveedores') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} proveedores registrados`;
        } else if (currentView === 'clientes') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} clientes registrados`;
        } else if (currentView === 'vehiculos') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} vehículos registrados`;
        } else if (currentView === 'citas') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} citas agendadas`;
        } else if (currentView === 'mecanicos') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} mecánicos registrados`;
        } else if (currentView === 'repuestos') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} repuestos en catálogo`;
        } else if (currentView === 'inventario') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} registros de inventario`;
        } else if (currentView === 'movimientos-inventario') {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} movimientos registrados`;
        } else if (['ordenes-trabajo', 'diagnosticos', 'detalles-orden', 'notificaciones', 'pagos', 'facturas', 'reportes'].includes(currentView)) {
            document.getElementById('cardSubtitle').textContent = `${currentData.length} registros`;
        }

        renderTable(currentData);
    } catch (err) {
        console.error("Error cargando datos:", err);
    }
}

// Render data to table
function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; color: var(--text-subtitle); padding: 2rem;">No hay registros encontrados.</td></tr>`;
        return;
    }

    data.forEach((item, index) => {
        let rowHtml = '';

        if (currentView === 'usuarios') {
            const avatarText = item.nombreUsuario ? item.nombreUsuario.substring(0, 2).toUpperCase() : 'US';
            const statusClass = item.estado === 1 ? 'activo' : 'inactivo';
            const statusLabel = item.estado === 1 ? 'Activo' : 'Inactivo';
            const createdDate = item.created_at ? new Date(item.created_at).toLocaleDateString('es-ES', { timeZone: 'UTC', year: 'numeric', month: 'short', day: 'numeric' }) : 'No disponible';
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="avatar-cell">
                                <div class="cell-avatar">${avatarText}</div>
                                <span>${item.nombreUsuario}</span>
                            </td>
                            <td>${item.email}</td>
                            <td><span class="role-badge">${item.rol ? item.rol.nombre : 'Sin Rol'}</span></td>
                            <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                            <td style="color:#64748b; font-size:0.85rem;">${createdDate}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idUsuario})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idUsuario})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'roles') {
            const countPermisos = item.permisos_count || 0;
            const countUsuarios = item.usuarios_count || 0;
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombre}</strong></td>
                            <td style="color: #64748b;">${item.descripcion || '-'}</td>
                            <td><span style="background: #e0e7ff; color: #4338ca; padding: 0.35rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600;">${countPermisos} permisos</span></td>
                            <td><span style="background: #f1f5f9; color: #475569; padding: 0.35rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600;">${countUsuarios} usuarios</span></td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idRol})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idRol})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'permisos') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombre}</strong></td>
                            <td><span class="role-badge" style="background-color: #f1f5f9; color: #475569;">${item.modulo}</span></td>
                            <td>ID: ${item.idRol}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idPermiso})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idPermiso})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'tipos-servicio') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombre}</strong></td>
                            <td>${item.descripcion || '-'}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idTipoServicio})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idTipoServicio})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'servicios') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombre}</strong></td>
                            <td>Bs. ${item.precioBase}</td>
                            <td>${item.duracionEstimada ? item.duracionEstimada + ' min' : '-'}</td>
                            <td><span class="role-badge">${item.tipo_servicio ? item.tipo_servicio.nombre : 'ID: ' + item.idTipoServicio}</span></td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idServicio})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idServicio})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'proveedores') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.razonSocial}</strong></td>
                            <td>${item.nit}</td>
                            <td>${item.telefono || '-'}</td>
                            <td>${item.email || '-'}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idProveedor})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idProveedor})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'clientes') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombreCompleto}</strong></td>
                            <td>${item.ci_nit}</td>
                            <td>${item.telefono || '-'}</td>
                            <td>${item.direccion || '-'}</td>
                            <td>${item.vehiculos_count ?? 0}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idCliente})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idCliente})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'vehiculos') {
            const marcaModelo = item.modelo ? `${item.modelo.marca ? item.modelo.marca.nombre : ''} ${item.modelo.nombre}`.trim() : '-';
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.placa}</strong></td>
                            <td>${item.cliente ? item.cliente.nombreCompleto : '-'}</td>
                            <td>${marcaModelo}</td>
                            <td>${item.anio}</td>
                            <td>${item.color || '-'}</td>
                            <td>${item.kilometraje ?? 0} km</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idVehiculo})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idVehiculo})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'citas') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.fecha}</td>
                            <td>${item.hora ? item.hora.substring(0, 5) : '-'}</td>
                            <td>${item.cliente ? item.cliente.nombreCompleto : '-'}</td>
                            <td>${item.vehiculo ? item.vehiculo.placa : '-'}</td>
                            <td>${item.mecanico ? item.mecanico.nombreCompleto : 'Sin asignar'}</td>
                            <td><span class="status-badge activo">${item.estado}</span></td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idCita})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idCita})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'mecanicos') {
            const especialidadesText = (item.especialidades && item.especialidades.length)
                ? item.especialidades.map(e => e.nombre).join(', ')
                : 'Sin especialidades';
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${item.nombreCompleto}</strong></td>
                            <td>${item.ci}</td>
                            <td>${item.telefono || '-'}</td>
                            <td>${item.sucursal ? item.sucursal.nombre : '-'}</td>
                            <td>${especialidadesText}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idMecanico})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idMecanico})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'repuestos') {
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.codigo}</td>
                            <td><strong>${item.nombre}</strong></td>
                            <td>${item.marca || '-'}</td>
                            <td>Bs. ${item.precioVenta}</td>
                            <td>${item.proveedor ? item.proveedor.razonSocial : 'Sin proveedor'}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idRepuesto})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idRepuesto})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'inventario') {
            const bajoStock = item.stockActual < item.stockMinimo;
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.repuesto ? item.repuesto.nombre : '-'}</td>
                            <td>${item.sucursal ? item.sucursal.nombre : '-'}</td>
                            <td><span class="status-badge ${bajoStock ? 'inactivo' : 'activo'}">${item.stockActual}</span></td>
                            <td>${item.stockMinimo}</td>
                            <td>${item.ubicacion || '-'}</td>
                            <td class="actions-cell">
                                <button class="action-btn edit" onclick="openEditModal(${item.idInventario})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="action-btn delete" onclick="deleteRecord(${item.idInventario})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'movimientos-inventario') {
            const fechaFmt = item.fecha ? new Date(item.fecha.replace(' ', 'T')).toLocaleString('es-ES', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
            rowHtml = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${fechaFmt}</td>
                            <td>${item.inventario && item.inventario.repuesto ? item.inventario.repuesto.nombre : '-'}</td>
                            <td>${item.inventario && item.inventario.sucursal ? item.inventario.sucursal.nombre : '-'}</td>
                            <td>${item.tipo}</td>
                            <td>${item.cantidad}</td>
                            <td>${item.motivo || '-'}</td>
                            <td class="actions-cell">
                                <button class="action-btn delete" onclick="deleteRecord(${item.idMovimiento})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                    `;
        } else if (currentView === 'ordenes-trabajo') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>${item.cliente ? item.cliente.nombreCompleto : item.idCliente}</td>
                        <td><span class="status-badge ${item.estado === 'Completado' ? 'activo' : 'inactivo'}">${item.estado}</span></td>
                        <td>Bs. ${item.totalEstimado || 0}</td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idOrden})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idOrden})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'diagnosticos') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>Orden #${item.idOrden}</td>
                        <td>${item.descripcion}</td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idDiagnostico})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idDiagnostico})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'detalles-orden') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>Orden #${item.idOrden}</td>
                        <td>Bs. ${item.subtotal || 0}</td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idDetalle})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idDetalle})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'notificaciones') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>${item.mensaje}</td>
                        <td>${item.created_at ? new Date(item.created_at).toLocaleString() : '-'}</td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idNotificacion})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idNotificacion})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'pagos') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>Bs. ${item.monto}</td>
                        <td><span class="status-badge ${item.estado === 'Completado' ? 'activo' : 'inactivo'}">${item.estado}</span></td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idPago})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idPago})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'facturas') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>${item.nroFactura}</td>
                        <td>Bs. ${item.total}</td>
                        <td class="actions-cell">
                            <button class="action-btn edit" onclick="openEditModal(${item.idFactura})"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idFactura})"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        } else if (currentView === 'reportes') {
            rowHtml = `<tr>
                        <td>${index + 1}</td>
                        <td>${item.tipo}</td>
                        <td>${item.fechaGeneracion}</td>
                        <td class="actions-cell">
                            <button class="action-btn text-info" onclick="visualizeReport(${item.idReporte})" title="Visualizar Reporte"><i class="fa-regular fa-eye"></i></button>
                            <button class="action-btn text-success" onclick="downloadReport(${item.idReporte})" title="Descargar Reporte"><i class="fa-solid fa-download"></i></button>
                            <button class="action-btn edit" onclick="openEditModal(${item.idReporte})" title="Editar"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button class="action-btn delete" onclick="deleteRecord(${item.idReporte})" title="Eliminar"><i class="fa-regular fa-trash-can"></i></button>
                        </td>
                    </tr>`;
        }

        tbody.innerHTML += rowHtml;
    });
}

// Trigger action for active view
function handleNewAction() {
    const conf = viewConfigs[currentView];
    conf.actionFn();
}

// --- AUTH & LOGIN CHECKS ---
async function checkAuth(isManual = false) {
    try {
        const res = await fetch('/api/me', { headers });

        const loginScreen = document.getElementById('loginScreen');
        const sidebar = document.getElementById('dashboardSidebar');
        const wrapper = document.getElementById('dashboardWrapper');
        const clientPortal = document.getElementById('clientPortal');

        const avatarLetter = document.getElementById('avatarLetter');
        const profileName = document.getElementById('currentProfileName');
        const profileRole = document.getElementById('currentProfileRole');
        const headerAvatar = document.getElementById('headerAvatar');

        if (res.status === 200) {
            const user = await res.json();
            const initial = user.nombreUsuario ? user.nombreUsuario.substring(0, 1).toUpperCase() : '?';


            window.currentUser = user;
            const landingScreen = document.getElementById('landingScreen');

            if (window.location.pathname === '/') {
                // Force landing page visibility
                if (landingScreen) {
                    landingScreen.style.display = 'block';
                    landingScreen.classList.remove('hidden');
                }
                // Hide all dashboard wrappers
                loginScreen.style.display = 'none';
                sidebar.style.display = 'none';
                wrapper.style.display = 'none';
                if (clientPortal) clientPortal.style.display = 'none';
                return; // Exit checkAuth to avoid showing dashboard
            }

            if (landingScreen) {
                landingScreen.style.display = 'none';
                landingScreen.classList.add('hidden');
            }


            avatarLetter.textContent = initial;
            headerAvatar.textContent = initial;
            profileName.textContent = user.nombreUsuario;
            profileRole.textContent = user.rol ? user.rol.nombre : 'Usuario';

            const roleName = user.rol ? user.rol.nombre : '';
            const normalizedRole = normalizeRoleName(roleName);
            const isCliente = normalizedRole === 'cliente';
            const isRecepcionista = normalizedRole === 'recepcionista';
            const isMecanico = normalizedRole === 'mecanico';

            console.log('[auth] rol recibido:', roleName, '| normalizado:', normalizedRole);

            loginScreen.style.display = 'none';
            activarPortalPorRol(roleName || 'Usuario');

            if (isManual) {
                triggerToast('Conexión activa con el servidor');
            }
        } else {
            window.currentUser = null;
            // Show landing screen instead of login by default
            const landingScreen = document.getElementById('landingScreen');
            if (landingScreen) {
                landingScreen.style.display = 'block';
                landingScreen.classList.remove('hidden');
            }

            loginScreen.style.display = 'none';
            sidebar.style.display = 'none';
            wrapper.style.display = 'none';
            if (clientPortal) clientPortal.style.display = 'none';

            if (window.location.pathname === '/login') {
                showLoginScreen();
            } else if (window.location.pathname !== '/') {
                window.history.replaceState({}, '', '/');
            }
        }
    } catch (err) {
        console.error("Auth check failed:", err);
    }
}


function showLoginScreen() {
    const landing = document.getElementById('landingScreen');
    if (landing) {
        landing.classList.add('hidden');
        setTimeout(() => landing.style.display = 'none', 500);
    }
    const login = document.getElementById('loginScreen');
    if (login) {
        login.style.display = 'flex';
    }
    if (window.location.pathname !== '/login') {
        window.history.pushState({}, '', '/login');
    }
}

function hideLoginScreen() {
    const login = document.getElementById('loginScreen');
    if (login) {
        login.style.display = 'none';
    }
    const landing = document.getElementById('landingScreen');
    if (landing) {
        landing.style.display = 'block';
        landing.classList.remove('hidden');
    }
    if (window.location.pathname !== '/') {
        window.history.pushState({}, '', '/');
    }
}

function scrollToLocations() {
    const loc = document.getElementById('landingLocations');
    if (loc) {
        loc.scrollIntoView({ behavior: 'smooth' });
    }
}

async function handleLoginSubmit(e) {
    e.preventDefault();
    const login = document.getElementById('loginUsername').value.trim();
    const contrasena = document.getElementById('loginPassword').value;
    const btn = e.target.querySelector('button[type="submit"]');
    const errBox = document.getElementById('loginError');

    // Reset error
    if (errBox) errBox.style.display = 'none';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verificando...'; }

    const res = await fetch('/api/login', {
        method: 'POST',
        headers,
        credentials: 'same-origin',
        body: JSON.stringify({ login, contrasena })
    });

    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span>Ingresar al Sistema</span>'; }

    if (res.ok) {
        triggerToast('Acceso concedido al sistema');
        window.history.pushState({}, '', '/panel');
        checkAuth();
    } else if (res.status === 429) {
        if (errBox) { errBox.textContent = '⚠️ Demasiados intentos. Espera 1 minuto e inténtalo de nuevo.'; errBox.style.display = 'block'; }
    } else {
        if (errBox) { errBox.textContent = '✕ Usuario o contraseña incorrectos.'; errBox.style.display = 'block'; }
        // Shake animation
        const card = document.querySelector('.login-card');
        if (card) { card.classList.add('shake'); setTimeout(() => card.classList.remove('shake'), 600); }
        document.getElementById('loginPassword').value = '';
        document.getElementById('loginPassword').focus();
    }
}

function openLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

function confirmLogout() {
    fetch('/api/logout', { method: 'POST', headers, credentials: 'same-origin' }).then(() => {
        closeLogoutModal();
        triggerToast('Sesión cerrada correctamente');
        setTimeout(() => {
            window.history.replaceState({}, '', '/');
            checkAuth();
        }, 600);
    });
}

function togglePasswordVisibility() {
    const passInput = document.getElementById('loginPassword');
    const icon = event.target;
    if (passInput.type === 'password') {
        passInput.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passInput.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function fillCredentials(email, password) {
    const userField = document.getElementById('loginUsername');
    const passField = document.getElementById('loginPassword');
    userField.value = email;
    passField.value = password;
    // Briefly highlight the filled fields
    userField.style.borderColor = '#6366f1';
    passField.style.borderColor = '#6366f1';
    userField.style.boxShadow = '0 0 0 4px rgba(99,102,241,0.25)';
    passField.style.boxShadow = '0 0 0 4px rgba(99,102,241,0.25)';
    setTimeout(() => {
        userField.style.borderColor = '';
        passField.style.borderColor = '';
        userField.style.boxShadow = '';
        passField.style.boxShadow = '';
    }, 1200);
    passField.type = 'password';
    document.querySelector('.password-toggle-icon').classList.remove('fa-eye-slash');
    document.querySelector('.password-toggle-icon').classList.add('fa-eye');
}

// --- DYNAMIC FORM INJECTION & CRUD ---
async function openAddModal() {
    editingId = null;
    document.getElementById('crudModalTitle').textContent = `Agregar a ${viewConfigs[currentView].title}`;
    await injectFields();
    document.getElementById('crudModal').style.display = 'flex';
}

async function openEditModal(id) {
    editingId = id;
    document.getElementById('crudModalTitle').textContent = `Editar en ${viewConfigs[currentView].title}`;
    await injectFields();

    // Populate form with existing data
    const item = currentData.find(d => {
        if (currentView === 'usuarios') return d.idUsuario === id;
        if (currentView === 'roles') return d.idRol === id;
        if (currentView === 'permisos') return d.idPermiso === id;
        if (currentView === 'tipos-servicio') return d.idTipoServicio === id;
        if (currentView === 'servicios') return d.idServicio === id;
        if (currentView === 'proveedores') return d.idProveedor === id;
        if (currentView === 'clientes') return d.idCliente === id;
        if (currentView === 'vehiculos') return d.idVehiculo === id;
        if (currentView === 'citas') return d.idCita === id;
        if (currentView === 'mecanicos') return d.idMecanico === id;
        if (currentView === 'repuestos') return d.idRepuesto === id;
        if (currentView === 'inventario') return d.idInventario === id;
        if (currentView === 'movimientos-inventario') return d.idMovimiento === id;
        if (currentView === 'ordenes-trabajo') return d.idOrden === id;
        if (currentView === 'diagnosticos') return d.idDiagnostico === id;
        if (currentView === 'detalles-orden') return d.idDetalle === id;
        if (currentView === 'notificaciones') return d.idNotificacion === id;
        if (currentView === 'pagos') return d.idPago === id;
        if (currentView === 'facturas') return d.idFactura === id;
        if (currentView === 'reportes') return d.idReporte === id;
    });

    if (item) {
        if (currentView === 'usuarios') {
            document.getElementById('f_nombreUsuario').value = item.nombreUsuario;
            document.getElementById('f_email').value = item.email;
            document.getElementById('f_idRol').value = item.idRol;
            document.getElementById('f_estado').value = item.estado;
            document.getElementById('f_contrasena').required = false;
            if (item.created_at) {
                // Format date for input type="date" (YYYY-MM-DD)
                document.getElementById('f_created_at').value = item.created_at.split('T')[0];
            }
        } else if (currentView === 'roles') {
            document.getElementById('f_nombre').value = item.nombre;
            document.getElementById('f_descripcion').value = item.descripcion || '';
        } else if (currentView === 'permisos') {
            document.getElementById('f_nombre').value = item.nombre;
            document.getElementById('f_modulo').value = item.modulo;
            document.getElementById('f_idRol').value = item.idRol;
        } else if (currentView === 'tipos-servicio') {
            document.getElementById('f_nombre').value = item.nombre;
            document.getElementById('f_descripcion').value = item.descripcion || '';
        } else if (currentView === 'servicios') {
            document.getElementById('f_nombre').value = item.nombre;
            document.getElementById('f_precioBase').value = item.precioBase;
            document.getElementById('f_duracionEstimada').value = item.duracionEstimada || '';
            document.getElementById('f_idTipoServicio').value = item.idTipoServicio;
        } else if (currentView === 'proveedores') {
            document.getElementById('f_razonSocial').value = item.razonSocial;
            document.getElementById('f_nit').value = item.nit;
            document.getElementById('f_telefono').value = item.telefono || '';
            document.getElementById('f_email').value = item.email || '';
        } else if (currentView === 'clientes') {
            document.getElementById('f_nombreCompleto').value = item.nombreCompleto;
            document.getElementById('f_ci_nit').value = item.ci_nit;
            document.getElementById('f_telefono').value = item.telefono || '';
            document.getElementById('f_direccion').value = item.direccion || '';
        } else if (currentView === 'vehiculos') {
            document.getElementById('f_idCliente').value = item.idCliente;
            document.getElementById('f_idModelo').value = item.idModelo;
            document.getElementById('f_placa').value = item.placa;
            document.getElementById('f_anio').value = item.anio;
            document.getElementById('f_color').value = item.color || '';
            document.getElementById('f_kilometraje').value = item.kilometraje ?? 0;
        } else if (currentView === 'citas') {
            document.getElementById('f_idCliente').value = item.idCliente;
            document.getElementById('f_idVehiculo').value = item.idVehiculo;
            document.getElementById('f_idMecanico').value = item.idMecanico || '';
            document.getElementById('f_fecha').value = item.fecha;
            document.getElementById('f_hora').value = item.hora ? item.hora.substring(0, 5) : '';
            document.getElementById('f_estado').value = item.estado;
            document.getElementById('f_motivo').value = item.motivo || '';
        } else if (currentView === 'mecanicos') {
            document.getElementById('f_nombreCompleto').value = item.nombreCompleto;
            document.getElementById('f_ci').value = item.ci;
            document.getElementById('f_telefono').value = item.telefono || '';
            document.getElementById('f_idSucursal').value = item.idSucursal;
            const especialidadIds = (item.especialidades || []).map(e => e.idEspecialidad);
            document.querySelectorAll('.f_especialidad').forEach(cb => {
                cb.checked = especialidadIds.includes(parseInt(cb.value, 10));
            });
        } else if (currentView === 'repuestos') {
            document.getElementById('f_codigo').value = item.codigo;
            document.getElementById('f_nombre').value = item.nombre;
            document.getElementById('f_marca').value = item.marca || '';
            document.getElementById('f_precioVenta').value = item.precioVenta;
            document.getElementById('f_idProveedor').value = item.idProveedor || '';
        } else if (currentView === 'inventario') {
            document.getElementById('f_idRepuesto').value = item.idRepuesto;
            document.getElementById('f_idSucursal').value = item.idSucursal;
            document.getElementById('f_stockActual').value = item.stockActual;
            document.getElementById('f_stockMinimo').value = item.stockMinimo;
            document.getElementById('f_ubicacion').value = item.ubicacion || '';
        } else if (currentView === 'ordenes-trabajo') {
            document.getElementById('f_idCliente').value = item.idCliente;
            document.getElementById('f_idVehiculo').value = item.idVehiculo;
            document.getElementById('f_idMecanicoAsignado').value = item.idMecanicoAsignado || '';
            document.getElementById('f_fechaRecepcion').value = item.fechaRecepcion ? item.fechaRecepcion.split('T')[0] : '';
            document.getElementById('f_fechaEstimadaEntrega').value = item.fechaEstimadaEntrega ? item.fechaEstimadaEntrega.split('T')[0] : '';
            document.getElementById('f_estado').value = item.estado || 'Pendiente';
            document.getElementById('f_totalEstimado').value = item.totalEstimado || 0;
            document.getElementById('f_notas').value = item.notas || '';
        } else if (currentView === 'diagnosticos') {
            document.getElementById('f_idOrden').value = item.idOrden;
            document.getElementById('f_descripcion').value = item.descripcion;
        } else if (currentView === 'detalles-orden') {
            document.getElementById('f_idOrden').value = item.idOrden;
            document.getElementById('f_idServicio').value = item.idServicio || '';
            document.getElementById('f_idRepuesto').value = item.idRepuesto || '';
            document.getElementById('f_cantidad').value = item.cantidad || 1;
            document.getElementById('f_precioUnitario').value = item.precioUnitario || 0;
        } else if (currentView === 'notificaciones') {
            document.getElementById('f_idUsuario').value = item.idUsuario;
            document.getElementById('f_mensaje').value = item.mensaje;
            document.getElementById('f_tipo').value = item.tipo;
            document.getElementById('f_leida').value = item.leida || 0;
        } else if (currentView === 'pagos') {
            document.getElementById('f_idFactura').value = item.idFactura;
            document.getElementById('f_idMetodoPago').value = item.idMetodoPago;
            document.getElementById('f_monto').value = item.monto;
            document.getElementById('f_estado').value = item.estado || 'Completado';
        } else if (currentView === 'facturas') {
            document.getElementById('f_idOrden').value = item.idOrden;
            document.getElementById('f_nroFactura').value = item.nroFactura;
            document.getElementById('f_nitCliente').value = item.nitCliente;
            document.getElementById('f_razonSocial').value = item.razonSocial;
            document.getElementById('f_total').value = item.total;
        } else if (currentView === 'reportes') {
            document.getElementById('f_tipoReporte').value = item.tipo;
            const p = item.parametros || '';
            if (p.includes('Desde: ')) {
                const m = p.match(/Desde:\s*([^ |]+)/);
                if (m) document.getElementById('f_param_inicio').value = m[1];
            }
            if (p.includes('Hasta: ')) {
                const m = p.match(/Hasta:\s*([^ |]+)/);
                if (m) document.getElementById('f_param_fin').value = m[1];
            }
            if (p.includes('Formato: ')) {
                const m = p.match(/Formato:\s*([^ |]+)/);
                if (m) document.getElementById('f_param_formato').value = m[1];
            }
        }
    }

    document.getElementById('crudModal').style.display = 'flex';
}

function closeCrudModal() {
    document.getElementById('crudModal').style.display = 'none';
}

async function injectFields() {
    // All catalog data is preloaded in preloadCatalogos() when the view loads.
    // This function now only builds the DOM — no fetch calls needed here.
    const container = document.getElementById('dynamicFormFields');
    container.innerHTML = '';

    if (currentView === 'usuarios') {
        const rolesOpts = allRoles.map(r => `<option value="${r.idRol}">${r.nombre}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre de Usuario</label>
                        <input type="text" class="form-control" id="f_nombreUsuario" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" class="form-control" id="f_email" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" class="form-control" id="f_contrasena" ${editingId ? '' : 'required'} placeholder="${editingId ? 'Dejar en blanco para no cambiar' : '******'}">
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select class="form-control" id="f_idRol" required>
                            <option value="">Seleccione Rol</option>
                            ${rolesOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="f_estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Creación</label>
                        <input type="date" class="form-control" id="f_created_at">
                        <small style="color: #94a3b8; font-size: 0.75rem;">Dejar en blanco para usar la fecha actual.</small>
                    </div>
                `;
    } else if (currentView === 'roles') {
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre del Rol</label>
                        <input type="text" class="form-control" id="f_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" id="f_descripcion">
                    </div>
                `;
    } else if (currentView === 'permisos') {
        const rolesOpts = allRoles.map(r => `<option value="${r.idRol}">${r.nombre}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre del Permiso</label>
                        <input type="text" class="form-control" id="f_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Módulo</label>
                        <input type="text" class="form-control" id="f_modulo" required>
                    </div>
                    <div class="form-group">
                        <label>Rol Relacionado</label>
                        <select class="form-control" id="f_idRol" required>
                            <option value="">Seleccione Rol</option>
                            ${rolesOpts}
                        </select>
                    </div>
                `;
    } else if (currentView === 'tipos-servicio') {
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre de Categoría</label>
                        <input type="text" class="form-control" id="f_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control" id="f_descripcion">
                    </div>
                `;
    } else if (currentView === 'servicios') {
        const tsOpts = allTiposServicio.map(ts => `<option value="${ts.idTipoServicio}">${ts.nombre}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre del Servicio</label>
                        <input type="text" class="form-control" id="f_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Precio Base (Bs.)</label>
                        <input type="number" step="0.01" class="form-control" id="f_precioBase" required>
                    </div>
                    <div class="form-group">
                        <label>Duración Estimada (Minutos)</label>
                        <input type="number" class="form-control" id="f_duracionEstimada">
                    </div>
                    <div class="form-group">
                        <label>Categoría (Tipo Servicio)</label>
                        <select class="form-control" id="f_idTipoServicio" required>
                            <option value="">Seleccione Categoría</option>
                            ${tsOpts}
                        </select>
                    </div>
                `;
    } else if (currentView === 'proveedores') {
        container.innerHTML = `
                    <div class="form-group">
                        <label>Razón Social</label>
                        <input type="text" class="form-control" id="f_razonSocial" required>
                    </div>
                    <div class="form-group">
                        <label>NIT</label>
                        <input type="text" class="form-control" id="f_nit" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="f_telefono">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="f_email">
                    </div>
                `;
    } else if (currentView === 'clientes') {
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" class="form-control" id="f_nombreCompleto" required>
                    </div>
                    <div class="form-group">
                        <label>CI / NIT</label>
                        <input type="text" class="form-control" id="f_ci_nit" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="f_telefono">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" class="form-control" id="f_direccion">
                    </div>
                `;
    } else if (currentView === 'vehiculos') {
        const clientesOpts = allClientes.map(c => `<option value="${c.idCliente}">${c.nombreCompleto} (${c.ci_nit})</option>`).join('');
        const modelosOpts = allModelos.map(m => `<option value="${m.idModelo}">${m.marca ? m.marca.nombre : ''} ${m.nombre}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Cliente</label>
                        <select class="form-control" id="f_idCliente" required>
                            <option value="">Seleccione Cliente</option>
                            ${clientesOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Modelo</label>
                        <select class="form-control" id="f_idModelo" required>
                            <option value="">Seleccione Modelo</option>
                            ${modelosOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Placa</label>
                        <input type="text" class="form-control" id="f_placa" required>
                    </div>
                    <div class="form-group">
                        <label>Año</label>
                        <input type="number" class="form-control" id="f_anio" required>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" class="form-control" id="f_color">
                    </div>
                    <div class="form-group">
                        <label>Kilometraje</label>
                        <input type="number" class="form-control" id="f_kilometraje" value="0">
                    </div>
                `;
    } else if (currentView === 'citas') {
        const clientesOpts = allClientes.map(c => `<option value="${c.idCliente}">${c.nombreCompleto}</option>`).join('');
        const vehiculosOpts = allVehiculos.map(v => `<option value="${v.idVehiculo}">${v.placa}</option>`).join('');
        const mecanicosOpts = allMecanicos.map(m => `<option value="${m.idMecanico}">${m.nombreCompleto}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Cliente</label>
                        <select class="form-control" id="f_idCliente" required>
                            <option value="">Seleccione Cliente</option>
                            ${clientesOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vehículo</label>
                        <select class="form-control" id="f_idVehiculo" required>
                            <option value="">Seleccione Vehículo</option>
                            ${vehiculosOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mecánico (opcional)</label>
                        <select class="form-control" id="f_idMecanico">
                            <option value="">Sin asignar</option>
                            ${mecanicosOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control" id="f_fecha" required>
                    </div>
                    <div class="form-group">
                        <label>Hora</label>
                        <input type="time" class="form-control" id="f_hora" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select class="form-control" id="f_estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Confirmada">Confirmada</option>
                            <option value="Cancelada">Cancelada</option>
                            <option value="Completada">Completada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" class="form-control" id="f_motivo">
                    </div>
                `;
    } else if (currentView === 'mecanicos') {
        const sucursalesOpts = allSucursales.map(s => `<option value="${s.idSucursal}">${s.nombre}</option>`).join('');
        const especialidadesChecks = allEspecialidades.map(e => `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input f_especialidad" value="${e.idEspecialidad}" id="esp_${e.idEspecialidad}">
                        <label class="form-check-label" for="esp_${e.idEspecialidad}">${e.nombre}</label>
                    </div>
                `).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" class="form-control" id="f_nombreCompleto" required>
                    </div>
                    <div class="form-group">
                        <label>CI</label>
                        <input type="text" class="form-control" id="f_ci" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" class="form-control" id="f_telefono">
                    </div>
                    <div class="form-group">
                        <label>Sucursal</label>
                        <select class="form-control" id="f_idSucursal" required>
                            <option value="">Seleccione Sucursal</option>
                            ${sucursalesOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Especialidades</label>
                        ${especialidadesChecks}
                    </div>
                `;
    } else if (currentView === 'repuestos') {
        const proveedoresOpts = allProveedores.map(p => `<option value="${p.idProveedor}">${p.razonSocial}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Código</label>
                        <input type="text" class="form-control" id="f_codigo" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="f_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Marca</label>
                        <input type="text" class="form-control" id="f_marca">
                    </div>
                    <div class="form-group">
                        <label>Precio de Venta (Bs.)</label>
                        <input type="number" step="0.01" class="form-control" id="f_precioVenta" required>
                    </div>
                    <div class="form-group">
                        <label>Proveedor (opcional)</label>
                        <select class="form-control" id="f_idProveedor">
                            <option value="">Sin proveedor</option>
                            ${proveedoresOpts}
                        </select>
                    </div>
                `;
    } else if (currentView === 'inventario') {
        const repuestosOpts = allRepuestos.map(r => `<option value="${r.idRepuesto}">${r.codigo} - ${r.nombre}</option>`).join('');
        const sucursalesOpts = allSucursales.map(s => `<option value="${s.idSucursal}">${s.nombre}</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Repuesto</label>
                        <select class="form-control" id="f_idRepuesto" required>
                            <option value="">Seleccione Repuesto</option>
                            ${repuestosOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sucursal</label>
                        <select class="form-control" id="f_idSucursal" required>
                            <option value="">Seleccione Sucursal</option>
                            ${sucursalesOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock Actual</label>
                        <input type="number" class="form-control" id="f_stockActual" value="0">
                    </div>
                    <div class="form-group">
                        <label>Stock Mínimo</label>
                        <input type="number" class="form-control" id="f_stockMinimo" value="0">
                    </div>
                    <div class="form-group">
                        <label>Ubicación</label>
                        <input type="text" class="form-control" id="f_ubicacion">
                    </div>
                `;
    } else if (currentView === 'movimientos-inventario') {
        const inventariosOpts = allInventarios.map(i => `<option value="${i.idInventario}">${i.repuesto ? i.repuesto.nombre : 'ID:' + i.idRepuesto} — ${i.sucursal ? i.sucursal.nombre : ''} (stock: ${i.stockActual})</option>`).join('');
        container.innerHTML = `
                    <div class="form-group">
                        <label>Inventario</label>
                        <select class="form-control" id="f_idInventario" required>
                            <option value="">Seleccione registro de inventario</option>
                            ${inventariosOpts}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de movimiento</label>
                        <select class="form-control" id="f_tipo" required>
                            <option value="Entrada">Entrada</option>
                            <option value="Salida">Salida</option>
                            <option value="Ajuste">Ajuste (fija el stock al valor indicado)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" class="form-control" id="f_cantidad" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Motivo</label>
                        <input type="text" class="form-control" id="f_motivo">
                    </div>
                `;
    } else if (currentView === 'ordenes-trabajo') {
        container.innerHTML = `
                    <div class="form-group"><label>ID Cliente</label><input type="number" class="form-control" id="f_idCliente" required></div>
                    <div class="form-group"><label>ID Vehículo</label><input type="number" class="form-control" id="f_idVehiculo" required></div>
                    <div class="form-group"><label>ID Mecánico</label><input type="number" class="form-control" id="f_idMecanicoAsignado"></div>
                    <div class="form-group"><label>Fecha Recepción</label><input type="date" class="form-control" id="f_fechaRecepcion"></div>
                    <div class="form-group"><label>Fecha Estimada Entrega</label><input type="date" class="form-control" id="f_fechaEstimadaEntrega"></div>
                    <div class="form-group"><label>Estado</label><select class="form-control" id="f_estado"><option>Pendiente</option><option>En Proceso</option><option>Completado</option><option>Cancelado</option></select></div>
                    <div class="form-group"><label>Total Estimado</label><input type="number" step="0.01" class="form-control" id="f_totalEstimado"></div>
                    <div class="form-group"><label>Notas</label><input type="text" class="form-control" id="f_notas"></div>
                `;
    } else if (currentView === 'diagnosticos') {
        const ordOpts = (window.allOrdenes || []).map(o => `<option value="${o.idOrden}">Orden #${o.idOrden}</option>`).join('');
        container.innerHTML = `<div class="form-group"><label>Orden de Trabajo</label><select class="form-control" id="f_idOrden" required><option value="">Seleccione...</option>${ordOpts}</select></div>
                <div class="form-group"><label>Descripción</label><input type="text" class="form-control" id="f_descripcion" required></div>`;
    } else if (currentView === 'detalles-orden') {
        const ordOpts = (window.allOrdenes || []).map(o => `<option value="${o.idOrden}">Orden #${o.idOrden}</option>`).join('');
        const servOpts = (window.allServicios || []).map(s => `<option value="${s.idServicio}">${s.nombre}</option>`).join('');
        const repOpts = (window.allRepuestos || []).map(r => `<option value="${r.idRepuesto}">${r.nombre}</option>`).join('');
        container.innerHTML = `<div class="form-group"><label>Orden de Trabajo</label><select class="form-control" id="f_idOrden" required><option value="">Seleccione...</option>${ordOpts}</select></div>
                <div class="form-group"><label>Servicio (Opcional)</label><select class="form-control" id="f_idServicio"><option value="">Ninguno</option>${servOpts}</select></div>
                <div class="form-group"><label>Repuesto (Opcional)</label><select class="form-control" id="f_idRepuesto"><option value="">Ninguno</option>${repOpts}</select></div>
                <div class="form-group"><label>Cantidad</label><input type="number" class="form-control" id="f_cantidad" value="1" required></div>
                <div class="form-group"><label>Precio Unitario</label><input type="number" step="0.01" class="form-control" id="f_precioUnitario" value="0" required></div>`;
    } else if (currentView === 'notificaciones') {
        const usOpts = (window.allUsuarios || []).map(u => `<option value="${u.idUsuario}">${u.nombreUsuario}</option>`).join('');
        container.innerHTML = `<div class="form-group"><label>Usuario</label><select class="form-control" id="f_idUsuario" required><option value="">Seleccione...</option>${usOpts}</select></div>
                <div class="form-group"><label>Mensaje</label><input type="text" class="form-control" id="f_mensaje" required></div>
                <div class="form-group"><label>Tipo</label><input type="text" class="form-control" id="f_tipo"></div>
                <div class="form-group"><label>Leída</label><select class="form-control" id="f_leida"><option value="0">No</option><option value="1">Sí</option></select></div>`;
    } else if (currentView === 'pagos') {
        const facOpts = (window.allFacturas || []).map(f => `<option value="${f.idFactura}">Factura #${f.nroFactura}</option>`).join('');
        container.innerHTML = `<div class="form-group"><label>Factura</label><select class="form-control" id="f_idFactura" required><option value="">Seleccione...</option>${facOpts}</select></div>
                <div class="form-group"><label>ID Método de Pago</label><input type="number" class="form-control" id="f_idMetodoPago" required></div>
                <div class="form-group"><label>Monto</label><input type="number" step="0.01" class="form-control" id="f_monto" required></div>
                <div class="form-group"><label>Estado</label><select class="form-control" id="f_estado"><option>Pendiente</option><option>Completado</option><option>Fallido</option></select></div>`;
    } else if (currentView === 'facturas') {
        const ordOpts = (window.allOrdenes || []).map(o => `<option value="${o.idOrden}">Orden #${o.idOrden}</option>`).join('');
        container.innerHTML = `<div class="form-group"><label>Orden</label><select class="form-control" id="f_idOrden" required><option value="">Seleccione...</option>${ordOpts}</select></div>
                <div class="form-group"><label>Nro Factura</label><input type="text" class="form-control" id="f_nroFactura" required></div>
                <div class="form-group"><label>NIT Cliente</label><input type="text" class="form-control" id="f_nitCliente" required></div>
                <div class="form-group"><label>Razón Social</label><input type="text" class="form-control" id="f_razonSocial" required></div>
                <div class="form-group"><label>Total</label><input type="number" step="0.01" class="form-control" id="f_total" required></div>`;
    } else if (currentView === 'reportes') {
        container.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><i class="fas fa-chart-pie me-2 text-primary"></i>Tipo de Reporte</label>
                            <select class="form-select" id="f_tipoReporte" required>
                                <option value="">Seleccione el tipo de reporte a generar...</option>
                                <option value="Ingresos y Ventas">Ingresos y Ventas Financieras</option>
                                <option value="Rendimiento de Órdenes">Rendimiento de Órdenes de Trabajo</option>
                                <option value="Estado de Inventario">Estado del Inventario de Repuestos</option>
                                <option value="Historial de Clientes">Historial y Fidelidad de Clientes</option>
                                <option value="Citas Programadas">Citas Programadas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt me-2 text-primary"></i>Fecha Inicio (Opcional)</label>
                            <input type="date" class="form-control" id="f_param_inicio">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-check me-2 text-primary"></i>Fecha Fin (Opcional)</label>
                            <input type="date" class="form-control" id="f_param_fin">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><i class="fas fa-file-export me-2 text-primary"></i>Formato de Exportación</label>
                            <select class="form-select" id="f_param_formato">
                                <option value="PDF">Documento PDF (.pdf)</option>
                                <option value="EXCEL">Hoja de Cálculo (.xlsx)</option>
                                <option value="CSV">Archivo de Texto (.csv)</option>
                            </select>
                        </div>
                    </div>
                </div>`;
    }
}

async function handleCrudSubmit(e) {
    e.preventDefault();
    let body = {};
    let url = `/api/${currentView}`;
    let method = editingId ? 'PUT' : 'POST';

    if (editingId) {
        url += `/${editingId}`;
    }

    if (currentView === 'usuarios') {
        body.nombreUsuario = document.getElementById('f_nombreUsuario').value;
        body.email = document.getElementById('f_email').value;
        body.idRol = parseInt(document.getElementById('f_idRol').value, 10);
        body.estado = parseInt(document.getElementById('f_estado').value, 10);

        const fecha = document.getElementById('f_created_at').value;
        if (fecha) body.created_at = fecha;

        const pass = document.getElementById('f_contrasena').value;
        if (pass) body.contrasena = pass;
    } else if (currentView === 'roles') {
        body.nombre = document.getElementById('f_nombre').value;
        body.descripcion = document.getElementById('f_descripcion').value;
    } else if (currentView === 'permisos') {
        body.nombre = document.getElementById('f_nombre').value;
        body.modulo = document.getElementById('f_modulo').value;
        body.idRol = parseInt(document.getElementById('f_idRol').value, 10);
    } else if (currentView === 'tipos-servicio') {
        body.nombre = document.getElementById('f_nombre').value;
        body.descripcion = document.getElementById('f_descripcion').value;
    } else if (currentView === 'servicios') {
        body.nombre = document.getElementById('f_nombre').value;
        body.precioBase = parseFloat(document.getElementById('f_precioBase').value);
        const dur = document.getElementById('f_duracionEstimada').value;
        if (dur) body.duracionEstimada = parseInt(dur, 10);
        body.idTipoServicio = parseInt(document.getElementById('f_idTipoServicio').value, 10);
    } else if (currentView === 'proveedores') {
        body.razonSocial = document.getElementById('f_razonSocial').value;
        body.nit = document.getElementById('f_nit').value;
        body.telefono = document.getElementById('f_telefono').value;
        body.email = document.getElementById('f_email').value;
    } else if (currentView === 'clientes') {
        body.nombreCompleto = document.getElementById('f_nombreCompleto').value;
        body.ci_nit = document.getElementById('f_ci_nit').value;
        body.telefono = document.getElementById('f_telefono').value;
        body.direccion = document.getElementById('f_direccion').value;
    } else if (currentView === 'vehiculos') {
        body.idCliente = parseInt(document.getElementById('f_idCliente').value, 10);
        body.idModelo = parseInt(document.getElementById('f_idModelo').value, 10);
        body.placa = document.getElementById('f_placa').value;
        body.anio = parseInt(document.getElementById('f_anio').value, 10);
        body.color = document.getElementById('f_color').value;
        body.kilometraje = parseInt(document.getElementById('f_kilometraje').value || 0, 10);
    } else if (currentView === 'citas') {
        body.idCliente = parseInt(document.getElementById('f_idCliente').value, 10);
        body.idVehiculo = parseInt(document.getElementById('f_idVehiculo').value, 10);
        const idMec = document.getElementById('f_idMecanico').value;
        body.idMecanico = idMec ? parseInt(idMec, 10) : null;
        body.fecha = document.getElementById('f_fecha').value;
        body.hora = document.getElementById('f_hora').value;
        body.estado = document.getElementById('f_estado').value;
        body.motivo = document.getElementById('f_motivo').value;
    } else if (currentView === 'mecanicos') {
        body.nombreCompleto = document.getElementById('f_nombreCompleto').value;
        body.ci = document.getElementById('f_ci').value;
        body.telefono = document.getElementById('f_telefono').value;
        body.idSucursal = parseInt(document.getElementById('f_idSucursal').value, 10);
        body.especialidades = Array.from(document.querySelectorAll('.f_especialidad:checked')).map(cb => parseInt(cb.value, 10));
    } else if (currentView === 'repuestos') {
        body.codigo = document.getElementById('f_codigo').value;
        body.nombre = document.getElementById('f_nombre').value;
        body.marca = document.getElementById('f_marca').value;
        body.precioVenta = parseFloat(document.getElementById('f_precioVenta').value);
        const idProv = document.getElementById('f_idProveedor').value;
        body.idProveedor = idProv ? parseInt(idProv, 10) : null;
    } else if (currentView === 'inventario') {
        body.idRepuesto = parseInt(document.getElementById('f_idRepuesto').value, 10);
        body.idSucursal = parseInt(document.getElementById('f_idSucursal').value, 10);
        body.stockActual = parseInt(document.getElementById('f_stockActual').value || 0, 10);
        body.stockMinimo = parseInt(document.getElementById('f_stockMinimo').value || 0, 10);
        body.ubicacion = document.getElementById('f_ubicacion').value;
    } else if (currentView === 'movimientos-inventario') {
        body.idInventario = parseInt(document.getElementById('f_idInventario').value, 10);
        body.tipo = document.getElementById('f_tipo').value;
        body.cantidad = parseInt(document.getElementById('f_cantidad').value, 10);
        body.motivo = document.getElementById('f_motivo').value;
    } else if (currentView === 'ordenes-trabajo') {
        body.idCliente = parseInt(document.getElementById('f_idCliente').value, 10);
        body.idVehiculo = parseInt(document.getElementById('f_idVehiculo').value, 10);
        const mec = document.getElementById('f_idMecanicoAsignado').value;
        body.idMecanicoAsignado = mec ? parseInt(mec, 10) : null;
        body.fechaRecepcion = document.getElementById('f_fechaRecepcion').value;
        body.fechaEstimadaEntrega = document.getElementById('f_fechaEstimadaEntrega').value;
        body.estado = document.getElementById('f_estado').value;
        body.totalEstimado = parseFloat(document.getElementById('f_totalEstimado').value || 0);
        body.notas = document.getElementById('f_notas').value;
    } else if (currentView === 'diagnosticos') {
        body.idOrden = parseInt(document.getElementById('f_idOrden').value, 10);
        body.descripcion = document.getElementById('f_descripcion').value;
    } else if (currentView === 'detalles-orden') {
        body.idOrden = parseInt(document.getElementById('f_idOrden').value, 10);
        const idS = document.getElementById('f_idServicio').value;
        body.idServicio = idS ? parseInt(idS, 10) : null;
        const idR = document.getElementById('f_idRepuesto').value;
        body.idRepuesto = idR ? parseInt(idR, 10) : null;
        body.cantidad = parseFloat(document.getElementById('f_cantidad').value || 1);
        body.precioUnitario = parseFloat(document.getElementById('f_precioUnitario').value || 0);
        body.subtotal = body.cantidad * body.precioUnitario;
    } else if (currentView === 'notificaciones') {
        body.idUsuario = parseInt(document.getElementById('f_idUsuario').value, 10);
        body.mensaje = document.getElementById('f_mensaje').value;
        body.tipo = document.getElementById('f_tipo').value;
        body.leida = parseInt(document.getElementById('f_leida').value, 10);
    } else if (currentView === 'pagos') {
        body.idFactura = parseInt(document.getElementById('f_idFactura').value, 10);
        body.idMetodoPago = parseInt(document.getElementById('f_idMetodoPago').value, 10);
        body.monto = parseFloat(document.getElementById('f_monto').value);
        body.estado = document.getElementById('f_estado').value;
    } else if (currentView === 'facturas') {
        body.idOrden = parseInt(document.getElementById('f_idOrden').value, 10);
        body.nroFactura = document.getElementById('f_nroFactura').value;
        body.nitCliente = document.getElementById('f_nitCliente').value;
        body.razonSocial = document.getElementById('f_razonSocial').value;
        body.total = parseFloat(document.getElementById('f_total').value);
    } else if (currentView === 'reportes') {
        body.tipo = document.getElementById('f_tipoReporte').value;
        const inicio = document.getElementById('f_param_inicio').value;
        const fin = document.getElementById('f_param_fin').value;
        const formato = document.getElementById('f_param_formato').value;

        let paramsArr = [];
        if (inicio) paramsArr.push(`Desde: ${inicio}`);
        if (fin) paramsArr.push(`Hasta: ${fin}`);
        paramsArr.push(`Formato: ${formato}`);
        body.parametros = paramsArr.join(' | ');
    }

    const res = await fetch(url, {
        method,
        headers,
        body: JSON.stringify(body)
    });

    if (res.ok) {
        triggerToast(editingId ? 'Registro actualizado con éxito' : 'Registro creado con éxito');
        closeCrudModal();
        refreshData();
    } else {
        const data = await res.json();
        let errorMsg = data.message || 'Fallo en la operación';
        if (data.errors) {
            errorMsg += '\n\n' + Object.values(data.errors).flat().join('\n');
        }
        alert(`Error: ${errorMsg}`);
    }
}

async function deleteRecord(id) {
    if (!confirm('¿Está seguro de que desea eliminar este registro?')) return;
    try {
        const res = await fetch(`/api/${currentView}/${id}`, {
            method: 'DELETE',
            headers
        });

        if (res.ok) {
            triggerToast('Registro eliminado');
            refreshData();
        } else {
            alert('No se pudo eliminar el registro.');
        }
    } catch (err) {
        console.error(err);
        alert('No se pudo eliminar el registro.');
    }
}

async function visualizeReport(id) {
    try {
        const res = await fetch(`/api/reportes/${id}/data`, { headers });
        if (!res.ok) throw new Error("No se pudieron cargar los datos del reporte.");
        const json = await res.json();

        // Build the preview HTML
        let html = `<div style="background: rgba(10,26,23,0.7); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <h5 style="color:#b6f24a; margin:0 0 8px 0;"><i class="fas fa-chart-pie me-2"></i>${json.tipo}</h5>
                    <p style="margin:0; color:#9db8b0; font-size:0.85rem;">${json.parametros || 'Sin parámetros definidos'}</p>
                </div>`;

        if (json.data && json.data.length > 0) {
            const keys = Object.keys(json.data[0]);
            html += `<div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                            <thead>
                                <tr style="background:rgba(182,242,74,0.1); border-bottom:1px solid rgba(182,242,74,0.3);">
                                    ${keys.map(k => `<th style="padding:10px 12px; text-align:left; color:#b6f24a; font-weight:600; white-space:nowrap;">${k.replace(/([A-Z])/g, ' $1').trim()}</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                ${json.data.map((row, i) => `
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05); background:${i % 2 === 0 ? 'rgba(255,255,255,0.01)' : 'transparent'};">
                                    ${keys.map(k => `<td style="padding:9px 12px; color:#ffffff;">${row[k] ?? '-'}</td>`).join('')}
                                </tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                    <p style="margin-top:10px; font-size:0.8rem; color:#9db8b0;">${json.data.length} registro(s) encontrado(s)</p>`;
        } else {
            html += `<div style="padding:30px; text-align:center; color:#f59e0b;">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>No hay datos disponibles para este tipo de reporte.</p>
                    </div>`;
        }

        // Use existing modal infrastructure
        document.getElementById('crudModalTitle').textContent = 'Vista Previa del Reporte';
        document.getElementById('dynamicFormFields').innerHTML = html;

        // Override the form submit to just close
        const form = document.getElementById('crudForm');
        form.onsubmit = (e) => { e.preventDefault(); closeCrudModal(); };

        // Replace the form-actions buttons temporarily
        const formActions = form.querySelector('.form-actions');
        if (formActions) {
            formActions.innerHTML = `
                        <button type="button" class="btn btn-secondary" onclick="closeCrudModal()">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="downloadReport(${id})">
                            <i class="fas fa-download me-2"></i>Descargar Reporte
                        </button>
                    `;
        }

        document.getElementById('crudModal').style.display = 'flex';

    } catch (e) {
        console.error(e);
        alert("Error al cargar la visualización del reporte: " + e.message);
    }
}

function downloadReport(id) {
    window.open(`/api/reportes/${id}/download`, '_blank');
}

// --- SEARCH FILTER ---
function handleSearch(q) {
    const query = q.toLowerCase();
    const filtered = currentData.filter(d => {
        if (currentView === 'usuarios') {
            return d.nombreUsuario.toLowerCase().includes(query) || d.email.toLowerCase().includes(query);
        } else if (currentView === 'roles') {
            return d.nombre.toLowerCase().includes(query) || (d.descripcion && d.descripcion.toLowerCase().includes(query));
        } else if (currentView === 'permisos') {
            return d.nombre.toLowerCase().includes(query) || d.modulo.toLowerCase().includes(query);
        } else if (currentView === 'tipos-servicio') {
            return d.nombre.toLowerCase().includes(query);
        } else if (currentView === 'servicios') {
            return d.nombre.toLowerCase().includes(query);
        } else if (currentView === 'proveedores') {
            return d.razonSocial.toLowerCase().includes(query) || d.nit.toLowerCase().includes(query);
        } else if (currentView === 'clientes') {
            return d.nombreCompleto.toLowerCase().includes(query) || d.ci_nit.toLowerCase().includes(query);
        } else if (currentView === 'vehiculos') {
            return d.placa.toLowerCase().includes(query) || (d.cliente && d.cliente.nombreCompleto.toLowerCase().includes(query));
        } else if (currentView === 'citas') {
            return (d.cliente && d.cliente.nombreCompleto.toLowerCase().includes(query)) || (d.vehiculo && d.vehiculo.placa.toLowerCase().includes(query));
        } else if (currentView === 'mecanicos') {
            return d.nombreCompleto.toLowerCase().includes(query) || d.ci.toLowerCase().includes(query);
        } else if (currentView === 'repuestos') {
            return d.codigo.toLowerCase().includes(query) || d.nombre.toLowerCase().includes(query);
        } else if (currentView === 'inventario') {
            return (d.repuesto && d.repuesto.nombre.toLowerCase().includes(query)) || (d.sucursal && d.sucursal.nombre.toLowerCase().includes(query));
        } else if (currentView === 'movimientos-inventario') {
            return (d.inventario && d.inventario.repuesto && d.inventario.repuesto.nombre.toLowerCase().includes(query)) || d.tipo.toLowerCase().includes(query);
        }
        return true;
    });
    renderTable(filtered);
}

// --- TOAST NOTIFICATIONS ---
function triggerToast(text) {
    document.getElementById('toastText').textContent = text;
    document.getElementById('toastBox').style.display = 'flex';
}

function closeToast() {
    document.getElementById('toastBox').style.display = 'none';
}

// Initial setup
checkAuth();

// Read the URL path to load the correct view
const initialPath = window.location.pathname.replace(/^\/|\/$/g, '');
const validViews = ['panel', 'usuarios', 'roles', 'permisos', 'servicios', 'tipos-servicio', 'proveedores', 'clientes', 'vehiculos', 'citas', 'mecanicos', 'repuestos', 'inventario', 'movimientos-inventario', 'ordenes-trabajo', 'diagnosticos', 'detalles-orden', 'notificaciones', 'pagos', 'facturas', 'reportes'];

if (initialPath && validViews.includes(initialPath)) {
    switchView(initialPath, false); // false = don't pushState on initial load
} else {
    switchView('panel', false);
}

// --- 3D TILT EFFECT FOR PROFILES ---
function init3DTilt() {
    const cards = document.querySelectorAll('.tilt-element');
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -15; // Invertido para sensación real
            const rotateY = ((x - centerX) / centerX) * 15;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
        });
    });
}

// Inicializar tilt para las tarjetas que existan en el DOM
setTimeout(init3DTilt, 500);

