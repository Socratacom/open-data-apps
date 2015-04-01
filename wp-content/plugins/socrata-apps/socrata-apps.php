<?php
/*
Plugin Name: Socrata Apps
Plugin URI: http://socrata.com/
Description: This is the Apps management plugin. Organizes all entered apps.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
*/
include_once('metaboxes/meta_box.php');
include_once('inc/fields.php');

// --------------------------------------------------------------------
// REGISTER POST TYPE
// --------------------------------------------------------------------
add_action( 'init', 'create_socrata_apps' );
function create_socrata_apps() {
  register_post_type( 'socrata_apps',
    array(
      'labels' => array(
        'name' => 'Apps',
        'singular_name' => 'Apps',
        'menu_name' => 'Apps',
        'add_new' => 'Add New App',
        'add_new_item' => 'Add New App',
        'edit' => 'Edit App',
        'edit_item' => 'Edit App',
        'new_item' => 'New App',
        'view' => 'View',
        'view_item' => 'View App',
        'search_items' => 'Search Apps',
        'not_found' => 'Not found',
        'not_found_in_trash' => 'Not found in Trash',
        'parent' => 'Parent Apps'
      ),
      'public' => true,
      'menu_position' => 5,
      'supports' => array( 'title', 'revisions', 'comments' ),
      'taxonomies' => array( 'socrata_apps_persona', 'socrata_apps_industry', 'socrata_apps_resources' ),
      'menu_icon' => '',
      'has_archive' => true,
      'rewrite' => array('with_front' => false, 'slug' => 'catalog')
    )
  );
}

function plugin_scripts() {

  wp_register_style( 'slick-carousel', plugins_url( '/assets/slick/slick.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'slick-carousel' );

  wp_enqueue_style( 'slick-carousel-theme', plugins_url( '/assets/slick/slick-theme.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'slick-carousel-theme' );

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
  wp_register_style( 'socrata-apps', plugins_url( '/style.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'socrata-apps' );

  wp_register_script( 'slick-carousel-js', plugins_url( '/assets/slick/slick.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'slick-carousel-js' );
  
  wp_register_script( 'readmore-js', plugins_url( '/assets/readmore/readmore.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'readmore-js' );

  wp_enqueue_script( 'shuffle-js', plugins_url( '/assets/shuffle/jquery.shuffle.min.js' , __FILE__ ), array(), false, true );
=======
>>>>>>> staging
=======
>>>>>>> staging
  wp_register_script( 'slick-carousel-js', plugins_url( '/assets/slick/slick.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'slick-carousel-js' );

  wp_register_script( 'readmore-js', plugins_url( '/assets/readmore/readmore.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'readmore-js' );

  wp_enqueue_script( 'modernizr', plugins_url( '/assets/shuffle/jquery.shuffle.modernizr.min.js' , __FILE__ ), array(), false, false );

  wp_register_script( 'shuffle-js', plugins_url( '/assets/shuffle/jquery.shuffle.min.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'shuffle-js' );  

  wp_enqueue_script( 'bootstrap-select-js', plugins_url( '/assets/bootstrap-select/js/bootstrap-select.min.js' , __FILE__ ), array(), false, true );
  wp_enqueue_script( 'bootstrap-select-js' );

  wp_enqueue_script( 'modernizr', plugins_url( '/assets/shuffle/jquery.shuffle.modernizr.min.js' , __FILE__ ), array(), false, false );

  wp_enqueue_script( 'shuffle-js', plugins_url( '/assets/shuffle/jquery.shuffle.min.js' , __FILE__ ), array(), false, true );  

  wp_register_style( 'bootstrap-select-css', plugins_url( '/assets/bootstrap-select/css/bootstrap-select.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'bootstrap-select-css' );

  wp_register_style( 'socrata-apps', plugins_url( '/style.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'socrata-apps' );
<<<<<<< HEAD
<<<<<<< HEAD
  
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
>>>>>>> staging

}
add_action( 'wp_enqueue_scripts', 'plugin_scripts' );

// MENU ICON
// Using Dashicon Font http://melchoyce.github.io/dashicons/
add_action( 'admin_head', 'add_menu_icons_styles' );

function add_menu_icons_styles(){
?>
<style>
#adminmenu .menu-icon-socrata_apps div.wp-menu-image:before {
  content: '\f471';
}
</style>
<?php
}

// --------------------------------------------------------------------
// REGISTER TAXONOMIES
// --------------------------------------------------------------------
add_action( 'init', 'socrata_apps_persona', 0 );
function socrata_apps_persona() {
  register_taxonomy(
    'socrata_apps_persona',
    'socrata_apps',
    array(
      'labels' => array(
        'name' => 'Who is the app for?',
        'menu_name' => 'Persona',
        'add_new_item' => 'Add New',
        'new_item_name' => "New Persona"
      ),
      'show_ui' => true,
      'show_tagcloud' => false,
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'show_admin_column' => true,
      'rewrite' => array('with_front' => false, 'slug' => 'persona')
    )
  );
}

add_action( 'init', 'socrata_apps_industry', 0 );
function socrata_apps_industry() {
  register_taxonomy(
    'socrata_apps_industry',
    'socrata_apps',
    array(
      'labels' => array(
        'name' => 'Industry/Vertical',
        'menu_name' => 'Industry',
        'add_new_item' => 'Add New',
        'new_item_name' => "New Industry"
      ),
      'show_ui' => true,
      'show_tagcloud' => false,
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'show_admin_column' => true,
      'rewrite' => array('with_front' => false, 'slug' => 'industry')
    )
  );
}

add_action( 'init', 'socrata_apps_resources', 0 );
function socrata_apps_resources() {
  register_taxonomy(
    'socrata_apps_resources',
    'socrata_apps',
    array(
      'labels' => array(
        'name' => 'Resources',
        'menu_name' => 'Resources',
        'add_new_item' => 'Add New',
        'new_item_name' => "New Resource"
      ),
      'show_ui' => true,
      'show_tagcloud' => false,
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'show_admin_column' => true,
      'rewrite' => array('with_front' => false, 'slug' => 'resources')
    )
  );
}

// --------------------------------------------------------------------
// TEMPLATE PATHS
// --------------------------------------------------------------------
add_filter( 'template_include', 'socrata_apps_single_template_function', 1 );
function socrata_apps_single_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_single() ) {
<<<<<<< HEAD
<<<<<<< HEAD
      $template_path = plugin_dir_path( __FILE__ ) . 'single-socrata-apps.php';
=======
<<<<<<< HEAD
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'single-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'single-socrata-apps.php';
      }
=======
      $template_path = plugin_dir_path( __FILE__ ) . 'single-socrata-apps.php';
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
      $template_path = plugin_dir_path( __FILE__ ) . 'single-socrata-apps.php';
>>>>>>> staging
    }
  }
  return $template_path;
}

add_filter( 'template_include', 'socrata_apps_archive_template_function', 1 );
function socrata_apps_archive_template_function( $template_path ) {
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
  if ( get_post_type() == 'socrata_apps' || is_front_page() ) {
    if ( is_archive() || is_front_page() ) {
=======
>>>>>>> staging
=======
>>>>>>> staging
  if ( is_front_page() || get_post_type() == 'socrata_apps' ) {
    if ( is_front_page() || is_archive() ) {
      $template_path = plugin_dir_path( __FILE__ ) . 'archive-socrata-apps.php';
    }
  }
  return $template_path;
}

add_filter( 'template_include', 'socrata_apps_category_template_function', 1 );
function socrata_apps_category_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_tax() ) {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
=======
>>>>>>> staging
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'taxonomy-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'taxonomy-socrata-apps.php';
      }
    }
  }
  return $template_path;
}

<<<<<<< HEAD
<<<<<<< HEAD
add_filter( 'template_include', 'socrata_apps_category_template_function', 1 );
function socrata_apps_category_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_tax() ) {
>>>>>>> staging
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'taxonomy-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'taxonomy-socrata-apps.php';
      }
    }
  }
  return $template_path;
}

<<<<<<< HEAD
=======
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
>>>>>>> staging

// --------------------------------------------------------------------
// FUNCTIONS FOR DISPLAYING APPS
// --------------------------------------------------------------------
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
function display_app_tile($app, $term) { 

  $meta = get_socrata_apps_meta($app->ID);
  $size = $meta[23] === 'yes' && $term === 'featured' ? 'tile-lg' : 'tile-md';

  ?>
  <div class="shuffle col-xs-12 <?php if ($meta[23] === 'yes' && $term === 'featured') { echo 'col-sm-12 col-md-8'; } else { echo 'col-sm-6 col-md-4'; } ?>">
    <div class="tile <?php echo $size; ?>" <?php echo $data_groups; ?>>
      <div class="tile-image">
        <a href="<?php echo get_permalink($app->ID); ?>">
          <?php echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?>
        </a>
      </div>
      <div class="tile-content">        
        <?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>  
        <h3><?php echo $app->post_title; ?></a></h3>
        <p class="tile-fade"><?php if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
        <a href="<?php echo get_permalink($app->ID); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
=======
>>>>>>> staging
=======
>>>>>>> staging
function display_app_tile($app, $is_featured) {

  // Get app meta field values
  $meta = get_socrata_apps_meta($app->ID);
  $size = $meta[23] === 'yes' && $is_featured ? 'tile-lg' : 'tile-md';

  // setting up data attributes for filtering
  $data_cost = '';
  $data_certified = '';
  $data_groups_array = array();
  $data_groups_string = '';
  $data_platform_array = array();
  $data_platform_string = '';

  // Create filter attributes string
  if ($meta[19] !== 'Paid App') {
    $data_cost = 'data-cost="free" ';
  }
  if ($meta[16] === '1') {
    $data_certified = 'data-certified="certified" ';
  }
  $meta[17][] = 'all';
  if (isset($meta[17]) && is_array($meta[17])) {
    for ($i=0; $i < count($meta[17]); $i++) {
      $data_platform_string .= '"' . str_replace(" ", "-", strtolower($meta[17][$i])) . '"' . ($i === count($meta[17]) - 1 ? '' : ', ');
    }
  }
  $data_platform = "data-platform='[$data_platform_string]'";
  $filter_attributes = $data_cost . $data_certified . $data_platform;

  // Get terms list
  $term_list = wp_get_post_terms($post->ID, 'socrata_apps_resources', array("fields" => "names"));

  // Change button label based on whether is an app or template
  $button_label = count($term_list) > 0 ? 'View Template' : 'View App';

  ?>
  <div class="shuffle-item col-xs-12 <?php if ($meta[23] === 'yes' && $is_featured) { echo 'col-sm-12 col-md-8 col-lg-8'; } else { echo 'col-sm-6 col-md-4 col-lg-4'; } ?> item" <?php echo $filter_attributes; ?>>
    <div class="tile <?php echo $size; ?>" <?php echo $data_groups; ?>>
      <div class="tile-image">
        <a href="<?php echo get_permalink($app->ID); ?>" style="position:relative; width:100%; height:0; padding-top:67%; display:block;">
          <?php echo wp_get_attachment_image($meta[5], $meta[23] === 'yes' && $is_featured ? 'screen-lg' : 'screen-md', false, array('class' => 'img-responsive')); ?>
        </a>
      </div>
      <div class="tile-content">
        <?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>
        <h3><?php echo $app->post_title; ?></a></h3>
        <p class="tile-fade"><?php if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
        <a href="<?php echo get_permalink($app->ID); ?>" class="btn btn-primary btn-xs tile-btn tile-fade"><?php echo $button_label; ?></a>
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
>>>>>>> staging
        <?php if ($meta[16]) echo "<ul class='appsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
        <div class="tile-overlay"></div>
        <a href="<?php echo get_permalink($app->ID); ?>" class="tile-link"></a>
      </div>
    </div>
  </div>

<?php }

<<<<<<< HEAD
<<<<<<< HEAD
function get_apps_tiles_by_term($taxonomies) {
=======
<<<<<<< HEAD
function get_apps_tiles_by_term($term) {
=======
function get_apps_tiles_by_term($taxonomies) {
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
function get_apps_tiles_by_term($taxonomies) {
>>>>>>> staging

  $args = array(
      'hide_empty' => true,
      'parent' => 0
  );

  $loaded_apps = array();

<<<<<<< HEAD
<<<<<<< HEAD
  $terms = get_terms( $taxonomies, $args );
=======
<<<<<<< HEAD
  $terms = get_terms( $term, $args );
=======
  $terms = get_terms( $taxonomies, $args );
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
  $terms = get_terms( $taxonomies, $args );
>>>>>>> staging

  if (!is_array($terms)) {
    return;
  }

<<<<<<< HEAD
<<<<<<< HEAD
  @usort($terms, function($a, $b) {
=======
<<<<<<< HEAD
  usort($terms, function($a, $b) {
=======
  @usort($terms, function($a, $b) {
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
  @usort($terms, function($a, $b) {
>>>>>>> staging
    $a = get_field('order_id', $a);
    $b = get_field('order_id', $b);
    return $a - $b;
  });

  foreach ( $terms as $term ) {

      $term_loaded_apps = array();
      $skipped_apps = array();

      if ($term->count == 0 || $term->slug === 'other') {
          continue;
      }

      $args = array(
          'posts_per_page' => -1,
          'post_type' => 'socrata_apps',
          'order_by' => 'modified',
          'post_status' => 'publish',
          'tax_query' => array(
                array(
                    'taxonomy' => 'socrata_apps_persona',
                    'field' => 'slug',
                    'terms' => $term->slug
                )
            )
      );
      $apps = get_posts( $args );

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
      $title = $term->slug === 'featured' ? 'Featured Apps' : 'Apps for ' . $term->name;

      echo '<h2 style="display: inline-block" class="title">'. $title .'</h2>';

      echo '<div class="'. $term->slug .'-arrows" style="position:relative; display: inline-block; top:-8px; left: 40px;"></div>';

      echo '<div class="row carousel '. $term->slug .'" style="margin-bottom: 30px;">';

      foreach ($apps as $app) {

        if (array_key_exists($app->ID, $loaded_apps)) {
            $skipped_apps[$app->ID] = $app;
            continue;
        }

        echo display_app_tile($app, $term->slug);
        $loaded_apps[$app->ID] = $app;
      }

      foreach ($skipped_apps as $app) {
        echo display_app_tile($app, $term->slug);
        $loaded_apps[$app->ID] = $app;
      }

      echo '</div>';

      if ($term->slug !== 'featured') {
      echo '<script>
              $(document).ready(function(){
                $(\'.row.carousel.'. $term->slug .'\').slick({
                  slidesToShow: 3,
                  slidesToScroll: 3,
                  appendArrows: \'.'. $term->slug .'-arrows\',
                  responsive: [
                      {
                        breakpoint: 1024,
                        settings: {
                          slidesToShow: 3,
                          slidesToScroll: 3
                        }
                      },
                      {
                        breakpoint: 600,
                        settings: {
                          slidesToShow: 2,
                          slidesToScroll: 2
                        }
                      },
                      {
                        breakpoint: 480,
                        settings: {
                          slidesToShow: 1,
                          slidesToScroll: 1
                        }
                      }
                  ]
                });
              });
            </script>';  
      }
      
  }

}
=======
>>>>>>> staging
=======
>>>>>>> staging
      if ($term->slug !== 'featured') {
        @usort($apps, function($a, $b) {
          $a = get_field('carousel_order', $a) === false ? 100 : intval(get_field('carousel_order', $a));
          $b = get_field('carousel_order', $b) === false ? 100 : intval(get_field('carousel_order', $b));
          if ($a < $b || $a == $b) {
            return 0;
          } else {
            return 1;
          }
        });
      }

      $title = $term->slug === 'featured' ? 'Featured Apps' : 'Apps for ' . $term->name;

      echo '<h2 style="display: inline-block; float:left;" class="title">'. $title .'</h2>';

      echo '<div class="'. $term->slug .'-arrows" style="float: right; position: relative; margin-right: 74px; margin-top: 0;"></div>';

      echo '<div style="clear:both;"></div>';

      echo '<div data-category="'. $term->slug .'" class="row carousel '. $term->slug .' '. ($term->slug === 'featured' ? 'js-shuffle shuffle--fluid' : '') . '" style="margin-bottom: 30px;">';

      foreach ($apps as $app) {

        // This foreach statement and the one below (BLOCK AB1), will force apps that have already been loaded to the end of the array.
        // if (array_key_exists($app->ID, $loaded_apps)) {
        //     $skipped_apps[$app->ID] = $app;
        //     continue;
        // }

        echo display_app_tile($app, ($term->slug === 'featured'));

        $loaded_apps[$app->ID] = $app;
      }

      // BLOCK AB1
      // foreach ($skipped_apps as $app) {
      //   echo display_app_tile($app, $term->slug);
      //   $loaded_apps[$app->ID] = $app;
      // }

      ?>

      <?php if ($term->slug === 'featured') { ?>
        <div class="shuffle__sizer col-xs-1"></div>
      <?php } ?>

      <?php

      echo '</div>';

      if ($term->slug !== 'featured') { ?>
      <script>
        $(document).ready(function(){

          $('.row.carousel.<?php echo $term->slug; ?>').slick({
            slidesToShow: 3,
            slidesToScroll: 3,
            appendArrows: '.<?php echo $term->slug; ?>-arrows',
            responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3
              }
            },
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            },
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
            ]
          });

        });
      </script>
      <?php } ?>

  <?php } ?>

  <script>
    $(document).ready(function(){

      $('.js-cost input, .js-certified input, .js-platform select').on('change', function(e) {
        var costIsChecked = $('.js-cost input').is(':checked');
        var certifiedIsChecked = $('.js-certified input').is(':checked');
        var selectedPlatform = $('.js-platform select').val();
        $('.row.carousel').not('.featured').each(function(index) {
          var rowCategory = '.row.carousel.' + $(this).attr('data-category');
          $(rowCategory).slick('slickUnfilter');
          $(rowCategory).slick('slickFilter', function() {
            var result = [];
            if (costIsChecked) {
              result.push($(this).attr('data-cost') === 'free');
            }
            if (certifiedIsChecked) {
              result.push($(this).attr('data-certified') === 'certified');
            }
            platformArray = jQuery.parseJSON( $(this).attr('data-platform'));
            result.push($.inArray(selectedPlatform, platformArray) == '-1' ? false : true);
            return ($.inArray(false, result) == '-1' ? true : false);
          });

          if($(rowCategory).slick('getSlick').slideCount == 0){
            $(this).addClass('no-results');
          } else {
            $(this).removeClass('no-results');
          }
        });
      }).change();

    });
  </script>

<?php }
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
>>>>>>> staging


// --------------------------------------------------------------------
// FUNCTION FOR DISPLAYING THE FILTER BAR
// --------------------------------------------------------------------
function display_filter_bar($post_ID) {
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
   include('filter-bar.php');
}
add_action( 'above_primary_content', 'display_filter_bar' );
=======
>>>>>>> staging
=======
>>>>>>> staging
  if ( !is_single() ) {
    include('filter-bar.php');
  }
}
add_action('above_primary_content', 'display_filter_bar');

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
>>>>>>> staging
