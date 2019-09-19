<?php

// Allow Shortcodes in widgets
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

// Add the latest article widget
require("widget-articles.php");

// Add the popular article widget
require("widget-popular-articles.php");


/**
 * Modify Exsisting Widgets
 */ 
 
add_filter('wp_list_categories', 'add_span_cat_count');
function add_span_cat_count($links) {
  $links = str_replace('</a> (', '</a> <span>', $links);
  $links = str_replace(')', '</span>', $links);
  return $links;
}

/**
 * Stop wp_list_categories function from echoing "No Categories"
 */
 
function st_dont_display_nocat($content) {
  if (!empty($content)) {
    $content = str_ireplace('<li>' .__( 'No categories', 'framework' ). '</li>', "", $content);
  }
  return $content;
}
add_filter('wp_list_categories','st_dont_display_nocat');