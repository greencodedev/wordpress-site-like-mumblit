<?php

/*
* Load Post Format Admin Script
*/
function ht_admin_post_format_switcher($hook) {
	if ($hook == 'post.php' || $hook == 'post-new.php') {
		wp_enqueue_script('pf-post-meta', get_template_directory_uri() . '/framework/js/post-format-switcher.js', array( 'jquery' ));
	}
}
add_action('admin_enqueue_scripts','ht_admin_post_format_switcher',10,1);


/**
* Standard Post Format Display
*/
function ht_post_format_standard() {

	if( has_post_thumbnail() && !is_author() ) { ?>
	<figure class="entry-thumb">   
	<?php the_post_thumbnail( 'post' ); ?>	
	</figure>
	<?php }

}

/**
* Video Post Format Display
*/
function ht_post_format_video() {

	// Get post meta values
	$st_pf_video_oembed = get_post_meta( get_the_ID(), 'st_video_oembed', true ); 
	$st_pf_video = get_post_meta(get_the_ID(), 'st_video', true); 
	$st_pf_video_poster = get_post_meta(get_the_ID(), 'st_video_poster', true);
	
	
	if ( is_single() ) {
	
         if ( $st_pf_video_oembed != '' || $st_pf_video != '' ) {  ?>
         <figure class="entry-video">  

            <?php if ( $st_pf_video_oembed ) {
                
                echo wp_oembed_get($st_pf_video_oembed, array('width'=>658));
    
            } else {
					
				// Get attachment URLs
				$st_pf_video_file_url = wp_get_attachment_url( $st_pf_video );
				$st_pf_video_file_poster_url = wp_get_attachment_url( $st_pf_video_poster );
				
				if ( $st_pf_video_file_poster_url != null ) {
					echo do_shortcode( '[video src="'. $st_pf_video_file_url .'" width="658" height="100%" poster="'. $st_pf_video_file_poster_url .'"]' );
				} else {
					echo do_shortcode( '[video src="'. $st_pf_video_file_url .'" width="658" height="100%"]' );	
				}
            
            } ?>
        </figure>
          <?php } ?>
        
    <?php  
	} else {
	if( has_post_thumbnail() && !is_author() ) { ?>
	<figure class="entry-thumb">   
	<?php the_post_thumbnail( 'post' ); ?>	
	</figure>
	<?php }	
	}

}



