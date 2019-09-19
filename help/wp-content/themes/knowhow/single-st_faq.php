<?php get_header(); ?>

<?php
$st_faq_sidebar = of_get_option('st_faq_sidebar');
$st_sidebar_class = 'sidebar-right';
if ( !is_active_sidebar( 'ht_faq' ) ) :

  if ($st_faq_sidebar == 'fullwidth') :
    $st_sidebar_class = 'sidebar-off';
  elseif ($st_faq_sidebar == 'sidebar-l') : 
    $st_sidebar_class = 'sidebar-left';
  elseif ($st_faq_sidebar == 'sidebar-r') :  
    $st_sidebar_class = 'sidebar-right';
  endif;

endif;
?>

<!-- #primary -->
<div id="primary" class="clearfix <?php echo $st_sidebar_class ?>"> 
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
    <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

	<header class="entry-header">
          
          <?php if ( 'has_post_thumbnail' ) { ?>
          <div class="entry-thumb">
            <?php the_post_thumbnail( 'post' ); ?>
            </div>
          <?php } ?>
        </header>
        
        <div class="entry-content">
          <?php the_content(); ?>
          <?php numbered_in_page_links( array( 'before' => '<div class="page-links"><strong>' . __( 'Pages:', 'framework' ) .'</strong>', 'after' => '</div>' ) ); ?>
        </div>
      
</article>

    <?php endwhile; // end of the loop. ?>
    
</section>
<!-- #content -->
  
<?php if (of_get_option('st_faq_sidebar') != 'fullwidth') {   ?>
	<?php get_sidebar( 'faq' ); ?>
<?php } ?>


</div>
<!-- .container --> 
</div>
<!-- /#primary -->

<?php get_footer(); ?>