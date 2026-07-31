# Paleta de Colores - Pantalla de Bienvenida
### Taller Automotriz · SisGest Pro

---

## Fondo Principal (Dark Background)

El fondo de la pantalla usa un sistema de tres capas combinadas:

| Nombre | Valor HEX | Rol |
|---|---|---|
| **Fondo Base** | `#04100e` | Color mas oscuro, fondo profundo |
| **Fondo Medio** | `#071613` | Fondo intermedio del degradado |
| **Fondo Claro** | `#0e2b28` | Borde derecho del fondo principal |

---

## Colores de Acento (Brand Colors)

| Nombre | Valor HEX | RGB | Rol en el diseno |
|---|---|---|---|
| Verde Lima (Primary) | `#b6f24a` | 182, 242, 74 | Botones principales, texto resaltado, iconos activos |
| Cian Turquesa (Secondary) | `#22d3c5` | 34, 211, 197 | Degradados, destellos, acento secundario |
| Verde Lima Suave | `#d7ff96` | 215, 255, 150 | Texto de la marca en navbar |

---

## Colores de Texto

| Nombre | Valor HEX | Clase Bootstrap | Uso |
|---|---|---|---|
| Texto Primario | `#ffffff` | text-white | Titulos, texto principal |
| Texto Secundario | `#9db8b0` | text-secondary (custom) | Subtitulos, descripciones |
| Texto Muted | `#5f9c92` | text-muted (custom) | Textos de apoyo en landing |
| Texto Marca | `#d7ff96` | -- | Nombre Taller Automotriz en navbar |

---

## Colores de Estado / Semanticos

| Estado | Valor HEX | Clase Bootstrap | Uso |
|---|---|---|---|
| Exito | `#10b981` | bg-success (override) | Badges activo, abierto |
| Advertencia | `#f59e0b` | bg-warning (override) | Alertas, estados pendientes |
| Error / Peligro | `#ef4444` | bg-danger (override) | Eliminacion, errores |
| Info | `#38bdf8` | bg-info (override) | Iconos decorativos, informacion |

---

## Glassmorphism (Efectos de Cristal)

| Elemento | Fondo | Borde |
|---|---|---|
| Navbar | rgba(15,23,42,0.85) | rgba(255,255,255,0.1) |
| Tarjeta Glass Hero | rgba(255,255,255,0.05) | rgba(120,180,170,0.25) |
| Badge Premium | rgba(182,242,74,0.15) | rgba(182,242,74,0.3) |
| Boton Ingresar | rgba(255,255,255,0.10) | rgba(255,255,255,0.2) |
| Stat Cards | rgba(0,0,0,0.30) | rgba(120,180,170,0.25) |

---

## Orbs / Halos de Luz (Glow Effects)

| Orb | Color | Posicion |
|---|---|---|
| Orb 1 (verde) | rgba(182,242,74,0.4) | Superior izquierda |
| Orb 2 (cyan) | rgba(34,211,197,0.3) | Inferior derecha |

---

## Degradados Principales

| Nombre | Usado en |
|---|---|
| linear-gradient(100deg, #b6f24a, #22d3c5) | Boton primario, subrayado nav, icono marca |
| linear-gradient(100deg, #fff, #d7ff96) | Nombre Taller Automotriz en navbar |
| linear-gradient(90deg, #b6f24a, #22d3c5) | Hover de links de navegacion |

---

## Clases Bootstrap 5 Usadas (version 5.3.3)

| Clase Bootstrap | Descripcion | Override aplicado |
|---|---|---|
| bg-success | Verde exito | Sobreescrito a #10b981 |
| bg-warning | Amarillo advertencia | Sobreescrito a #f59e0b |
| bg-danger | Rojo peligro | Sobreescrito a #ef4444 |
| bg-opacity-25 | Transparencia de fondo | En badges de estado |
| text-white | Texto blanco | Se usa directamente |
| text-muted | Texto atenuado | Para subtitulos |
| badge | Etiqueta de estado | Combinado con colores custom |

### Como sobrescribir colores Bootstrap:

    :root {
        --bs-success:     #10b981;
        --bs-warning:     #f59e0b;
        --bs-danger:      #ef4444;
        --bs-info:        #38bdf8;
    }

---

## Fuentes Tipograficas

| Fuente | Uso | Fuente |
|---|---|---|
| Outfit (300-900) | Texto general, dashboard, botones | Google Fonts |
| Anton (400) | Titulos hero grandes en la landing | Google Fonts |
| Space Grotesk (400-700) | Navegacion y subtitulos | Google Fonts |
| Inter (300-900) | Dashboard administrativo | Google Fonts |

---

## Sombras y Efectos 3D

| Uso | Valor |
|---|---|
| Boton principal (glow verde) | 0 12px 30px -6px rgba(182,242,74,0.6) |
| Boton hover | 0 20px 40px -6px rgba(182,242,74,0.8) |
| Icono de marca | 0 10px 20px -5px rgba(182,242,74,0.5) |
| Boton Ingresar hover | 0 8px 25px rgba(182,242,74,0.4) |
| Tarjeta Glass | 0 30px 60px -12px rgba(0,0,0,0.5) |
| Navbar | 0 10px 30px -10px rgba(0,0,0,0.5) |

---

Documentacion generada automaticamente · SisGest Pro · Taller Automotriz 2026
