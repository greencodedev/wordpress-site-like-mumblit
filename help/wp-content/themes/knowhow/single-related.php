<?php 
$orig_post = $post;
global $post;
$categories = get_the_category($post->ID);
if ($categories) {
	$category_ids = array();
    foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

    $args=array(
    'category__in' => $category_ids,
    'post__not_in' => array($post->ID),
    'posts_per_page'=> 6, // Number of related posts that will be shown.
    'ignore_sticky_posts'=>1
    );

    $my_query = new wp_query( $args );
    
	if( $my_query->have_posts() ) { ?>
     
     <section id="related-posts" class="clearfix">
     <h3 id="related-posts-title"><?php _e("Related Articles", "framework"); ?></h3>
     	<ul class="clearfix"><?php

    while( $my_query->have_posts() ) {
    	$my_query->the_post();
	
		// Set search result class	
		if ( has_post_format( 'video' )) { 
		$st_search_class = 'video';
		} else {
		$st_search_class = 'standard';
		}
		?>
        
		<li class="<?php echo $st_search_class ?>">
        <h4 class="entry-title"><a href="<?php the_permalink()?>" rel="bookmark" title="<?php echo esc_attr( sprintf( the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_title(); ?></a></h4>
        </li>

<?php } ?>
</ul></section>
<?php    }
    }
 $post = $orig_post;
wp_reset_query(); 
?>