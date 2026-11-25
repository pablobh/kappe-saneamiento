# CHANGELOG

Un bloque por componente cerrado. Lo escribe Claude Code al cerrar cada componente;
Pablo lo revisa en la Puerta 2.

## Plantilla
### E2.X · nombre del componente (fecha) · rama `componente/...`
- Qué cambió:
- Archivos tocados:
- Qué debe probar Pablo:
- Cómo deshacerlo:

## Historial
### 2026-11-24 · Estructura inicial
- Documentos del plan, `.gitignore`, tema de producción (Kappe 1.6) y base 2.1 con `es_ES`.

### E2.A · Código muerto (2026-11-24) · rama `componente/a-codigo-muerto`
- Qué cambió:
  - E2.A1: eliminados el envío de correo propio (`sendmail`, `contact-form`, PHPMailer), el notificador de actualizaciones, pricing tables (`pricing-tables`, `table_process`, `table_wizard`, sus imágenes, el `include` y el submenú; esto corrige el error fatal que ya daba esa página del admin), las plantillas sin uso (`contact-template.php`, `portfolio_template2.php`, `under-construction.php`, `woocommerce.php`), `gmap3`, `jquery.countdown` y su encolado, las pestañas "Under construct." y "Contact settings" del panel, las librerías sin referencia (`jquery.min`, `jquery.bxslider.js`, `font-awesome.css`, `magnific-popup.css`, 5 archivos de `js/jplayer/` y los skins `blue.monday` y `circle.skin`), el bloque del widget de contacto en `script.js`, un bloque huérfano de `main.js` y 7 funciones sin uso de `helper-functions.php`.
  - E2.A3: `widgets.php` deja de cargarse (como en producción); eliminados `OAuth.php`, `twitteroauth.php` y `addons/flickr/`.
  - E2.A2 cerrada sin cambios.
  - Al guardar el panel se borran las opciones `contact_*`, `uc_*` y `google_api` (aceptado).
- Archivos tocados: `functions.php`, `js/script.js`, `library/admin-panel/{admin-ui,admin-functions}.php`, `library/admin-panel/js/main.js`, `library/functions/helper-functions.php`, más los archivos eliminados. Commits `e583cad` (A1) y el de A3.
- Qué debe probar Pablo: las 6 páginas, el blog y la búsqueda cargan (verificado en local: 200 sin avisos, sin `debug.log`); el panel del tema carga y guarda (verificado por Pablo el 24-nov).
- Cómo deshacerlo: revertir los commits de la rama o volver al tag `base-2.1`.
