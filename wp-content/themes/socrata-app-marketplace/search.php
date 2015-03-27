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
              <div class="js-notice" style="display:none"><div style="color: #666; font-size:18px">No apps found.</div></div>

            <div class="row js-shuffle">
                <?php if(have_posts()): while(have_posts()): the_post();?>
                  <?php display_app_tile($post, false); ?>
                  <?php endwhile; ?>
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
