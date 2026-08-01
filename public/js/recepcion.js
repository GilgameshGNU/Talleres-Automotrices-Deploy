// ================================================================
// recepcion.js — Portal de Recepcionista SisGest Pro
// Depende de: apiFetch() definido en dashboard.js
// ================================================================

let _recepcionIniciado = false;
let semanaOffset = 0;          // 0 = semana actual, -1 = anterior, +1 = siguiente
let rpClienteSeleccionado = null; // { idCliente, nombreCompleto }
let rpVehiculoSeleccionado = null; // { idVehiculo, placa }
let rpEditCitaId = null;
let _rpNotificaciones = [];    // cache para filtrar sin refetch

// ─── INIT ─────────────────────────────────────────────────────────────────────
function initRecepcionPortal() {
    if (_recepcionIniciado) return;
    _recepcionIniciado = true;

    // Datos del usuario
    if (window.currentUser) {
        document.getElementById('rp-username').textContent = window.currentUser.nombreUsuario;
        document.getElementById('rp-avatar').textContent =
            window.currentUser.nombreUsuario.charAt(0).toUpperCase();
    }

    // Fecha actual
    const opts = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('rp-date-label').textContent =
        new Date().toLocaleDateString('es-ES', opts);

    // Hora de inicio por defecto en wizard
    const ahora = new Date();
    ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
    const iso = ahora.toISOString().slice(0, 16);
    const inp = document.getElementById('rp-orden-hora-inicio');
    if (inp) inp.value = iso;

    // Cargar dashboard
    loadRecepDashboard();
}

// ─── NAVEGACIÓN POR TABS ──────────────────────────────────────────────────────
function switchRecepTab(tabId) {
    // Ocultar todos los tabs
    document.querySelectorAll('.rp-tab-content').forEach(t => t.style.display = 'none');
    // Mostrar tab seleccionado
    const target = document.getElementById(`rp-tab-${tabId}`);
    if (target) target.style.display = 'block';

    // Actualizar botones navbar
    document.querySelectorAll('.op-nav-btn').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById(`rp-nav-${tabId}`);
    if (btn) btn.classList.add('active');

    // Carga específica por tab
    if (tabId === 'clientes') loadClientes();
    if (tabId === 'citas') loadCitasSemana();
    if (tabId === 'servicios') loadServicios();
    if (tabId === 'notificaciones') loadNotificaciones();
    if (tabId === 'nueva-orden') loadMecanicosConCarga();
}

// ─── DASHBOARD ────────────────────────────────────────────────────────────────
async function loadRecepDashboard() {
    try {
        const [citasRes, ordRes, notifRes] = await Promise.all([
            apiFetch('/api/citas/hoy'),
            apiFetch('/api/ordenes-trabajo'),
            apiFetch('/api/notificaciones/no-leidas')
        ]);

        if (citasRes.ok) {
            const citas = await citasRes.json();
            const confirmadas = citas.filter(c => c.estado === 'Confirmada').length;
            document.getElementById('kpi-rp-citas').textContent = citas.length;
            document.getElementById('kpi-rp-confirmadas').textContent = confirmadas;
            renderCitasDashboard(citas);
        }

        if (ordRes.ok) {
            const ordenes = await ordRes.json();
            const activas = ordenes.filter(o =>
                o.estado !== 'Completado' && o.estado !== 'Terminado'
            ).length;
            document.getElementById('kpi-rp-ordenes').textContent = activas;
        }

        if (notifRes.ok) {
            const notifs = await notifRes.json();
            document.getElementById('kpi-rp-notif').textContent = notifs.length;
            // Actualizar badge campana
            const badge = document.getElementById('rp-notif-badge');
            if (badge) {
                badge.textContent = notifs.length;
                badge.style.display = notifs.length > 0 ? 'flex' : 'none';
            }
        }
    } catch (e) {
        console.error('Error cargando dashboard de recepción:', e);
    }
}

function renderCitasDashboard(citas) {
    const tbody = document.getElementById('rp-dashboard-citas-tbody');
    if (!tbody) return;

    if (citas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="padding:2rem; text-align:center; color:var(--text-muted,#5f9c92);">
            <i class="fa-regular fa-calendar" style="font-size:1.5rem; display:block; margin-bottom:.5rem; opacity:.4;"></i>
            No hay citas programadas para hoy
        </td></tr>`;
        return;
    }

    tbody.innerHTML = citas.map(c => `
        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
            <td style="padding:.7rem .75rem; color:#fff; font-weight:700;">${c.hora ? c.hora.substring(0,5) : '—'}</td>
            <td style="padding:.7rem .75rem; color:var(--text-secondary,#9db8b0);">${c.cliente ? c.cliente.nombreCompleto : '—'}</td>
            <td style="padding:.7rem .75rem; color:var(--text-secondary,#9db8b0);">${c.vehiculo ? c.vehiculo.placa : '—'}</td>
            <td style="padding:.7rem .75rem; color:var(--text-secondary,#9db8b0);">${c.motivo || '—'}</td>
            <td style="padding:.7rem .75rem;">
                <span class="status-badge" data-status="${c.estado}">${c.estado}</span>
            </td>
            <td style="padding:.7rem .75rem;">
                <button class="op-btn-ghost" style="padding:.3rem .7rem; font-size:.78rem;"
                    onclick="recibirCita(${c.idCita || c.id || 0}, ${c.idCliente || 'null'}, ${c.idVehiculo || 'null'})">
                    <i class="fa-solid fa-clipboard-check"></i> Recibir
                </button>
            </td>
        </tr>
    `).join('');
}

// ─── CLIENTES ─────────────────────────────────────────────────────────────────
async function loadClientes(query) {
    const q = (query || '').trim();
    const container = document.getElementById('rp-search-results');
    if (!container) return;

    container.innerHTML = `<div style="text-align:center; padding:1rem; color:var(--text-muted,#5f9c92);"><i class="fa-solid fa-spinner fa-spin"></i> Buscando...</div>`;

    try {
        const url = q ? '/api/clientes/buscar?q=' + encodeURIComponent(q) : '/api/clientes/buscar';
        const res = await apiFetch(url);
        if (!res.ok) {
            container.innerHTML = `<div style="text-align:center; padding:1rem; color:var(--text-muted,#5f9c92);">Error al buscar clientes. Intenta de nuevo.</div>`;
            return;
        }

        const clientes = await res.json();

        if (clientes.length === 0) {
            container.innerHTML = `<div class="glass-card" style="text-align:center; padding:2rem;">
                <i class="fa-solid fa-user-slash" style="font-size:2rem; color:var(--text-muted,#5f9c92); display:block; margin-bottom:1rem; opacity:.5;"></i>
                <p style="color:var(--text-secondary,#9db8b0); margin:0 0 1rem;">No se encontraron clientes ${q ? 'con "<strong style="color:#fff;">' + q + '</strong>"' : ''}</p>
                <button class="op-btn-primary" onclick="abrirOffcanvasCliente('${q.replace(/'/g, "\\'")}')">
                    <i class="fa-solid fa-user-plus"></i> Registrar ${q ? '"' + q + '" como ' : ''}Nuevo Cliente
                </button>
            </div>`;
            return;
        }

        container.innerHTML = clientes.map(c => `
            <div class="rp-cliente-card">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div style="width:42px; height:42px; border-radius:50%;
                        background:rgba(182,242,74,0.12); color:var(--accent-primary,#b6f24a);
                        font-weight:900; font-size:1.1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        ${(c.nombreCompleto || 'C').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div style="color:#fff; font-weight:700;">${c.nombreCompleto || 'Sin nombre'}</div>
                        <div style="color:var(--text-muted,#5f9c92); font-size:.8rem;">CI/NIT: ${c.ci_nit || '—'} | Tel: ${c.telefono || '—'}</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:.75rem;">
                    <span class="status-badge" data-status="${c.idUsuario ? 'Confirmada' : 'Disponible'}" style="font-size:.72rem;">
                        ${c.idUsuario ? 'Con cuenta' : 'Sin cuenta'}
                    </span>
                    <button class="op-btn-primary" style="padding:.4rem .9rem; font-size:.8rem;"
                        onclick="preseleccionarClienteOrden(${c.idCliente}, '${(c.nombreCompleto || '').replace(/'/g, "\\'")}')">
                        <i class="fa-solid fa-file-signature"></i> Crear Orden
                    </button>
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.error('Error buscando clientes:', e);
        container.innerHTML = `<div style="text-align:center; padding:1rem; color:var(--text-muted,#5f9c92);">Error al buscar clientes. Revisa la consola o inténtalo de nuevo.</div>`;
    }
}

// ─── OFFCANVAS Y MODAL CREDENCIALES ──────────────────────────────────────────
function abrirOffcanvasCliente(nombrePredefinido) {
    const oc = document.getElementById('rp-offcanvas-cliente');
    if (oc) oc.style.display = 'block';
    document.getElementById('rp-form-cliente')?.reset();
    if (nombrePredefinido && typeof nombrePredefinido === 'string') {
        const inpNombre = document.getElementById('rp-cli-nombre');
        if (inpNombre) inpNombre.value = nombrePredefinido;
    }
}

function cerrarOffcanvasCliente() {
    const oc = document.getElementById('rp-offcanvas-cliente');
    if (oc) oc.style.display = 'none';
}

async function crearClienteConUsuario(e) {
    e.preventDefault();
    const passwordInp = document.getElementById('rp-cli-password')?.value;
    const payload = {
        nombreCompleto: document.getElementById('rp-cli-nombre').value,
        ci_nit:         document.getElementById('rp-cli-ci').value,
        telefono:       document.getElementById('rp-cli-telefono').value,
        email:          document.getElementById('rp-cli-email').value,
        direccion:      document.getElementById('rp-cli-direccion').value,
        password:       passwordInp || undefined,
    };

    try {
        const res = await apiFetch('/api/clientes/con-usuario', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            const data = await res.json();
            cerrarOffcanvasCliente();
            // Mostrar credenciales UNA SOLA VEZ
            const u = data.usuario;
            document.getElementById('rp-cred-usuario').textContent = `${u.nombreUsuario} / ${u.email}`;
            document.getElementById('rp-cred-password').textContent =
                data.passwordPlano || u.passwordPlano || payload.password || payload.ci_nit;
            document.getElementById('rp-modal-credenciales').style.display = 'flex';

            // Actualizar búsquedas si corresponde
            const searchInput = document.getElementById('rp-search-input');
            if (searchInput) {
                searchInput.value = payload.nombreCompleto;
                loadClientes(payload.nombreCompleto);
            }
            const ordenSearchInput = document.getElementById('rp-orden-search-cliente');
            if (ordenSearchInput) {
                ordenSearchInput.value = payload.nombreCompleto;
                buscarClienteParaOrden(payload.nombreCompleto);
            }
        } else {
            const err = await res.json();
            if (typeof triggerToast === 'function')
                triggerToast('Error: ' + (err.message || 'Verifica los datos'), 'error');
            else alert('Error: ' + (err.message || 'Verifica los datos'));
        }
    } catch (err) {
        console.error('Error creando cliente:', err);
    }
}

function cerrarModalCredenciales() {
    document.getElementById('rp-modal-credenciales').style.display = 'none';
    // Limpiar las credenciales de la memoria
    document.getElementById('rp-cred-usuario').textContent = '';
    document.getElementById('rp-cred-password').textContent = '';
    if (typeof triggerToast === 'function') triggerToast('Cliente registrado con éxito');
}

function imprimirCredenciales() {
    // Aislar solo la tarjeta para impresión
    const original = document.body.innerHTML;
    const cred = document.querySelector('.credencial-card').outerHTML;
    document.body.innerHTML = `<style>body{background:#071613;font-family:'Outfit',sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;}</style>${cred}`;
    window.print();
    document.body.innerHTML = original;
    location.reload();
}

// ─── WIZARD NUEVA ORDEN ───────────────────────────────────────────────────────
async function loadMecanicosConCarga() {
    try {
        const res = await apiFetch('/api/mecanicos/con-carga');
        if (!res.ok) return;
        const mecanicos = await res.json();
        const sel = document.getElementById('rp-mecanicoSelect');
        if (!sel) return;
        sel.innerHTML = '<option value="">— Selecciona un mecánico —</option>' +
            mecanicos.map(m =>
                `<option value="${m.idMecanico}">${m.nombreCompleto} (${m.ordenes_activas_count || 0} órdenes activas)</option>`
            ).join('');
    } catch (e) {
        console.error('Error cargando mecánicos:', e);
    }
}

function toggleAsignacion() {
    const especifico = document.getElementById('rp-asign-especifico').checked;
    const wrap = document.getElementById('rp-mecanico-select-wrap');
    if (wrap) wrap.style.display = especifico ? 'block' : 'none';
    if (especifico) loadMecanicosConCarga();
}

async function buscarClienteParaOrden(query) {
    const q = (query || '').trim();
    const container = document.getElementById('rp-paso1-resultados');
    if (!container) return;

    container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem;"><i class="fa-solid fa-spinner fa-spin"></i> Buscando...</div>`;

    try {
        const url = q ? '/api/clientes/buscar?q=' + encodeURIComponent(q) : '/api/clientes/buscar';
        const res = await apiFetch(url);
        if (!res.ok) {
            container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem; padding:.75rem;">Error al buscar clientes. Intenta nuevamente.</div>`;
            return;
        }
        const clientes = await res.json();

        if (clientes.length === 0) {
            container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem; padding:.75rem; display:flex; justify-content:space-between; align-items:center;">
                <span>No se encontraron clientes ${q ? 'con "' + q + '"' : ''}.</span>
                <button class="op-btn-primary" style="font-size:.78rem; padding:.3rem .7rem;" onclick="abrirOffcanvasCliente('${q.replace(/'/g, "\\'")}')">
                    <i class="fa-solid fa-user-plus"></i> Registrar Cliente
                </button>
            </div>`;
            return;
        }

        container.innerHTML = clientes.map(c => `
            <div onclick="seleccionarClienteOrden(${c.idCliente}, '${c.nombreCompleto.replace(/'/g, "\\'")}')"
                style="padding:.7rem 1rem; border-radius:8px; cursor:pointer;
                    background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
                    margin-bottom:.4rem; display:flex; align-items:center; gap:.75rem; transition:background .2s;"
                onmouseover="this.style.background='rgba(182,242,74,0.08)'"
                onmouseout="this.style.background='rgba(255,255,255,0.04)'">
                <div style="width:34px; height:34px; border-radius:50%; background:rgba(182,242,74,0.12);
                    color:var(--accent-primary,#b6f24a); font-weight:900; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    ${c.nombreCompleto.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div style="color:#fff; font-weight:700; font-size:.9rem;">${c.nombreCompleto}</div>
                    <div style="color:var(--text-muted,#5f9c92); font-size:.75rem;">CI: ${c.ci_nit} | Tel: ${c.telefono || '—'}</div>
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.error('Error buscando para orden:', e);
        container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem; padding:.75rem;">Error al buscar clientes. Revisa la consola o inténtalo nuevamente.</div>`;
    }
}

async function seleccionarClienteOrden(idCliente, nombre) {
    rpClienteSeleccionado = { idCliente, nombreCompleto: nombre };
    rpVehiculoSeleccionado = null;

    // Actualizar UI paso 1
    document.getElementById('rp-paso1-resultados').innerHTML = '';
    document.getElementById('rp-paso1-seleccionado').style.display = 'block';
    document.getElementById('rp-paso1-nombre').textContent = nombre;

    // Actualizar stepper
    document.getElementById('rp-step-1').classList.remove('active');
    document.getElementById('rp-step-1').classList.add('done');
    document.getElementById('rp-step-2').classList.add('active');

    // Mostrar paso 2 y cargar vehículos
    const paso2 = document.getElementById('rp-paso-2');
    if (paso2) paso2.style.display = 'block';
    document.getElementById('rp-paso-3').style.display = 'none';

    await cargarVehiculosDeCliente(idCliente);
    paso2.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function deseleccionarCliente() {
    rpClienteSeleccionado = null;
    rpVehiculoSeleccionado = null;
    document.getElementById('rp-paso1-seleccionado').style.display = 'none';
    document.getElementById('rp-paso-2').style.display = 'none';
    document.getElementById('rp-paso-3').style.display = 'none';
    document.getElementById('rp-step-1').classList.add('active');
    document.getElementById('rp-step-1').classList.remove('done');
    document.getElementById('rp-step-2').classList.remove('active', 'done');
    document.getElementById('rp-step-3').classList.remove('active');
}

async function cargarVehiculosDeCliente(idCliente) {
    const container = document.getElementById('rp-paso2-vehiculos');
    container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem;"><i class="fa-solid fa-spinner fa-spin"></i> Cargando vehículos...</div>`;

    try {
        const res = await apiFetch(`/api/clientes/${idCliente}`);
        if (!res.ok) return;
        const data = await res.json();
        const vehiculos = data.vehiculos || [];

        if (vehiculos.length === 0) {
            container.innerHTML = `<div style="color:var(--text-muted,#5f9c92); font-size:.85rem;">Este cliente no tiene vehículos registrados.</div>`;
            return;
        }

        container.innerHTML = vehiculos.map(v => `
            <div class="rp-vehiculo-card" id="rv-${v.idVehiculo}"
                onclick="seleccionarVehiculoOrden(${v.idVehiculo}, '${v.placa}')">
                <i class="fa-solid fa-car" style="font-size:1.5rem; color:var(--accent-primary,#b6f24a); display:block; margin-bottom:.5rem;"></i>
                <div style="color:#fff; font-weight:700;">${v.placa}</div>
                <div style="color:var(--text-muted,#5f9c92); font-size:.78rem;">${v.marca || ''} ${v.modelo || ''}</div>
            </div>
        `).join('');
    } catch (e) {
        console.error('Error cargando vehículos:', e);
    }
}

function seleccionarVehiculoOrden(idVehiculo, placa) {
    rpVehiculoSeleccionado = { idVehiculo, placa };

    // Resaltar tarjeta seleccionada
    document.querySelectorAll('.rp-vehiculo-card').forEach(c => c.classList.remove('seleccionado'));
    document.getElementById(`rv-${idVehiculo}`)?.classList.add('seleccionado');

    // Actualizar stepper
    document.getElementById('rp-step-2').classList.remove('active');
    document.getElementById('rp-step-2').classList.add('done');
    document.getElementById('rp-step-3').classList.add('active');

    // Mostrar paso 3
    const paso3 = document.getElementById('rp-paso-3');
    if (paso3) {
        paso3.style.display = 'block';
        paso3.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function abrirModalNuevoVehiculo() {
    if (!rpClienteSeleccionado) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona un cliente primero');
        return;
    }

    const existingModal = document.getElementById('rp-modal-nuevo-vehiculo');
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.id = 'rp-modal-nuevo-vehiculo';
    modal.style.cssText = 'position:fixed; inset:0; z-index:1150; display:flex; align-items:center; justify-content:center; padding:1rem; background:rgba(0,0,0,.72); backdrop-filter:blur(5px);';
    modal.innerHTML = `
        <div style="width:min(100%, 520px); max-height:90vh; overflow-y:auto; background:#071613; border:1px solid rgba(182,242,74,.22); border-radius:18px; padding:1.5rem; font-family:'Outfit',sans-serif; box-shadow:0 24px 60px rgba(0,0,0,.45);">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.25rem;">
                <div>
                    <h3 style="color:#fff; font-size:1.2rem; font-weight:800; margin:0;">Registrar vehículo</h3>
                    <p style="color:var(--text-muted,#5f9c92); font-size:.82rem; margin:.3rem 0 0;">Cliente: ${escapeHtmlRecep(rpClienteSeleccionado.nombreCompleto)}</p>
                </div>
                <button type="button" id="rp-cerrar-vehiculo" class="op-btn-ghost" style="padding:.35rem .65rem;" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="rp-form-nuevo-vehiculo">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div style="grid-column:1/-1;">
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Modelo *</label>
                        <select id="rp-vehiculo-modelo" required style="width:100%; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                            <option value="">Cargando modelos...</option>
                        </select>
                    </div>
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Placa *</label>
                        <input id="rp-vehiculo-placa" required maxlength="15" placeholder="Ej: ABC123" style="width:100%; box-sizing:border-box; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none; text-transform:uppercase;">
                    </div>
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Año *</label>
                        <input id="rp-vehiculo-anio" type="number" required min="1900" max="${new Date().getFullYear() + 1}" placeholder="Ej: 2022" style="width:100%; box-sizing:border-box; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                    </div>
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Color</label>
                        <input id="rp-vehiculo-color" maxlength="30" placeholder="Ej: Blanco" style="width:100%; box-sizing:border-box; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                    </div>
                    <div>
                        <label style="color:var(--text-secondary,#9db8b0); font-size:.82rem; font-weight:600; display:block; margin-bottom:.4rem;">Kilometraje</label>
                        <input id="rp-vehiculo-kilometraje" type="number" min="0" value="0" style="width:100%; box-sizing:border-box; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#fff; border-radius:10px; padding:.7rem 1rem; font-family:'Outfit',sans-serif; font-size:.9rem; outline:none;">
                    </div>
                </div>
                <div id="rp-vehiculo-error" style="display:none; margin-top:1rem; color:#fca5a5; font-size:.82rem;"></div>
                <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:1.25rem;">
                    <button type="button" id="rp-cancelar-vehiculo" class="op-btn-ghost">Cancelar</button>
                    <button type="submit" class="op-btn-primary"><i class="fa-solid fa-car"></i> Registrar vehículo</button>
                </div>
            </form>
        </div>`;

    document.body.appendChild(modal);
    document.getElementById('rp-cerrar-vehiculo').addEventListener('click', () => modal.remove());
    document.getElementById('rp-cancelar-vehiculo').addEventListener('click', () => modal.remove());
    cargarModelosParaRecepcion();
    document.getElementById('rp-form-nuevo-vehiculo').addEventListener('submit', async event => {
        event.preventDefault();
        await guardarVehiculoRecepcion(modal);
    });
}

function escapeHtmlRecep(value) {
    return String(value ?? '').replace(/[&<>'"]/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    }[character]));
}

async function cargarModelosParaRecepcion() {
    const select = document.getElementById('rp-vehiculo-modelo');
    if (!select) return;

    try {
        const response = await apiFetch('/api/modelos-vehiculo');
        if (!response.ok) throw new Error('No se pudieron cargar los modelos.');
        const modelos = await response.json();
        select.innerHTML = '<option value="">Selecciona un modelo</option>' + modelos.map(modelo => {
            const marca = modelo.marca?.nombre ? `${modelo.marca.nombre} ` : '';
            return `<option value="${modelo.idModelo}">${escapeHtmlRecep(marca + modelo.nombre)}</option>`;
        }).join('');
    } catch (error) {
        select.innerHTML = '<option value="">No se pudieron cargar los modelos</option>';
        mostrarErrorVehiculoRecepcion(error.message);
    }
}

async function guardarVehiculoRecepcion(modal) {
    const payload = {
        idCliente: rpClienteSeleccionado.idCliente,
        idModelo: parseInt(document.getElementById('rp-vehiculo-modelo').value, 10),
        placa: document.getElementById('rp-vehiculo-placa').value.trim().toUpperCase(),
        anio: parseInt(document.getElementById('rp-vehiculo-anio').value, 10),
        color: document.getElementById('rp-vehiculo-color').value.trim() || null,
        kilometraje: parseInt(document.getElementById('rp-vehiculo-kilometraje').value || 0, 10)
    };

    try {
        const response = await apiFetch('/api/vehiculos', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!response.ok) {
            const validationMessage = data.errors ? Object.values(data.errors).flat().join(' ') : data.message;
            throw new Error(validationMessage || 'No se pudo registrar el vehículo.');
        }

        modal.remove();
        await cargarVehiculosDeCliente(rpClienteSeleccionado.idCliente);
        seleccionarVehiculoOrden(data.idVehiculo, payload.placa);
        if (typeof triggerToast === 'function') triggerToast('Vehículo registrado correctamente');
    } catch (error) {
        mostrarErrorVehiculoRecepcion(error.message);
    }
}

function mostrarErrorVehiculoRecepcion(message) {
    const errorElement = document.getElementById('rp-vehiculo-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    } else if (typeof triggerToast === 'function') {
        triggerToast(message);
    }
}

async function crearOrden() {
    if (!rpClienteSeleccionado) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona un cliente primero');
        return;
    }
    if (!rpVehiculoSeleccionado) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona un vehículo primero');
        return;
    }

    const servicio = document.getElementById('rp-orden-servicio').value.trim();
    if (!servicio) {
        if (typeof triggerToast === 'function') triggerToast('Describe el servicio solicitado');
        return;
    }

    const asignEspecifico = document.getElementById('rp-asign-especifico').checked;
    const idMecanico = asignEspecifico
        ? document.getElementById('rp-mecanicoSelect').value || null
        : null;

    const payload = {
        idCliente:          rpClienteSeleccionado.idCliente,
        idVehiculo:         rpVehiculoSeleccionado.idVehiculo,
        servicioSolicitado: servicio,
        sucursal:           document.getElementById('rp-orden-sucursal').value || null,
        horaFinEstimada:    document.getElementById('rp-orden-hora-fin').value || null,
        idMecanico:         idMecanico || undefined,
    };

    try {
        const res = await apiFetch('/api/ordenes-trabajo', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            if (typeof triggerToast === 'function') triggerToast('✅ Orden de trabajo creada con éxito');
            reiniciarWizard();
            switchRecepTab('dashboard');
            loadRecepDashboard();
        } else {
            const err = await res.json();
            let errorMessage = err.message || 'Verifica los datos';
            if (err.errors) {
                errorMessage = Object.values(err.errors).flat().join(' | ');
            }
            if (typeof triggerToast === 'function')
                triggerToast('Error: ' + errorMessage);
        }
    } catch (e) {
        console.error('Error creando orden:', e);
    }
}

function reiniciarWizard() {
    rpClienteSeleccionado = null;
    rpVehiculoSeleccionado = null;
    document.getElementById('rp-paso1-seleccionado').style.display = 'none';
    document.getElementById('rp-paso-2').style.display = 'none';
    document.getElementById('rp-paso-3').style.display = 'none';
    document.getElementById('rp-orden-search-cliente').value = '';
    document.getElementById('rp-paso1-resultados').innerHTML = '';
    document.getElementById('rp-orden-servicio').value = '';
    document.getElementById('rp-orden-sucursal').value = '';
    document.getElementById('rp-orden-hora-fin').value = '';
    document.getElementById('rp-asign-disponible').checked = true;
    document.getElementById('rp-mecanico-select-wrap').style.display = 'none';
    // Reset stepper
    ['rp-step-1','rp-step-2','rp-step-3'].forEach(id => {
        document.getElementById(id)?.classList.remove('active','done');
    });
    document.getElementById('rp-step-1')?.classList.add('active');
    // Reset hora inicio
    const ahora = new Date();
    ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
    document.getElementById('rp-orden-hora-inicio').value = ahora.toISOString().slice(0, 16);
}

// Desde Dashboard: cuando se hace click en "Recibir" en una cita
function recibirCita(idCita, idCliente, idVehiculo) {
    switchRecepTab('nueva-orden');
    if (idCliente) {
        // Pre-buscar y seleccionar el cliente
        setTimeout(async () => {
            try {
                const res = await apiFetch(`/api/clientes/${idCliente}`);
                if (res.ok) {
                    const data = await res.json();
                    await seleccionarClienteOrden(data.idCliente, data.nombreCompleto);
                    if (idVehiculo) {
                        setTimeout(() => seleccionarVehiculoOrden(idVehiculo, ''), 300);
                    }
                }
            } catch (e) { console.error(e); }
        }, 100);
    }
}

// Desde tab Clientes
function preseleccionarClienteOrden(idCliente, nombre) {
    switchRecepTab('nueva-orden');
    setTimeout(() => seleccionarClienteOrden(idCliente, nombre), 100);
}

// ─── CITAS (VISTA SEMANAL) ────────────────────────────────────────────────────
function cambiarSemana(delta) {
    semanaOffset += delta;
    loadCitasSemana();
}

async function loadCitasSemana() {
    const grid = document.getElementById('rp-semana-grid');
    if (!grid) return;

    // Calcular fechas de la semana
    const hoy = new Date();
    const inicioSemana = new Date(hoy);
    const diaSemana = hoy.getDay() === 0 ? 6 : hoy.getDay() - 1; // Lunes=0
    inicioSemana.setDate(hoy.getDate() - diaSemana + semanaOffset * 7);

    const dias = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    const fechasSemana = dias.map((_, i) => {
        const d = new Date(inicioSemana);
        d.setDate(inicioSemana.getDate() + i);
        return d;
    });

    // Label de la semana
    const label = document.getElementById('rp-semana-label');
    if (label) {
        const inicio = fechasSemana[0].toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
        const fin = fechasSemana[6].toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        label.textContent = `${inicio} – ${fin}`;
    }

    // Esqueleto vacío mientras carga
    const todayStr = hoy.toISOString().slice(0,10);
    grid.innerHTML = fechasSemana.map((fecha, i) => {
        const fechaStr = fecha.toISOString().slice(0,10);
        const esHoy = fechaStr === todayStr;
        return `
            <div class="rp-semana-col" id="rp-col-${fechaStr}">
                <div class="rp-semana-col-header ${esHoy ? 'hoy' : ''}">
                    <div class="dia">${dias[i]}</div>
                    <div class="num">${fecha.getDate()}</div>
                </div>
                <div id="rp-citas-${fechaStr}">
                    <div style="text-align:center; padding:1rem; color:var(--text-muted,#5f9c92); font-size:.75rem;">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    try {
        const res = await apiFetch('/api/citas');
        if (!res.ok) return;
        const todasCitas = await res.json();

        // Agrupar por fecha
        fechasSemana.forEach(fecha => {
            const fechaStr = fecha.toISOString().slice(0,10);
            const citasDia = todasCitas.filter(c => c.fecha && c.fecha.startsWith(fechaStr));
            const container = document.getElementById(`rp-citas-${fechaStr}`);
            if (!container) return;

            if (citasDia.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding:1rem; color:var(--text-muted,#5f9c92); font-size:.75rem; opacity:.5;">Sin citas</div>`;
                return;
            }

            container.innerHTML = citasDia.map(c => {
                const colores = {
                    'Pendiente':  { border: '#f59e0b', bg: 'rgba(245,158,11,0.08)' },
                    'Confirmada': { border: '#10b981', bg: 'rgba(16,185,129,0.08)' },
                    'Cancelada':  { border: '#ef4444', bg: 'rgba(239,68,68,0.08)' },
                    'Completada': { border: '#b6f24a', bg: 'rgba(182,242,74,0.08)' },
                };
                const col = colores[c.estado] || { border: '#9db8b0', bg: 'rgba(157,184,176,0.08)' };
                return `
                    <div class="rp-cita-bloque"
                        style="border-left-color:${col.border}; background:${col.bg}; color:#fff;"
                        onclick="editarCita(${c.idCita || c.id || 0})"
                        title="${c.motivo || c.estado}">
                        <div style="font-weight:700;">${c.hora ? c.hora.substring(0,5) : ''}</div>
                        <div style="font-size:.72rem; color:var(--text-secondary,#9db8b0); overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                            ${c.cliente ? c.cliente.nombreCompleto : '—'}
                        </div>
                    </div>
                `;
            }).join('');
        });
    } catch (e) {
        console.error('Error cargando citas de la semana:', e);
    }
}

// ─── MODAL CITA ───────────────────────────────────────────────────────────────
function abrirModalCita() {
    rpEditCitaId = null;
    document.getElementById('rp-modal-cita-titulo').textContent = 'Nueva Cita';
    document.getElementById('rp-cita-id').value = '';
    document.getElementById('rp-cita-buscar-cliente').value = '';
    document.getElementById('rp-cita-cliente-id').value = '';
    document.getElementById('rp-cita-cliente-resultados').innerHTML = '';
    document.getElementById('rp-cita-vehiculo').innerHTML = '<option value="">— Selecciona un cliente primero —</option>';
    document.getElementById('rp-cita-motivo').value = '';
    document.getElementById('rp-cita-estado').value = 'Pendiente';
    // Fecha y hora
    const hoy = new Date().toISOString().slice(0,10);
    document.getElementById('rp-cita-fecha').value = hoy;
    document.getElementById('rp-cita-hora').value = '09:00';
    document.getElementById('rp-modal-cita').style.display = 'flex';
}

async function editarCita(idCita) {
    try {
        const res = await apiFetch(`/api/citas/${idCita}`);
        if (!res.ok) return;
        const c = await res.json();

        rpEditCitaId = idCita;
        document.getElementById('rp-modal-cita-titulo').textContent = 'Editar Cita';
        document.getElementById('rp-cita-id').value = idCita;
        document.getElementById('rp-cita-buscar-cliente').value = c.cliente ? c.cliente.nombreCompleto : '';
        document.getElementById('rp-cita-cliente-id').value = c.idCliente || '';
        document.getElementById('rp-cita-fecha').value = c.fecha || '';
        document.getElementById('rp-cita-hora').value = c.hora ? c.hora.substring(0,5) : '';
        document.getElementById('rp-cita-motivo').value = c.motivo || '';
        document.getElementById('rp-cita-estado').value = c.estado || 'Pendiente';

        // Cargar vehículos del cliente
        if (c.idCliente) await cargarVehiculosCita(c.idCliente, c.idVehiculo);
        document.getElementById('rp-modal-cita').style.display = 'flex';
    } catch (e) {
        console.error('Error cargando cita:', e);
    }
}

async function buscarClienteCita(query) {
    const q = (query || '').trim();
    const container = document.getElementById('rp-cita-cliente-resultados');
    if (q.length < 2) { container.innerHTML = ''; return; }

    try {
        const res = await apiFetch('/api/clientes/buscar?q=' + encodeURIComponent(q));
        if (!res.ok) return;
        const clientes = await res.json();

        container.innerHTML = clientes.slice(0,5).map(c => `
            <div onclick="seleccionarClienteCita(${c.idCliente}, '${c.nombreCompleto.replace(/'/g, "\\'")}')"
                style="padding:.5rem .75rem; border-radius:6px; cursor:pointer;
                    background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
                    margin-bottom:.3rem; color:#fff; font-size:.85rem; transition:background .15s;"
                onmouseover="this.style.background='rgba(182,242,74,0.08)'"
                onmouseout="this.style.background='rgba(255,255,255,0.04)'">
                <strong>${c.nombreCompleto}</strong> <span style="color:var(--text-muted,#5f9c92); font-size:.75rem;">${c.ci_nit}</span>
            </div>
        `).join('');
    } catch (e) {
        console.error(e);
    }
}

async function seleccionarClienteCita(idCliente, nombre) {
    document.getElementById('rp-cita-buscar-cliente').value = nombre;
    document.getElementById('rp-cita-cliente-id').value = idCliente;
    document.getElementById('rp-cita-cliente-resultados').innerHTML = '';
    await cargarVehiculosCita(idCliente, null);
}

async function cargarVehiculosCita(idCliente, selectedId) {
    try {
        const res = await apiFetch(`/api/clientes/${idCliente}`);
        if (!res.ok) return;
        const data = await res.json();
        const sel = document.getElementById('rp-cita-vehiculo');
        sel.innerHTML = '<option value="">— Selecciona vehículo —</option>' +
            (data.vehiculos || []).map(v =>
                `<option value="${v.idVehiculo}" ${v.idVehiculo == selectedId ? 'selected' : ''}>${v.placa}</option>`
            ).join('');
    } catch (e) { console.error(e); }
}

async function guardarCita() {
    const idCliente = document.getElementById('rp-cita-cliente-id').value;
    const idVehiculo = document.getElementById('rp-cita-vehiculo').value;
    const fecha = document.getElementById('rp-cita-fecha').value;
    const hora = document.getElementById('rp-cita-hora').value;
    
    if (!idCliente) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona un cliente para la cita');
        return;
    }
    if (!idVehiculo) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona un vehículo (si el cliente no tiene, ve a Nueva Orden para registrar uno o agrégalo desde el panel)');
        return;
    }
    if (!fecha) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona la fecha de la cita');
        return;
    }
    if (!hora) {
        if (typeof triggerToast === 'function') triggerToast('Selecciona la hora de la cita');
        return;
    }

    const payload = {
        idCliente: parseInt(idCliente),
        idVehiculo: parseInt(idVehiculo),
        fecha,
        hora,
        motivo: document.getElementById('rp-cita-motivo').value || null,
        estado: document.getElementById('rp-cita-estado').value
    };

    try {
        const id = document.getElementById('rp-cita-id').value;
        const url = id ? `/api/citas/${id}` : '/api/citas';
        const method = id ? 'PUT' : 'POST';
        const res = await apiFetch(url, { method, body: JSON.stringify(payload) });

        if (res.ok) {
            if (typeof triggerToast === 'function') triggerToast('Cita guardada con éxito');
            cerrarModalCita();
            loadCitasSemana();
            loadRecepDashboard();
        } else {
            const err = await res.json();
            if (typeof triggerToast === 'function') triggerToast('Error: ' + (err.message || 'Verifica los datos'));
        }
    } catch (e) {
        console.error('Error guardando cita:', e);
    }
}

function cerrarModalCita() {
    document.getElementById('rp-modal-cita').style.display = 'none';
    rpEditCitaId = null;
}

// ─── SERVICIOS ────────────────────────────────────────────────────────────────
let _rpServicios = [], _rpTipos = [];

async function loadServicios() {
    if (_rpServicios.length > 0) { renderServiciosGrid(_rpServicios); return; }

    try {
        const [sRes, tRes] = await Promise.all([
            apiFetch('/api/servicios'),
            apiFetch('/api/tipos-servicio')
        ]);
        _rpServicios = sRes.ok ? await sRes.json() : [];
        _rpTipos = tRes.ok ? await tRes.json() : [];

        // Pills de tipo
        const pillsContainer = document.getElementById('rp-tipo-pills');
        if (pillsContainer) {
            pillsContainer.innerHTML =
                `<button class="rp-chip-filtro active" data-tipo="todos" onclick="filtrarServiciosTipo(this, 'todos')">Todos</button>` +
                _rpTipos.map(t =>
                    `<button class="rp-chip-filtro" data-tipo="${t.idTipoServicio}" onclick="filtrarServiciosTipo(this, ${t.idTipoServicio})">${t.nombre}</button>`
                ).join('');
        }

        renderServiciosGrid(_rpServicios);
    } catch (e) {
        console.error('Error cargando servicios:', e);
    }
}

function filtrarServiciosTipo(btn, tipo) {
    document.querySelectorAll('#rp-tipo-pills .rp-chip-filtro').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filtrados = tipo === 'todos' ? _rpServicios :
        _rpServicios.filter(s => s.idTipoServicio == tipo);
    renderServiciosGrid(filtrados);
}

function renderServiciosGrid(servicios) {
    const grid = document.getElementById('rp-servicios-grid');
    if (!grid) return;

    if (servicios.length === 0) {
        grid.innerHTML = `<div style="color:var(--text-muted,#5f9c92); text-align:center; padding:2rem; grid-column:1/-1;">Sin servicios en esta categoría.</div>`;
        return;
    }

    grid.innerHTML = servicios.map(s => {
        const tipo = _rpTipos.find(t => t.idTipoServicio === s.idTipoServicio);
        const precio = parseFloat(s.precioBase || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 });
        return `
            <div class="glass-card" style="display:flex; flex-direction:column; gap:.75rem;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <span style="background:rgba(182,242,74,0.12); color:var(--accent-primary,#b6f24a);
                        border-radius:99px; padding:.25rem .7rem; font-size:.72rem; font-weight:700;">
                        ${tipo ? tipo.nombre : 'Servicio'}
                    </span>
                </div>
                <h4 style="color:#fff; font-weight:700; margin:0; font-size:1rem;">${s.nombre}</h4>
                <p style="color:var(--text-muted,#5f9c92); font-size:.82rem; margin:0; flex:1;">
                    ${s.descripcion || 'Servicio automotriz profesional'}
                </p>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:.25rem;">
                    <div style="color:var(--accent-primary,#b6f24a); font-weight:800; font-size:1.1rem;">Bs. ${precio}</div>
                    <div style="color:var(--text-muted,#5f9c92); font-size:.78rem;">
                        <i class="fa-regular fa-clock"></i>
                        ${s.duracionEstimada ? s.duracionEstimada + ' min' : 'Consultar'}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// ─── NOTIFICACIONES ───────────────────────────────────────────────────────────
async function loadNotificaciones() {
    try {
        const res = await apiFetch('/api/notificaciones/no-leidas');
        if (!res.ok) return;
        _rpNotificaciones = await res.json();
        renderNotifTimeline(_rpNotificaciones);
    } catch (e) {
        console.error('Error cargando notificaciones:', e);
    }
}

function filtrarNotif(btn) {
    document.querySelectorAll('.rp-chip-filtro').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filtro = btn.dataset.filtro;

    let lista = _rpNotificaciones;
    if (filtro === 'cita') lista = _rpNotificaciones.filter(n => n.mensaje && n.mensaje.toLowerCase().includes('cita'));
    else if (filtro === 'listo') lista = _rpNotificaciones.filter(n => n.mensaje && n.mensaje.toLowerCase().includes('listo'));
    else if (filtro === 'otro') lista = _rpNotificaciones.filter(n => {
        const m = (n.mensaje || '').toLowerCase();
        return !m.includes('cita') && !m.includes('listo');
    });

    renderNotifTimeline(lista);
}

function renderNotifTimeline(notifs) {
    const timeline = document.getElementById('rp-notif-timeline');
    if (!timeline) return;

    if (notifs.length === 0) {
        timeline.innerHTML = `<div style="text-align:center; padding:2.5rem; color:var(--text-muted,#5f9c92);">
            <i class="fa-regular fa-bell" style="font-size:2rem; display:block; margin-bottom:1rem; opacity:.4;"></i>
            Sin notificaciones pendientes
        </div>`;
        return;
    }

    timeline.innerHTML = `<div class="op-timeline">` +
        notifs.map(n => {
            const esListo = n.mensaje && n.mensaje.toLowerCase().includes('listo');
            return `
                <div class="op-timeline-item" style="${esListo ? 'padding:0.75rem; background:rgba(16,185,129,0.06); border-radius:10px; border-left:3px solid #10b981;' : ''}">
                    <div class="op-timeline-dot" style="${esListo ? 'background:var(--state-success,#10b981); box-shadow:0 0 8px rgba(16,185,129,0.5);' : ''}"></div>
                    <div class="op-timeline-date">${n.fechaEnvio || n.fecha || '—'}</div>
                    <div class="op-timeline-msg">${n.mensaje || '—'}</div>
                </div>
            `;
        }).join('') + `</div>`;
}

// ─── MODAL NOTIFICACIÓN MANUAL ────────────────────────────────────────────────
function abrirModalNotifManual() {
    document.getElementById('rp-modal-notif').style.display = 'flex';
    document.getElementById('rp-notif-destinatario').value = '';
    document.getElementById('rp-notif-usuario-id').value = '';
    document.getElementById('rp-notif-mensaje').value = '';
}

function cerrarModalNotif() {
    document.getElementById('rp-modal-notif').style.display = 'none';
}

async function enviarNotifManual() {
    const idUsuario = document.getElementById('rp-notif-usuario-id').value;
    const mensaje = document.getElementById('rp-notif-mensaje').value.trim();
    if (!idUsuario || !mensaje) {
        if (typeof triggerToast === 'function') triggerToast('Completa el destinatario y el mensaje');
        return;
    }

    try {
        const res = await apiFetch('/api/notificaciones', {
            method: 'POST',
            body: JSON.stringify({ idUsuario, mensaje })
        });
        if (res.ok) {
            if (typeof triggerToast === 'function') triggerToast('Notificación enviada');
            cerrarModalNotif();
        }
    } catch (e) {
        console.error('Error enviando notificación:', e);
    }
}
