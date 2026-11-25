#!/usr/bin/env python3
"""E1.5a · Extrae del código del tema hooks, shortcodes, enqueues, opciones, metas, SQL, AJAX,
definiciones y entradas/salidas sensibles, como archivo:línea:coincidencia (TSV).

Salidas (en inventario/raw/), todas con columnas: archivo, linea, nombre, detalle
  codigo-hooks.tsv         add_action/add_filter/remove_*  (nombre = hook, detalle = callback)
  codigo-shortcodes.tsv    add_shortcode                   (nombre = etiqueta)
  codigo-registros.tsv     register_*, add_theme_support, add_image_size, add_meta_box, add_*_page
  codigo-enqueues.tsv      wp_enqueue_* / wp_register_*    (nombre = handle, detalle = origen)
  codigo-opciones.tsv      weblusive_get_option, get_option, update_option, get_theme_mod…
  codigo-opciones-def.tsv  ids de opciones definidos en el panel ("id" => "…")
  codigo-metas.tsv         get/update/add/delete_post_meta y $get_meta["…"]
  codigo-sql.tsv           $wpdb->…
  codigo-ajax.tsv          wp_ajax_*, admin-post, admin-ajax, $.ajax / $.post / ajaxurl
  codigo-plantillas.tsv    "Template Name:", get_template_part, include/require, locate_template
  codigo-definiciones.tsv  function / class por archivo
  codigo-entradas.tsv      $_GET/$_POST/$_REQUEST/$_COOKIE/$_FILES/$_SERVER[...]
  seguridad-por-archivo.tsv  conteos por archivo: entradas, mail(), eval, unserialize, $wpdb, prepare,
                             escapes, nonces, current_user_can
Uso: python3 -I 20-codigo.py <raiz-del-repo>
Solo lee. No modifica nada. Las coincidencias se cortan a 140 caracteres.
"""
import os
import re
import sys

raiz = os.path.abspath(sys.argv[1])
tema = os.path.join(raiz, "tema", "kappe")
raw = os.path.join(raiz, "inventario", "raw")

MAX = 140


def corta(s):
    s = " ".join(s.split())
    return s if len(s) <= MAX else s[: MAX - 1] + "…"


def q(g):
    return r"""['"]""" + g + r"""['"]"""


# (archivo de salida, regex, grupo del nombre, grupo del detalle o None)
PHP_REGLAS = [
    ("hooks", re.compile(r"""\b(add_action|add_filter|remove_action|remove_filter)\s*\(\s*['"]([^'"]+)['"](?:\s*,\s*([^,)]*))?"""), 2, 3, 1),
    ("shortcodes", re.compile(r"""\badd_shortcode\s*\(\s*['"]([^'"]+)['"](?:\s*,\s*([^,)]*))?"""), 1, 2, None),
    ("registros", re.compile(r"""\b(register_[a-z_]+|add_theme_support|add_image_size|set_post_thumbnail_size|add_meta_box|add_menu_page|add_submenu_page|add_options_page|add_theme_page|add_editor_style|add_image_size)\s*\(\s*(?:['"]([^'"]*)['"])?"""), 2, None, 1),
    ("enqueues", re.compile(r"""\b(wp_enqueue_script|wp_enqueue_style|wp_register_script|wp_register_style|wp_localize_script|wp_deregister_script|wp_deregister_style)\s*\(\s*['"]([^'"]+)['"](?:\s*,\s*([^,)]*))?"""), 2, 3, 1),
    ("opciones", re.compile(r"""\b(weblusive_get_option|get_option|update_option|add_option|delete_option|get_theme_mod|set_theme_mod|remove_theme_mod|get_site_option|get_transient|set_transient)\s*\(\s*(?:['"]([^'"]+)['"]|(\$[A-Za-z_]+))"""), 2, 3, 1),
    ("metas", re.compile(r"""\b(get_post_meta|update_post_meta|add_post_meta|delete_post_meta|get_post_custom_values)\s*\(\s*[^,]+,\s*['"]([^'"]+)['"]"""), 2, None, 1),
    ("metas", re.compile(r"""\$get_meta\s*\[\s*['"]([^'"]+)['"]"""), 1, None, None),
    ("sql", re.compile(r"""\$wpdb\s*->\s*([A-Za-z_]+)"""), 1, None, None),
    ("ajax", re.compile(r"""\b(wp_ajax_nopriv_[A-Za-z0-9_]+|wp_ajax_[A-Za-z0-9_]+|admin_post_nopriv_[A-Za-z0-9_]+|admin_post_[A-Za-z0-9_]+)"""), 1, None, None),
    ("ajax", re.compile(r"""(admin-ajax\.php|\bajaxurl\b|ajax-load-more\.php|wp-load\.php)"""), 1, None, None),
    ("plantillas", re.compile(r"""Template Name:\s*(.+?)\s*(?:\*/)?$"""), 1, None, None),
    ("plantillas", re.compile(r"""\b(get_template_part|locate_template|get_header|get_footer|get_sidebar|include|include_once|require|require_once)\b\s*\(?\s*([^;]*)"""), 1, 2, None),
    ("plantillas", re.compile(r"""\b(is_page_template)\s*\(\s*['"]([^'"]+)['"]"""), 2, None, 1),
    ("definiciones", re.compile(r"""^\s*(?:abstract\s+|final\s+)?(function|class)\s+&?([A-Za-z_][A-Za-z0-9_]*)"""), 2, None, 1),
    ("entradas", re.compile(r"""\$_(GET|POST|REQUEST|COOKIE|FILES|SERVER)\s*\[\s*['"]?([A-Za-z0-9_\-]*)"""), 2, None, 1),
]

JS_REGLAS = [
    ("ajax", re.compile(r"""(\$\.ajax|jQuery\.ajax|\$\.post|\$\.get|\$\.getJSON|admin-ajax\.php|\bajaxurl\b|ajax-load-more\.php|XMLHttpRequest|fetch\s*\()"""), 1, None, None),
]

DEF_OPC = re.compile(r"""["']id["']\s*=>\s*["']([^"']+)["']""")

CONTEOS = [
    ("entradas", re.compile(r"""\$_(GET|POST|REQUEST|COOKIE|FILES)\s*\[""")),
    ("server", re.compile(r"""\$_SERVER\s*\[""")),
    ("mail", re.compile(r"""\bmail\s*\(""")),
    ("eval", re.compile(r"""\b(eval|assert|create_function|preg_replace\s*\(\s*['"][^'"]*/e['"]|shell_exec|exec|system|passthru|popen|proc_open)\s*\(""")),
    ("unserialize", re.compile(r"""\b(unserialize|maybe_unserialize)\s*\(""")),
    ("wpdb", re.compile(r"""\$wpdb\s*->""")),
    ("prepare", re.compile(r"""\$wpdb\s*->\s*prepare\s*\(""")),
    ("escapes", re.compile(r"""\b(esc_html|esc_attr|esc_url|esc_js|esc_textarea|esc_sql|wp_kses|wp_kses_post|sanitize_[a-z_]+|intval|absint)\s*\(""")),
    ("nonces", re.compile(r"""\b(wp_verify_nonce|check_admin_referer|check_ajax_referer|wp_nonce_field|wp_create_nonce)\s*\(""")),
    ("capacidades", re.compile(r"""\b(current_user_can|is_admin|is_user_logged_in)\s*\(""")),
    ("red", re.compile(r"""\b(file_get_contents|curl_init|wp_remote_get|wp_remote_post|fsockopen|fopen)\s*\(""")),
]

# Archivos de terceros: se escanean igual (los hooks no aparecerán) pero el inventario los marca con 10-archivos.
salidas = {n: [] for n in ("hooks", "shortcodes", "registros", "enqueues", "opciones", "opciones-def",
                           "metas", "sql", "ajax", "plantillas", "definiciones", "entradas")}
seguridad = []


def leer(path):
    with open(path, "rb") as f:
        data = f.read()
    try:
        return data.decode("utf-8")
    except UnicodeDecodeError:
        return data.decode("latin-1")


# JS propios (no minificados ni de terceros)
JS_PROPIOS = re.compile(r"^(js/script\.js|library/admin-panel/js/(main|helper)\.js|library/functions/shortcodes/shortcode\.js)$")

for base, dirs, files in os.walk(tema):
    dirs.sort()
    for nombre in sorted(files):
        p = os.path.join(base, nombre)
        if os.path.islink(p):
            continue
        ruta = os.path.relpath(p, tema)
        ext = os.path.splitext(nombre)[1].lower()
        if ext == ".php":
            reglas = PHP_REGLAS
        elif ext == ".js" and JS_PROPIOS.match(ruta):
            reglas = JS_REGLAS
        else:
            continue
        texto = leer(p)
        lineas = texto.replace("\r\n", "\n").replace("\r", "\n").split("\n")
        conteos = {k: 0 for k, _ in CONTEOS}
        for n, linea in enumerate(lineas, 1):
            if ext == ".php":
                # Comentarios de una línea completa: no cuentan como uso real.
                if re.match(r"\s*(//|#|\*|/\*)", linea) and "Template Name:" not in linea:
                    continue
                for clave, rx in CONTEOS:
                    conteos[clave] += len(rx.findall(linea))
                m = DEF_OPC.search(linea)
                if m and ruta.startswith("library/admin-panel/"):
                    salidas["opciones-def"].append((ruta, n, m.group(1), corta(linea)))
            for clave, rx, gn, gd, gk in reglas:
                m = rx.search(linea)
                if not m:
                    continue
                nombre_ = (m.group(gn) or "").strip() if gn else ""
                detalle = (m.group(gd) or "").strip() if gd else ""
                if gk and clave in ("hooks", "registros", "enqueues", "opciones", "metas", "definiciones", "entradas", "plantillas"):
                    detalle = f"{m.group(gk)}" + (f" → {detalle}" if detalle else "")
                if clave == "registros" and not nombre_:
                    nombre_ = ""
                salidas[clave].append((ruta, n, nombre_ or corta(m.group(0)), corta(detalle)))
        if ext == ".php" and any(conteos.values()):
            seguridad.append((ruta, conteos))

for clave, filas in salidas.items():
    with open(os.path.join(raw, f"codigo-{clave}.tsv"), "w", encoding="utf-8") as f:
        f.write("archivo\tlinea\tnombre\tdetalle\n")
        for fila in filas:
            f.write("\t".join(str(x) for x in fila) + "\n")

with open(os.path.join(raw, "seguridad-por-archivo.tsv"), "w", encoding="utf-8") as f:
    cols = [k for k, _ in CONTEOS]
    f.write("archivo\t" + "\t".join(cols) + "\n")
    for ruta, c in seguridad:
        f.write(ruta + "\t" + "\t".join(str(c[k]) for k in cols) + "\n")

print("  ".join(f"{k}:{len(v)}" for k, v in salidas.items()))
