#!/usr/bin/env bash
# E1.5a · Ejecuta todos los scripts de extracción, en orden. Escribe solo en inventario/raw/.
# Uso: inventario/scripts/ejecutar-todo.sh
# Requisito para el paso 4: LocalWP con el sitio 4topixel encendido (MySQL en marcha).
source "$(dirname "${BASH_SOURCE[0]}")/_comun.sh"

echo "== 1/4 Archivos y carpetas"
py "$SCRIPTS/10-archivos.py" "$RAIZ"

echo "== 2/4 Hooks, shortcodes, enqueues, opciones, metas, SQL, AJAX, definiciones"
py "$SCRIPTS/20-codigo.py" "$RAIZ"

echo "== 3/4 php -l (8.2, 8.4, 8.5) y patrones de compatibilidad"
bash "$SCRIPTS/30-lint.sh"

echo "== 4/4 Consultas a la BD local (WP-CLI)"
if [ ! -S "$SOCKET" ]; then
  echo "AVISO: MySQL de Local no está en marcha ($SOCKET). Enciende el sitio 4topixel en LocalWP y repite." >&2
  exit 1
fi
INVENTARIO_RAW="$RAW" wp_local eval-file "$SCRIPTS/40-bd.php" 2>&1 | grep -v '^Deprecated:' || true

echo "== Listo. Salidas en inventario/raw/:"
( cd "$RAW" && wc -c -- *.tsv *.json 2>/dev/null | sort -k2 )
