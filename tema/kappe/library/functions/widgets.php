<?php

/**** WIDGETS AREA ****/

/* ***************************************************** 
 * Plugin Name: Kappe Social widget
 * Description: Display social links.
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
class alc_social_widget extends WP_Widget {

	// Widget setup.
	function __construct() {

		// Widget settings.
		$widget_ops = array(
			'classname' => 'widget_alc_social',
			'description' => __('Display social links', 'kappe')
		);

		// Widget control settings.
		$control_ops = array(
			'id_base' => 'alc-social-widget'
		);

		// Create the widget.
		parent::__construct('alc-social-widget', __('Kappe - Social links', 'kappe') , $widget_ops, $control_ops);
	}

	// Display the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$social_target = apply_filters('social_target', $instance['social_target']);
		$bitbucket = apply_filters('bitbucket', $instance['bitbucket']);
		$dribbble = apply_filters('dribbble', $instance['dribbble']);
		$facebook = apply_filters('facebook', $instance['facebook']);
		$flickr = apply_filters('flickr', $instance['flickr']);
		$github = apply_filters('github', $instance['github']);
		$google = apply_filters('google', $instance['google']);
		$instagram = apply_filters('instagram', $instance['instagram']);
		$linkedin = apply_filters('linkedin', $instance['linkedin']);
		$pinterest = apply_filters('pinterest', $instance['pinterest']);
		$skype = apply_filters('skype', $instance['skype']);
		$twitter = apply_filters('twitter', $instance['twitter']);
		$youtube = apply_filters('youtube', $instance['youtube']);
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		
		$socials = array(
			'bitbucket' => $bitbucket, 
			'dribbble' => $dribbble, 
			'facebook' => $facebook, 
			'flickr' => $flickr, 
			'github' => $github, 
			'google-plus' => $google, 
			'instagram' => $instagram, 
			'linkedin' => $linkedin, 
			'pinterest' => $pinterest, 
			'skype' => $skype, 
			'twitter' => $twitter, 
			'youtube' => $youtube
		);
		$social_target = ($social_target === '0') ? '' : ' target="_blank"';
		$fullmarkup = array();
		foreach ($socials as $name => $link)
		{
			if ($link !== '')
			{
				$fullmarkup[] = '<li><a href="'.$link.'" class="'.$name.'"'.$social_target.'><i class="fa fa-'.$name.'"></i></a>';
			}
		}
		echo '<ul class="social-icons">'.implode( "\n", $fullmarkup ).'</ul>'.$after_widget;
		
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['social_target'] = strip_tags($new_instance['social_target']);
		$instance['bitbucket'] = strip_tags($new_instance['bitbucket']);
		$instance['dribbble'] = strip_tags($new_instance['dribbble']);
		$instance['facebook'] = strip_tags($new_instance['facebook']);
		$instance['flickr'] = strip_tags($new_instance['flickr']);
		$instance['github'] = strip_tags($new_instance['github']);
		$instance['google'] = strip_tags($new_instance['google']);
		$instance['instagram'] = strip_tags($new_instance['instagram']);
		$instance['linkedin'] = strip_tags($new_instance['linkedin']);
		$instance['pinterest'] = strip_tags($new_instance['pinterest']);
		$instance['skype'] = strip_tags($new_instance['skype']);
		$instance['twitter'] = strip_tags($new_instance['twitter']);
		$instance['youtube'] = strip_tags($new_instance['youtube']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
		'title' => '',
		'social_target' => '',
		'bitbucket' => '',
		'dribbble' => '',
		'facebook' => '',
		'flickr' => '',
		'github' => '',
		'google' => '',
		'instagram' => '',
		'linkedin' => '',
		'pinterest' => '',
		'skype' => '',
		'twitter' => '',
		'youtube' => '',
		);
		
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'kappe'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('social_target'); ?>"><?php _e('Target window:', 'kappe'); ?></label>
			<select id="<?php echo $this->get_field_id('social_target'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" class="widefat">
				<option value="0" <?php if( $instance['social_target'] == 0):?>selected="selected"<?php endif?>>Same window</option>
				<option value="1" <?php if( $instance['social_target'] == 1):?>selected="selected"<?php endif?>>New window</option>
			</select>
		</p>
        
		<p>
			<label for="<?php echo $this->get_field_id('bitbucket'); ?>"><?php _e('Bitbucket:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('bitbucket'); ?>" type="text" name="<?php echo $this->get_field_name('bitbucket'); ?>" value="<?php echo $instance['bitbucket']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('dribble'); ?>"><?php _e('Dribbble:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('dribbble'); ?>" type="text" name="<?php echo $this->get_field_name('dribbble'); ?>" value="<?php echo $instance['dribbble']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('facebook'); ?>" type="text" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $instance['facebook']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('flickr'); ?>"><?php _e('Flickr:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('flickr'); ?>" type="text" name="<?php echo $this->get_field_name('flickr'); ?>" value="<?php echo $instance['flickr']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('github'); ?>"><?php _e('Github:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('github'); ?>" type="text" name="<?php echo $this->get_field_name('github'); ?>" value="<?php echo $instance['github']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('google'); ?>"><?php _e('Google+:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('google'); ?>" type="text" name="<?php echo $this->get_field_name('google'); ?>" value="<?php echo $instance['google']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('instagram'); ?>"><?php _e('Instagram:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('instagram'); ?>" type="text" name="<?php echo $this->get_field_name('instagram'); ?>" value="<?php echo $instance['instagram']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('LinkedIn:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('linkedin'); ?>" type="text" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo $instance['linkedin']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('pinterest'); ?>"><?php _e('Pinterest:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('pinterest'); ?>" type="text" name="<?php echo $this->get_field_name('pinterest'); ?>" value="<?php echo $instance['pinterest']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('skype'); ?>"><?php _e('Skype:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('skype'); ?>" type="text" name="<?php echo $this->get_field_name('skype'); ?>" value="<?php echo $instance['skype']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('twitter'); ?>" type="text" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $instance['twitter']; ?>" class="widefat" />
    	</p>
		<p>
			<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('Youtube:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('youtube'); ?>" type="text" name="<?php echo $this->get_field_name('youtube'); ?>" value="<?php echo $instance['youtube']; ?>" class="widefat" />
    	</p>
	<?php
	}
}

register_widget('alc_social_widget');

/* ***************************************************** 
 * Plugin Name: Kappe Last Works
 * Description: Retrieve and display latest works (Portfolio).
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
class alc_works_widget extends WP_Widget {

	// Widget setup.
	function __construct() {

		// Widget settings.
		$widget_ops = array(
			'classname' => 'widget_alc_works post-widget',
			'description' => __('Display latest works (Portoflio)', 'kappe')
		);

		// Create the widget.
		parent::__construct('alc-works-widget', __('Kappe - Latest Works', 'kappe') , $widget_ops);
	}

	// Display the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['post_title']);
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		$post_count = $instance['post_count'];
		$featured = $instance['featured'];
		
		$args = array('post_type' => 'portfolio', 'taxonomy'=> 'portfolio_category', 'posts_per_page' => $post_count);
		if ($featured)
		{
			$args['meta_key'] = '_portfolio_featured'; 
			$args['meta_value'] = '1';
		}
		$loop = new WP_Query($args);?>
		<div class="project-gallery">
			<ul>
				<?php while ( $loop->have_posts() ) : $loop->the_post();
					$thumbnail = get_the_post_thumbnail( $loop->post->ID, 'blog-thumb', array('class'=>'media-object') );
					?>
					<li>
						<a href="<?php the_permalink()?>">
							<?php if(!empty($thumbnail)):?>
								<?php echo $thumbnail ?>
							<?php else:?>
								<img src = "http://placehold.it/78x78" alt="<?php _e('No Image', 'kappe')?>" />
							<?php endif?>
						</a>
					</li>
				<?php endwhile;?>
				<?php wp_reset_query()?>
			</ul>
		</div>
		<?php echo $after_widget; 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['post_title'] = strip_tags($new_instance['post_title']);
		$instance['post_count'] = strip_tags($new_instance['post_count']);
		$instance['featured'] = strip_tags($new_instance['featured']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'post_title' => 'Recent works',
			'post_count' => '9',
			'featured' => '0',
		);
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('post_title'); ?>"><?php _e('Title', 'kappe'); ?></label>
			<input id="<?php echo $this->get_field_id('post_title'); ?>" type="text" name="<?php echo $this->get_field_name('post_title'); ?>" value="<?php echo $instance['post_title']; ?>" class="widefat" />
		</p>
        
         <p>
			<label for="<?php echo $this->get_field_id('featured'); ?>"><?php _e('Show only featured posts', 'kappe'); ?></label> 
			<select id="<?php echo $this->get_field_id('featured'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" class="widefat">
				<option value="0" <?php if( $instance['featured'] == 0):?>selected="selected"<?php endif?>>No</option> 
				<option value="1" <?php if( $instance['featured'] == 1):?>selected="selected"<?php endif?>>Yes</option> 
			</select>
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of Posts to show', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('post_count'); ?>" type="text" name="<?php echo $this->get_field_name('post_count'); ?>" value="<?php echo $instance['post_count']; ?>" class="widefat" />
		</p>
	<?php
	}
}

register_widget('alc_works_widget');



/* ***************************************************** 
 * Plugin Name: Kappe Flickr
 * Description: Retrieve and display photos from Flickr.
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
class alc_flickr_widget extends WP_Widget {

	// Widget setup.
	function __construct() {

		// Widget settings.
		$widget_ops = array(
			'classname' => 'widget_alc_flickr',
			'description' => __('Display images from flickr', 'kappe')
		);

		// Widget control settings.
		$control_ops = array(
			'id_base' => 'alc-flickr-widget'
		);

		// Create the widget.
		parent::__construct('alc-flickr-widget', __('Kappe - Flickr images', 'kappe') , $widget_ops, $control_ops);
	}

	// Display the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$id = $instance['flickr_id'];
		$nr = ($instance['flickr_nr'] != '') ? $nr = $instance['flickr_nr'] : $nr = 16;
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		echo "
		<script type=\"text/javascript\">
			jQuery(document).ready(function(){
				jQuery('ul#basicuse').jflickrfeed({
					limit: ".$nr.",
					qstrings: {
						id: '".$id."'
					},
					itemTemplate: '<li><a href=\"http://www.flickr.com/photos/".$id."\"><img src=\"{{image_s}}\" alt=\"{{title}}\" /></a></li>'
				});
			});
		</script>";
		echo '<div class="flickr-widget"><ul id="basicuse" class="flickr-list"></ul></div>'.$after_widget;
		
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['flickr_id'] = strip_tags($new_instance['flickr_id']);
		$instance['flickr_nr'] = strip_tags($new_instance['flickr_nr']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
		'title' => 'Latest From Flickr',
		'flickr_nr' => '16',
		'flickr_id' => '47445714@N03'
		);
		
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'kappe'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
        
		<p>
			<label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Flickr ID:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('flickr_id'); ?>" type="text" name="<?php echo $this->get_field_name('flickr_id'); ?>" value="<?php echo $instance['flickr_id']; ?>" class="widefat" />
            <small style="line-height:12px;"><a href="http://www.idgettr.com">Find your Flickr user or group id</a></small>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('flickr_nr'); ?>"><?php _e('Number of photos:', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('flickr_nr'); ?>" type="text" name="<?php echo $this->get_field_name('flickr_nr'); ?>" value="<?php echo $instance['flickr_nr']; ?>" class="widefat" />
		</p>
	<?php
	}
}

register_widget('alc_flickr_widget');


/* ***************************************************** 
 * Plugin Name: Last Tweets
 * Description: Displays Latest Tweets.
 * Version: 1.1
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */

add_action( 'widgets_init', 'latest_tweet_widget' );
function latest_tweet_widget() {
	register_widget( 'Latest_Tweets' );
}
class Latest_Tweets extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'twitter-widget'  );
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'latest-tweets-widget' );
		parent::__construct( 'latest-tweets-widget','Kappe - Latest Tweets', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );
		$no_of_tweets = $instance['no_of_tweets'];
		$transName = 'list_tweets';
	    $cacheTime = 20;
		$twitter_username 		= $instance['twitter_username'];
		$consumer_key 			= $instance['consumer_key'];
		$consumer_secret		= $instance['consumer_secret'];
		$access_token 			= $instance['access_token'];
		$access_token_secret 	= $instance['access_token_secret'];
		
	if( !empty($twitter_username) && !empty($consumer_key) && !empty($consumer_secret) && !empty($access_token) && !empty($access_token_secret)  ){
	    if(false === ($twitterData = get_transient($transName) ) ){
		
			$twitterConnection = new TwitterOAuth( $consumer_key , $consumer_secret , $access_token , $access_token_secret	);
			$twitterData = $twitterConnection->get(
					  'statuses/user_timeline',
					  array(
					    'screen_name'     => $twitter_username ,
					    'count'           => $no_of_tweets
						)
					);
			if($twitterConnection->http_code != 200)
			{
				$twitterData = get_transient($transName);
			}
	        // Save our new transient.
	        set_transient($transName, $twitterData, 60 * $cacheTime);
	    }
		/* Before widget (defined by themes). */
		echo $before_widget;
	
	
			echo $before_title; ?>
			<?php echo $title ; ?>
		<?php echo $after_title; 

            	if( !empty($twitterData) && is_array($twitterData)  && !isset($twitterData['error'])){
            		$i=0;
					$hyperlinks = true;
					$encode_utf8 = true;
					$twitter_users = true;
					$update = true;
					echo '
<div id="twitter-widget">
<ul class="twitter_update_list tweets">';
		            foreach($twitterData as $item){
		                    $msg = $item->text;
		                    $permalink = 'http://twitter.com/#!/'. $twitter_username .'/status/'. $item->id_str;
		                    if($encode_utf8) $msg = utf8_encode($msg);
		                    $link = $permalink;
		                     echo '
<li class="twitter-item"><i class="fa fa-twitter"></i>';
		                      if ($hyperlinks) {    $msg = $this->hyperlinks($msg); }
		                      if ($twitter_users)  { $msg = $this->twitter_users($msg); }
		                      echo $msg;
		                    if($update) {
		                      $time = strtotime($item->created_at);
		                      if ( ( abs( time() - $time) ) < 86400 )
		                        $h_time = sprintf( __('%s ago', 'kappe'), human_time_diff( $time ) );
		                      else
		                        $h_time = date(__('Y/m/d', 'kappe'), $time);
		                      echo sprintf( __('%s', 'twitter-for-wordpress', 'kappe'),' <span class="twitter-timestamp"><abbr title="' . date(__('Y/m/d H:i:s', 'kappe'), $time) . '">' . $h_time . '</abbr></span>' );
		                     }
		                    echo '</li>
';
		                    $i++;
		                    if ( $i >= $no_of_tweets ) break;
		            }
					echo '</ul> </div>
';
            	}
				else
				{ 
					echo _e('Sorry , Twitter seems down or responds slowly.', 'kappe'); 
				}
            ?>
		<?php
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	else{
		echo $before_widget;
		echo $before_title; ?>
			<a href="http://twitter.com/<?php echo $twitter_username?>" class="twitter-title"><?php echo $title ; ?></a>
		<?php echo $after_title._e('You need to Setup Twitter API OAuth settings first', 'kappe');
		echo $after_widget;
	}
}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['no_of_tweets'] = strip_tags( $new_instance['no_of_tweets'] );
		
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username'] );
		$instance['consumer_key'] = strip_tags( $new_instance['consumer_key'] );
		$instance['consumer_secret'] = strip_tags( $new_instance['consumer_secret'] );
		$instance['access_token'] = strip_tags( $new_instance['access_token'] );
		$instance['access_token_secret'] = strip_tags( $new_instance['access_token_secret'] );
		//$instance['accounts'] = strip_tags( $new_instance['accounts'] );
		//$instance['replies'] = strip_tags( $new_instance['replies'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' =>__('@Follow Me' , 'kappe') , 'no_of_tweets' => '5', 'twitter_username'=>'', 'consumer_key' => '', 'consumer_secret' => '', 'access_token' => '', 'access_token_secret' => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>"><?php _e('Username', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" value="<?php echo $instance['twitter_username']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>"><?php _e('Consumer Key', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" value="<?php echo $instance['consumer_key']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>"><?php _e('Consumer Secret', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" value="<?php echo $instance['consumer_secret']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token' ); ?>"><?php _e('Access Token', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'access_token' ); ?>" name="<?php echo $this->get_field_name( 'access_token' ); ?>" value="<?php echo $instance['access_token']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token_secret' ); ?>"><?php _e('Access Token Secret', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'access_token_secret' ); ?>" value="<?php echo $instance['access_token_secret']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'no_of_tweets' ); ?>"><?php _e('Number of Tweets to show', 'kappe')?> : </label>
			<input id="<?php echo $this->get_field_id( 'no_of_tweets' ); ?>" name="<?php echo $this->get_field_name( 'no_of_tweets' ); ?>" value="<?php echo $instance['no_of_tweets']; ?>" type="text" size="3" />
		</p>
	<?php
	}
	
		/**
	 * Find links and create the hyperlinks
	 */
	private function hyperlinks($text) {
	    $text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);
	    $text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);
	    // match name@address
	    $text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);
	        //mach #trendingtopics. Props to Michael Voigt
	    $text = preg_replace('/([\.|\,|\:|\?|\?|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text);
	    return $text;
	}
	/**
	 * Find twitter usernames and link to them
	 */
	private function twitter_users($text) {
	       $text = preg_replace('/([\.|\,|\:|\?|\?|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);
	       return $text;
	}
	
}


/* ***************************************************** 
 * Plugin Name: Kappe Blog Posts
 * Description: Retrieve and display latest blog posts.
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
class alc_blogposts_widget extends WP_Widget {

	// Widget setup.
	function __construct() {

		// Widget settings.
		$widget_ops = array(
			'classname' => 'widget_alc_blogposts',
			'description' => __('Display latest blog posts', 'kappe')
		);

		// Create the widget.
		parent::__construct('alc-blogposts-widget', __('Kappe Blog Posts', 'kappe') , $widget_ops);
	}

	// Display the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['post_title']);
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		$post_count = $instance['post_count'];
		$post_category = $instance['post_category'];
		$post_thumbs = $instance['post_thumbs'];
		global $post;
		$args = array( 'numberposts' => $post_count);
		if (!empty($post_category))
		$args['category'] = $post_category;
		$myposts = get_posts( $args );
		
		if ($myposts):
			foreach( $myposts as $post ) :	setup_postdata($post);  ?>          
				<?php if ($post_thumbs == 1):?>
				<div class="media">
					<a href="<?php the_permalink()?>" class="pull-left">
						<?php if(has_post_thumbnail()):?>
							<?php the_post_thumbnail('blog-thumb', array('class'=>'media-object') ); ?>
						<?php else:?>
							<img src = "http://placehold.it/59x59" alt="No Image" />
						<?php endif?>
					</a>
					<div class="media-body">
						<h5 class="media-heading"><a href="<?php the_permalink()?>"><?php the_title()?></a></h5> 
						<small><?php the_date()?></small>
					</div>
				</div>
				<?php else:?>
				<ul class="post-widget">
					<li>
						<h4 class="media-heading"><a href="<?php the_permalink()?>"><?php the_title()?></a></h4> 
						<small><?php the_date()?></small>
					</li>
				</ul>
				<?php endif?>
			<?php endforeach; ?>
			<?php wp_reset_query()?>
		<?php endif; ?>
        <?php echo $after_widget; 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['post_title'] = strip_tags($new_instance['post_title']);
		$instance['post_count'] = strip_tags($new_instance['post_count']);
		$instance['post_thumbs'] = strip_tags($new_instance['post_thumbs']);
		$instance['post_category'] = strip_tags($new_instance['post_category']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'post_title' => 'Latest from the blog',
			'post_count' => '2',
			'post_category' => '',
			'post_thumbs' => ''
		);
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('post_title'); ?>"><?php _e('Title', 'kappe'); ?></label>
			<input id="<?php echo $this->get_field_id('post_title'); ?>" type="text" name="<?php echo $this->get_field_name('post_title'); ?>" value="<?php echo $instance['post_title']; ?>" class="widefat" />
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of Posts to show', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('post_count'); ?>" type="text" name="<?php echo $this->get_field_name('post_count'); ?>" value="<?php echo $instance['post_count']; ?>" class="widefat" />
		</p>
        
         <p>
			<label for="<?php echo $this->get_field_id('post_category'); ?>"><?php _e('Category (Leave Blank to show from all categories)', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('post_category'); ?>" type="text" name="<?php echo $this->get_field_name('post_category'); ?>" value="<?php echo $instance['post_category']; ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_thumbs'); ?>"><?php _e('Show thumbnails', 'kappe'); ?></label>
			<select id="<?php echo $this->get_field_id('post_thumbs'); ?>" name="<?php echo $this->get_field_name('post_thumbs'); ?>" class="widefat">
				<option value="0" <?php if( $instance['post_thumbs'] == 0):?>selected="selected"<?php endif?>>No</option> 
				<option value="1" <?php if( $instance['post_thumbs'] == 1):?>selected="selected"<?php endif?>>Yes</option> 
			</select>
		</p>
	<?php
	}
}

register_widget('alc_blogposts_widget');




/* ***************************************************** 
 * Plugin Name: Kappe Contact Widget
 * Description: Display contact widget on footer.
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
/**
 * Contact Form Widget Class
 */
class alc_Contact_Form extends WP_Widget {
	
	function __construct() {
		$widget_ops = array('classname' => 'alc_contact_form_entries', 'description' => __("Contact widget", 'kappe') );
		parent::__construct('alc_Contact_Form', __('Kappe - Contact Form', 'kappe'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Contact Us', 'kappe') : $instance['title'], $instance);
		$email = apply_filters('widget_title', empty($instance['email']) ? __('', 'kappe') : $instance['email'], $instance);
		$success = apply_filters('widget_title', empty($instance['success']) ? __('Thank you, e-mail sent.', 'kappe') : $instance['success'], $instance);
		
		echo $before_widget;
		
		if ( $title ) echo $before_title . $title . $after_title;
        ?>                
			<form action="#" method="post" id="contactFormWidget">
                            <div class="row">
					<div class="col-md-6">
						<input type="text" name="wname" id="wname" value="" size="22" placeholder="<?php _e('Name', 'kappe')?>"/>
					</div>
					<div class="col-md-6">
						<input type="text" name="wemail" id="wemail" value="" size="22" placeholder="<?php _e('Email', 'kappe')?>" />
					</div>
                             </div>
                            <div class="row">	
					<div class="col-md-12">
						<textarea name="wmessage" id="wmessage"  placeholder="<?php _e('Message', 'kappe')?>"></textarea>
					
						<input type="submit" id="wformsend" value="<?php _e('Send', 'kappe')?>" class="btn" name="wsend" />
					</div>
                            </div>
				<div class="loading"></div>
				<div>
					<input type="hidden" name="wcontactemail" id="wcontactemail" value="<?php echo $email?>" />
					<input type="hidden" value="<?php echo home_url()?>" id="wcontactwebsite" name="wcontactwebsite" />
					<input type="hidden" name="wcontacturl" id="wcontacturl" value="<?php echo get_template_directory_uri()?>/library/sendmail.php" />
				</div>
				<div class="clear"></div>
				<div class="widgeterror"></div>
				<div class="widgetinfo"><i class="fa fa-envelope"></i><?php echo $success?></div>
			</form>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['email'] = strip_tags($new_instance['email']);
		$instance['success'] = strip_tags($new_instance['success']);
		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$email = isset($instance['email']) ? esc_attr($instance['email']) : '';
		$success = isset($instance['success']) ? esc_attr($instance['success']) : '';
	
              
	?>
		<div>
        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:<br />', 'kappe'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		</div>
        <div>
        	<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email Address:<br />', 'kappe'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo $email; ?>" /></label></p>
		</div>
        <div>
        	<label for="<?php echo $this->get_field_id('success'); ?>"><?php _e('Success Message:<br />', 'kappe'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('success'); ?>" name="<?php echo $this->get_field_name('success'); ?>" type="text" value="<?php echo $success; ?>" /></label></p>
		</div>
		<div style="clear:both"></div>
<?php
	}
}

register_widget('alc_Contact_Form');
/* ***************************************************** 
 * Plugin Name: Kappe Post Info
 * Description: Retrieve and display latest blog posts.
 * Version: 1.0
 * Author: Weblusive
 * Author URI: http://www.weblusive.com
 * ************************************************** */
class alc_postinfo_widget extends WP_Widget {

	// Widget setup.
	function __construct() {

		// Widget settings.
		$widget_ops = array(
			'classname' => 'widget_alc_postinfo',
			'description' => __('Display post info', 'kappe')
		);

		// Create the widget.
		parent::__construct('alc-postinfo-widget', __('Kappe Post Info', 'kappe') , $widget_ops);
	}

	// Display the widget on the screen.
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['post_title']);
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		$post_author = $instance['post_author'];
		$post_date = $instance['post_date'];
		$post_website = $instance['post_website'];
		global $post;
		
		?>
			
		<div class="post-info">
			<ul>
				<?php if ($post_author == 1):?>
				<li><span><i class="fa fa-user"></i></span><a href="#"><?php the_author(); ?></a></li>
				<?php endif; ?>
				<?php if ($post_date == 1):?>
				<li><span><i class="fa fa-calendar"></i></span><a href="#"><?php the_date()?></a></li>
				<?php endif; ?>
				<?php if (!empty($post_website) && $post_website !== ''):?>
				<li><span><i class="fa fa-link"></i></span><a href="#"><?php echo $post_website; ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
			
        <?php echo $after_widget; 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['post_title'] = strip_tags($new_instance['post_title']);
		$instance['post_author'] = strip_tags($new_instance['post_author']);
		$instance['post_date'] = strip_tags($new_instance['post_date']);
		$instance['post_website'] = strip_tags($new_instance['post_website']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'post_title' => 'Post info',
                        'post_author'=>'',
                        'post_date'=>'',
			'post_website' => 'http://'
		);
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('post_title'); ?>"><?php _e('Title', 'kappe'); ?></label>
			<input id="<?php echo $this->get_field_id('post_title'); ?>" type="text" name="<?php echo $this->get_field_name('post_title'); ?>" value="<?php echo $instance['post_title']; ?>" class="widefat" />
		</p>
        
                <p>
			<label for="<?php echo $this->get_field_id('post_author'); ?>"><?php _e('Show author', 'kappe'); ?></label>
			<select id="<?php echo $this->get_field_id('post_author'); ?>" name="<?php echo $this->get_field_name('post_author'); ?>" class="widefat">
				<option value="0" <?php if( $instance['post_author'] == 0):?>selected="selected"<?php endif?>>No</option> 
				<option value="1" <?php if( $instance['post_author'] == 1):?>selected="selected"<?php endif?>>Yes</option> 
			</select>
		</p>
                <p>
			<label for="<?php echo $this->get_field_id('post_date'); ?>"><?php _e('Show Date', 'kappe'); ?></label>
			<select id="<?php echo $this->get_field_id('post_date'); ?>" name="<?php echo $this->get_field_name('post_date'); ?>" class="widefat">
				<option value="0" <?php if( $instance['post_date'] == 0):?>selected="selected"<?php endif?>>No</option> 
				<option value="1" <?php if( $instance['post_date'] == 1):?>selected="selected"<?php endif?>>Yes</option> 
			</select>
		</p>
        
                <p>
			<label for="<?php echo $this->get_field_id('post_website'); ?>"><?php _e('Alternative website', 'kappe'); ?></label> 
			<input id="<?php echo $this->get_field_id('post_website'); ?>" type="text" name="<?php echo $this->get_field_name('post_website'); ?>" value="<?php echo $instance['post_website']; ?>" class="widefat" />
		</p>
		
		
	<?php
	}
}

register_widget('alc_postinfo_widget');
?>