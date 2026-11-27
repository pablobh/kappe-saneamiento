import { chromium } from '/Users/pablobh/Projects/kappe-saneamiento/pruebas/node_modules/playwright/index.mjs';
const urls = ['https://4topixel.local/', 'https://4topixel.local/blog/'];
const browser = await chromium.launch();
for (const url of urls) {
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(url, { waitUntil: 'load' });
  await page.waitForTimeout(800);
  const seq = [];
  let firstFocusInfo = null;
  for (let i = 0; i < 160; i++) {
    await page.keyboard.press('Tab');
    const info = await page.evaluate(() => {
      const el = document.activeElement;
      if (!el || el === document.body) return { none: true };
      const cs = getComputedStyle(el);
      const r = el.getBoundingClientRect();
      const label = (el.getAttribute('aria-label') || el.innerText || el.value || el.alt || el.title || el.getAttribute('name') || '').trim().replace(/\s+/g, ' ').slice(0, 50);
      const visibleOutline = (cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0) || (cs.boxShadow && cs.boxShadow !== 'none');
      const inView = r.width > 0 && r.height > 0 && r.bottom > 0 && r.top < innerHeight;
      return { tag: el.tagName.toLowerCase(), id: el.id, cls: (el.className && el.className.baseVal === undefined ? el.className : '').toString().slice(0, 40), label, outline: visibleOutline ? `${cs.outlineStyle} ${cs.outlineWidth} ${cs.outlineColor}` + (cs.boxShadow !== 'none' ? ` shadow:${cs.boxShadow.slice(0,60)}` : '') : 'NONE', href: el.getAttribute('href') || '', inView, w: r.width, h: r.height };
    });
    if (info.none) { seq.push('(sin foco)'); continue; }
    const key = `${info.tag}#${info.id}|${info.label}|${info.href}`;
    seq.push(key);
    if (!firstFocusInfo) firstFocusInfo = info;
    if (i === 0) firstFocusInfo = info;
    await page.evaluate(() => {});
    globalThis.__last = info;
    if (i >= 3 && seq.slice(-3).every(k => k === key) ) break;
  }
  // Detect cycle: first repeat of the very first element means focus returned to start
  const first = seq[0];
  const loopAt = seq.findIndex((k, idx) => idx > 0 && k === first);
  const uniq = new Set(seq);
  const noOutline = [];
  // re-run quick outline audit: count tabbable elements without visible focus
  console.log(`\n=== ${url} ===`);
  console.log(`pulsaciones: ${seq.length}; elementos distintos: ${uniq.size}; vuelve al inicio en: ${loopAt >= 0 ? loopAt + 1 : 'no'}`);
  seq.forEach((k, i) => console.log(String(i + 1).padStart(3), k));
  await ctx.close();
}
await browser.close();
