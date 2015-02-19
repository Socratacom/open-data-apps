<?php get_header(); ?>

<div class="container">

	<div class="row">

		<div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
			<?php get_template_part( 'sidebar-app-nav' ); ?>
		</div>

		<div class="col-xs-12 col-sm-8 col-md-9"><!-- App Tiles -->

			<?php do_action( 'above_primary_content' ); ?>

			<div class="row">
				<div id="content">
					<div class="col-xs-12">
						<h2 class="title"><?php single_cat_title('',true); ?></h2>
					</div>

					<div id="grid">
					
						<?php if(have_posts()): while(have_posts()): the_post();?>
						<?php

						$meta = get_socrata_apps_meta(get_the_ID());
						$data_groups_array = array();

						if ($meta[19] !== 'Paid App') {
							$data_groups_array[] = 'free';
						}

						$data_groups = '[';
						for ($i = 0; $i < count($data_groups_array); $i++) {
							$data_groups .= '"'. $data_groups_array[$i];
							$data_groups .= ($i < (count($data_groups_array) - 1) ? '", ' : '"');
						}
						$data_groups .= ']';

						$term_list = wp_get_post_terms($post->ID, 'socrata_apps_resources', array("fields" => "names"));

						$button_label = count($term_list) > 0 ? 'View Template' : 'View App';
						
						?>
						<div class="col-xs-12 col-sm-6 col-md-4 item" data-groups="free, cat">
							<div class="tile tile-md">
								<div class="tile-image">
									<a href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?></a>
								</div>
								<div class="tile-content">        
									<?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>  
									<h3><?php the_title(); ?></a></h3>
									<p class=" tile-fade"><?php if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
									<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-xs tile-btn tile-fade"><?php echo $button_label; ?></a>
									<?php if ($meta[16]) echo "<ul class='appsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
									<div class="tile-overlay"></div>
									<a href="<?php the_permalink(); ?>" class="tile-link"></a>
								</div>
							</div>
						</div>
						<?php endwhile; ?>
					</div>
				</div>
			<div class="col-xs-12">
				<ul class="pagination hidden-xs hidden-sm hidden-md hidden-lg">
					<li class="older"><?php next_posts_link('&laquo; Older') ?></li>
					<li class="newer"><?php previous_posts_link('Newer &raquo;') ?></li>
				</ul>
			</div>
		<?php endif;?>

	</div>
</div>    

</div><!-- .row -->
</div><!-- .container -->
<!--[if gte IE 9]>
<style type="text/css">
.gradient {
filter: none;
}
</style>
<![endif]-->
<script>
$('.appsIcons span:contains(Socrata Certified)').addClass('icon-certified');
</script>

<script>
// $(document).ready(function() {
//   var $grid = $('#grid'),
//       $sizer = $grid.find('.item');

//   $grid.shuffle({
//   	group: 'free',
//     itemSelector: '.item',
//     delimeter: ',',
//     sizer: $sizer
//   });
// });
</script>

<?php get_footer(); ?>
