<!-- client_dashboard.blade.php — Portal del Cliente Taller Automotriz -->

<div id="clientPortal" style="display: none; flex-direction: column; min-height: 100vh; width: 100%;">

    <nav class="cp-navbar">
        <div class="cp-nav-brand">
            <div class="cp-logo-icon">
                <i class="fa-solid fa-car-burst"></i>
            </div>
            <div class="cp-brand-text">
                <strong>AutoTaller</strong>
                <span>Portal del Cliente</span>
            </div>
        </div>

        <div class="cp-nav-links">
            <button class="cp-nav-link active" id="cp-nav-btn-inicio" onclick="switchClientTab('inicio')">
                <i class="fa-solid fa-house"></i> Inicio
            </button>
            <button class="cp-nav-link" id="cp-nav-btn-historial" onclick="switchClientTab('historial')">
                <i class="fa-solid fa-clock-rotate-left"></i> Historial
            </button>
            <button class="cp-nav-link" id="cp-nav-btn-notificaciones" onclick="switchClientTab('notificaciones')">
                <i class="fa-regular fa-bell"></i> Notificaciones
                <span class="cp-nav-badge" id="cp-nav-badge-notificaciones">0</span>
            </button>
            <button class="cp-nav-link" id="cp-nav-btn-perfil" onclick="switchClientTab('perfil')">
                <i class="fa-regular fa-user"></i> Mi Perfil
            </button>
        </div>

        <div class="cp-nav-actions">
            <button class="cp-icon-btn" id="cp-notif-trigger" onclick="switchClientTab('notificaciones')" title="Notificaciones">
                <i class="fa-regular fa-bell"></i>
                <span class="cp-notif-dot" id="cp-notif-dot"></span>
            </button>
            <button class="cp-profile-btn" onclick="switchClientTab('perfil')">
                <div class="cp-avatar" id="cp-avatar-initial">C</div>
                <span id="cp-nav-name" style="font-weight:700;">Cargando...</span>
            </button>
            <button class="cp-icon-btn cp-logout-btn" onclick="openLogoutModal()" title="Cerrar sesión">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </nav>

    <main class="cp-main-content">

        <section id="cp-tab-inicio" class="cp-tab-content active">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="fa-solid fa-car-side"></i> Estado de tu vehículo</h1>
                    <p class="cp-page-subtitle">Aquí verás en tiempo real el estado del servicio activo y el avance de la reparación.</p>
                </div>
            </div>

            <div class="cp-hero-banner">
                <div>
                    <div class="cp-hero-badge">
                        <i class="fa-solid fa-shield-check"></i> Bienvenido, <span id="cp-hero-name">Cliente</span>
                    </div>
                    <h2>Tu vehículo está en seguimiento continuo</h2>
                    <p>El taller te mantiene informado sobre cada avance, mec-nico asignado y el tiempo estimado de entrega.</p>
                </div>
            </div>

            <div id="cp-estado-vehiculo-wrap" class="cp-estado-list">
                <div class="cp-loading"><i class="fa-solid fa-spinner fa-spin"></i> Cargando estado del vehículo...</div>
            </div>
        </section>

        <section id="cp-tab-historial" class="cp-tab-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="fa-solid fa-clock-rotate-left"></i> Historial</h1>
                    <p class="cp-page-subtitle">Revisa tus órdenes finalizadas y los servicios completados en el taller.</p>
                </div>
            </div>

            <div id="cp-historial-container" class="cp-timeline">
                <div class="cp-loading"><i class="fa-solid fa-spinner fa-spin"></i> Cargando historial...</div>
            </div>
        </section>

        <section id="cp-tab-notificaciones" class="cp-tab-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="fa-regular fa-bell"></i> Notificaciones</h1>
                    <p class="cp-page-subtitle">Recibe avisos importantes sobre el progreso de tus servicios y los cambios de estado.</p>
                </div>
            </div>

            <div id="cp-notificaciones-container" class="cp-timeline">
                <div class="cp-loading"><i class="fa-solid fa-spinner fa-spin"></i> Cargando notificaciones...</div>
            </div>
        </section>

        <section id="cp-tab-perfil" class="cp-tab-content">
            <div class="cp-page-header">
                <h1 class="cp-page-title"><i class="fa-regular fa-user"></i> Mi Perfil</h1>
            </div>

            <div class="cp-profile-unified-wrapper">
                <div class="cp-profile-unified-card card-3d">
                    <div class="cp-profile-unified-header">
                        <div class="cp-profile-avatar-large" id="cp-profile-avatar-large">C</div>
                        <h2 class="cp-profile-name-large" id="cp-profile-name-large">Cargando...</h2>
                        <p class="cp-profile-company" id="cp-profile-company">Cliente Taller Automotriz</p>
                        
                        <div class="cp-profile-actions">
                            <button class="cp-btn cp-btn-primary" onclick="cp_openEditProfileModal()">
                                <i class="fa-regular fa-pen-to-square"></i> Editar mi perfil
                            </button>
                        </div>
                    </div>

                    <div class="cp-profile-unified-body">
                        <div class="cp-info-grid">
                            <div class="cp-info-item">
                                <label><i class="fa-regular fa-user"></i> Nombre</label>
                                <p id="cp-info-nombre">—</p>
                            </div>
                            <div class="cp-info-item">
                                <label><i class="fa-regular fa-id-card"></i> CI / NIT</label>
                                <p id="cp-info-ci">—</p>
                            </div>
                            <div class="cp-info-item">
                                <label><i class="fa-regular fa-envelope"></i> Correo Electrónico</label>
                                <p id="cp-info-email">—</p>
                            </div>
                            <div class="cp-info-item">
                                <label><i class="fa-solid fa-phone"></i> Teléfono</label>
                                <p id="cp-info-telefono">No registrado</p>
                            </div>
                            <div class="cp-info-item" style="grid-column: 1 / -1;">
                                <label><i class="fa-solid fa-location-dot"></i> Dirección</label>
                                <p id="cp-info-direccion">No registrada</p>
                            </div>
                            <div class="cp-info-item" style="grid-column: 1 / -1;">
                                <label><i class="fa-solid fa-user-shield"></i> Rol Asignado</label>
                                <p id="cp-info-rol">Cliente</p>
                            </div>
                        </div>

                        <div class="cp-permisos-section">
                            <h3><i class="fa-solid fa-key" style="color:var(--cp-primary);"></i> Permisos de mi cuenta</h3>
                            <div class="cp-permisos-list" id="cp-permisos-container">
                                <div class="cp-loading"><i class="fa-solid fa-spinner fa-spin"></i> Cargando permisos...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- NUEVA PESTAÑA: EDITAR PERFIL -->
        <section id="cp-tab-editar-perfil" class="cp-tab-content">
            <div class="cp-page-header">
                <h1 class="cp-page-title"><i class="fa-regular fa-pen-to-square"></i> Editar Mi Perfil</h1>
                <button class="cp-btn cp-btn-outline" onclick="switchClientTab('perfil')" style="padding: 0.5rem 1.25rem;">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </button>
            </div>

            <div class="cp-profile-unified-wrapper">
                <form class="cp-profile-unified-card card-3d cp-edit-form" onsubmit="cp_handleEditProfile(event)" style="padding: 3rem; background: rgba(10, 26, 23, 0.95);">
                    <p style="font-size:1.05rem; color:var(--cp-text-muted); margin-bottom:2rem; text-align:center;">Actualiza tus datos personales y credenciales de acceso al portal.</p>
                    
                    <div class="cp-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="cp-form-group">
                            <label>Nombre Completo</label>
                            <input type="text" id="cp_edit_nombre" class="cp-form-control" required>
                        </div>

                        <div class="cp-form-group">
                            <label>Teléfono</label>
                            <input type="text" id="cp_edit_telefono" class="cp-form-control">
                        </div>

                        <div class="cp-form-group" style="grid-column: 1 / -1;">
                            <label>Dirección</label>
                            <input type="text" id="cp_edit_direccion" class="cp-form-control">
                        </div>

                        <div class="cp-form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" id="cp_edit_email" class="cp-form-control" required>
                        </div>

                        <div class="cp-form-group">
                            <label>Nueva Contraseña <span style="font-weight:normal;color:var(--cp-text-muted); font-size:0.75rem;">(opcional)</span></label>
                            <input type="password" id="cp_edit_password" class="cp-form-control" placeholder="Dejar en blanco para no cambiar">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 3rem;">
                        <button type="button" class="cp-btn cp-btn-outline" onclick="switchClientTab('perfil')">Cancelar</button>
                        <button type="submit" class="cp-btn cp-btn-primary" style="padding: 0.8rem 3rem;"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </section>

    </main>

</div>
