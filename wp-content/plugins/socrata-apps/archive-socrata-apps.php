<?php get_header(); ?>

<div class="container">
    <div class="row">

        <div class="col-xs-12">
            <div class="hero">
                <div class="row">
                    <div class="col-md-8" style="text-align:left">
                        <h1 style="margin:0; font-weight: 300; margin-bottom: 20px;">Welcome to Socrata's App Marketplace.</h1>
                        <p style="margin-bottom: 20px">For government buyers and consumers the Marketplace <br>organizes and facilitates easy discoverability of engaging applications.</p>
                        <p style="margin-bottom: 5px">Civic App Developers, we want your app.</p>
                        <p style="margin-bottom: 30px;">For civic application developers it provides an ideal vehicle <br>to grow your business.</p>
<<<<<<< HEAD
                        <a href="/submit-app/" class="btn btn-outline">Submit your app</a>
=======
<<<<<<< HEAD
                        <a href="/apps/submit-app/" class="btn btn-outline">Submit your app</a>
=======
                        <a href="/submit-app/" class="btn btn-outline">Submit your app</a>
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
                    </div>
                </div>
            </div>
        </div>

    </div><!-- .row -->
</div><!-- .container -->

<div class="container">
    <div class="row">

        <?php get_template_part( 'sidebar-app-nav' ); ?>

        <div class="col-xs-12 col-sm-8 col-md-9"><!-- App Tiles -->
<<<<<<< HEAD
            
            <?php do_action( 'above_primary_content' ); ?>

=======
<<<<<<< HEAD
=======
            
            <?php do_action( 'above_primary_content' ); ?>

>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
            <div class="row">
                <div id="content">
                    <div class="col-xs-12">
                        <h2 class="title"><?php single_cat_title('',true); ?></h2>
                    </div>
                    <div id="grid" class="col-xs-12">
<<<<<<< HEAD
                        <?php get_apps_tiles_by_term('socrata_apps_persona'); ?> 
=======
<<<<<<< HEAD
                      <?php get_apps_tiles_by_term('socrata_apps_persona'); ?> 
=======
                        <?php get_apps_tiles_by_term('socrata_apps_persona'); ?> 
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
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
