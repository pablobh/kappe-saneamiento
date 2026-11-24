<?php get_header();?>
<!--
<div class="banner">
    <div class="inner-banner">
            <div class="note"><?php /*_e('Page not found', 'Kappe')?></div>
            <div class="site-map">
                <?php _e('You are here:', 'Kappe');?>
                <?php if(class_exists('the_breadcrumb')){ $albc = new the_breadcrumb; }*/ ?>
            </div>
	<div class="clear"></div>
    </div>
</div>
-->
<div id="content">
    <div class="inner-content">
        <div class="about-page">
            <div class="about-box">
                <div class="full-page">
                    <div class="notfound"></div> 
                    <p class="lost"></p>
                    <div class="col-md-8 col-md-offset-2 notfound_text">
                        <p class="notfound_description">
                            <?php _e('The page you are looking for seems to be missing.Go back, or return to yourcompany.com to choose a new direction.Please report any broken links to our team.', 'Kappe')?>
                        </p>
                        <a class="btn  btn-large" href="javascript: history.go(-1)"><i class="fa fa-undo"></i><?php _e('Return to previous page', 'Kappe')?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
