# INSTRUCCIÓN PARA AGENTE — SisGest Pro (v2 — Corregida)
## Implementación de portales para roles no-administrador

---

## 📐 Arquitectura Real del Proyecto (LEER ANTES DE TODO)

El proyecto **no usa Blade multi-página clásica**. Es una **SPA monolítica**:

- Un único entry point: `welcome.blade.php`
- Vistas = partials Blade incluidos con `@include`, contenidos en `resources/views/partials/`
- Cada "pantalla" es un `<div id="xxxPortal" style="display:none">` activado por JS
- Frontend: **HTML + JavaScript puro + CSS** (sin Vue, sin React, sin Alpine)
- Backend: **API REST** bajo `/api/*` — los controladores devuelven JSON
- Auth: `modelo Usuario` custom (tabla `usuario`, PK `idUsuario`, campo `contrasena`)
- Ya existe: `#clientPortal` (portal cliente), `#dashboardWrapper` (admin), `#loginScreen`, `#landingPage`

### ❌ Lo que NO existe (diferencias con instrucción original)

| Instrucción original | Realidad | Ajuste correcto |
|---|---|---|
| `layouts/app-operativo.blade.php` + `@yield` | No se usa Blade layouts | `partials/recepcion.blade.php` y `partials/mecanico.blade.php` como divs SPA |
| Componentes `<x-glass-card>` / `<x-status-badge>` | No hay Blade components activos | Clases CSS: `.glass-card`, `.status-badge[data-status="..."]` |
| `resources/views/recepcionista/*.blade.php` | Todo en `partials/` | `partials/recepcion.blade.php`, `partials/mecanico.blade.php` |
| Rutas web `/recepcion/*`, `/mecanico/*`, `/portal/*` | Rutas SPA + API REST | Nuevas rutas API bajo `/api/recepcion/*`, `/api/mecanico/*`; routing por JS |
| `RecepcionDashboardController` (nuevo) | Backend ya tiene todos los controladores | Agregar **métodos** a controladores existentes, no crear nuevos |

### ✅ Lo que YA existe y se reutiliza

- `CitaController` — CRUD completo con relaciones `cliente/vehiculo/mecanico`
- `ClienteController` — CRUD básico
- `ClientePortalController` — métodos `getProfile`, `updateProfile`, `getSolicitudes` (API)
- `OrdenTrabajoController` — CRUD básico (necesita métodos nuevos)
- `NotificacionController` — CRUD básico (tabla `notificacion`: `idOrden`, `idUsuario`, `mensaje`, `leido`, `fecha`)
- `VehiculoController`, `ServicioController`, `TipoServicioController`, `RepuestoController`, `InventarioController`, `MovimientoInventarioController`, `DiagnosticoController`, `DetalleOrdenTrabajoController` — todos disponibles
- CSS variables en `dashboard.css` (`:root` con `--clr-brand-*`, `--body-bg`, etc.)
- CSS del portal cliente en `client.css` con `.cp-navbar`, `.glass-card` equivalentes

### ⚠️ Gaps de Base de Datos

La tabla `ordentrabajo` **no tiene**:
- `horaInicio` — hora de ingreso del vehículo (no solo fecha)
- `horaFinEstimada` — hora estimada de entrega
- `horaFinReal` — hora real de finalización
- `etapa` — etapa actual del trabajo (Recibido / Diagnóstico / En reparación / Terminado)
- `sucursal` — string simple con nombre de sucursal (no FK por ahora)
- `servicioSolicitado` — descripción libre del trabajo pedido por el cliente

La tabla `mecanico` **no tiene**:
- `idUsuario` — vínculo al usuario que inicia sesión como mecánico
- `disponible` — boolean de disponibilidad

El campo `estado` en `ordentrabajo` actualmente acepta solo `Pendiente`. Necesita valores: `Disponible`, `En Progreso`, `Terminado`, `Cancelada`.

> [!IMPORTANT]
> **Verificar si `estado` es ENUM antes de migrar.** Si el tipo de columna es `ENUM`, añadir directamente los nuevos valores al ENUM es la única vía segura; `string` / `varchar` los acepta sin cambio de esquema. Ejecutar primero:
> ```sql
> SHOW COLUMNS FROM ordentrabajo LIKE 'estado';
> ```
> - Si el resultado muestra `Type = varchar(...)` → no se necesita alterar la columna; los nuevos valores ya son aceptados.
> - Si el resultado muestra `Type = enum(...)` → agregar a la migración de Fase 0.1 el alter explícito:
>   ```php
>   // Solo si estado ES enum:
>   DB::statement("ALTER TABLE ordentrabajo MODIFY COLUMN estado ENUM('Pendiente','Disponible','En Progreso','Terminado','Cancelada') NOT NULL DEFAULT 'Pendiente'");
>   ```
>   Esto preserva los datos existentes (`Pendiente`) y amplía los valores permitidos.

Los roles `Recepcionista` y `Mecánico` **no están en el seed** de `web.php`. Solo existen: `Administrador`, `Encargado`, `Cliente`.

---

## REGLAS DE NEGOCIO CLAVE

1. **El cliente no se auto-registra.** La Recepcionista crea la cuenta en persona. No hay "Crear cuenta" público. El login es solo usuario/contraseña.

2. **El mecánico recibe trabajo de dos formas:**
   - Asignación directa por la Recepcionista al abrir la orden
   - O la orden queda `Disponible` (sin `idMecanico`) y el mecánico la acepta desde su bandeja
   Una vez aceptada/asignada, el flujo es idéntico.

3. **El portal del cliente es informativo.** No agenda, no diagnostica, no solicita servicios. Su pantalla central es el **estado en tiempo real de su vehículo**.

4. **Sucursal siempre visible al cliente.** Aunque hoy haya una sola, el campo `sucursal` (string) debe existir en `ordentrabajo` y mostrarse al cliente.

5. **La notificación "vehículo listo" se dispara en el mismo request** en que el mecánico marca la orden como `Terminado`. No es asíncrono, no requiere jobs/queues — es un `Notificacion::create()` dentro del método `updateEstado()`.

---

## FASE 0 — Base de datos y CSS base (BLOQUEANTE)

### 0.1 Migraciones nuevas

#### Migration: `add_campos_operativos_to_ordentrabajo_table`
```php
Schema::table('ordentrabajo', function (Blueprint $table) {
    $table->time('horaInicio')->nullable()->after('fechaIngreso');
    $table->dateTime('horaFinEstimada')->nullable()->after('horaInicio');
    $table->dateTime('horaFinReal')->nullable()->after('horaFinEstimada');
    $table->string('etapa', 30)->default('Recibido')->after('estado');
    // etapa: Recibido | Diagnóstico | En reparación | Terminado
    $table->string('sucursal', 100)->nullable()->after('etapa');
    $table->string('servicioSolicitado', 255)->nullable()->after('diagnostico');
});
// Nota: no tocar la columna 'estado' para no romper datos existentes
// Nuevos valores aceptados: Disponible | En Progreso | Terminado | Cancelada | Pendiente (legacy)
```

#### Migration: `add_campos_operativos_to_mecanico_table`
```php
Schema::table('mecanico', function (Blueprint $table) {
    $table->integer('idUsuario')->nullable()->after('idMecanico');
    $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->onDelete('set null');
    $table->tinyInteger('disponible')->default(1)->after('idSucursal');
});
```

### 0.2 Seed de roles y usuarios operativos

En la ruta `/` de `web.php`, **dentro del bloque `if (Rol::count() === 0)`**, agregar al final:

```php
// Rol Recepcionista
$rolRecep = Rol::create(['nombre' => 'Recepcionista', 'descripcion' => 'Gestión de recepción y citas']);
Usuario::create([
    'idRol' => $rolRecep->idRol,
    'nombreUsuario' => 'recepcion1',
    'email' => 'recepcion@empresa.com',
    'contrasena' => Hash::make('Recepcion@2024'),
    'estado' => 1
]);

// Rol Mecánico
$rolMec = Rol::create(['nombre' => 'Mecanico', 'descripcion' => 'Ejecución de órdenes de trabajo']);
$usuMec = Usuario::create([
    'idRol' => $rolMec->idRol,
    'nombreUsuario' => 'mecanico1',
    'email' => 'mecanico@empresa.com',
    'contrasena' => Hash::make('Mecanico@2024'),
    'estado' => 1
]);
// Vincular mecánico existente o crear uno demo
Mecanico::updateOrCreate(
    ['ci' => '99999999'],
    ['nombreCompleto' => 'Carlos Demo', 'ci' => '99999999',
     'telefono' => '70000001', 'idSucursal' => 1,
     'idUsuario' => $usuMec->idUsuario, 'disponible' => 1]
);
```

### 0.3 Modelos — actualizar `$fillable`

**`OrdenTrabajo.php`** — agregar a `$fillable`:
`'horaInicio', 'horaFinEstimada', 'horaFinReal', 'etapa', 'sucursal', 'servicioSolicitado'`

Agregar relación:
```php
public function vehiculo() {
    return $this->belongsTo(Vehiculo::class, 'idVehiculo', 'idVehiculo');
}
```

**`Mecanico.php`** — agregar a `$fillable`:
`'idUsuario', 'disponible'`

Agregar relación:
```php
public function usuario() {
    return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
}
```

**`Cliente.php`** — agregar relación al usuario:
```php
public function usuario() {
    return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
}
```

### 0.4 CSS — `public/css/operativo.css` (archivo nuevo)

Variables del sistema de diseño operativo, **sin duplicar** los tokens que ya existen en `dashboard.css`. Solo los específicos de los portales operativos:

```css
/* Importar Outfit exclusivamente para portales operativos */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

/* Tokens operativos — complementan dashboard.css */
:root {
  --bg-base:   #04100e;
  --bg-mid:    #071613;
  --bg-light:  #0e2b28;
  --accent-primary:   #b6f24a;   /* mismo que --clr-brand-500 */
  --accent-secondary: #22d3c5;   /* mismo que --clr-accent    */
  --accent-soft:      #d7ff96;   /* mismo que --clr-brand-200  */
  --text-primary:   #ffffff;
  --text-secondary: #9db8b0;
  --text-muted:     #5f9c92;
  --state-success: #10b981;      /* mismo que --clr-success */
  --state-warning: #f59e0b;      /* mismo que --clr-warning */
  --state-danger:  #ef4444;      /* mismo que --clr-danger  */
  --state-info:    #38bdf8;
  --glass-card-bg:     rgba(255,255,255,0.05);
  --glass-card-border: rgba(120,180,170,0.25);
  --gradient-primary:  linear-gradient(100deg, #b6f24a, #22d3c5);

  /* Bootstrap overrides (mismos valores que admin) */
  --bs-success: #10b981;
  --bs-warning: #f59e0b;
  --bs-danger:  #ef4444;
  --bs-info:    #38bdf8;
}

/* ─── Navbar glass SPA (sin sidebar) ───────────────────────── */
.op-navbar {
  position: sticky; top: 0; z-index: 200;
  background: rgba(15,23,42,0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255,255,255,0.1);
  display: flex; align-items: center;
  justify-content: space-between;
  padding: 0 2rem; height: 68px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.35);
}
.op-navbar-brand { display:flex; align-items:center; gap:.85rem; }
.op-navbar-icon {
  width:38px; height:38px; border-radius:10px;
  background: var(--gradient-primary);
  display:flex; align-items:center; justify-content:center;
  font-size:1.1rem; color:#04100e; font-weight:900;
  box-shadow: 0 4px 14px rgba(182,242,74,0.45);
}
.op-navbar-title { font-weight:800; font-size:1rem; color:#fff; }
.op-navbar-sub { font-size:.7rem; color:var(--text-muted); }
.op-nav-links { display:flex; gap:.25rem; }
.op-nav-btn {
  background: transparent; border: none;
  color: var(--text-secondary); padding:.5rem .9rem;
  border-radius:10px; cursor:pointer; font-size:.88rem;
  font-weight:600; font-family:'Outfit',sans-serif;
  display:flex; align-items:center; gap:.45rem;
  transition: all .2s ease;
}
.op-nav-btn:hover { background: rgba(182,242,74,0.1); color:#fff; }
.op-nav-btn.active { background: rgba(182,242,74,0.18); color:var(--accent-primary); }
.op-nav-right { display:flex; align-items:center; gap:.75rem; }
.op-badge-dot {
  width:8px; height:8px; border-radius:50%;
  background: var(--accent-primary);
  box-shadow: 0 0 8px rgba(182,242,74,0.7);
  animation: pulseDot 2s infinite;
}
@keyframes pulseDot {
  0%,100%{ transform:scale(1); opacity:1; }
  50%{ transform:scale(1.4); opacity:.7; }
}

/* ─── Glass Card ────────────────────────────────────────────── */
.glass-card {
  background: var(--glass-card-bg);
  border: 1px solid var(--glass-card-border);
  border-radius: 16px;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  padding: 1.5rem;
  transition: border-color .25s, box-shadow .25s;
}
.glass-card:hover {
  border-color: rgba(182,242,74,0.3);
  box-shadow: 0 8px 28px rgba(0,0,0,0.35);
}
.glass-card.glow-success {
  border-color: var(--state-success);
  box-shadow: 0 0 24px rgba(16,185,129,0.25);
}
.glass-card.glow-warning {
  border-color: var(--state-warning);
  box-shadow: 0 0 24px rgba(245,158,11,0.25);
}
.glass-card.glow-lime {
  border-color: var(--accent-primary);
  box-shadow: 0 0 30px rgba(182,242,74,0.35);
}

/* ─── Status Badge ──────────────────────────────────────────── */
.status-badge {
  display: inline-flex; align-items:center; gap:.35rem;
  padding: .28rem .75rem; border-radius: 99px;
  font-size: .78rem; font-weight: 700;
  font-family:'Outfit',sans-serif; white-space:nowrap;
}
/* Órdenes */
.status-badge[data-status="Disponible"]    { background:rgba(56,189,248,0.15); color:#38bdf8; }
.status-badge[data-status="En Progreso"]   { background:rgba(245,158,11,0.15); color:#f59e0b; }
.status-badge[data-status="Terminado"]     { background:rgba(16,185,129,0.15); color:#10b981; }
.status-badge[data-status="Cancelada"]     { background:rgba(239,68,68,0.15);  color:#ef4444; }
.status-badge[data-status="Pendiente"]     { background:rgba(245,158,11,0.12); color:#f59e0b; }
/* Citas */
.status-badge[data-status="Confirmada"]    { background:rgba(16,185,129,0.15); color:#10b981; }
.status-badge[data-status="Completada"]    { background:rgba(182,242,74,0.15); color:#b6f24a; }
/* Stock */
.status-badge[data-status="En Stock"]      { background:rgba(16,185,129,0.15); color:#10b981; }
.status-badge[data-status="Bajo Stock"]    { background:rgba(245,158,11,0.15); color:#f59e0b; }
.status-badge[data-status="Agotado"]       { background:rgba(239,68,68,0.15);  color:#ef4444; }

/* ─── Contenedor de portal SPA ──────────────────────────────── */
.op-portal {
  display: none; flex-direction: column;
  min-height: 100vh; width: 100%;
  background: linear-gradient(135deg, #04100e 0%, #071613 55%, #0e2b28 100%);
  font-family: 'Outfit', sans-serif;
  color: #fff;
}
.op-main { flex:1; max-width: 1280px; margin: 0 auto; width:100%; padding: 2rem 1.5rem; }

/* ─── Stat Cards ────────────────────────────────────────────── */
.op-stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.25rem; margin-bottom:1.75rem; }
.op-stat-card { display:flex; align-items:center; gap:1rem; }
.op-stat-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
.op-stat-val { font-size:2rem; font-weight:900; line-height:1; }
.op-stat-lbl { font-size:.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.06em; }

/* ─── Stepper ───────────────────────────────────────────────── */
.op-stepper { display:flex; align-items:center; gap:0; margin:1.5rem 0; overflow-x:auto; }
.op-step { display:flex; align-items:center; flex:1; min-width:0; }
.op-step-bubble {
  width:36px; height:36px; border-radius:50%; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  font-weight:800; font-size:.8rem;
  background: rgba(255,255,255,0.08);
  border: 2px solid rgba(255,255,255,0.15);
  color: var(--text-muted);
  transition: all .3s ease;
}
.op-step.active .op-step-bubble {
  background: var(--gradient-primary);
  border-color: transparent; color:#04100e;
  box-shadow: 0 0 16px rgba(182,242,74,0.5);
}
.op-step.done .op-step-bubble { background:var(--state-success); border-color:transparent; color:#fff; }
.op-step-label { font-size:.72rem; font-weight:700; color:var(--text-muted); text-align:center; margin-top:.35rem; }
.op-step.active .op-step-label { color:var(--accent-primary); }
.op-step.done .op-step-label { color:var(--state-success); }
.op-step-line { flex:1; height:2px; background:rgba(255,255,255,0.1); margin:0 .5rem; }
.op-step.done .op-step-line { background:var(--state-success); }

/* ─── Timeline ──────────────────────────────────────────────── */
.op-timeline { position:relative; padding-left:1.5rem; }
.op-timeline::before { content:''; position:absolute; left:.5rem; top:0; bottom:0; width:2px; background:rgba(255,255,255,0.1); }
.op-timeline-item { position:relative; padding-bottom:1.25rem; }
.op-timeline-dot {
  position:absolute; left:-1.1rem; top:.3rem;
  width:10px; height:10px; border-radius:50%;
  background:var(--accent-primary);
  box-shadow:0 0 8px rgba(182,242,74,0.6);
}
.op-timeline-date { font-size:.73rem; color:var(--text-muted); font-weight:600; }
.op-timeline-msg { font-size:.9rem; color:#fff; margin:.2rem 0 0; }

/* ─── Botones ───────────────────────────────────────────────── */
.op-btn-primary {
  background: var(--gradient-primary);
  color: #04100e; border:none; border-radius:10px;
  padding:.65rem 1.25rem; font-weight:800; font-size:.9rem;
  font-family:'Outfit',sans-serif; cursor:pointer;
  display:inline-flex; align-items:center; gap:.5rem;
  box-shadow: 0 6px 18px rgba(182,242,74,0.4);
  transition: transform .2s, box-shadow .2s;
}
.op-btn-primary:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(182,242,74,0.5); }
.op-btn-ghost {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  color:#fff; border-radius:10px;
  padding:.65rem 1.25rem; font-weight:600; font-size:.9rem;
  font-family:'Outfit',sans-serif; cursor:pointer;
  display:inline-flex; align-items:center; gap:.5rem;
  transition: background .2s, border-color .2s;
}
.op-btn-ghost:hover { background:rgba(182,242,74,0.1); border-color:rgba(182,242,74,0.3); }
.op-btn-danger { background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#ef4444; }

/* ─── Toggle disponibilidad mecánico ───────────────────────── */
.op-toggle-wrap { display:flex; align-items:center; gap:.6rem; cursor:pointer; }
.op-toggle { position:relative; width:44px; height:24px; }
.op-toggle input { opacity:0; width:0; height:0; }
.op-toggle-slider {
  position:absolute; inset:0; border-radius:99px;
  background:rgba(255,255,255,0.1); transition:.3s;
}
.op-toggle-slider::before {
  content:''; position:absolute;
  width:18px; height:18px; border-radius:50%;
  left:3px; top:3px; background:#fff;
  transition:.3s;
}
.op-toggle input:checked + .op-toggle-slider { background:var(--state-success); }
.op-toggle input:checked + .op-toggle-slider::before { transform:translateX(20px); }

/* ─── Responsive tablet ──────────────────────────────────────── */
@media (max-width: 768px) {
  .op-main { padding: 1.25rem 1rem; }
  .op-stats-grid { grid-template-columns: 1fr 1fr; }
  .op-navbar { padding:0 1rem; }
  .op-nav-links .op-nav-btn span { display:none; } /* Solo iconos en mobile */
  .op-step-label { display:none; }
}
```

### 0.5 Agregar en `welcome.blade.php`

```html
<!-- Fuentes operativas -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- CSS operativo (después de dashboard.css) -->
<link rel="stylesheet" href="{{ asset('css/operativo.css') }}">

<!-- Partials nuevos (antes de </body>) -->
@include('partials.recepcion')
@include('partials.mecanico')

<!-- JS nuevos (antes de </body>) -->
<script src="{{ asset('js/recepcion.js') }}"></script>
<script src="{{ asset('js/mecanico.js') }}"></script>
```

---

## FASE 1 — Portal Recepcionista

### 1.A Backend — Nuevas rutas y métodos API

Agregar en `routes/web.php` dentro del grupo `Route::middleware('auth.session')`:

```php
// Recepción: métodos adicionales
POST   /api/clientes/con-usuario          → ClienteController@storeConUsuario
GET    /api/citas/hoy                     → CitaController@hoy
GET    /api/clientes/buscar               → ClienteController@buscar  (q=nombre|ci|telefono)
GET    /api/mecanicos/con-carga           → MecanicoController@conCarga  (incluye count órdenes abiertas)
POST   /api/ordenes-trabajo               → OrdenTrabajoController@store  (enriquecido con horaInicio, sucursal, etc.)
POST   /api/ordenes-trabajo/{id}/aceptar  → OrdenTrabajoController@aceptar
PATCH  /api/ordenes-trabajo/{id}/estado   → OrdenTrabajoController@updateEstado
GET    /api/notificaciones/no-leidas      → NotificacionController@noLeidas
```

#### `ClienteController@storeConUsuario`
```php
// Valida: nombreCompleto, ci_nit, telefono, email, direccion
// 1. Crea registro en 'cliente'
// 2. Genera usuario: cli_<primer_apellido>, contraseña random 10 chars (letras+números)
// 3. Busca idRol donde nombre='Cliente'
// 4. Crea Usuario con rol Cliente
// 5. Actualiza cliente.idUsuario = nuevo usuario
// 6. Retorna: { cliente, usuario: { nombreUsuario, passwordPlano } }
//    — passwordPlano solo en este response, NO se almacena en texto claro
```

#### `ClienteController@buscar`
```php
// GET /api/clientes/buscar?q=...
// WHERE nombreCompleto LIKE %q% OR ci_nit LIKE %q% OR telefono LIKE %q%
// Incluye: usuario vinculado (para saber si ya tiene cuenta)
// Retorna array de clientes
```

#### `CitaController@hoy`
```php
// GET /api/citas/hoy
// WHERE fecha = today(), with(['cliente','vehiculo','mecanico']), orderBy hora
```

#### `MecanicoController@conCarga`
```php
// GET /api/mecanicos/con-carga
// Mecánico::with('sucursal')
//   ->withCount(['ordenesTrabajo as ordenes_activas_count' => fn($q) =>
//       $q->whereIn('estado', ['En Progreso', 'Disponible'])])
//   ->where('disponible', 1)
//   ->get()
```

#### `OrdenTrabajoController@store` (reemplazar actual)
```php
// Valida: idCliente, idVehiculo, idServicio(nullable), idMecanico(nullable),
//         servicioSolicitado, sucursal, horaFinEstimada(nullable)
// horaInicio = now()
// estado = idMecanico ? 'En Progreso' : 'Disponible'
// etapa = 'Recibido'
// Al guardar: crear Notificacion al idUsuario del cliente
//   mensaje: "Tu vehículo ha sido recibido. Te notificaremos cuando esté listo."
```

#### `OrdenTrabajoController@aceptar($id)`
```php
// POST /api/ordenes-trabajo/{id}/aceptar
// Solo si orden.idMecanico === null && orden.estado === 'Disponible'
// Busca Mecanico where idUsuario = Auth::user()->idUsuario
// Actualiza: idMecanico, estado = 'En Progreso'
// Retorna: orden actualizada
```

#### `OrdenTrabajoController@updateEstado($id)`
```php
// PATCH /api/ordenes-trabajo/{id}/estado
// Body: { etapa: 'Diagnóstico'|'En reparación'|'Terminado' }
// Si etapa === 'Terminado':
//   - horaFinReal = now()
//   - estado = 'Terminado'
//   - Crear Notificacion al idUsuario del cliente:
//     "✅ Tu vehículo está listo. Ya puedes pasar a recogerlo."
// Retorna: orden actualizada
```

#### `NotificacionController@noLeidas`
```php
// GET /api/notificaciones/no-leidas
// WHERE idUsuario = Auth::user()->idUsuario AND leido = false
// Retorna count + listado reciente
```

### 1.B Frontend — `resources/views/partials/recepcion.blade.php`

Estructura del div contenedor:

```html
<div id="recepcionPortal" class="op-portal">

  <!-- Navbar -->
  <nav class="op-navbar">
    <!-- Logo | Links: Dashboard / Clientes / Nueva Orden / Citas / Servicios -->
    <!-- Right: bell con badge, avatar, logout -->
  </nav>

  <!-- Tab: Dashboard (default) -->
  <div id="rp-tab-dashboard" class="op-main rp-tab-content active">
    <!-- Stat cards: citas pendientes/confirmadas/canceladas -->
    <!-- Lista de citas de hoy con botón "Recibir" -->
    <!-- Botón fijo "Nueva cita" arriba-derecha en navbar -->
  </div>

  <!-- Tab: Clientes -->
  <div id="rp-tab-clientes" class="op-main rp-tab-content">
    <!-- Buscador prominente (placeholder: "Buscar por nombre, CI o teléfono...") -->
    <!-- Resultados: tarjeta por cliente con estado "Con cuenta / Sin cuenta" -->
    <!-- Botón "Nuevo cliente" visible solo si búsqueda activa y sin resultados -->
    <!-- Offcanvas derecho: form nuevo cliente (nombre, CI, teléfono, email, dirección) -->
    <!-- Modal credenciales: aparece UNA SOLA VEZ tras crear cliente exitosamente -->
    <!--   Muestra: usuario generado + contraseña temporal + botón "Imprimir" -->
    <!--   Clase CSS especial: credencial-card (fondo oscuro, texto monospace) -->
  </div>

  <!-- Tab: Nueva Orden -->
  <div id="rp-tab-nueva-orden" class="op-main rp-tab-content">
    <!-- Wizard de 3 pasos (simple, no stepper visual obligatorio):
         Paso 1: Seleccionar cliente → input search, resultado clickeable
         Paso 2: Seleccionar vehículo del cliente (cards) o "Agregar vehículo nuevo"
         Paso 3: Datos de la orden:
           - Servicio solicitado (textarea)
           - Hora de inicio (datetime-local, default: ahora)
           - Sucursal (input text o select si hay varias)
           - Sección asignación:
               [Radio] Asignar a mecánico específico
                 → Select: muestra nombre + "(N órdenes activas)" por cada mecánico disponible
               [Radio] Dejar disponible (cualquier mecánico puede tomar)
           - Hora estimada de entrega (datetime-local, opcional)
    -->
    <!-- Botón "Abrir orden de trabajo" -->
  </div>

  <!-- Tab: Notificaciones -->
  <div id="rp-tab-notificaciones" class="op-main rp-tab-content">
    <!-- Chips de filtro: Todas | Citas | Órdenes listas | Otros -->
    <!-- Timeline de notificaciones con .op-timeline -->
    <!-- Botón "Enviar notificación manual" → modal: destinatario + mensaje -->
  </div>

  <!-- Tab: Citas -->
  <div id="rp-tab-citas" class="op-main rp-tab-content">
    <!-- Semana actual: header con 7 columnas (L-D), navegación prev/next semana -->
    <!-- Cada cita: bloque .glass-card pequeño con color por estado en borde-izquierdo -->
    <!-- Botón "+" en cada columna o en navbar: abre modal crear cita -->
    <!-- Modal crear/editar: cliente (buscar), vehículo, motivo, fecha, hora, estado -->
  </div>

  <!-- Tab: Servicios (solo lectura) -->
  <div id="rp-tab-servicios" class="op-main rp-tab-content">
    <!-- Tabs por TipoServicio (chips) -->
    <!-- Grid de .glass-card: nombre, descripción, precio, duración estimada -->
    <!-- Sin botones de editar/eliminar -->
  </div>

</div>
```

### 1.C Frontend — `public/js/recepcion.js`

Módulos de la SPA de Recepcionista:

```
initRecepcionPortal()        — carga perfil, activa dashboard tab
switchRecepTab(tabId)        — cambia tab activo, actualiza nav botón
loadRecepDashboard()         — GET /api/citas/hoy → renderiza stats + lista citas
loadClientes(query)          — GET /api/clientes/buscar?q= → renderiza resultados
crearClienteConUsuario(data) — POST /api/clientes/con-usuario → muestra modal credenciales
imprimirCredenciales()       — window.print() sobre la credencial-card
loadMecanicosConCarga()      — GET /api/mecanicos/con-carga → popula select asignación
crearOrden(data)             — POST /api/ordenes-trabajo → confirma y redirige a dashboard
loadNotificaciones(filtro)   — GET /api/notificaciones + filtro → render timeline
loadCitasSemana(offset)      — GET /api/citas → filtra por semana actual ± offset
crearCita(data)              — POST /api/citas
actualizarCita(id, data)     — PUT  /api/citas/{id}
loadServicios()              — GET /api/servicios → con tipos → render grid
renderStatusBadge(estado)    — retorna HTML del .status-badge según estado
```

**Regla JS crítica:** Al login exitoso en `dashboard.js`, si `data.rol === 'Recepcionista'`:
- Ocultar todos los demás portales
- `document.getElementById('recepcionPortal').style.display = 'flex'`
- Llamar `initRecepcionPortal()`

---

## FASE 2 — Portal Mecánico

### 2.A Backend — Métodos adicionales

```php
// Ya cubiertos en Fase 1.A:
GET  /api/ordenes-trabajo              → filtrar: ?disponibles=1 o ?mecanico_id=me
POST /api/ordenes-trabajo/{id}/aceptar → OrdenTrabajoController@aceptar
PATCH /api/ordenes-trabajo/{id}/estado → OrdenTrabajoController@updateEstado

// Nuevos para mecánico:
PATCH /api/mecanicos/mi-disponibilidad → MecanicoController@toggleDisponible
GET   /api/diagnosticos?idOrden={id}   → DiagnosticoController (método index modificado)
POST  /api/diagnosticos                → DiagnosticoController@store (ya existe)
GET   /api/detalles-orden?idOrden={id} → DetalleOrdenTrabajoController (index modificado)
POST  /api/detalles-orden              → DetalleOrdenTrabajoController@store (ya existe)
GET   /api/repuestos?q={query}         → RepuestoController con join a inventario
POST  /api/movimientos-inventario      → MovimientoInventarioController@store (ya existe)
```

#### `MecanicoController@toggleDisponible`
```php
// PATCH /api/mecanicos/mi-disponibilidad
// Busca Mecanico where idUsuario = Auth::user()->idUsuario
// Alterna: disponible = !disponible
// Retorna: { disponible: bool }
```

#### `OrdenTrabajoController@index` (modificar)
```php
// Soportar parámetros:
// ?disponibles=1  → WHERE idMecanico IS NULL AND estado = 'Disponible'
// ?mis=1          → WHERE idMecanico = mecanico del usuario autenticado
// Default (admin): retorna todas
```

### 2.B Frontend — `resources/views/partials/mecanico.blade.php`

```html
<div id="mecanicoPortal" class="op-portal">

  <!-- Navbar — SIMPLIFICADA, max 4 accesos -->
  <nav class="op-navbar">
    <!-- Logo | Links: Bandeja / Mi Orden / Repuestos -->
    <!-- Right: toggle disponible/ocupado + avatar + logout -->
    <!-- El toggle llama PATCH /api/mecanicos/mi-disponibilidad -->
  </nav>

  <!-- Tab: Bandeja (DEFAULT y pantalla principal) -->
  <div id="mp-tab-bandeja" class="op-main rp-tab-content active">

    <!-- Sección "Disponibles para tomar" -->
    <section id="mp-disponibles">
      <h2>Disponibles para tomar <span class="badge" id="mp-count-disponibles">0</span></h2>
      <div id="mp-lista-disponibles">
        <!-- Por cada orden: .glass-card con vehículo, cliente, servicio, sucursal -->
        <!-- Botón "Aceptar" → POST /api/ordenes-trabajo/{id}/aceptar -->
      </div>
    </section>

    <!-- Sección "Mis órdenes asignadas" -->
    <section id="mp-asignadas">
      <h2>Mis órdenes asignadas <span class="badge" id="mp-count-asignadas">0</span></h2>
      <div id="mp-lista-asignadas">
        <!-- Orden más urgente destacada con .glass-card.glow-lime -->
        <!-- Botón "Ver orden" → switchMecanicoTab('orden', idOrden) -->
      </div>
    </section>

  </div>

  <!-- Tab: Orden de trabajo (detalle) -->
  <div id="mp-tab-orden" class="op-main rp-tab-content">
    <!-- Header: placa/modelo, cliente, hora inicio -->
    <!-- Stepper horizontal: Recibido → Diagnóstico → En reparación → Terminado -->
    <!-- Botón avanzar etapa (solo una etapa a la vez) -->
    <!-- En etapa "Terminado": modal confirmación → PATCH estado → notificación cliente -->
    <!-- Links opcionales: "Agregar diagnóstico", "Ver tareas", "Usar repuesto" -->
  </div>

  <!-- Tab: Diagnóstico (aparece desde orden) -->
  <div id="mp-tab-diagnostico" class="op-main rp-tab-content">
    <!-- Textarea descripción -->
    <!-- Chips seleccionables: Frenos / Motor / Eléctrico / Suspensión / Transmisión / A/C -->
    <!-- Selector severidad: Baja (info) / Media (warning) / Alta (danger) -->
    <!-- Botón "Guardar diagnóstico" → POST /api/diagnosticos -->
  </div>

  <!-- Tab: Tareas (opcional, para órdenes complejas) -->
  <div id="mp-tab-tareas" class="op-main rp-tab-content">
    <!-- Lista de DetalleOrdenTrabajo: checkbox + descripción + tiempo estimado -->
    <!-- Barra de progreso: tareas completadas / total -->
    <!-- Botón "Agregar tarea" -->
  </div>

  <!-- Tab: Repuestos (solo lectura) -->
  <div id="mp-tab-repuestos" class="op-main rp-tab-content">
    <!-- Input búsqueda por nombre/código -->
    <!-- Resultados: .glass-card con nombre, código, stock (.status-badge stock), precio -->
    <!-- Botón "Registrar uso" → abre form de movimiento -->
    <!-- Form movimiento: repuesto seleccionado, cantidad, orden asociada -->
  </div>

</div>
```

### 2.C Frontend — `public/js/mecanico.js`

```
initMecanicoPortal()             — carga mecánico, activa bandeja, carga disponibles + asignadas
switchMecanicoTab(tab, idOrden)  — cambia tab, si idOrden → carga orden específica
loadDisponibles()                — GET /api/ordenes-trabajo?disponibles=1 → render tarjetas
loadAsignadas()                  — GET /api/ordenes-trabajo?mis=1 → render tarjetas (urgente al tope)
aceptarOrden(idOrden)            — POST /api/ordenes-trabajo/{id}/aceptar → reload bandeja
loadOrdenDetalle(idOrden)        — GET /api/ordenes-trabajo/{id} → render header + stepper
avanzarEtapa(idOrden, etapaIdx)  — PATCH /api/ordenes-trabajo/{id}/estado → reload stepper
confirmarTerminado(idOrden)      — modal → PATCH estado=Terminado → notificación disparada server-side
toggleDisponible()               — PATCH /api/mecanicos/mi-disponibilidad → actualiza UI navbar
guardarDiagnostico(data)         — POST /api/diagnosticos
loadRepuestos(query)             — GET /api/repuestos?q= → render resultados
registrarUsoRepuesto(data)       — POST /api/movimientos-inventario
```

---

## FASE 3 — Portal del Cliente (rediseño)

El portal del cliente YA existe (`#clientPortal` con `client_dashboard.blade.php` + `client.css` + `client.js`). **No se reemplaza, se mejora.**

### 3.A Backend — Métodos faltantes

#### `ClientePortalController@getEstadoVehiculo`
```php
// GET /api/client/estado-vehiculo
// Busca órdenes del cliente autenticado que NO estén Canceladas
// WITH mecanico, vehiculo
// Retorna: array de ordenes con horaInicio, horaFinEstimada, horaFinReal,
//          estado, etapa, sucursal, mecanico.nombreCompleto, mecanico.telefono
//
// ── Composición de fechas y horas (campos separados en BD) ───────
// La tabla ordentrabajo almacena:
//   fechaIngreso  DATE         — día en que ingresó el vehículo (ej: "2025-07-27")
//   horaInicio    TIME/null    — hora puntual de ingreso (ej: "09:30:00")
//   horaFinEstimada DATETIME/null — fecha+hora estimada de entrega (campo completo)
//   horaFinReal     DATETIME/null — fecha+hora real de finalización (campo completo)
//
// Para el frontend, combinar fechaIngreso + horaInicio al serializar:
//   'inicio_display' => $orden->fechaIngreso
//                       . ($orden->horaInicio ? ' ' . $orden->horaInicio : '')
//   // O convertir a Carbon para formato localizado:
//   'inicio_display' => $orden->horaInicio
//       ? \Carbon\Carbon::parse($orden->fechaIngreso . ' ' . $orden->horaInicio)
//                        ->format('d/m/Y H:i')
//       : \Carbon\Carbon::parse($orden->fechaIngreso)->format('d/m/Y')
//
// horaFinEstimada y horaFinReal son DATETIME completos — no requieren combinación.
// El JS del portal cliente recibirá estos tres campos y mostrará:
//   • Si estado = 'En Progreso': horaFinEstimada formateada (o "Por definir")
//   • Si estado = 'Terminado':   horaFinReal formateada + banner de vehículo listo
```

#### `ClientePortalController@getNotificacionesCliente`
```php
// GET /api/client/notificaciones
// WHERE idUsuario = Auth::user()->idUsuario ORDER BY fecha DESC LIMIT 50
// Marcar como leídas (leido = true) al leer
```

Agregar rutas en `web.php`:
```php
Route::get('/client/estado-vehiculo',  [ClientePortalController::class, 'getEstadoVehiculo']);
Route::get('/client/notificaciones',   [ClientePortalController::class, 'getNotificacionesCliente']);
Route::get('/client/historial',        [ClientePortalController::class, 'getHistorial']);
```

#### `ClientePortalController@getHistorial`
```php
// GET /api/client/historial
// Órdenes Terminadas del cliente, WITH vehiculo, mecanico
// OrderBy fechaIngreso desc
```

### 3.B Frontend — cambios en `client_dashboard.blade.php`

**Pantalla "Inicio" → rediseñar completamente:**

- Eliminar las KPI cards actuales (servicios en catálogo, solicitudes activas)
- Eliminar botón "Ver catálogo de servicios" y "Solicitar servicio"
- Reemplazar hero genérico por la **tarjeta de estado de vehículo** como contenido principal:

```html
<!-- Tarjeta central: Estado del vehículo -->
<div id="cp-estado-vehiculo-wrap">
  <!-- Si no hay órdenes activas: mensaje vacío amigable -->
  <!-- Por cada orden activa: una tarjeta .glass-card con: -->
  <!--   Badge de estado (En Progreso / Terminado) -->
  <!--   Vehículo: placa, modelo -->
  <!--   Hora inicio servicio -->
  <!--   Hora estimada de entrega (si En Progreso) O Hora real (si Terminado) -->
  <!--   Mecánico: nombre + teléfono (o "Asignándose..." si null) -->
  <!--   Sucursal -->
  <!--   Si Terminado: borde verde glow + mensaje "✅ Ya puedes recoger tu vehículo" -->
</div>
```

**Agregar tab "Notificaciones"** (ya existe el botón de campana, conectarlo):
- Lista de notificaciones del cliente filtradas por `idUsuario`
- La notificación de "vehículo listo" tiene fondo diferenciado (rgba verde suave)

**Mantener:**
- Tab "Mi Perfil" (con form editable solo teléfono/email)
- Tab "Historial" (timeline de órdenes terminadas)

**Eliminar:**
- Tab "Catálogo de servicios" (no es parte del flujo real del cliente)
- Cualquier botón de "solicitar servicio" (viola regla de negocio #3)

### 3.C CSS — agregar en `client.css`

```css
/* Orbes de luz en el fondo del portal cliente */
#clientPortal::before {
  content: '';
  position: fixed; pointer-events: none;
  width: 600px; height: 600px; border-radius: 50%;
  top: -100px; left: -100px;
  background: radial-gradient(circle, rgba(182,242,74,0.08) 0%, transparent 70%);
  z-index: 0;
}
#clientPortal::after {
  content: '';
  position: fixed; pointer-events: none;
  width: 500px; height: 500px; border-radius: 50%;
  bottom: -80px; right: -80px;
  background: radial-gradient(circle, rgba(34,211,197,0.07) 0%, transparent 70%);
  z-index: 0;
}

/* Tarjeta de estado — "vehículo listo" */
.cp-estado-card.terminado {
  border-color: var(--state-success) !important;
  box-shadow: 0 0 28px rgba(16,185,129,0.22) !important;
}
.cp-estado-listo-banner {
  background: rgba(16,185,129,0.12);
  border-radius: 10px; padding: .75rem 1rem;
  color: var(--state-success); font-weight: 700;
  display: flex; align-items: center; gap: .5rem;
  margin-top: 1rem;
}

/* Notificación "vehículo listo" destacada */
.cp-notif-item.veh-listo {
  background: rgba(16,185,129,0.08);
  border-left: 3px solid var(--state-success);
}
```

---

## FASE 4 — Routing SPA por rol

### 4.A `AuthController` — verificar respuesta de login

La respuesta del `POST /api/login` debe incluir:
```json
{
  "message": "Login exitoso",
  "user": {
    "idUsuario": 1,
    "nombreUsuario": "recepcion1",
    "email": "...",
    "rol": { "nombre": "Recepcionista" }
  }
}
```

Si `usuario->load('rol')` no está en el `me()` o `login()` del `AuthController`, agregarlo.

### 4.B `dashboard.js` — función de routing por rol

Dentro de `handleLoginSuccess(data)` (o donde se active el portal correcto tras login):

```javascript
function activarPortalPorRol(rol) {
  // Ocultar todos los portales
  ['dashboardWrapper', 'clientPortal', 'recepcionPortal', 'mecanicoPortal']
    .forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });

  switch(rol) {
    case 'Administrador':
    case 'Encargado':
      document.getElementById('dashboardWrapper').style.display = 'flex';
      initDashboard?.();
      break;
    case 'Recepcionista':
      document.getElementById('recepcionPortal').style.display = 'flex';
      initRecepcionPortal?.();
      break;
    case 'Mecanico':
      document.getElementById('mecanicoPortal').style.display = 'flex';
      initMecanicoPortal?.();
      break;
    case 'Cliente':
      document.getElementById('clientPortal').style.display = 'flex';
      initClientPortal?.();
      break;
  }
}
```

---

## FASE 6 — Seguridad: Control de Acceso por Rol + Rate Limiting

> [!IMPORTANT]
> Esta fase es transversal. Se implementa **después de que las Fases 1–3 estén funcionales** pero **antes de pasar a producción**. No bloquea el desarrollo de frontend, pero sí debe estar presente antes de cualquier demo real.

### Contexto del problema

Actualmente cualquier usuario autenticado puede invocar cualquier endpoint de la API sin importar su rol. Un cliente podría borrar registros, un mecánico podría ver reportes financieros. Esta fase cierra esos huecos en dos capas:

- **Capa 1 — RBAC (Role-Based Access Control):** Middleware que verifica el rol antes de ejecutar el controlador.
- **Capa 2 — Rate Limiting:** Límites de peticiones por usuario/IP para prevenir abuso y ataques de fuerza bruta.
- **Capa 3 — Form Requests:** Validación estricta de entradas en los endpoints más críticos.

---

### 6.A — Middleware de Roles (`CheckRole`)

#### [NEW] `app/Http/Middleware/CheckRole.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Uso en rutas: ->middleware('role:Administrador,Recepcionista')
     * Acepta uno o más roles separados por coma.
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Cargar relación si no está cargada
        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        $rolNombre = $user->rol?->nombre;

        if (!in_array($rolNombre, $roles)) {
            return response()->json([
                'message' => 'Acceso denegado. Tu rol no tiene permiso para esta operación.',
                'rol_actual' => $rolNombre,
                'roles_requeridos' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
```

#### [MODIFY] `bootstrap/app.php` — Registrar alias del middleware

```php
// En la sección de middlewareAliases:
$middleware->alias([
    'auth.session' => \App\Http\Middleware\AuthenticateSession::class, // ya existe
    'role'         => \App\Http\Middleware\CheckRole::class,           // nuevo
]);
```

---

### 6.B — Tabla de Permisos por Endpoint

| Endpoint | Admin | Recepcionista | Mecánico | Cliente |
|---|---|---|---|---|
| `GET /api/clientes` | ✅ | ✅ | ✅ | ❌ |
| `POST /api/clientes` | ✅ | ✅ | ❌ | ❌ |
| `PUT/DELETE /api/clientes` | ✅ | ✅ | ❌ | ❌ |
| `POST /api/clientes/con-usuario` | ✅ | ✅ | ❌ | ❌ |
| `GET /api/clientes/buscar` | ✅ | ✅ | ❌ | ❌ |
| `GET/POST /api/citas` | ✅ | ✅ | ✅ (solo GET) | ❌ |
| `PUT/DELETE /api/citas` | ✅ | ✅ | ❌ | ❌ |
| `GET /api/citas/hoy` | ✅ | ✅ | ❌ | ❌ |
| `GET /api/ordenes-trabajo` | ✅ | ✅ | ✅ | ❌ |
| `POST /api/ordenes-trabajo` | ✅ | ✅ | ❌ | ❌ |
| `PUT/PATCH /api/ordenes-trabajo/{id}` | ✅ | ✅ | ✅ | ❌ |
| `DELETE /api/ordenes-trabajo/{id}` | ✅ | ❌ | ❌ | ❌ |
| `POST /api/ordenes-trabajo/{id}/aceptar` | ✅ | ❌ | ✅ | ❌ |
| `PATCH /api/ordenes-trabajo/{id}/estado` | ✅ | ✅ | ✅ | ❌ |
| `GET/POST /api/diagnosticos` | ✅ | ❌ | ✅ | ❌ |
| `GET /api/detalles-orden` | ✅ | ✅ | ✅ | ❌ |
| `POST /api/detalles-orden` | ✅ | ❌ | ✅ | ❌ |
| `GET /api/repuestos` | ✅ | ✅ | ✅ | ❌ |
| `POST/PUT/DELETE /api/repuestos` | ✅ | ❌ | ❌ | ❌ |
| `GET /api/inventario` | ✅ | ✅ | ✅ | ❌ |
| `POST/PUT/DELETE /api/inventario` | ✅ | ❌ | ❌ | ❌ |
| `POST /api/movimientos-inventario` | ✅ | ❌ | ✅ | ❌ |
| `GET/POST /api/pagos` | ✅ | ✅ | ❌ | ❌ |
| `DELETE /api/pagos` | ✅ | ❌ | ❌ | ❌ |
| `GET/POST /api/facturas` | ✅ | ✅ | ❌ | ❌ |
| `DELETE /api/facturas` | ✅ | ❌ | ❌ | ❌ |
| `GET /api/reportes` | ✅ | ❌ | ❌ | ❌ |
| `GET/POST /api/usuarios` | ✅ | ❌ | ❌ | ❌ |
| `GET/POST /api/roles` | ✅ | ❌ | ❌ | ❌ |
| `GET/POST /api/permisos` | ✅ | ❌ | ❌ | ❌ |
| `GET /api/mecanicos` | ✅ | ✅ | ✅ | ❌ |
| `POST/PUT/DELETE /api/mecanicos` | ✅ | ❌ | ❌ | ❌ |
| `GET /api/mecanicos/con-carga` | ✅ | ✅ | ❌ | ❌ |
| `PATCH /api/mecanicos/mi-disponibilidad` | ✅ | ❌ | ✅ | ❌ |
| `/api/client/*` | ❌ | ❌ | ❌ | ✅ |
| `GET /api/notificaciones` | ✅ | ✅ | ✅ | ❌ |
| `/api/client/notificaciones` | ❌ | ❌ | ❌ | ✅ |
| `GET /api/notificaciones/no-leidas` | ✅ | ✅ | ✅ | ❌ |
| `POST /api/notificaciones` | ✅ | ✅ | ❌ | ❌ |

---

### 6.C — Reestructura de `routes/web.php` con grupos de roles

> [!WARNING]
> **Bug de rutas duplicadas (resuelto en esta versión).** El patrón anterior usaba `apiResource('clientes')->except(['destroy'])` y `apiResource('citas')->except(['destroy'])` en el grupo Admin+Recepcionista. Eso registra también el verbo `GET index` (e.g. `GET /api/clientes`), que Laravel resuelve **antes** de llegar al grupo de 3 roles donde el Mecánico debería tener acceso. El resultado: el Mecánico recibe `403` pese a que la tabla de permisos lo permite.
>
> **Solución aplicada:** se usa `->only(['store', 'update'])` en los recursos `clientes` y `citas` dentro del grupo Admin+Recepcionista — solo quedan escrituras restringidas. El `GET index` (y `show`) ya está declarado en el grupo de 3 roles, que es el más permisivo de los dos y debe definirse **primero** o en la posición correcta de evaluación. La rutas de escritura exclusiva (store/update) van después.

El grupo `Route::middleware('auth.session')` actual se divide en sub-grupos.

**Orden de declaración importante:** Los grupos más específicos (Admin-only) se declaran al final para no capturar rutas antes de los grupos más amplios. Laravel evalúa las rutas en el orden en que se registran; los GET más permisivos deben declararse **antes** de las rutas que los subconjuntos de roles llegan a ver.

```php
Route::middleware('auth.session')->group(function () {

    // ── Admin + Recepcionista + Mecánico (GET compartidos) ───────
    // DEBE ir antes que los grupos más restrictivos para que el GET
    // no quede sombreado por el apiResource del grupo restringido.
    Route::middleware('role:Administrador,Recepcionista,Mecanico')->group(function () {
        Route::get('ordenes-trabajo',        [OrdenTrabajoController::class, 'index']);
        Route::get('ordenes-trabajo/{id}',   [OrdenTrabajoController::class, 'show']);
        Route::get('clientes',               [ClienteController::class, 'index']);
        Route::get('clientes/{id}',          [ClienteController::class, 'show']);
        Route::get('citas',                  [CitaController::class, 'index']);
        Route::get('citas/{id}',             [CitaController::class, 'show']);
        Route::get('repuestos',              [RepuestoController::class, 'index']);
        Route::get('inventario',             [InventarioController::class, 'index']);
        Route::get('sucursales',             fn() => response()->json(Sucursal::all()));
        Route::get('especialidades',         fn() => response()->json(Especialidad::all()));
        Route::get('mecanicos',              [MecanicoController::class, 'index']);
    });

    // ── Admin + Recepcionista (escrituras y rutas exclusivas) ────
    // Solo store/update para clientes y citas — el index ya está
    // en el grupo de 3 roles de arriba.
    Route::middleware('role:Administrador,Recepcionista')->group(function () {
        // clientes: solo escritura (index/show declarados arriba)
        Route::post('clientes',             [ClienteController::class, 'store']);
        Route::put('clientes/{id}',         [ClienteController::class, 'update']);
        Route::patch('clientes/{id}',       [ClienteController::class, 'update']);
        Route::post('clientes/con-usuario', [ClienteController::class, 'storeConUsuario']);
        Route::get('clientes/buscar',       [ClienteController::class, 'buscar']);
        // citas: solo escritura (index/show declarados arriba)
        Route::post('citas',                [CitaController::class, 'store']);
        Route::put('citas/{id}',            [CitaController::class, 'update']);
        Route::patch('citas/{id}',          [CitaController::class, 'update']);
        Route::get('citas/hoy',             [CitaController::class, 'hoy']);
        // recursos completos (sin conflicto de GET en rutas más amplias)
        Route::apiResource('vehiculos',     VehiculoController::class);
        Route::get('/modelos-vehiculo',     fn() => response()->json(ModeloVehiculo::with('marca')->get()));
        Route::apiResource('tipos-servicio', TipoServicioController::class);
        Route::apiResource('servicios',     ServicioController::class);
        Route::apiResource('proveedores',   ProveedorController::class);
        Route::apiResource('pagos',         PagoController::class)->only(['index','store','show','update']);
        Route::apiResource('facturas',      FacturaController::class)->only(['index','store','show','update']);
        Route::get('mecanicos/con-carga',   [MecanicoController::class, 'conCarga']);
        Route::apiResource('notificaciones', NotificacionController::class)->only(['index','store','show','update']);
        Route::get('notificaciones/no-leidas', [NotificacionController::class, 'noLeidas']);
        // ordenes-trabajo: escrituras permitidas a Recepcionista
        Route::post('ordenes-trabajo',      [OrdenTrabajoController::class, 'store']);
        Route::put('ordenes-trabajo/{id}',  [OrdenTrabajoController::class, 'update']);
        Route::patch('ordenes-trabajo/{id}/estado', [OrdenTrabajoController::class, 'updateEstado']);
    });

    // ── Admin + Mecánico (acciones operativas del mecánico) ──────
    Route::middleware('role:Administrador,Mecanico')->group(function () {
        Route::post('ordenes-trabajo/{id}/aceptar', [OrdenTrabajoController::class, 'aceptar']);
        Route::patch('mecanicos/mi-disponibilidad', [MecanicoController::class, 'toggleDisponible']);
        Route::apiResource('diagnosticos',   DiagnosticoController::class);
        Route::apiResource('detalles-orden', DetalleOrdenTrabajoController::class);
        Route::post('movimientos-inventario', [MovimientoInventarioController::class, 'store']);
    });

    // ── Solo Administrador ──────────────────────────────────────
    Route::middleware('role:Administrador')->group(function () {
        Route::apiResource('usuarios',  UsuarioController::class);
        Route::apiResource('roles',     RolController::class);
        Route::get('roles/{id}/permisos', [RolController::class, 'getRolPermisos']);
        Route::apiResource('permisos',  PermisoController::class);
        Route::get('reportes/{id}/data',     [ReporteController::class, 'data']);
        Route::get('reportes/{id}/download', [ReporteController::class, 'download']);
        Route::apiResource('reportes',  ReporteController::class);
        Route::apiResource('mecanicos', MecanicoController::class)->only(['store','update','destroy']);
        // DELETE exclusivos de admin:
        Route::delete('clientes/{id}',        [ClienteController::class, 'destroy']);
        Route::delete('citas/{id}',           [CitaController::class, 'destroy']);
        Route::delete('ordenes-trabajo/{id}', [OrdenTrabajoController::class, 'destroy']);
        Route::delete('pagos/{id}',           [PagoController::class, 'destroy']);
        Route::delete('facturas/{id}',        [FacturaController::class, 'destroy']);
        Route::delete('repuestos/{id}',       [RepuestoController::class, 'destroy']);
        Route::delete('inventario/{id}',      [InventarioController::class, 'destroy']);
    });

    // ── Solo Cliente (portal propio) ─────────────────────────────
    Route::middleware('role:Cliente')->prefix('client')->group(function () {
        Route::get('/profile',           [ClientePortalController::class, 'getProfile']);
        Route::put('/profile',           [ClientePortalController::class, 'updateProfile']);
        Route::get('/estado-vehiculo',   [ClientePortalController::class, 'getEstadoVehiculo']);
        Route::get('/notificaciones',    [ClientePortalController::class, 'getNotificacionesCliente']);
        Route::get('/historial',         [ClientePortalController::class, 'getHistorial']);
        // Endpoints legacy (mantener por compatibilidad):
        Route::get('/catalogo',          [ClientePortalController::class, 'getCatalogo']);
        Route::get('/solicitudes',       [ClientePortalController::class, 'getSolicitudes']);
    });

});
```

> [!NOTE]
> El `DELETE` de clientes lo maneja solo el Admin. La Recepcionista puede crear/editar pero no eliminar clientes. Cambiar si el usuario decide lo contrario.

---

### 6.D — Rate Limiting (Protección del Servidor)

#### [MODIFY] `routes/web.php` — Actualizar y ampliar RateLimiters

> [!IMPORTANT]
> **Los limiters de 6.D se aplican al grupo ya definido en 6.C — no se duplican rutas.**
> El bloque de definición de `RateLimiter::for(...)` va en el `boot()` del `AppServiceProvider` (o en la sección de bootstrapping de `web.php` antes del primer `Route::...`). Luego, el throttle se encadena como middleware adicional sobre el **mismo** `Route::middleware('auth.session')->group(...)` de 6.C — no se abre un nuevo `Route::prefix('api')->group(...)` independiente. Hacerlo en un bloque separado duplicaría todas las rutas.

**Paso 1 — Definir los limiters** (en `AppServiceProvider::boot()` o al inicio de `web.php`):

```php
// ── Rate Limiters ────────────────────────────────────────────────
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

// 1. Login — ya existe, mejorar respuesta informativa
RateLimiter::for('login-attempts', function (Request $request) {
    return Limit::perMinute(5)
        ->by($request->ip())
        ->response(function () {
            return response()->json([
                'message'     => 'Demasiados intentos de inicio de sesión. Intenta de nuevo en 1 minuto.',
                'retry_after' => 60,
            ], 429);
        });
});

// 2. API General — 120 req/min por usuario autenticado (o IP si no autenticado)
RateLimiter::for('api-general', function (Request $request) {
    return Limit::perMinute(120)
        ->by($request->user()?->idUsuario ?: $request->ip());
});

// 3. Escrituras — POST/PUT/PATCH/DELETE: 30/min por usuario
RateLimiter::for('api-escritura', function (Request $request) {
    if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        return Limit::perMinute(30)
            ->by($request->user()?->idUsuario ?: $request->ip());
    }
    return Limit::none();
});

// 4. Descargas — 10/min por usuario
RateLimiter::for('descargas', function (Request $request) {
    return Limit::perMinute(10)
        ->by($request->user()?->idUsuario ?: $request->ip());
});
```

**Paso 2 — Aplicar throttle sobre el grupo existente de 6.C** (modificar solo la cabecera del grupo, no duplicar rutas):

```php
// Login (ya tiene throttle, mejorar respuesta)
Route::post('/api/login', [AuthController::class, 'login'])
    ->middleware('throttle:login-attempts');

// El grupo 'auth.session' de 6.C se amplía con los throttles.
// Es la MISMA cadena de grupo — solo se agregan middlewares:
Route::middleware(['auth.session', 'throttle:api-general', 'throttle:api-escritura'])
    ->group(function () {
        // ... aquí van exactamente los sub-grupos de 6.C (Admin-only, Admin+Recep, etc.) ...
        // NO repetir las rutas — este bloque ES el de 6.C con throttle añadido.
    });

// Descarga de reportes con límite propio (se superpone al registro de Admin-only de 6.C)
Route::get('api/reportes/{id}/download', [ReporteController::class, 'download'])
    ->middleware(['auth.session', 'role:Administrador', 'throttle:descargas']);
```

---

### 6.E — Form Requests (Validación de Entradas)

Prioridad alta — endpoints más críticos:

#### [NEW] `app/Http/Requests/StoreOrdenTrabajoRequest.php`
```php
// rules():
'idCliente'          => 'required|integer|exists:cliente,idCliente',
'idVehiculo'         => 'required|integer|exists:vehiculo,idVehiculo',
'idMecanico'         => 'nullable|integer|exists:mecanico,idMecanico',
'servicioSolicitado' => 'required|string|max:500',
'sucursal'           => 'nullable|string|max:100',
'horaFinEstimada'    => 'nullable|date|after:now',
```

#### [NEW] `app/Http/Requests/StoreClienteConUsuarioRequest.php`
```php
// rules():
'nombreCompleto' => 'required|string|max:150',
'ci_nit'         => 'required|string|max:20|unique:cliente,ci_nit',
'telefono'       => 'required|string|max:20',
'email'          => 'required|email|max:100|unique:usuario,email',
'direccion'      => 'nullable|string|max:255',
```

#### [NEW] `app/Http/Requests/StoreCitaRequest.php`
```php
// rules():
'idCliente'  => 'required|integer|exists:cliente,idCliente',
'idVehiculo' => 'required|integer|exists:vehiculo,idVehiculo',
'idMecanico' => 'nullable|integer|exists:mecanico,idMecanico',
'fecha'      => 'required|date|after_or_equal:today',
'hora'       => 'required|date_format:H:i',
'motivo'     => 'nullable|string|max:255',
// Validar solapamiento (lógica en controller):
// WHERE fecha = $fecha AND hora = $hora AND idMecanico = $idMecanico → error si existe
```

#### [NEW] `app/Http/Requests/UpdateEstadoOrdenRequest.php`
```php
// rules():
'etapa' => 'required|in:Recibido,Diagnóstico,En reparación,Terminado',
```

---

### 6.F — Manejo de error 403 en el Frontend

En `dashboard.js` / `recepcion.js` / `mecanico.js`, la función genérica de fetch debe manejar el 403:

```javascript
async function apiFetch(url, options = {}) {
    const res = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        ...options
    });

    if (res.status === 403) {
        showToast('No tienes permiso para realizar esta acción.', 'error');
        throw new Error('Acceso denegado (403)');
    }
    if (res.status === 429) {
        const data = await res.json().catch(() => ({}));
        showToast(data.message || 'Demasiadas peticiones. Espera un momento.', 'warning');
        throw new Error('Rate limit (429)');
    }
    if (res.status === 401) {
        // Sesión expirada → volver al login
        location.reload();
        throw new Error('No autenticado (401)');
    }

    return res;
}
```

---

### 6.G — Preguntas Abiertas (no bloquean implementación)

> [!IMPORTANT]
> **¿Puede la Recepcionista eliminar clientes y citas?**
> El plan actual solo permite `DELETE` al Administrador. Si la Recepcionista también debe poder eliminar, agregar `Recepcionista` al grupo de DELETE correspondiente en `routes/web.php`.

> [!NOTE]
> **Sidebar del Administrador para roles mixtos:** El routing SPA ya oculta el sidebar según el rol (ver Fase 4.B). No hay trabajo adicional aquí — el menú lateral del Admin nunca se muestra a Recepcionistas ni Mecánicos.

> [!NOTE]
> **Mecánico que intenta acceder al dashboard Admin vía URL:** Como todo es SPA y no hay rutas web separadas, no es posible navegar directamente. El único punto de entrada es el login, que ya routea por rol. El middleware RBAC en las APIs es la barrera real.

---

## FASE 5 — Encargado *(no implementar todavía)*

Awaiting explicit confirmation. Structure reserved:
- `#encargadoPortal` div en `welcome.blade.php`
- `partials/encargado.blade.php`
- `public/js/encargado.js`
- Tabs: Dashboard cobranza / Registrar pago / Facturación / Reportes (único con gráficos)
- Requiere agregar rol `Encargado` a los grupos RBAC en Fase 6 cuando se implemente

---

## Orden de ejecución definitivo

```
── INFRAESTRUCTURA BASE ─────────────────────────────────────────────
 1. FASE 0.1  — Migraciones: add_campos_operativos a ordentrabajo y mecanico
 2. FASE 0.2  — Seed roles Recepcionista y Mecánico en web.php
 3. FASE 0.3  — Actualizar modelos: OrdenTrabajo, Mecanico, Cliente
 4. FASE 0.4  — Crear public/css/operativo.css
 5. FASE 0.5  — Actualizar welcome.blade.php (CSS + partials + scripts)

── SEGURIDAD (hacer antes de conectar los portales) ─────────────────
 6. FASE 6.A  — Crear CheckRole middleware + registrar alias en bootstrap/app.php
 7. FASE 6.D  — Ampliar Rate Limiters en web.php
 8. FASE 6.C  — Reestructurar rutas con grupos de roles (reemplaza grupo actual)
 9. FASE 6.E  — Crear Form Requests para endpoints críticos
10. FASE 6.F  — Agregar apiFetch() con manejo de 403/429/401 en dashboard.js

── ROUTING SPA ───────────────────────────────────────────────────────
11. FASE 4.A  — Verificar AuthController@login incluye rol en respuesta
12. FASE 4.B  — Agregar activarPortalPorRol() en dashboard.js

── PORTAL RECEPCIONISTA ─────────────────────────────────────────────
13. FASE 1.A  — Métodos backend: storeConUsuario, buscar, hoy, conCarga, store(orden), aceptar, updateEstado, noLeidas
14. FASE 1.B  — Crear partials/recepcion.blade.php
15. FASE 1.C  — Crear public/js/recepcion.js

── PORTAL MECÁNICO ──────────────────────────────────────────────────
16. FASE 2.A  — Métodos backend: toggleDisponible, index con parámetros
17. FASE 2.B  — Crear partials/mecanico.blade.php
18. FASE 2.C  — Crear public/js/mecanico.js

── PORTAL CLIENTE (mejoras) ─────────────────────────────────────────
19. FASE 3.A  — Métodos backend: getEstadoVehiculo, getNotificacionesCliente, getHistorial
20. FASE 3.B  — Rediseñar client_dashboard.blade.php
21. FASE 3.C  — Agregar estilos en client.css
```

---

## Criterios de aceptación (checklist de verificación)

### Portales y Routing
- [ ] No aparece sidebar en ninguna vista de rol no-admin
- [ ] Login como `recepcion1` → muestra `#recepcionPortal`, no dashboard
- [ ] Login como `mecanico1` → muestra `#mecanicoPortal`, no dashboard
- [ ] Login como `cliente1` → muestra `#clientPortal`, sin botón de registro

### Flujo de negocio
- [ ] Crear cliente desde Recepcionista → genera usuario+contraseña, muestra credencial UNA sola vez
- [ ] Crear orden "Dejar disponible" → aparece en bandeja del Mecánico
- [ ] Mecánico acepta orden → desaparece de "Disponibles", aparece en "Mis asignadas"
- [ ] Mecánico marca "Terminado" → `horaFinReal = now()` + notificación al cliente en mismo request
- [ ] Cliente ve "Ya puedes recoger tu vehículo" con glow verde en su portal

### Diseño
- [ ] `.status-badge[data-status]` muestra colores correctos para cada estado
- [ ] Portal Mecánico usable en viewport 768px (tablet)
- [ ] `operativo.css` cargado después de `dashboard.css` sin conflictos en `:root`

### Seguridad — RBAC
- [ ] `GET /api/usuarios` con sesión de Mecánico → responde `403`
- [ ] `DELETE /api/facturas/1` con sesión de Recepcionista → responde `403`
- [ ] `GET /api/ordenes-trabajo` con sesión de Mecánico → responde `200`
- [ ] `POST /api/ordenes-trabajo/{id}/aceptar` con sesión de Recepcionista → responde `403`
- [ ] `GET /api/client/estado-vehiculo` con sesión de Mecánico → responde `403`
- [ ] `GET /api/client/estado-vehiculo` con sesión de Cliente → responde `200`
- [ ] `GET /api/reportes` con sesión de Recepcionista → responde `403`

### Seguridad — Rate Limiting
- [ ] 6 intentos de login incorrectos desde la misma IP → responde `429` con mensaje informativo
- [ ] Response `429` incluye campo `retry_after` o mensaje claro
- [ ] El frontend muestra toast de error al recibir `403` o `429` (no pantalla en blanco)

