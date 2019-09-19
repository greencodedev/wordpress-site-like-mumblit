<?php get_header(); ?>

<?php
// Get sidebar position
$ht_post_sidebar = null;
$ht_post_sidebar = get_post_meta( get_the_ID(), 'st_post_sidebar', true );
if ($ht_post_sidebar == '') {
	$ht_post_sidebar = 'sidebar-right';
}
?>

<!-- #primary -->
<div id="primary" class="<?php echo $ht_post_sidebar ?> clearfix"> 
<!-- .ht-container -->
<div class="ht-container">

  <!-- #content -->
  <section id="content" role="main">
  
<!-- #page-header -->
<header id="page-header" class="clearfix">
  <h1 class="page-title"><?php the_title(); ?></h1>
  <?php st_breadcrumb(); ?>
</header>
<!-- /#page-header --> 

 
<?php while ( have_posts() ) : the_post(); ?>

  <?php st_set_post_views(get_the_ID()); ?>
  
  <?php get_template_part( 'content', 'meta' ); ?>
  
    <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
    
	<!-- .entry-header -->
	<header class="entry-header">
    
    <?php if ( has_post_format( 'video' )) { ?>
    	<?php ht_post_format_video() ?>
    <?php } else { ?>
    	<?php ht_post_format_standard() ?>
    <?php } ?>

	</header>
	<!-- /.entry-header -->
      
        
        <div class="entry-content">
          <?php the_content(); ?>
          <?php wp_link_pages( array(
        'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'framework' ) . '</span>',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'framework' ) . ' </span>%',
        'separator'   => '<span class="screen-reader-text">, </span>',
      ) ); ?>
        </div>
        
        <?php if (is_single() && has_tag()) { ?>

        <div class="tags">
          <?php the_tags( '<strong> '.  __( 'Tagged:', 'framework' )  .' </strong>', '', ''); ?>
        </div>
		<?php } ?>
 
</article>

<?php if (of_get_option('st_single_authorbox')) { ?>
	<?php get_template_part('author-bio'); ?>
<?php } ?>
    
<?php if (of_get_option('st_single_related')) { ?>
	<?php get_template_part('single', 'related'); ?>
<?php } ?>
      
<?php // If comments are open or we have at least one comment, load up the comment template
	if ( comments_open() || '0' != get_comments_number() )
	comments_template( '', true ); ?>
    
<?php endwhile;  // end of the loop. ?>
    
</section>
<!-- #content -->

<?php if ($ht_post_sidebar != 'sidebar-off') {   ?>
<?php get_sidebar(); ?>
<?php } ?>

</div>
<!-- .ht-container -->
</div>
<!-- /#primary -->

<?php get_footer(); ?>