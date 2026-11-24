<?php
 
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
 
if ( post_password_required() ) : ?>
	<p class="nocomment"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'Kappe' ); ?></p>
	</div>
	<?php return;
endif;
?>
 
<?php if ( have_comments() ) : ?>
	<h1>
		<?php printf( _n( '%1$s Comment', '%1$s Comments', get_comments_number(), 'Kappe' ),number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );?>
	</h1>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div class="navigation">
			<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'Kappe' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'Kappe' ) ); ?></div>
		</div> <!-- .navigation -->
	<?php endif; ?>
	 
	<ul class="comment-tree">
		<?php wp_list_comments('callback=Kappe_comment'); ?>
	</ul>
<?php else : // this is displayed if there are no comments so far ?>
	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->
	<?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e( 'Comments are closed.', 'Kappe' ); ?></p>
	 <?php endif; ?>
<?php endif; ?>

<?php if ('open' == $post->comment_status) : ?>
	<div class="leave-comment msg-form">
		<h1><?php comment_form_title( 'Leave a Comment', 'Leave a Reply to %s' ); ?></h1>
		<div class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></div>
	 
		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p class="bottom20">
				You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> 
				to post a comment.
			</p>
		<?php else : ?> 
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="comment-form" id="contact-form2">				
                            <?php if ( $user_ID ) : ?>
                                <div class="row-fluid">  
                                    <p class="com_logined_text">
                                        <?php _e('Logged in as', 'Kappe')?>
					<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. 
					<a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php _e('Log out', 'Kappe')?> &raquo;</a>
                                    </p>
				</div>
                            <?php else : ?>
                                <div class="text-fields">
                                    <div class="float-input">
                                        <input type="text" name="author" id="author" placeholder="<?php _e('Name', 'Kappe')?>" title="<?php _e('Name', 'Kappe')?>" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
                                        <span><i class="fa fa-user"></i></span>
                                    </div>
                                    <div class="float-input">
                                        <input type="text" name="email" id="email" placeholder="<?php _e('Email', 'Kappe')?>" title="<?php _e('Email', 'Kappe')?>" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
                                        <span><i class="fa fa-envelope-o"></i></span>
                                    </div>
                                    <div class="float-input">
                                        <input type="text" name="website" id="website" placeholder="<?php _e('Website', 'Kappe')?>" title="<?php _e('Website', 'Kappe')?>" value="<?php echo isset($comment_author_website) ? $comment_author_website : '' ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
                                        <span><i class="fa fa-link"></i></span>
                                    </div>	
                                </div>	
                            <?php endif; ?>
                            <div class="<?php echo empty($user_ID) ? 'submit-area' : ''; ?>">
                                    <textarea name="comment" title="<?php _e('Comment', 'Kappe')?>" placeholder="<?php _e('Comment', 'Kappe')?>" id="comment"  tabindex="4"></textarea>
                                    <input type="submit" class="main-button" id="submit_contact" value="<?php _e('Add Comment', 'Kappe')?>">
                                </div>
                            <?php comment_id_fields(); ?>
                            <?php do_action('comment_form', $post->ID); ?>	
                        </form>
		<?php endif;  ?>
	</div>
<?php endif;  ?>
