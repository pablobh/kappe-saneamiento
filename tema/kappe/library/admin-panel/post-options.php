<?php
add_action("admin_init", "posts_init");
function posts_init(){
	add_meta_box("post_options", theme_name ." - Post Options", "post_options", "post", "normal", "high");
	add_meta_box("page_options", theme_name ." - Page Options", "page_options", "page", "normal", "high");
	add_meta_box("portfolio_options", theme_name ." - Portfolio item options", "portfolio_options", "portfolio", "normal", "high");
}

function portfolio_options(){
	global $post ;
	$get_meta = get_post_custom($post->ID);
	$weblusive_sidebar_pos = isset($get_meta["_weblusive_sidebar_pos"]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
	wp_enqueue_script( 'weblusive-admin-slider' );  

?>	
		<div class="weblusivepanel-item">
			<?php weblusive_post_options_hidden_fields(); ?>
		
		
			<h3>Post Head Options</h3>
			<?php	

			weblusive_post_options(				
				array(	"name" => "Display",
						"id" => "_weblusive_post_head",
						"type" => "select",
						"options" => array(
							''=> 'Default',
							'none'=> 'None',
							'slider'=> 'Slider',
							'promotext'=> 'Promo text',
							'thumb'=> 'Featured Image',
							'lightbox'=> 'Featured Image + lightbox'
						)));
			
			global $post;
			$orig_post = $post;
			
			$sliders = array();
			$custom_slider = new WP_Query( array( 'post_type' => 'weblusive_slider', 'posts_per_page' => -1 ) );
			while ( $custom_slider->have_posts() ) {
				$custom_slider->the_post();
				$sliders[get_the_ID()] = get_the_title();
			}
			$post = $orig_post;
			wp_reset_query();
	
			weblusive_post_options(				
				array(	"name" => "Custom Slider",
						"id" => "_weblusive_post_slider",
						"type" => "select",
						"options" => $sliders ));

			weblusive_post_options(				
				array(	"name" => "Promo text",
						"id" => "_weblusive_promotext",
						"type" => "textarea"));

			?>
		</div>

		<div class="weblusivepanel-item">
			<h3>General Options</h3>
			<?php	

			weblusive_post_options(				
				array(	"name" => "Item links to inner page",
						"id" => "_portfolio_no_lightbox",
						"type" => "checkbox",
						"hint" =>  "Thumbnail to link directly to the portfolio item detail or custom URL instead of opening the full image in the lightbox"
				));
			weblusive_post_options(				
				array(	"name" => "Show post content in a lightbox",
						"id" => "_portfolio_inner_lightbox",
						"type" => "checkbox",
						"hint" =>  "All the information set in the post's content editor will display in a lightbox"
				));	

			weblusive_post_options(				
				array(	"name" => "Portfolio Item custom destination URL",
						"id" => "_portfolio_link",
						"hint" => "If you want the portfolio item have custom link rather going to item's details page. Example: http://www.weblusive.com",
						"type" => "text")
				);
											
			weblusive_post_options(				
				array(	"name" => "Portfolio third-party video in lightbox",
						"id" => "_portfolio_video",
						"hint" => "<strong>Supports Youtube, Vimeo, etc.. </strong><br /> Example:http://www.youtube.com/watch?v=ehuwoGVLyhg",
						"type" => "text"));
											
			weblusive_post_options(				
				array(	"name" => "Make project featured",
						"id" => "_portfolio_featured",
						"type" => "select",
						"hint" => "If set, this item will appear in portfolio's featured items list when using [list_portfolio /] shortcode.",
						"type" => "checkbox"));
			?>
		</div>		
  <?php
}

function post_options(){
	global $post ;
	$get_meta = get_post_custom($post->ID);
	$weblusive_sidebar_pos = isset($get_meta["_weblusive_sidebar_pos"]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'right';
	wp_enqueue_script( 'weblusive-admin-slider' );  

?>	
		<div class="weblusivepanel-item">
		<?php weblusive_post_options_hidden_fields(); ?>
		
		<h3>General Options</h3>
			<?php weblusive_post_options(				
				array(
					"name" => "Media type to show on listing",
					"id" => "_blog_mediatype",
					"type" => "select",
					"options" => array( 
						"image" => "Featured image" ,
						"slider" => "Slider",
						"youtube" => "Youtube video" ,
						"vimeo" => "Vimeo video",
						"dailymotion" => "Dailymotion video",
						"" => "None of the above"
					))
				);
				
				weblusive_post_options(				
				array(	"name" => "Video ID",
						"id" => "_blog_video",
						"hint" => "Choose only if any of Video services has been chosen from the dropdown above.",
						"type" => "text")
				);
				weblusive_post_options(		
				array(
					"name" => "Autoplay video",
					"id" => "_blog_videoap",
					"type" => "select",
					"options" => array( 
						"0" => "No" ,
						"1" => "Yes"
					))
				);
			?>
		</div>
		
		<div class="weblusivepanel-item">
			<h3>Sidebar Options</h3>
			<div class="option-item">
				<?php
					$checked = 'checked="checked"';
				?>
				<ul id="sidebar-position-options" class="weblusive-options">
									
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="right" <?php if($weblusive_sidebar_pos == 'right' ) echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-right.png" /></a>
					</li>
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="left" <?php if($weblusive_sidebar_pos == 'left') echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-left.png" /></a>
					</li>
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="full" <?php if($weblusive_sidebar_pos == 'full' || !$weblusive_sidebar_pos) echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-no.png" /></a>
					</li>
				</ul>
			</div>
			<?php
			$sidebars = weblusive_get_option( 'sidebars' ) ;
			$new_sidebars = array(''=> 'Default');
			if($sidebars){
				foreach ($sidebars as $sidebar) {
					$new_sidebars[$sidebar] = $sidebar;
				}
			}
					
			weblusive_post_options(				
				array(	"name" => "Choose Sidebar",
						"id" => "_weblusive_sidebar_post",
						"type" => "select",
						"options" => $new_sidebars ));
			?>
		</div>
		
		
		<div class="weblusivepanel-item">
			<h3>Post Head Options</h3>
			<?php	

			weblusive_post_options(				
				array(	"name" => "Display",
						"id" => "_weblusive_post_head",
						"type" => "select",
						"options" => array(
							''=> 'Default',
							'none'=> 'None',
							'slider'=> 'Slider',
							'promotext'=> 'Promo text',
							'thumb'=> 'Featured Image',
							'lightbox'=> 'Featured Image + lightbox'
						)));
			
			global $post;
			$orig_post = $post;
			
			$sliders = array();
			$custom_slider = new WP_Query( array( 'post_type' => 'weblusive_slider', 'posts_per_page' => -1 ) );
			while ( $custom_slider->have_posts() ) {
				$custom_slider->the_post();
				$sliders[get_the_ID()] = get_the_title();
			}
			$post = $orig_post;
			wp_reset_query();
	
			weblusive_post_options(				
				array(	"name" => "Custom Slider",
						"id" => "_weblusive_post_slider",
						"type" => "select",
						"options" => $sliders ));

			weblusive_post_options(				
				array(	"name" => "Promo text",
						"id" => "_weblusive_promotext",
						"type" => "textarea"));

			?>
		</div>	
  <?php
}

/*********************************************************************************************/

function page_options(){
	global $post ;
	$get_meta = get_post_custom($post->ID);
	$weblusive_sidebar_pos = isset ($get_meta["_weblusive_sidebar_pos"][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
	wp_enqueue_script( 'weblusive-admin-slider' );  
	
	$categories_obj = get_categories();
	$categories = array();
	$categories = array(''=> 'All Categories');
	foreach ($categories_obj as $pn_cat) {
		$categories[$pn_cat->cat_ID] = $pn_cat->cat_name;
	}
	
?>
		<?php weblusive_post_options_hidden_fields(); ?>
		<div class="weblusivepanel-item">
			<h3>Sidebar Options</h3>
			<div class="option-item">
				<?php
					$checked = 'checked="checked"';
				?>
				<ul id="sidebar-position-options" class="weblusive-options">
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="right" <?php if($weblusive_sidebar_pos == 'right' ) echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-right.png" /></a>
					</li>
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="left" <?php if($weblusive_sidebar_pos == 'left') echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-left.png" /></a>
					</li>
					<li>
						<input id="_weblusive_sidebar_pos"  name="_weblusive_sidebar_pos" type="radio" value="full" <?php if($weblusive_sidebar_pos == 'full' || !$weblusive_sidebar_pos) echo $checked; ?> />
						<a class="checkbox-select" href="#"><img src="<?php echo get_template_directory_uri(); ?>/library/admin-panel/images/sidebar-no.png" /></a>
					</li>
				</ul>
			</div>
			<?php
			$sidebars = weblusive_get_option( 'sidebars' ) ;
			$new_sidebars = array(''=> 'None');
			if($sidebars){
				foreach ($sidebars as $sidebar) {
					$new_sidebars[$sidebar] = $sidebar;
				}
			}
					
			weblusive_post_options(				
				array(	"name" => "Choose Sidebar",
						"id" => "_weblusive_sidebar_post",
						"type" => "select",
						"options" => $new_sidebars ));
			?>
		</div>
		
		<div class="weblusivepanel-item">
			<h3>Head Options</h3>
			<?php	

			weblusive_post_options(				
				array(	"name" => "Display",
						"id" => "_weblusive_post_head",
						"type" => "select",
						"options" => array(
							''=> 'Default',
							'none'=> 'None',
							'slider'=> 'Slider',
							'promotext'=> 'Promo text',
							'thumb'=> 'Featured Image',
							'lightbox'=> 'Featured Image + lightbox'
						)));


		
			global $post;
			$orig_post = $post;
			
			$sliders = array();
			$custom_slider = new WP_Query( array( 'post_type' => 'weblusive_slider', 'posts_per_page' => -1 ) );
			while ( $custom_slider->have_posts() ) {
				$custom_slider->the_post();
				$sliders[get_the_ID()] = get_the_title();
			}
			$post = $orig_post;
			wp_reset_query();
	
			weblusive_post_options(				
				array(	"name" => "Custom Slider",
						"id" => "_weblusive_post_slider",
						"type" => "select",
						"options" => $sliders ));
			weblusive_post_options(				
				array(	
					"name" => "Promo text",
					"id" => "_weblusive_promotext",
					"type" => "textarea",
					"hint"=> "If you would like to have several promo texts with automatical sliding, separate your texts with * symbol."));
				
			?>
		</div>
		
		<div class="weblusivepanel-item">
			
			<h3>Portfolio page Options</h3>
			<?php	
				weblusive_post_options(
					array(
					"id" => "_page_portfolio_cat",
					"name" => "Portfolio Categories",
					"hint" => "Choose only if this page uses a Portfolio page template",
					"type" => "portfolio_cat")
				);	
				
				weblusive_post_options(
					array(
					"id" => "_page_portfolio_num_items_page",
					"name" => "Portfolio items per page",
					"hint" => "Number of items displayed per page. Leave blank if you don't want to paginate the portfolio items.",
					"type"  => "slider",  
					"min"   => "0",  
					"max"   => "100",  
					"step"  => "1")
				);	
				weblusive_post_options(
					array(
						"id" => "_portfolio_type",
						"name" => "Portfolio type",
						"hint" => "Choose over animated portfolio with pagination (all items displayed in one page) or non-animated portfolio with pagination enabled.<br /> Applies only to portfolio pages.",
						"type" => "select",
						"options" => array('0' => "Animated without pagination", '1' => "Non-animated with pagination")
					)
				);	
			?>	
		</div>

  <?php
}

/**
 * Campo oculto que marca el formulario de las cajas del tema y su nonce (E2.B2).
 */
function weblusive_post_options_hidden_fields(){
	echo '<input type="hidden" name="weblusive_hidden_flag" value="true" />';
	wp_nonce_field( 'weblusive_post_options', 'weblusive_post_options_nonce' );
}

/**
 * Valores permitidos de las listas desplegables. Un valor fuera de la lista no se guarda
 * (la meta se queda como estaba).
 */
function weblusive_post_options_allowed(){
	return array(
		'_weblusive_sidebar_pos' => array( 'right', 'left', 'full' ),
		'_weblusive_post_head'   => array( '', 'none', 'slider', 'promotext', 'thumb', 'lightbox' ),
		'_portfolio_type'        => array( '0', '1' ),
		'_blog_videoap'          => array( '0', '1' ),
		'_blog_mediatype'        => array( 'image', 'slider', 'youtube', 'vimeo', 'dailymotion', '' ),
	);
}

add_action('save_post', 'save_postdata');
function save_postdata( $post_id ){

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return;
	if ( ! isset( $_POST['weblusive_hidden_flag'], $_POST['weblusive_post_options_nonce'] ) )
		return;
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['weblusive_post_options_nonce'] ) ), 'weblusive_post_options' ) )
		return;
	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	$allowed = weblusive_post_options_allowed();
	$ints    = array( '_weblusive_post_slider', '_page_portfolio_num_items_page' );

		$custom_meta_fields = array(
			'_weblusive_sidebar_pos',
			'_page_portfolio_cat',
			'_page_portfolio_num_items_page',
			'_portfolio_type',
			'_weblusive_sidebar_post',
			'_weblusive_post_head',
			'_weblusive_post_slider',
			'_weblusive_promotext',
			'_portfolio_no_lightbox',
			'_portfolio_inner_lightbox',
			'_portfolio_link',
			'_portfolio_video',
			'_portfolio_featured',
			'_blog_videoap',
			'_blog_video',
			'_blog_mediatype'
		);
		
			
		foreach( $custom_meta_fields as $custom_meta_field ){
			if(isset($_POST[$custom_meta_field]) )
			{
				$raw = wp_unslash( $_POST[$custom_meta_field] );
				if(is_array($raw))
				{
					// Categorías del portafolio: se guardan como lista "1,2,3" (formato original).
					$data = implode( ',', array_filter( array_map( 'absint', $raw ) ) );
					update_post_meta($post_id, $custom_meta_field, $data);
				}
				elseif ( isset( $allowed[ $custom_meta_field ] ) )
				{
					if ( in_array( (string) $raw, $allowed[ $custom_meta_field ], true ) )
						update_post_meta($post_id, $custom_meta_field, $raw);
				}
				elseif ( in_array( $custom_meta_field, $ints, true ) )
				{
					update_post_meta($post_id, $custom_meta_field, (string) absint( $raw ));
				}
				else
				{
					// Formato original: texto escapado con htmlspecialchars (las plantillas lo imprimen tal cual).
					update_post_meta($post_id, $custom_meta_field, wp_slash( htmlspecialchars( (string) $raw ) ) );
				}
			}
			else
			{
				delete_post_meta($post_id, $custom_meta_field);
			}
		}
}

/*********************************************************/

function weblusive_post_options($value){
	global $post;
?>

	<div class="option-item" id="<?php echo $value['id'] ?>-item">
		<span class="label"><?php  echo $value['name']; ?></span>
	<?php
		$id = $value['id'];
		$get_meta = get_post_custom($post->ID);
		$meta_box_value = get_post_meta($post->ID, $id, true);
		if( isset( $get_meta[$id][0] ) )
			$current_value = $get_meta[$id][0];
			
	switch ( $value['type'] ) {
		
		case 'text': ?>
			<input  name="<?php echo $value['id']; ?>" id="<?php  echo $value['id']; ?>" type="text" value="<?php echo (isset($current_value) && !empty( $current_value )) ? esc_attr( $current_value ) : '' ?>" />
			<?php if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif?>
		<?php 
		break;

		case 'checkbox':
			if(isset($current_value) && !empty( $current_value ) ){$checked = "checked=\"checked\"";  } else{$checked = "";} ?>
				<input type="checkbox" name="<?php echo $value['id'] ?>" id="<?php echo $value['id'] ?>" value="true" <?php echo $checked; ?> />	
			<?php if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif?>		
		<?php	
		break;
		
		case 'select':
		?>
			<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
				<?php foreach ($value['options'] as $key => $option) { ?>
				<option value="<?php echo esc_attr( $key ) ?>" <?php if (isset($current_value) && !empty( $current_value ) && $current_value == $key) { echo ' selected="selected"' ; } ?>><?php echo esc_html( $option ); ?></option>
				<?php } ?>
			</select>
			<?php if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif?>
		<?php
		break;
		
		case 'textarea':
		?>
			<textarea style="direction:ltr; text-align:left; width:430px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="textarea" cols="100%" rows="3" tabindex="4"><?php echo isset($current_value) ? $current_value : ''  ?></textarea>
			<?php if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif?>
		<?php
		break;
		
		case 'slider':
			$meta_box_value = ($meta_box_value == '') ? 9 : absint( $meta_box_value );
			echo '
			<script type="text/javascript">		
			jQuery(document).ready(function () {						
				jQuery( "#'.$value['id'].'-slider" ).slider({ 
					value: '.$meta_box_value.', 
					min: '.$value['min'].', 
					max: '.$value['max'].', 
					step: '.$value['step'].', 
					slide: function( event, ui ) { 
						jQuery( "#'.$value['id'].'" ).val( ui.value ); 
					} 
				});
			});
			</script>';  
			
			echo '<div id="'.$value['id'].'-slider" class="slider-container"></div>
			<input type="text" name="'.$value['id'].'" id="'.$value['id'].'" value="'.$meta_box_value.'" size="5" class="minimal-textbox custom-tm" />
			<div class="clear"></div>';
			if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif;
		break;
		
		case 'portfolio_cat':
			// Get the categories first
			$args = array( 'taxonomy' => 'portfolio_category', 'hide_empty' => '0' );
			$categories = get_categories( $args ); 
			
			$selected_cats = explode( ",", $meta_box_value );
			
			echo '<ul class="portfolio-listing">';

			// Loop through each category
			foreach ($categories as $category) {
											
				foreach ($selected_cats as $selected_cat) {
					if($selected_cat == $category->cat_ID){ $checked = 'checked="checked"'; break; } else { $checked = ""; }
				}
				
				echo '<li>
					<input style="width: 14px;" type="checkbox" id="pcategory' . $category->cat_ID . '" name="' . $value[ 'id' ] . '[]" value="' . $category->cat_ID . '" ' . $checked . ' />
					<label for="pcategory'.$category->cat_ID.'" class="inline">' . $category->name . '</label>
					</li>';
			}
			
			echo '</ul>';
			if (isset($value ['hint'])):?><a href="#" class="mo-help tooltip" title="<?php echo $value ['hint']?>"></a><?php endif;
		break;
		
	
	} ?>
	</div>
<?php
}
?>