<?php get_header(); ?>

<?php
// Get sidebar position
$ht_page_sidebar = null;
$ht_page_sidebar = get_post_meta( get_the_ID(), 'st_post_sidebar', true );
if ($ht_page_sidebar == '') {
	$ht_page_sidebar = 'sidebar-right';
}
?>

<!-- #primary -->
<div id="primary" class="<?php echo $ht_page_sidebar ?> clearfix">
<!-- .ht-container -->
<div class="ht-container">

<!-- #content -->
  <section id="content" role="main">
  
  <!-- #page-header -->
<header id="page-header" class="clearfix">
  <h1 class="page-title"><?php the_title(); ?></h1>
  <?php st_breadcrumb(); ?>
  
  <?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<div class="entry-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<?php endif; ?>
</header>
<!-- /#page-header --> 
  
    <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          
      <div class="entry-content">
        <?php the_content(); ?>
        <?php numbered_in_page_links( array( 'before' => '<div class="page-links"><strong>' . __( 'Pages:', 'framework' ) .'</strong>', 'after' => '</div>' ) ); ?>
      </div>

    </article>
    
     <?php // If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() )
			comments_template( '', true ); ?>
    <?php endwhile; // end of the loop. ?>
    
</section>
<!-- #content -->

<?php if ($ht_page_sidebar != 'sidebar-off') {   ?>
<?php get_sidebar(); ?>
<?php } ?>

</div>
<!-- .ht-container -->
</div>
<!-- #primary -->

<?php get_footer(); ?>