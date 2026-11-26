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

### E2.C · Compatibilidad PHP/WordPress (2026-11-25) · rama `componente/c-compatibilidad`
- Qué cambió:
  - E2.C3: avisos de PHP 8 en plantillas. `taxonomy-portfolio_category.php` sin `$_SESSION` ni la búsqueda de página con un nombre de plantilla inexistente (variables que no se usaban) y sin el `get_template_part('portfolio_header')` de un archivo que no existe; `archive.php` y `search.php` sin `$post->ID` fuera del bucle; `single-portfolio.php` con `$sidebar_pos` (errata `$sidebarPos`); la cabecera "slider" de `page-head.php` omite diapositivas con adjunto borrado.
  - E2.C1: nueva `kappe_setup()` en `after_setup_theme` (`load_theme_textdomain`, los 4 `add_theme_support`, `register_nav_menus` y los 5 `add_image_size`); las barras del pie se registran en `widgets_init` con los mismos IDs; text domain `'Kappe'` → `'kappe'` en 120 llamadas (18 archivos); `weblusive_admin_head()` fuera de `weblusive_add_admin()`; borradas `redirect_docs()` y `$docs_url` (huérfanas).
  - `header.php`: fuera todo lo de IE (html5shiv por `http://`, `css/ie.css`, `<html>` condicional de IE 8 con `lang="en"`); queda `<html class="no-js" lang="es-CO">` (decisión de Pablo, 25-nov).
- Archivos tocados: `functions.php`, `header.php`, `library/admin-panel/admin-functions.php`, plantillas y librerías con llamadas de traducción (`404.php`, `blog-template.php`, `comments.php`, `loop.php`, `portfolio_template.php`, `search.php`, `searchform.php`, `single.php`, `taxonomy-portfolio_category.php`, `library/functions/{helper-functions,post-types,widgets}.php`, `library/functions/shortcodes/shortcode.php`, `library/includes/{page-head,the_breadcrumb}.php`); eliminado `css/ie.css`.
- Qué debe probar Pablo: que los textos del tema sigan en español (p. ej. "(Editar)"), el menú principal y las páginas del admin del tema.
- Pruebas hechas en local: foto WP-CLI antes/después idéntica (barras, tamaños de imagen, soportes y menús); lint 8.2/8.4/8.5 y PHPCompatibilityWP 8.2- sin errores; portada, servicios, blog, búsqueda y proyecto 2467 dan 200 sin avisos; panel, escritorio, "Sort items" y edición de un proyecto dan 200 sin avisos; log de PHP sin líneas nuevas.
- Cómo deshacerlo: revertir el commit de E2.C1.

### E2.D · Rendimiento (2026-11-26) · rama `componente/d-rendimiento`
- Qué cambió:
  - E2.D1: eliminado `retina.js` (encolado y archivo).
  - E2.D3: fuera `opacity: 0` y el preloader (CSS, JS, `footer.php` y el GIF); la opción `disable_loader` sale del panel.
  - E2.D2: estilos del panel en línea con caché (transient `kappe_dynamic_css`); el CSS que vivía en la BD (colores de créditos, `.inner-content`, `header .logo-box`, `.escala-horizontal` de Gravity Forms) pasa a `style.css`. Los `!important` del panel se conservan (decisión de Pablo).
  - E2.D4: altura del menú lateral solo con CSS (`100dvh`, `flow-root`, menú fijo desde 768 px); fuera el JS que fijaba la altura del `header`.
  - E2.D5: Roboto solo con los pesos usados (300, 300i, 400, 500, 500i, 700, 700i) y `display=swap`.
  - E2.D6: fuera Migrate 1.0 del tema, Modernizr, nicescroll (la barra de scroll pasa a ser la nativa), jQuery Validate y jPlayer con `[audio]` y `[shvideo]`; Bootstrap con `defer`; `.size()` pasa a `.length`.
  - E2.D7: `loading="eager"` en las 4 primeras tarjetas del portafolio y `fetchpriority="high"` en la primera; `fetchpriority="high"` en la primera diapositiva de `page-head.php`.
- Archivos tocados: `functions.php`, `style.css`, `js/script.js`, `library/functions/dynamic-css.php`, `footer.php`, `library/admin-panel/admin-ui.php`, `library/includes/page-head.php`, `portfolio_template.php`, `js/jquery.hoverdir.js`, y se eliminan `js/retina-1.1.0.min.js`, `images/preloader.gif`, `js/jplayer/`, `js/jquery.migrate.js` y Modernizr. Commits: `b64daa4` (D1), `a02036b` y `ab18de8` (D3), `8804cbf` (D2), `aba652d` (D4), `b424999` (D5), `319c829` (D6); D7 en el commit `1efb46d`.
- Qué debe probar Pablo: la portada con la barra de scroll nativa; la portada y el menú a 768 px y con pantalla baja (menú con scroll); el lightbox de YouTube del portafolio; las fuentes sin cambios visibles.
- Pruebas hechas en local: `php -l` 8.2/8.4/8.5 y `node --check` OK; PHPCompatibilityWP 8.2- sin errores; `/`, `/blog/`, `/nuestro-equipo/`, `/servicios/`, `/contactanos/`, `/trabaja-con-nosotros/` y `/politica-de-privacidad/` dan 200; estilos calculados iguales a 1440 y 390 px (en 768 px el menú queda fijo, es el cambio buscado); el log de PHP sigue en 778 líneas, sin avisos nuevos.
- Cómo deshacerlo: revertir los commits de la lista anterior, en orden inverso.

### E2.E · SEO y HTML (2026-11-26) · rama `componente/e-seo-html`
- Qué cambió:
  - E2.E1: `<h1>` oculto (`kappe_h1()`, clase `.kappe-h1`) en portafolio, blog, archivo, búsqueda y categoría del portafolio (los `<h1>` del contenido de las páginas no se tocan); `add_theme_support('title-tag')` y fuera el `<title>` manual; `alt` de las tarjetas del portafolio (el del adjunto o, si está vacío, el título del proyecto); título de cada tarjeta como enlace real al proyecto; `alt` del blog arreglado. Sin `srcset`/`sizes` en las miniaturas (se decide en E3.4 según Lighthouse).
  - E2.E2: viewport sin `maximum-scale=1`; `wp_body_open()` tras `<body>`.
  - E2.E3 (opción mínima): `.tab-content` de `#a3a3a3` a `#6f6f6f`, enlaces `tel:` del contenido a `#2a6496`, y fuera dos `outline:none` (campo de búsqueda y cierre del lightbox). **Sin tocar, a decidir:** blanco sobre turquesa `#34B7AE` en el menú (2,46), hover de las tarjetas (2,1), `h1` turquesa (2,24) y submenú (2,87); propuesta `#1E7F78`.
- Archivos tocados: `header.php`, `style.css`, `portfolio_template.php`, `taxonomy-portfolio_category.php`, `blog-template.php`, `archive.php`, `search.php`, `loop.php`, `library/functions/helper-functions.php`, `functions.php`. Commits `da7d6e0` (E1), `a8a969b` (E2) y el de E3.
- Qué debe probar Pablo: un solo `<h1>` por página; el título de las pestañas del navegador (Yoast sigue gestionándolo); las tarjetas del portafolio (el título es enlace, mismo aspecto); el texto de `/servicios/` y el teléfono de `/contactanos/` con los colores nuevos; el foco con Tab en el menú, la búsqueda y el lightbox.
- Pruebas hechas en local: `php -l` 8.x y PHPCompatibilityWP 8.2- sin errores (E1/E2); 9 URL dan 200 sin avisos; estilos calculados iguales a 1440, 768 y 390 px salvo el `alt` del blog (mejora pedida); contraste medido con Playwright; log de PHP sin líneas nuevas (831).
- Cómo deshacerlo: revertir los commits de la lista anterior, en orden inverso.
