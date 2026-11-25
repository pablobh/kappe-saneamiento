# ESTADO — Saneamiento de Kappe / 4topixel.com (actualizado 24-nov-2026, 14:00)

**EN CURSO — Etapa 1.** Pablo retomó el proyecto el 24-nov-2026. Todo el desarrollo lo hace con Claude Code, en local.

**Plan vigente: `PLAN-3-ETAPAS.md`** (aprobado 24-nov-2026): 1) revisión general y plan, 2) desarrollo por componente, 3) pruebas en local. Trae el modelo recomendado por tarea y cuándo cambiarlo. Manda sobre `TAREAS.md` y `SPECS.md` §10 en etapas, ramas, pruebas, despliegue y modelo. `SPECS.md` sigue siendo el alcance y las decisiones; `TAREAS.md`, el detalle de cada tarea.

## Hecho
- Auditoría del tema; producción es **Kappe 1.6** (no 2.1). Base del saneamiento: 2.1 solo si es compatible con datos y configuración de producción (SPECS §2.1, §5).
- CSS original (altura del sidebar, margen superior, color de créditos) resuelto antes.
- **Fase 0 completa** (Pablo borró los archivos expuestos; verificado 404 en Chrome). Excepción intencional: `ajax-load-more.php` (200, lo usa "Cargar más"; se elimina en E2.B1). Caché de Cloudflare purgada.
- **Cloudflare (plan gratis), 21-nov:**
  - Regla de rate limiting "Limitar intentos de login WordPress": POST a `/wp-login.php`, 5 req/10 s por IP, Block 10 s, activa.
  - Borradas las 5 reglas de acceso por IP "Allow" que Pablo no reconocía. Lista vacía.
  - **Observación terminada (24-nov):** nada ha fallado desde que se quitaron; ya no hace falta recrearlas.
  - La regla de caché la gestiona el plugin WP Super Page Cache: no editarla a mano.
  - Bot Fight Mode apagado (decisión aplazada). Panel "Zone Lockdown" da error de carga de la interfaz: ignorar.
- 24-nov: Pablo aprobó el plan en 3 etapas con modelo por tarea (`PLAN-3-ETAPAS.md`).
- **24-nov, etapa 1 en marcha:**
  - Repo `kappe-saneamiento` en `~/Projects` y en GitHub (privado, `pablobh/kappe-saneamiento`). Ramas `main` y `desarrollo`; tags `prod-1.6` (tema de producción) y `base-2.1` (2.1 + `es_ES`). `CLAUDE.md`, `README.md`, `CHANGELOG.md` y `LICENSE.md` creados (el tema declara "GNU General Public License" en `style.css`, sin versión).
  - Copia local en LocalWP: sitio `4topixel` (https://4topixel.local, PHP 8.4), con la BD y `wp-content` de producción. Desactivados en local: wordfence, wp-cloudflare-page-cache, google-site-kit y worker. Prefijo de tablas `p1x3l4d0s_`. La verificación con WP-CLI coincide con producción (mismo contenido); local sirve la 2.1 y producción la 1.6. Quedan 12 tablas `wp_*` sobrantes de la instalación vacía de Local (opcional borrarlas).
  - Línea base con la 1.6 en curso (worktree `~/Projects/kappe-prod16` y enlace de Local apuntando a él): capturas hechas; Lighthouse corriendo. Al terminar, volver el enlace a `~/Projects/kappe-saneamiento/tema/kappe`.
  - Hallazgo: `ajax-load-more.php` carga `../../../wp-load.php` con ruta relativa y el enlace simbólico la rompe. Solución solo local: puente `~/Projects/wp-load.php` (se borra al terminar E2.B1).
- **E1.2 cerrada (24-nov):** `CAMBIOS-PRODUCCION.md` en la raíz. De 106 archivos distintos entre `prod-1.6` y `base-2.1`, 83 son solo fin de línea (1.6 en LF, 2.1 en CRLF); quedan 23 con cambios reales (`git diff --ignore-cr-at-eol`). `es_ES.po` y `table_process.php` no cambian de contenido.
- **E1.3 cerrada (24-nov), sin cambios en el tema:** `script.js` solo cambia el tamaño por defecto de prettyPhoto (853×480); `prettyPhoto.js` (3.1.6) solo añade `allowfullscreen`; `addons/paginajax` no existe en los tags ni se referencia (carpeta vacía en producción); `es_ES.po`/`.mo` iguales y coherentes entre sí (38 cadenas traducidas, 68 sin traducir). Pendiente de decidir en E2.C1: el texto "Send a message" (2.1) frente a "Send us a message" (el `.po`), la errata "611 characters" y el text domain `'Kappe'` frente a `kappe` en `style.css`.
- **E1.4 cerrada (24-nov), sin cambios en el tema:** `table_process.php:99` usa `$row_{$i} = …` (18 apariciones, solo en ese archivo), sintaxis de llaves que PHP 8.0 eliminó: `php -l` da error de análisis en 8.2, 8.4 y 8.5. El archivo es idéntico en la 1.6 y la 2.1. Se carga con `require_once` dentro de la función `pricing_tables()` (`pricing-tables.php:4`), así que el sitio y el admin cargan bien y el fallo ocurre solo al abrir el submenú "Pricing Tables" (`admin-functions.php:180`). Reproducido con un script aislado en PHP 8.4, sin tocar el sitio. Lo resuelve E2.A1 (se elimina el módulo, el `include` de `functions.php:33` y el submenú). **Producción corre PHP 8.2** (dato de Pablo): la 1.6 ya tiene hoy este error fatal al abrir esa página del admin.
- **E1.5a cerrada (24-nov):** scripts en `inventario/scripts/` (`ejecutar-todo.sh` los corre en orden; solo leen; escriben únicamente en `inventario/raw/`, 168 KB). Salidas: archivos y carpetas con estado según `reglas-estado.tsv`, hooks/shortcodes/enqueues/opciones/metas/SQL/AJAX/plantillas/definiciones como archivo:línea, `php -l` en 8.2/8.4/8.5, patrones de compatibilidad PHP 8 y consultas WP-CLI a la BD local (valores sensibles ocultos). **PHPCompatibilityWP no está instalado** (instalarlo requiere tu confirmación; se usa en E3.1); `php -l` y `compat-patrones.tsv` son una aproximación. Datos de la BD: tema activo Kappe 2.1 (local), **idioma del sitio `es_CO`** (el tema solo trae `es_ES.mo`: confirmar en E1.5b si la traducción se carga), **ningún widget activo** en ninguna barra lateral, plantillas de página usadas: `default`, `page.php`, `portfolio_template.php`, `blog-template.php`; shortcodes del tema usados: solo `row` y `tabgroup` (50 de 52 sin uso); plugins activos incluyen `easy-bootstrap-shortcodes` (`ebs_*`), `gravityforms`, `wordpress-seo`, `wp-mail-smtp`, `classic-editor`, `code-snippets`.

## Decisiones del 24-nov
- **El despliegue final lo hace Pablo.** Claude solo desarrolla y prueba en local y entrega el paquete con instrucciones (E3.5).
- **Cloudflare y SEO se revisan después del despliegue final**, no antes (xmlrpc, DNS que exponen la IP, Bot Fight Mode, Wordfence vs Cloudflare, S.1–S.4).
- **E1.9 (SPECS §9):** el blog no se usa (entradas de hace más de 10 años, fuera del menú principal): que funcione no es prioridad. Las entradas se editan con el **editor clásico** (no quitar los botones de TinyMCE del tema).
- **E2.B1:** se elimina `ajax-load-more.php` (y su llamada en `js/script.js` y el botón "Cargar más") y se reemplaza por la paginación nativa de WordPress en `blog-template.php`. Modelo: Sonnet. Al terminar se borra el puente `~/Projects/wp-load.php`.
- **Carpeta en producción:** se mantiene el nombre `kappe` y se sobrescribe en su sitio tras el respaldo (WordPress guarda menú asignado, CSS adicional y ajustes del Personalizador por nombre de carpeta, `theme_mods_kappe`).
- **Staging solo en local.** No se crean más copias del sitio en DreamHost: E1.8 queda cancelada. El TTFB real y el efecto de Wordfence se miden después del despliegue.
- **E3.5:** solo se sube a producción la versión saneada. El repo conserva ambas versiones (1.6 y la saneada). Al empaquetar, `style.css` debe decir **`Version: 3.0`** (la última del desarrollador fue la 2.1).

## Verificación de E1.1 (24-nov, cerrada)
- `wp-content` local = producción, salvo los datos en vivo de WP Super Page Cache (caché y configuración; el plugin está inactivo en local).
- El tema del tag `prod-1.6` es idéntico, por checksum, al que corre en producción. En producción existe además una carpeta vacía `addons/paginajax/` que Git no guarda.
- Sin pendientes de Pablo en esta verificación.

## Pendiente (siguiente)
1. **Etapa 1:** terminar línea base (Lighthouse, commit de `pruebas/`), luego E1.3 a E1.5 en Claude Code (diferencias sin revisar, causa de la caída de la 2.1, `INVENTARIO.md` con lectura única), E1.6 compatibilidad 1.6→2.1 y plan final E1.10 aprobado por Pablo.
2. **Etapa 2:** componentes A (código muerto, incluye quitar el `include` de `pricing-tables.php` en `functions.php:33` y el submenú en `admin-functions.php:180`), B (seguridad), C (compatibilidad), D (rendimiento), E (SEO HTML).
3. **Etapa 3:** pruebas en local y paquete de despliegue.
4. **Después del despliegue:** Cloudflare y SEO (ver `PLAN-3-ETAPAS.md`).
5. Más adelante: MCP de WordPress (aplazado); tema de bloques (feb-2027, SPECS §8).

## Reglas de trabajo
- Preguntar todo lo necesario antes de actuar; cambios pequeños sin pedir permiso, el resto se aprueba primero.
- Nunca tocar producción sin respaldo. Conservar los colores de créditos de Pablo (#F5F8E3 texto, #34B7AE enlaces) y no cambiar tamaños de fuente.
- Navegador: Claude in Chrome ya conectado; avisar a Claude cuando Pablo esté logueado en el sitio o en Cloudflare.
- Tareas de herramientas en el navegador: hacerlas en lotes (`browser_batch`) para gastar menos.
- No leer el tema completo: usar `INVENTARIO.md` (cuando exista) y abrir solo los archivos de la tarea.
