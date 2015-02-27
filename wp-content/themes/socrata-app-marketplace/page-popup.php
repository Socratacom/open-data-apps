<?php
/*
Template Name: Pop up page
*/
?>

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

<div class="container">
  <div class="row">
    <div class="col-sm-12" style="padding: 10px 40px 30px 40px"><!-- Content Column -->
      <?php if (have_posts()); ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content()?>
      <?php endwhile; ?>
    </div>
  </div>
</div>

