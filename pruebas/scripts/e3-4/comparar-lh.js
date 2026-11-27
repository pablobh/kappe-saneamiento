const fs=require("fs");
const base="linea-base", nue="etapa-3";
const med=a=>{a=[...a].sort((x,y)=>x-y);return a[Math.floor(a.length/2)]};
const load=(dir,f)=>JSON.parse(fs.readFileSync(`${dir}/${f}.report.json`,"utf8"));
const pages=["home","blog","servicios","nuestro-equipo","contactanos","trabaja-con-nosotros"];
for (const ff of ["escritorio","movil"]) for (const slug of pages) {
  const b=[1,2,3].map(n=>load(base,`${slug}-${ff}-${n}`)), e=[1,2,3].map(n=>load(nue,`${slug}-${ff}-${n}`));
  const sc=(arr,c)=>med(arr.map(r=>Math.round((r.categories[c]?.score??0)*100)));
  const m=(arr,a)=>Math.round(med(arr.map(r=>r.audits[a]?.numericValue??0)));
  const cl=(arr)=>med(arr.map(r=>r.audits["cumulative-layout-shift"].numericValue)).toFixed(3);
  console.log(`${ff} ${slug}: perf ${sc(b,"performance")}→${sc(e,"performance")} | a11y ${sc(b,"accessibility")}→${sc(e,"accessibility")} | bp ${sc(b,"best-practices")}→${sc(e,"best-practices")} | seo ${sc(b,"seo")}→${sc(e,"seo")} | LCP ${m(b,"largest-contentful-paint")}→${m(e,"largest-contentful-paint")} ms | TBT ${m(b,"total-blocking-time")}→${m(e,"total-blocking-time")} | CLS ${cl(b)}→${cl(e)} | isz ${b.map(r=>r.audits["image-size-responsive"].score).join("")}→${e.map(r=>r.audits["image-size-responsive"].score).join("")}`);
}
// Fallos que aparecen o desaparecen
const fails=(arr)=>{const s=new Set(); for(const r of arr) for(const [k,a] of Object.entries(r.audits)) if(a.score!==null && a.score<1 && a.scoreDisplayMode!=="informative" && a.scoreDisplayMode!=="notApplicable") s.add(k); return s;};
const allB=new Set(), allE=new Set();
for (const ff of ["escritorio","movil"]) for (const slug of pages) { [1,2,3].forEach(n=>{ fails([load(base,`${slug}-${ff}-${n}`)]).forEach(k=>allB.add(ff+":"+k)); fails([load(nue,`${slug}-${ff}-${n}`)]).forEach(k=>allE.add(ff+":"+k)); }); }
console.log("\nSolo en la línea base (ya no fallan):", [...allB].filter(x=>!allE.has(x)).join(", ") || "ninguno");
console.log("Solo en la etapa 3 (nuevos fallos):", [...allE].filter(x=>!allB.has(x)).join(", ") || "ninguno");
