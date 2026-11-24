<?php get_header();

$sidebar = weblusive_get_option('sidebar_archive');
$sidebar_pos =  weblusive_get_option('sidebar_pos');
if (!$sidebar) $sidebar_pos = 'full';

$size = weblusive_get_option('sidebar_size');
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';
$get_meta = get_post_custom($post->ID);
?>
<div id="content">
    <div class="inner-content blog-content">
		<?php if ($sidebar_pos == 'left' && $sidebar):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar($sidebar); ?></aside><?php endif; ?>
		<div class="<?php echo $layout_size ?>">
			<div class="blog-box">
				<?php
				if ( have_posts() ) the_post();
					rewind_posts();       
					get_template_part( 'loop', 'archive' );
				?>
			</div>
			<?php if ( $wp_query->max_num_pages > 1 ): ?>
				<div class="pagination-list blog-pagination">
					<?php include(Kappe_PLUGINS . '/wp-pagenavi.php' ); wp_pagenavi(); ?> 
					<div class="clear"></div>
				</div>
			<?php endif?>
		</div>
		<?php if ($sidebar_pos == 'right' && $sidebar):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar($sidebar); ?></aside><?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>
<?php get_footer(); ?>