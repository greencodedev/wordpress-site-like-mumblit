<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */

/********************* META BOX DEFINITIONS ***********************/

/**
 * Prefix of meta keys (optional)
 * Use underscore (_) at the beginning to make keys hidden
 * Alt.: You also can make prefix empty to disable it
 */
// Better has an underscore as last sign
$prefix = 'st_';

global $meta_boxes;

$meta_boxes = array();

// Post Options
$meta_boxes[] = array(
	
	// Meta box id, UNIQUE per meta box. Optional since 4.1.5
	'id' => 'post_sidebar',

	// Meta box title - Will appear at the drag and drop handle bar. Required.
	'title' => __( 'Sidebar Options', 'framework' ),

	// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
	'pages' => array( 'post', 'page' ),

	// Where the meta box appear: normal (default), advanced, side. Optional.
	'context' => 'side',

	// Order of meta box: high (default), low. Optional.
	'priority' => 'low',

	// Auto save: true, false (default). Optional.
	'autosave' => true,

	'fields' => array(
			// SELECT BOX
			array(
			'name' => __( 'Sidebar Position', 'framework' ),
			'id' => "{$prefix}post_sidebar",
			'type' => 'select',
			// Array of 'value' => 'Label' pairs for select box
			'options' => array(
				'sidebar-right' => __( 'Sidebar Right', 'framework' ),
				'sidebar-left' => __( 'Sidebar Left', 'framework' ),
				'sidebar-off' => __( 'Sidebar Off', 'framework' ),
			),
			// Select multiple values, optional. Default is false.
			'multiple' => false,
			),

	)
);


// Video Post Format
$meta_boxes[] = array(
	
	// Meta box id, UNIQUE per meta box. Optional since 4.1.5
	'id' => 'pf_video',

	// Meta box title - Will appear at the drag and drop handle bar. Required.
	'title' => __( 'Video Post Format', 'framework' ),

	// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
	'pages' => array( 'post' ),

	// Where the meta box appear: normal (default), advanced, side. Optional.
	'context' => 'normal',

	// Order of meta box: high (default), low. Optional.
	'priority' => 'high',

	// Auto save: true, false (default). Optional.
	'autosave' => true,

	'fields' => array(
		// OEMBED
			array(
			'name' => __( 'Video URL (oEmbed)', 'framework' ),
			'id' => "{$prefix}video_oembed",
			'desc' => __( 'Enter the URL to your video. (Supported services: YouTube, Vimeo, Hulu, Instagram, Qik)', 'framework' ),
			'type' => 'oembed',
		),
		// FILE ADVANCED (WP 3.5+)
		array(
			'name' => __( 'Video File Upload', 'framework' ),
			'id' => "{$prefix}video",
			'desc' => __( 'Upload your video file.', 'framework' ),
			'type' => 'file_advanced',
			'max_file_uploads' => 1,
			'mime_type' => 'video', // Leave blank for all file types
		),
		// IMAGE ADVANCED (WP 3.5+)
		array(
		'name' => __( 'Video Poster Upload', 'framework' ),
		'id' => "{$prefix}video_poster",
		'desc' => __( 'The video poster is displayed on the video player until the user hits the play button. (Optional).', 'framework' ),
		'type' => 'image_advanced',
		'max_file_uploads' => 1,
		),


	)
);


/********************* META BOX REGISTERING ***********************/

/**
 * Register meta boxes
 *
 * @return void
 */
function st_register_meta_boxes()
{
	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( !class_exists( 'RW_Meta_Box' ) )
		return;

	global $meta_boxes;
	foreach ( $meta_boxes as $meta_box )
	{
		new RW_Meta_Box( $meta_box );
	}
}
// Hook to 'admin_init' to make sure the meta box class is loaded before
// (in case using the meta box class in another plugin)
// This is also helpful for some conditionals like checking page template, categories, etc.
add_action( 'admin_init', 'st_register_meta_boxes' );
