<?php /* Template Name: Contact Form */ 

get_header();

$get_meta = get_post_custom($post->ID);
$size = weblusive_get_option('sidebar_size');

$sidebar_pos = isset( $get_meta['_weblusive_sidebar_pos'][0]) ? $get_meta["_weblusive_sidebar_pos"][0] : 'full';
$sidebar_size = (isset($size) && !empty ($size)) ? $size : 'col-md-6';
$layout_size = ($sidebar_pos === 'full') ? 'col-md-12' : 'col-md-6';
if ($sidebar_size === 'col-md-3' && $sidebar_pos !== 'full') $layout_size = 'col-md-9';
$options = array(
		weblusive_get_option('contact_error'), 
		weblusive_get_option('contact_success'),
		weblusive_get_option('contact_subject'), 
		weblusive_get_option('contact_email'), 		
	);
$address = weblusive_get_option('contact_address'); 
if (!empty($address)):?>
<script type="text/javascript">   
jQuery(function(){
	jQuery('#map').gmap3({
		action: 'addMarker',
		address: "<?php echo htmlspecialchars($address)?>",
		
		marker:{
			options:{
				icon : new google.maps.MarkerImage('<?php echo get_template_directory_uri()?>/images/mapmarker.png')
			}
		},
		
		map:{
			center: true,
			zoom: 14,
			},
		},
		{action: 'setOptions', args:[{scrollwheel:true}]}		
		);	  
	});
</script> 
<?php endif?> 

<div id="content" class="contact-page">
	<?php if (!empty($address)):?>
		<div id="map" class="gmap3 map_location map1"></div>
	<?php endif?>
	<?php get_template_part( 'library/includes/page-head' ); ?>
    <div class="inner-content">
		<div class="contact-page">
			<?php if ($sidebar_pos == 'left'):?><aside class="<?php echo $sidebar_size?> spart left-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>		
				<div class="contact-box">
					<?php the_content(); ?>
					<form method="POST" class="contactForm" id="contact-form">
						<h1><?php _e('Send us a message', 'Kappe'); ?></h1>
						<div id="status"></div>
						<div class="text-fields">
							<div id="error-name"></div>
							<div class="float-input">
								<input type="text" placeholder="<?php _e('Name', 'Kappe')?>" name="contactname" id="contactname" />
								<span><i class="fa fa-user"></i></span>
								<small class="errorarr"><?php if(isset($nameError) && $nameError != ''): ?><?php echo $nameError;?><?php endif;?></small>
							</div>
							<div id="error-mail"></div>
							<div class="float-input">
								<input type="text" placeholder="<?php _e('E-mail', 'Kappe')?>" name="contactemail" id="contactemail" />
								<span><i class="fa fa-envelope-o"></i></span>
								<small class="errorarr"><?php if(isset($emailError) && $emailError != ''): ?><?php echo $emailError;?><?php endif;?></small>
							</div>
							<div class="float-input">
								<input type="text" placeholder="<?php _e('Website', 'Kappe')?>" name="contactwebsite" id="contactwebsite" />
								<span><i class="fa fa-link"></i></span>
							</div>
						</div>
						<div id="error-text"></div>
						<div class="submit-area">
							<textarea cols="10" rows="15" name="contactmessage" id="contactmessage"  placeholder="<?php _e('Message', 'Kappe')?>" ></textarea>
							<small class="errorarr"><?php if(isset($messageError) && $messageError != ''): ?><?php echo $messageError;?><?php endif;?></small>
							<input type="submit" class="main-button" id="submit_contact" value="<?php _e('Send Now', 'Kappe')?>" name="send"  />
							<input type="hidden" name = "options" value="<?php echo implode('|', $options) ?>" />
						</div>
					</form>					
				</div>
			<?php endwhile; ?>	
			<?php if ($sidebar_pos == 'right'):?><aside class="<?php echo $sidebar_size?> spart right-side" id="sidebar"><?php get_sidebar(); ?></aside><?php endif; ?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
<!-- Contact form validation-->
jQuery(document).ready(function(){

  jQuery(".contactForm").validate({
	submitHandler: function() {
	
		var postvalues =  jQuery(".contactForm").serialize();
		
		jQuery.ajax
		 ({
		   type: "POST",
		  url: "<?php echo get_template_directory_uri()  ?>/contact-form.php",
		   data: postvalues,
		   success: function(response)
		   {
		 	 jQuery("#status").html(response).show('normal');
		     jQuery('#contactmessage, #contactemail, #contactname, #contactwebsite').val("");
		   }
		 });
		return false;
		
    },
	focusInvalid: true,
	focusCleanup: false,
	//errorLabelContainer: jQuery("#registerErrors"),
  	rules: 
	{
		contactname: {required: true},
		contactemail: {required: true, minlength: 6,maxlength: 50, email:true},
		contactmessage: {required: true, minlength: 6}
	},
	
	messages: 
	{
		contactname: {required: "<?php _e( 'Name is required', 'Kappe' ); ?>"},
		contactemail: {required: "<?php _e( 'E-mail is required', 'Kappe' ); ?>", email: "<?php _e( 'Please provide a valid e-mail', 'Kappe' ); ?>", minlength:"<?php _e( 'E-mail address should contain at least 6 characters', 'Kappe' ); ?>"},
		contactmessage: {required: "<?php _e( 'Message is required', 'Kappe' ); ?>"}
	},
        errorPlacement: function(error, element) 
	{
		 error.insertAfter(element);
	},
	invalidHandler: function()
	{
		//jQuery("body").animate({ scrollTop: 0 }, "slow");
	}
	
});
});
-->
</script>


<?php get_footer(); ?>