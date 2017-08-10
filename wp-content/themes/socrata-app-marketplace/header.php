<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php is_front_page() ? bloginfo('name') : wp_title( '|', true, 'right' ); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../../favicon.ico">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W84T3M');</script>
    <!-- End Google Tag Manager -->
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W84T3M"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
                                        <li><h5>Categories</h5></li>
                                        <li><?php wp_nav_menu( array( 'theme_location' => 'industry', 'depth' => -1 ) ); ?></li>
                                        <li><h5>Resources</h5></li>
                                        <li><?php wp_nav_menu( array( 'theme_location' => 'resources', 'depth' => -1 ) ); ?></li>
                                    </ul>
                                </div><!-- /gn-scroller -->
                            </nav>
                        </li>
                    </ul> 

                    <a style="margin-top: 13px; padding:8px 40px;" href="/submit-app/" class="hidden-xs btn btn-warning btn-sm pull-right" role="button">Submit Your App</a>

                </div><!-- /.navbar-header -->   
            </div> 
        </div>
    </nav>
</header>

<div id="page">

