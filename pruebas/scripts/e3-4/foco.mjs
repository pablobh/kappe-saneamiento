import { chromium } from '/Users/pablobh/Projects/kappe-saneamiento/pruebas/node_modules/playwright/index.mjs';
const urls = ['https://4topixel.local/', 'https://4topixel.local/blog/'];
const browser = await chromium.launch();
for (const url of urls) {
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(url, { waitUntil: 'load' });
  await page.waitForTimeout(800);
  const rows = [];
  const seen = new Set();
  for (let i = 0; i < 130; i++) {
    await page.keyboard.press('Tab');
    const r = await page.evaluate(() => {
      const el = document.activeElement;
      if (!el || el === document.body || el === document.documentElement) return null;
      const cs = getComputedStyle(el);
      const label = (el.getAttribute('aria-label') || el.innerText || el.alt || el.title || el.value || '').trim().replace(/\s+/g, ' ').slice(0, 40);
      const outline = cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0;
      const shadow = cs.boxShadow !== 'none';
      const bg = cs.backgroundColor;
      return { key: el.tagName + '#' + el.id + '|' + label + '|' + (el.getAttribute('href')||''), label, tag: el.tagName, outline, shadow, ol: outline ? cs.outlineColor + ' ' + cs.outlineWidth : '', sh: shadow ? cs.boxShadow.slice(0,70) : '', bg, ph: el.getAttribute('type') };
    });
    if (!r) continue;
    if (seen.has(r.key)) break;
    seen.add(r.key);
    rows.push(r);
  }
  const sinIndicador = rows.filter(r => !r.outline && !r.shadow);
  console.log(`\n=== ${url}: ${rows.length} elementos distintos en el ciclo; sin contorno ni sombra al enfocar: ${sinIndicador.length}`);
  sinIndicador.forEach(r => console.log('  SIN INDICADOR:', r.tag, r.label, 'bg=' + r.bg));
  const conContorno = rows.filter(r => r.outline).map(r => r.ol);
  console.log('  contornos distintos:', [...new Set(conContorno)].join(' ; ') || '(ninguno)');
  const sombras = [...new Set(rows.filter(r=>r.shadow).map(r=>r.sh))];
  console.log('  sombras distintas:', sombras.length, sombras.slice(0,3).join(' | '));
  await ctx.close();
}
await browser.close();
