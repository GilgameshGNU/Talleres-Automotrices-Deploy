<!-- MODAL CRUD GENERAL -->
<div class="modal" id="crudModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="crudModalTitle">Agregar Registro</h3>
            <button class="close-modal-btn" onclick="closeCrudModal()" title="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="crudForm" onsubmit="handleCrudSubmit(event)">
            <div class="modal-body">
                <!-- Los campos se inyectan dinámicamente por JS -->
                <div id="dynamicFormFields"></div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeCrudModal()">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Guardar cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL LOGOUT -->
<div id="logoutModal" style="display:none;">
    <div class="modal-box" style="text-align:center; padding:2.5rem 2rem; border-radius:28px; max-width:420px;">
        <div style="
            width:72px; height:72px; border-radius:22px;
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            color: #dc2626; font-size:1.85rem;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1.5rem;
            box-shadow: 0 10px 24px rgba(239,68,68,0.25);
        ">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <h3 style="font-size:1.35rem; font-weight:900; color:#0f172a; margin-bottom:0.6rem; letter-spacing:-0.02em;">
            ¿Cerrar sesión?
        </h3>
        <p style="color:#64748b; font-size:0.92rem; margin-bottom:2rem; line-height:1.6;">
            Se cerrará tu sesión actual y serás redirigido a la pantalla de inicio.
        </p>
        <div style="display:flex; gap:0.75rem; justify-content:center;">
            <button class="btn-secondary" onclick="closeLogoutModal()" style="min-width:120px;">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </button>
            <button class="btn-primary" onclick="confirmLogout()" style="min-width:140px; background:linear-gradient(135deg,#ef4444,#dc2626); box-shadow:0 8px 20px -4px rgba(239,68,68,0.45);">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
            </button>
        </div>
    </div>
</div>

<!-- TOAST DE NOTIFICACIONES -->
<div class="toast-notification" id="toastBox" style="display: none;">
    <div class="toast-message">
        <i class="fa-solid fa-circle-check" id="toastIcon"></i>
        <span id="toastText">¡Listo!</span>
    </div>
    <div class="toast-actions">
        <button class="toast-feedback-btn" title="Útil"><i class="fa-regular fa-thumbs-up"></i></button>
        <button class="toast-feedback-btn" title="No útil"><i class="fa-regular fa-thumbs-down"></i></button>
        <button class="toast-feedback-btn" onclick="closeToast()" title="Cerrar"><i class="fa-solid fa-xmark"></i></button>
    </div>
</div>
