<div style="margin-bottom: 30px">
	<input class="field-search-icon" type="text" placeholder="search" value="">
</div>
<div class="app-nav">
	<h2 class="title">Categories</h2>
	<?php wp_nav_menu( array( 'theme_location' => 'industry' ) ); ?>
	<h5>Resources</h5>
	<?php wp_nav_menu( array( 'theme_location' => 'resources' ) ); ?>
</div>