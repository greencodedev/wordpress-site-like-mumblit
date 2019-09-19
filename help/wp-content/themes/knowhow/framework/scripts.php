<?php
/**
 * Enqueues scripts and styles for front-end.
 */
function st_enqueue_scripts() {
	
	/*
	* Load our main theme JavaScript file
	*/
	wp_enqueue_script('st_theme_custom', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), false, true);
	
	/*
	* Register JavaScript for Live Search
	*/
	wp_register_script('st_live_search', get_template_directory_uri() . '/js/jquery.livesearch.js', array( 'jquery' ), false, true);
		
	/*
	* Adds JavaScript for shortcodes
	* (will be mvoed to plugin soon)
	*/
	wp_enqueue_script('st_shortcodes', get_template_directory_uri() . '/framework/shortcodes/shortcodes.js', array( 'jquery' ), false, true);
	
	/*
	* Adds JavaScript to pages with the comment form to support
	* sites with threaded comments (when in use).
	*/
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
	}

}
add_action('wp_enqueue_scripts', 'st_enqueue_scripts');


/**
 * add ie conditional html5 shim to header
 */
function add_ie_html5_shim () {
    echo '<!--[if lt IE 9]>';
    echo '<script src="'. get_template_directory_uri() .'/js/html5.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'add_ie_html5_shim');	

/**
 * add ie 6-8 conditional selectivizr to header
 * Important for the responsive framework
 */
function add_ie_selectivizr () {
    echo '<!--[if (gte IE 6)&(lte IE 8)]>';
    echo '<script src="'. get_template_directory_uri() .'/js/selectivizr-min.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'add_ie_selectivizr');	

/**
 * Add live search JavaScript to the footer
 */
function add_live_search () {
?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	jQuery('#live-search #s').liveSearch({url: '<?php echo home_url(); ?>/index.php?ajax=1&s='});
	});
	</script>
<?php
	wp_enqueue_script('st_live_search');

}
if (of_get_option('st_live_search') == true) {
add_action('wp_footer', 'add_live_search');	
}