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
      'taxonomies' => array( '' ),
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

  wp_register_style( 'socrata-apps', plugins_url( '/style.css' , __FILE__ ), array('custom-css', 'slick-carousel'), false, 'all' );
  wp_enqueue_style( 'socrata-apps' );

  wp_enqueue_script( 'slick-carousel-js', plugins_url( '/assets/slick/slick.js' , __FILE__ ), array(), false, true );
  
  wp_enqueue_script( 'readmore-js', plugins_url( '/assets/readmore/readmore.js' , __FILE__ ), array(), false, true );
  

  // wp_enqueue_style( 'shuffle-css', plugins_url( '/assets/Shuffle-master/css/style.css' , __FILE__ ) );
  // wp_enqueue_script( 'shuffle-js', plugins_url( '/assets/Shuffle-master/dist/jquery.shuffle.min.js' , __FILE__ ), array(), false, true );

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
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'single-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'single-socrata-apps.php';
      }
    }
  }
  return $template_path;
}

add_filter( 'template_include', 'socrata_apps_archive_template_function', 1 );
function socrata_apps_archive_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' || is_front_page() ) {
    if ( is_archive() || is_front_page() ) {
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'archive-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'archive-socrata-apps.php';
      }
    }
  }
  return $template_path;
}

add_filter( 'template_include', 'socrata_apps_category_template_function', 1 );
function socrata_apps_category_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_tax() ) {
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


function display_app_tile($app) { ?>
                  
  <div class="tile tile-md">
    <div class="tile-image">
      <a href="<?php echo get_permalink($app->ID); ?>">
        <?php $meta = get_socrata_apps_meta($app->ID); echo wp_get_attachment_image($meta[5], 'screen-sm', false, array('class' => 'img-responsive')); ?>
      </a>
    </div>
    <div class="tile-content">        
      <?php $meta = get_socrata_apps_meta($app->ID); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-sm', false, array('class' => 'tile-icon')); ?>  
      <h3><?php echo $app->post_title; ?></a></h3>
      <p class=" tile-fade"><?php $meta = get_socrata_apps_meta($app->ID); if ($meta[14]) echo "<strong>$meta[9]</strong><br>$meta[14]" ; ?></p>
      <a href="<?php echo get_permalink($app->ID); ?>" class="btn btn-primary btn-xs tile-btn tile-fade">View App</a>
      <?php $meta = get_socrata_apps_meta($app->ID); if ($meta[16]) echo "<ul class='appsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
      <div class="tile-overlay"></div>
      <a href="<?php echo get_permalink($app->ID); ?>" class="tile-link"></a>
    </div>
  </div>

<?php }

function get_apps_tiles_by_term($term) {

  $args = array(
      'hide_empty' => true,
      'parent' => 0
  );

  $terms = get_terms( $term, $args );

  $loaded_apps = array();

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

      echo '<h2 class="title">Apps for ' . $term->name . '</h2>';

      echo '<div class="row carousel '. $term->slug .'" style="margin-bottom: 60px; margin-left: 20px; margin-right: 20px">';

      foreach ($apps as $app) {

        if (array_key_exists($app->ID, $loaded_apps)) {
            $skipped_apps[$app->ID] = $app;
            continue;
        }

        echo '<div class="col-xs-4" data-groups=\'["photography"]\'>';
        echo  display_app_tile($app);
        echo '</div>';

        $loaded_apps[$app->ID] = $app;

      }

      foreach ($skipped_apps as $app) {
        echo '<div class="col-xs-4" data-groups=\'["photography"]\'>';
        echo  display_app_tile($app);
        echo '</div>';

        $loaded_apps[$app->ID] = $app;
      }

      echo '</div>';

      echo '<script>
              $(document).ready(function(){
                $(\'.row.carousel.'. $term->slug .'\').slick({
                  slidesToShow: 3,
                  slidesToScroll: 3
                });
              });
            </script>';

  }

}


// --------------------------------------------------------------------
// SHORTCODE TO DISPLAY Apps GROUP
// [socrata_apps]
// --------------------------------------------------------------------
/*
add_shortcode('socrata_apps','socrata_apps_shortcode');
function socrata_apps_shortcode( $atts ) {
  ob_start();
  extract( shortcode_atts( array (
    'type' => 'socrata_apps',
    'order' => 'date',
    'orderby' => 'desc',
    'posts' => 1,
  ), $atts ) );
  $options = array(
    'post_type' => $type,
    'order' => $order,
    'orderby' => $orderby,
    'posts_per_page' => $posts,
  );
  $query = new WP_Query( $options );
  if ( $query->have_posts() ) { ?>
  <?php while ( $query->have_posts() ) : $query->the_post(); ?>
  <?php if(has_post_thumbnail()) :?>
  <?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), ''); $url = $thumb['0']; ?>
  <?php endif;?>
  <div class="socrata_apps-image format_text" style="background-image:url(<?=$url?>);">    
    <div class="socrata_apps-wrapper clearfix">
      <div class="fade <?php $meta = get_socrata_apps_meta(); if ($meta[6]) echo "$meta[6]"; ?> <?php $meta = get_socrata_apps_meta(); if ($meta[2]) echo "$meta[2]"; ?>">
        <?php $meta = get_socrata_apps_meta(); if ($meta[0]) echo "<h1>$meta[0]</h1>"; ?>
        <?php $meta = get_socrata_apps_meta(); if ($meta[1]) echo "<h2>$meta[1]</h2>"; ?>
        <?php $meta = get_socrata_apps_meta(); if ($meta[3]) echo "<a href='$meta[4]' target='$meta[5]' class='button' style='border:#fff solid 2px; font-weight:300;'>$meta[3]</a>"; ?>
      </div>
    </div>    
  </div>
  <div class="what-is-socrata format_text mobile-hide">
    <div class="what-is-socrata-wrapper">
      <table>
        <tr>
          <td>Who is Socrata?</td>
          <td>Socrata's open data and open performance platform have transformed how data is discovered, analyzed, and shared online. We work everyday to unleash the power of data to improve the world.</td>
          <td><a href="/unleash-the-power-of-open-data/" class="button" style="font-weight: 300;">Watch the Video <span class="ss-icon">right</span></a></td>
        </tr>
      </table>
    </div>
  </div>
  <?php endwhile; wp_reset_postdata(); ?>
  <?php $myvariable = ob_get_clean();
  return $myvariable;
  } 
}
*/
