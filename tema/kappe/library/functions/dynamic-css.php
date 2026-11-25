<?php
/*-----------------------------------------------------------------------------------*/
# Estilos dinámicos del panel (E2.D2)
# Antes: css/dynamic-styles.php, que se pedía por URL y cargaba wp-load.php en cada
# visita. Ahora el mismo CSS se genera aquí, se guarda en un transient y sale en línea
# (wp_add_inline_style) en el mismo punto de la cascada. Los !important del panel se
# conservan a propósito (decisión de Pablo, 25-nov-2026).
/*-----------------------------------------------------------------------------------*/

// Súbelo si cambia la plantilla de abajo: invalida el CSS guardado.
define( 'KAPPE_DYNAMIC_CSS_VER', 1 );

/*-----------------------------------------------------------------------------------*/
# Get Font Name
/*-----------------------------------------------------------------------------------*/
function weblusive_get_font ( $got_font ) {
	if ($got_font) {
		$font_pieces = explode(":", $got_font);
		$font_name = $font_pieces[0];
		return $font_name;
	}
}

// CSS guardado en caché; se regenera si falta o es de otra versión.
function kappe_get_dynamic_css() {
	$cached = get_transient( 'kappe_dynamic_css' );
	if ( is_array( $cached ) && isset( $cached['v'], $cached['css'] ) && KAPPE_DYNAMIC_CSS_VER === $cached['v'] ) {
		return $cached['css'];
	}
	$css = kappe_build_dynamic_css();
	set_transient( 'kappe_dynamic_css', array( 'v' => KAPPE_DYNAMIC_CSS_VER, 'css' => $css ), DAY_IN_SECONDS );
	return $css;
}

// Borra la caché cuando cambian las opciones del panel (guardar, importar) o el tema.
function kappe_flush_dynamic_css() {
	delete_transient( 'kappe_dynamic_css' );
}
add_action( 'add_option_weblusive_options', 'kappe_flush_dynamic_css' );
add_action( 'update_option_weblusive_options', 'kappe_flush_dynamic_css' );
add_action( 'delete_option_weblusive_options', 'kappe_flush_dynamic_css' );
add_action( 'after_switch_theme', 'kappe_flush_dynamic_css' );

// Genera el CSS (misma salida que el antiguo css/dynamic-styles.php).
function kappe_build_dynamic_css() {
	global $custom_typography;
	ob_start();
?>
<?php $maincolor = weblusive_get_option('global_color'); if($maincolor):?>
a.comment-reply-link, header .menu-box,#contact-form input[type="submit"], .comment-form input[type="submit"],.btn-Kappe,ul.tab-links li:hover, ul.tab-links li.active,
.team-hover,.client-test h3, .blog-page-link,#wp-calendar caption, .bx-controls-direction a:hover, .toggle-trigger.open,
.nicescroll-rails div, #contactFormWidget .col-md-12 input[type='submit'], #banner .bx-wrapper, .sticky .post-content,
ul.tab-links li:hover, ul.tab-links li a.active, 
ul.menu li.active a, ul.menu li.current-menu-item a:after, ul.menu li.current-menu-parent > a:after, 
ul.menu > li > a:hover:after, ul.drop-down li > a:hover:after, ul.menu li.active a:after,
.info-box a.info-toggle, .link-post .post-content, ul.menu > li > a:after, ul.drop-down > li > a:after, a.elemadded,
.flex-direction-nav .flex-prev, .flex-direction-nav .flex-next, #uc-content .progress-bar, div.clock p
    {
	background:<?php echo weblusive_get_option('global_color')?> !important;
}
.project-post .hover-box{<?php $bg2 = HexToRGB ($maincolor)?>
background:<?php echo $maincolor;?>; background: rgba(<?php echo $bg2['r'].','.$bg2['g'].','.$bg2['b']?>, 0.8)
}
p.com_logined_text a, .quote-post span, .footer-block h4, .search-widget button i,
    .post-info ul li span i, .float-input span i {
	color:<?php echo weblusive_get_option('global_color')?> !important;
}
.post-info ul li span, .float-input span i {
    border-color:<?php echo weblusive_get_option('global_color')?>!important;
    }
.aside-post .post-content {
	border-left-color:<?php echo weblusive_get_option('global_color')?>!important;
}
.services-post > a:after, .work-post:hover .hover-box:after, #nav ul.sf-menu li.current-menu-item:after, .lastdiv,
.testimonials-box h3 span:after, .staff-box h3 span:after, .infographic-box h3 span:after, h3.title2 span:after{
	border-top-color:<?php echo weblusive_get_option('global_color')?>;
}
ul.drop-down li:hover > a, .subheader .subheader2 li, .module_arrow, .arrow-right, .top-bar-section .dropdown li:hover {
	border-left-color:<?php echo weblusive_get_option('global_color')?>;
}.services-post > a:before{
    border-bottom-color:<?php echo weblusive_get_option('global_color')?>;
}
.subheader, .inner-footer h4, .sf-menu .sub-menu li, .sl-slider h2 span, .recent-projects > h2 span, h2.heading span, .team > h2 span, .contact-page > h2 span, .blog > h2 span, 
h2.top-title span, .single-work > h2 span, .filter-items a.active, .filter-items li a:hover{
	border-bottom-color:<?php echo weblusive_get_option('global_color')?>;
}
<?php endif?>
<?php $bg = weblusive_get_option( 'background' );
if($bg) :
if( weblusive_get_option('background_full') ): ?>
body{<?php echo "\n"; ?>
	<?php  if (isset( $bg['color'])):?> background-color: <?php echo $bg['color'] ; ?>!important;<?php endif ?>
	<?php if (isset( $bg['img'])):?>
		background : url('<?php echo $bg['img'] ?>') no-repeat center center fixed; <?php echo "\n"; ?>
		filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg['img'] ?>',sizingMethod='scale') !important;<?php echo "\n"; ?>
		-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $bg['img'] ?>',sizingMethod='scale')" !important;<?php echo "\n"; ?>
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
	<?php endif?>
}
<?php else: ?>
	body{background:<?php  echo isset($bg['color']) ? $bg['color'] : '';?> url('<?php echo isset($bg['img']) ? $bg['img'] : '' ?>') <?php echo isset($bg['repeat']) ? $bg['repeat'] : ''; ?> <?php echo isset($bg['attachment']) ? $bg['attachment'] : ''; ?> <?php echo  isset($bg['hor']) ? $bg['hor'] : ''  ?> <?php echo  isset($bg['ver']) ? $bg['ver'] :'' ?> !important;}<?php echo "\n"; ?>
<?php endif; ?>
<?php endif; ?>
<?php if( weblusive_get_option( 'links_color' ) || weblusive_get_option( 'links_decoration' )  ): ?>
a , .menu .sub-menu li a{
	<?php if( weblusive_get_option( 'links_color' ) ) echo 'color: '.weblusive_get_option( 'links_color' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'links_decoration' ) ) echo 'text-decoration: '.weblusive_get_option( 'links_decoration' ).' !important;'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'links_color_hover' ) || weblusive_get_option( 'links_decoration_hover' )  ): ?>
a:hover, .menu .sub-menu li a:hover {
	<?php if( weblusive_get_option( 'links_color_hover' ) ) echo 'color: '.weblusive_get_option( 'links_color_hover' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'links_decoration_hover' ) ) echo 'text-decoration: '.weblusive_get_option( 'links_decoration_hover' ).' !important;'; ?>
}
<?php endif; ?>

.subheader{
<?php 
 if( weblusive_get_option('topbar_background')):$bg = weblusive_get_option( 'topbar_background' );?>
 
	background:<?php echo isset($bg['color']) ? $bg['color'] : '';?> url('<?php echo isset($bg['img']) ? $bg['img'] : '' ?>') <?php echo isset($bg['repeat']) ? $bg['repeat'] : ''; ?> <?php echo isset($bg['attachment']) ? $bg['attachment'] : ''; ?> <?php echo  isset($bg['hor']) ? $bg['hor'] : ''  ?> <?php echo  isset($bg['ver']) ? $bg['ver'] :'' ?> !important;
<?php endif; ?>
}
.subheader .phone{
<?php if(weblusive_get_option('topbar_color')):?>
	color:<?php echo weblusive_get_option('topbar_color')?> !important
<?php endif?>
}
<?php if( weblusive_get_option('topbar_links_color')):?>
	.subheader a {color:<?php echo weblusive_get_option('topbar_links_color')?> !Important}
<?php endif?>
<?php if(weblusive_get_option('topbar_links_color_hover')):?>
	.subheader a:hover {color:<?php echo weblusive_get_option('topbar_links_color_hover')?> !Important}
<?php endif
 
?> 
 
<?php if( weblusive_get_option('header_background')):$bg = weblusive_get_option( 'header_background' );?>
	header .logo-box, header .menu-box{background:<?php echo isset($bg['color']) ? $bg['color'] : '';?> url('<?php echo isset($bg['img']) ? $bg['img'] : '' ?>') <?php echo isset($bg['repeat']) ? $bg['repeat'] : ''; ?> <?php echo isset($bg['attachment']) ? $bg['attachment'] : ''; ?> <?php echo  isset($bg['hor']) ? $bg['hor'] : ''  ?> <?php echo  isset($bg['ver']) ? $bg['ver'] :'' ?> !important;}
<?php endif; ?>

<?php if( weblusive_get_option( 'sub_nav_background' )): ?>
ul.dropdown-menu li, .menu .sub-menu li a:hover  {background-color:<?php echo weblusive_get_option( 'sub_nav_background' ).' !important;';?>;}<?php echo "\n"; ?>
<?php endif; ?>
<?php if( weblusive_get_option( 'sub_nav_color' )): ?>
.menu .sub-menu a, .menu .sub-menu li a:hover {color:<?php echo weblusive_get_option( 'sub_nav_color' ).' !important;';?>;}<?php echo "\n"; ?>
<?php endif; ?>
<?php if( weblusive_get_option( 'nav_links_color' ) || weblusive_get_option( 'nav_shadow_color' ) ): ?>
ul.menu>li>a{
	<?php if( weblusive_get_option( 'nav_links_color' ) ) echo 'color: '.weblusive_get_option( 'nav_links_color' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'nav_shadow_color' ) ) echo 'text-shadow: 0 1px 1px '.weblusive_get_option( 'nav_shadow_color' ).' !important;'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'nav_links_color_hover' ) || weblusive_get_option( 'nav_shadow_color_hover' ) ): ?>
ul.menu>li>a:hover  {
	<?php if( weblusive_get_option( 'nav_links_color_hover' ) ) echo 'color: '.weblusive_get_option( 'nav_links_color_hover' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'nav_shadow_color_hover' ) ) echo 'text-shadow: 0 1px 1px '.weblusive_get_option( 'nav_shadow_color_hover' ).' !important;'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'nav_current_links_color' ) || weblusive_get_option( 'nav_current_shadow_color' ) || weblusive_get_option( 'nav_current_bg' )): ?>
ul.menu>li.active:hover{<?php if( weblusive_get_option( 'nav_current_bg' ) ) echo 'background-color:'.weblusive_get_option( 'nav_current_bg' ).' !important;'; ?>} 
.navbar-default .navbar-nav > li.current-menu-item:after{<?php if( weblusive_get_option( 'nav_current_bg' ) ) echo 'border-top-color:'.weblusive_get_option( 'nav_current_bg' ).' !important;'; ?>} 
ul.menu>li.current-menu-item > a{
	<?php if( weblusive_get_option( 'nav_current_links_color' ) ) echo 'color: '.weblusive_get_option( 'nav_current_links_color' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'nav_current_shadow_color' ) ) echo 'text-shadow: 0 1px 1px '.weblusive_get_option( 'nav_current_shadow_color' ).' !important;'; ?>
	<?php if( weblusive_get_option( 'nav_current_bg' ) ) echo 'background-color:'.weblusive_get_option( 'nav_current_bg' ).' !important;'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'nav_separator' ) ): ?>
ul.dropdown-menu li {
	border-bottom-color: <?php echo weblusive_get_option( 'nav_separator' ); ?>;
}
ul.dropdown-menu li:hover>a {border-left-color:<?php echo weblusive_get_option( 'nav_separator' ); ?>;}
<?php endif; ?>
<?php $contentbg = weblusive_get_option( 'main_content_bg' ); 
if( !empty( $contentbg['img']) || !empty( $contentbg['color'] ) ): ?>
.about-box,  .contact-box, .single-box{background:<?php echo isset($contentbg['color']) ? $contentbg['color'] : '';?> url('<?php echo isset($contentbg['img']) ? $contentbg['img'] : '' ?>') <?php echo isset($contentbg['repeat']) ? $contentbg['repeat'] : ''; ?> <?php echo isset($contentbg['attachment']) ? $contentbg['attachment'] : ''; ?> <?php echo  isset($contentbg['hor']) ? $contentbg['hor'] : ''  ?> <?php echo  isset($contentbg['ver']) ? $contentbg['ver'] :'' ?> !important;}<?php echo "\n"; ?>
<?php endif; ?>
<?php if( weblusive_get_option('promo_bg')):$bg = weblusive_get_option( 'promo_bg' );?>
	#banner .bx-wrapper{background:<?php echo isset($bg['color']) ? $bg['color'] : '';?> url('<?php echo isset($bg['img']) ? $bg['img'] : '' ?>') <?php echo isset($bg['repeat']) ? $bg['repeat'] : ''; ?> <?php echo isset($bg['attachment']) ? $bg['attachment'] : ''; ?> <?php echo  isset($bg['hor']) ? $bg['hor'] : ''  ?> <?php echo  isset($bg['ver']) ? $bg['ver'] :'' ?> !important;}
<?php endif; ?>
<?php if( weblusive_get_option('footer_background')):$footerbg = weblusive_get_option( 'footer_background' );?>
	.footer-part {background:<?php echo isset($footerbg['color']) ? $footerbg['color'] : '';?> url('<?php echo isset($footerbg['img']) ? $footerbg['img'] : '' ?>') <?php echo isset($footerbg['repeat']) ? $footerbg['repeat'] : ''; ?> <?php echo isset($footerbg['attachment']) ? $footerbg['attachment'] : ''; ?> <?php echo  isset($footerbg['hor']) ? $footerbg['hor'] : ''  ?> <?php echo  isset($footerbg['ver']) ? $footerbg['ver'] :'' ?> !important;}
<?php endif; ?>
<?php if( weblusive_get_option( 'footer_title_color' ) ): ?>
.footer-block h2{	<?php if( weblusive_get_option( 'footer_title_color' ) ) echo 'color: '.weblusive_get_option( 'footer_title_color' ).';'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'footer_links_color' ) ): ?>
.footer-part a  {	<?php if( weblusive_get_option( 'footer_links_color' ) ) echo 'color: '.weblusive_get_option( 'footer_links_color' ).' !important;'; ?>
}
<?php endif; ?>
<?php if( weblusive_get_option( 'footer_links_color_hover' ) ): ?>
.footer-part a:hover {<?php if( weblusive_get_option( 'footer_links_color_hover' ) ) echo 'color: '.weblusive_get_option( 'footer_links_color_hover' ).' !important;'; ?>
}
<?php endif; ?>
<?php  $css =  weblusive_get_option('css'); if (isset($css)) echo htmlspecialchars_decode($css ) , "\n";?>
<?php if( weblusive_get_option('css_tablets') ) : ?>
@media (min-width: 768px) and (max-width: 979px) { 
<?php echo htmlspecialchars_decode( weblusive_get_option('css_tablets') ) , "\n";?>
}
<?php endif; ?>
<?php if( weblusive_get_option('css_wide_phones') ) : ?>
@media (max-width: 767px) {
<?php echo htmlspecialchars_decode( weblusive_get_option('css_wide_phones') ) , "\n";?>
}
<?php endif; ?>
<?php if( weblusive_get_option('css_phones') ) : ?>
@media (max-width: 480px) {
<?php echo htmlspecialchars_decode( weblusive_get_option('css_phones') ) , "\n";?>
}
<?php endif; ?>
<?php foreach( $custom_typography as $selector => $value):
$option = weblusive_get_option( $value );
if(isset( $option['font']) || isset($option['color']) || isset($option['size']) || isset($option['weight']) || isset($option['style']) ):
echo "\n".$selector."{\n"; ?>
<?php if(isset($option['font']))
	echo "	font-family: '". weblusive_get_font( $option['font']  )."' !important;\n"?>
<?php if(isset($option['color']))
	echo "	color :". $option['color']."  !important;\n"?>
<?php if(isset($option['size'] ))
	echo "	font-size : ".$option['size']."px !important;\n"?>
<?php if(isset($option['weight']) )
	echo "	font-weight: ".$option['weight']." !important;\n"?>
<?php if(isset($option['style'] ))
	echo "	font-style: ". $option['style']."  !important;\n";
	echo "}\n";?>
<?php endif;
endforeach; ?>

<?php
	$css = ob_get_clean();
	// Dentro de <style>: que nada del panel pueda cerrar la etiqueta.
	return str_ireplace( '</style', '<\/style', $css );
}
