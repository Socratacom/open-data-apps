<?php get_header(); ?>

<div class="container">
	<div class="row">

		<?php get_template_part( 'sidebar-app-nav' ); ?>

		<div class="col-xs-12 col-sm-8 col-md-9"><!-- App Tiles -->

			<?php do_action( 'above_primary_content' ); ?>

			<div class="row">
				<div id="content">
					
					<div class="col-xs-12">
						<h2 class="title"><?php single_cat_title('',true); ?></h2>
					</div>

					<div class="col-xs-12">
<<<<<<< HEAD

						<div class="js-notice" style="display:none"><div style="color: #aaa">no apps found.</div></div>

						<div class="row js-shuffle">
					
						<?php if(have_posts()): while(have_posts()): the_post();

						// Get app meta field values
						$meta = get_socrata_apps_meta(get_the_ID());

						// setting up data attributes for filtering						
						$data_cost = '';
						$data_certified = '';
						$data_groups_array = array();
						$data_groups_string = '';

						$data_platform_array = array();
						$data_platform_string = '';						

						if ($meta[19] !== 'Paid App') {
							$data_cost = 'data-cost="free" ';
						}
						if ($meta[16] === '1') {
							$data_certified = 'data-certified="certified" ';
						}
						
						$meta[17][] = 'all';
						if (isset($meta[17]) && is_array($meta[17])) {
							for ($i=0; $i < count($meta[17]); $i++) {
								$data_platform_string .= '"' . str_replace(" ", "-", strtolower($meta[17][$i])) . '"' . ($i === count($meta[17]) - 1 ? '' : ', ');
							}
						}
						$data_platform = "data-platform='[$data_platform_string]'";

						// Get terms list
						$term_list = wp_get_post_terms($post->ID, 'socrata_apps_resources', array("fields" => "names"));

						// Change button label based on whether is an app or template
						$button_label = count($term_list) > 0 ? 'View Template' : 'View App'; ?>

						<div class="col-xs-12 col-sm-6 col-md-4 item" <?php echo $data_cost; echo $data_certified; echo $data_platform; ?>>
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

						<?php endwhile; ?><?php endif;?>

=======
						<div class="row js-shuffle">
							<?php if(have_posts()): while(have_posts()): the_post(); ?>
							<?php display_app_tile($post, false); ?>
							<?php endwhile; ?><?php endif;?>
>>>>>>> c322486de31cc6ecd7ec7d0ccbbdd4c32976887f
						</div>
					</div>

					<div class="col-xs-12">
						<ul class="pagination hidden-xs hidden-sm hidden-md hidden-lg">
							<li class="older"><?php next_posts_link('&laquo; Next') ?></li>
							<li class="newer"><?php previous_posts_link('Prev &raquo;') ?></li>
						</ul>
					</div>

				</div><!-- #content -->
			</div><!-- .row -->

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

<<<<<<< HEAD
<script>

	var Exports = {
	  Modules : {}
	};

	Exports.Modules.Gallery = (function($, undefined) {
		
		var $grid,

		$cost,
		$certified,
		$platform,

		cost = [],
		certified = [],
		platform = [],
		device = [],

		init = function() {
			setVars();
			initFilters();
			initShuffle();
		},

		setVars = function() {
			$grid = $('.js-shuffle');
			$cost = $('.js-cost');
			$certified = $('.js-certified');
			$platform = $('.js-platform');
			$device = $('.js-device');
		},

		initShuffle = function() {

			// instantiate the plugin
			$grid.shuffle({
				speed : 250,
				delimeter: ',',
				easing : 'cubic-bezier(0.165, 0.840, 0.440, 1.000)' // easeOutQuart
			});

		},

		initFilters = function() {

			// cost
			$cost.find('input').on('change', function() {
				var $checked = $cost.find('input:checked'),
				groups = [];

				if ($checked.length !== 0) {
					$checked.each(function() {
						groups.push(this.value);
					});
				}
				cost = groups;

				filter();
			});

			// certified
			$certified.find('input').on('change', function() {
				var $checked = $certified.find('input:checked'),
				groups = [];

				if ($checked.length !== 0) {
					$checked.each(function() {
						groups.push(this.value);
					});
				}
				certified = groups;

				filter();
			});

			// platform
			$platform.find('select').on('change', function() {
				var $select = $platform.find('select'),
				groups = [];
				platform = $select.val();
				filter();
			});

			// device
			$device.find('select').on('change', function() {
				var $select = $device.find('select'),
				groups = [];
				device = $select.val();
				filter();
			});

		},

		filter = function() {
			if ( hasActiveFilters() ) {
				$grid.shuffle('shuffle', function($el) {
					return itemPassesFilters( $el.data() );
				});
			} else {
				$grid.shuffle( 'shuffle', 'all' );
			}

			if ($grid.find('.filtered').length === 0) {
				$('.js-notice').show();
			} else {
				$('.js-notice').hide();
			}
		},

		itemPassesFilters = function(data) {

			if ( cost.length > 0 && !valueInArray(data.cost, cost) ) {
				return false;
			}

			if ( certified.length > 0 && !valueInArray(data.certified, certified) ) {
				return false;
			}

			if ( platform.length > 0 ) {
				for (i=0; i < data.platform.length; i++) {
					if (platform === data.platform[i]) {
						return true;
					}
				}
				return false;
			}

			return true;
		},

		hasActiveFilters = function() {
			return cost.length > 0 || certified.length > 0 || platform.length > 0;
		},

		valueInArray = function(value, arr) {
			return $.inArray(value, arr) !== -1;
		};

		return {
			init: init
		};

	}(jQuery));

	$(document).ready(function() {
	  Exports.Modules.Gallery.init();
	});

</script>

=======
>>>>>>> c322486de31cc6ecd7ec7d0ccbbdd4c32976887f
<?php get_footer(); ?>
