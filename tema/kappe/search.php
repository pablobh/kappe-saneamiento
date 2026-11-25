<?php /** The template for displaying Archive pages. **/
get_header();
$sidebar = weblusive_get_option('sidebar_archive');
$sidebar_pos =  weblusive_get_option('sidebar_pos');
if (!$sidebar) $sidebar_pos  = 'full';

$size = weblusive_get_option('sidebar_size');
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';
$get_meta = get_post_custom($post->ID);

?>
<div id="content">
    <div class="inner-content blog-content">
		<?php if ($sidebar_pos == 'left'):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar($sidebar); ?></aside><?php endif; ?>
			<div class="<?php echo $layout_size ?>">
				<div class="blog-box">
				<?php if ( have_posts() ) : ?>
					<?php get_template_part( 'loop', 'search' );?>
				<?php else : ?>
				<div id="post-0" class="post no-results not-found">
					<h4><?php _e( 'Nothing Found', 'kappe' ); ?></h4>
					<div class="entry-content">
						<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'kappe' ); ?></p>
					</div><!-- .entry-content -->
				</div><!-- #post-0 -->
				<?php endif; ?>
			</div>
		</div>
		<?php if ($sidebar_pos == 'right'):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar($sidebar); ?></aside><?php endif; ?>
		<div class="clearfix"></div>
    </div>
</div>
<?php get_footer(); ?>