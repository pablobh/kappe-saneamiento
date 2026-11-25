# CAMBIOS-PRODUCCION — Kappe 1.6 (producción) frente a la base 2.1

Diferencias entre el tema que corre en producción (tag `prod-1.6`) y la base del saneamiento (tag `base-2.1` = Kappe 2.1 + traducción `es_ES` de producción). Generado en E1.2 con `git diff` entre los dos tags, solo en local.

> Este documento **describe**; no decide. El análisis de riesgo y las propuestas de `script.js`, `prettyPhoto.js`, `addons/paginajax` y `es_ES.po` van en E1.3; la causa de la caída de la 2.1 (`table_process.php:99`), en E1.4.

## Resumen

- 106 archivos aparecen distintos con `git diff` normal, pero **83 son solo fin de línea**: la 1.6 usa LF y la 2.1 usa CRLF.
- Ignorando eso (`git diff --ignore-cr-at-eol`) quedan **23 archivos** con cambios reales: 2.431 líneas añadidas y 3.342 quitadas, casi todo por tres archivos (`class.phpmailer.php` +2.320, `jquery.nicescroll.OLD.min.js` −3.252 y `style.css`).
- Para ver diffs reales: `git diff --ignore-cr-at-eol prod-1.6 base-2.1`.
- `languages/es_ES.po`, `es_ES.mo` y `en_US.*`: **iguales** entre los dos tags (solo CRLF).
- `library/admin-panel/table_process.php` (donde cae la 2.1): sin cambios de contenido entre tags (solo CRLF).
- `addons/paginajax/`: no existe en ninguno de los dos tags (en producción hay una carpeta vacía que Git no guarda).

## Archivos añadidos (2.1)

| Archivo | Qué es |
|---|---|
| `library/class.phpmailer.php` | PHPMailer (2.320 líneas). Se elimina en E2.A1. |
| `library/sendmail.php` | Envío de correo con `mail()` y datos de `$_REQUEST` sin validar (widget de contacto). Se elimina en E2.A1. |

## Archivos eliminados (2.1)

| Archivo | Comentario |
|---|---|
| `images/preloader.orig.gif` | Copia sobrante del preloader. |
| `js/jquery.nicescroll.OLD.min.js` | Versión vieja de nicescroll (referencias por confirmar en el inventario). |
| `js/script.zip` | Archivo suelto. |
| `notifier.xml` | Datos del notificador de actualizaciones del desarrollador. |

## Cambios de comportamiento o contenido

### Plantillas y PHP del tema

| Archivo | Cambio 1.6 → 2.1 |
|---|---|
| `functions.php` | **Activa `widgets.php`** (en la 1.6 el `require_once` está comentado: los widgets propios no se cargan en producción). |
| `functions.php` | `style.css` se encola con `get_template_directory_uri()` en vez de `get_stylesheet_directory_uri()` (idéntico sin tema hijo). |
| `functions.php` | Google Maps: lee la nueva opción `google_api`; solo carga el mapa si hay dirección **y** clave; la URL pasa de `?sensor=false` a `?key=…` (sigue en `http://`); los *handles* pasan a minúsculas (`google-map-api`, `google-map`). Sin clave, el mapa ya no se carga. |
| `library/functions/widgets.php` | Constructores PHP 4 → `__construct()` y `parent::__construct()` en los 7 widgets (compatibilidad PHP 8). |
| `library/includes/the_breadcrumb.php` | Constructor `the_breadcrumb()` → `__construct()` (en PHP 8 el método antiguo ya no es constructor). |
| `library/admin-panel/admin-ui.php` | Nuevo campo **"Google API Key"** (`id: google_api`) en el panel; añade una clave nueva a `weblusive_options`, no renombra ninguna existente. |
| `library/admin-panel/admin-functions.php` | Se quita el submenú "Documentation" (sigue el submenú "Pricing Tables"). |
| `library/admin-panel/notifier/update-notifier.php` | URL del XML del notificador: `weblusive-themes.com/Kappe/notifier.xml` → `premiumlayers.net/_themes/Kappe/notifier.xml`. |
| `blog-template.php` | `$cat = &get_category(...)` → `$cat = get_category(...)` (sin referencia; deja el original comentado). |
| `single.php` | El iframe de YouTube queda **comentado con un truco mixto HTML/PHP** (`<!-- --><?php //elseif …`): las entradas con vídeo YouTube dejan de mostrarlo. Vimeo y el resto siguen igual. |
| `loop.php` | Iframe de YouTube con `allowfullscreen`; el resto, espacios en blanco. |
| `contact-template.php` | Título "Send us a message" → "Send a message"; el mensaje deja de ser obligatorio en la validación (`required: false`, con `minlength: 6`); mensaje de error del correo con **"611 characters"** (errata, debería ser 6); nuevo mensaje `minlength` del mensaje. |
| `header.php`, `footer.php`, `portfolio_template.php`, `portfolio_template2.php` | Solo espacios en blanco. |

### JavaScript

| Archivo | Cambio 1.6 → 2.1 |
|---|---|
| `js/script.js` | `prettyPhoto`: `default_width/height` 500×344 → 853×480; el resto, un salto de línea. |
| `js/jquery.prettyPhoto.js` | Una línea minificada; la única diferencia es que añade el atributo `allowfullscreen` al iframe de vídeo. |

### CSS

| Archivo | Cambio 1.6 → 2.1 |
|---|---|
| `style.css` | Cabecera del tema reescrita: `Version: 2.1`, nuevas URI y autor, `License: GNU General Public License`, `Text Domain: kappe`, etiquetas nuevas. |
| `style.css` | Capa *hover* del portafolio: `rgba(0,0,0,0.8)` (negro) → `rgba(36,176,174,0.9)` (turquesa). **Cambio visible.** |
| `style.css` | Nuevo `@media (max-device-width: 768px)` para que el iframe de vídeo de prettyPhoto ocupe 100 %. |

### Traducciones

- `languages/es_ES.po` y `en_US.po` son iguales en los dos tags, pero la 2.1 usa textos nuevos que **no están en `es_ES.po`**: "Send a message" (el `.po` tiene "Send us a message") y "Please enter at least 6 characters.". En la 2.1 saldrían en inglés en el formulario de contacto. El texto "E-mail address should contain at least 611 characters" tampoco coincide con el `msgid` del `.po` (que dice 6).

## Pendiente en otros documentos

- E1.3: `script.js`, `prettyPhoto.js`, `addons/paginajax`, `es_ES.po` (riesgo y propuesta).
- E1.4: caída de la 2.1 en `table_process.php:99`.
- E1.6: compatibilidad 1.6→2.1 con capturas (el *hover* turquesa y el formulario de contacto son candidatos a revisar).
