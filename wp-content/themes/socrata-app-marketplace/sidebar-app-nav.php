<p><a href="/apps/catalog/" class="btn btn-info btn-block" role="button">All Apps</a></p>
<div class="app-nav" style="margin-bottom:30px;">
    <h5>Apps for...</h5>
    <?php wp_nav_menu( array( 
    'theme_location' => 'persona',
    'depth' => -1,
    )); ?>
    <h5>Industry/Vertical</h5>
    <?php wp_nav_menu( array( 'theme_location' => 'industry' ) ); ?>
    <h5>Resources</h5>
    <?php wp_nav_menu( array( 'theme_location' => 'resources' ) ); ?>
</div>
<p><a href="/apps/submit-app/" class="btn btn-primary btn-block" role="button">Submit Your App</a></p>