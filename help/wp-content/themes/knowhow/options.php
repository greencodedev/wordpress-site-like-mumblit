<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'options_framework_theme'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one' => __('One', 'options_framework_theme'),
		'two' => __('Two', 'options_framework_theme'),
		'three' => __('Three', 'options_framework_theme'),
		'four' => __('Four', 'options_framework_theme'),
		'five' => __('Five', 'options_framework_theme')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'options_framework_theme'),
		'two' => __('Pancake', 'options_framework_theme'),
		'three' => __('Omelette', 'options_framework_theme'),
		'four' => __('Crepe', 'options_framework_theme'),
		'five' => __('Waffle', 'options_framework_theme')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );
		
	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}
	
	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);
	
	$wp_editor_small = array(
		'wpautop' => true, // Default
		'textarea_rows' => 2,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/framework/admin/images/';
		
	$options = array();
	
	$options[] = array( "name" => __("General Options", "framework"),
						'id' => 'st_general',
						"type" => "heading");
						
	$options[] = array(
						'name' => __('Enable Live Search?', 'framework'),
						'desc' => __('Check to enable live search.', 'framework'),
						'id' => 'st_live_search',
						'std' => '1',
						'type' => 'checkbox');
						
	$options[] = array(	'name' => __('Search Text', 'framework'),
						'desc' => '',
						'id' => 'st_search_text',
						'std' => 'Have a question? Ask or enter a search term.',
						'type' => 'text');
						
	$options[] = array(
						'name' => __('Article Meta', 'framework'),
						'desc' => __('Select which meta information to show with article posts.', 'framework'),
						'id' => 'st_article_meta',
						'std' => array(
									'date' => '1',
									'author' => '1',
									'category' => '1',
									'comments' => '1'), // On my default
						'type' => 'multicheck',
						'options' => array(
										'date' => __('Date', 'framework'),
										'author' => __('Author', 'framework'),
										'category' => __('Category', 'framework'),
										'comments' => __('Comments', 'framework')),
						);
											
	$options[] = array(
						'name' => __('Show Article Author Box?', 'framework'),
						'desc' => __('Check to show an author box at the end of an article. (The author must have their bio filled out for this to show)', 'framework'),
						'id' => 'st_single_authorbox',
						'std' => '1',
						'type' => 'checkbox');

	$options[] = array(
						'name' => __('Show Related Articles?', 'framework'),
						'desc' => __('Check to show a related articles box at the end of an article.', 'framework'),
						'id' => 'st_single_related',
						'std' => '1',
						'type' => 'checkbox');
						
	$options[] = array( "name" => __("Footer Copyright Text", "framework"),
						"desc" => __("Custom copyright text that will appear in the footer of your theme.", "framework"),
						"id" => "st_footer_copyright",
						"std" => "&copy; Copyright, A <a href='http://herothemes.com'>HeroTheme</a>",
						"type" => "editor",
						"settings" => array( 'wpautop' => true, 'textarea_rows' => 3, 'tinymce' => array( 'plugins' => 'wordpress' )) );
	
						
	// Homepage Options
						
	$options[] = array( "name" => __("Homepage Options", "framework"),
						"type" => "heading");

	$options[] = array( "name" => __("Sidebar Layout", "framework"),
						"desc" => __("Select or disable the position of the sidebar.", "framework"),
						"id" => "st_hp_sidebar",
						"std" => "sidebar-r",
						"type" => "images",
						"options" => array(
						"fullwidth" => $imagepath . "1col.png",
						"sidebar-l" => $imagepath . "2cl.png",
						"sidebar-r" => $imagepath . "2cr.png")
						);
									
	$options[] = array(
						'name' => __('Top Level Category Options', 'framework'),
						'desc' => __('The below options apply to top level categories.', 'framework'),
						'type' => 'info');
						
	$options[] = array(	'name' => __('Exclude Categories', 'framework'),
						'desc' => __('Enter a list of category IDs to exclude from displaying on the homepage. Seperate with a comma like this: 3,6,4', 'framework'),
						'id' => 'st_hp_cat_exclude',
						'std' => '',
						'class' => 'mini',
						'type' => 'text');
						
	$options[] = array(	'name' => __('Number Of Category Posts', 'framework'),
						'desc' => __('Enter the number of posts to show under each category. Default is 5', 'framework'),
						'id' => 'st_hp_cat_postnum',
						'std' => '5',
						'class' => 'mini',
						'type' => 'text');
						
	$options[] = array(
						'name' => __('Category Post Ordering', 'framework'),
						'desc' => __('Change which article are shown below each category.', 'framework'),
						'id' => 'st_hp_cat_posts_order',
						'std' => 'date',
						'type' => 'select',
						'class' => 'mini', //mini, tiny, small
						'options' => array(
							'date' => __('Post Date', 'framework'),
							'rand' => __('Random', 'framework'),
							'meta_value_num' => __('Popular', 'framework')
						));
						
	$options[] = array(
						'name' => __('Show Category Counts?', 'framework'),
						'desc' => __('Display the number of articles each category contains next to the category title?', 'framework'),
						'id' => 'st_hp_cat_counts',
						'std' => '1',
						'type' => 'checkbox');
						
	$options[] = array(
						'name' => __('Sub Categories', 'framework'),
						'desc' => __('The below options apply to sub (2nd level) categories.', 'framework'),
						'type' => 'info');
						
	$options[] = array(
						'name' => __('Show Sub Categories?', 'framework'),
						'desc' => __('Check to show sub categories on the homepage.', 'framework'),
						'id' => 'st_hp_subcat',
						'std' => '1',
						'type' => 'checkbox');
						
	$options[] = array(	'name' => __('Exclude Sub Categories', 'framework'),
						'desc' => __('Enter a list of category IDs to exclude from displaying on the homepage. Seperate with a comma like this: 3,6,4', 'framework'),
						'id' => 'st_hp_subcat_exclude',
						'std' => '',
						'class' => 'mini',
						'type' => 'text');
						
	$options[] = array(
						'name' => __('Show Sub Category Counts?', 'framework'),
						'desc' => __('Display the number of articles each category contains next to the category title?', 'framework'),
						'id' => 'st_hp_subcat_counts',
						'std' => '0',
						'type' => 'checkbox');	
						
	// FAQ Options

	$options[] = array( "name" => __("FAQ Options", "framework"),
						'id' => 'st_faq',
						"type" => "heading");

	$options[] = array(	'name' => __('FAQ Permalink Slug', 'framework'),
						'desc' => __('Enter the slug for your FAQ page. (Important: Set and resave your permalinks when you change this option).', 'framework'),
						'id' => 'st_faq_slug',
						'std' => 'faq',
						'class' => 'mini',
						'type' => 'text');

	$options[] = array( "name" => __("FAQ Sidebar", "framework"),
						"desc" => __("Select or disable the position of the FAQ page sidebar.", "framework"),
						"id" => "st_faq_sidebar",
						"std" => "sidebar-r",
						"type" => "images",
						"options" => array(
						"fullwidth" => $imagepath . "1col.png",
						"sidebar-l" => $imagepath . "2cl.png",
						"sidebar-r" => $imagepath . "2cr.png")
						);

	// Styling Opyions		
													
	$options[] = array( 
						"name" => __("Styling Options", "framework"),
						"type" => "heading");
						
	$options[] = array( "name" => __("Custom Logo", "framework"),
						"desc" => __("Upload a custom logo for your Website.", "framework"),
						"id" => "st_logo",
						"type" => "upload");
						
	$options[] = array( "name" => __("Custom Favicon", "framework"),
						"desc" => __("Upload a 16px x 16px png/ico image that will represent your website's favicon.", "framework"),
						"id" => "st_custom_favicon",
						"type" => "upload");
			
	$options[] = array( "name" => __("Footer Widget Columns", "framework"),
						"desc" => __("Select the number of column the footer widget should be displayed in.", "framework"),
						"id" => "st_footer_widgets_layout",
						"std" => "3col",
						"type" => "images",
						"options" => array(
						"2col" => $imagepath . "2col.png",
						"3col" => $imagepath . "3col.png",
						"4col" => $imagepath . "4col.png")
						);	
			
	$options[] = array( "name" => __("Theme Color", "framework"),
						"desc" => __("Select the theme color. (Works best when this and the link color match).", "framework"),
						"id" => "st_theme_color",
						"std" => "#a03717",
						"type" => "color");
				
	$options[] = array( "name" => __("Link Color", "framework"),
						"desc" => __("Select a custom link color.", "framework"),
						"id" => "st_link_color",
						"std" => "#a03717",
						"type" => "color");
						
	$options[] = array( "name" => __("Link Color Hover", "framework"),
						"desc" => __("Select a custom link hover color", "framework"),
						"id" => "st_link_color_hover",
						"std" => "#a03717",
						"type" => "color");
						
	$options[] = array( "name" => __("Custom CSS", "framework"),
						"desc" => __("Add some CSS to your theme by adding it to this block.", "framework"),
						"id" => "st_custom_css",
						"std" => "",
						"type" => "textarea");

	return $options;
}



/*
 * This is an example of how to add custom scripts to the options panel.
 * This example shows/hides an option when a checkbox is clicked.
 */

add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');

function optionsframework_custom_scripts() { ?>

<script type="text/javascript">
jQuery(document).ready(function() {

jQuery('#st_live_search').click(function() {
jQuery('#section-st_search_text').fadeToggle(400);
});
if (jQuery('#st_live_search:checked').val() !== undefined) {
jQuery('#section-st_search_text').show();
}
});
</script>
<?php
}