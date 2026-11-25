#!/usr/bin/env bash
# E1.5a · php -l de cada archivo PHP del tema con PHP 8.2 (producción), 8.4 y 8.5, y búsqueda de patrones
# que PHP 8.x quitó o desaconsejó. Solo lee.
#
# Salidas (en inventario/raw/):
#   lint.tsv              archivo, php8.2, php8.4, php8.5   (OK o el primer mensaje de error)
#   compat-patrones.tsv   archivo, linea, patron, texto      (aproximación a PHPCompatibilityWP)
#
# NOTA: PHPCompatibilityWP/phpcs NO está instalado (instalar herramientas requiere confirmación de Pablo).
# `php -l` solo detecta errores de sintaxis; los patrones de abajo cubren lo más conocido, pero no sustituyen
# a PHPCompatibilityWP, que se ejecuta en E3.1.
source "$(dirname "${BASH_SOURCE[0]}")/_comun.sh"

P82="$(php_de 8.2)"; P84="$(php_de 8.4)"; P85="$(php_de 8.5)"

{
  printf 'archivo\tphp8.2\tphp8.4\tphp8.5\n'
  cd "$TEMA"
  find . -name '*.php' -type f ! -type l | sed 's|^\./||' | LC_ALL=C sort | while IFS= read -r f; do
    row="$f"
    for bin in "$P82" "$P84" "$P85"; do
      out="$("$bin" -d display_errors=0 -l "$f" 2>&1 | tr -d '\r' | head -1 || true)"
      case "$out" in
        "No syntax errors detected"*) row="$row	OK" ;;
        *) row="$row	$(printf '%s' "$out" | sed -E "s|in $TEMA/||; s/^(PHP )?(Parse|Fatal) error: *//" | cut -c1-140)" ;;
      esac
    done
    printf '%s\n' "$row"
  done
} > "$RAW/lint.tsv"

# Patrones de incompatibilidad con PHP 8.x (aproximación)
py - "$TEMA" "$RAW/compat-patrones.tsv" <<'PY'
import os, re, sys
tema, salida = sys.argv[1], sys.argv[2]
PATRONES = [
    ("llaves-offset $a{$i}",      r"\$[A-Za-z_]+\{[^}]+\}"),            # eliminado en 8.0
    ("create_function",           r"\bcreate_function\s*\("),           # eliminado en 8.0
    ("each()",                    r"(?<![A-Za-z_>:.])each\s*\("),        # eliminado en 8.0
    ("mysql_*",                   r"\bmysql_[a-z_]+\s*\("),             # eliminado en 7.0
    ("ereg/split",                r"\b(ereg|eregi|split|spliti)\s*\("), # eliminado en 7.0
    ("magic_quotes",              r"\b(get_magic_quotes_gpc|magic_quotes_runtime|set_magic_quotes_runtime)\s*\("),
    ("money_format",              r"\bmoney_format\s*\("),              # eliminado en 8.0
    ("utf8_encode/decode",        r"\butf8_(encode|decode)\s*\("),      # desaconsejado en 8.2
    ("${var} en cadenas",         r"\"[^\"]*\$\{[A-Za-z_]+\}[^\"]*\""), # desaconsejado en 8.2
    ("asignar por referencia =&", r"=\s*&\s*(new\s|[a-z_]+\s*\()"),     # aviso en 7/8
    ("(real)/(unset)",            r"\((real|unset|binary)\)"),
    ("FILTER_SANITIZE_STRING",    r"FILTER_SANITIZE_STRING"),           # desaconsejado en 8.1
    ("strftime/gmstrftime",       r"\b(gm)?strftime\s*\("),             # desaconsejado en 8.1
    ("llamada a constructor viejo", r"\$this->WP_Widget\s*\("),
    ("empty() con expresión",     r"\bempty\s*\(\s*\$[A-Za-z_]+\s*(&&|\|\|)"),
]
rx = [(n, re.compile(p)) for n, p in PATRONES]
filas = []
for base, dirs, files in os.walk(tema):
    dirs.sort()
    for nombre in sorted(files):
        if not nombre.endswith(".php"):
            continue
        p = os.path.join(base, nombre)
        if os.path.islink(p):
            continue
        ruta = os.path.relpath(p, tema)
        data = open(p, "rb").read()
        try: texto = data.decode("utf-8")
        except UnicodeDecodeError: texto = data.decode("latin-1")
        clase = None
        for n, linea in enumerate(texto.splitlines(), 1):
            if re.match(r"\s*(//|#|\*|/\*)", linea):
                continue
            m = re.match(r"\s*(?:abstract\s+|final\s+)?class\s+([A-Za-z_][A-Za-z0-9_]*)", linea)
            if m: clase = m.group(1)
            if clase:
                m = re.match(r"\s*(?:public\s+|private\s+|protected\s+|static\s+)*function\s+&?" + re.escape(clase) + r"\s*\(", linea, re.I)
                if m: filas.append((ruta, n, "constructor PHP 4 (método = clase)", " ".join(linea.split())[:120]))
            for nombre_, r in rx:
                if r.search(linea):
                    filas.append((ruta, n, nombre_, " ".join(linea.split())[:120]))
with open(salida, "w", encoding="utf-8") as f:
    f.write("archivo\tlinea\tpatron\ttexto\n")
    for fila in filas:
        f.write("\t".join(str(x) for x in fila) + "\n")
print(f"compat-patrones.tsv: {len(filas)} coincidencias")
PY

total=$(($(wc -l < "$RAW/lint.tsv") - 1))
con_error=$(awk -F'\t' 'NR>1 && ($2!="OK" || $3!="OK" || $4!="OK")' "$RAW/lint.tsv" | wc -l | tr -d ' ')
echo "lint.tsv: $total archivos PHP, $con_error con error de sintaxis en alguna versión"
