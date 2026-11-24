<?php /* Template Name: Blog */

get_header();
$catid = get_query_var('cat');
$cat = &get_category($catid);

$get_meta = get_post_custom($post->ID);
$size = weblusive_get_option('sidebar_size');
$sidebar_pos = isset( $get_meta['_weblusive_sidebar_pos'][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';
if ( get_query_var('paged') ) {

    $paged = get_query_var('paged');

} elseif ( get_query_var('page') ) {

    $paged = get_query_var('page');

} else {

    $paged = 1;

}
?>

<!--Container-->
<div id="content">
	 <?php get_template_part( 'library/includes/page-head' ); ?>
    <div class="inner-content blog-content">
       
        <div class="blog-page" id="ajax-load-more">
			<?php if ($sidebar_pos == 'left'):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="<?php echo $layout_size?>">
				<div class="blog-box" data-path="<?php echo get_template_directory_uri(); ?>" data-post-type="post" data-category="" data-taxonomy="" data-tag="" data-author="" data-display-posts="<?php echo get_option('posts_per_page') ?>" data-button-text="<?php _e( 'Load More', 'Kappe' ); ?>">
					<?php if ( $wp_query->max_num_pages > 1 ): ?><?php endif?>
				</div>
			</div>
			<?php if ($sidebar_pos == 'right'):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="clearfix"></div>
        </div>
    </div>
</div>

<?php get_footer(); ?>