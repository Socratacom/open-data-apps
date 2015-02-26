<!-- Left Nav -->
<div class="col-sm-4 col-md-3 hidden-xs">
	<div style="margin-bottom: 30px">
		 <?php get_search_form(true); ?> 
	</div>
	<div class="app-nav">
		<h2 class="title">Categories</h2>
		<?php wp_nav_menu( array( 'theme_location' => 'industry' ) ); ?>
		<h5>Resources</h5>
		<?php wp_nav_menu( array( 'theme_location' => 'resources' ) ); ?>
	</div>
</div>
<!-- End Left Nav -->