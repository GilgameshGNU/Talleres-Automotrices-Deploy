// public/js/mecanico.js

let mpCurrentOrdenId = null;
let mpCurrentOrdenData = null;
let mpAsignadas = [];
let mpDisponibles = [];

function initMecanicoPortal() {
    const portal = document.getElementById('mecanicoPortal');
    if (!portal) return;

    const name = window.currentUser?.nombreUsuario || 'Mecánico';
    document.getElementById('mp-username').textContent = name;
    document.getElementById('mp-avatar').textContent = name.charAt(0).toUpperCase();

    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateLabel = document.getElementById('mp-date-label');
    if (dateLabel) dateLabel.textContent = new Date().toLocaleDateString('es-ES', options);

    const toggle = document.getElementById('mp-toggle-disp');
    if (toggle) {
        toggle.checked = false;
        document.getElementById('mp-toggle-state').textContent = 'Ocupado';
    }

    buildDiagnosticoChips();
    switchMecTab('bandeja');
    loadBandeja();
}

function switchMecTab(tab, idOrden = null) {
    document.querySelectorAll('.mp-tab-content').forEach(item => item.style.display = 'none');
    document.getElementById(`mp-tab-${tab}`).style.display = 'block';

    document.querySelectorAll('.op-nav-btn').forEach(btn => btn.classList.remove('active'));
    const activeButton = document.getElementById(`mp-nav-${tab}`);
    if (activeButton) activeButton.classList.add('active');

    if (tab === 'workspace' && idOrden) {
        document.getElementById('mp-nav-workspace').style.display = 'inline-flex';
        document.getElementById('mp-ws-tab-title').textContent = `Orden #${idOrden}`;
        loadOrdenDetalle(idOrden);
    } else if (tab === 'bandeja') {
        document.getElementById('mp-nav-workspace').style.display = 'none';
        loadBandeja();
    }
}

async function loadBandeja() {
    try {
        const [dispRes, asigRes] = await Promise.all([
            apiFetch('/api/ordenes-trabajo?disponibles=1'),
            apiFetch('/api/ordenes-trabajo?mis=1')
        ]);

        const disponibles = dispRes.ok ? extractArrayPayload(await dispRes.json()) : [];
        const asignadas = asigRes.ok ? extractArrayPayload(await asigRes.json()) : [];

        mpDisponibles = normalizeDisponibles(disponibles);
        mpAsignadas = normalizeAsignadas(asignadas, disponibles);

        document.getElementById('mp-kpi-disponibles').textContent = mpDisponibles.length;
        document.getElementById('mp-kpi-asignadas').textContent = mpAsignadas.length;
        document.getElementById('mp-kpi-diagnosticos').textContent = mpAsignadas.filter(item => item.estado === 'Diagnóstico' || item.estado === 'En reparación').length;
        document.getElementById('mp-count-disponibles').textContent = mpDisponibles.length;
        document.getElementById('mp-count-asignadas').textContent = mpAsignadas.length;

        renderDisponibles(mpDisponibles);
        renderAsignadas(mpAsignadas);
    } catch (error) {
        console.error('Error loading bandeja:', error);
        showBanner('No se pudo cargar la bandeja de órdenes.', 'danger');
    }
}

function renderDisponibles(ordenes) {
    const container = document.getElementById('mp-lista-disponibles');
    if (!container) return;

    if (!ordenes.length) {
        container.innerHTML = '<div class="mp-empty-state">No hay órdenes disponibles para aceptar en este momento.</div>';
        return;
    }

    container.innerHTML = ordenes.map(renderOrdenCard('disponibles')).join('');
}

function renderAsignadas(ordenes) {
    const container = document.getElementById('mp-lista-asignadas');
    if (!container) return;

    if (!ordenes.length) {
        container.innerHTML = '<div class="mp-empty-state">Aún no tienes órdenes asignadas.</div>';
        return;
    }

    const cards = ordenes.map((orden, index) => renderOrdenCard('asignadas')(orden, index));
    container.innerHTML = cards.join('');
}

function renderOrdenCard(tipo) {
    return function (orden, index) {
        const id = orden.idOrden || orden.id;
        const placa = orden.vehiculo?.placa || orden.placa || '-';
        const cliente = orden.cliente?.nombreCompleto || orden.cliente?.nombre || orden.nombreCliente || '-';
        const servicio = orden.servicioSolicitado || orden.servicio || orden.descripcion || 'Sin servicio registrado';
        const estado = orden.estado || orden.etapa || 'Recibido';
        const sucursal = orden.sucursal?.nombre || orden.sucursal || 'Sucursal';
        const urgente = tipo === 'asignadas' && index === 0;
        const action = tipo === 'disponibles'
            ? `<button class="op-btn-primary" onclick="aceptarOrden(${id})"><i class="fa-solid fa-check"></i> Aceptar</button>`
            : `<button class="op-btn-primary" onclick="switchMecTab('workspace', ${id})"><i class="fa-solid fa-eye"></i> Abrir Workspace</button>`;

        return `
            <div class="glass-card mp-order-card${urgente ? ' glow-lime' : ''}">
                <div class="mp-order-head">
                    <div>
                        <div class="mp-order-title">#${id} · ${placa}</div>
                        <div class="mp-order-subtitle">${cliente}</div>
                    </div>
                    <span class="status-badge" data-status="${estado}">${estado}</span>
                </div>
                <div class="mp-order-meta">
                    <div><i class="fa-solid fa-wrench"></i> ${servicio}</div>
                    <div><i class="fa-solid fa-location-dot"></i> ${sucursal}</div>
                    <div><i class="fa-regular fa-clock"></i> ${orden.horaEntrega || 'Sin hora estimada'}</div>
                </div>
                <div class="mp-card-actions">${action}</div>
            </div>
        `;
    };
}

async function aceptarOrden(idOrden) {
    try {
        const res = await apiFetch(`/api/ordenes-trabajo/${idOrden}/aceptar`, {
            method: 'POST',
            body: JSON.stringify({})
        });
        if (res.ok) {
            showBanner('✅ Orden aceptada correctamente.', 'success');
            await loadBandeja();
            switchMecTab('workspace', idOrden);
        } else {
            showBanner('No se pudo aceptar la orden.', 'danger');
        }
    } catch (error) {
        console.error('Error aceptando orden:', error);
        showBanner('Hubo un error al aceptar la orden.', 'danger');
    }
}

async function loadOrdenDetalle(idOrden) {
    try {
        const res = await apiFetch(`/api/ordenes-trabajo/${idOrden}`);
        if (!res.ok) {
            showBanner('No se pudo cargar la orden seleccionada.', 'danger');
            return;
        }

        const data = await res.json();
        mpCurrentOrdenId = idOrden;
        mpCurrentOrdenData = data;

        const title = document.getElementById('mp-orden-title');
        const subtitle = document.getElementById('mp-orden-subtitle');
        const meta = document.getElementById('mp-orden-meta');
        if (title) title.textContent = `Orden #${idOrden}`;
        if (subtitle) subtitle.textContent = `${data.cliente?.nombreCompleto || '-'} · ${data.vehiculo?.placa || '-'}`;
        if (meta) {
            const modeloNombre = data.vehiculo?.modelo_nombre || (typeof data.vehiculo?.modelo === 'string' ? data.vehiculo?.modelo : data.vehiculo?.modelo?.nombre) || '';
            meta.innerHTML = `
                <div><i class="fa-solid fa-car"></i> ${data.vehiculo?.marca || ''} ${modeloNombre}</div>
                <div><i class="fa-solid fa-user"></i> ${data.cliente?.nombreCompleto || '-'}</div>
                <div><i class="fa-regular fa-clock"></i> Creada: ${data.fechaIngreso || 'Sin fecha registrada'}</div>
            `;
        }

        const etapaActual = data.etapa || data.estado || 'Recibido';
        renderStepper(etapaActual);
        
        await loadDiagnostico(idOrden);
        document.getElementById('mp-rep-resultados').innerHTML = '<div class="mp-empty-state" style="padding:1rem;">Busca un repuesto para añadirlo a esta orden.</div>';
        document.getElementById('mp-reg-uso-form').style.display = 'none';

    } catch (error) {
        console.error('Error loading orden detalle:', error);
    }
}

function renderStepper(etapaActual) {
    const currentIndex = getStepIndex(etapaActual);
    const avanceButton = document.getElementById('mp-btn-avanzar');
    const btnText = document.getElementById('mp-btn-avanzar-text');
    if (avanceButton && btnText) {
        avanceButton.disabled = currentIndex >= 3;
        if (currentIndex === 0) btnText.textContent = 'Iniciar Diagnóstico';
        else if (currentIndex === 1) btnText.textContent = 'Iniciar Reparación';
        else if (currentIndex === 2) btnText.textContent = 'Terminar Reparación';
        else btnText.textContent = 'Orden Finalizada';
    }

    const btnFinalizar = document.getElementById('mp-btn-finalizar');
    if (btnFinalizar) {
        btnFinalizar.style.display = currentIndex >= 3 ? 'none' : 'inline-flex';
    }
}

function getStepIndex(etapa) {
    const value = String(etapa || 'Recibido').toLowerCase();
    if (value.includes('termin')) return 3;
    if (value.includes('repar')) return 2;
    if (value.includes('diag')) return 1;
    return 0;
}

async function avanzarEtapa() {
    const idOrden = mpCurrentOrdenId;
    if (!idOrden) return;

    const etapaActual = mpCurrentOrdenData?.etapa || mpCurrentOrdenData?.estado || 'Recibido';
    const nextEtapa = nextStep(etapaActual);

    if (!nextEtapa) {
        showBanner('La orden ya está finalizada.', 'success');
        return;
    }

    if (nextEtapa === 'Terminado') {
        abrirModalTerminado();
        return;
    }

    try {
        const res = await apiFetch(`/api/ordenes-trabajo/${idOrden}/estado`, {
            method: 'PATCH',
            body: JSON.stringify({ etapa: nextEtapa })
        });
        if (res.ok) {
            showBanner(`✅ Etapa actualizada a ${nextEtapa}.`, 'success');
            await loadOrdenDetalle(idOrden);
        } else {
            showBanner('No se pudo avanzar la etapa.', 'danger');
        }
    } catch (error) {
        console.error('Error avanzando etapa:', error);
    }
}

function nextStep(etapa) {
    const index = getStepIndex(etapa);
    const steps = ['Recibido', 'Diagnóstico', 'En reparación', 'Terminado'];
    return steps[index + 1] || null;
}

function abrirModalTerminado() {
    const modal = document.getElementById('mp-modal-confirmar-terminado');
    if (modal) modal.style.display = 'flex';
}

function cerrarModalTerminado() {
    const modal = document.getElementById('mp-modal-confirmar-terminado');
    if (modal) modal.style.display = 'none';
}

async function confirmarTerminado() {
    const idOrden = mpCurrentOrdenId;
    if (!idOrden) return;

    try {
        const res = await apiFetch(`/api/ordenes-trabajo/${idOrden}/estado`, {
            method: 'PATCH',
            body: JSON.stringify({ etapa: 'Terminado' })
        });
        if (res.ok) {
            cerrarModalTerminado();
            showBanner('✅ Vehículo marcado como listo. Notificación enviada.', 'success');
            await loadOrdenDetalle(idOrden);
        } else {
            showBanner('No se pudo marcar la orden como terminada.', 'danger');
        }
    } catch (error) {
        console.error('Error confirmando terminado:', error);
    }
}

async function toggleDisponible() {
    const toggle = document.getElementById('mp-toggle-disp');
    const stateLabel = document.getElementById('mp-toggle-state');
    if (!toggle) return;

    try {
        const res = await apiFetch('/api/mecanicos/mi-disponibilidad', {
            method: 'PATCH',
            body: JSON.stringify({})
        });
        if (res.ok) {
            const data = await res.json();
            const disponible = Boolean(data.disponible ?? toggle.checked);
            toggle.checked = disponible;
            if (stateLabel) stateLabel.textContent = disponible ? 'Disponible' : 'Ocupado';
            showBanner(disponible ? '✅ Estás disponible para recibir órdenes.' : '🛠 Estás ocupado por ahora.', 'success');
        } else {
            toggle.checked = !toggle.checked;
            showBanner('No se pudo actualizar la disponibilidad.', 'danger');
        }
    } catch (error) {
        console.error('Error cambiando disponibilidad:', error);
        toggle.checked = !toggle.checked;
    }
}

function buildDiagnosticoChips() {
    const chipsContainer = document.getElementById('mp-diag-chips');
    if (!chipsContainer) return;

    const opciones = ['Frenos', 'Motor', 'Eléctrico', 'Suspensión', 'Transmisión', 'A/C', 'Carrocería', 'Escape'];
    chipsContainer.innerHTML = opciones.map(opcion => `
        <button type="button" class="mp-chip" data-value="${opcion}" onclick="toggleChip('${opcion}', this)">${opcion}</button>
    `).join('');
}

function toggleChip(value, element) {
    element.classList.toggle('active');
}

async function loadDiagnostico(idOrden) {
    try {
        document.getElementById('mp-diag-desc').value = '';
        document.querySelectorAll('.mp-chip').forEach(c => c.classList.remove('active'));
        document.querySelector('input[name="mp-severity"][value="Media"]').checked = true;

        const res = await apiFetch(`/api/diagnosticos?idOrden=${idOrden}`);
        if (!res.ok) return;

        const data = await res.json();
        const diagnostico = Array.isArray(data) ? data[0] : data;
        if (!diagnostico) return;

        const textArea = document.getElementById('mp-diag-desc');
        if (textArea) textArea.value = diagnostico.descripcion || diagnostico.detalle || diagnostico.observaciones || '';

        const especialidades = (diagnostico.especialidades || diagnostico.especialidad || '').split(',').map(item => item.trim()).filter(Boolean);
        document.querySelectorAll('.mp-chip').forEach(chip => {
            chip.classList.toggle('active', especialidades.includes(chip.dataset.value));
        });

        const severityInput = document.querySelector(`input[name="mp-severity"][value="${diagnostico.severidad || 'Media'}"]`);
        if (severityInput) severityInput.checked = true;
    } catch (error) {
        console.error('Error loading diagnóstico:', error);
    }
}

async function guardarDiagnostico() {
    const idOrden = mpCurrentOrdenId;
    if (!idOrden) return;
    const descripcion = document.getElementById('mp-diag-desc')?.value || '';
    const especialidades = Array.from(document.querySelectorAll('.mp-chip.active')).map(chip => chip.dataset.value);
    const severidad = document.querySelector('input[name="mp-severity"]:checked')?.value || 'Media';

    try {
        const res = await apiFetch('/api/diagnosticos', {
            method: 'POST',
            body: JSON.stringify({ idOrden, descripcion, especialidades, severidad })
        });
        if (res.ok) {
            showBanner('✅ Diagnóstico guardado correctamente.', 'success');
        } else {
            showBanner('No se pudo guardar el diagnóstico.', 'danger');
        }
    } catch (error) {
        console.error('Error guardando diagnóstico:', error);
    }
}

async function loadRepuestos(query = '') {
    if (!query.trim()) {
        document.getElementById('mp-rep-resultados').innerHTML = '<div class="mp-empty-state" style="padding:1rem;">Escribe algo para buscar repuestos.</div>';
        return;
    }
    
    try {
        const res = await apiFetch(`/api/repuestos?q=${encodeURIComponent(query)}`);
        if (!res.ok) return;

        const data = await res.json();
        const repuestos = extractArrayPayload(data);
        const container = document.getElementById('mp-rep-resultados');
        if (!container) return;

        if (!repuestos.length) {
            container.innerHTML = '<div class="mp-empty-state">No hay repuestos disponibles con esa búsqueda.</div>';
            return;
        }

        container.innerHTML = repuestos.map(repuesto => {
            const stock = repuesto.stock ?? repuesto.cantidad ?? repuesto.stockActual ?? 0;
            const estado = stock <= 0 ? 'Agotado' : stock <= 5 ? 'Bajo Stock' : 'En Stock';
            const precio = repuesto.precioUnitario ?? repuesto.precio ?? repuesto.precio_unitario ?? 0;
            const nombre = JSON.stringify(repuesto.nombre || repuesto.descripcion || 'Repuesto').replace(/'/g, "&apos;");
            const id = repuesto.idRepuesto || repuesto.id;
            
            return `
                <div class="glass-card mp-order-card" style="margin-bottom:.75rem; padding:1rem;">
                    <div class="mp-order-head" style="margin-bottom:.5rem;">
                        <div>
                            <div class="mp-order-title">${repuesto.nombre || repuesto.descripcion || 'Repuesto'}</div>
                            <div class="mp-order-subtitle">Código: ${repuesto.codigo || id || '-'}</div>
                        </div>
                        <span class="status-badge" data-status="${estado}">${estado}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div class="mp-order-meta" style="gap:.75rem; margin-bottom:0;">
                            <div><i class="fa-solid fa-dollar-sign"></i> ${Number(precio).toLocaleString('es-ES')}</div>
                            <div><i class="fa-solid fa-boxes-stacked"></i> Stock: ${stock}</div>
                        </div>
                        <button class="op-btn-primary" onclick='abrirFormUso(${id}, ${nombre})' style="padding:.4rem .8rem; font-size:.85rem;">
                            <i class="fa-solid fa-plus"></i> Usar
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading repuestos:', error);
    }
}

function abrirFormUso(idRepuesto, nombre) {
    const form = document.getElementById('mp-reg-uso-form');
    const input = document.getElementById('mp-uso-repuesto');
    if (form) form.style.display = 'block';
    if (input) input.value = `${nombre} (${idRepuesto})`;
    document.getElementById('mp-uso-cantidad').value = 1;
}

async function confirmarUsoRepuesto() {
    const val = document.getElementById('mp-uso-repuesto').value;
    const match = val.match(/\((\d+)\)/);
    const idRepuesto = match ? match[1] : null;
    const cantidad = document.getElementById('mp-uso-cantidad').value;
    const idOrden = mpCurrentOrdenId;

    if (!idRepuesto || !idOrden) {
        showBanner('Datos inválidos para registrar uso.', 'danger');
        return;
    }

    try {
        const res = await apiFetch('/api/movimientos-inventario', {
            method: 'POST',
            body: JSON.stringify({ idRepuesto, cantidad, idOrden, tipo: 'uso' })
        });
        if (res.ok) {
            showBanner('✅ Uso de repuesto registrado en esta orden.', 'success');
            document.getElementById('mp-reg-uso-form').style.display = 'none';
            document.getElementById('mp-uso-cantidad').value = 1;
            loadRepuestos(document.getElementById('mp-rep-search')?.value || '');
        } else {
            showBanner('No se pudo registrar el uso del repuesto.', 'danger');
        }
    } catch (error) {
        console.error('Error registrando uso de repuesto:', error);
    }
}

function showBanner(message, type = 'success') {
    const banner = document.getElementById('mp-banner');
    if (!banner) return;
    banner.className = `mp-banner ${type}`;
    banner.innerHTML = `<i class="fa-solid ${type === 'danger' ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i> ${message}`;
    banner.style.display = 'block';
    clearTimeout(showBanner.timeout);
    showBanner.timeout = setTimeout(() => {
        banner.style.display = 'none';
    }, 3200);
}

function extractArrayPayload(data) {
    if (Array.isArray(data)) return data;
    if (data && Array.isArray(data.data)) return data.data;
    if (data && Array.isArray(data.ordenes)) return data.ordenes;
    if (data && Array.isArray(data.result)) return data.result;
    return [];
}

function normalizeDisponibles(ordenes) {
    return ordenes.filter(orden => {
        const estado = String(orden.estado || orden.etapa || '').toLowerCase();
        return !orden.idMecanico && (estado === 'disponible' || estado === 'recibido' || estado === 'pendiente' || !estado);
    });
}

function normalizeAsignadas(ordenes, disponibles = []) {
    const currentUserId = window.currentUser?.idUsuario || window.currentUser?.id || null;
    const all = ordenes.length ? ordenes : disponibles;

    return all.filter(orden => {
        const sameUser = currentUserId && [orden.idMecanico, orden.mecanico?.idUsuario, orden.mecanico?.id].includes(currentUserId);
        const hasProgress = ['Diagnóstico', 'En reparación', 'Terminado', 'En Progreso'].includes(orden.estado || orden.etapa || '');
        return sameUser || hasProgress || Boolean(orden.idMecanico);
    });
}
