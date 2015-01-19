<?php
 
// Do not delete this section
if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])){
  die ('Please do not load this page directly. Thanks!'); }
if ( post_password_required() ) { ?>
  <div class="alert alert-warning">
    <?php _e('This post is password protected. Enter the password to view comments.', 'bst'); ?>
  </div>
<?php
  return; 
}
// End do not delete section

if (have_comments()) : ?>
<p class="text-muted">
 <span style="font-size: 36px;"><?php comments_number('None', '1', '%'); ?></span> Reviews | <a href="#respond">Leave a Review</a>
</p>
  
<ul class="commentlist">
  <?php wp_list_comments('type=comment&callback=bst_comment');?>
</ul>

<ul class="pagination">
  <li class="older"><?php previous_comments_link('Older Reviews') ?></li>
  <li class="newer"><?php next_comments_link('Newer Reviews') ?></li>
</ul>

<?php
  else :
    if (comments_open()) :
  echo"<p class='alert alert-info'>Be the first to write a review.</p>";
    else :
      echo"<p class='alert alert-warning'>Reviews are closed for this app.</p>";
    endif;
  endif;
?>

<?php comment_form($fields); ?>
<script>
jQuery(function($) {
    $('#review-content').on('click', '.pagination a', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        $('#review-content').fadeOut(200, function(){
            $(this).load(link + ' #review-content', function() {
                $(this).fadeIn(200);
            });
        });
    });
});

</script>




