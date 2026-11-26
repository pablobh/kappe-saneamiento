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
<?php wp_footer()?>

</body>
</html>
