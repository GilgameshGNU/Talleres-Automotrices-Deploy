<!-- PANTALLA DE LOGIN 3D -->
<div id="loginScreen">
    <!-- Botón para volver atrás -->
    <button onclick="hideLoginScreen()" class="btn-back-landing" style="position: absolute; top: 1.5rem; left: 1.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; transition: all 0.3s; z-index: 10;">
        <i class="fa-solid fa-arrow-left me-2"></i> Volver al Inicio
    </button>

    <div class="login-logo-box">
        <span class="login-brand-text">Taller Automotriz</span>
        <span class="login-brand-sub">Sistema de Gestión Empresarial</span>
    </div>

    <div class="login-card">
        <div class="login-header">
            <h2>Iniciar sesión</h2>
            <p>Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <form onsubmit="handleLoginSubmit(event)" autocomplete="off">
            <div class="login-form-group">
                <label><i class="fa-regular fa-user me-1"></i> Usuario</label>
                <div class="login-input-wrapper">
                    <input type="text" class="login-control" id="loginUsername" required placeholder="ej. admin" autocomplete="off">
                </div>
            </div>

            <div class="login-form-group">
                <label><i class="fa-solid fa-key me-1"></i> Contraseña</label>
                <div class="login-input-wrapper">
                    <input type="password" class="login-control" id="loginPassword" required placeholder="••••••••" autocomplete="new-password">
                    <i class="fa-solid fa-eye password-toggle-icon" onclick="togglePasswordVisibility()"></i>
                </div>
            </div>

            <!-- Caja de error inline (oculta por defecto) -->
            <div id="loginError" style="display:none; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; border-radius:10px; padding:0.7rem 1rem; font-size:0.85rem; font-weight:600; margin-bottom:1rem;"></div>

            <button type="submit" class="btn-login-gradient" id="loginSubmitBtn">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Ingresar al Sistema</span>
            </button>
        </form>


    </div>
</div>
