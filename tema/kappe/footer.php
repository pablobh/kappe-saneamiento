<?php if(weblusive_get_option('layout_type')=='boxed'):?></div> <?php endif; ?><!--main content end -->
</div><!--container -->
<?php if(weblusive_get_option( 'info_box' )):?>
<div class="info-box">
    <a class="info-toggle" href="#"><i class="fa fa-info-circle"></i></a>
    <div class="info-content">
        <?php echo htmlspecialchars_decode(weblusive_get_option('info_box')); ?>
    </div>
</div>
<?php endif?>
<?php $noloader = weblusive_get_option( 'disable_loader' ); if(!$noloader):?>
<div class="preloader">
    <img alt="" src="<?php echo get_template_directory_uri().'/images/preloader.gif' ?>">
</div>
<?php endif?>
<?php wp_footer()?>
</body>
</html>
