<?php get_header(); ?>

<!-- #primary -->
<div id="primary" class="clearfix <?php if (of_get_option('st_hp_sidebar') == 'fullwidth') { echo 'sidebar-off'; } elseif (of_get_option('st_hp_sidebar') == 'sidebar-l') { echo 'sidebar-left'; } else { echo 'sidebar-right'; } ?>"> 
<!-- .ht-container -->
<div class="ht-container">

<!-- #content -->
<section id="content" role="main">


<!-- #page-header -->
<header id="page-header">
  <h1 class="page-title"><?php _e('Category: ', 'framework') ?><span><?php echo single_cat_title(); ?></span></h1>
  <?php	// show an optional category description
		$st_category_description = category_description();
		if ( ! empty( $st_category_description ) )
		echo apply_filters( 'category_archive_meta', $st_category_description );  ?>
	<?php st_breadcrumb(); ?>
</header>
<!-- /#page-header --> 

<!-- #sub-cats -->
<?php
// Sub category
$st_sub_category_id = get_query_var('cat');

$st_subcat_args = array(
  'orderby' => 'name',
  'order' => 'ASC',
  'child_of' => $st_sub_category_id,
  'pad_counts'  => 1
);
$st_sub_categories = get_categories($st_subcat_args); 
$st_sub_categories = wp_list_filter($st_sub_categories,array('parent'=>$st_sub_category_id));

if ($st_sub_categories) { ?>
<ul class="sub-categories clearfix">
<?php foreach($st_sub_categories as $st_sub_category) {  ?>
<li><h4><a href="<?php echo get_category_link( $st_sub_category->term_id ) ?>"><?php echo $st_sub_category->name ?></a><?php if (of_get_option('st_hp_subcat_counts') == '1') {
			echo '<span class="cat-count">(' . $st_sub_category->count.')</span>';	
		} ?></h4></li>
<?php } ?>
</ul>
<?php } ?>
<!-- #/sub-cats --> 

<?php
$current_cat = intval( get_query_var('cat') );
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args=array(
  'category__in' => array($current_cat),
  'paged' => $paged,
  'post_type' => 'post',
  'post_status' => 'publish',
  'ignore_sticky_posts'=> 1
);
query_posts($args);
?>

<?php if ( have_posts() ) : ?>

<?php while ( have_posts() ) : the_post(); ?>

<?php get_template_part( 'content', get_post_format() ); ?>

<?php endwhile; ?>

<?php get_template_part( 'page', 'navigation' ); ?>
  
<?php else : ?>
  <?php if (!$st_sub_categories) { ?>
<?php get_template_part( 'no-results', 'index' ); ?>
  <?php } ?>
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