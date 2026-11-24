<?php /* Template Name: Regular page */ 

get_header();?>

<?php
	$get_meta = get_post_custom($post->ID);
	$size = weblusive_get_option('sidebar_size');
	
	$sidebar_pos = isset( $get_meta['_weblusive_sidebar_pos'][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
	$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
	$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
	if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';
?>

<!-- Container -->
<div id="content">
	<?php get_template_part( 'library/includes/page-head' ); ?>
    <div class="inner-content">
		<div class="row-fluid">
            <?php if ($sidebar_pos == 'left'):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<div class="<?php echo $layout_size?> cpart">
					<div class="main-content">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endwhile; ?>	
			<?php if ($sidebar_pos == 'right'):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="clearfix"></div>
        </div>
    </div>
</div>

<?php get_footer();?>