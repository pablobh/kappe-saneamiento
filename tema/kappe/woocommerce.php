<?php /* Template Name: Shop page */ 

get_header();

$id = get_option('woocommerce_shop_page_id');
$get_meta = get_post_custom($id);

$sidebar = isset( $get_meta['_weblusive_sidebar_post'][0]) ? $get_meta["_weblusive_sidebar_post"][0] : 'Primary Widget Area';
$percolumn = weblusive_get_option('products_per_row');
if (empty($percolumn)) $percolumn = 'columns-4';
$weblusive_sidebar_pos = isset( $get_meta['_weblusive_sidebar_pos'][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';

$sidebarLayout=  weblusive_get_option('sidebar_layout_type');

$hidetitles = weblusive_get_option('hide_titles');
$hidebreadcrumbs = weblusive_get_option('hide_breadcrumbs');

$hideheading = isset( $get_meta['_weblusive_hide_innerheading'][0]) ? $get_meta["_weblusive_hide_innerheading"][0] : '';
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 

?>



<div id="content">
	<?php get_template_part( 'library/includes/page-head' ); ?>
	<div class="inner-content">
		<div class="row-fluid">
		
		<?php if ($weblusive_sidebar_pos == 'left'):?><aside class="col-sm-3 sidebar sidebar-left"><?php dynamic_sidebar($sidebar); ?></aside><?php endif?>	
		<div class="<?php if ($weblusive_sidebar_pos == 'full'):?>col-md-12<?php else:?>col-md-9<?php endif?> woocommerce-page cpart <?php echo $percolumn?>">
			<?php if(function_exists('woocommerce_content')): ?>
				<div class="main-content"><?php woocommerce_content(); ?></div>
			<?php else:?>
				<p class="label label-warning">Please install and activate the Woocommerce plugin to use the shop functionality.</p>
			<?php endif?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'construction' ), 'after' => '</div>' ) ); ?>
		</div>
		
		<?php if ($weblusive_sidebar_pos == 'right'):?><aside class="col-sm-3 sidebar" ><?php dynamic_sidebar($sidebar); ?></aside><?php endif?>
	</div>
</div>
<?php get_footer();?>