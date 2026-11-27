import { chromium } from '/Users/pablobh/Projects/kappe-saneamiento/pruebas/node_modules/playwright/index.mjs';
const urls = ['https://4topixel.local/', 'https://4topixel.local/blog/', 'https://4topixel.local/servicios/', 'https://4topixel.local/nuestro-equipo/', 'https://4topixel.local/contactanos/', 'https://4topixel.local/trabaja-con-nosotros/'];
const browser = await chromium.launch();
for (const vp of [{width:1440,height:900,dpr:1},{width:390,height:844,dpr:3}]) {
  for (const url of urls) {
    const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: {width:vp.width,height:vp.height}, deviceScaleFactor: vp.dpr });
    const page = await ctx.newPage();
    await page.goto(url, { waitUntil: 'load' });
    await page.waitForTimeout(1500);
    const imgs = await page.evaluate(() => [...document.images].filter(i => i.currentSrc && i.naturalWidth > 0).map(i => {
      const r = i.getBoundingClientRect();
      const cw = r.width * devicePixelRatio, ch = r.height * devicePixelRatio;
      const ratio = i.naturalWidth / Math.max(cw, 1);
      return { src: i.currentSrc.split('/').pop().slice(0,60), nat: i.naturalWidth + 'x' + i.naturalHeight, shown: Math.round(cw) + 'x' + Math.round(ch), ratio: +ratio.toFixed(2), srcset: !!i.srcset, sizes: !!i.sizes, w: r.width };
    }).filter(x => x.w > 0));
    const grandes = imgs.filter(x => x.ratio > 1.5);
    console.log(`\n${vp.width}px ${url}: ${imgs.length} imágenes visibles; con natural > 1,5× lo mostrado: ${grandes.length}`);
    grandes.slice(0, 6).forEach(x => console.log('   ', x.src, 'nat', x.nat, 'mostrada', x.shown, 'x' + x.ratio, 'srcset:' + x.srcset));
    await ctx.close();
  }
}
await browser.close();
