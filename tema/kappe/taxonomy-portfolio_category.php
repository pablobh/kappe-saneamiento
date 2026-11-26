<?php get_header();

	//global $paged;
 	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
	
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}
	
	$itemsize = 'column-three';	
	$itemlayout = 'column4';	
	$colnumber = 3;
	$thumbsize = 'portfolio';
	
?>

<div id="content">
<?php kappe_h1( single_term_title( '', false ) ); ?>
	<div class="inner-content  blog-content">
        <div class="portfolio-page portfolio-box">	
			<?php
            $counter=1;
			if ($wp_query->have_posts()):
				while ($wp_query->have_posts()) : 							
					$wp_query->the_post();
					$custom = get_post_custom($post->ID);
					// Get the portfolio item categories
					$cat_slugs = '';
					$cats = wp_get_object_terms($post->ID, 'portfolio_category');
					if ($cats && ! is_wp_error($cats)):
						foreach( $cats as $cat ) {$cat_slugs .= $cat->slug . " ";}
					endif;
					$link = ''; $thumbnail = get_the_post_thumbnail($post->ID, $thumbsize);
					?>
						
					<div class="project-post <?php echo $cat_slugs; ?>">
						<?php if (!empty($thumbnail)): ?>
							<?php the_post_thumbnail($thumbsize, array('class' => 'cover', 'alt' => kappe_thumb_alt())); ?>
						<?php else :?>
							 <img src="http://placehold.it/405x311" alt="<?php echo esc_attr( get_the_title() ) ?>" />
						<?php endif?>
						<div class="hover-box">
							<div class="project-title">
								<h2><a class="kappe-card-link" href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>                                    
								<span><?php echo $cat_slugs; ?></span>
								<?php if( !empty ( $custom['_portfolio_video'][0] ) ) : $link = $custom['_portfolio_video'][0]; ?>
									<a href="<?php echo $link ?>" class="zoom video" data-rel="prettyPhoto" title="<?php echo esc_attr(get_the_title()) ?>"><i class="fa fa-film"></i></a>
								<?php elseif( isset($custom['_portfolio_link'][0]) && $custom['_portfolio_link'][0] != '' ) : ?>
									<a href="<?php echo $custom['_portfolio_link'][0] ?>"  title="<?php echo esc_attr(get_the_title()) ?>">
										<i class="fa fa-external-link"></i>
									</a>
								<?php elseif(  isset( $custom['_portfolio_no_lightbox'][0] )  &&  $custom['_portfolio_no_lightbox'][0] !='' ) : $link = get_permalink(get_the_ID());  ?>
									<a href="<?php echo $link; ?>"  title="<?php echo esc_attr(get_the_title()) ?>"><i class="fa fa-arrow-right"></i></a>
								<?php elseif(  isset( $custom['_portfolio_inner_lightbox'][0] )  &&  $custom['_portfolio_inner_lightbox'][0] !='' ) : 
									$id = get_the_ID(); $link = '#portfolio-item-'.$id;  ?>
									<a href="<?php echo $link; ?>" class="link-project button btn-icon" data-rel="prettyPhoto" >
										<i class="fa fa-location-arrow icon-large"></i>
									</a>
									<div id="portfolio-item-<?php echo $id?>" class="hide lightbox-item">
											<?php the_post_thumbnail($thumbsize, array('class' => 'cover')); ?>
										<?php the_content()?>
									</div>
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
											'exclude' => get_post_thumbnail_id()
										);														
										$link = $full_image ? $full_image[0] : '';
									?>
									<a href="<?php echo $link ?>" class="zoom-image" data-rel="prettyPhoto[ppgal<?php echo $post->ID?>]" title="<?php echo esc_attr(get_the_title()) ?>"><i class="fa fa-search"></i></a>
									<?php 
										$attachments = get_posts($argsThumb);
										if ($attachments) {
											foreach ($attachments as $attachment) {
												echo '<a href="'.wp_get_attachment_url($attachment->ID, 'full', false, false).'" data-rel="prettyPhoto[ppgal'.$post->ID.']" class="gallery-link" title="'.esc_attr(get_the_title($post->ID)).'"></a>';
											}
										}
									?>
								<?php endif; ?>  
							 </div>   
						</div>	
					</div>		
				<?php $counter++; endwhile; ?>				
			<?php endif?>
		</div>
		
		<?php if ( $wp_query->max_num_pages > 1 ): ?>
			<div class="pagination-wrapper">
				<div class="col-md-6 col-md-offset-3 text-center">
					<?php include(Kappe_PLUGINS . '/wp-pagenavi.php' ); wp_pagenavi(); ?> 
					<div class="clearfix"></div>
				</div>
			</div>
		<?php endif?>
			
		<div class="clearfix"></div>
	</div>
</div>
<?php get_footer() ?>