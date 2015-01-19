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

// REGISTER POST TYPE
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

// MENU ICON
//Using Dashicon Font http://melchoyce.github.io/dashicons/
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

// REGISTER TAXONOMIES
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

// TEMPLATE PATHS
add_filter( 'template_include', 'socrata_apps_single_template_function', 1 );
function socrata_apps_single_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_single() ) {
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'single-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . '/single-socrata-apps.php';
      }
    }
  }
  return $template_path;
}

add_filter( 'template_include', 'socrata_apps_archive_template_function', 1 );
function socrata_apps_archive_template_function( $template_path ) {
  if ( get_post_type() == 'socrata_apps' ) {
    if ( is_archive() ) {
      // checks if the file exists in the theme first,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array ( 'archive-socrata-apps.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . '/archive-socrata-apps.php';
      }
    }
  }
  return $template_path;
}






// SHORTCODE TO DISPLAY Apps GROUP
// [socrata_apps]
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
