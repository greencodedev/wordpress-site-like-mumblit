<?php
/**
 * The template for displaying 404 pages (Not Found).
**/
?>
<?php get_header(); ?>

<!-- #primary -->
<div id="primary" class="sidebar-off container clearfix"> 
<!-- .ht-container -->
<div class="ht-container">

  <!-- #content --> 
  <section id="content" role="main">
  
  <!-- #page-header -->
<header id="page-header">
	<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'framework' ); ?></h1>
	<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search above?', 'framework' ); ?></p>
</header>
<!-- /#page-header -->


</section>
<!-- /#content-->
  
</div>
<!-- .ht-container --> 
</div>
<!-- /#primary--> 

<?php get_footer(); ?>