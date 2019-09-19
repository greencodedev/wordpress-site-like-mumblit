<?php

/*-----------------------------------------------------------------------------------*/
/*	Add Post Types
/*-----------------------------------------------------------------------------------*/

add_action( 'init', 'st_create_faq_post_type' );
if ( ! function_exists( 'st_create_faq_post_type' ) ) {
	function st_create_faq_post_type() {
		
		// Get FAQ slug from options
		$st_faq_slug = 'faq';
		$st_faq_slug = of_get_option('st_faq_slug');
		
		// Register FAQ Post Type
		register_post_type( 'st_faq',
			array(
			'description' => '',
			'labels' => array(
					'name' => __( 'FAQs', 'framework' ),
					'singular_name' => __( 'FAQ', 'framework' ),
					'add_new' => __('Add New', 'framework'),  
	  				'add_new_item' => __('Add New FAQ', 'framework'),  
	   				'edit_item' => __('Edit FAQ', 'framework'),  
	   				'new_item' => __('New FAQ', 'framework'),  
	   				'view_item' => __('View FAQ', 'framework'),  
	   				'search_items' => __('Search FAQs', 'framework'),  
	   				'not_found' =>  __('No FAQs found', 'framework'),  
	   				'not_found_in_trash' => __('No FAQs found in Trash', 'framework')
				),
			'public' => true,
	        'menu_position' => 5,
			'has_archive' => $st_faq_slug,
	        'rewrite' => array('slug' => $st_faq_slug),
			'supports' => array('title', 'editor', 'page-attributes' ),
			'public' => true,
			'show_ui' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false
			)
		);
	}
}

// Change Default Title

if ( ! function_exists( 'st_change_default_title' ) ) {
function st_change_default_title( $title ){
     $screen = get_current_screen();
	 if  ( 'st_faq' == $screen->post_type ) {
          $title = 'Enter the FAQ question';
     }
     return $title;
}
}
add_filter( 'enter_title_here', 'st_change_default_title' );


// Change Updated Title

add_filter('post_updated_messages', 'faq_updated_messages');
function faq_updated_messages( $messages ) {

$messages['st_faq'] = array(
0 => '', // Unused. Messages start at index 1.
1 => sprintf( __('FAQ updated.', 'framework'), esc_url( get_permalink(get_the_ID()) ) ),
2 => __('Custom field updated.', 'framework'),
3 => __('Custom field deleted.', 'framework'),
4 => __('FAQ updated.', 'framework'),
/* translators: %s: date and time of the revision */
5 => isset($_GET['revision']) ? sprintf( __('FAQ restored to revision from %s', 'framework'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
6 => sprintf( __('FAQ published.', 'framework'), esc_url( get_permalink(get_the_ID()) ) ),
7 => __('FAQ saved.', 'framework'),
8 => sprintf( __('FAQ submitted.', 'framework'), esc_url( add_query_arg( 'preview', 'true', get_permalink(get_the_ID()) ) ) ),
9 => sprintf( __('FAQ scheduled for: <strong>%1$s</strong>.', 'framework'),
  // translators: Publish box date format, see http://php.net/date
  esc_url( get_permalink(get_the_ID()) ) ),
10 => sprintf( __('FAQ draft updated.', 'framework'), esc_url( add_query_arg( 'preview', 'true', get_permalink(get_the_ID()) ) ) ),
);

return $messages;
}

// Change order of FAQs
function st_faq_admin_order($query) {
 
  //if($query->is_admin) {
 
        if ($query->get('post_type') == 'st_faq') {
          $query->set('orderby', 'menu_order');
          $query->set('order', 'ASC');
        }
  //}
  return $query;
}
add_filter('pre_get_posts', 'st_faq_admin_order');