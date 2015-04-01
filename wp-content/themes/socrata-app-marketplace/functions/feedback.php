<?php
/*
Custom feedback comments
https://codex.wordpress.org/Function_Reference/wp_list_comments#Comments_Only_With_A_Custom_Comment_Display
*/

function bst_comment($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
  extract($args, EXTR_SKIP);
  if ( 'div' == $args['style'] ) {
    $tag = 'div';
    $add_below = 'comment';
  } else {
    $tag = 'li';
    $add_below = 'div-comment';
  }
?>
<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
  <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
  <?php endif; ?>
    <div class="comment-author vcard">
    <ul> 
        <li>by <?php comment_author(); ?> - <?php printf( __('%1$s'), get_comment_date()) ?></li>
        <li><?php $commentrating = get_comment_meta(get_comment_ID(), 'rating', true ); echo '<img src="'. get_site_url() .'/wp-content/themes/socrata-app-marketplace/images/'. $commentrating . 'star.png"/>'; ?></li>
    </ul>
        <?php if ($comment->comment_approved == '0') : ?>
          <p class='alert alert-info'><em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em></p>
        <?php endif; ?>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <?php $commenttitle = get_comment_meta(get_comment_ID(), 'title', true ); echo '<h4>' . esc_attr( $commenttitle ) . '</h4>'; ?>
      	<?php comment_text() ?>
      </div>  
    </div>
    <div class="reply">
      <?php edit_comment_link(__('<p class="text-right"><span class="btn btn-default btn-info">Edit</span></p>'),' ','' );	?>
    </div>
      <?php if ( 'div' != $args['style'] ) : ?>
    </div>
  <?php endif; ?>
<?php } ?>
