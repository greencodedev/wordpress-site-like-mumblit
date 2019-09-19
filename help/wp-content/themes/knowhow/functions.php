<?php
/**
* KnowHow functions and definitions
* by HeroThemes (http://herothemes.com)
*/

/**
* Set the content width based on the theme's design and stylesheet.
*/
if ( ! isset( $content_width ) ) $content_width = 980;


/**
* Sets up theme defaults and registers support for various WordPress features.
*/
if ( ! function_exists( 'st_theme_setup' ) ):
function st_theme_setup() {
	
	/**
	* Make theme available for translation
	* Translations can be filed in the /languages/ directory
	*/
	load_theme_textdomain( 'framework', get_template_directory() . '/languages' );
	

	/**
	* Add default posts and comments RSS feed links to head
	*/
	add_theme_support( 'automatic-feed-links' );
	
	/**
	* Enable support for Post Thumbnails
	*/
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 60, 60 );
	add_image_size( 'post', 150, 150, false ); // Post thumbnail	
	
	/**
	* Register menu locations
	*/
	register_nav_menus( array(
			'primary-nav' => __( 'Primary Menu', 'framework' ),
			'footer-nav' => __( 'Footer Menu', 'framework' )
	));
	
	/**
	* Add Support for post formarts
	*/
	add_theme_support( 'post-formats', array( 'video' ) );
	
	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );	

    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a
     * hard-coded <title> tag in the document head, and expect WordPress to
     * provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

    // This is a hero theme
    add_theme_support('ht-hero-theme');
	
}
endif; // st_theme_setup
add_action( 'after_setup_theme', 'st_theme_setup' );


/**
* Custom Theme Options
*/
if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/framework/admin/' );
	require_once dirname( __FILE__ ) . '/framework/admin/options-framework.php';
}

/**
 * Enqueues scripts and styles for front-end.
 */
require("framework/scripts.php");
require("framework/styles.php");

/**
 * Theme Functions
 */
require("framework/theme-functions.php");

/**
 * Adds theme shortcodes
 */
require("framework/shortcodes/shortcodes.php");

// Add shortcode manager
require("framework/wysiwyg/wysiwyg.php");

/**
 * Comment Functions
 */
require("framework/comment-functions.php");

/**
 * Post Types
 */
require("framework/post-types.php");

/**
 * Post Meta Boxes
 */
require_once ("framework/meta-box-library/meta-box.php");
// Include the meta box definition
include 'framework/post-meta.php';

/**
 * Post Format Functions
 */
require("framework/post-formats.php");

/**
 * Comment Functions
 */
require("framework/template-navigation.php");

/**
 * Register widgetized area and update sidebar with default widgets
 */
require("framework/register-sidebars.php");

/**
 * Add Widget Functions
 */ 
require("framework/widgets/widget-functions.php");

/**
 * Add post views
 */
function st_set_post_views($postID) {
    $count_key = '_st_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 1;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '1');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function st_get_post_views($postID){
    $count_key = '_st_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '1');
        return "1 View";
    }
    return $count.' Views';
}


// Add support for WP 4.1 title tag
if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function theme_slug_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
    }
    add_action( 'wp_head', 'theme_slug_render_title' );
endif;
