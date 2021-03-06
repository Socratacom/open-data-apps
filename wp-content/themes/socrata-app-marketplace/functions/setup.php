<?php

function site_setup() {
  add_editor_style('css/editor-style.css');
  add_theme_support('post-thumbnails');
  add_theme_support( 'html5', array( 'search-form' ) );
	update_option('thumbnail_size_w', 170);
  update_option('medium_size_w', 470);
  update_option('large_size_w', 970);
}
add_action('init', 'site_setup');

add_image_size( 'square-sm', 100, 100, true );
add_image_size( 'square-md', 200, 200, true );
add_image_size( 'square-lg', 500, 500, true );
add_image_size( 'screen-lg', 600, 382, true );
add_image_size( 'screen-md', 470, 299, true );
add_image_size( 'screen-sm', 262, 167, true );

if (! isset($content_width))
	$content_width = 600;


// Register Menus
add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array(
        'persona' => __( 'Persona Menu' ),
        'industry' => __( 'Industry Menu' ),
        'resources' => __( 'Resources Menu' )
    )
  );
}

//Page Slug Body Class
function add_slug_body_class( $classes ) {
global $post;
  if ( isset( $post ) ) {
    $classes[] = $post->post_name;
  }
  return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

// Remove anchor tag links on gravity forms step forms
add_filter("gform_confirmation_anchor", create_function("","return false;"));

// Customizing wp search
function marketplace_search_form( $form ) {
  $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
  <!--<input type="hidden" name="post_type[]" value="socrata_apps" />-->
  <div class="button-wrapper">
    <input class="btn btn-primary" type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
  </div>
  <div class="field-wrapper">
    <input placeholder="Search" type="text" value="' . get_search_query() . '" name="s" id="s" />
  </div>
  </form>';

  return $form;
}
add_filter( 'get_search_form', 'marketplace_search_form' );

function search_filter($query) {
  if ( !is_admin() && $query->is_main_query() && $query->is_search) {
      $query->set( 'post_type', array( 'socrata_apps' ) );
      $query->set( 'showposts', '100' );
  }
}
add_action('pre_get_posts','search_filter');

function modify_wp_search_where($where) {
  
  if ( !is_admin() && is_search() ) {
    
    global $wpdb, $wp;

    $where = preg_replace(
      "/({$wpdb->posts}.post_title (LIKE '%". preg_quote(esc_sql(get_query_var('s')), '/') ."%'))/i",
      "$0 OR ( {$wpdb->postmeta}.meta_value LIKE '%". preg_quote(esc_sql(get_query_var('s')), '/') ."%' )",
      $where
      );
    
    add_filter( 'posts_join_request', 'modify_wp_search_join' );
    add_filter( 'posts_distinct_request', 'modify_wp_search_distinct' );
  }
  
  return $where;
  
}
add_action( 'posts_where_request', 'modify_wp_search_where' );

function modify_wp_search_join( $join ) {
  global $wpdb;
  return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
}

function modify_wp_search_distinct( $distinct ) {
  return 'DISTINCT';
}