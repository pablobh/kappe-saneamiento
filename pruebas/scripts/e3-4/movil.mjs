import { chromium } from '/Users/pablobh/Projects/kappe-saneamiento/pruebas/node_modules/playwright/index.mjs';
const browser = await chromium.launch();
const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 412, height: 823 }, deviceScaleFactor: 2.625, isMobile: true });
const page = await ctx.newPage();
await page.goto('https://4topixel.local/', { waitUntil: 'load' });
await page.waitForTimeout(1500);
const r = await page.evaluate(() => [...document.images].filter(i => i.naturalWidth && i.getBoundingClientRect().width > 0).map(i => {
  const b = i.getBoundingClientRect(); const dw = b.width * devicePixelRatio;
  return [i.currentSrc.split('/').pop().slice(0, 45), `${i.naturalWidth}x${i.naturalHeight}`, `${Math.round(b.width)}css`, `${Math.round(dw)}dev`, (i.naturalWidth / dw).toFixed(2)];
}));
r.forEach(x => console.log(x.join('  ')));
await browser.close();
