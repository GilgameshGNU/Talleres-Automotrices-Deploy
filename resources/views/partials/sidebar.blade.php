<!-- BARRA LATERAL DE NAVEGACIÓN -->
<aside id="dashboardSidebar" style="display: none;">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div>
            <span class="brand-text">Taller</span>
            <span class="brand-version">Automotriz</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="menu-container">

        <div class="menu-item active" id="nav-panel" onclick="switchView('panel')">
            <div class="menu-item-label">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Panel principal</span>
            </div>
        </div>

        <div class="menu-section-title admin-only">Gestión de accesos</div>

        <div class="menu-item admin-only" id="nav-usuarios" onclick="switchView('usuarios')">
            <div class="menu-item-label">
                <i class="fa-solid fa-users"></i>
                <span>Usuarios</span>
            </div>
        </div>

        <div class="menu-item admin-only" id="nav-roles" onclick="switchView('roles')">
            <div class="menu-item-label">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Roles</span>
            </div>
        </div>

        <div class="menu-item admin-only" id="nav-permisos" onclick="switchView('permisos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-key"></i>
                <span>Permisos</span>
            </div>
        </div>

        <div class="menu-section-title">Catálogo de servicios</div>

        <div class="menu-item" id="nav-tipos-servicio" onclick="switchView('tipos-servicio')">
            <div class="menu-item-label">
                <i class="fa-solid fa-tags"></i>
                <span>Tipos de Servicio</span>
            </div>
        </div>

        <div class="menu-item" id="nav-servicios" onclick="switchView('servicios')">
            <div class="menu-item-label">
                <i class="fa-solid fa-wrench"></i>
                <span>Servicios</span>
            </div>
        </div>

        <div class="menu-section-title">Directorio</div>

        <div class="menu-item" id="nav-proveedores" onclick="switchView('proveedores')">
            <div class="menu-item-label">
                <i class="fa-solid fa-truck"></i>
                <span>Proveedores</span>
            </div>
        </div>

        <div class="menu-section-title">Clientes y Vehículos</div>

        <div class="menu-item" id="nav-clientes" onclick="switchView('clientes')">
            <div class="menu-item-label">
                <i class="fa-solid fa-address-card"></i>
                <span>Clientes</span>
            </div>
        </div>

        <div class="menu-item" id="nav-vehiculos" onclick="switchView('vehiculos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-car"></i>
                <span>Vehículos</span>
            </div>
        </div>

        <div class="menu-item" id="nav-citas" onclick="switchView('citas')">
            <div class="menu-item-label">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Citas</span>
            </div>
        </div>

        <div class="menu-section-title">Taller</div>

        <div class="menu-item" id="nav-mecanicos" onclick="switchView('mecanicos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-user-gear"></i>
                <span>Mecánicos</span>
            </div>
        </div>

        <div class="menu-item" id="nav-repuestos" onclick="switchView('repuestos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-gears"></i>
                <span>Repuestos</span>
            </div>
        </div>

        <div class="menu-item" id="nav-inventario" onclick="switchView('inventario')">
            <div class="menu-item-label">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventario</span>
            </div>
        </div>

        <div class="menu-item" id="nav-movimientos-inventario" onclick="switchView('movimientos-inventario')">
            <div class="menu-item-label">
                <i class="fa-solid fa-right-left"></i>
                <span>Movimientos</span>
            </div>
        </div>

        <div class="menu-section-title">Operaciones</div>

        <div class="menu-item" id="nav-ordenes-trabajo" onclick="switchView('ordenes-trabajo')">
            <div class="menu-item-label">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Órdenes de Trabajo</span>
            </div>
        </div>

        <div class="menu-item" id="nav-diagnosticos" onclick="switchView('diagnosticos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Diagnósticos</span>
            </div>
        </div>

        <div class="menu-item" id="nav-detalles-orden" onclick="switchView('detalles-orden')">
            <div class="menu-item-label">
                <i class="fa-solid fa-list-check"></i>
                <span>Detalles de Orden</span>
            </div>
        </div>

        <div class="menu-item" id="nav-notificaciones" onclick="switchView('notificaciones')">
            <div class="menu-item-label">
                <i class="fa-solid fa-bell"></i>
                <span>Notificaciones</span>
            </div>
        </div>

        <div class="menu-item" id="nav-pagos" onclick="switchView('pagos')">
            <div class="menu-item-label">
                <i class="fa-solid fa-money-bill-wave"></i>
                <span>Pagos</span>
            </div>
        </div>

        <div class="menu-item" id="nav-facturas" onclick="switchView('facturas')">
            <div class="menu-item-label">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Facturas</span>
            </div>
        </div>

        <div class="menu-item admin-only" id="nav-reportes" onclick="switchView('reportes')">
            <div class="menu-item-label">
                <i class="fa-solid fa-chart-line"></i>
                <span>Reportes</span>
            </div>
        </div>

    </div>

    <!-- Sidebar Profile Footer -->
    <div class="sidebar-profile">
        <div class="profile-info">
            <div class="profile-avatar" id="avatarLetter">?</div>
            <div class="profile-details">
                <div class="profile-name" id="currentProfileName">Cargando...</div>
                <div class="profile-role" id="currentProfileRole">Invitado</div>
            </div>
        </div>
        <div class="logout-link" onclick="openLogoutModal()">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span>Cerrar sesión</span>
        </div>
    </div>
</aside>
