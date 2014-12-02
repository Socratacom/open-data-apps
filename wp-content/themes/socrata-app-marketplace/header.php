<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php is_front_page() ? bloginfo('name') : wp_title( '|', true, 'right' ); ?></title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="../../favicon.ico">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php global $wp_google_tag_manager;
if(is_object($wp_google_tag_manager) && is_a($wp_google_tag_manager,"WpGoogleTagManager")){
$wp_google_tag_manager->output_manual();
} ?>
<!--[if lt IE 8]>
<div class="alert alert-warning">
  You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
</div>
<![endif]-->
<header>
  <nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 navbar-header">           
          <a class="color-logo lg-logo" href="<?php echo home_url('/'); ?>"></a>
          <!-- Mobile Menu -->
          <ul id="gn-menu" class="gn-menu-main hidden-sm hidden-md hidden-lg">
            <li class="gn-trigger">
              <a class="gn-icon gn-icon-menu"><span>Menu</span></a>
              <nav class="gn-menu-wrapper">
                <div class="gn-scroller">
                  <ul class="gn-menu">                  
                    <li><h5>Apps for...</h5></li>              
                    <li class="persona"><?php wp_nav_menu( array( 
                          'theme_location' => 'persona',
                          'depth' => -1,
                          )); ?></li>
                    <li><h5>Industry/Vertical</h5></li>              
                    <li><?php wp_nav_menu( array( 
                          'theme_location' => 'industry',
                          'depth' => -1,
                          )); ?></li>
                  </ul>
                </div><!-- /gn-scroller -->
              </nav>
            </li>
          </ul> 
        </div><!-- /.navbar-header -->   
      </div> 
    </div>
  </nav>
</header>
<div id="page">
<!-- Cookiebar Notification -->
<script type="text/javascript">
  $(document).ready(function() {
    $('.cookie-message').cookieBar({ closeButton : '.my-close-button' });
  });
</script>
  <div class="cookie-message hidden-xs hidden-sm">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <p><strong>Civic App Developers</strong>, we want your app. <a href="/apps/submit-app/">Submit Your App Here</a>.</p>
          <a class="my-close-button"><span class="glyphicon glyphicon-remove"></span></a>
        </div>
      </div>
    </div>
  </div>