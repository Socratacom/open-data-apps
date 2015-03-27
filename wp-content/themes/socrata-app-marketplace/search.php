<?php
/*
Template Name: Search Page
*/
get_header(); ?>

<div class="container">

    <div class="row">

        <?php get_template_part( 'sidebar-app-nav' ); ?>

        <div class="col-xs-12 col-sm-8 col-md-9"><!-- App Tiles -->

            <?php do_action( 'above_primary_content' ); ?>
          
            <div id="content" role="main">
              <h2 class="title">Search Results for &ldquo;<?php the_search_query(); ?>&rdquo;</h2>
<<<<<<< HEAD
              <div id="grid" class="row">
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
=======
              <div class="js-notice" style="display:none"><div style="color: #666; font-size:18px">No apps found.</div></div>

            <div class="row js-shuffle">
                <?php if(have_posts()): while(have_posts()): the_post();?>
                  <?php display_app_tile($post, false); ?>
                  <?php endwhile; ?>
>>>>>>> c322486de31cc6ecd7ec7d0ccbbdd4c32976887f
              </div>
              <?php else :?>
                <div class="col-xs-12">
                  We didn't find any apps that match your search.<br>
                  Please use a different term or explore the categories to the left.
                </div>
              <?php endif;?>
            </div><!-- #content -->
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
