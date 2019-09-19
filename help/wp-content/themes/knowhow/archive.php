<?php get_header(); ?>

<!-- #primary -->
<div id="primary" class="clearfix <?php if (of_get_option('st_hp_sidebar') == 'fullwidth') { echo 'sidebar-off'; } elseif (of_get_option('st_hp_sidebar') == 'sidebar-l') { echo 'sidebar-left'; } else { echo 'sidebar-right'; } ?>"> 
<!-- .ht-container -->
<div class="ht-container">

<!-- #content -->
<section id="content" role="main">

<!-- #page-header -->
<header id="page-header">
  <h1 class="page-title">
    <?php
							if ( is_category() ) {
								printf( __( 'Category: %s', 'framework' ), '<span>' . single_cat_title( '', false ) . '</span>' );

							} elseif ( is_tag() ) {
								printf( __( 'Tag: %s', 'framework' ), '<span>' . single_tag_title( '', false ) . '</span>' );

							} elseif ( is_author() ) {
								/* Queue the first post, that way we know
								 * what author we're dealing with (if that is the case).
								*/
								the_post();
								printf( __( 'Author Archives: %s', 'framework' ), '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
								/* Since we called the_post() above, we need to
								 * rewind the loop back to the beginning that way
								 * we can run the loop properly, in full.
								 */
								rewind_posts();

							} elseif ( is_day() ) {
								printf( __( 'Daily Archives: %s', 'framework' ), '<span>' . get_the_date() . '</span>' );

							} elseif ( is_month() ) {
								printf( __( 'Monthly Archives: %s', 'framework' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

							} elseif ( is_year() ) {
								printf( __( 'Yearly Archives: %s', 'framework' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

							} else {
								_e( 'Archives', 'framework' );

							}
						?>
  </h1>
  <?php
						if ( is_category() ) {
							// show an optional category description
							$category_description = category_description();
							if ( ! empty( $category_description ) )
								echo apply_filters( 'category_archive_meta', $category_description );

						} elseif ( is_tag() ) {
							// show an optional tag description
							$tag_description = tag_description();
							if ( ! empty( $tag_description ) )
								echo apply_filters( 'tag_archive_meta', '<p>' . $tag_description . '</p>' );
						}
					?>
</header>
<!-- /#page-header --> 


<?php if ( have_posts() ) : ?>
<?php rewind_posts(); ?>
<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', get_post_format() );
					?>
<?php endwhile; ?>

<?php get_template_part( 'page', 'navigation' ); ?>
  
<?php else : ?>

<?php get_template_part( 'no-results', 'index' ); ?>

<?php endif; ?>
  </section>
  <!-- /#content -->
<?php if (of_get_option('st_hp_sidebar') != 'fullwidth') {   ?>
<?php get_sidebar(); ?>
<?php } ?>

</div>
<!-- .ht-container --> 
</div>
<!-- /#primary -->

<?php get_footer(); ?>