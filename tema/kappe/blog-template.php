<?php /* Template Name: Blog */

get_header();
$catid = get_query_var('cat');
/* $cat = &get_category($catid); */
$cat = get_category($catid);

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

$blog_url = get_permalink( get_queried_object_id() );

// Entradas con paginación nativa de WordPress (sustituye al antiguo "Cargar más" por AJAX).
$blog_query = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => get_option('posts_per_page'),
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<!--Container-->
<div id="content">
<?php kappe_h1( get_the_title( get_queried_object_id() ) ); ?>
	 <?php get_template_part( 'library/includes/page-head' ); ?>
    <div class="inner-content blog-content">
       
        <div class="blog-page">
			<?php if ($sidebar_pos == 'left'):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="<?php echo $layout_size?>">
				<div class="blog-box">
					<?php
					// loop.php usa have_posts()/the_post() sobre la consulta global.
					$main_query = $GLOBALS['wp_query'];
					$GLOBALS['wp_query'] = $blog_query;
					get_template_part( 'loop', 'index' );
					$GLOBALS['wp_query'] = $main_query;
					wp_reset_postdata();
					?>
				</div>
				<?php
				$page_links = paginate_links( array(
					'base'      => trailingslashit( $blog_url ) . '%_%',
					'format'    => 'page/%#%/',
					'current'   => $paged,
					'total'     => $blog_query->max_num_pages,
					'type'      => 'array',
					'prev_text' => __( 'Previous', 'kappe' ),
					'next_text' => __( 'Next', 'kappe' ),
				) );
				if ( $page_links ) : ?>
					<ul class="pagination">
						<?php foreach ( $page_links as $link ) : ?>
							<li<?php echo ( false !== strpos( $link, 'current' ) ) ? ' class="current active"' : ''; ?>><?php echo $link; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php if ($sidebar_pos == 'right'):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="clearfix"></div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
