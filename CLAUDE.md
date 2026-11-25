# CLAUDE.md — kappe-saneamiento

Saneamiento corto del tema Kappe 1.6 de 4topixel.com. Trabajamos en español y se tutea a Pablo.

## Al empezar cada sesión
1. Lee `docs/ESTADO.md` y `docs/PLAN-3-ETAPAS.md`.
2. Di en qué tarea vamos (ID), qué modelo le toca según el plan y qué rama usar. Espera mi confirmación.

## Reglas
- Todo en local: sitio LocalWP `4topixel` (https://4topixel.local, PHP 8.4; 8.5 para pruebas).
  Nunca toques producción ni te conectes por SSH al servidor.
- Confirma conmigo cada cambio que no sea pequeño antes de hacerlo. Si una tarea depende de una
  decisión pendiente (SPECS §9), detente y pregunta.
- No leas el tema completo. Lee `docs/INVENTARIO.md` (la sección 10 "Índice por tarea" dice qué
  archivos abrir para cada ID, y cada archivo tiene su entrada con riesgos y tareas) y abre solo
  los archivos de la tarea. Si descubres un dato nuevo, actualiza su entrada. Los volcados que
  respaldan el inventario están en `inventario/raw/` y se regeneran con
  `inventario/scripts/ejecutar-todo.sh` (solo lectura; necesita el sitio encendido en Local).
- No renombres nada de lo que SPECS §5 lista como datos guardados.
- Conserva los colores de créditos (#F5F8E3 texto, #34B7AE enlaces) y no cambies tamaños de fuente.
- El tema vive en `tema/kappe/` (enlazado a LocalWP). `respaldos/` no se sube a Git.
- Sin dependencias nuevas de Composer ni npm dentro del tema. Pídeme confirmación antes de instalar herramientas.

## Git
- `main` = lo aprobado en cada puerta · `desarrollo` = integración · `componente/<letra>-<nombre>` = una rama por componente.
- Commits en español, con el ID de la tarea al inicio: `E2.B1: endurecer "Cargar más"`.
- Haz commit solo cuando yo lo pida. El `push` lo hago yo.
- Al cerrar una tarea: marca su casilla en `docs/PLAN-3-ETAPAS.md` y actualiza `docs/ESTADO.md`.
  Al cerrar un componente: entrada en `CHANGELOG.md`.

## Modelo
Sonnet por defecto. Opus solo en las tareas marcadas, o si Sonnet falla dos veces seguidas,
o si toca seguridad o datos guardados. Haiku solo en las tareas marcadas. Cambia con /model al empezar la tarea.

## Copia local
- Prefijo de tablas `p1x3l4d0s_`. Desactivados en local: wordfence, wp-cloudflare-page-cache, google-site-kit, worker.
- Ejecuta WP-CLI y PHP desde la shell del sitio de Local.
- `~/Projects/wp-load.php` es un puente solo para local que necesita `ajax-load-more.php` mientras exista; se borra al terminar E2.B1.

## Decisiones de Pablo
- El blog no se usa (entradas de hace más de 10 años, fuera del menú principal): que funcione no es prioridad.
- Las entradas se editan con el editor clásico: no quitar los botones de TinyMCE del tema.
- Al empaquetar, `style.css` debe decir `Version: 3.0` (la última versión del desarrollador fue la 2.1).
- `ajax-load-more.php` se elimina (E2.B1) y la paginación del blog pasa a la nativa de WordPress.
- La carpeta del tema se llama `kappe` también en producción (se sobrescribe en su sitio tras el respaldo).
- Al desplegar, los archivos eliminados en la versión saneada se borran del servidor con `rsync --delete`, siempre con simulación previa (`-n -i`) revisada por Pablo.
- Staging solo en local: no se crean copias del sitio en DreamHost.
- Los colores de créditos y los demás ajustes que hoy viven en la BD (opción `css` del panel y CSS adicional del Personalizador) se integran **directamente en los estilos del tema**.
- El sitio no usa widgets: `widgets.php` no se carga (como en producción).
- Se eliminan las plantillas que ninguna página usa: `contact-template.php`, `portfolio_template2.php`, `under-construction.php`, `woocommerce.php` (el contacto es Gravity Forms con la plantilla `default`).
- Las traducciones del tema se llaman `es_CO.po/.mo` (el sitio está en `es_CO`; con `es_ES` no se cargaban).
- Se quita jQuery Migrate y las librerías que no se usan (las que ningún archivo referencia); no añadir jQuery nuevo. No se reescribe `script.js` en JavaScript puro salvo lo que haga falta al quitar Migrate.
- Todo el CSS que hoy vive en la BD (opción `css` del panel y CSS adicional del Personalizador, incluido Gravity Forms `.escala-horizontal`, `flow-root` y el menú lateral fijo) se integra en el tema; al desplegar se retiran de la BD.
- Al quitar las pestañas "Contact settings" y "Under construct." del panel se acepta que se borren sus opciones guardadas (`contact_*`, `uc_*`, `google_api`): ninguna plantilla las usa.
