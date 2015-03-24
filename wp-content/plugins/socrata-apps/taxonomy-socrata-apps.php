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
						<div class="row js-shuffle">
							<?php if(have_posts()): while(have_posts()): the_post(); ?>
							<?php display_app_tile($post, false); ?>
							<?php endwhile; ?><?php endif;?>
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

<?php get_footer(); ?>
