<?php

function script_enqueues()
{
  wp_deregister_script( 'jquery' );
  wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', false, null, false);
  wp_enqueue_script( 'jquery' );

  wp_register_style('bootstrap-css', get_template_directory_uri() . '/css/bootstrap.min.css', false, null);
  wp_enqueue_style('bootstrap-css');

  wp_register_style('custom-css', get_template_directory_uri() . '/css/custom.css', array('bootstrap-css'), null);
  wp_enqueue_style('custom-css');

  wp_register_style( 'google-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600', false, null);
  wp_enqueue_style('google-fonts');

  wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr-2.6.2.min.js', false, null, true);
  wp_enqueue_script('modernizr');

  wp_register_script('html5shiv.js', get_template_directory_uri() . '/js/html5shiv.js', false, null, true);
  wp_enqueue_script('html5shiv.js');

  wp_register_script('respond', get_template_directory_uri() . '/js/respond.min.js', false, null, true);
  wp_enqueue_script('respond');

  wp_register_script('bootstrap-js', get_template_directory_uri() . '/js/bootstrap.js', false, null, true);
  wp_enqueue_script('bootstrap-js');

  wp_register_script('classie-js', get_template_directory_uri() . '/js/classie.js', false, null, true);
  wp_enqueue_script('classie-js');

  wp_register_script('gnmenu-js', get_template_directory_uri() . '/js/gnmenu.js', false, null, true);
  wp_enqueue_script('gnmenu-js');

  /* Cookie Bar */

  wp_register_style('cookiebar-css', get_template_directory_uri() . '/css/cookiebar.css', false, null);
  wp_enqueue_style('cookiebar-css');

  wp_register_script('cookiebar-js', get_template_directory_uri() . '/js/jquery.cookieBar.js', false, null, true);
  wp_enqueue_script('cookiebar-js');

}
add_action('wp_enqueue_scripts', 'script_enqueues', 100);

?>