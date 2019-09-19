<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

<?php ht_post_format_video() ?>

<h2 class="entry-title"><a rel="bookmark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    
  <div class="entry-content">
    <?php the_excerpt(); ?>
  </div>

  <a href="<?php the_permalink(); ?>" class="readmore"><?php _e( 'Read More', 'framework' ); ?><span> &rarr;</span></a>

</article>