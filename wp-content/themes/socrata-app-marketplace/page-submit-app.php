<?php
/*
Template Name: Submit App
*/

get_header(); ?>

<div class="container content">
	<div class="row">
		<div class="col-md-12 gform_wrapper">
			<div class="gf_page_steps" id="gf_page_steps_2">
                <div class="gf_step gf_step_next gf_step_pending" id="gf_step_2_1"><span class="gf_step_number">1</span>&nbsp;Point of Contact Information</div>
                <div class="gf_step gf_step_pending" id="gf_step_2_2"><span class="gf_step_number">2</span>&nbsp;App Information</div>
                <div class="gf_step gf_step_pending" id="gf_step_2_3"><span class="gf_step_number">3</span>&nbsp;App Details</div>
                <div class="gf_step gf_step_pending" id="gf_step_2_4"><span class="gf_step_number">4</span>&nbsp;Image Assets</div>
                <div class="gf_step gf_step_pending" id="gf_step_2_5"><span class="gf_step_number">5</span>&nbsp;Additional App Information</div>
                <div class="gf_step gf_step_last gf_step_pending" id="gf_step_2_6"><span class="gf_step_number">6</span>&nbsp;App Certification</div>
                <div class="gf_step_clear"></div>
            </div>
            <div class="gform_body">
		  		<?php if (have_posts()); ?>
			    <?php while ( have_posts() ) : the_post(); ?>
				    <?php the_content()?>
			    <?php endwhile; ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>