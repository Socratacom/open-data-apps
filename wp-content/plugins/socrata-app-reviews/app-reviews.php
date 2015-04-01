<?php
/*
Plugin Name: Socrata App Reviews
Version: 1.0
Plugin URI: http://socrata.com
Description: This adds additional fields and star ratings to the Review Form
Author: Michael Church
Author URI: http://socrata.com
*/

// Add custom meta (ratings) fields to the default comment form
// Default comment form includes name, email and URL
// Default comment form elements are hidden when user is logged in

add_filter('comment_form_default_fields','custom_fields');
function custom_fields($fields) {

	unset($fields['url']);
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );

	$fields[ 'author' ] = '<div class="form-group">'.
		'<label for="author">' . __( 'Name' ) . '</label>'.
		( $req ? '<span class="required">*</span>' : '' ).
		'<input id="author" name="author" type="text" placeholder="Your Name" value="'. esc_attr( $commenter['comment_author'] ) . 
		'" tabindex="1"' . $aria_req . ' class="form-control"/></div>';
	
	$fields[ 'email' ] = '<div class="form-group">'.
		'<label for="email">' . __( 'Email' ) . '</label>'.
		( $req ? '<span class="required">*</span>' : '' ).
		'<input id="email" name="email" type="text" placeholder="Email Address" value="'. esc_attr( $commenter['comment_author_email'] ) . 
		'" tabindex="2"' . $aria_req . ' class="form-control" /></div>';


return $fields;

}

// Add fields after default fields

add_action( 'comment_form_logged_in_after', 'additional_fields' );
add_action( 'comment_form_after_fields', 'additional_fields' );

function additional_fields () {
	echo '<div class="form-group">'.
	'<label for="title">' . __( 'Comment Title' ) . '</label>'.
	'<input id="title" name="title" type="text" tabindex="3" class="form-control"/></div>';

	echo '<div class="form-group">'.
	'<label for="rating">'. __('Rating') . '<span class="required">*</span></label>
	<ul class="commentratingbox">';
	
	for( $i=1; $i <= 5; $i++ )
	echo '<li><input type="radio" name="rating" id="rating" value="'. $i .'"/> '. $i .'</li>';

	echo'</ul></div>';

}

// Save the comment meta data along with comment

add_action( 'comment_post', 'save_comment_meta_data' );
function save_comment_meta_data( $comment_id ) {

	if ( ( isset( $_POST['title'] ) ) && ( $_POST['title'] != '') )
	$title = wp_filter_nohtml_kses($_POST['title']);
	add_comment_meta( $comment_id, 'title', $title );

	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') )
	$rating = wp_filter_nohtml_kses($_POST['rating']);
	add_comment_meta( $comment_id, 'rating', $rating );
}


// Add the filter to check if the comment meta data has been filled or not

add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {
	if ( ! isset( $_POST['rating'] ) )
	wp_die( __( 'Error: You did not add your rating. Hit the BACK button of your Web browser and resubmit your comment with rating.' ) );
	return $commentdata;
}

//Add an edit option in comment edit screen  

add_action( 'add_meta_boxes_comment', 'app_reviews_add_meta_box' );
function app_reviews_add_meta_box() {
    add_meta_box( 'title', __( 'Review Metadata - App Reviews' ), 'app_reviews_meta_box', 'comment', 'normal', 'high' );
}
 
function app_reviews_meta_box ( $comment ) {
    $title = get_comment_meta( $comment->comment_ID, 'title', true );
    $rating = get_comment_meta( $comment->comment_ID, 'rating', true );
    wp_nonce_field( 'app_reviews_update', 'app_reviews_update', false );
    ?>
    <p>
        <label for="title"><?php _e( 'Review Title' ); ?></label>
        <input type="text" name="title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
    </p>
    <p>
        <label for="rating"><?php _e( 'Rating: ' ); ?></label>
			<span class="commentratingbox">
			<?php for( $i=1; $i <= 5; $i++ ) {
				echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="'. $i .'"';
				if ( $rating == $i ) echo ' checked="checked"';
				echo ' />'. $i .' </span>'; 
				}
			?>
			</span>
    </p>
    <?php
}

// Update comment meta data from comment edit screen 

add_action( 'edit_comment', 'app_reviews_edit_metafields' );
function app_reviews_edit_metafields( $comment_id ) {
    if( ! isset( $_POST['app_reviews_update'] ) || ! wp_verify_nonce( $_POST['app_reviews_update'], 'app_reviews_update' ) ) return;
		
	if ( ( isset( $_POST['title'] ) ) && ( $_POST['title'] != '') ):
	$title = wp_filter_nohtml_kses($_POST['title']);
	update_comment_meta( $comment_id, 'title', $title );
	else :
	delete_comment_meta( $comment_id, 'title');
	endif;

	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') ):
	$rating = wp_filter_nohtml_kses($_POST['rating']);
	update_comment_meta( $comment_id, 'rating', $rating );
	else :
	delete_comment_meta( $comment_id, 'rating');
	endif;
	
}

// Add the comment meta (saved earlier) to the comment text 
// You can also output the comment meta values directly in comments template  

add_filter( 'comment_text', 'modify_comment');
function modify_comment( $text ){

	$plugin_url_path = WP_PLUGIN_URL;

	if( $commenttitle = get_comment_meta( get_comment_ID(), 'title', true ) ) {
		$commenttitle = '<h4 class="hidden-xs hidden-sm hidden-md hidden-lg">' . esc_attr( $commenttitle ) . '</h4>';
		$text = $commenttitle . $text;
	} 

	if( $commentrating = get_comment_meta( get_comment_ID(), 'rating', true ) ) {
		$commentrating = '<p class="comment-rating hidden-xs hidden-sm hidden-md hidden-lg">	<img src="'. $plugin_url_path .
		'/socrata-app-reviews/images/'. $commentrating . 'star.png"/><br/>Rating: <strong>'. $commentrating .' / 5</strong></p>';
		$text = $text . $commentrating;
		return $text;		
	} else {
		return $text;		
	}	 
}

// ADJUST DEFAULTS
add_filter( 'comment_form_defaults', 'remove_comment_form_allowed_tags' );
function remove_comment_form_allowed_tags ( $defaults ) {
  	$defaults['comment_notes_after'] = '';
  	$defaults['title_reply'] = 'Write a Review';
  	$defaults['comment'] = 'Write a Review';
  	$defaults['label_submit'] = 'Submit';
  	$defaults['button_submit'] = '<input name="submit" class="btn btn-default" type="submit" id="submit" value="Submit me">';
  	$defaults['comment_field'] = '<div class="form-group"><label for="comment">' . _x( 'Review', 'noun' ) . '</label><textarea id="comment" name="comment" rows="8" aria-required="true" class="form-control"></textarea></div>';
  	
  return $defaults;
}

add_action('comment_form', 'bootstrap3_comment_button' );
function bootstrap3_comment_button() {
    echo '<button class="btn btn-info" type="submit">' . __( 'Submit Review' ) . '</button>';
}








