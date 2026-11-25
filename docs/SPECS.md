# SPECS — Saneamiento del tema Kappe (4topixel.com) y requisitos del tema de bloques
 
> Documento de alcance. Léelo completo antes de tocar código. Tareas en orden: `TAREAS.md`.
> Actualizado el 20 de noviembre de 2026 tras comparar el tema en producción con el zip original,
> medir el sitio en vivo y revisar Search Console.
 
---
 
## 1. Objetivo y alcance
 
El sitio corre **Kappe**, un tema de ThemeForest sin actualizaciones desde ~2016. En **febrero de 2027** se lanzará un **tema de bloques nuevo** con diseño nuevo. Por eso **no se hace el refactor completo**. El alcance es un **saneamiento corto**:
 
1. **Seguridad:** cerrar los endpoints y las rutas de código peligrosas.
2. **PHP 8.5:** que el tema no genere errores fatales ni *deprecations* en las pantallas que se usan.
3. **Rendimiento:** solo los cambios de mayor impacto y menor riesgo.
4. **SEO del lado del tema:** correcciones puntuales de HTML (§6).
**Fuera de alcance del saneamiento** (se hará en el tema nuevo, §8): reescribir JS sin jQuery, reemplazar Bootstrap, Font Awesome, Isotope, prettyPhoto, la migración a WPCS completo, el panel de opciones y los shortcodes. Todo cambio debe poder deshacerse activando la copia de respaldo del tema.
 
Nada de esto debe cambiar el contenido guardado en la base de datos (§5).
 
---
 
## 2. Punto de partida
 
### 2.1 Qué hay realmente en producción
 
Se comparó el tema en producción con el zip "original" (`diff -r`, 20-nov-2026). Conclusión: **producción no es Kappe 2.1 con una modificación pequeña; es Kappe 1.6**. El `style.css` en vivo dice `Version: 1.6 · PremiumLayers & Weblusive`; el zip es la 2.1. El sitio nunca pasó a la 2.1: Pablo intentó instalarla y el sitio se cayó.
 
**Causa probable de esa caída:** `library/admin-panel/table_process.php:99` usa `$row_{$i}`, sintaxis que PHP 8 ya no admite y que genera **error fatal de sintaxis** al cargar el archivo (lo carga la pantalla de admin "Pricing Tables"). Producción tiene el mismo archivo; no cae porque nadie abre esa pantalla. **Hay que confirmarlo en local** (T1.3) antes de darlo por hecho. Hoy `table_process.php` en vivo responde 500, lo que además revela errores.
 
**Diferencias producción (1.6) frente a 2.1:**
 
| Archivo | En producción (1.6) | En 2.1 | Decisión |
|---|---|---|---|
| `library/functions/widgets.php` | Constructores estilo PHP 4 (`function alc_social_widget()` + `$this->WP_Widget(...)`) en 7 widgets. Obsoleto desde WP 4.3 | `__construct` con `parent::__construct` | Usar 2.1 |
| `functions.php` | `require_once` de `widgets.php` **comentado**; `style.css` encolado dos veces; Google Maps por `http://…?sensor=false` | Sin esos problemas | Usar 2.1; confirmar cómo se registran hoy los widgets |
| `library/includes/the_breadcrumb.php` | Constructor `the_breadcrumb()` (en PHP 8 ya no es constructor: el breadcrumb no inicializa sus opciones) | `__construct` | Usar 2.1 |
| `blog-template.php` | `$cat = &get_category(…)` (aviso en PHP 8) | Corregido | Usar 2.1 |
| `single.php` | iframe de YouTube por `http://` (contenido mixto) | Comentado | Usar 2.1 y poner `https` |
| `contact-template.php` | Validación distinta; texto con "611 caracteres" | Corregido | Usar 2.1 (el sitio usa Gravity Forms, no esta plantilla) |
| `admin-ui.php` / `admin-functions.php` | Sin opción `google_api`; con enlace a "Documentation" del proveedor | Con `google_api`, sin el enlace | Usar 2.1 |
| `notifier` | Apunta a `weblusive-themes.com` | Apunta a `premiumlayers.net` | Se elimina (ver Fase 0) |
| Archivos extra | `js/script.zip`, `js/jquery.nicescroll.OLD.min.js`, `images/preloader.orig.gif`, `addons/paginajax/`, `notifier.xml`, `languages/es_ES.mo/.po` | — | Los `.zip`, `.OLD`, `.orig` y `paginajax` se eliminan; **`es_ES.mo/.po` se conserva** |
| Archivos que faltan en producción | `library/sendmail.php`, `library/class.phpmailer.php` (ya borrados) | existen | Se quedan eliminados |
| CSS adicional del Personalizador | Ver §5.1 | — | Se integra en el tema y luego se retira del Personalizador |
 
**Regla:** se toma la **2.1 como base**, **siempre que sea compatible con los datos y la configuración de producción** (§5). Verificado: ambas versiones guardan las opciones en la misma clave (`weblusive_options`) y la 2.1 solo lee una opción más (`google_api`). Las plantillas y metas son las mismas. Lo único a reaplicar de producción es la traducción `es_ES` y el CSS adicional.
 
**Qué NO se copia de producción:** los constructores PHP 4 y el `require` comentado. Se conserva la *intención* (que los widgets se registren y funcionen en WP actual), implementada con `__construct` y `widgets_init`.
 
### 2.2 Qué usa el sitio en vivo
 
Revisado el 20-nov-2026:
 
| Página | Plantilla | Funciones del tema que usa |
|---|---|---|
| `/` | `portfolio_template.php` | Grilla con filtro (Isotope), hover direccional, lightbox (prettyPhoto) con imágenes y YouTube, Font Awesome |
| `/blog/` | `blog-template.php` | "Cargar más" por AJAX (`ajax-load-more.php`), grilla Isotope, sidebar |
| `/servicios/` | `page` | Shortcode de pestañas (`tabgroup`/`tab`), Font Awesome |
| `/nuestro-equipo/` | `page.php` | Shortcode `teammember`, sidebar (nota E1.10: el inventario de la BD solo encontró `row` y `tabgroup` del tema; no afecta porque no se eliminan shortcodes) |
| `/trabaja-con-nosotros/` | `page` | iframe, sidebar |
| `/contactanos/` | `page.php` | **Gravity Forms** (no el formulario del tema), Easy Bootstrap Shortcodes, iframe de mapa |
 
Hay 2 posts recientes en el feed (el último, de 2017). Los singles de post y de portafolio, el archivo de categoría y la búsqueda existen y se conservan. No se detectó uso de flexslider, bxslider, jPlayer, testimonios, acordeón, barras de progreso, Flickr, Twitter, widget de contacto, formulario de contacto del tema, gmap3, countdown, tablas de precios ni slider personalizado (confirmar en T1.4 antes de borrar).
 
### 2.3 Mediciones en vivo (20-nov-2026, desde Colombia)
 
| Métrica | Valor |
|---|---|
| TTFB del home con caché de Cloudflare (`HIT`) | ~105–170 ms |
| TTFB sin caché (sirve el origen) | ~2.260 ms |
| **Peticiones de `retina.js`** | **53 por visita**, todas 404 servidos por WordPress (de 1,5 a 17,6 s cada una) |
| `css/dynamic-styles.php` | ~180 ms, arranca WordPress completo una segunda vez (`wp-load.php`) |
| JS del tema | 16 archivos, más jQuery 3.7.1 y Migrate 3.4.1 de WP y el Migrate 1.0.0 del tema |
| Imágenes del home | 52; 51 con `loading="lazy"`, incluidas las de la primera fila |
| Fuentes | Roboto con 10 variantes |
 
### 2.4 Estado de seguridad (21-nov-2026, verificado desde Chrome)
 
| # | Archivo | Problema | Estado |
|---|---|---|---|
| S1 | `library/sendmail.php` | Relay de spam abierto (`mail()` con destinatario y cabeceras desde `$_REQUEST`) | ✅ **404** |
| S2 | `contact-form.php` | Relay de spam: destinatario en campo oculto del POST | ✅ **404** |
| S9 | `library/admin-panel/notifier/` | 500 al llamarlo directo | ✅ **404** (carpeta borrada) |
| S3 | `ajax-load-more.php` | Carga `wp-load.php` y pasa `post_type`, `taxonomy`, `tag`, `postNotIn`, `numPosts` desde `$_GET` sin validar a `query_posts()`; `numPosts` sin límite | ⚠️ **200**. Lo usa "Cargar más" del blog. Se endurece (T2.2) |
| — | `library/admin-panel/table_process.php` | Error fatal de sintaxis en PHP 8; responde 500 y revela rutas | ⚠️ **500**. Se elimina (T2.6) |
| — | `library/admin-panel/pricing-tables.php` | Responde 200 al ser llamado directo | ⚠️ Se elimina junto con lo anterior |
| — | `notifier.xml` (raíz), `js/script.zip`, `js/jquery.nicescroll.OLD.min.js`, `images/preloader.orig.gif`, `readme.txt` | Archivos sueltos descargables; revelan la versión del tema | ⚠️ **200**. Se eliminan (T0.5) |
| S4 | `admin-functions.php` | Importar opciones usa `unserialize()` sin `current_user_can` | Pendiente (T2.3) |
| S5 | `post-options.php`, `custom-slider.php` | `save_post` sin nonce ni capacidad; `save_slide()` borra el meta de otros posts | Pendiente (T2.4) |
| S6 | `helper-functions.php` | 3 consultas SQL interpoladas | Pendiente (T2.5) |
| S7 | prettyPhoto 3.1.6 | Abandonado, con historial de XSS | Se reemplaza en el tema nuevo; en el saneamiento se escapa el título |
 
### 2.5 Análisis estático (PHP 8.3, sobre el zip 2.1)
 
- `php -l`: **único error fatal de sintaxis** en `table_process.php:99` (`$row_{$i}`). Producción tiene el mismo.
- PHPCompatibilityWP (8.5): 25 errores y 10 avisos en 10 archivos: `each()`, `magic_quotes`, `/e` en `preg_replace` (PHPMailer 2009), llaves en offsets, `utf8_encode()`, `openssl_free_key()`, `curl_close()` y constructores PHP 4. La mayoría está en código que se elimina (PHPMailer, Twitter/OAuth, tablas de precios, notificador).
- WPCS 3: 18.089 violaciones, de las cuales 15.624 son de formato. **No se corrigen en el saneamiento**; se atienden solo los puntos de seguridad (§2.4).
- Text domain inconsistente (`kappe` en `style.css`, `'Kappe'` en el código) y `load_theme_textdomain()` fuera de `after_setup_theme` (genera aviso en WP 6.7+). Se corrige en T2.7.
---
 
## 3. Requisitos del saneamiento
 
### 3.1 Plataforma
- Sin avisos, *deprecations* ni *notices* del tema en **PHP 8.4 (el del hosting) y 8.5**, con `WP_DEBUG` y `WP_DEBUG_LOG`, en las pantallas del frontend y del panel que se usan.
- PHPCompatibilityWP `8.4-`: **0 errores** en los archivos que se conservan.
- **El hosting (DreamHost compartido) corre PHP 8.4.** Las pruebas de 8.5 se hacen en local.
### 3.2 Rendimiento: metas
- **0** peticiones 404 generadas por el tema (hoy, 53 por visita).
- **0** cargas extra de WordPress por petición (hoy, `dynamic-styles.php`).
- Contenido visible sin esperar al evento `load` (hoy `body` y `#container` empiezan con `opacity: 0` y un preloader GIF).
- Primera fila del portafolio con `loading="eager"` y la primera imagen con `fetchpriority="high"`.
- Medición con Lighthouse móvil, sin caché de página, antes y después. No hay meta numérica global en el saneamiento, solo la mejora medida.
### 3.3 Accesibilidad mínima (se hace aquí porque es una línea de CSS o HTML)
- Quitar `maximum-scale=1` del viewport (bloquea el zoom).
- Contraste AA en el texto del menú lateral y de los créditos.
---
 
## 4. Librerías: qué se hace ahora y qué queda para el tema nuevo
 
| Librería | En el saneamiento | En el tema nuevo (feb-2027) |
|---|---|---|
| retina.js | **Eliminar** (causa los 53 × 404) | — |
| nicescroll, Modernizr, jQuery Migrate 1.0 del tema | **Eliminar** | — |
| PHPMailer, OAuth, TwitterOAuth, notificador | **Eliminar** (código muerto) | — |
| jQuery (WP) | Se mantiene | **Sin jQuery** en el frontend |
| Isotope, hoverdir | Se mantienen | CSS Grid + filtro nativo |
| prettyPhoto | Se mantiene (con título escapado) | Lightbox mantenido sin jQuery o `<dialog>` |
| Bootstrap (plugin Easy Bootstrap Shortcodes) | Se mantiene | **Sin Bootstrap**: grilla y componentes propios |
| Font Awesome 4 | Se mantiene | **SVG en línea** (solo se usan 6 íconos: th-large, film, search, desktop, pencil, print) |
| Google Fonts (Roboto, 10 variantes) | Reducir variantes (T3.5) | **Red Hat Display autoalojada** para todo (§8) |
 
---
 
## 5. Compatibilidad de datos (no romper)
 
Estos identificadores están en la base de datos. **No se renombran**:
 
- Opción del panel `weblusive_options` (array). Hay ~171 llamadas y ~80 claves; hacer el inventario con `grep weblusive_get_option`.
- Metas: `_weblusive_sidebar_pos`, `_page_portfolio_cat`, `_page_portfolio_num_items_page`, `_portfolio_type`, `_weblusive_sidebar_post`, `_weblusive_post_head`, `_weblusive_post_slider`, `_weblusive_promotext`, `_portfolio_no_lightbox`, `_portfolio_inner_lightbox`, `_portfolio_link`, `_portfolio_video`, `_portfolio_featured`, `_blog_videoap`, `_blog_video`, `_blog_mediatype` y `custom_slider`.
- CPT `portfolio` (slug `works`) y taxonomía `portfolio_category`. **Las URLs `/works/...` no cambian** durante el saneamiento (ver §6).
- Ubicación de menú `primary_nav`; sidebars `primary-widget-area`, `footer-sidebar-1…4` y los personalizados.
- Tamaños de imagen `portfolio`, `portfolio-single`, `blog-list`, `blog-thumb`, `blog-thumb-3`.
- Nombres de las plantillas de página (se guardan en `_wp_page_template`).
- Shortcodes en el contenido: como mínimo `tabgroup`, `tab`, `teammember`. Si se elimina uno que esté en la BD, dejar un *fallback* que devuelva `do_shortcode( $content )`.
- `slugify()` **se usa** (filtro del portafolio en `portfolio_template.php` y `portfolio_walker.php`): no se elimina; si se renombra, la salida debe ser idéntica.
- Traducción `languages/es_ES.mo/.po` de producción: se conserva.
- **Widgets:** los sidebars y widgets guardados en la BD no deben perderse al cambiar de 1.6 a 2.1 (verificar en staging, T1.5).
### 5.1 CSS adicional del Personalizador en producción
Hoy corrige errores del tema. El saneamiento resuelve la causa en el tema y **después** Pablo borra el bloque:
- Menú lateral fijo a pantalla completa (`#container > header { position: fixed; height: 100vh }`). Causa: `script.js` fija la altura del `<header>` con JS antes de que Isotope acomode la grilla.
- Margen superior de 25 px (`display: flow-root` en `#container` y `#content`). Causa: colapso de márgenes.
- Color de los créditos (`#F5F8E3`, enlace `#34B7AE` subrayado). Causa: `dynamic-styles.php` aplica `p, li, td { color: … !important; font-size: 16px !important }`. **Se conservan los colores de Pablo y no se cambian los tamaños.**
---
 
## 6. SEO del lado del tema (parte del saneamiento)
 
### 6.1 Datos de Search Console (16 meses, al 20-nov-2026)
- **222 clics y 9.555 impresiones** en total (~14 clics al mes). Casi todo el tráfico es de marca: "4to.pix" y "4to.pixel" suman ~51 clics. El home concentra 172 de los 222 clics.
- **67 de las 90 páginas con impresiones son `/works/...`**: 6.128 impresiones y solo 31 clics. Algunas posicionan bien (carnet de Cotecmar, posición 1,95; "carnet de visitante" y "piezas publicitarias" con cientos de impresiones y casi cero clics) pero son páginas casi sin texto, así que no atraen clics.
- **Indexación:** 52 indexadas y 239 no indexadas: 202 "descubierta, no indexada", 27 "rastreada, no indexada", 6 con 404, 3 con redirección, 1 bloqueada por robots.txt.
- **País:** Colombia 92 clics, Estados Unidos 74 (CTR 7,3 %). **Dispositivo:** 175 clics en escritorio y 47 en móvil (CTR 1,33 % en móvil).
### 6.2 Cambios en el tema (saneamiento)
1. **Un solo `<h1>` por página.** Revisar `header.php`, `portfolio_template.php`, `single.php`, `blog-template.php` y `contact-template.php`: hoy el logo o el título de sección compiten.
2. **Viewport sin `maximum-scale=1`** (permitir zoom).
3. **`<title>` y meta description correctos:** `add_theme_support( 'title-tag' )` y quitar el `<title>` manual. Mientras siga Yoast, la meta description la genera Yoast; el tema no debe duplicarla.
4. **`wp_body_open()`** después de `<body>` y `lang` correcto.
5. **Texto alternativo** en las imágenes del portafolio (usar el `alt` del adjunto, con respaldo al título del proyecto).
6. **Enlaces internos rastreables:** las tarjetas del portafolio deben enlazar al proyecto con `<a href>` real, no solo por JS.
7. **Primera fila del portafolio sin `lazy`** (también ayuda al LCP).
8. **`rel="canonical"`** no duplicado entre tema y Yoast.
### 6.3 Decisiones de SEO que van más allá del tema (se deciden con Pablo, no se ejecutan en el saneamiento)
- Qué hacer con los `/works/<slug>/` (hoy casi vacíos): indexarlos con contenido de caso de éxito, redirigir a una página de portafolio o noindex. Se decide **caso por caso** con los datos de Search Console, porque algunos ya posicionan.
- Sitemap de Yoast: quitar **attachment** y **author**; revisar el sitemap de portafolio.
- `robots.txt`: limpiar (`Crawl-delay: 10` lo ignora Google; las líneas de `/admin/`, `/includes/`, `/config/`, `/logs/`, `/backup/`, `/.env` no corresponden a WordPress). Revisar la 1 URL bloqueada.
- Corregir los 6 enlaces 404 y las 3 redirecciones.
- Migrar **Yoast → The SEO Framework** (extensión *Transport*) es un paso aparte, **después** del saneamiento y **antes** del tema nuevo, con respaldo de la BD y comparación de títulos y descripciones antes y después.
- Cloudflare mantiene `s-maxage` de 1 año: **purgar la caché** después de cada cambio de HTML.
---
 
## 7. Hosting, staging y seguridad del servidor
 
- **Hosting:** DreamHost compartido, PHP 8.4. Es lento: TTFB sin caché ≈ 2,2 s. No está claro cuánto es del tema, cuánto del hosting y cuánto de Wordfence (gratis: firewall, escaneo, bloqueo de login). Se mide en staging con y sin Wordfence.
- **Cloudflare:** plan gratis, delante del sitio. Revisar con Pablo (panel, por Chrome): reglas de WAF, Bot Fight Mode, reglas de caché y estado del proxy DNS. La idea es evaluar si Cloudflare cubre lo que hoy hace Wordfence.
  - Estado 21-nov-2026: regla de rate limiting para el login creada y activa; reglas de acceso por IP desconocidas eliminadas; la regla de caché la gestiona el plugin WP Super Page Cache (no editarla a mano); Bot Fight Mode apagado (decisión aplazada); Cloudflare avisa que la IP de origen está parcialmente expuesta por registros DNS-only; `xmlrpc.php` sin regla (decisión pendiente).
- **Staging:**
  - Para trabajar el tema: **entorno local** (LocalWP) con copia de la BD de producción y PHP 8.5.
  - Para medir rendimiento real: **subdominio en DreamHost** con contraseña y `noindex`, para que no compita con el sitio real.
  - El **VPS (DigitalOcean)** se reserva para el tema de bloques nuevo.
- **Nunca** se prueba en producción sin respaldo; el tema 1.6 actual se conserva en el servidor con otro nombre para poder volver atrás.
 
---
 
## 8. Requisitos que pasan al tema de bloques nuevo (febrero de 2027)
 
Nombre del tema: pendiente; **no es prioridad** (por ahora, "4to Pixel – Classic" solo si fuera un tema clásico, y este será de bloques).
 
- **Tema de bloques nativo** (`theme.json`, plantillas y *template parts* HTML, *patterns*), con el mínimo de dependencias y sin plugins de maquetación.
- **Sin jQuery**, sin Bootstrap, sin Font Awesome (SVG en línea) y sin el plugin Easy Bootstrap Shortcodes. Las 2–3 páginas hechas con esos shortcodes (`/servicios/`, `/nuestro-equipo/` y `/contactanos/`) se **reconstruyen a mano** con bloques; no hace falta un script de migración.
- **Tipografía:** **Red Hat Display** para todo, variable (300–900), autoalojada en woff2 con `font-display: swap`. Nota: existe una variante *Red Hat Text* pensada para párrafos; la decisión es usar solo Display.
- **HTML válido y accesible** (WCAG 2.2 AA), con **metadatos y schema** (Organization, WebSite, BreadcrumbList; `CreativeWork` o `Article` por proyecto; `Service` por servicio). Si se migra a The SEO Framework, que sea la fuente del schema.
- **Portafolio como casos de estudio:** plantilla de proyecto con cliente, problema, alcance del trabajo, solución, resultados y galería. Reemplaza a la galería de mockups sin texto.
- **Páginas nuevas:** servicios (una por servicio), experiencia y equipo, contacto, y una página de portafolio con filtros.
- **Blog:** al menos 2 publicaciones al mes.
- **Mantener las URLs con tráfico** o dejar redirecciones 301 desde `/works/...`, `/servicios/`, `/nuestro-equipo/` y `/contactanos/`, con comparación de Search Console antes y después.
- **Rendimiento:** CSS crítico mínimo, sin bloqueo del render, imágenes con `srcset`, `sizes`, WebP/AVIF y `fetchpriority`.
---
 
## 9. Decisiones pendientes (Pablo)
 
1. **Cloudflare:** acceso (panel por Chrome) para revisar WAF y caché.
2. **MCP de WordPress:** se deja para después; sirve para contenido y SEO, no para el tema.
3. **Editor:** ¿se usa el editor clásico o el de bloques? (decide si se eliminan los botones TinyMCE).
4. **Widgets en la barra lateral:** confirmar cuáles están activos antes de cambiar de 1.6 a 2.1.
5. **Estrategia de `/works/`:** caso por caso (§6.3).
---
 
## 10. Forma de trabajo para Claude Code
 
1. **Nunca tocar producción.** Trabajar en local con copia de la BD y PHP 8.5 (y 8.4 como el hosting).
2. Repositorio Git: commit 1 = tema de producción (1.6), commit 2 = base 2.1 con la traducción `es_ES`. Una **rama por fase**, commits pequeños.
3. Antes de cada fase, capturas de referencia de las 6 páginas de §2.2 a 1440 px y 390 px; después, comparar, revisar `debug.log` vacío y consola sin errores.
4. Herramientas: PHPCS con WPCS 3 y PHPCompatibilityWP, Query Monitor y Lighthouse CLI.
5. Sin dependencias nuevas de Composer ni npm en el tema.
6. Al terminar cada fase, marcar `TAREAS.md` y escribir en `CHANGELOG.md` qué cambió y qué debe probar Pablo.
7. Si una tarea depende de una decisión de §9 sin resolver, **detenerse y preguntar**.
 
