# PLAN EN 3 ETAPAS — Saneamiento de Kappe (con modelo por tarea)
 
> Aprobado por Pablo el 24-nov-2026. Estado: **EN PAUSA** hasta que Pablo retome el proyecto.
> **Precedencia:** este documento manda sobre `TAREAS.md` y `SPECS.md` §10 en lo que toca a etapas, ramas, pruebas, despliegue y modelo. `TAREAS.md` sigue siendo el catálogo con el detalle y los criterios de cada tarea (IDs T…); `SPECS.md`, el alcance y los datos que no se renombran.
 
---
 
## Reglas
 
1. Confirmar con Pablo cada cambio que no sea pequeño antes de hacerlo.
2. Todo se desarrolla y se prueba **en local** (LocalWP, PHP 8.4 y 8.5). Claude no toca producción.
3. **El despliegue final lo hace Pablo.** Claude solo entrega el paquete y las instrucciones (E3.5).
4. Cada etapa termina con una **puerta**: no se pasa a la siguiente sin la aprobación de Pablo.
5. Etapa 2: **una rama Git por componente**. Al cerrar cada componente: verificación mínima (las 6 páginas cargan, `debug.log` vacío) y entrada en `CHANGELOG.md`.
6. Cloudflare y SEO se revisan **después** del despliegue final (ver el último apartado).
---
 
## Cuándo cambiar de modelo
 
- **Modelo por defecto: Sonnet.** Cada tarea de las tablas trae su modelo; se usa ese.
- **Subir a Opus** cuando: la tarea está marcada Opus; o Sonnet falla dos intentos seguidos en la misma tarea; o la tarea toca datos guardados, seguridad o algo irreversible; o las pruebas dan resultados contradictorios.
- **Haiku** solo en las tareas marcadas (mecánicas y muy acotadas). Si falla una vez, repetir con Sonnet.
- **Cuándo cambiar:** al empezar la tarea, con `/model`, y haciendo `/clear` entre componentes. Evitar cambiar de modelo a mitad de una conversación larga: se pierde la caché y se vuelve a procesar todo el contexto.
- Volver a Sonnet al terminar la tarea de Opus.
---
 
## Cómo leer el tema una sola vez (E1.5 → `INVENTARIO.md`)
 
Objetivo: que ninguna sesión posterior tenga que leer el tema completo (~550 KB de código propio). Solo se leen los archivos que la tarea toca, guiándose por `INVENTARIO.md`.
 
**Paso 1 — Scripts (no el modelo) extraen los datos.** Modelo: **Sonnet** (escribe los scripts). Los scripts guardan su salida en `inventario/raw/` y el modelo lee esas salidas, que son pequeñas, no el código:
- Lista de archivos con tamaño, marcada "se conserva" o "se elimina" según SPECS (PHPMailer, OAuth, notificador, pricing tables, archivos sueltos).
- `grep` de `add_action`, `add_filter`, `add_shortcode`, `register_*`, `weblusive_get_option`, `get_post_meta`, `update_post_meta`, `wp_ajax`, `$wpdb` → `archivo:línea:coincidencia`.
- Resultados de `php -l` y de PHPCompatibilityWP por archivo.
- Consultas WP-CLI/SQL de T1.4 (shortcodes en `post_content`, widgets activos, CPT `weblusive_slider`, plantillas, metas, claves de `weblusive_options`).
**Paso 2 — Una sola pasada de lectura.** Modelo: **Sonnet**, en una sola sesión. Lee únicamente los archivos que se conservan, en orden. No lee el código que se elimina ni las librerías minificadas o de terceros (Isotope, prettyPhoto, Bootstrap…): de esas solo anota la versión. Después de leer cada archivo escribe de inmediato su entrada en `INVENTARIO.md`, para no tener que releerlo:
 
`ruta · propósito (1 línea) · hooks, shortcodes y opciones que usa · riesgos · tareas que lo tocan (IDs) · se conserva / se elimina`
 
**Paso 3 — Cerrar y proteger el ahorro.**
- Commit de `INVENTARIO.md` en el repo.
- Añadir a `CLAUDE.md` del repo: "No leas el tema completo. Lee primero `INVENTARIO.md` y abre solo los archivos listados para la tarea. Si descubres un dato nuevo, actualiza la entrada correspondiente."
- `/clear`.
**Paso 4 — Quien planifica lee poco.** E1.10 (Opus) lee solo `INVENTARIO.md`, `SPECS.md` y `TAREAS.md`.
 
**Por qué Sonnet y no Opus:** el paso 2 es comprensión y resumen de mucho texto, no razonamiento difícil; ahí Opus gastaría más sin aportar. Opus entra en E1.10, que es corto y de alto criterio. **Por qué no Haiku:** el resumen de código con riesgos necesita más criterio que un `grep`.
 
---
 
## Etapa 1 — Revisión general y plan de trabajo
 
La Fase 0 ya está hecha (archivos expuestos borrados, Cloudflare con rate limiting de login).
 
| ☐ | ID | Tarea | Antes | Modelo |
|---|---|---|---|---|
| ☐ | E1.1 | LocalWP con BD y uploads de producción, PHP 8.4 y 8.5 | T1.1 | Sonnet |
| ☐ | E1.2 | Repo Git (commit 1 = tema 1.6 de producción; commit 2 = base 2.1 + `es_ES`) y `CAMBIOS-PRODUCCION.md` | T1.2 | Sonnet |
| ☐ | E1.3 | Revisar las diferencias producción-vs-2.1 pendientes: `script.js`, `prettyPhoto.js`, `addons/paginajax`, `es_ES.po` | ESTADO #4 | Sonnet |
| ☐ | E1.4 | Confirmar la causa de la caída de la 2.1 (`table_process.php:99`) | T1.3 | Sonnet |
| ☐ | E1.5a | Scripts de extracción (paso 1 de `INVENTARIO.md`) | T1.4 | Sonnet |
| ☐ | E1.5b | Lectura única y `INVENTARIO.md` (pasos 2 y 3) | T1.4 | Sonnet |
| ☐ | E1.6 | Compatibilidad 1.6→2.1 con capturas a 1440 px y 390 px. Si algo no es compatible, parar y avisar a Pablo | T1.5 | Sonnet; **Opus** solo si aparece una incompatibilidad |
| ☐ | E1.7 | Lighthouse de línea base (móvil y escritorio, 6 páginas, sin caché de página) | T1.6 | Haiku |
| ☐ | E1.8 | Staging medible en DreamHost (contraseña y `noindex`); TTFB y Lighthouse con y sin Wordfence | T1.7 | Sonnet |
| ☐ | E1.9 | Decisiones de Pablo pendientes de SPECS §9 que afectan al tema (editor clásico o de bloques, widgets activos) | SPECS §9 | Sonnet, en el chat |
| ☐ | E1.10 | **Plan final:** componentes, orden, dependencias y estimación recalculada. Lee solo `INVENTARIO.md`, `SPECS.md` y `TAREAS.md` | nuevo | **Opus** (una vez, salida corta) |
 
**Puerta 1:** Pablo aprueba el plan final de E1.10 antes de programar.
 
---
 
## Etapa 2 — Desarrollo por componente
 
Orden: A → B → C → D → E. D2 y D4 van después de C1 porque dependen de `functions.php`.
 
| ☐ | ID | Tarea | Antes | Modelo |
|---|---|---|---|---|
| | | **A · Código muerto** (rama `componente/a-codigo-muerto`) | | |
| ☐ | E2.A1 | Quitar `sendmail`, `contact-form`, PHPMailer, notificador, pricing tables, `table_wizard`, archivos sueltos, el `include` de `pricing-tables.php` y el submenú "Pricing Tables" | T2.1, T2.6 | Sonnet |
| ☐ | E2.A2 | Constructor del breadcrumb (`__construct`) | T2.8 | Haiku |
| | | **B · Seguridad** (rama `componente/b-seguridad`) | | |
| ☐ | E2.B1 | Endurecer "Cargar más" (REST o `wp_ajax_nopriv_`, lista blanca, `per_page` ≤ 24) | T2.2 | **Opus** |
| ☐ | E2.B2 | Meta boxes: nonce, capacidades, autosave; `save_slide()` no borra metas | T2.4 | **Opus** |
| ☐ | E2.B3 | Importar/exportar opciones en JSON con `current_user_can` | T2.3 | Sonnet |
| ☐ | E2.B4 | Consultas SQL de `helper-functions.php` a APIs de WP | T2.5 | Sonnet |
| ☐ | E2.B5 | Escapar el título de prettyPhoto; iframes de `single.php` por `https` y `youtube-nocookie.com` | T2.9 | Sonnet |
| | | **C · Compatibilidad PHP/WordPress** (rama `componente/c-compatibilidad`) | | |
| ☐ | E2.C1 | `functions.php`: hooks (`after_setup_theme`, `widgets_init`), text domain `kappe`, doble `style.css`, Google Maps por `https` | T2.7 | **Opus** |
| ☐ | E2.C2 | Eliminar Twitter/OAuth y, si E1.5 lo confirma, Flickr y el widget de contacto | T2.6 | Sonnet |
| | | **D · Rendimiento** (rama `componente/d-rendimiento`) | | |
| ☐ | E2.D1 | Eliminar `retina.js` | T3.1 | Haiku |
| ☐ | E2.D2 | Reemplazar `dynamic-styles.php` por CSS en línea con transient; quitar `!important`; integrar los colores de créditos (#F5F8E3 y #34B7AE) | T3.2 | **Opus** |
| ☐ | E2.D3 | Quitar `opacity: 0` y el preloader GIF | T3.3 | Sonnet |
| ☐ | E2.D4 | Altura del menú lateral solo con CSS (`100dvh`, `flow-root`) | T3.4 | **Opus** |
| ☐ | E2.D5 | Fuentes: solo los pesos usados y `display=swap` | T3.5 | Haiku |
| ☐ | E2.D6 | Carga condicional y `defer`; quitar Modernizr, nicescroll y Migrate 1.0 | T3.6 | Sonnet |
| ☐ | E2.D7 | `eager` en la primera fila y `fetchpriority="high"` en la primera imagen | T3.7 | Haiku |
| | | **E · SEO del lado del tema** (rama `componente/e-seo-html`) | | |
| ☐ | E2.E1 | Un solo `<h1>`; `title-tag`; `alt` del portafolio; tarjetas con `<a href>` real | T3B.1, T3B.3, T3B.5, T3B.6 | Sonnet |
| ☐ | E2.E2 | Viewport sin `maximum-scale=1`; `wp_body_open()`; `lang` | T3B.2, T3B.4 | Haiku |
| ☐ | E2.E3 | Contraste AA y foco visible | T3B.7 | Sonnet |
 
**Puerta 2:** Pablo revisa el resumen de `CHANGELOG.md` antes de pasar a las pruebas.
 
---
 
## Etapa 3 — Prueba de funcionamiento (todo en local)
 
| ☐ | ID | Tarea | Antes | Modelo |
|---|---|---|---|---|
| ☐ | E3.1 | PHPCompatibilityWP en 0 errores y `debug.log` vacío en PHP 8.4 y 8.5 | T2.10, T4.1 | Haiku para ejecutar; Sonnet para corregir lo que falle |
| ☐ | E3.2 | Pruebas funcionales: las 6 páginas, paginación del blog hasta el final, guardar una página y un proyecto conserva sus opciones, el panel del tema guarda e importa | "Cómo probar" | Sonnet |
| ☐ | E3.3 | Comparación visual contra las capturas de E1.6 (1440 px y 390 px); solo se aceptan diferencias que sean mejoras acordadas | T4.2 | Sonnet |
| ☐ | E3.4 | Lighthouse después contra E1.7 y prueba de teclado en el home y el blog | T4.3 | Haiku (medición); Sonnet (análisis) |
| ☐ | E3.5 | Paquete para el despliegue: carpeta `kappe-saneado`, instrucciones paso a paso (respaldo, subir, activar, purgar Cloudflare, qué revisar), cómo volver atrás y el retiro del CSS adicional del Personalizador. **El despliegue lo hace Pablo** | T4.4, T4.5 | Sonnet |
 
**Puerta 3:** Pablo da por buena la etapa 3 y despliega.
 
---
 
## Después del despliegue final (fuera de las 3 etapas)
 
Se retoma con Pablo una vez desplegado el tema saneado. Modelo sugerido: Sonnet, en el chat, con Claude in Chrome para el panel de Cloudflare.
 
- ☐ **Cloudflare:** decidir `xmlrpc.php` (Pablo cree que no se usa: bloquear con regla personalizada y observar); registros DNS "DNS only" que exponen la IP de origen; Bot Fight Mode vs protección anti-bots de DreamHost; Wordfence vs Cloudflare (S.5).
- ☐ **SEO:** `/works/` caso por caso con datos de Search Console (S.1); sitemap de Yoast y `robots.txt` (S.2); 6 enlaces 404 y 3 redirecciones (S.3); Yoast → The SEO Framework con respaldo y comparación antes/después (S.4).
- Recordar **purgar la caché de Cloudflare** tras cada cambio de HTML.
 
