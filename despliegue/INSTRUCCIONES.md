# Despliegue del tema Kappe 3.0 en producción (4topixel.com)

Lo hace Pablo. Claude no toca producción. Sigue los pasos en orden y no saltes ninguno.
Los valores entre `<…>` son datos del servidor que solo tú tienes (usuario SSH, host, ruta del sitio).

**Qué se sube:** solo la carpeta `tema/kappe/` de la rama que apruebes (versión saneada, `style.css` con `Version: 3.0`).
**Qué no se sube:** nada más del repo (`docs/`, `pruebas/`, `inventario/`, `respaldos/`).
**Volver atrás:** el tema 1.6 original queda en el tag `prod-1.6` y en el respaldo del paso 1.

---

## 0. Antes de empezar
- [ ] La rama a desplegar está fusionada y aprobada (Puerta 3) y `git status` está limpio.
- [ ] Elige una hora de poco tráfico.
- [ ] Define las variables en tu terminal (ajusta los valores):
  ```bash
  export SRV="<usuario>@<host>"
  export SITIO="<ruta-del-sitio-en-el-servidor>"   # carpeta que contiene wp-content/
  export TEMA="$SITIO/wp-content/themes/kappe"
  cd ~/Projects/kappe-saneamiento
  ```

## 1. Respaldo (obligatorio)
1. **Base de datos** (WP-CLI en el servidor, o exportación desde el panel de DreamHost):
   ```bash
   ssh $SRV "cd $SITIO && wp db export ~/respaldo-4topixel-$(date +%F).sql"
   scp $SRV:~/respaldo-4topixel-$(date +%F).sql respaldos/
   ```
2. **Carpeta del tema actual:**
   ```bash
   ssh $SRV "cd $SITIO/wp-content/themes && zip -qr ~/kappe-prod-$(date +%F).zip kappe"
   scp $SRV:~/kappe-prod-$(date +%F).zip respaldos/
   ```
3. **Comprueba** que ambos archivos abren (el `.zip` se descomprime, el `.sql` empieza con `-- MySQL dump` y pesa más de 20 MB).
4. **Anota el CSS que se retirará más adelante** (paso 6): copia el texto de la opción `css` del panel y el CSS adicional del Personalizador a un archivo en `respaldos/`.
5. **Exporta la configuración del panel** (JSON): panel Kappe → pestaña de exportar. Guárdala junto al respaldo.

## 2. Simulación de la subida (no cambia nada)
```bash
rsync -rltvn -i --delete --exclude='.DS_Store' tema/kappe/ $SRV:$TEMA/
```
- `-n` = simulación, `-i` = lista cada cambio. **Revisa la lista línea a línea.**
- Líneas `*deleting …` = archivos que el tema saneado elimina del servidor (la lista real contra el 1.6 sale de `git diff --name-status --diff-filter=D prod-1.6 HEAD -- tema/kappe`). Se esperan: `ajax-load-more.php`, `contact-form.php`, `contact-template.php`, `portfolio_template2.php`, `under-construction.php`, `woocommerce.php`, `notifier.xml`, `addons/flickr/`, `css/{font-awesome,ie,magnific-popup}.css`, `images/preloader*.gif`, `js/gmap3.min.js`, `js/jplayer/` (27 archivos), `js/jquery.{bxslider,countdown,migrate,min,nicescroll*,validate.min}.js`, `js/modernizr.custom.97074.js`, `js/retina-1.1.0.min.js`, `js/script.zip`, `languages/es_ES.mo` (pasa a `es_CO`), y 9 archivos de `library/admin-panel/`, 2 de `library/functions/` y 2 de `library/includes/` (OAuth, twitteroauth).
- **Desconfía** si aparece borrado: `style.css`, `functions.php`, `languages/`, `images/` completo o `screenshot.png`; o si la lista de `*deleting` es enorme. En ese caso detente y avísame.
- Entrégame la lista si quieres que la revise antes del paso 3.

## 3. Subida real
Solo si la simulación estaba bien. Mismo comando sin `-n`:
```bash
rsync -rlt -i --delete --exclude='.DS_Store' tema/kappe/ $SRV:$TEMA/
```
Comprueba: `ssh $SRV "grep '^\s*Version' $TEMA/style.css"` → `Version: 3.0`, y que `ajax-load-more.php` ya no existe en el servidor.

## 4. Activación
El tema ya está activo (carpeta `kappe`, mismo nombre), así que no hace falta activar nada: WordPress usa los archivos nuevos. El menú, el CSS adicional y los ajustes del Personalizador se conservan (`theme_mods_kappe`).

## 5. Panel "Kappe" → "Save Changes" (una vez)
- Panel de WordPress → Kappe → **Save Changes**.
- Borra los 8 `typography_*.color` que mostraban la ruta del servidor en el CSS público, las opciones `contact_*`, `uc_*` y `google_api`, y vacía la caché del CSS dinámico (`kappe_dynamic_css`).

## 6. Retirar de la BD el CSS que ya trae el tema
Primero comprueba que el sitio se ve igual (paso 8 abreviado: portada, servicios, contáctanos, créditos del pie, menú lateral a 1440 px).
1. **Opción `css` del panel:** vacía el campo de CSS personalizado y pulsa **Save Changes** (así también se renueva la caché).
2. **Personalizador → CSS adicional:** borra el contenido (Gravity Forms `.escala-horizontal`, `flow-root`, menú lateral fijo, colores de créditos) y publica. Ya tienes copia del texto (paso 1.4).
3. Vuelve a mirar el sitio. Si algo cambió, pega de nuevo el CSS y avísame.

## 7. Miniaturas y logo (opcional, recomendado)
- **Miniaturas del portafolio** (tamaño nuevo `portfolio-large` con `srcset`; se quitan `portfolio-single` y `blog-thumb-3`). Con el respaldo ya hecho:
  ```bash
  ssh $SRV "cd $SITIO && wp media regenerate --only-missing --yes"   # solo crea lo que falta (portfolio-large)
  ```
  Para regenerar solo los adjuntos del portafolio, pásale sus IDs: `wp media regenerate <id> <id> … --yes` (los IDs salen de `wp post list --post_type=portfolio --field=ID` y la meta `_thumbnail_id` de cada uno).
  Cuidado: `wp media regenerate` sin `--only-missing` borra los tamaños no registrados y recrea todos; tarda y consume CPU del servidor. El plugin reSmush llama a un servicio externo al regenerar (en local fue lento).
  Las imágenes con fuente menor de 720 px de ancho no obtienen el tamaño nuevo (p. ej. el proyecto 2467); es lo esperado.
- **Logo:** hoy es `2014/04/logo-para-web2.jpg` (193×186). Para pantallas de alta densidad sube uno de **≥ 290 px de ancho** en el panel Kappe (opción de logo).

## 8. Purgar cachés
- Cloudflare: Caching → **Purge Everything**.
- WP Super Page Cache: botón de purgar caché en la barra de administración.

## 9. Qué comprobar en producción
- [ ] Portada, `/servicios/`, `/nuestro-equipo/`, `/contactanos/`, `/trabaja-con-nosotros/`, `/politica-de-privacidad/` y un proyecto del portafolio cargan.
- [ ] El formulario de contacto (Gravity Forms) se ve y envía.
- [ ] El blog pagina hasta el final (`/blog/`, `/blog/page/2/`; `/blog/page/3/` = 404) y `https://4topixel.com/wp-content/themes/kappe/ajax-load-more.php` da 404.
- [ ] El CSS público no contiene "Warning" ni la ruta `/home/…`: abre el código fuente de la portada y busca `kappe-dynamic` o `/home/`.
- [ ] Créditos del pie con `#F5F8E3` (texto) y `#34B7AE` (enlaces); menú lateral fijo a ≥ 768 px.
- [ ] Guardar una página y un proyecto (editor clásico) conserva sus opciones del tema.
- [ ] "Sort items" reordena el portafolio.
- [ ] El panel Kappe guarda, exporta e importa (JSON).
- [ ] Consola del navegador sin errores propios del tema.
- [ ] El log de errores PHP del servidor (DreamHost → logs) no tiene líneas nuevas del tema. Vigílalo la primera semana.

## 10. Cómo volver atrás
1. Restaura el tema del respaldo:
   ```bash
   scp respaldos/kappe-prod-<fecha>.zip $SRV:~/
   ssh $SRV "cd $SITIO/wp-content/themes && mv kappe kappe-3.0-fallido && unzip -q ~/kappe-prod-<fecha>.zip"
   ```
2. Si cambiaste la BD (pasos 5 y 6), reimporta la configuración del panel **solo si la exportación es JSON** (las viejas en base64 ya no se importan) o restaura la BD con `wp db import`.
3. Vuelve a poner el CSS que guardaste en el paso 1.4.
4. Purga Cloudflare y WP Super Page Cache.

## 11. Después del despliegue
Cloudflare y SEO se revisan después (ver `docs/PLAN-3-ETAPAS.md`, "Después del despliegue final").
