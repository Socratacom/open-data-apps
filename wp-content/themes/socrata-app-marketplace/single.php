<?php get_header(); ?>

<div class="container">

  <div class="row visible-xs">
    <div class="col-xs-12">
      <button style="margin-bottom: 20px;" type="button" class="pull-right btn btn-default" data-toggle="offcanvas">Off-canvas sidebar <i class="glyphicon glyphicon-arrow-right"></i>
      </button>
		</div>
  </div>
  <div class="row row-offcanvas row-offcanvas-right">
    
    <div class="col-xs-12 col-sm-8">
      <div id="content" role="main">
        <?php if(have_posts()): while(have_posts()): the_post();?>
        <article role="article" id="post_<?php the_ID()?>" <?php post_class()?>>
          <header>
            <h2><?php the_title()?></h2>
            <h4>
              <em>
                <span class="text-muted" class="author">By <?php the_author() ?>,</span>
                <time  class="text-muted" datetime="<?php the_time('d-m-Y')?>"><?php the_time('jS F Y') ?></time>
              </em>
            </h4>
            <p class="text-muted" style="margin-bottom: 30px;">
              <i class="glyphicon glyphicon-folder-open"></i>&nbsp; Filed under: <?php _e(''); ?> <?php the_category(', ') ?><br/>
              <i class="glyphicon glyphicon-comment"></i>&nbsp; Comments: <?php comments_popup_link('None', '1', '%'); ?>
            </p>
          </header>
          <?php the_post_thumbnail(); ?>
          <?php the_content()?>
          <hr/>
        </article>
        <?php comments_template('/comments.php'); ?>
        <?php endwhile; ?>
        <?php else: ?>
        <?php wp_redirect(get_bloginfo('siteurl').'/404', 404); exit; ?>  
        <?php endif;?>
      </div><!-- #content -->
    </div>
    
    <div class="col-xs-6 col-sm-4 sidebar-offcanvas" id="sidebar" role="navigation">
      <div class="panel panel-default">
        <?php get_sidebar(); ?>
      </div>
    </div>
    
  </div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
