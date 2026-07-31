// ================================================================
// client.js - Portal del Cliente (L-gica Web 3D & Responsive)
// ================================================================

const cpHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
};

const cpFetchOptions = {
    headers: cpHeaders,
    credentials: 'same-origin'
};

let cp_userProfile = null;

async function initClientPortal() {
    if (window._cpInitialized) return;
    window._cpInitialized = true;

    await Promise.all([
        cp_loadProfile(),
        cp_loadEstadoVehiculo(),
        cp_loadNotificaciones()
    ]);

    cp_initTiltEffect();
    switchClientTab('inicio');
}

function cp_initTiltEffect() {
    setTimeout(() => {
        const cards = document.querySelectorAll('.card-3d');
        cards.forEach(card => {
            if (card.dataset.tiltBound) return;
            card.dataset.tiltBound = 'true';

            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = ((y - centerY) / centerY) * -8;
                const rotateY = ((x - centerX) / centerX) * 8;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) scale(1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px) scale(1)';
            });
        });
    }, 100);
}

function cp_setBellBadge(count) {
    const badge = document.getElementById('cp-nav-badge-notificaciones');
    const dot = document.getElementById('cp-notif-dot');
    const countText = count > 0 ? String(count) : '0';

    if (badge) badge.textContent = countText;
    if (dot) dot.style.display = count > 0 ? 'inline-block' : 'none';
}

function cp_formatFecha(value) {
    if (!value) return 'Por confirmar';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString('es-ES', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function cp_loadProfile() {
    try {
        const res = await fetch('/api/me', cpFetchOptions);
        if (!res.ok) return;
        const user = await res.json();

        const profileRes = await fetch('/api/client/profile', cpFetchOptions);
        const profileData = profileRes.ok ? await profileRes.json() : null;

        cp_userProfile = {
            ...user,
            ...(profileData?.cliente || {}),
            ...(profileData?.usuario || {})
        };

        const nombre = cp_userProfile.nombreCompleto || user.nombreUsuario || 'Cliente';
        const inicial = nombre.charAt(0).toUpperCase();
        const rol = user.rol?.nombre || 'Cliente';
        const telefono = cp_userProfile.telefono || 'No registrado';
        const direccion = cp_userProfile.direccion || 'No registrada';
        const ci = cp_userProfile.ci_nit || 'No registrado';
        const sinceText = user.created_at ? 'Cliente desde ' + new Date(user.created_at).toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }) : 'Cliente registrado';

        const navName = document.getElementById('cp-nav-name');
        if (navName) navName.textContent = nombre;

        const avatarInitial = document.getElementById('cp-avatar-initial');
        if (avatarInitial) avatarInitial.textContent = inicial;

        const heroName = document.getElementById('cp-hero-name');
        if (heroName) heroName.textContent = nombre;

        const profileAvatar = document.getElementById('cp-profile-avatar-large');
        if (profileAvatar) profileAvatar.textContent = inicial;

        const profileName = document.getElementById('cp-profile-name-large');
        if (profileName) profileName.textContent = nombre;

        const profileCompany = document.getElementById('cp-profile-company');
        if (profileCompany) profileCompany.textContent = 'Cliente Taller Automotriz';

        const profileSince = document.getElementById('cp-profile-since-large');
        if (profileSince) profileSince.textContent = sinceText;

        const infoNombre = document.getElementById('cp-info-nombre');
        if (infoNombre) infoNombre.textContent = nombre;

        const infoCi = document.getElementById('cp-info-ci');
        if (infoCi) infoCi.textContent = ci;

        const infoEmail = document.getElementById('cp-info-email');
        if (infoEmail) infoEmail.textContent = user.email || '-';

        const infoTelefono = document.getElementById('cp-info-telefono');
        if (infoTelefono) infoTelefono.textContent = telefono;

        const infoDireccion = document.getElementById('cp-info-direccion');
        if (infoDireccion) infoDireccion.textContent = direccion;

        const infoRol = document.getElementById('cp-info-rol');
        if (infoRol) infoRol.textContent = rol;


        cp_loadPermisos(user.rol?.idRol);
    } catch (e) {
        console.error('cp_loadProfile error:', e);
    }
}

async function cp_loadPermisos(idRol) {
    const container = document.getElementById('cp-permisos-container');
    if (!container) return;

    if (!idRol) {
        container.innerHTML = '<p style="color:#9db8b0; font-size:0.85rem;">Sin permisos asignados.</p>';
        return;
    }

    try {
        const res = await fetch('/api/permisos', cpFetchOptions);
        if (!res.ok) {
            container.innerHTML = '<p style="color:#9db8b0; font-size:0.85rem;">No se pudieron cargar los permisos.</p>';
            return;
        }

        const todos = await res.json();
        const misPermisos = todos.filter(p => p.idRol == idRol);

        if (misPermisos.length === 0) {
            container.innerHTML = '<p style="color:#9db8b0; font-size:0.85rem;">No hay permisos asociados a tu rol.</p>';
            return;
        }

        container.innerHTML = misPermisos.map(p => `
            <span class="cp-permiso-chip">
                <i class="fa-solid fa-circle-check"></i>
                ${p.nombre} <span style="font-size:0.72rem; opacity:0.75;">(${p.modulo})</span>
            </span>
        `).join('');
    } catch (e) {
        container.innerHTML = '<p style="color:#9db8b0; font-size:0.85rem;">Error al cargar los permisos.</p>';
    }
}

async function cp_loadEstadoVehiculo() {
    const container = document.getElementById('cp-estado-vehiculo-wrap');
    if (!container) return;

    try {
        const res = await fetch('/api/client/estado-vehiculo', cpFetchOptions);
        const estados = res.ok ? await res.json() : [];

        if (!Array.isArray(estados) || estados.length === 0) {
            container.innerHTML = `
                <div class="cp-empty">
                    <i class="fa-solid fa-car-side"></i>
                    <p>No tienes veh-culos en proceso en este momento. Pronto comenzaremos el diagn-stico.</p>
                </div>`;
            return;
        }

        container.innerHTML = estados.map(item => {
            const estado = item.estado || 'En revisi-n';
            const isTerminado = ['Terminado', 'Completado'].includes(estado);
            const estadoClass = isTerminado ? 'terminado' : '';
            const entrega = item.fechaEstimadaEntrega ? cp_formatFecha(item.fechaEstimadaEntrega) : 'Por confirmar';
            const inicio = item.fechaIngreso ? cp_formatFecha(item.fechaIngreso) : 'Sin registro';
            const sucursal = 'Sucursal principal';
            const mecanico = item.mecanico || 'Sin asignar';
            const telefono = item.telefonoMecanico || 'Tel. por confirmar';

            return `
                <article class="cp-estado-card card-3d ${estadoClass}">
                    ${isTerminado ? '<div class="cp-estado-listo-banner"><i class="fa-solid fa-circle-check"></i> Ya puedes recoger tu vehículo</div>' : ''}
                    <div class="cp-estado-top">
                        <div>
                            <span class="cp-status-badge" data-status="${estado}">${estado}</span>
                            <h3>Orden #${item.idOrden}</h3>
                        </div>
                        <div class="cp-estado-chip"><i class="fa-solid fa-location-dot"></i> ${sucursal}</div>
                    </div>
                    <div class="cp-estado-grid">
                        <div>
                            <span class="cp-label">Placa</span>
                            <strong>${item.placa || 'Sin vehículo'}</strong>
                        </div>
                        <div>
                            <span class="cp-label">Ingreso</span>
                            <strong>${inicio}</strong>
                        </div>
                        <div>
                            <span class="cp-label">Entrega estimada</span>
                            <strong>${entrega}</strong>
                        </div>
                        <div>
                            <span class="cp-label">Mec-nico</span>
                            <strong>${mecanico}</strong>
                        </div>
                    </div>
                    <div class="cp-estado-footer">
                        <span><i class="fa-solid fa-phone"></i> ${telefono}</span>
                        <span><i class="fa-solid fa-spinner"></i> Progreso ${item.progreso || 0}%</span>
                    </div>
                </article>
            `;
        }).join('');

        cp_initTiltEffect();
    } catch (e) {
        console.error('cp_loadEstadoVehiculo error:', e);
            container.innerHTML = '<div class="cp-empty"><i class="fa-solid fa-circle-exclamation"></i><p>No se pudo cargar el estado del veh-culo.</p></div>';
    }
}

async function cp_loadNotificaciones() {
    const container = document.getElementById('cp-notificaciones-container');
    if (!container) return;

    try {
        const res = await fetch('/api/client/notificaciones', cpFetchOptions);
        const items = res.ok ? await res.json() : [];

        const unreadCount = Array.isArray(items) ? items.filter(item => !item.leida).length : 0;
        cp_setBellBadge(unreadCount);

        if (!Array.isArray(items) || items.length === 0) {
            container.innerHTML = `
                <div class="cp-empty">
                    <i class="fa-regular fa-bell-slash"></i>
                    <p>No tienes notificaciones por el momento.</p>
                </div>`;
            return;
        }

        container.innerHTML = items.map(item => {
            const mensaje = item.mensaje || 'Sin mensaje';
            const isVehiculoListo = /vehículo listo|vehiculo listo|listo/i.test(mensaje);
            const className = isVehiculoListo ? 'cp-notif-item veh-listo' : 'cp-notif-item';
            return `
                <div class="${className}">
                    <div class="cp-timeline-dot"></div>
                    <div class="cp-timeline-content">
                        <div class="cp-timeline-title">${mensaje}</div>
                        <div class="cp-timeline-meta">${cp_formatFecha(item.fechaEnvio || item.created_at)}</div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (e) {
        console.error('cp_loadNotificaciones error:', e);
        container.innerHTML = '<div class="cp-empty"><i class="fa-solid fa-circle-exclamation"></i><p>No se pudieron cargar las notificaciones.</p></div>';
    }
}

async function cp_loadHistorial() {
    const container = document.getElementById('cp-historial-container');
    if (!container) return;

    try {
        const res = await fetch('/api/client/historial', cpFetchOptions);
        const historial = res.ok ? await res.json() : [];

        if (!Array.isArray(historial) || historial.length === 0) {
            container.innerHTML = `
                <div class="cp-empty">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <p>A-n no tienes -rdenes finalizadas.</p>
                </div>`;
            return;
        }

        container.innerHTML = historial.map(item => {
            const placa = item.vehiculo?.placa || 'Sin vehículo';
            const servicio = item.detalles?.[0]?.servicio?.nombre || 'Servicio completado';
            const total = item.total ? `Bs. ${Number(item.total).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : 'Sin total';
            return `
                <div class="cp-history-item card-3d">
                    <div class="cp-history-title">${servicio}</div>
                    <div class="cp-history-meta">${placa} - ${cp_formatFecha(item.fechaSalida || item.fechaIngreso)}</div>
                    <div class="cp-history-footer">
                        <span class="cp-status-badge" data-status="${item.estado || 'Completado'}">${item.estado || 'Completado'}</span>
                        <strong>${total}</strong>
                    </div>
                </div>
            `;
        }).join('');

        cp_initTiltEffect();
    } catch (e) {
        console.error('cp_loadHistorial error:', e);
        container.innerHTML = '<div class="cp-empty"><i class="fa-solid fa-circle-exclamation"></i><p>No se pudo cargar el historial.</p></div>';
    }
}

function switchClientTab(tab) {
    document.querySelectorAll('.cp-tab-content').forEach(t => t.classList.remove('active'));
    const target = document.getElementById(`cp-tab-${tab}`);
    if (target) target.classList.add('active');

    document.querySelectorAll('.cp-nav-link').forEach(l => l.classList.remove('active'));
    const navBtn = document.getElementById(`cp-nav-btn-${tab}`);
    if (navBtn) navBtn.classList.add('active');

    if (tab === 'historial') {
        cp_loadHistorial();
    }

    if (tab === 'notificaciones') {
        cp_loadNotificaciones();
    }
}

function cp_openEditProfileModal() {
    if (!cp_userProfile) return;

    const nombreInput = document.getElementById('cp_edit_nombre');
    const telefonoInput = document.getElementById('cp_edit_telefono');
    const direccionInput = document.getElementById('cp_edit_direccion');
    const emailInput = document.getElementById('cp_edit_email');
    const passwordInput = document.getElementById('cp_edit_password');

    if (nombreInput) nombreInput.value = cp_userProfile.nombreCompleto || cp_userProfile.nombreUsuario || '';
    if (telefonoInput) telefonoInput.value = cp_userProfile.telefono || '';
    if (direccionInput) direccionInput.value = cp_userProfile.direccion || '';
    if (emailInput) emailInput.value = cp_userProfile.email || '';
    if (passwordInput) passwordInput.value = '';

    switchClientTab('editar-perfil');
}

function cp_closeEditProfileModal() {
    switchClientTab('perfil');
}

async function cp_handleEditProfile(e) {
    e.preventDefault();
    if (!cp_userProfile) return;

    const nombre = document.getElementById('cp_edit_nombre')?.value || '';
    const telefono = document.getElementById('cp_edit_telefono')?.value || '';
    const direccion = document.getElementById('cp_edit_direccion')?.value || '';
    const email = document.getElementById('cp_edit_email')?.value || '';
    const password = document.getElementById('cp_edit_password')?.value || '';

    const body = {
        nombreCompleto: nombre,
        telefono,
        direccion,
        email
    };

    if (password.trim() !== '') {
        body.contrasena = password;
    }

    try {
        const res = await fetch('/api/client/profile', {
            method: 'PUT',
            headers: cpHeaders,
            credentials: 'same-origin',
            body: JSON.stringify(body)
        });

        if (res.ok) {
            alert('Perfil actualizado con éxito');
            switchClientTab('perfil');
            await cp_loadProfile();
        } else {
            const err = await res.json();
            alert('Error al actualizar: ' + (err.message || 'Datos inválidos'));
        }
    } catch (error) {
        console.error('Error editando perfil:', error);
        alert('Error al conectar con el servidor');
    }
}

const originalInitClientPortal = initClientPortal;
initClientPortal = async function () {
    await originalInitClientPortal();
};

