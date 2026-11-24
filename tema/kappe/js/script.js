/*jshint jquery:true */
/*global $:true */

var $ = jQuery.noConflict();

$(document).ready(function($) {
	"use strict";
	try {
		var projectPost = $('.project-post');
		projectPost.each( function() { $(this).hoverdir(); } );
	} catch(err) {
		alert(err);
	}
	
$(function () {
   "use strict";
   if ($("#ajax-load-more").length) {
      var page = 1,
         $loading = true,
         $finished = false,
         $window = $(window),
         $el = $('#ajax-load-more'),
         $content = $('#ajax-load-more div.blog-box'),
         $path = $content.attr('data-path');

      if ($path === undefined) {
         $path = '/wp-content/themes/kappe';
      }
	  
	  var $button = 'Older Posts';
	  
      //Define button text
      if ($content.attr('data-button-text') === undefined) {
         $button = 'Older Posts';
      } else {
         $button = $content.attr('data-button-text');
      }
      $el.append('<div class="col-md-12"><a href="#" id="load-more" class="more blog-page-link"><span class="loader"></span><span class="text">' + $button + '</span></a></div>');

      //Load posts function
      var load_posts = function () {
            $('#load-more').addClass('loading');
            $('#load-more span.text').text("Loading...");
            $.ajax({
               type: "GET",
               data: {
                  postType: $content.attr('data-post-type'),
                  category: $content.attr('data-category'),
                  author: $content.attr('data-author'),
                  taxonomy: $content.attr('data-taxonomy'),
                  tag: $content.attr('data-tag'),
                  postNotIn: $content.attr('data-post-not-in'),
                  numPosts: $content.attr('data-display-posts'),
                  pageNumber: page,
               },
               dataType: "html",
               url: $path + "/ajax-load-more.php",
               beforeSend: function () {
                  if (page != 1) {
                     $('#load-more').addClass('loading');
                     $('#load-more span.text').text("Loading...");
                  }
               },
               success: function (data) {
				
                  var $data = $(data); // Convert data to an object   
                  //alert(data);           
                  if (data.length > 1) {
                     $data.hide();
                     $content.append($data);
					 var $newItems = $data;
						$('.blog-box').imagesLoaded( function(){
							$('.blog-box').isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
							$('.flexslider').flexslider({animation: "fade", slideshowSpeed: 4000});
						});
					 
					 $data.fadeIn(500, function () {
                        $('#load-more').removeClass('loading');
                        $('#load-more span.text').text($button);
						$loading = false;
                     });
                  } else {
                     $('#load-more').removeClass('loading').addClass('done');
                     $('#load-more span.text').text($button);
                     $loading = false;
                     $finished = true;
                  }
               },
               error: function (jqXHR, textStatus, errorThrown) {
                  $('#load-more').removeClass('loading');
                  $('#load-more span.text').text($button);
                  //alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
               }
            });
         }

      $('#load-more').click(function (event) {
			event.preventDefault();
         if (!$loading && !$finished && !$(this).hasClass('done')) {
            $loading = true;
            page++;
			
            load_posts();
			 $("html, body").animate({ scrollTop: $(document).height() }, 1000);
         }
		 
      });
      load_posts();
   }

});	
	
    
/*-----------------PAGE HEAD------------------------------*/
var sliderBanner = $('#banner .bxslider');
	try{		
		sliderBanner.bxSlider({
			auto: true,
			mode: 'vertical',
			maxSlides:1,
			minSlides:1
		});
	} catch(err) {
		//alert ('There was an issue displaying the slider');
	}
	/*-------------------------------------------------*/
	/* =  portfolio isotope
	/*-------------------------------------------------*/

	var winDow = $(window);
	// Needed variables
	var $container=$('.portfolio-box, .blog-box');
	var $filter=$('.filter');

	try{
		$container.imagesLoaded( function(){
			$container.show();
			$container.isotope({
				filter:'*',
				layoutMode:'masonry',
				animationOptions:{
					duration:750,
					easing:'linear'
				}
			}).isotope( 'reloadItems' );
			$('.flexslider').flexslider({animation: "fade", slideshowSpeed: 4000});
		});
	} catch(err) {
	}

	winDow.bind('resize', function(){
		var selector = $filter.find('a.active').attr('data-filter');

		try {
			$container.isotope({ 
				filter	: selector,
				animationOptions: {
					duration: 750,
					easing	: 'linear',
					queue	: false,
				}
			});
		} catch(err) {
		}
		return false;
	});
	
	// Isotope Filter 
	$filter.find('a').click(function(){
		var selector = $(this).attr('data-filter');

		try {
			$container.isotope({ 
				filter	: selector,
				animationOptions: {
					duration: 750,
					easing	: 'linear',
					queue	: false,
				}
			});
		} catch(err) {

		}
		return false;
	});


	var filterItemA	= $('.filter li a');

	filterItemA.on('click', function(){
		var $this = $(this);
		if ( !$this.hasClass('active')) {
			filterItemA.removeClass('active');
			$this.addClass('active');
		}
	});

	/*-------------------------------------------------*/
	/* =  preloader function
	/*-------------------------------------------------*/
	var body = $('body');
	body.addClass('active');

	winDow.load( function(){
		var mainDiv = $('#container'),
			preloader = $('.preloader');

			preloader.fadeOut(400, function(){
				mainDiv.delay(400).addClass('active');
				body.delay(400).css('background', '#b4b7b8');
			});
	});

	/*-------------------------------------------------*/
	/* =  flexslider
	/*-------------------------------------------------*/
	try {

		var SliderPost = $('.flexslider, .blog-post .flexslider');

		SliderPost.flexslider({
			animation: "fade",
			slideshowSpeed: 4000,
		});
	} catch(err) {

	}
	
	
	/*-------------------------------------------------*/
	/* =  header height fix
	/*-------------------------------------------------*/
	
	/*var bodyHeight = $(window).outerHeight();
	var containerHeight = $('.inner-content').outerHeight();
	var headerHeight = $('header').outerHeight();
	
	if( headerHeight > containerHeight ) {
		$('#map').css('height', headerHeight);
	} else {
		$('header').css('height', containerHeight);
		$('#map').css('height', containerHeight);
	}*/
	var content = $('#content');
	content.imagesLoaded( function(){
		var bodyHeight = $(window).outerHeight(),
		containerHeight = $('.inner-content').outerHeight(),
		headerHeight = $('header');

		if( bodyHeight > containerHeight ) {
			headerHeight.css('height',bodyHeight);
		} else {
			headerHeight.css('height',containerHeight);	
		}
	});

	winDow.bind('resize', function(){
		var bodyHeight = $(window).outerHeight(),
		containerHeight = $('.inner-content').outerHeight(),
		headerHeight = $('header');

		if( bodyHeight > containerHeight ) {
			headerHeight.css('height',bodyHeight);
		} else {
			headerHeight.css('height',containerHeight);	
		}
	});

	/* ---------------------------------------------------------------------- */
	/*	nice scroll
	/* ---------------------------------------------------------------------- */

	try {
		var HTMLcontainer = $("html");
		HTMLcontainer.niceScroll({
			//autohidemode : false
			//cursorcolor:"#2fbab9"
		});
	} catch(err) {

	}

	/* ---------------------------------------------------------------------- */
	/*	info box toggle
	/* ---------------------------------------------------------------------- */

	var toggleInfo = $('.info-toggle'),
		toggleContent = $('.info-content');

		toggleInfo.on('click', function(e){
			e.preventDefault();

			if ( !$(this).hasClass('active') ) {
				$(this).addClass('active');
				toggleContent.addClass('active');
			} else {
				$(this).removeClass('active');
				toggleContent.removeClass('active');				
			}
		});
	/* ---------------------------------------------------------------------- */
	/*	magnific-popup
	/* ---------------------------------------------------------------------- */
	jQuery('a[data-rel]').each(function() {
		jQuery(this).attr('rel', jQuery(this).data('rel'));
	});
	$("a[rel^='prettyPhoto']").prettyPhoto({
		//hook: 'data-rel', 
		default_width: 500,
		default_height: 344,
		social_tools: '',
		theme: 'light_square',
		inline_markup: '<div class="pp_inline clearfix inline-markup">{content}</div>',
	});
	/*
	
	try {
		// Example with multiple objects
		var ZoomImage = $('.zoom, .zoom-image');
		ZoomImage.magnificPopup({
			type: 'image'
		});
	} catch(err) {

	}
        try {
		// Example with multiple objects
		$('.zoom.video').magnificPopup({
			type: 'iframe'
		});
	} catch(err) {

	}*/

	/*-------------------------------------------------*/
	/* =  Testimonial
	/*-------------------------------------------------*/
	try{
		var testimUl = $('.testimonial ul');
               if($('.testimonial ul li').size()>1){
		testimUl.quovolver({
			transitionSpeed:300,
			autoPlay:true
		});
               }
	}catch(err){
	}

	/* ---------------------------------------------------------------------- */
	/*	Tabs
	/* ---------------------------------------------------------------------- */
	var clickTab = $('.tab-links li a');

	clickTab.on('click', function(e){
		e.preventDefault();

		var $this = $(this),
			hisIndex = $this.parent('li').index(),
			tabCont = $('.tab-content'),
			tabContIndex = $(".tab-content:eq(" + hisIndex + ")");

		if( !$this.hasClass('active')) {

			clickTab.removeClass('active');
			$this.addClass('active');

			tabCont.slideUp(400);
			tabCont.removeClass('active');
			tabContIndex.delay(400).slideDown(400);
			tabContIndex.addClass('active');
		}
	});
        /*-------------------Accordion------------------------*/
        var acordDivHide = $('.accordion div'),
			acordHeadClick = $('.accordion h2');


		acordDivHide.addClass('hide');

		acordHeadClick.on('click', function() {
			var $this = $(this);
			
			if ( !$this.hasClass('active')) {
			acordHeadClick.removeClass('active');
			$this.addClass('active')
                             .next()
                             .slideDown(200)
                             .siblings('.accordion div')
                             .slideUp(300);
                         acordDivHide.removeClass('hide');
			}
			else {
				$(this).removeClass('active')
						.next()
						.slideUp(300);
                                  acordDivHide.addClass('hide')              
			}
		});

	/*-------------------------------------------------*/
        // Toggles
	$(".toggle-container").hide();
	$(".toggle-trigger").click(function(e){
		e.preventDefault();
		$(this).toggleClass("open").next().slideToggle(500);
		return false;
	});
	/*-------------------------------------------------*/
	/* = skills animate
	/*-------------------------------------------------*/

	try {
		var animateElement = $(".meter > span");
		animateElement.each(function() {
			$(this)
				.data("origWidth", $(this).width())
				.width(0)
				.animate({
					width: $(this).data("origWidth")
				}, 1200);
		});
	} catch(err) {

	}

	/* ---------------------------------------------------------------------- */
	/*	menu responsive
	/* ---------------------------------------------------------------------- */
	var menuClick = $('a.elemadded'),
		navbarVertical = $('.menu-box');
		
	menuClick.on('click', function(e){
		e.preventDefault();

		if( navbarVertical.hasClass('active') ){
			navbarVertical.slideUp(300).removeClass('active');
		} else {
			navbarVertical.slideDown(300).addClass('active');
		}
	});

	winDow.bind('resize', function(){
		if ( winDow.width() > 768 ) {
			navbarVertical.slideDown(300).removeClass('active');
		} else {
			navbarVertical.slideUp(300).removeClass('active');
		}
	});

});
/********* Contact Widget *************/
function checkemail(emailaddress){
	"use strict";
	var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i); 
	return pattern.test(emailaddress); 
}

jQuery(document).ready(function(){ 
	"use strict";
	jQuery('#registerErrors, .widgetinfo').hide();		
	var $messageshort = false;
	var $emailerror = false;
	var $nameshort = false;
	var $namelong = false;
	
	jQuery('#contactFormWidget input#wformsend').click(function(){ 
		var $name = jQuery('#wname').val();
		var $email = jQuery('#wemail').val();
		var $message = jQuery('#wmessage').val();
		var $contactemail = jQuery('#wcontactemail').val();
		var $contacturl = jQuery('#wcontacturl').val();
		var $subject = jQuery('#wsubject').val();
	
		if ($name !== '' && $name.length < 3){ $nameshort = true; } else { $nameshort = false; }
		if ($name !== '' && $name.length > 30){ $namelong = true; } else { $namelong = false; }
		if ($email !== '' && checkemail($email)){ $emailerror = true; } else { $emailerror = false; }
		if ($message !== '' && $message !== 'Message' && $message.length < 3){ $messageshort = true; } else { $messageshort = false; }
		
		jQuery('#contactFormWidget .loading').animate({opacity: 1}, 250);
		
		if ($name !== '' && $nameshort !== true && $namelong !== true && $email !== '' && $emailerror !== false && $message !== '' && $messageshort !== true && $contactemail !== ''){ 
			jQuery.post($contacturl, 
				{type: 'widget', contactemail: $contactemail, subject: $subject, name: $name, email: $email, message: $message}, 
				function(/*data*/){
					jQuery('#contactFormWidget .loading').animate({opacity: 0}, 250);
					jQuery('.form').fadeOut();
					jQuery('#bottom #wname, #bottom #wemail, #bottom #wmessage').css({'border':'0'});
					jQuery('.widgeterror').hide();
					jQuery('.widgetinfo').fadeIn('slow');
					jQuery('.widgetinfo').delay(2000).fadeOut(1000, function(){ 
						jQuery('#wname, #wemail, #wmessage').val('');
						jQuery('.form').fadeIn('slow');
					});
				}
			);
			
			return false;
		} else {
			jQuery('#contactFormWidget .loading').animate({opacity: 0}, 250);
			jQuery('.widgeterror').hide();
			jQuery('.widgeterror').fadeIn('fast');
			jQuery('.widgeterror').delay(3000).fadeOut(1000);
			
			if ($name === '' || $name === 'Name' || $nameshort === true || $namelong === true){ 
				jQuery('#wname').css({'border-left':'4px solid #red'}); 
			} else { 
				jQuery('#wname').css({'border-left':'4px solid #929DAC'}); 
			}
			
			if ($email === '' || $email === 'Email' || $emailerror === false){ 
				jQuery('#wemail').css({'border-left':'4px solid red'});
			} else { 
				jQuery('#wemail').css({'border-left':'4px solid #929DAC'}); 
			}
			
			if ($message === '' || $message === 'Message' || $messageshort === true){ 
				jQuery('#wmessage').css({'border-left':'4px solid red'}); 
			} else { 
				jQuery('#wmessage').css({'border-left':'4px solid #929DAC'}); 
			}
			
			return false;
		}
	});
});