<div id="mecanicoPortal" class="op-portal" style="display: none;">
    <nav class="op-navbar">
        <div class="op-navbar-brand">
            <div class="op-navbar-icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div>
                <div class="op-navbar-title">SisGest Pro</div>
                <div class="op-navbar-sub">Portal de Mecánico</div>
            </div>
        </div>

        <div class="op-nav-links">
            <button class="op-nav-btn active" id="mp-nav-bandeja" data-tab="bandeja" onclick="switchMecTab('bandeja')">
                <i class="fa-solid fa-inbox"></i> <span>Bandeja Principal</span>
            </button>
            <button class="op-nav-btn" id="mp-nav-workspace" data-tab="workspace" onclick="switchMecTab('workspace')" style="display: none;">
                <i class="fa-solid fa-car"></i> <span id="mp-ws-tab-title">Espacio de Trabajo</span>
            </button>
        </div>

        <div class="op-nav-right">
            <label class="op-toggle-wrap" for="mp-toggle-disp">
                <span id="mp-toggle-state" style="font-size:.82rem; color:var(--text-secondary,#9db8b0);">Ocupado</span>
                <div class="op-toggle">
                    <input type="checkbox" id="mp-toggle-disp" onchange="toggleDisponible()">
                    <span class="op-toggle-slider"></span>
                </div>
            </label>
            <div style="font-size:.82rem; color:var(--text-secondary,#9db8b0);">
                <strong id="mp-username" style="color:#fff;">Cargando...</strong><br>
                <span style="font-size:.72rem; color:var(--text-muted,#5f9c92);">Mecánico</span>
            </div>
            <div id="mp-avatar" style="width:36px; height:36px; border-radius:50%; background:var(--gradient-primary,linear-gradient(100deg,#b6f24a,#22d3c5)); color:#04100e; font-weight:900; font-size:1rem; display:flex; align-items:center; justify-content:center;">M</div>
            <button class="op-btn-ghost" onclick="openLogoutModal()" style="padding:.4rem .8rem; font-size:.82rem;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </nav>

    <div id="mp-banner" class="mp-banner" style="display:none; margin:1.5rem 1.5rem 0;"></div>

    <!-- VISTA 1: BANDEJA (Lista de órdenes) -->
    <div id="mp-tab-bandeja" class="op-main mp-tab-content active">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem; gap:1rem; flex-wrap:wrap;">
            <div>
                <h2 style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Bandeja de Órdenes</h2>
                <p style="color:var(--text-muted,#5f9c92); font-size:.85rem; margin:.25rem 0 0;" id="mp-date-label">Cargando fecha...</p>
            </div>
            <button class="op-btn-primary" onclick="loadBandeja()">
                <i class="fa-solid fa-rotate-right"></i> Actualizar
            </button>
        </div>

        <div class="op-stats-grid" style="margin-bottom:1.25rem;">
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(56,189,248,0.15); color:var(--state-info,#38bdf8);">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="mp-kpi-disponibles">0</div>
                    <div class="op-stat-lbl">Disponibles</div>
                </div>
            </div>
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(182,242,74,0.15); color:var(--accent-primary,#b6f24a);">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="mp-kpi-asignadas">0</div>
                    <div class="op-stat-lbl">Mis asignadas</div>
                </div>
            </div>
            <div class="glass-card op-stat-card">
                <div class="op-stat-icon" style="background:rgba(245,158,11,0.15); color:var(--state-warning,#f59e0b);">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <div>
                    <div class="op-stat-val" id="mp-kpi-diagnosticos">0</div>
                    <div class="op-stat-lbl">Diagnósticos</div>
                </div>
            </div>
        </div>

        <div class="glass-card mp-section-card">
            <div class="mp-section-head">
                <div>
                    <h3>Mis Órdenes Asignadas</h3>
                    <p>Vehículos que estás atendiendo actualmente.</p>
                </div>
                <span class="mp-count-badge" id="mp-count-asignadas">0</span>
            </div>
            <div id="mp-lista-asignadas" class="mp-card-grid"></div>
        </div>

        <div class="glass-card mp-section-card">
            <div class="mp-section-head">
                <div>
                    <h3>Disponibles para tomar</h3>
                    <p>Órdenes sin mecánico asignado, en espera de revisión.</p>
                </div>
                <span class="mp-count-badge" id="mp-count-disponibles">0</span>
            </div>
            <div id="mp-lista-disponibles" class="mp-card-grid"></div>
        </div>
    </div>

    <!-- VISTA 2: WORKSPACE (Todo en uno para la orden seleccionada) -->
    <div id="mp-tab-workspace" class="op-main mp-tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
            <div>
                <button class="op-btn-ghost" onclick="switchMecTab('bandeja')" style="margin-bottom:1rem;">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Bandeja
                </button>
                <h2 id="mp-orden-title" style="font-size:1.5rem; font-weight:800; color:#fff; margin:0;">Orden #000</h2>
                <p id="mp-orden-subtitle" style="color:var(--text-muted,#5f9c92); font-size:.9rem; margin:.25rem 0 0;">Cargando vehículo...</p>
            </div>
            <div class="mp-card-actions" style="gap:.75rem;">
                <button class="op-btn-primary" id="mp-btn-avanzar" onclick="avanzarEtapa()" style="font-size:1rem; padding:.6rem 1.2rem;">
                    <i class="fa-solid fa-forward-step"></i> <span id="mp-btn-avanzar-text">Avanzar Etapa</span>
                </button>
                <button class="op-btn-ghost" id="mp-btn-finalizar" onclick="abrirModalTerminado()" style="font-size:1rem; padding:.6rem 1.2rem; color:var(--state-success,#10b981); border:1px solid rgba(16,185,129,0.3); background:rgba(16,185,129,0.05);">
                    <i class="fa-solid fa-flag-checkered"></i> Finalizar
                </button>
            </div>
        </div>

        <div class="glass-card" style="margin-bottom:1.5rem;">
            <div id="mp-orden-meta" class="mp-order-meta"></div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap:1.5rem;">
            <!-- Panel Diagnóstico -->
            <div class="glass-card">
                <div class="mp-section-head" style="margin-bottom:1rem;">
                    <div>
                        <h3 style="margin:0;"><i class="fa-solid fa-stethoscope" style="color:var(--state-info,#38bdf8);"></i> Diagnóstico Activo</h3>
                    </div>
                </div>
                <div class="mp-form-grid">
                    <label>
                        Descripción del problema / Notas
                        <textarea id="mp-diag-desc" class="mp-textarea" rows="4" placeholder="Describe el fallo, síntomas y observaciones..."></textarea>
                    </label>
                    <div>
                        <label style="margin-bottom:.6rem;">Sistemas Afectados (Especialidades)</label>
                        <div id="mp-diag-chips" class="mp-chip-list"></div>
                    </div>
                    <div>
                        <label style="margin-bottom:.6rem;">Severidad</label>
                        <div class="mp-severity-grid">
                            <label class="mp-severity-option"><input type="radio" name="mp-severity" value="Baja"><span>🔵 Baja</span></label>
                            <label class="mp-severity-option"><input type="radio" name="mp-severity" value="Media" checked><span>🟡 Media</span></label>
                            <label class="mp-severity-option"><input type="radio" name="mp-severity" value="Alta"><span>🔴 Alta</span></label>
                        </div>
                    </div>
                    <button class="op-btn-primary" onclick="guardarDiagnostico()" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Diagnóstico
                    </button>
                </div>
            </div>

            <!-- Panel Repuestos -->
            <div class="glass-card">
                <div class="mp-section-head" style="margin-bottom:1rem;">
                    <div>
                        <h3 style="margin:0;"><i class="fa-solid fa-box-open" style="color:var(--accent-primary,#b6f24a);"></i> Uso de Repuestos</h3>
                    </div>
                </div>
                
                <div class="mp-card-actions" style="margin-bottom:1rem;">
                    <input id="mp-rep-search" class="mp-input" type="text" placeholder="Buscar por nombre o código..." onkeyup="if(event.key==='Enter') loadRepuestos(this.value)">
                    <button class="op-btn-ghost" onclick="loadRepuestos(document.getElementById('mp-rep-search').value)">
                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                </div>
                
                <div id="mp-rep-resultados" class="mp-card-grid" style="max-height: 400px; overflow-y: auto; padding-right:.5rem;">
                    <div class="mp-empty-state" style="padding:1rem;">Busca un repuesto para añadirlo a esta orden.</div>
                </div>

                <div id="mp-reg-uso-form" class="glass-card" style="display:none; margin-top:1rem; border:1px solid var(--accent-primary,#b6f24a);">
                    <div class="mp-section-head" style="margin-bottom:.5rem;">
                        <h4 style="margin:0;">Añadir a la orden</h4>
                    </div>
                    <div class="mp-form-grid">
                        <input id="mp-uso-repuesto" class="mp-input" type="text" readonly style="background:rgba(0,0,0,0.2);">
                        <div style="display:flex; gap:1rem; align-items:flex-end;">
                            <label style="flex:1;">
                                Cantidad
                                <input id="mp-uso-cantidad" class="mp-input" type="number" min="1" value="1">
                            </label>
                            <button class="op-btn-primary" onclick="confirmarUsoRepuesto()" style="flex:2; justify-content:center;">
                                <i class="fa-solid fa-plus"></i> Registrar
                            </button>
                        </div>
                        <button class="op-btn-ghost" onclick="document.getElementById('mp-reg-uso-form').style.display='none'" style="width:100%; justify-content:center; color:var(--text-muted,#5f9c92);">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Terminado -->
    <div id="mp-modal-confirmar-terminado" class="mp-modal-backdrop" style="display:none;">
        <div class="glass-card mp-modal-card">
            <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1rem;">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(16,185,129,0.15); color:var(--state-success,#10b981); display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 style="margin:0; color:#fff;">¿Marcar vehículo como LISTO?</h3>
                    <p style="margin:.2rem 0 0; color:var(--text-muted,#5f9c92);">La orden pasará a Terminado y el cliente será notificado.</p>
                </div>
            </div>
            <div class="mp-card-actions" style="justify-content:flex-end;">
                <button class="op-btn-ghost" onclick="cerrarModalTerminado()">Cancelar</button>
                <button class="op-btn-primary" onclick="confirmarTerminado()">Sí, vehículo listo</button>
            </div>
        </div>
    </div>
</div>
