<?php

/**
 * Display navigation to next/previous pages when applicable
 */
if ( ! function_exists( 'st_content_nav' ) ):
function st_content_nav( $nav_id ) {
	global $wp_query;

	$nav_class = 'site-navigation paging-navigation';
	if ( is_single() )
		$nav_class = 'site-navigation post-navigation';

	?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'framework' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">&larr;</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">&rarr;</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><span class="meta-nav">&larr;</span> <?php next_posts_link( __( 'Older posts', 'framework' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'framework' ) ); ?> <span class="meta-nav">&rarr;</span></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}
endif;


/**
 * Pagination
 */
function st_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<div class='pagination'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}


/**
 * Breadcrumbs
 */
function st_breadcrumb() {
	//global WordPress variable $post. Needed to display multi-page navigations.
    global $post, $cat,$wp_query;    
	
    $sep = '<span class="sep">/</span>';
    $parents = '';

    if (!is_front_page()) {  
        echo '<div id="breadcrumbs">';

        //Home Link
        echo '<a href="' . home_url() . '"><icon class="fa fa-home"></i></a>' . $sep;   
        	
        	
        //Category
        if (is_category()) {
        	echo get_category_parents($cat, true,' ' . $sep . ' ');

        } else if ( is_page() ) {
               
            // Standard page
            if( $post->post_parent ){
                   
                // If child page, get parents 
                $anc = get_post_ancestors( $post->ID );
                   
                // Get parents in the right order
                $anc = array_reverse($anc);
                   
                // Parent page loop
                foreach ( $anc as $ancestor ) {
                    $parents .= '<a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a>' . $sep . '';
                }
                   
                // Display parent pages
                echo $parents;
                   
                // Current page
                echo get_the_title();
                   
            } else {
                   
                // Just display current page if not parents
                echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
                   
            }

        } elseif ( 'st_faq' == get_post_type() ) {
        // FAQ Archive			
        	$st_faq_data = get_post_type_object('st_faq');
        	echo $st_faq_data->labels->name;	 

        } elseif ( is_single() && 'st_faq' != get_post_type() ) {

        //Single Post
        $terms = wp_get_post_terms( $post->ID , 'category');
        	
        $visited = array();

        foreach($terms as $term) {
        	
            if ($term->parent != 0) { 
            	echo get_category_parents($term->term_id, true,' ' . $sep . ' ', false, $visited );
            } else {
            	echo '<a href="' . esc_attr(get_term_link($term, 'category')) . '" title="' . sprintf( __( "View all posts in %s", "framework" ), $term->name ) . '" ' . '>' . $term->name.'</a> ';
            	echo $sep;
            	$visited[] = $term->term_id;
            }

        } // End foreach

        echo get_the_title();

        } else {
        	//Display the post title.
        	echo get_the_title();
        }
    	
        echo '</div>';	

    } //is_front_page
}


/**
 * The formatted output of a list of pages.
 */
add_action( 'numbered_in_page_links', 'numbered_in_page_links', 10, 1 );

/**
 * Modification of wp_link_pages() with an extra element to highlight the current page.
 *
 * @param  array $args
 * @return void
 */
function numbered_in_page_links( $args = array () )
{
    $defaults = array(
        'before'      => '<p>' . __('Pages:', 'framework')
    ,   'after'       => '</p>'
    ,   'link_before' => ''
    ,   'link_after'  => ''
    ,   'pagelink'    => '%'
    ,   'echo'        => 1
        // element for the current page
    ,   'highlight'   => 'span'
    );

    $r = wp_parse_args( $args, $defaults );
    $r = apply_filters( 'wp_link_pages_args', $r );
    extract( $r, EXTR_SKIP );

    global $page, $numpages, $multipage, $more, $pagenow;

    if ( ! $multipage )
    {
        return;
    }

    $output = $before;

    for ( $i = 1; $i < ( $numpages + 1 ); $i++ )
    {
        $j       = str_replace( '%', $i, $pagelink );
        $output .= ' ';

        if ( $i != $page || ( ! $more && 1 == $page ) )
        {
            $output .= _wp_link_page( $i ) . "{$link_before}{$j}{$link_after}</a>";
        }
        else
        {   // highlight the current page
            // not sure if we need $link_before and $link_after
            $output .= "<$highlight>{$link_before}{$j}{$link_after}</$highlight>";
        }
    }

    print $output . $after;
}