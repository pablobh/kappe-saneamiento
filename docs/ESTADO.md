# ESTADO — Saneamiento de Kappe / 4topixel.com (actualizado 24-nov-2026)
 
**EN PAUSA.** Es un proyecto grande que Pablo retomará más adelante, después de cerrar los pendientes cortos de los briefs (ver briefs/estado-y-traspaso-2026-11-20.md).
 
**Plan vigente: `PLAN-3-ETAPAS.md`** (aprobado 24-nov-2026): 1) revisión general y plan, 2) desarrollo por componente, 3) pruebas en local. Trae el modelo recomendado por tarea y cuándo cambiarlo. Manda sobre `TAREAS.md` y `SPECS.md` §10 en etapas, ramas, pruebas, despliegue y modelo. `SPECS.md` sigue siendo el alcance y las decisiones; `TAREAS.md`, el detalle de cada tarea.
 
## Hecho
- Auditoría del tema; producción es **Kappe 1.6** (no 2.1). Base del saneamiento: 2.1 solo si es compatible con datos y configuración de producción (SPECS §2.1, §5).
- CSS original (altura del sidebar, margen superior, color de créditos) resuelto antes.
- **Fase 0 completa** (Pablo borró los archivos expuestos; verificado 404 en Chrome). Excepción intencional: `ajax-load-more.php` (200, lo usa "Cargar más"; se endurece en E2.B1 / T2.2). Caché de Cloudflare purgada.
- **Cloudflare (plan gratis), 21-nov:**
  - Regla de rate limiting "Limitar intentos de login WordPress": POST a `/wp-login.php`, 5 req/10 s por IP, Block 10 s, activa.
  - Borradas las 5 reglas de acceso por IP "Allow" que Pablo no reconocía. Lista vacía.
  - **Observación terminada (24-nov):** nada ha fallado desde que se quitaron; ya no hace falta recrearlas.
  - La regla de caché la gestiona el plugin WP Super Page Cache: no editarla a mano.
  - Bot Fight Mode apagado (decisión aplazada). Panel "Zone Lockdown" da error de carga de la interfaz: ignorar.
- 24-nov: Pablo aprobó el plan en 3 etapas con modelo por tarea (`PLAN-3-ETAPAS.md`).
## Decisiones del 24-nov
- **El despliegue final lo hace Pablo.** Claude solo desarrolla y prueba en local y entrega el paquete con instrucciones (E3.5).
- **Cloudflare y SEO se revisan después del despliegue final**, no antes (xmlrpc, DNS que exponen la IP, Bot Fight Mode, Wordfence vs Cloudflare, S.1–S.4).
## Pendiente (cuando se retome)
1. **Etapa 1** (E1.1–E1.10): LocalWP, Git, diferencias sin revisar (`script.js`, `prettyPhoto.js`, `addons/paginajax`, `es_ES.po`), causa de la caída de la 2.1, `INVENTARIO.md` con lectura única, compatibilidad 1.6→2.1, línea base Lighthouse, staging y plan final aprobado por Pablo.
2. **Etapa 2:** componentes A (código muerto, incluye quitar el `include` de `pricing-tables.php` en `functions.php:33` y el submenú en `admin-functions.php:180`), B (seguridad), C (compatibilidad), D (rendimiento), E (SEO HTML).
3. **Etapa 3:** pruebas en local y paquete de despliegue.
4. **Después del despliegue:** Cloudflare y SEO (ver `PLAN-3-ETAPAS.md`).
5. Más adelante: MCP de WordPress (aplazado); tema de bloques (feb-2027, SPECS §8).
## Reglas de trabajo
- Preguntar todo lo necesario antes de actuar; cambios pequeños sin pedir permiso, el resto se aprueba primero.
- Nunca tocar producción sin respaldo. Conservar los colores de créditos de Pablo (#F5F8E3 texto, #34B7AE enlaces) y no cambiar tamaños de fuente.
- Navegador: Claude in Chrome ya conectado; avisar a Claude cuando Pablo esté logueado en el sitio o en Cloudflare.
- Tareas de herramientas en el navegador: hacerlas en lotes (`browser_batch`) para gastar menos.
- No leer el tema completo: usar `INVENTARIO.md` (cuando exista) y abrir solo los archivos de la tarea.
 
