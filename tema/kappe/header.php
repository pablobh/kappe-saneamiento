<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="alternate" type="application/rss+xml" title="RSS2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        
   	<?php $favicon = weblusive_get_option('favicon'); if(!empty($favicon)):?>
		<link rel="shortcut icon" href="<?php echo $favicon ?>" /> 
 	<?php endif?>
	
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php if(weblusive_get_option('header_code')) echo  htmlspecialchars_decode(weblusive_get_option('header_code')); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="container">
	<?php if (!is_page_template('under-construction.php')):?>	
		<?php if(weblusive_get_option('layout_type')=='boxed'):?><div id="boxed"><?php endif?>
		<header>
			<div class="logo-box">
				<?php 
					$logo = weblusive_get_option('logo'); 
					$logosettings =  weblusive_get_option('logo_setting');
				?>
				<a href="<?php echo home_url() ?>" id="logo" class="logo">
				<?php if($logosettings == 'logo' && !empty($logo)):?>
					<img src="<?php echo $logo ?>" alt="<?php echo get_bloginfo('name')?>" id="logo-image" />
                <?php else:?>
                    <p class="slogan"><?php echo get_bloginfo('description') ?></p>
                <?php endif?>
				</a>
			</div>
			<a class="elemadded responsive-link" href="#">Menu</a>
			<div class="navbar-fixed-top">
				<div class="menu-box">
					<?php 
					$walker = new My_Walker;
					if(function_exists('wp_nav_menu')):
						wp_nav_menu( 
							array( 
							'theme_location' => 'primary_nav',
							'menu' =>'primary_nav', 
							'container'=>'', 
							'depth' => 4, 
							'menu_class' => 'menu',
							'menu_id' => 'header-mainnav',
							'link_before'     => '<span>',
							'link_after'      => '</span>',
							'walker' => $walker)  
						); 
					else:?>
						<ul class="sf-menu top-level-menu"><?php wp_list_pages('title_li=&depth=4'); ?></ul>
					<?php endif; ?>
				</div>
			</div>
			<div class="footer-part">
				<?php if (is_page_template('portfolio_template.php') || is_tax('portfolio_category')):
						$portfolio_type = get_post_meta($post->ID, "_portfolio_type", $single = false);
						$paginationEnabled = (isset($portfolio_type) && !(empty ($portfolio_type))) ? $portfolio_type[0] : 0;
					?>
					<div class="filter-box">
						<h3><?php _e('Filter', 'kappe')?><i class="fa fa-th-large"></i></h3>
						<?php if ($paginationEnabled || is_tax('portfolio_category')):?>
							<ul class="filter-items">
								<?php 
									$taxpage = get_page_ID_by_page_template('portfolio_template.php');
									$cats = get_post_meta($taxpage, "_page_portfolio_cat", $single = true);
									$MyWalker = new PortfolioWalker2();
									$args = array( 'taxonomy' => 'portfolio_category', 'orderby'  => 'name', 'order' => 'ASC', 'hide_empty' => '0', 'include' => $cats, 'title_li'=> '', 'walker' => $MyWalker, 'show_count' => '1');
									$categories = wp_list_categories ($args);
								?>
							</ul>
						<?php else: ?>
							<ul id="filterOptions" class="filter-items filter">
								<li><a href="#" class="active" data-filter="*"><?php _e('All Works', 'kappe')?></a></li>
								<?php 
									$cats = get_post_meta($post->ID, "_page_portfolio_cat", $single = true);                                                 
									$MyWalker = new PortfolioWalker();
									$args = array( 'taxonomy' => 'portfolio_category',  'orderby'  => 'name', 'order' => 'ASC', 'hide_empty' => '0', 'include' => $cats, 'title_li'=> '', 'walker' => $MyWalker, 'show_count' => '1');
									$categories = wp_list_categories ($args);
								?>
							</ul>
						<?php endif?>
					</div>
				<?php endif?>
				<div class="wigets-header">
					<?php 
					$footer_widget_count = weblusive_get_option('footer_widgets');
					if($footer_widget_count !== 'none'):
						for($i = 1; $i<= $footer_widget_count; $i++){
							if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Footer Widget ".$i) ) :endif;
						}			
					 endif; ?>
				</div>
				<div class="copyright-box">
					<p class="copyright">
						<?php echo  htmlspecialchars_decode(do_shortcode(weblusive_get_option('footer_copyright')))?>
					</p>   
				</div>
            </div>
			<?php if(weblusive_get_option('layout_type')=='boxed'):?></div><?php endif?>
		</header>
	 <?php endif;?>
  
