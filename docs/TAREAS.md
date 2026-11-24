# TAREAS — Saneamiento del tema Kappe (fases 0 a 3 + SEO del tema)
 
> Ordenadas por importancia: seguridad, luego lo que bloquea PHP 8.4/8.5, luego rendimiento y SEO del tema.
> Detalle y criterios en `SPECS.md`. Esto **no** es el refactor completo: el tema de bloques de febrero de 2027 lo reemplaza (SPECS §8).
> **Leyenda.** Impacto: 🔴 alto · 🟠 medio · 🟢 bajo. Estimaciones al final.
 
---
 
## Fase 0 — Mitigación inmediata en producción (Pablo, sin Claude Code)
 
Respaldo completo antes de empezar.
 
- [x] **T0.1** Borrar `library/sendmail.php` (S1). Verificado: 404.
- [x] **T0.2** Borrar `contact-form.php` (S2). Verificado: 404.
- [x] **T0.3** Borrar la carpeta `library/admin-panel/notifier/` (S9). Verificado: 404.
- [x] **T0.4** Borrar `library/class.phpmailer.php`. Verificado: 404.
- [x] **T0.5** Borrar archivos sueltos que hoy responden 200 (`wp-content/themes/kappe/`): `notifier.xml`, `js/script.zip`, `js/jquery.nicescroll.OLD.min.js`, `images/preloader.orig.gif`, `readme.txt`. 🟢 · riesgo nulo
- [x] **T0.6** Borrar `library/admin-panel/pricing-tables.php` y `library/admin-panel/table_process.php` (500 con error fatal de sintaxis en PHP 8). Antes, confirmar que no hay un menú de admin que los llame (es el submenú "Pricing Tables" del tema). 🟠 · riesgo bajo
- [x] **T0.7** Purgar la caché de Cloudflare de esas URLs y volver a probar que todas respondan 404.
`ajax-load-more.php` **no se borra**: lo usa "Cargar más" del blog. Se corrige en T2.2.
 
---
 
## Fase 1 — Preparación
 
- [ ] **T1.1** Montar WordPress local (LocalWP) con la BD y los `uploads` de producción, PHP 8.5 y 8.4.
- [ ] **T1.2** Repositorio Git: commit 1 = tema de producción (Kappe 1.6, zip `kappe-live`), commit 2 = base 2.1 más `languages/es_ES.mo/.po` de producción. Registrar las diferencias en `CAMBIOS-PRODUCCION.md` (ya hay una lista en SPECS §2.1).
- [ ] **T1.3** **Confirmar la causa de la caída al instalar la 2.1:** activar la 2.1 en local con PHP 8.4 y abrir "Pricing Tables" y el resto del admin; comprobar que el error es `table_process.php:99`. Registrar el resultado.
- [ ] **T1.4** **Inventario de uso en la BD** (WP-CLI o SQL): shortcodes presentes en `post_content`; widgets activos (`sidebars_widgets`, `widget_*`); posts del CPT `weblusive_slider`; páginas con `under-construction.php` o `contact-template.php`; metas `_blog_mediatype` y `custom_slider`; claves con valor en `weblusive_options`. Resultado: `INVENTARIO.md`.
- [ ] **T1.5** **Compatibilidad 1.6 → 2.1:** con la BD de producción en local, activar la 2.1 y comparar las 6 páginas de SPECS §2.2 y el home con capturas de 1440 px y 390 px. Verificar que los widgets, los menús, las opciones y los metas se conservan. **Si algo no es compatible, parar y avisar a Pablo antes de seguir.**
- [ ] **T1.6** Línea base: Lighthouse móvil y escritorio de las 6 páginas, sin caché de página.
- [ ] **T1.7** Staging medible: subdominio en DreamHost con contraseña y `noindex`. Medir TTFB y Lighthouse con y sin Wordfence para separar tema, hosting y plugin.
---
 
## Fase 2 — Seguridad y PHP 8.4/8.5
 
- [ ] **T2.1** Quitar del repo `sendmail.php`, `contact-form.php`, `class.phpmailer.php`, `notifier/`, `pricing-tables.php`, `table_process.php`, `table_wizard.php` y `notifier.xml`, más los archivos sueltos de T0.5. Quitar las referencias en `functions.php` y `admin-functions.php`. 🔴 · riesgo bajo
- [ ] **T2.2** **Endurecer "Cargar más":** reemplazar `ajax-load-more.php` por un endpoint REST (`register_rest_route`) o `wp_ajax_nopriv_`, con lista blanca de `post_type` (`post`) y taxonomías, `per_page` máximo 24, `absint` en la paginación y `WP_Query` en vez de `query_posts`. Actualizar la llamada en `js/script.js` (línea ~57). Eliminar `ajax-load-more.php` y no cargar `wp-load.php`. 🔴 · riesgo medio (probar paginación y fin de resultados)
- [ ] **T2.3** Importar y exportar opciones con JSON en vez de `serialize`/`unserialize`, con `current_user_can( 'edit_theme_options' )`. 🟠 · riesgo bajo
- [ ] **T2.4** Meta boxes (`post-options.php`): nonce, `current_user_can( 'edit_post', $post_id )`, usar `$post_id` del hook, sanitizar y salir en autosave y revisiones. `save_slide()` **no debe borrar** el meta si el campo no vino en el POST. 🟠 · riesgo bajo
- [ ] **T2.5** Reemplazar las 3 consultas SQL de `helper-functions.php` por APIs de WP (`get_post_ancestors()`, `get_the_title()`, `get_posts()` con `meta_key`). 🟠 · riesgo bajo
- [ ] **T2.6** Twitter/OAuth: eliminar el widget, `OAuth.php` y `twitteroauth.php` (`utf8_encode`, `openssl_free_key`, API muerta). Si T1.4 muestra que Flickr o el widget de contacto no se usan, eliminarlos también. 🟠 · riesgo bajo
- [ ] **T2.7** `functions.php`: mover `load_theme_textdomain`, `register_nav_menus`, `add_theme_support` y `add_image_size` a `after_setup_theme`; sidebars y `register_widget()` a `widgets_init`; `dynamic_sidebar()` por ID; redirección tras activar con `after_switch_theme`. Unificar el text domain a `kappe`. Quitar la doble carga de `style.css` y el Google Maps por `http`. 🟠 · riesgo medio
- [ ] **T2.8** `the_breadcrumb.php`: constructor `__construct` (ya viene corregido en la 2.1). 🟢 · riesgo nulo
- [ ] **T2.9** Escapar el título y la descripción que prettyPhoto inserta como HTML (S7) y `single.php`: iframes de YouTube y Vimeo por `https` con `youtube-nocookie.com`. 🟠 seguridad · riesgo bajo
- [ ] **T2.10** PHPCompatibilityWP `8.4-` y `8.5` hasta **0 errores**. Navegar el sitio y el admin en PHP 8.4 y 8.5 con `WP_DEBUG` y dejar `debug.log` vacío. 🔴
**Cómo probar:** las 6 páginas cargan, el blog pagina y termina, guardar una página y un proyecto conserva sus opciones, el panel del tema guarda e importa.
 
---
 
## Fase 3 — Rendimiento: los cambios de mayor impacto
 
- [ ] **T3.1** **Eliminar `retina.js`.** Quita 53 peticiones 404 por visita, cada una con una ejecución completa de WordPress (de 1,5 a 17 s). 🔴 · riesgo nulo
- [ ] **T3.2** **Eliminar `css/dynamic-styles.php`:** generar el CSS en PHP y encolarlo con `wp_add_inline_style()`, cacheado en un transient que se invalide al guardar opciones, con custom properties. **Quitar `p, li, td { color/font-size !important }`** y aplicar la tipografía a `body`. Integrar los colores de los créditos (`#F5F8E3` y enlace `#34B7AE`) como valores del tema. 🔴 · riesgo medio (comparar colores)
- [ ] **T3.3** **Mostrar el contenido sin esperar al evento `load`:** quitar `opacity: 0` de `body` y `#container`, y el preloader GIF. 🔴 FCP/LCP · riesgo bajo
- [ ] **T3.4** **Altura del menú lateral resuelta en CSS** (`position: fixed` con `100dvh` y `overflow` propio, más `display: flow-root` en `#container` y `#content`), quitando el JS que fija la altura de `<header>`. Esto reemplaza el CSS adicional del Personalizador (SPECS §5.1). 🔴 · riesgo bajo
- [ ] **T3.5** Fuentes: pedir solo los pesos que se usan (probablemente 300, 400, 500 y 700) en vez de 10 variantes, con `display=swap`. 🟠 · riesgo bajo
- [ ] **T3.6** Carga condicional y `defer`: encolar cada script y estilo solo donde se usa y con `strategy => defer` en el footer. Quitar Modernizr, nicescroll y el jQuery Migrate 1.0 del tema. 🔴 · riesgo bajo
- [ ] **T3.7** Imágenes del portafolio: `loading="eager"` en la primera fila y `fetchpriority="high"` en la primera, con `sizes` correcto. 🟠 LCP · riesgo nulo
**Cómo probar:** Lighthouse contra T1.6. Pestaña Red sin 404 y sin peticiones a `dynamic-styles.php`.
 
---
 
## Fase 3B — SEO del lado del tema
 
- [ ] **T3B.1** **Un solo `<h1>` por página** (header, portafolio, single, blog, contacto). Las demás cabeceras bajan a `<h2>`. 🟠
- [ ] **T3B.2** Viewport sin `maximum-scale=1`. 🟠 accesibilidad y móvil
- [ ] **T3B.3** `add_theme_support( 'title-tag' )`, quitar el `<title>` manual, evitar `meta description` y `canonical` duplicados con Yoast. 🟠
- [ ] **T3B.4** `wp_body_open()` y atributo `lang` correcto. 🟢
- [ ] **T3B.5** `alt` en las imágenes del portafolio (alt del adjunto, con respaldo al título del proyecto). 🟠
- [ ] **T3B.6** Las tarjetas del portafolio enlazan con `<a href>` real al proyecto (no solo por JS). 🟠
- [ ] **T3B.7** Contraste AA y foco visible en el menú, enlaces y créditos. 🟠 accesibilidad
---
 
## Fase 4 — Pruebas y despliegue
 
- [ ] **T4.1** PHPCompatibilityWP en 0. `debug.log` vacío en PHP 8.4 y 8.5.
- [ ] **T4.2** Comparación visual contra las capturas de T1.5 (1440 px y 390 px). Diferencias solo si son mejoras acordadas.
- [ ] **T4.3** Lighthouse después contra T1.6, y prueba de teclado en el home y el blog.
- [ ] **T4.4** Despliegue: respaldo completo; subir como carpeta nueva (`kappe-saneado`) conservando el 1.6 actual como `kappe`; activar; **purgar la caché de Cloudflare**; revisar en vivo; después quitar el CSS adicional del Personalizador (SPECS §5.1).
- [ ] **T4.5** Instrucciones para Pablo: qué probar y cómo volver atrás.
---
 
## SEO fuera del tema (con Pablo; se hace aparte, antes del tema nuevo)
 
- [ ] **S.1** Decidir caso por caso qué hacer con los `/works/<slug>/` (SPECS §6.3). Entregable: tabla URL → acción, usando los datos de Search Console.
- [ ] **S.2** Sitemap de Yoast: quitar attachment y author. Limpiar `robots.txt`.
- [ ] **S.3** Corregir los 6 enlaces 404 y las 3 redirecciones; revisar la URL bloqueada por robots.txt.
- [ ] **S.4** Migrar Yoast → The SEO Framework (extensión *Transport*): respaldo, exportar títulos y descripciones antes, migrar, comparar después.
- [ ] **S.5** Evaluar Cloudflare (WAF, Bot Fight Mode, reglas de caché) frente a Wordfence, y quitar Wordfence solo si está cubierto.
  - Hecho (21-nov-2026): regla de rate limiting "Limitar intentos de login WordPress" (POST a `/wp-login.php`, 5 req/10 s por IP, Block 10 s, activa); borradas las 5 reglas de acceso por IP "Allow" que Pablo no reconocía (158.69.204.91, 162.213.255.28, 198.100.156.65, 192.64.119.84, 136.179.15.207), lista vacía. Si algo falla en los próximos días, se pueden recrear.
  - Pendiente: decidir si se bloquea `/xmlrpc.php` (Pablo cree que no se usa; verificar con logs de DreamHost/Wordfence o bloquear y observar); revisar registros DNS "DNS only" que exponen la IP de origen (aviso de Cloudflare); Bot Fight Mode de Cloudflare vs. protección anti-bots de DreamHost (evitar doble protección); Wordfence vs. Cloudflare.
---
 
## Tema de bloques (febrero de 2027): requisitos ya definidos
 
Ver SPECS §8. Resumen: Red Hat Display para todo, sin jQuery ni Bootstrap ni Font Awesome, páginas de servicios, portafolio como casos de estudio, blog 2 veces al mes, schema, HTML válido y accesible, redirecciones 301 para las URLs con tráfico.
 
---
 
## Estimaciones
 
El código propio del tema suma ~550 KB (unos 150.000 tokens para leerlo una vez). El saneamiento toca una fracción: seguridad, `functions.php`, `header.php`, 3–4 plantillas, `dynamic-styles.php` y el CSS.
 
| Fase | Horas (Claude Code + pruebas) | Tokens procesados (aprox.) | Modelo sugerido |
|---|---|---|---|
| 0 — Mitigación | 0,5 (solo Pablo) | 0 | — |
| 1 — Preparación | 2–4 | 0,5–1 M | Sonnet |
| 2 — Seguridad y PHP 8.4/8.5 | 3–5 | 1,5–3 M | **Opus** |
| 3 — Rendimiento | 2–4 | 1–2 M | Opus (T3.2, T3.4); Sonnet para el resto |
| 3B — SEO del tema | 1–2 | 0,3–0,6 M | Sonnet |
| 4 — Pruebas y despliegue | 2–3 | 0,5–1 M | Opus |
| **Total** | **≈ 11–18 h** | **≈ 4–8 M** | |
 
- La mayor parte de esos tokens es entrada leída desde caché; la salida suele ser una fracción pequeña.
- Con el plan Pro, distribuirlo en 3–5 sesiones (límite de 5 horas y límite semanal compartido con el chat). Evitar las horas pico (entre semana de 8 a. m. a 2 p. m., hora de Bogotá).
- Usar `/clear` al terminar cada fase y dejar `SPECS.md` y `TAREAS.md` como memoria entre sesiones.
- El SEO fuera del tema (S.1–S.5) no está incluido: depende de las decisiones de Pablo.
 
