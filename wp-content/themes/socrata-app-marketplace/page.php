<?php get_header(); ?>

<div class="container">
  <div class="row">
    <?php get_template_part( 'sidebar-app-nav' ); ?>
    <div class="col-sm-8 col-md-9"><!-- Content Column -->
      <?php if (have_posts()); ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content()?>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php get_footer(); ?>
