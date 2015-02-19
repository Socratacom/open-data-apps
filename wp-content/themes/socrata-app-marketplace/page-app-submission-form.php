<?php
/*
Template Name: App submission form
*/

get_header(); ?>

<div class="container content">
	<div class="row">
		<!-- <div class="col-md-4">
			<ul class="progress-menu">
				<li style="padding: 25px 30px; background: #ccc; border-bottom: 1px solid #ccc">Guidelines</li>
				<li style="padding: 25px 30px; background: #ddd; border-bottom: 1px solid #ccc; color: #aaa"><a href="">1. Point of Contact Information</a></li>
				<li style="padding: 25px 30px; background: #ddd; border-bottom: 1px solid #ccc; color: #aaa">2. App Information</li>
				<li style="padding: 25px 30px; background: #ddd; border-bottom: 1px solid #ccc; color: #aaa">3. App Details</li>
				<li style="padding: 25px 30px; background: #ddd; border-bottom: 1px solid #ccc; color: #aaa">4. Image Assets</li>
				<li style="padding: 25px 30px; background: #ddd; border-bottom: 1px solid #ccc; color: #aaa">5. Additional App Information</li>
				<li style="padding: 25px 30px; background: #ddd; color:#aaa">6. App Certification</li>
			</ul>
		</div> -->
		<div class="col-md-12">
	  		<?php if (have_posts()); ?>
		    <?php while ( have_posts() ) : the_post(); ?>
			    <?php the_content()?>
		    <?php endwhile; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>