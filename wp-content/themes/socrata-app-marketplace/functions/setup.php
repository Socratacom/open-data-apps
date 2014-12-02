<?php

function site_setup() {
  add_editor_style('css/editor-style.css');
  add_theme_support('post-thumbnails');
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

function site_search_form( $form ) {
    $form = '<form class="form-inline" role="search" method="get" id="searchform" action="' . home_url('/') . '" >
    <div class="form-group">
		    <input class="form-control" type="text" value="' . get_search_query() . '" name="s" id="s" />
    </div>
		<button type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> Search</button>
    </form>';
    return $form;
}
add_filter( 'get_search_form', 'site_search_form' );

// Register Menus

add_action( 'init', 'register_my_menus' );
function register_my_menus() {
  register_nav_menus(
    array(
        'persona' => __( 'Persona Menu' ),
        'industry' => __( 'Industry Menu' )
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



