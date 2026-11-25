<?php

/*************** PORTFOLIO POST-TYPES  ****************/
add_action('init', 'portfolio_register');

function portfolio_register() {	

	register_post_type( 'portfolio' , 
		array(
			'label' => 'Portfolio',
			'singular_label' => 'Portfolio',
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'public' => true,
			'rewrite' => array('with_front' => false, 'slug'=>'works'),
			'menu_position' => null,
			'show_ui' => true, 
			'query_var' => true,
			'capability_type' => 'page',
			'hierarchical' => false,
			'edit_item' => __( 'Edit Work', 'Kappe'),
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions')
		)
	);

	register_taxonomy( 'portfolio_category', 
			'portfolio', 
			array( 'hierarchical' => true, 
					'label' => __('Categories', 'Kappe'),
					'singular_label' => __('Category', 'Kappe'),
					'public' => true,
					'show_tagcloud' => false,
					'query_var' => 'true',
					'rewrite' => array('slug' => 'portfolio_category' , 'with_front' => false)
			)
		);  
	
	add_filter('manage_edit-portfolio_columns', 'portfolio_edit_columns');
	add_action('manage_posts_custom_column',  'portfolio_custom_columns');
	
	function portfolio_edit_columns($columns){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'portfolio_category' => 'Category',
			'portfolio_description' => 'Description',
			'portfolio_image' => 'Image Preview'
		);
	
		return $columns;
	}
	
	function portfolio_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "portfolio_category":  
				echo get_the_term_list($post->ID, 'portfolio_category', '', ', ','');  
				break;  

			case 'portfolio_description':
				the_excerpt();  
				break;  

			case 'portfolio_image':
				the_post_thumbnail( 'blog-thumb' );
				break;
		}
	}
}


function my_post_type_link_filter_function( $post_link, $id = 0, $leavename = FALSE ) {
    if ( strpos('%portfolio_category%', $post_link)  < 0 ) {
      return $post_link;
    }
    $post = get_post($id);
    if ( !is_object($post) || $post->post_type != 'portfolio' ) {
      return $post_link;
    }
    $terms = wp_get_object_terms($post->ID, 'portfolio_category');
    if ( !$terms ) {
      return str_replace('portfolio/category/%portfolio_category%/', '', $post_link);
    }
    return str_replace('%portfolio_category%', $terms[0]->slug, $post_link);
}
  
add_filter('post_type_link', 'my_post_type_link_filter_function', 1, 3);
  


add_action( 'admin_menu', 'register_portfolio_menu' );

function register_portfolio_menu() {
	add_submenu_page(
		'edit.php?post_type=portfolio',
		'Order portfolio',
		'Sort items',
		'edit_pages', 'portfolio-order',
		'portfolio_order_page'
	);
}


function portfolio_order_page() 
{
	?></pre>
	<div class="wrap">
        <h2>Sort Items</h2>
        Simply drag the items up or down and they will be saved in that order.
        
        <?php $slides = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order' ) ); ?>
        <table id="sortable-table-portfolio" class="wp-list-table widefat fixed posts">
            <thead>
                <tr>
                    <th class="column-order">Order</th>
                    <th class="column-title">Title</th>
                    <th class="column-thumbnail">Thumbnail</th>
         
                </tr>
            </thead>
            <tbody data-post-type="portfolio" data-nonce="<?php echo esc_attr( wp_create_nonce( 'portfolio_update_post_order' ) ); ?>"><!--?php while( $products--->
				<?php if( $slides->have_posts() )  : ?>
                    <?php while ($slides->have_posts()): $slides->the_post(); ?>
                        <tr id="post-<?php the_ID(); ?>">
                            <td class="column-order"><img title="" src="<?php echo get_stylesheet_directory_uri() . '/images/move-icon.png'; ?>" alt="Move Icon" width="32" height="32" /></td>
                            <td class="column-title"><strong><?php the_title(); ?></strong></td>
                    		<td class="column-thumbnail"><?php the_post_thumbnail( 'blog-thumb' ); ?></td>
                         </tr>
                    <?php endwhile; ?>
                <?php else : ?>        
                    No portfolio items found, make sure you <a href="post-new.php?post_type=portfolio">create one</a>.
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>	
            </tbody>
            <tfoot>
                <tr>
                    <th class="column-order">Order</th>
                    <th class="column-title">Title</th>
                    <th class="column-thumbnail">Thumbnail</th>
                </tr>
            </tfoot>
        </table>
 	</div>
	<pre>
	<!-- .wrap -->	
	<?php 
}

add_action( 'wp_ajax_portfolio_update_post_order', 'portfolio_update_post_order' );

// E2.B2: nonce, capacidad del menú "Sort items" (edit_pages) y permiso sobre cada proyecto.
function portfolio_update_post_order() {
	check_ajax_referer( 'portfolio_update_post_order', 'nonce' );

	if ( ! current_user_can( 'edit_pages' ) )
		wp_send_json_error( null, 403 );

	$order = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : array();
	if ( ! is_array( $order ) )
		wp_send_json_error( null, 400 );

	/**
	*    Expect: $order = array(
	*                menu_order => post-XX
	*            );
	*/
	foreach( $order as $menu_order => $post_id )
	{
		$post_id    = absint( str_ireplace( 'post-', '', (string) $post_id ) );
		$menu_order = absint( $menu_order );
		if ( ! $post_id || 'portfolio' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) )
			continue;
		wp_update_post( array( 'ID' => $post_id, 'menu_order' => $menu_order ) );
	}

	wp_send_json_success();
}



?>