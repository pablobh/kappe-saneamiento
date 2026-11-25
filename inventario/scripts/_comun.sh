#!/usr/bin/env bash
# Variables y utilidades comunes de los scripts de extracción (E1.5a).
# Solo LECTURA: no modifican el tema ni la base de datos. Solo escriben en inventario/raw/.
set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
TEMA="$RAIZ/tema/kappe"
RAW="$RAIZ/inventario/raw"
SCRIPTS="$RAIZ/inventario/scripts"

# Sitio LocalWP "4topixel" (ver CLAUDE.md). Nunca producción.
LOCAL_APP="$HOME/Library/Application Support/Local"
SITIO="$HOME/Local Sites/4topixel/app/public"
SOCKET="$LOCAL_APP/run/9bhtu1l7E/mysql/mysqld.sock"
WPCLI_PHAR="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar"

# php_de <prefijo de versión>  →  ruta al binario de PHP de Local (p. ej. php_de 8.4)
php_de() {
  local p
  p="$(ls "$LOCAL_APP"/lightning-services/php-"$1"*/bin/*/bin/php 2>/dev/null | head -1)"
  [ -n "$p" ] || { echo "No encuentro PHP $1 en Local" >&2; return 1; }
  printf '%s' "$p"
}

# wp_local <args…>  →  WP-CLI contra el sitio de Local, con el PHP configurado en el sitio (8.5).
wp_local() {
  local php; php="$(php_de 8.5)"
  ( cd "$SITIO" && "$php" \
      -d "mysqli.default_socket=$SOCKET" -d "pdo_mysql.default_socket=$SOCKET" \
      -d memory_limit=512M -d "error_reporting=E_ALL & ~E_DEPRECATED" \
      "$WPCLI_PHAR" "$@" )
}

# Python aislado (-I): no importa módulos de la carpeta de trabajo.
py() { python3 -I "$@"; }

mkdir -p "$RAW"
