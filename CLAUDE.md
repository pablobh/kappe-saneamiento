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
- No leas el tema completo. Lee `docs/INVENTARIO.md` (cuando exista) y abre solo los archivos de
  la tarea. Si descubres un dato nuevo, actualiza su entrada.
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