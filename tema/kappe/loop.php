<?php /*** The loop that displays posts.***/

//$get_meta = get_post_custom($post->ID);
$showdate = weblusive_get_option('blog_show_date');
$showcomments = weblusive_get_option('blog_show_comments');
$showcategory = weblusive_get_option('blog_show_category');
$showrmtext = weblusive_get_option('blog_show_rmtext');
?>

<?php if ( ! have_posts() ) : ?>
	<style type="text/css">#load-more{display:none}</style>
<?php else:?>
	<?php while ( have_posts() ) : the_post();
		$get_meta = get_post_custom($post->ID);

		$mediatype = isset($get_meta["_blog_mediatype"]) ? $get_meta["_blog_mediatype"][0] : 'image';
		$videoId = isset($get_meta["_blog_video"]) ? $get_meta["_blog_video"][0] : '';
		$autoplay =  isset($get_meta["_blog_videoap"]) ? $get_meta["_blog_videoap"][0] : '0';
		$thumbnail = get_the_post_thumbnail($post->ID, 'blog-list');
		?>
        <!-------------POST FORMAT IMAGE-------------------->
        <?php if ( ( function_exists( 'get_post_format' ) && 'image' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post photo-post'); ?>>
            <div class="inner-post">
                <?php the_post_thumbnail('blog-list'); ?>
                <?php
                    $full_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full', false);
                    $link = $full_image[0];
                ?>
                <a  class="zoom" href="<?php echo $link ?>"><?php  _e( '+', 'kappe' ); ?></a>
            </div>
        </div>
        <!-------------POST FORMAT LINK-------------------->
        <?php elseif( ( function_exists( 'get_post_format' ) && 'link' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post link-post'); ?>>
            <div class="inner-post">
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <!-------------POST FORMAT ASIDE-------------------->
        <?php elseif( ( function_exists( 'get_post_format' ) && 'aside' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post aside-post'); ?>>
            <div class="inner-post">
                <div class="post-content">
                    <h2><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <!-------------POST FORMAT QUOTE-------------------->
        <?php elseif( ( function_exists( 'get_post_format' ) && 'quote' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post quote-post'); ?>>
            <div class="inner-post">
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <!-------------POST FORMAT VIDEO-------------------->
        <?php elseif( ( function_exists( 'get_post_format' ) && 'video' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post video-post'); ?>>
            <div class="inner-post">
                <?php if ( $mediatype == "youtube" && $videoId):?>
                    <iframe src="https://www.youtube-nocookie.com/embed/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="videoembed" allowfullscreen></iframe>
                <?php elseif ( $mediatype == "vimeo" && $videoId):?>
                    <iframe src="https://player.vimeo.com/video/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="videoembed"></iframe>
				<?php elseif ( $mediatype == "dailymotion" && $videoId):?>
                    <iframe  src="https://www.dailymotion.com/embed/video/<?php echo $videoId?>?autoplay=<?php echo $autoplay?>" class="videoembed"></iframe>
                <?php endif;?>
                <div class="post-content">
                    <h2><a href="<?php the_permalink()?>"><?php the_title()?></a></h2>
                    <?php echo do_shortcode(get_the_excerpt()); ?>
                </div>
				<ul class="post-tags">
                     <?php if($showcategory): ?>
                        <li><a class="autor" href="#"><i class="fa fa-user"></i><?php the_author() ?></a></li>
                    <?php endif?>
                     <?php if( 'open' == $post->comment_status && $showcomments) : ?>
                     <li>
                         <i class="fa fa-comment"></i>
                         <?php comments_popup_link( __( '0 Comments', 'kappe' ), __( '1 Comment', 'kappe' ), __( '% Comments', 'kappe' )); ?>
                     </li>
                     <?php endif?>
                    <?php if($showdate): ?>
                        <li><a class="date" href="#"><i class="fa fa-calendar"></i><?php echo get_the_time('M d, Y'); ?></a></li>
					<?php endif; ?>
				</ul>
            </div>
        </div>
         <!-------------POST FORMAT GALLERY-------------------->
        <?php elseif( ( function_exists( 'get_post_format' ) && 'gallery' == get_post_format( $post->ID ) )  ) : ?>
        <div <?php post_class('blog-post gallery-post'); ?>>
            <div class="inner-post">
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
				?>
                <div class="flexslider">
                    <ul class="slides">
						<?php
						$attachments = get_posts($argsThumb);
						if ($attachments) {
                            foreach ($attachments as $attachment) {
                                echo '<li><img src="'.wp_get_attachment_url($attachment->ID, 'full', false, false).'" alt="'.get_the_title($post->ID).'"></li>';
                            }
						}
						?>
                    </ul>
                </div>
                <div class="post-content">
                    <h2><a href="<?php the_permalink()?>"><?php the_title()?></a></h2>
                    <?php echo do_shortcode(get_the_excerpt()); ?>
                </div>
				<ul class="post-tags">
                     <?php if($showcategory): ?>
                        <li><a class="autor" href="#"><i class="fa fa-user"></i><?php the_author() ?></a></li>
                    <?php endif?>
                     <?php if( 'open' == $post->comment_status && $showcomments) : ?>
                     <li>
                         <i class="fa fa-comment"></i>
                         <?php comments_popup_link( __( '0 Comments', 'kappe' ), __( '1 Comment', 'kappe' ), __( '% Comments', 'kappe' )); ?>
                     </li>
                     <?php endif?>
                    <?php if($showdate): ?>
                        <li><a class="date" href="#"><i class="fa fa-calendar"></i><?php echo get_the_time('M d, Y'); ?></a></li>
					<?php endif; ?>
				</ul>
            </div>
        </div>
        <?php else : ?>
        <div <?php post_class('blog-post image-post'); ?>>
            <div class="inner-post">
               <?php $thumbnail = get_the_post_thumbnail();
				if(!empty($thumbnail)) : ?>
					<?php the_post_thumbnail('blog-list'); ?>
                <?php else : ?>
					<img src = "http://placehold.it/300x193" alt="<?php echo esc_attr__( 'No image', 'kappe' ) ?>" />
				<?php endif; ?>
                <div class="post-content">
                    <h2><a href="<?php the_permalink()?>"><?php the_title()?></a></h2>
                    <?php echo do_shortcode(get_the_excerpt()); ?>
                </div>
				<ul class="post-tags">
                     <?php if($showcategory): ?>
                        <li><a class="autor" href="#"><i class="fa fa-user"></i><?php the_author() ?></a></li>
                    <?php endif?>
                     <?php if( 'open' == $post->comment_status && $showcomments) : ?>
                     <li>
                         <i class="fa fa-comment"></i>
                         <?php comments_popup_link( __( '0 Comments', 'kappe' ), __( '1 Comment', 'kappe' ), __( '% Comments', 'kappe' )); ?>
                     </li>
                     <?php endif?>
                    <?php if($showdate): ?>
                        <li><a class="date" href="#"><i class="fa fa-calendar"></i><?php echo get_the_time('M d, Y'); ?></a></li>
					<?php endif; ?>
				</ul>
            </div>
        </div>
        <?php endif; ?>
	<?php endwhile;?>
<?php endif; ?>
