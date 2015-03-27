<?php

// HOMEPAGE FEATURED APPS QUERY
// [home-featured-apps"]
add_shortcode('home-featured-apps','home_featured_apps');
function home_featured_apps ($atts, $content = null) { ob_start(); ?>
<!--<h2>Featured Apps</h2>-->
<div class="row tiles hidden-xs hidden-sm">
	<div class="col-md-8">
		<?php $feature_query = new WP_Query('post_type=socrata_apps&meta_key=_featured&meta_value=yes&orderby=desc&showposts=1'); while ($feature_query->have_posts()) : $feature_query->the_post(); ?>
		<div class="tile tile-lg">
			<div class="tile-image">
				<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-lg', false, array('class' => 'img-responsive')); ?></a>
			</div>
			<div class="tile-content">				
				<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
				<h3><?php the_title(); ?></a></h3>
				<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
				<a href="<?php the_permalink(); ?>" class="btn btn-primary tile-btn tile-fade hidden-xs">View App</a>
				<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='featureIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
				<div class="tile-overlay"></div>
				<a href="<?php the_permalink(); ?>" class="tile-link"></a>
			</div>
		</div>
		<?php endwhile;  wp_reset_postdata(); ?>
	</div>
	<div class="col-md-4">
		<?php $feature_query = new WP_Query('post_type=socrata_apps&meta_key=_featured&meta_value=yes&orderby=desc&showposts=2&offset=1'); while ($feature_query->have_posts()) : $feature_query->the_post(); ?>
		<div class="tile tile-md">
			<div class="tile-image">
				<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
			</div>
			<div class="tile-content">				
				<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
				<h3><?php the_title(); ?></a></h3>
				<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
				<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
				<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='featureIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
				<div class="tile-overlay"></div>
				<a href="<?php the_permalink(); ?>" class="tile-link"></a>
			</div>
		</div>
		<?php endwhile;  wp_reset_postdata(); ?>
	</div>
</div>
<div class="row tiles hidden-xs hidden-sm">
	<?php $feature_query = new WP_Query('post_type=socrata_apps&meta_key=_featured&meta_value=yes&orderby=desc&showposts=3&offset=3'); while ($feature_query->have_posts()) : $feature_query->the_post(); ?>
	<div class="col-sm-6 col-md-4">
		<div class="tile tile-md">
			<div class="tile-image">
				<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
			</div>
			<div class="tile-content">				
				<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
				<h3><?php the_title(); ?></a></h3>
				<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
				<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
				<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='featureIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
				<div class="tile-overlay"></div>
				<a href="<?php the_permalink(); ?>" class="tile-link"></a>
			</div>
		</div>
	</div>
	<?php endwhile;  wp_reset_postdata(); ?>
</div>
<div class="row tiles hidden-md hidden-lg">
	<?php $feature_query = new WP_Query('post_type=socrata_apps&meta_key=_featured&meta_value=yes&orderby=desc&showposts=4'); while ($feature_query->have_posts()) : $feature_query->the_post(); ?>
	<div class="col-xs-12 col-sm-6">
		<div class="tile tile-md">
			<div class="tile-image">
				<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
			</div>
			<div class="tile-content">				
				<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
				<h3><?php the_title(); ?></a></h3>
				<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
				<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
				<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='featureIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
				<div class="tile-overlay"></div>
				<a href="<?php the_permalink(); ?>" class="tile-link"></a>
			</div>
		</div>
	</div>
    <?php endwhile;  wp_reset_postdata(); ?>
</div>
<!--[if gte IE 9]>
<style type="text/css">
.gradient {
filter: none;
}
</style>
<![endif]-->
<script>
  $('.featureIcons span:contains(Socrata Certified)').addClass('icon-certified');
</script>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}


// HOMEPAGE SOCRATA BUSINESS APPS QUERY
// [home-business-apps]
add_shortcode('home-business-apps','home_business_apps');
function home_business_apps ($atts, $content = null) {
	ob_start(); 
	?>
	<section class="business-apps">
		<div class="row">
			<div class="col-xs-12 category-title">
				<h2>Business Apps</h2>
				<a href="/persona/business/">See All <span class="glyphicon glyphicon-chevron-right"></span></a>
			</div>
		</div>
		<div class="row">
			<?php
				$feature_query = new WP_Query('post_type=socrata_apps&taxonomy=socrata_apps_persona&term=business&orderby=rand&showposts=4'); 
				$count = 0;
				while ($feature_query->have_posts()) : $feature_query->the_post();
				$count++;
			    $fourth_div = ($count%4 == 0) ? ' tile-last' : '';
			?>
			<div class="col-xs-12 col-sm-6 col-md-4<?php echo $fourth_div; ?>">
				<div class="tile tile-md">
					<div class="tile-image">
						<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
					</div>
					<div class="tile-content">				
						<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
						<h3><?php the_title(); ?></a></h3>
						<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
						<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
						<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='businessAppsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
						<div class="tile-overlay"></div>
						<a href="<?php the_permalink(); ?>" class="tile-link"></a>
					</div>
				</div>
			</div>
		    <?php endwhile;  wp_reset_postdata(); ?>
	    </div>
	</section>
<!--[if gte IE 9]>
<style type="text/css">
.gradient {
   filter: none;
}
</style>
<![endif]-->
<script>
  $('.businessAppsIcons span:contains(Socrata Certified)').addClass('icon-certified');
</script>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}

// HOMEPAGE SOCRATA CONSUMER APPS QUERY
// [home-consumer-apps]
add_shortcode('home-consumer-apps','home_consumer_apps');
function home_consumer_apps ($atts, $content = null) {
	ob_start(); 
	?>
	<section class="consumer-apps">
	<div class="row">
		<div class="col-xs-12 category-title">
			<h2>Consumer Apps</h2>
			<a href="/persona/consumers/">See All <span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
	</div>
	<div class="row tiles">
		<?php
			$feature_query = new WP_Query('post_type=socrata_apps&taxonomy=socrata_apps_persona&term=consumers&orderby=rand&showposts=4'); 
			$count = 0;
			while ($feature_query->have_posts()) : $feature_query->the_post();
			$count++;
		    $fourth_div = ($count%4 == 0) ? ' tile-last' : '';
		?>
		<div class="col-xs-12 col-sm-6 col-md-4<?php echo $fourth_div; ?>">
			<div class="tile tile-md">
				<div class="tile-image">
					<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
				</div>
				<div class="tile-content">				
					<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
					<h3><?php the_title(); ?></a></h3>
					<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
					<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
					<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='consumerAppsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
					<div class="tile-overlay"></div>
					<a href="<?php the_permalink(); ?>" class="tile-link"></a>
				</div>
			</div>
		</div>
	    <?php endwhile;  wp_reset_postdata(); ?>
    </div>
	</section>
<!--[if gte IE 9]>
<style type="text/css">
.gradient {
filter: none;
}
</style>
<![endif]-->
<script>
  $('.consumerAppsIcons span:contains(Socrata Certified)').addClass('icon-certified');
</script>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}

// HOMEPAGE SOCRATA GOVERNMENT APPS QUERY
// [home-government-apps]
add_shortcode('home-government-apps','home_government_apps');
function home_government_apps ($atts, $content = null) {
	ob_start(); 
	?>
	<section class="government-apps">
	<div class="row">
		<div class="col-xs-12 category-title">
			<h2>Government Apps</h2>
			<a href="/persona/government/">See All <span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
	</div>
	<div class="row tiles">
		<?php
			$feature_query = new WP_Query('post_type=socrata_apps&taxonomy=socrata_apps_persona&term=government&orderby=rand&showposts=4'); 
			$count = 0;
			while ($feature_query->have_posts()) : $feature_query->the_post();
			$count++;
		    $fourth_div = ($count%4 == 0) ? ' tile-last' : '';
		?>
		<div class="col-xs-12 col-sm-6 col-md-4<?php echo $fourth_div; ?>">
			<div class="tile tile-md">
				<div class="tile-image">
					<a href="<?php the_permalink(); ?>"><?php $meta = get_socrata_apps_meta(); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
				</div>
				<div class="tile-content">				
					<?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>	
					<h3><?php the_title(); ?></a></h3>
					<p class=" tile-fade"><?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
					<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
					<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<ul class='governmentAppsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
					<div class="tile-overlay"></div>
					<a href="<?php the_permalink(); ?>" class="tile-link"></a>
				</div>
			</div>
		</div>
	    <?php endwhile;  wp_reset_postdata(); ?>
    </div>
	</section>
<!--[if gte IE 9]>
<style type="text/css">
.gradient {
filter: none;
}
</style>
<![endif]-->
<script>
  $('.governmentAppsIcons span:contains(Socrata Certified)').addClass('icon-certified');
</script>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}


?>