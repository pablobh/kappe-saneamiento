<?php
/******************* DEFINE NAME/VERSION ********************/

$themename = "Kappe";
$themefolder = "kappe";

define ('theme_name', $themename );
define ('theme_ver' , 1 );

/***************** ADD POST FORMAT SUPPORT *******************/
add_theme_support('post-formats', array( 'aside','gallery','link','image','quote','video') );



/********************* DEFINE MAIN PATHS ********************/

define('Kappe_PLUGINS',  get_template_directory() . '/addons' ); // Shortcut to the /addons/ directory
$adminPath 	=  get_template_directory() . '/library/admin-panel/';
$funcPath 	=  get_template_directory() . '/library/functions/';
$incPath 	=  get_template_directory() . '/library/includes/';

require_once ($funcPath . 'helper-functions.php');

require_once ($incPath . 'the_breadcrumb.php');
require_once ($incPath . 'OAuth.php');
require_once ($incPath . 'twitteroauth.php');
require_once ($incPath . 'portfolio_walker.php');
require_once ($funcPath . 'post-types.php');
require_once ($funcPath . 'widgets.php');
require_once ($funcPath . '/shortcodes/shortcode.php');

include (get_template_directory() . '/library/admin-panel/admin-ui.php');
include (get_template_directory() . '/library/admin-panel/admin-functions.php');
include (get_template_directory() . '/library/admin-panel/post-options.php');
include (get_template_directory() . '/library/admin-panel/custom-slider.php');

/************************************************************/


/*********** LOAD ALL REQUIRED SCRIPTS AND STYLES ***********/
function kappe_load_styles() {           
	wp_enqueue_style('bootstrap',  get_template_directory_uri().'/css/bootstrap.css');
	wp_enqueue_style('main-styles', get_template_directory_uri().'/style.css');
	wp_enqueue_style('responsive-styles',  get_template_directory_uri().'/responsive.css');
	wp_enqueue_style('flex-slider-styles',  get_template_directory_uri().'/css/flexslider.css');
	wp_enqueue_style('bx-slider-styles',  get_template_directory_uri().'/css/jquery.bxslider.css');
	wp_enqueue_style('font-awesome',  get_template_directory_uri().'/addons/fontawesome/css/font-awesome.min.css');
	wp_enqueue_style('jplayer-styles',  get_template_directory_uri().'/js/jplayer/skin/pink.flag/jplayer.pink.flag.css',false,'3.0.1','all');
	wp_enqueue_style('dynamic-styles',  get_template_directory_uri().'/css/dynamic-styles.php');
	wp_enqueue_style('pretty-photo',  get_template_directory_uri().'/css/prettyPhoto.css');
}                

function kappe_load_scripts() { 		
	wp_enqueue_script('jquery');
	wp_enqueue_script('migrate',  get_template_directory_uri(). '/js/jquery.migrate.js', array('jquery'), '3.2', true);
	wp_enqueue_script('modernizer',  get_template_directory_uri(). '/js/modernizr.custom.97074.js');
	
	
	wp_enqueue_script('imgload-script', get_template_directory_uri().'/js/jquery.imagesloaded.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('isotope', get_template_directory_uri() .'/js/jquery.isotope.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('jplayer-audio',  get_template_directory_uri().'/js/jplayer/jquery.jplayer.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('prettyp', get_template_directory_uri().'/js/jquery.prettyPhoto.js', array('jquery'), '3.2', true);
	wp_enqueue_script('flex-slider', get_template_directory_uri().'/js/jquery.flexslider.js', array('jquery'), '3.2', true);
	wp_enqueue_script('bx-slider', get_template_directory_uri().'/js/jquery.bxslider.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('testimonial-script', get_template_directory_uri().'/js/jquery.quovolver.js', array('jquery'), '3.2', true);
	wp_enqueue_script('bootstrap',  get_template_directory_uri(). '/js/bootstrap.js', array('jquery'), '3.0.1' );
	wp_enqueue_script('nicescroll',  get_template_directory_uri().'/js/jquery.nicescroll.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('flickr', get_template_directory_uri().'/addons/flickr/jflickrfeed.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('Validate',  get_template_directory_uri().'/js/jquery.validate.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('retina', get_template_directory_uri().'/js/retina-1.1.0.min.js', array('jquery'), '3.2', true);
	wp_enqueue_script('hoverdir', get_template_directory_uri() .'/js/jquery.hoverdir.js', array('jquery'), '3.2', true);
	
	wp_enqueue_script('custom-scripts', get_template_directory_uri() .'/js/script.js', array('jquery'), '3.2', true);
}
add_action( 'wp_enqueue_scripts', 'kappe_load_styles' );
add_action( 'wp_enqueue_scripts', 'kappe_load_scripts' );

// Load Google Fonts
function Kappe_fonts() {
    $protocol = is_ssl() ? 'https' : 'http';
    wp_enqueue_style( 'Kappe-opensans', "$protocol://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic" );
    }
add_action( 'wp_enqueue_scripts', 'Kappe_fonts' );

/************************************************************/
/*-----------------------------------------------------------------------------------*/
# Typography Elements Array
/*-----------------------------------------------------------------------------------*/
$custom_typography = array(
	"body, .post-content, .full-page, .contact-info, .project-post-content, .about-section p, p, li, td"   =>	"typography_general",
	"p.slogan"                                              =>	"typography_site_title",
	".menu > li > a"                                          =>	"typography_main_nav",
	".note, .recent-projects > h4, .latest-blog>h4, .title-block"=>"typography_page_title",
	".site-map, .site-map a"                                     => "typography_breadcrumbs",
	"#sidebar .widget h1"                                        =>	"typography_widgets_title",
	".footer-block h2"                                           =>	"typography_footer_widgets_title",
	"h1"                                                         =>	"typography_heading1",
	"h2"                                                         =>	"typography_heading2",
	"h3"                                                         =>	"typography_heading3",
	"h4"                                                         =>	"typography_heading4",
	"h5"                                                         =>	"typography_heading5",
);
	
add_action('wp_enqueue_scripts', 'weblusive_typography');
function weblusive_typography(){
	global $custom_typography;
        require get_template_directory() . '/library/admin-panel/google-fonts-function.php';
	foreach( $custom_typography as $selector => $value){
		$option = weblusive_get_option( $value );
		
		if( !empty($option['font']))
			weblusive_enqueue_font( $option['font'] ) ;
	}
}



/************** ADD SUPPORT FOR LOCALIZATION ***************/

load_theme_textdomain( 'Kappe',  get_template_directory() . '/languages' );

	$locale = get_locale();

	$locale_file =  get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

/************************************************************/


/************* ADD SUPPORT FOR WORDPRESS 3 MENUS ************/

add_theme_support( 'menus' );

//Register Navigations
add_action( 'init', 'my_custom_menus' );
function my_custom_menus() {
    register_nav_menus(
        array(
            'primary_nav' => __( 'Primary Navigation', 'Kappe'),

        )
    );
}
/************************************************************/


/**************** ADD SUPPORT FOR POST THUMBS ***************/

add_theme_support( 'post-thumbnails');

// Define various thumbnail sizes
add_image_size('portfolio', 405, 311, true);
add_image_size('portfolio-single', 654, 469, true);
add_image_size('blog-list', 294, 9999, true);
add_image_size('blog-thumb', 75, 75, true);
add_image_size('blog-thumb-3', 270, 180, true);

/************************************************************/


/******* FIX THE PORTFOLIO CATEGORY PAGINATION ISSUE ********/

$option_posts_per_page = get_option( 'posts_per_page' );
add_action( 'init', 'my_modify_posts_per_page', 0);
function my_modify_posts_per_page() {
    add_filter( 'option_posts_per_page', 'my_option_posts_per_page' );
}
function my_option_posts_per_page( $value ) {
    global $option_posts_per_page;
    if ( is_tax( 'portfolio_category') ) {
		$pageId = get_page_ID_by_page_template('portfolio-template3.php');
		if ($pageId)
		{
			$custom =  get_post_custom($pageId);
			$items_per_page = isset ($custom['_page_portfolio_num_items_page']) ? $custom['_page_portfolio_num_items_page'][0] : '777';
			return $items_per_page;
		}
		else
		{
			return 4;
		}
    } else {
        return $option_posts_per_page;
    }
}

/************************************************************/


function weblusive_get_option( $name ) {
	$get_options = get_option( 'weblusive_options' );
	
	if( !empty( $get_options[$name] ))
		return $get_options[$name];
		
	return false ;
}

//Docs Url
$docs_url = "http://weblusive-themes.com/documentation/".$themefolder;

// Redirect To Theme Options Page on Activation
if (is_admin() && isset($_GET['activated'])){
	wp_redirect(admin_url('admin.php?page=panel'));
}

/************************************************************/


/****************** REGISTER SIDEBARS ***********************/

if ( function_exists('register_sidebar') )
{
   
	$footer_widget_count = weblusive_get_option('footer_widgets');
	if($footer_widget_count !== 'none')
	{
		$columns = 'column3';
		switch($footer_widget_count)
		{
			case '4':
			$columns = 'column3';
			break;
			case '3':
			$columns = 'column4';
			break;
			case '2':
			$columns = 'column6';
			break;
		}
		for($i = 1; $i<= $footer_widget_count; $i++)
		{
			unregister_sidebar('Footer Widget '.$i);
			if ( function_exists('register_sidebar') )
			register_sidebar(array(
				'name' => 'Footer Widget '.$i,
				'id'	=> 'footer-sidebar-'.$i,
				'before_widget' => '<div class="'.$columns.' widget footer-block">',
				'after_widget' => '</div>',
				'before_title' => '<h4>',
				'after_title' => '</h4>',
			));
		}
	}
}

add_filter('widget_text', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');

add_action( 'widgets_init', 'weblusive_widgets_init' );
function weblusive_widgets_init() {
	$before_widget =  '<div id="%1$s" class="widget %2$s">';
	$after_widget  =  '</div></div>';
	$before_title  =  '<h3>';
	$after_title   =  '</h3><div class="widget-container">';
					
	register_sidebar( array(
		'name' =>  __( 'Primary Widget Area', 'Kappe' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The Primary widget area', 'Kappe' ),
		'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
	) );

	//Custom Sidebars
	$sidebars = weblusive_get_option( 'sidebars' ) ;
	if($sidebars){
		foreach ($sidebars as $sidebar) {
			register_sidebar( array(
				'name' => $sidebar,
				'id' => sanitize_title($sidebar),
				'before_widget' => $before_widget , 'after_widget' => $after_widget , 'before_title' => $before_title , 'after_title' => $after_title ,
			) );
		}
	}
}

/************************************************************/


/****************** CUSTOM LOGIN LOGO ***********************/

function weblusive_login_logo(){
	if( weblusive_get_option('dashboard_logo') )
    echo '<style  type="text/css"> h1 a {  background-image:url('.weblusive_get_option('dashboard_logo').')  !important; } </style>';  
}  
add_action('login_head',  'weblusive_login_logo'); 

/************************************************************/


/******************** CUSTOM GRAVATAR ***********************/

function weblusive_custom_gravatar ($avatar) {
	$weblusive_gravatar = weblusive_get_option( 'gravatar' );
	if($weblusive_gravatar){
		$custom_avatar = weblusive_get_option( 'gravatar' );
		$avatar[$custom_avatar] = "Custom Gravatar";
	}
	return $avatar;
}
add_filter( 'avatar_defaults', 'weblusive_custom_gravatar' ); 

/************************************************************/


/********************* CUSTOM TAG CLOUDS ********************/

function Kappe_custom_tag_cloud_widget($args) {
	$args['number'] = 0; //adding a 0 will display all tags
	$args['largest'] = 18; //largest tag
	$args['smallest'] = 12; //smallest tag
	$args['unit'] = 'px'; //tag font unit
	$args['format'] = 'list'; //ul with a class of wp-tag-cloud
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'Kappe_custom_tag_cloud_widget' );

/************************************************************/


/****************** ENABLE SESSIONS *************************/

function cp_admin_init() {
	if (!session_id())
	session_start();
}

//add_action('init', 'cp_admin_init');

/************************************************************/


/******************* FILL EMPTY WIDGET TITLE ****************/

function Kappe_fill_widget_title($title){
	if( empty( $title ) )
		return ' ';
	else return $title;
}
add_filter('widget_title', 'Kappe_fill_widget_title');

add_theme_support( 'automatic-feed-links' );
if ( ! isset( $content_width ) ) $content_width = 960;

/************************************************************/


function slugify($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => false,
	);
	
	// Merge options
	$options = array_merge($defaults, $options);
	
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',
 
		// Latin symbols
		'©' => '(c)',
 
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
 
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
 
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
 
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
 
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 
 
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
 
		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);
	
	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);
	
	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}
?>
