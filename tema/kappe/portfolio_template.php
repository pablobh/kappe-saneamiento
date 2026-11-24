<?php /* Template Name: Portfolio */
get_header();
$pageId = $post->ID;
$_SESSION['Kappe_page_id'] = $pageId;

$get_meta = get_post_custom($post->ID);
$size = weblusive_get_option('sidebar_size');

$sidebar_pos = isset( $get_meta['_weblusive_sidebar_pos'][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';

$portfolio_type = get_post_meta($post->ID, "_portfolio_type", $single = false);
$paginationEnabled = (isset($portfolio_type) && !(empty ($portfolio_type))) ? $portfolio_type[0] : 0;
$page_template_name = get_post_meta($post->ID,'_wp_page_template',true); 

$itemsize = 'column-three';	
$itemlayout = 'column4';
$colnumber = 3;
$thumbsize = 'portfolio';

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if( get_post_meta($post->ID, "_page_portfolio_num_items_page", $single = true) != '' && $paginationEnabled ) 
{ 
	$items_per_page = get_post_meta($post->ID, "_page_portfolio_num_items_page", $single = false);
} 
else 
{ // else don't paginate
	$items_per_page[0] = 777;
}
$loop = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => $items_per_page[0])); 
?>
<div id="content">

<?php if ( post_password_required() ) {
 
  echo '  <div class="inner-content blog-content" style="background:#fff; margin:0px 20px; padding:20px">'.get_the_password_form().'</div>';
 
} else { ?>

	<?php get_template_part( 'library/includes/page-head' ); ?>
    <div class="inner-content blog-content">
        <div class="portfolio-page">
            
            <?php if ($sidebar_pos == 'left'): ?><aside class="sidebar-div <?php echo $sidebar_size?>" id="sidebar"><?php echo get_sidebar(); ?></aside><?php endif?>
				<div class="<?php echo $layout_size?>">
					<div class="portfolio-box">
						<?php 
						$cats = get_post_meta($post->ID, "_page_portfolio_cat", $single = true);
						if( $cats == '' ): ?>
							<p><?php _e('No categories selected. To fix this, please login to your WP Admin area and set
								the categories you want to show by editing this page and setting one or more categories 
								in the multi checkbox field "Portfolio Categories".', 'Kappe')?>
							</p>
						<?php else: ?>		
							<?php 
								// If the user hasn't set a number of items per page, then use JavaScript filtering
								if( $items_per_page[0] == 777 ) : endif;
								$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
								//  query the posts in selected terms
								$portfolio_posts_to_query = get_objects_in_term( explode( ",", $cats ), 'portfolio_category');
							 ?>
							 <?php if (!empty($portfolio_posts_to_query)):			
								$wp_query = new WP_Query( array( 'post_type' => 'portfolio', 'orderby' => 'menu_order', 'order' => 'ASC', 'post__in' => $portfolio_posts_to_query, 'paged' => $paged, 'showposts' => $items_per_page[0] ) ); 
								
								if ($wp_query->have_posts()):  ?>
								<?php while ($wp_query->have_posts()) : 							
									$wp_query->the_post();
									$custom = get_post_custom($post->ID);
										 
									// Get the portfolio item categories
									$cats = wp_get_object_terms($post->ID, 'portfolio_category');
														   
															
									if ($cats):
										$cat_slugs = '';
										$cat_names = '';
										foreach( $cats as $cat ) {
											$cat_slugs .= slugify($cat->slug) . " ";
											$cat_names.= $cat->name . " ";
										}
									endif;
									?>
						
									<?php $link = ''; $thumbnail = get_the_post_thumbnail($post->ID, $thumbsize); ?>
									
									<div class="project-post <?php echo $cat_slugs; ?>">
                            <?php if (!empty($thumbnail)): ?>
								<?php the_post_thumbnail($thumbsize, array('class' => 'cover')); ?>
							<?php else :?>
								 <img src="http://placehold.it/405x311" alt="<?php _e ('No preview image', 'SDSU') ?>" />
							<?php endif?>
                            <div class="hover-box">
								<div class="project-title">
									<h2><?php the_title() ?></h2>                                    
									<span><?php echo $cat_names; ?></span>
								</div>
								<ul class="project-links">
									<?php if( !empty ( $custom['_portfolio_video'][0] ) ) : $link = $custom['_portfolio_video'][0]; ?>
										<li>
											<a href="<?php echo $link ?>" class="zoom video"  data-rel="prettyPhoto" title="<?php echo '<h5>'.get_the_title($post->ID).'</h5><p>'.get_the_excerpt().'</p>' ?>">
												<i class="fa fa-film"></i>
											</a>
										</li>
									<?php elseif( isset($custom['_portfolio_link'][0]) && $custom['_portfolio_link'][0] != '' ) : ?>
										<li>
											<a href="<?php echo $custom['_portfolio_link'][0] ?>"  title="<?php the_title() ?>">
												<i class="fa fa-external-link"></i>
											</a>
										</li>
									<?php elseif(  isset( $custom['_portfolio_no_lightbox'][0] )  &&  $custom['_portfolio_no_lightbox'][0] !='' ) : $link = get_permalink(get_the_ID());  ?>
										<li>
											<a href="<?php echo $link; ?>"  title="<?php the_title() ?>">
												<i class="fa fa-arrow-right"></i>
											</a>
										</li>
									<?php else : ?>
										<?php  
											$full_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full', false);
											$argsThumb = array(
												'order'          => 'ASC',
												'posts_per_page'  => 99,
												'post_type'      => 'attachment',
												'post_parent'    => $post->ID,
												'post_mime_type' => 'image',
												'post_status'    => null,
												'exclude' => get_post_thumbnail_id($post->ID)
											);														
											$link = $full_image[0];
										?>
										<li>
											<a href="<?php echo $link ?>" class="zoom-image" data-rel="prettyPhoto[ppgal<?php echo $post->ID?>]" title="<?php echo '<h5>'.get_the_title($post->ID).'</h5><p>'.get_the_excerpt().'</p>' ?>"><i class="fa fa-search"></i></a>
											<?php 
												$attachments = get_posts($argsThumb);
												if ($attachments) {
													foreach ($attachments as $attachment) {
														echo '<a href="'.wp_get_attachment_url($attachment->ID, 'full', false, false).'" data-rel="prettyPhoto[ppgal'.$post->ID.']" class="gallery-link" title="'.get_the_title($post->ID).'"></a>';
													}
												}
											?>
										</li>
										
									<?php endif; ?>  
								</ul>     
							</div>	
						</div>		
								<?php endwhile; ?>
							<?php endif;?>
						<?php endif;?>
					<?php endif?>
				</div>
				<?php if ($paginationEnabled ):?>
					<div class="pagination-wrapper">
						<div class="col-md-6 col-md-offset-3 text-center">
							<?php include(Kappe_PLUGINS . '/wp-pagenavi.php' ); wp_pagenavi(); ?> 
							<div class="clearfix"></div>
						</div>
					</div>
				<?php endif?>
            </div>
            <?php if ($sidebar_pos == 'right'):?><aside class="sidebar-div <?php echo $sidebar_size?>" id="sidebar"><?php echo get_sidebar(); ?></aside><?php endif?>
        </div>
    </div>
	
<?php }  ?>
</div>
				
<?php  get_footer(); ?>