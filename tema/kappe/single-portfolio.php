<?php /*** Portfolio Single Posts template. */ 

get_header(); 

$sidebar = weblusive_get_option('sidebar_portfolio');

$sidebar_pos =  weblusive_get_option('sidebar_pos');
if (!$sidebar) $sidebarPos = 'full';

$size = weblusive_get_option('sidebar_size');
$sidebar_size = (isset($size) && !empty ($sidebar)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full' || empty ($sidebar)) ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';

?>

<!-- Container -->
<div id="content">
    <div class="inner-content">
       <?php get_template_part( 'library/includes/page-head' ); ?>
        <div class="single-project">
            <div class="single-box">
				 <?php if ($sidebar_pos == 'left' && !empty($sidebar)): ?>
					<aside class="sidebar-div <?php echo $sidebar_size?> spart left-side" id="sidebar"><div class="sidebar-content"><?php dynamic_sidebar($sidebar); ?></div></aside>
				<?php endif; ?>
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo $layout_size?>">
						<div class="project-post-content">
							<h1><?php the_title(); ?></h1>
							<?php the_content(); ?>
						</div>
					</div>
				<?php endwhile; ?>	
				<?php if (!empty($sidebar) && $sidebar_pos=='right') :?>
					<aside class="sidebar-div <?php echo $sidebar_size?> spart right-side" id="sidebar"><div class="sidebar-content"><?php dynamic_sidebar($sidebar); ?></div></aside>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>