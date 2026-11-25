# PLAN EN 3 ETAPAS — Saneamiento de Kappe (con modelo por tarea)
 
> Aprobado por Pablo el 24-nov-2026; etapa 2 reordenada en E1.10 (24-nov). Estado: **Puerta 1 aprobada (24-nov); EN CURSO — etapa 2** (ver `ESTADO.md`).
> **Precedencia:** este documento manda sobre `TAREAS.md` y `SPECS.md` §10 en lo que toca a etapas, ramas, pruebas, despliegue y modelo. `TAREAS.md` sigue siendo el catálogo con el detalle y los criterios de cada tarea (IDs T…); `SPECS.md`, el alcance y los datos que no se renombran.
 
---
 
## Reglas
 
1. Confirmar con Pablo cada cambio que no sea pequeño antes de hacerlo.
2. Todo se desarrolla y se prueba **en local** (LocalWP, PHP 8.4 y 8.5). Claude no toca producción.
3. **El despliegue final lo hace Pablo.** Claude solo entrega el paquete y las instrucciones (E3.5).
4. Cada etapa termina con una **puerta**: no se pasa a la siguiente sin la aprobación de Pablo.
5. Etapa 2: **una rama Git por componente**. Al cerrar cada componente: verificación mínima (las 6 páginas cargan, `debug.log` vacío) y entrada en `CHANGELOG.md`.
6. Cloudflare y SEO se revisan **después** del despliegue final (ver el último apartado).
7. Staging **solo en local**: no se crean copias del sitio en DreamHost.

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
- `CLAUDE.md` ya contiene la regla: "No leas el tema completo. Lee primero `INVENTARIO.md` y abre solo los archivos listados para la tarea. Si descubres un dato nuevo, actualiza la entrada correspondiente."
- `/clear`.

**Paso 4 — Quien planifica lee poco.** E1.10 (Opus) lee solo `INVENTARIO.md`, `SPECS.md` y `TAREAS.md`.
 
**Por qué Sonnet y no Opus:** el paso 2 es comprensión y resumen de mucho texto, no razonamiento difícil; ahí Opus gastaría más sin aportar. Opus entra en E1.10, que es corto y de alto criterio. **Por qué no Haiku:** el resumen de código con riesgos necesita más criterio que un `grep`.
 
---
 
## Etapa 1 — Revisión general y plan de trabajo
 
La Fase 0 ya está hecha (archivos expuestos borrados, Cloudflare con rate limiting de login).
 
| ☐ | ID | Tarea | Antes | Modelo |
|---|---|---|---|---|
| ☑ | E1.1 | LocalWP con BD y uploads de producción, PHP 8.4 y 8.5 (hecho 24-nov: `wp-content`, tema y WP-CLI coinciden con producción; el sitio corre hoy con PHP 8.5.13 y están instalados 8.2, 8.4 y 8.5; borradas las 12 tablas `wp_*` sobrantes) | T1.1 | Sonnet |
| ☑ | E1.2 | Repo Git (commit 1 = tema 1.6 de producción; commit 2 = base 2.1 + `es_ES`) y `CAMBIOS-PRODUCCION.md` (hecho 24-nov) | T1.2 | Sonnet |
| ☑ | E1.3 | Revisar las diferencias producción-vs-2.1 pendientes: `script.js`, `prettyPhoto.js`, `addons/paginajax`, `es_ES.po` (hecho 24-nov: sin bloqueos) | ESTADO #4 | Sonnet |
| ☑ | E1.4 | Confirmar la causa de la caída de la 2.1 (`table_process.php:99`) (hecho 24-nov: sintaxis `$row_{$i}` inválida en PHP 8; solo falla al abrir la página admin "Pricing Tables") | T1.3 | Sonnet |
| ☑ | E1.5a | Scripts de extracción (paso 1 de `INVENTARIO.md`) (hecho 24-nov: `inventario/scripts/ejecutar-todo.sh` → `inventario/raw/`) | T1.4 | Sonnet |
| ☑ | E1.5b | Lectura única y `INVENTARIO.md` (pasos 2 y 3) (hecho 24-nov: `docs/INVENTARIO.md`; `CLAUDE.md` actualizado) | T1.4 | Sonnet |
| ☑ | E1.6 | Compatibilidad 1.6→2.1 con capturas a 1440 px y 390 px (hecho 24-nov: sin incompatibilidades; 12 capturas en `pruebas/capturas/despues/`). Si algo no es compatible, parar y avisar a Pablo | T1.5 | Sonnet; **Opus** solo si aparece una incompatibilidad |
| ☑ | E1.7 | Lighthouse de línea base (móvil y escritorio, 6 páginas, sin caché de página) (hecho 24-nov con la 1.6: 36 informes en `pruebas/lighthouse/linea-base/`, commit `2db47b7`; capturas de la 1.6 en `pruebas/capturas/antes/`) | T1.6 | Haiku |
| ✗ | E1.8 | ~~Staging medible en DreamHost~~ **Cancelada 24-nov:** no habrá copias del sitio en DreamHost; el staging es solo local. El TTFB real y el efecto de Wordfence se miden después del despliegue | T1.7 | — |
| ☑ | E1.9 | Decisiones de Pablo de SPECS §9 que afectan al tema. **Resueltas 24-nov:** el blog no se usa (no es prioridad); editor clásico (no quitar botones de TinyMCE). Los widgets activos los saca el inventario | SPECS §9 | Sonnet, en el chat |
| ☑ | E1.10 | **Plan final:** componentes, orden, dependencias y estimación recalculada. Lee solo `INVENTARIO.md`, `SPECS.md` y `TAREAS.md` (hecho 24-nov: etapa 2 reordenada abajo; decisiones de Pablo incluidas) | nuevo | **Opus** (una vez, salida corta) |
 
**Puerta 1:** Pablo aprueba el plan final de E1.10 antes de programar. ☑ **Aprobada el 24-nov-2026.**
 
---
 
## Etapa 2 — Desarrollo por componente
 
Orden: A → B → C → D → E, en el orden de las filas (plan final de E1.10, 24-nov). **Sin ramas en paralelo:** cada rama de componente sale de `desarrollo` después de fusionar la anterior (`js/script.js` lo tocan 5 tareas y `functions.php` 7).

**Dependencias:** C1 después de A3 (recibe `functions.php` ya limpio); D6 después de A1 y B1 (quitan sus bloques de `script.js`); D4 y E3 después de D2.

**Verificación al cerrar cada componente:** las 6 páginas cargan, `debug.log` vacío, entrada en `CHANGELOG.md`; en D y E, además, capturas rápidas a 1440 px.

| ☐ | ID | Tarea | Antes | Modelo | Horas |
|---|---|---|---|---|---|
| | | **A · Código muerto** (rama `componente/a-codigo-muerto`) | | | |
| ☑ | E2.A1 | Quitar `sendmail`, `contact-form`, PHPMailer, notificador, pricing tables, `table_wizard`, archivos sueltos, el `include` de `pricing-tables.php` y el submenú "Pricing Tables". **Decisión 24-nov:** también `contact-template.php`, `portfolio_template2.php`, `under-construction.php` y `woocommerce.php` (ninguna página las usa; el contacto es Gravity Forms con la plantilla `default`) y lo que solo ellas usaban (`gmap3`, `jquery.countdown`, su encolado en `functions.php`, las pestañas "Contact settings" y "Under construct." del panel; **se acepta que sus opciones guardadas `contact_*`, `uc_*` y `google_api` se borren**), más las librerías que ningún archivo referencia (`js/jquery.min.js`, `js/jquery.bxslider.js`, `css/font-awesome.css`, `css/magnific-popup.css` y los archivos sobrantes de `js/jplayer/`). **E1.10:** también el bloque del widget de contacto en `script.js`, la llamada huérfana `sneek_update_post_order` en `library/admin-panel/js/main.js` y las funciones sin uso de `helper-functions.php` (`get_page_name_by_ID`, `get_page_ID_by_custom_field_value`, `get_taxonomy_related_posts`, `list_taxonomy`, `alc_trim`, más `get_root_page` y `last_pos`, que solo usan esas; decisión de Pablo) | T2.1, T2.6 | Sonnet | 1–1,5 (hecho 24-nov, ver `ESTADO.md`) |
| ☑ | E2.A2 | ~~Constructor del breadcrumb~~ **Cerrada sin cambios (E1.10, 24-nov):** la 2.1 ya trae `__construct` y el breadcrumb no se usa (su única llamada está comentada en `404.php`); el archivo se queda | T2.8 | — | 0 |
| ☐ | E2.A3 | (antes E2.C2) Eliminar Twitter/OAuth. **Decisión 24-nov:** el sitio no usa widgets, así que `widgets.php` **no se carga** (como en producción) y con él quedan fuera Flickr (incluido su `jflickrfeed` encolado) y el widget de contacto | T2.6 | Sonnet | 0,3 |
| | | **B · Seguridad** (rama `componente/b-seguridad`) | | | |
| ☐ | E2.B1 | Eliminar `ajax-load-more.php`, su llamada en `js/script.js` y el botón "Cargar más"; reemplazar por la paginación nativa de WordPress (`paginate_links()` con `paged`) en `blog-template.php`. Decidido 24-nov: el blog no se usa, así que no se endurece. Al terminar, borrar el puente `~/Projects/wp-load.php` | T2.2 | Sonnet | 1 |
| ☐ | E2.B2 | Meta boxes: nonce, capacidades, autosave; `save_slide()` no borra metas. **E1.10:** también `portfolio_update_post_order` (`post-types.php`) con nonce y `current_user_can`, y el nonce en su llamada de `main.js` | T2.4 | **Opus** | 1,5–2 |
| ☐ | E2.B3 | Importar/exportar opciones en JSON con `current_user_can`. **E1.10:** también `current_user_can` en `weblusive_save_ajax` y en "reset"; conservar el formato guardado (`htmlspecialchars`, SPECS §5) | T2.3 | **Opus** (decisión de Pablo: seguridad y datos guardados) | 1 |
| ☐ | E2.B4 | Consultas SQL de `helper-functions.php` a APIs de WP (tras A1 quedan solo `get_page_ID_by_page_template` y `getPageContent`) | T2.5 | Sonnet | 0,3–0,5 |
| ☐ | E2.B5 | Escapar el título de prettyPhoto; iframes de `single.php` por `https` y `youtube-nocookie.com` | T2.9 | Sonnet | 0,5 |
| | | **C · Compatibilidad PHP/WordPress** (rama `componente/c-compatibilidad`) | | | |
| ☐ | E2.C1 | `functions.php`: hooks (`after_setup_theme`, `widgets_init`), text domain `kappe`, doble `style.css`, Google Maps por `https`; `redirect_docs()` y `weblusive_admin_head()` fuera de `weblusive_add_admin()`. **No cambiar los IDs de las barras laterales** que WP genera hoy (comprobarlos antes con WP-CLI) | T2.7 | **Opus** | 1,5 |
| ☐ | E2.C3 | (nuevo, E1.10) Avisos de PHP 8 en plantillas: `taxonomy-portfolio_category.php` (`$get_meta[...][0]` sin `isset`, referencias rotas), `$post->ID` fuera del bucle en `archive.php` y `search.php`, `$sidebarPos` en `single-portfolio.php`; apoyarse en phpcs | T2.10 | Sonnet | 0,5–1 |
| | | **D · Rendimiento** (rama `componente/d-rendimiento`) | | | |
| ☐ | E2.D1 | Eliminar `retina.js` | T3.1 | Haiku | 0,1 |
| ☐ | E2.D3 | Quitar `opacity: 0` y el preloader GIF (va antes de D2 para comparar colores sin preloader) | T3.3 | Sonnet | 0,5 |
| ☐ | E2.D2 | Reemplazar `dynamic-styles.php` por CSS en línea con transient; quitar `!important`; integrar en los estilos del tema el CSS que hoy vive en la BD (opción `css` del panel y CSS adicional del Personalizador): colores de créditos (#F5F8E3 y #34B7AE), `.inner-content`, `header .logo-box` y `.escala-horizontal .gfield_radio` de Gravity Forms. **`flow-root` y el menú lateral fijo van en D4** (E1.10). Ambos bloques se retiran de la BD en E3.5. Corregir `HexToRGB` (variables sin definir) si se sigue usando | T3.2 | **Opus** | 2 |
| ☐ | E2.D4 | Altura del menú lateral solo con CSS (`100dvh`, `flow-root`, menú fijo del Personalizador), quitando el JS que fija la altura del `header` | T3.4 | **Opus** | 1–1,5 |
| ☐ | E2.D5 | Fuentes: solo los pesos usados y `display=swap` | T3.5 | Haiku | 0,3 |
| ☐ | E2.D6 | Carga condicional y `defer`; quitar Modernizr, nicescroll y **solo el Migrate 1.0 del tema** (el Migrate 3.x de WordPress se queda; decisión de Pablo en E1.10). Corregir lo necesario en `script.js` (`.load()`, `.size()`, `.bind()`) sin reescribirlo en JS puro ni añadir jQuery nuevo | T3.6 | Sonnet | 1–1,5 |
| ☐ | E2.D7 | `eager` en la primera fila y `fetchpriority="high"` en la primera imagen | T3.7 | Haiku | 0,2 |
| | | **E · SEO del lado del tema** (rama `componente/e-seo-html`) | | | |
| ☐ | E2.E1 | Un solo `<h1>`; `title-tag`; `alt` del portafolio; tarjetas con `<a href>` real; corregir el `alt` literal `'.__('No image', 'Kappe').'` de las tarjetas del blog (hallazgo de E1.6) | T3B.1, T3B.3, T3B.5, T3B.6 | Sonnet | 1–1,5 |
| ☐ | E2.E2 | Viewport sin `maximum-scale=1`; `wp_body_open()`; `lang` | T3B.2, T3B.4 | Haiku | 0,2 |
| ☐ | E2.E3 | Contraste AA y foco visible | T3B.7 | Sonnet | 0,5–1 |

**Fuera del saneamiento (decidido en E1.10):** `post_type_link` (`strpos` al revés; arreglarlo podría cambiar las URL `/works/`), `my_option_posts_per_page` (se mantienen las 4 por página en categorías del portafolio), cargar el panel solo en el admin (~0,7 ms, riesgo con el CPT `weblusive_slider`), shortcodes sin uso y el escapado de sus atributos (SPECS §1; los botones de TinyMCE se quedan) y `comments.php` (comentarios desactivados).

**Estimación (E1.10):** etapa 2 ≈ 13–18 h y etapa 3 ≈ 3–5 h, en total ≈ 16–23 h en 7–8 sesiones (una por componente; D en dos). Es más que las 11–18 h de `TAREAS.md` porque el alcance creció: más seguridad, cuatro plantillas eliminadas con sus opciones, todo el CSS de la BD y E2.C3.
 
**Puerta 2:** Pablo revisa el resumen de `CHANGELOG.md` antes de pasar a las pruebas.
 
---
 
## Etapa 3 — Prueba de funcionamiento (todo en local)
 
| ☐ | ID | Tarea | Antes | Modelo |
|---|---|---|---|---|
| ☐ | E3.1 | PHPCompatibilityWP (`8.2-`) en 0 errores y `debug.log` vacío en PHP **8.2** (el de producción), 8.4 y 8.5 | T2.10, T4.1 | Haiku para ejecutar; Sonnet para corregir lo que falle |
| ☐ | E3.2 | Pruebas funcionales: las 6 páginas, paginación nativa del blog hasta el final (E2.B1), guardar una página y un proyecto conserva sus opciones, el panel del tema guarda e importa | "Cómo probar" | Sonnet |
| ☐ | E3.3 | Comparación visual contra las capturas de E1.6 (1440 px y 390 px); solo se aceptan diferencias que sean mejoras acordadas | T4.2 | Sonnet |
| ☐ | E3.4 | Lighthouse después contra E1.7 y prueba de teclado en el home y el blog | T4.3 | Haiku (medición); Sonnet (análisis) |
| ☐ | E3.5 | Paquete para el despliegue: `style.css` con **`Version: 3.0`**; el repo conserva ambas versiones (tag `prod-1.6` y la saneada) y solo se sube la saneada; la carpeta se llama `kappe`, igual que en producción, y se sobrescribe en su sitio tras el respaldo (así se conservan menú, CSS adicional y ajustes del Personalizador); **los archivos que la versión saneada elimina (p. ej. `ajax-load-more.php`) se borran del servidor con `rsync --delete`: primero simulación con `-n -i` y revisión de la lista por Pablo, y solo después la subida real**; instrucciones paso a paso (respaldo, subir, activar, purgar Cloudflare, qué revisar), cómo volver atrás y el retiro del CSS adicional del Personalizador. **El despliegue lo hace Pablo** | T4.4, T4.5 | Sonnet |
 
**Puerta 3:** Pablo da por buena la etapa 3 y despliega.
 
---
 
## Después del despliegue final (fuera de las 3 etapas)
 
Se retoma con Pablo una vez desplegado el tema saneado. Modelo sugerido: Sonnet, en el chat, con Claude in Chrome para el panel de Cloudflare.
 
- ☐ **Cloudflare:** decidir `xmlrpc.php` (Pablo cree que no se usa: bloquear con regla personalizada y observar); registros DNS "DNS only" que exponen la IP de origen; Bot Fight Mode vs protección anti-bots de DreamHost; Wordfence vs Cloudflare (S.5), midiendo el TTFB real en producción con y sin Wordfence.
- ☐ **SEO:** `/works/` caso por caso con datos de Search Console (S.1); sitemap de Yoast y `robots.txt` (S.2); 6 enlaces 404 y 3 redirecciones (S.3); Yoast → The SEO Framework con respaldo y comparación antes/después (S.4).
- Recordar **purgar la caché de Cloudflare** tras cada cambio de HTML.
 
