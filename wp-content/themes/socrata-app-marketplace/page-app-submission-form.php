<?php
/*
Template Name: App submission form
*/

get_header(); ?>

<div class="container content">
	<div class="row">
		<div class="col-md-12">
	  		<?php if (have_posts()); ?>
		    <?php while ( have_posts() ) : the_post(); ?>
			    <?php the_content()?>
		    <?php endwhile; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>