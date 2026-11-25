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

### E2.B · Seguridad (2026-11-25) · rama `componente/b-seguridad`
- Qué cambió:
  - E2.B1: fuera `ajax-load-more.php` (y su puente local, el bloque `#ajax-load-more` de `script.js` y el botón "Cargar más"); `blog-template.php` pagina con `WP_Query` + `paginate_links()` (HTML `ul.pagination` de Bootstrap).
  - E2.B2: meta boxes con nonce, capacidad `edit_post` y salida en autosave/revisiones; `save_slide()` solo borra `custom_slider` si llega el formulario; `unserialize()` fuera de `custom-slider.php` y `page-head.php`; el orden del portafolio por AJAX exige nonce, `edit_pages` y permiso sobre cada proyecto. El formato guardado no cambia.
  - E2.B3: guardar, importar y exportar opciones del panel exigen `switch_themes`; exportar e importar en JSON (el formato base64 viejo se rechaza; sin `unserialize`); sin doble escape. Los colores de tipografía que guardaban un aviso PHP con una ruta del servidor ya no se imprimen ni se guardan.
  - E2.B4: `get_page_ID_by_page_template()` y `getPageContent()` usan `get_posts()`/`get_post()` en vez de SQL con variables interpoladas. No queda `$wpdb` en el código propio del tema.
  - E2.B5: `title` de los enlaces de prettyPhoto escapados (el HTML `<h5>…</h5><p>…` se conserva, con el texto escapado por dentro); iframes por `https` y YouTube por `youtube-nocookie.com` (incluido el que arma `js/jquery.prettyPhoto.js` para el lightbox); quitados Veoh, blip.tv y Viddler de plantillas, shortcode `video`, panel y asistente de shortcodes (la BD no los usaba).
- Archivos tocados: `blog-template.php`, `single.php`, `loop.php`, `portfolio_template.php`, `taxonomy-portfolio_category.php`, `js/script.js`, `js/jquery.prettyPhoto.js`, `library/admin-panel/{admin-functions,admin-ui,post-options,custom-slider}.php`, `library/admin-panel/js/main.js`, `library/functions/{helper-functions,post-types}.php`, `library/functions/shortcodes/{shortcode,ui}.php`, `library/includes/page-head.php`; `ajax-load-more.php` eliminado. Commits `cffbaac` (B1), `bd9acdf` (B2), `84e0338` (B3), `99ae97a` (B4) y el de B5.
- Qué debe probar Pablo:
  - **Al desplegar, abrir el panel del tema y pulsar "Save Changes" una vez** (limpia 8 colores de tipografía que guardaban un aviso PHP).
  - Blog: `/blog/` y su paginación nativa (hoy solo hay 2 entradas, así que `/blog/page/2/` da 404).
  - Portafolio: el lightbox del vídeo de YouTube del proyecto "4to Pixel – Crear es un proceso" (verificado por Pablo el 25-nov) y una galería de imágenes.
  - Admin: editar una página con las cajas del tema, el "Sort items" del portafolio y guardar/exportar/importar opciones del panel.
- Pruebas hechas en local (25-nov): `php -l` de los 36 archivos PHP del tema en 8.2, 8.4 y 8.5 sin errores; phpcs PHPCompatibilityWP 8.2- sin errores (14 avisos de archivos minificados o `index.php` vacíos); `node --check` de los JS tocados; `/`, `/blog/`, `/nuestro-equipo/`, búsqueda y el proyecto 2467 dan 200 sin avisos; `ajax-load-more.php` da 404; log de PHP sin líneas nuevas. Por HTTP con usuarios temporales (borrados): guardar panel sin sesión = 400, sin nonce o con nonce falso = 403, editor = -1, importar formato viejo o claves ajenas = 0; orden del portafolio sin sesión = 400, sin nonce o con nonce falso = 403, `order` que no es lista = 400, ID de página (no portafolio) ignorado.
- Cómo deshacerlo: revertir los commits de la rama o volver al tag `base-2.1`. Los datos guardados no cambian de formato, salvo los 8 colores de tipografía saneados.

### E2.C · Compatibilidad PHP/WordPress (EN CURSO, 2026-11-25) · rama `componente/c-compatibilidad`
- Qué cambió (hasta ahora, E2.C1; falta E2.C3 para cerrar el componente):
  - E2.C1: nueva `kappe_setup()` en `after_setup_theme` (`load_theme_textdomain`, los 4 `add_theme_support`, `register_nav_menus` y los 5 `add_image_size`); las barras del pie se registran en `widgets_init` con los mismos IDs; text domain `'Kappe'` → `'kappe'` en 120 llamadas (18 archivos); `weblusive_admin_head()` fuera de `weblusive_add_admin()`; borradas `redirect_docs()` y `$docs_url` (huérfanas).
  - `header.php`: fuera todo lo de IE (html5shiv por `http://`, `css/ie.css`, `<html>` condicional de IE 8 con `lang="en"`); queda `<html class="no-js" lang="es-CO">` (decisión de Pablo, 25-nov).
- Archivos tocados: `functions.php`, `header.php`, `library/admin-panel/admin-functions.php`, plantillas y librerías con llamadas de traducción (`404.php`, `blog-template.php`, `comments.php`, `loop.php`, `portfolio_template.php`, `search.php`, `searchform.php`, `single.php`, `taxonomy-portfolio_category.php`, `library/functions/{helper-functions,post-types,widgets}.php`, `library/functions/shortcodes/shortcode.php`, `library/includes/{page-head,the_breadcrumb}.php`); eliminado `css/ie.css`.
- Qué debe probar Pablo: que los textos del tema sigan en español (p. ej. "(Editar)"), el menú principal y las páginas del admin del tema.
- Pruebas hechas en local: foto WP-CLI antes/después idéntica (barras, tamaños de imagen, soportes y menús); lint 8.2/8.4/8.5 y PHPCompatibilityWP 8.2- sin errores; portada, servicios, blog, búsqueda y proyecto 2467 dan 200 sin avisos; panel, escritorio, "Sort items" y edición de un proyecto dan 200 sin avisos; log de PHP sin líneas nuevas.
- Cómo deshacerlo: revertir el commit de E2.C1.
