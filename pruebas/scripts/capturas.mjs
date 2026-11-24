// Uso: node scripts/capturas.mjs <url-base> <carpeta-salida>
import { chromium } from 'playwright';
import fs from 'node:fs';

const [base, out] = process.argv.slice(2);
if (!base || !out) { console.error('Uso: node scripts/capturas.mjs <url-base> <carpeta-salida>'); process.exit(1); }

const rutas = ['/', '/blog/', '/servicios/', '/nuestro-equipo/', '/trabaja-con-nosotros/', '/contactanos/'];
const tamanos = {
  escritorio: { viewport: { width: 1440, height: 900 } },
  movil: { viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true },
};

fs.mkdirSync(out, { recursive: true });
const browser = await chromium.launch();
for (const [nombre, opciones] of Object.entries(tamanos)) {
  const ctx = await browser.newContext({ ...opciones, ignoreHTTPSErrors: true });
  const page = await ctx.newPage();
  for (const ruta of rutas) {
    await page.goto(base + ruta, { waitUntil: 'networkidle' });
    // Recorre la página para que carguen las imágenes con lazy-load
    await page.evaluate(async () => {
      const alto = () => Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
      for (let y = 0; y < alto() && y < 30000; y += 500) {
        window.scrollTo(0, y);
        await new Promise((r) => setTimeout(r, 120));
      }
      window.scrollTo(0, 0);
    });
    await page.waitForTimeout(1500);
    const slug = ruta === '/' ? 'home' : ruta.replaceAll('/', '');
    await page.screenshot({ path: `${out}/${slug}-${nombre}.png`, fullPage: true });
    console.log('OK', slug, nombre);
  }
  await ctx.close();
}
await browser.close();