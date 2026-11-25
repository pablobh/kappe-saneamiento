#!/usr/bin/env python3
"""E1.5a · Lista de archivos del tema con tamaño, tipo y estado, y resumen por carpeta.

Salidas (en inventario/raw/):
  archivos.tsv  ruta, bytes, lineas, ext, tipo, estado, tarea, nota
  carpetas.tsv  carpeta, archivos, bytes, php, js, css, imagenes_fuentes
Uso: python3 -I 10-archivos.py <raiz-del-repo>
Solo lee. No modifica nada.
"""
import os
import re
import sys

raiz = os.path.abspath(sys.argv[1])
tema = os.path.join(raiz, "tema", "kappe")
raw = os.path.join(raiz, "inventario", "raw")
reglas_ruta = os.path.join(raiz, "inventario", "scripts", "reglas-estado.tsv")

reglas = []
with open(reglas_ruta, encoding="utf-8") as f:
    for linea in f:
        if not linea.strip() or linea.startswith("#"):
            continue
        c = linea.rstrip("\n").split("\t")
        reglas.append((re.compile(c[0]), c[1], c[2] if len(c) > 2 else "", c[3] if len(c) > 3 else ""))

IMG_FUENTE = {".png", ".jpg", ".jpeg", ".gif", ".svg", ".ico", ".webp", ".eot", ".ttf", ".woff", ".woff2", ".otf", ".mo"}
CODIGO = {".php", ".js", ".css", ".po", ".xml", ".txt", ".html", ".json", ".md"}

# Código de terceros (no se lee línea a línea; solo se anota la versión). Primera coincidencia gana.
TERCEROS = [
    r"^js/(?!script\.js$)",                       # librerías JS (jQuery, Bootstrap, Isotope, prettyPhoto, jPlayer…)
    r"^library/admin-panel/js/(colorpicker|jquery\.ui\.slider)\.js$",
    r"^addons/(fontawesome|flickr)/",
    r"^css/(?!dynamic-styles\.php$)",           # CSS de librerías (Bootstrap, etc.); dynamic-styles.php es propio
    r"^library/class\.phpmailer\.php$",
    r"^library/includes/(OAuth|twitteroauth)\.php$",
    r"^library/admin-panel/google-fonts\.php$",   # lista de Google Fonts
    r"^addons/wp-pagenavi\.php$",                 # WP-PageNavi embebido
]
TERCEROS = [re.compile(t) for t in TERCEROS]


def tipo_de(ruta, ext):
    if ext in IMG_FUENTE:
        return "mo" if ext == ".mo" else "imagen/fuente"
    if any(t.search(ruta) for t in TERCEROS):
        return "tercero"
    if ext in CODIGO:
        return "propio"
    return "otro"


def estado_de(ruta):
    for rx, estado, tarea, nota in reglas:
        if rx.search(ruta):
            return estado, tarea, nota
    return "se conserva", "", ""


def lineas_de(path):
    try:
        with open(path, "rb") as f:
            return sum(1 for _ in f)
    except OSError:
        return 0


filas = []
for base, dirs, files in os.walk(tema):
    dirs.sort()
    for nombre in sorted(files):
        if nombre == ".DS_Store":
            continue
        p = os.path.join(base, nombre)
        if os.path.islink(p):
            continue
        ruta = os.path.relpath(p, tema)
        ext = os.path.splitext(nombre)[1].lower()
        tipo = tipo_de(ruta, ext)
        estado, tarea, nota = estado_de(ruta)
        lin = lineas_de(p) if tipo in ("propio", "tercero") else ""
        filas.append((ruta, os.path.getsize(p), lin, ext, tipo, estado, tarea, nota))

with open(os.path.join(raw, "archivos.tsv"), "w", encoding="utf-8") as f:
    f.write("ruta\tbytes\tlineas\text\ttipo\testado\ttarea\tnota\n")
    for fila in filas:
        f.write("\t".join(str(x) for x in fila) + "\n")

carpetas = {}
for ruta, tam, _lin, ext, tipo, *_ in filas:
    d = os.path.dirname(ruta) or "."
    c = carpetas.setdefault(d, {"n": 0, "b": 0, "php": 0, "js": 0, "css": 0, "img": 0})
    c["n"] += 1
    c["b"] += tam
    if ext == ".php":
        c["php"] += 1
    elif ext == ".js":
        c["js"] += 1
    elif ext == ".css":
        c["css"] += 1
    elif tipo in ("imagen/fuente", "mo"):
        c["img"] += 1

with open(os.path.join(raw, "carpetas.tsv"), "w", encoding="utf-8") as f:
    f.write("carpeta\tarchivos\tbytes\tphp\tjs\tcss\timagenes_fuentes\n")
    for d in sorted(carpetas):
        c = carpetas[d]
        f.write(f"{d}\t{c['n']}\t{c['b']}\t{c['php']}\t{c['js']}\t{c['css']}\t{c['img']}\n")

propio = sum(1 for x in filas if x[4] == "propio")
propio_b = sum(x[1] for x in filas if x[4] == "propio")
print(f"archivos.tsv: {len(filas)} archivos ({propio} propios, {propio_b} bytes de código propio); carpetas.tsv: {len(carpetas)} carpetas")
