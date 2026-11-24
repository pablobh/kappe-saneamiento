<?php /** The Template for displaying all single posts. **/

get_header(); 
$get_meta = get_post_custom($post->ID);
$sidebar = weblusive_get_option('sidebar_post');

$sidebar_pos =  weblusive_get_option('sidebar_pos');
if (!$sidebar) $sidebar_pos = 'full';

$size = weblusive_get_option('sidebar_size');
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';

$showdate = weblusive_get_option('blog_show_date'); 
$showcomments = weblusive_get_option('blog_show_comments'); 
$showcategory = weblusive_get_option('blog_show_category'); 
?>

<!-- Container -->
<div id="content">
    <div class="inner-content">
        <?php get_template_part( 'library/includes/page-head' ); ?>
        <div class="single-project">
            <div class="single-box">
                <?php if ($sidebar_pos == 'left' && !empty($sidebar)): ?>
					<aside class="sidebar-div <?php echo $sidebar_size?>" id="sidebar"><div class="sidebar-content"><?php dynamic_sidebar($sidebar); ?></div></aside>
				<?php endif; ?>
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div class="<?php echo $layout_size?>">
						<?php 
							$get_meta = get_post_custom($post->ID);
							$mediatype = isset($get_meta["_blog_mediatype"]) ? $get_meta["_blog_mediatype"][0] : 'image';
							$videoId = isset($get_meta["_blog_video"]) ? $get_meta["_blog_video"][0] : ''; 
							$autoplay =  isset($get_meta["_blog_videoap"]) ? $get_meta["_blog_videoap"][0] : '0';
							$thumbnail = get_the_post_thumbnail($post->ID, 'blog-list');
						?>		
						<?php if($mediatype== 'image' && !empty($thumbnail)):?>
							<a class="blog-image" href="<?php the_permalink()?>"><?php the_post_thumbnail('blog-list'); ?></a>
<!--						--><?php //elseif ( $mediatype == "youtube" && $videoId):?>
<!--							<iframe  src="http://www.youtube.com/embed/--><?php //echo $videoId?><!--?autoplay=--><?php //echo $autoplay?><!--" class="vid iframe-youtube"></iframe>-->
						<?php elseif ( $mediatype == "vimeo" && $videoId):?>
							<iframe  src="http://player.vimeo.com/video/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="vid iframe-vimeo"></iframe>
						<?php elseif ( $mediatype == "dailymotion" && $videoId):?>
							<iframe  src="http://www.dailymotion.com/embed/video/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="vid dailymotion-vimeo"></iframe>
						<?php elseif ( $mediatype == "veoh" && $videoId):?>
							<iframe  src="http://www.veoh.com/static/swf/veoh/SPL.swf?videoAutoPlay=<?php echo $autoplay?>&permalinkId=<?php echo $videoId?>" class="vid iframe-veoh"></iframe>
						<?php elseif ( $mediatype == "bliptv" && $videoId):?>
							<iframe  src="http://a.blip.tv/scripts/shoggplayer.html#file=http://blip.tv/rss/flash/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="vid iframe-bliptv"></iframe>
						<?php elseif ( $mediatype == "viddler" && $videoId):?>
							<iframe  src="http://www.viddler.com/embed/<?php echo $videoId?>e/?f=1&offset=0&autoplay=<?php echo $autoplay?>" class="vid iframe-viddler"></iframe>
						<?php elseif($mediatype== 'slider' && !empty($thumbnail)):?>
						<div class="flexslider">
								<ul class="slides">
									<?php 
									$argsThumb = array(
										'order'          => 'ASC',
										'post_type'      => 'attachment',
										'post_parent'    => $post->ID,
										'post_mime_type' => 'image',
										'post_status'    => null,
									);
									$attachments = get_posts($argsThumb);
									if ($attachments) {
										foreach ($attachments as $attachment) {
											echo '<li><img src="'.wp_get_attachment_url($attachment->ID, 'full', false, false).'" alt="'.get_post_meta($attachment->ID, '_wp_attachment_image_alt', true).'" /></li>';
										}
									}?>
								</ul>
							</div>
						<?php endif?>
						<div class="project-post-content">
							<div class="project-text">
								<h1><?php the_title()?></h1>
								<?php the_content()?>
							</div>
							<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'Kappe' ), 'after' => '</div>' ) ); ?>
							<div class="comment-section">
								<?php if( 'open' == $post->comment_status) comments_template(); ?>
								<?php $test = false; if ($test) {comment_form();} ?>
							</div>
						</div>	
					</div>
				<?php endwhile; ?>	
				<?php if ($sidebar_pos == 'right' && !empty($sidebar)) :?>
					<aside class="sidebar-div <?php echo $sidebar_size?>" id="sidebar"><div class="sidebar-content"><?php dynamic_sidebar($sidebar); ?></div></aside>
				<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>