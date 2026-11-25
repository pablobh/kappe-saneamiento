<?php
/**
 * E1.5a · Consultas de solo lectura a la copia LOCAL (WP-CLI: wp eval-file).
 * Uso: wp eval-file 40-bd.php <carpeta-de-salida>
 *
 * Salidas (en inventario/raw/):
 *   bd-resumen.json      versiones, tema activo, idioma, plugins activos, tipos de entrada y taxonomías
 *   bd-plantillas.tsv    plantilla de página usada → nº de páginas
 *   bd-shortcodes.tsv    etiqueta → entradas que la usan (por tipo) y ejemplos de ID; marca si es del tema
 *   bd-widgets.tsv       barra lateral → widgets; y widgets por tipo, con marca "del tema"
 *   bd-metas.tsv         meta_key privadas → tipo de entrada → nº
 *   bd-opciones.tsv      claves de weblusive_options (tipo, longitud, valor corto o [oculto])
 *   bd-otras-opciones.tsv  opciones del tema/Personalizador (theme_mods_*, nav_menu_locations…)
 *
 * No escribe en la base de datos. No vuelca contenido de entradas. Los valores que parecen secretos
 * (claves, tokens, correos, contraseñas, URLs de usuario) salen como [oculto].
 */

if (!defined('WP_CLI') || !WP_CLI) { exit("Ejecutar con: wp eval-file\n"); }

global $wpdb, $shortcode_tags;
$args = $GLOBALS['argv'] ?? [];
$salida = rtrim(getenv('INVENTARIO_RAW') ?: '', '/');
if ($salida === '' || !is_dir($salida)) { WP_CLI::error('Define INVENTARIO_RAW con la carpeta de salida.'); }

function tsv($ruta, $cabecera, $filas) {
    $f = fopen($ruta, 'w');
    fwrite($f, implode("\t", $cabecera) . "\n");
    foreach ($filas as $fila) {
        fwrite($f, implode("\t", array_map(function ($v) {
            return str_replace(["\t", "\r", "\n"], ' ', (string) $v);
        }, $fila)) . "\n");
    }
    fclose($f);
}

function oculto_si_secreto($clave, $valor) {
    if (is_array($valor) || is_object($valor)) { return null; }
    $v = (string) $valor;
    if (preg_match('/(key|secret|token|pass|oauth|smtp|twitter|api|mail|email|user|login|phone|tel|address|direcci)/i', $clave)) {
        return $v === '' ? '' : '[oculto]';
    }
    if (preg_match('/@|^https?:\/\/[^\/]*:[^\/]*@/', $v)) { return '[oculto]'; }
    return mb_strlen($v) > 60 ? '[' . mb_strlen($v) . ' caracteres]' : $v;
}

/* ---------- resumen ---------- */
$tema = wp_get_theme();
$plugins_activos = (array) get_option('active_plugins', []);
$tipos = [];
foreach (get_post_types([], 'objects') as $t) {
    $conteo = (array) wp_count_posts($t->name);
    $tipos[$t->name] = ['propio_del_tema' => !$t->_builtin, 'publicados' => (int) ($conteo['publish'] ?? 0),
                        'borradores_y_otros' => array_sum(array_map('intval', $conteo)) - (int) ($conteo['publish'] ?? 0)];
}
$taxos = [];
foreach (get_taxonomies([], 'objects') as $t) {
    $taxos[$t->name] = ['propia' => !$t->_builtin, 'terminos' => (int) wp_count_terms(['taxonomy' => $t->name, 'hide_empty' => false]),
                        'tipos' => $t->object_type];
}
file_put_contents($salida . '/bd-resumen.json', json_encode([
    'wordpress' => get_bloginfo('version'),
    'php_wp_cli' => PHP_VERSION,
    'tema_activo' => ['nombre' => $tema->get('Name'), 'version' => $tema->get('Version'),
                      'carpeta' => get_stylesheet(), 'plantilla' => get_template(), 'es_hijo' => get_stylesheet() !== get_template()],
    'idioma_sitio' => get_locale(),
    'opcion_WPLANG' => get_option('WPLANG'),
    'prefijo_tablas' => $wpdb->prefix,
    'plugins_activos' => array_map(function ($p) { return dirname($p) === '.' ? $p : dirname($p); }, $plugins_activos),
    'tipos_de_entrada' => $tipos,
    'taxonomias' => $taxos,
    'menus' => array_map(function ($m) { return ['nombre' => $m->name, 'elementos' => (int) $m->count]; }, wp_get_nav_menus()),
    'ubicaciones_de_menu' => get_nav_menu_locations(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

/* ---------- plantillas de página ---------- */
$filas = [];
$rs = $wpdb->get_results("SELECT p.post_type, pm.meta_value plantilla, COUNT(*) n
    FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE pm.meta_key = '_wp_page_template' AND p.post_status NOT IN ('auto-draft','trash')
    GROUP BY p.post_type, pm.meta_value ORDER BY n DESC");
foreach ($rs as $r) { $filas[] = [$r->post_type, $r->plantilla, $r->n]; }
tsv($salida . '/bd-plantillas.tsv', ['tipo', 'plantilla', 'n'], $filas);

/* ---------- shortcodes en el contenido ---------- */
// Etiquetas del tema, tomadas de lo que extrajo 20-codigo.py (add_shortcode).
$del_tema = [];
$ruta_sc = $salida . '/codigo-shortcodes.tsv';
if (is_readable($ruta_sc)) {
    foreach (array_slice(file($ruta_sc, FILE_IGNORE_NEW_LINES), 1) as $l) {
        $c = explode("\t", $l);
        if (!empty($c[2])) { $del_tema[$c[2]] = $c[0]; }
    }
}
$etiquetas = array_unique(array_merge(array_keys($shortcode_tags), array_keys($del_tema)));
$uso = [];  // etiqueta => [tipo => n], ids
$maxid = (int) $wpdb->get_var("SELECT MAX(ID) FROM {$wpdb->posts}");
$regex = get_shortcode_regex($etiquetas);
for ($desde = 0; $desde <= $maxid; $desde += 500) {
    $lote = $wpdb->get_results($wpdb->prepare(
        "SELECT ID, post_type, post_content FROM {$wpdb->posts}
         WHERE ID > %d AND ID <= %d AND post_status NOT IN ('auto-draft','inherit','trash')
           AND post_type NOT IN ('revision','nav_menu_item','customize_changeset')
           AND post_content LIKE %s", $desde, $desde + 500, '%[%'));
    foreach ($lote as $p) {
        if (!preg_match_all('/' . $regex . '/', $p->post_content, $m, PREG_SET_ORDER)) { continue; }
        foreach ($m as $x) {
            $t = $x[2];
            $uso[$t]['tipos'][$p->post_type] = ($uso[$t]['tipos'][$p->post_type] ?? 0) + 1;
            $uso[$t]['ids'][$p->ID] = true;
            $uso[$t]['usos'] = ($uso[$t]['usos'] ?? 0) + 1;
        }
    }
}
$filas = [];
foreach ($etiquetas as $t) {
    $u = $uso[$t] ?? null;
    $tipos_txt = $u ? implode(', ', array_map(function ($k, $v) { return "$k:$v"; }, array_keys($u['tipos']), $u['tipos'])) : '';
    $filas[] = [$t, isset($del_tema[$t]) ? 'tema (' . $del_tema[$t] . ')' : 'otro (plugin o núcleo)',
                $u ? $u['usos'] : 0, $u ? count($u['ids']) : 0, $tipos_txt,
                $u ? implode(',', array_slice(array_keys($u['ids']), 0, 8)) : ''];
}
usort($filas, function ($a, $b) { return [$b[2], $a[0]] <=> [$a[2], $b[0]]; });
tsv($salida . '/bd-shortcodes.tsv', ['etiqueta', 'origen', 'usos', 'entradas', 'por_tipo', 'ids_ejemplo'], $filas);

/* ---------- widgets activos ---------- */
$bases_tema = ['alc-social-widget', 'alc-works-widget', 'alc-flickr-widget', 'latest-tweets-widget',
               'alc-blogposts-widget', 'alc_contact_form', 'alc-postinfo-widget'];
$filas = [];
$sidebars = (array) get_option('sidebars_widgets', []);
$por_base = [];
foreach ($sidebars as $sb => $ws) {
    if (!is_array($ws)) { continue; }
    foreach ($ws as $w) {
        $base = preg_replace('/-\d+$/', '', $w);
        $filas[] = [$sb, $w, $base, in_array(strtolower($base), array_map('strtolower', $bases_tema), true) ? 'del tema' : '',
                    $sb === 'wp_inactive_widgets' ? 'inactivo' : 'activo'];
        if ($sb !== 'wp_inactive_widgets') { $por_base[$base] = ($por_base[$base] ?? 0) + 1; }
    }
}
if (!$por_base) { $filas[] = ['(ninguna)', '(ningún widget activo en ninguna barra lateral)', '', '', 0]; }
foreach ($por_base as $b => $n) { $filas[] = ['(resumen activos)', $b, $b, in_array(strtolower($b), array_map('strtolower', $bases_tema), true) ? 'del tema' : '', $n]; }
tsv($salida . '/bd-widgets.tsv', ['barra_lateral', 'widget', 'tipo', 'origen', 'estado_o_n'], $filas);

/* ---------- metas privadas por tipo ---------- */
$ignora = "'_edit_lock','_edit_last','_thumbnail_id','_wp_old_slug','_wp_trash_meta_status','_wp_trash_meta_time','_pingme','_encloseme','_wp_desired_post_slug'";
$rs = $wpdb->get_results("SELECT pm.meta_key, p.post_type, COUNT(*) n
    FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE p.post_status NOT IN ('auto-draft','trash') AND p.post_type NOT IN ('revision','nav_menu_item')
      AND pm.meta_key NOT IN ($ignora) AND pm.meta_key NOT LIKE '\\_oembed%'
    GROUP BY pm.meta_key, p.post_type ORDER BY pm.meta_key, p.post_type");
$filas = [];
foreach ($rs as $r) { $filas[] = [$r->meta_key, $r->post_type, $r->n]; }
tsv($salida . '/bd-metas.tsv', ['meta_key', 'tipo', 'n'], $filas);

/* ---------- opciones del tema (weblusive_options) ---------- */
$filas = [];
$opts = get_option('weblusive_options', []);
if (is_array($opts)) {
    ksort($opts);
    foreach ($opts as $k => $v) {
        $tipo = gettype($v);
        $len = is_scalar($v) ? mb_strlen((string) $v) : (is_array($v) ? count($v) : 0);
        $valor = oculto_si_secreto($k, $v);
        if ($valor === null) { $valor = is_array($v) ? '[array de ' . count($v) . ']' : '[objeto]'; }
        $filas[] = [$k, $tipo, $len, $valor];
    }
}
tsv($salida . '/bd-opciones.tsv', ['clave', 'tipo', 'longitud_o_n', 'valor'], $filas);

/* ---------- otras opciones del tema y del Personalizador ---------- */
$filas = [];
$rs = $wpdb->get_results("SELECT option_name, LENGTH(option_value) bytes, autoload FROM {$wpdb->options}
    WHERE option_name LIKE 'theme\\_mods\\_%' OR option_name LIKE 'weblusive%' OR option_name LIKE 'kappe%'
       OR option_name LIKE 'widget\\_%' OR option_name IN ('sidebars_widgets','nav_menu_options','stylesheet','template','current_theme','show_on_front','page_on_front','page_for_posts','permalink_structure','custom_css_post_id')
    ORDER BY option_name");
foreach ($rs as $r) {
    $v = get_option($r->option_name);
    $claves = '';
    if (is_array($v)) {
        $partes = [];
        foreach ($v as $k => $x) {
            $partes[] = $k . '=' . (is_array($x) ? '[array]' : (oculto_si_secreto((string) $k, $x) ?? ''));
        }
        $claves = implode('; ', array_slice($partes, 0, 40)) . (count($partes) > 40 ? '; …(+' . (count($partes) - 40) . ')' : '');
    } else {
        $claves = oculto_si_secreto($r->option_name, $v) ?? '';
    }
    $filas[] = [$r->option_name, $r->bytes, $r->autoload, mb_substr($claves, 0, 600)];
}
tsv($salida . '/bd-otras-opciones.tsv', ['opcion', 'bytes', 'autoload', 'contenido_o_claves'], $filas);

WP_CLI::success('Extracción de BD terminada en ' . $salida);
