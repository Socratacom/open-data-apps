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

  wp_register_style( 'socrata-apps', plugins_url( '/style.css' , __FILE__ ), array(), false, 'all' );
  wp_enqueue_style( 'socrata-apps' );

  wp_register_script( 'slick-carousel-js', plugins_url( '/assets/slick/slick.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'slick-carousel-js' );
  
  wp_register_script( 'readmore-js', plugins_url( '/assets/readmore/readmore.js' , __FILE__ ), false, null, true );
  wp_enqueue_script( 'readmore-js' );

  wp_enqueue_style( 'shuffle-css', plugins_url( '/assets/shuffle/css/style.css' , __FILE__ ) );
  wp_enqueue_script( 'custom-modernizer-js', plugins_url( '/assets/shuffle/dist/modernizr.custom.min.js' , __FILE__ ), array(), false, true );
  wp_enqueue_script( 'shuffle-js', plugins_url( '/assets/shuffle/dist/jquery.shuffle.min.js' , __FILE__ ), array(), false, true );
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


// --------------------------------------------------------------------
// FUNCTIONS FOR DISPLAYING APPS
// --------------------------------------------------------------------
function display_app_tile($app, $term) { 

  $meta = get_socrata_apps_meta($app->ID);
  $size = $meta[23] === 'yes' && $term === 'featured' ? 'tile-lg' : 'tile-md';

  ?>
  <div class="shuffle col-xs-12 <?php if ($meta[23] === 'yes' && $term === 'featured') { echo 'col-md-8'; } else { echo 'col-md-4'; } ?>">
    <div class="tile <?php echo $size; ?>">
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
        <?php if ($meta[16]) echo "<ul class='appsIcons tile-certified'><li>Socrata Certified</li><li><span class='icon16'>Socrata Certified</span></li></ul>" ; ?>
        <div class="tile-overlay"></div>
        <a href="<?php echo get_permalink($app->ID); ?>" class="tile-link"></a>
      </div>
    </div>
  </div>

<?php }

function get_apps_tiles_by_term($term) {

  $args = array(
      'hide_empty' => true,
      'parent' => 0
  );

  $loaded_apps = array();

  $terms = get_terms( $term, $args );

  if (!is_array($terms)) {
    return;
  }

  usort($terms, function($a, $b) {
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


// --------------------------------------------------------------------
// FUNCTION FOR DISPLAYING THE FILTER BAR
// --------------------------------------------------------------------
function display_filter_bar($post_ID) {
   include('filter-bar.php');
}
add_action( 'above_primary_content', 'display_filter_bar' );
