<?php get_header(); ?>

<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="hero">
        <div class="row">
            <div class="col-md-8" style="text-align:left">
                <h1 style="margin:0; font-weight: 300; margin-bottom: 20px;">Welcome to Socrata's App Marketplace.</h1>
                <p style="font-weight: 300; line-height: 1.5; font-size: 18px; margin-bottom: 20px">For government buyers and consumers the Marketplace <br>organizes and facilitates easy discoverability of engaging applications.</p>
                <p style="font-weight: 300; line-height: 1.5; font-size: 18px; margin-bottom: 5px">Civic App Developers, we want your app.</p>
                <p style="font-weight: 300; line-height: 1.5; font-size: 18px; margin-bottom: 30px;">For civic application developers it provides an ideal vehicle <br>to grow your business.</p>
                <a href="/apps/submit-app/" class="btn btn-outline">Submit your app</a>
            </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="container">

    <div class="row">

        <div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
            <?php get_template_part( 'sidebar-app-nav' ); ?>
        </div>

        <div class="col-xs-12 col-sm-8 col-md-9"><!-- App Tiles -->
            <div class="row">
                <div id="content">
                    
                    <div class="col-xs-12">
                        <h2 class="title"><?php single_cat_title('',true); ?></h2>
                    </div>

                    <div id="grid" class="col-xs-12">
                      <?php get_apps_tiles_by_term('socrata_apps_persona'); ?> 
                    </div>

                </div>
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

<?php get_footer(); ?>
