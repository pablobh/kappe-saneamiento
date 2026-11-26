<?php
// Custom Menu Walker
class My_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul role=\"menu\" class=\"drop-down\">\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if (isset($args->has_children) && $args->has_children  && $depth < 1)
				$class_names .= ' drop';
			if (isset($args->has_children) && $args->has_children && $depth >=1)
				$class_names .= ' drop';
			if ( in_array( 'current-menu-item', $classes ) )
				$class_names .= ' active';

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->title )	? $item->title	: '';
			$atts['target'] = ! empty( $item->target )	? $item->target	: '';
			$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

			// If item has_children add atts to a.
			if (isset($args->has_children) &&  $args->has_children && $depth === 0 ) {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
				$atts['data-toggle']	= 'drop';
				$atts['class']			= 'dropdown-toggle';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = isset($args->before) ? $args->before : '';

			/*
			 * Glyphicons
			 * ===========
			 * Since the the menu item is NOT a Divider or Header we check the see
			 * if there is a value in the attr_title property. If the attr_title
			 * property is NOT null we apply it as the class name for the glyphicon.
			 */
			if ( ! empty( $item->attr_title ) )
				$item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
			else
				$item_output .= '<a'. $attributes .'>';
            if (isset($args->link_before) && isset($args->link_after)){
			    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            }
            if (isset($args->has_children)){
                $item_output .= ( $args->has_children && 0 === $depth ) ? ' </a>' : '</a>';
            }
			$item_output .= isset($args->after) ? $args->after : '';

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">Add a menu</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo $fb_output;
		}
	}
}
/********* STRING MANIPULATIONS ************/

function limit_words($string, $word_limit) {
 
	// creates an array of words from $string (this will be our excerpt)
	// explode divides the excerpt up by using a space character
 
	$words = explode(' ', $string);
 
	// this next bit chops the $words array and sticks it back together
	// starting at the first word '0' and ending at the $word_limit
	// the $word_limit which is passed in the function will be the number
	// of words we want to use
	// implode glues the chopped up array back together using a space character
 
	return implode(' ', array_slice($words, 0, $word_limit)).'...';
}

/********** GET PAGES BY PARAMS ************/

/*-- Get page ID by Page Template --*/
function get_page_ID_by_page_template($template_name)
{
	$ids = get_posts(array(
		'post_type'              => 'any',
		'post_status'            => 'any',
		'posts_per_page'         => 1,
		'orderby'                => 'ID',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'meta_query'             => array(
			array(
				'key'   => '_wp_page_template',
				'value' => $template_name,
			),
		),
	));
	return $ids ? $ids[0] : null;
}

/*-- Get page content (Used for pages with custom post types) --*/
if(!function_exists('getPageContent'))
{
	function getPageContent($pageId)
	{
		if(!is_numeric($pageId))
		{
			return;
		}
		$post = get_post((int) $pageId);
		if($post)
		{
			return nl2br($post->post_content);
		}
	}
}


/********* HEX TO RGB CONVERSION ************/
function HexToRGB($hex) {
		$hex = str_replace("#", "", $hex);
		$color = array();
		
		if(strlen($hex) == 3) {
			$color['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$color['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$color['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		}
		else if(strlen($hex) == 6) {
			$color['r'] = hexdec(substr($hex, 0, 2));
			$color['g'] = hexdec(substr($hex, 2, 2));
			$color['b'] = hexdec(substr($hex, 4, 2));
		}
		
		return $color;
	}
/********************************************/

/************* COMMENTS HOOK *************/

function Kappe_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	
    <li class="comment-item" id="li-comment-<?php comment_ID() ?>">
        <div class="comment-box" id="comment-<?php comment_ID(); ?>">
            <?php echo get_avatar($comment, 80); ?>                
            <div class="comment-content">
				<h6>
					<?php echo get_comment_author().' / '?>		
					<span class="time"><?php printf(__('%1$s at %2$s', 'kappe'), get_comment_date(),get_comment_time()) ?></span>
					<?php edit_comment_link(__('(Edit)', 'kappe'),'  ','') ?>
				</h6>
                <?php comment_text() ?>
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'style'=>'<ul class="depth"', 'max_depth' => $args['max_depth']))) ?>
            </div>
             <?php if ($comment->comment_approved == '0') : ?><small><?php _e('Your comment is awaiting moderation.', 'kappe') ?></small><?php endif; ?>
        </div>	
<?php }

/*****************************************/

/**
 * <h1> oculto (solo lectores de pantalla y buscadores) para las plantillas
 * que no imprimen ninguno. No cambia el aspecto de la página.
 */
function kappe_h1( $text = '' ) {
	if ( $text === '' ) {
		$text = is_front_page() ? get_bloginfo( 'name' ) : single_post_title( '', false );
	}
	echo '<h1 class="kappe-h1">' . esc_html( $text ) . '</h1>' . "\n";
}

/**
 * Texto alternativo de la miniatura de una entrada: el del adjunto y, si está vacío, el título.
 */
function kappe_thumb_alt( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$alt     = trim( (string) get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true ) );
	return $alt !== '' ? $alt : get_the_title( $post_id );
}

?>