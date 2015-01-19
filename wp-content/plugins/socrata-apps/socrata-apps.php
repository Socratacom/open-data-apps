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
