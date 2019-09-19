<?php
/**
* The template for displaying Search Results pages.
*
*/
?>

<?php
// Get HP sidebar position
$st_hp_sidebar = of_get_option('st_hp_sidebar');
if ($st_hp_sidebar == 'fullwidth') {
	$st_hp_sidebar = 'sidebar-off';
} elseif ($st_hp_sidebar == 'sidebar-l') {
	$st_hp_sidebar = 'sidebar-left';
} elseif ($st_hp_sidebar == 'sidebar-r') {
	$st_hp_sidebar = 'sidebar-right';	
} else {
	$st_hp_sidebar = 'sidebar-right';
}
?>

<?php if(!empty($_GET['ajax']) ? $_GET['ajax'] : null) { // Is Live Search ?>

<?php
// Get FAQ slug from options
$st_faq_slug = 'faq';
$st_faq_slug = of_get_option('st_faq_slug');
?>

<?php if (have_posts()) { ?>

<ul id="search-result">
  <?php while (have_posts()) : the_post(); ?>
  
  <?php
	// Set search result class	
	if ( has_post_format( 'video' )) { 
	$st_search_class = 'video';
	} elseif ( 'st_faq' == get_post_type() ) {
	$st_search_class = 'faq';
	} else {
	$st_search_class = 'standard';
	}
  ?>
  <li class="<?php echo $st_search_class ?>">
  <?php if ( 'st_faq' == get_post_type() ) { ?>
  <a href="<?php echo home_url(); ?>/<?php echo $st_faq_slug ?>/#faq-<?php the_ID(); ?>"><?php the_title(); ?></a>
  <?php } else { ?>
  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
  <?php } ?>
  </li>
  <?php endwhile; ?>
 
</ul>

<?php } else { ?>

<ul id="search-result">
  <li class="nothing-here"><?php _e( "Sorry, no posts were found.", "framework" ); ?></li>
</ul>

<?php } ?>

<?php } else { // Is Normal Search ?>

<?php get_header(); ?>


<!-- #primary -->
<div id="primary" class="<?php echo $st_hp_sidebar; ?> clearfix"> 
<!-- .ht-container -->
<div class="ht-container">
  
<!-- #content -->
<section id="content" role="main">
  
<!-- #page-header -->
<header id="page-header" class="clearfix">
<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'framework' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
<p><?php _e( "Here's what we've found for you", "framework" ); ?></p>
</header>
<!-- /#page-header -->
  
<?php if ( have_posts() ) { ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

<?php get_template_part( 'page', 'navigation' ); ?>

			<?php } else { ?>

				<?php get_template_part( 'content-none' ); ?>

			<?php } ?>

</section>
<!-- #content -->

<?php if (of_get_option('st_hp_sidebar') != 'fullwidth') {   ?>
<?php get_sidebar(); ?>
<?php } ?>

</div>
<!-- /.ht-container -->
</div>
<!-- #primary -->

<?php get_footer(); ?>

<?php } ?>