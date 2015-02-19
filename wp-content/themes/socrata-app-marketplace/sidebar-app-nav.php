<div style="margin-bottom: 30px">
	 <?php get_search_form(true); ?> 
</div>
<div class="app-nav">
	<h2 class="title">Categories</h2>
	<?php wp_nav_menu( array( 'theme_location' => 'industry' ) ); ?>
	<h5>Resources</h5>
	<?php wp_nav_menu( array( 'theme_location' => 'resources' ) ); ?>
</div>