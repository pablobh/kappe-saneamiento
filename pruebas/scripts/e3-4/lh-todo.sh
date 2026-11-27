#!/bin/bash
CH="$HOME/Library/Caches/ms-playwright/chromium-1255/chrome-mac-arm64/Google Chrome for Testing.app/Contents/MacOS/Google Chrome for Testing"
OUT=/Users/pablobh/Projects/kappe-saneamiento/pruebas/lighthouse/etapa-3
cd /private/tmp
ruta() { case "$1" in home) echo "/";; blog) echo "/blog/";; servicios) echo "/servicios/";; nuestro-equipo) echo "/nuestro-equipo/";; contactanos) echo "/contactanos/";; trabaja-con-nosotros) echo "/trabaja-con-nosotros/";; esac; }
for ff in movil escritorio; do
  for slug in home blog servicios nuestro-equipo contactanos trabaja-con-nosotros; do
    for n in 1 2 3; do
      name="$slug-$ff-$n"
      preset=""; [ "$ff" = escritorio ] && preset="--preset=desktop"
      npx -y -p lighthouse@13.6.0 lighthouse "https://4topixel.local$(ruta $slug)" $preset \
        --chrome-path="$CH" --chrome-flags="--headless=new --ignore-certificate-errors" \
        --only-categories=performance,accessibility,best-practices,seo \
        --output=json,html --output-path="$OUT/$name" --quiet > "$OUT/$name.log" 2>&1
      echo "$name exit $?"
    done
  done
done
echo TERMINADO
