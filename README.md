# kappe-saneamiento

Saneamiento corto del tema Kappe 1.6 de 4topixel.com: seguridad, compatibilidad con PHP 8.4/8.5,
rendimiento y SEO del lado del tema. El tema de bloques nuevo (feb-2027) lo reemplazará.

- Plan, tareas y modelo por tarea: `docs/PLAN-3-ETAPAS.md`
- Alcance y datos que no se renombran: `docs/SPECS.md`
- Dónde vamos: `docs/ESTADO.md`
- Mapa del tema: `docs/INVENTARIO.md` (se genera en la etapa 1)
- Todo se desarrolla y se prueba en local. El despliegue lo hace Pablo.

## Retomar el trabajo
cd ~/Projects/kappe-saneamiento
git switch desarrollo && git pull
claude
Primer mensaje: "Lee CLAUDE.md, docs/ESTADO.md y docs/PLAN-3-ETAPAS.md. Dime en qué tarea vamos,
qué modelo le toca y qué rama usar. No hagas cambios hasta que yo lo confirme."

## Ramas
`main` = lo aprobado en cada puerta · `desarrollo` = integración · `componente/<letra>-<nombre>` = una por componente.

Repositorio privado: contiene código comercial con licencia (ver LICENSE.md).
