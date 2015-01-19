<?php
/*
Template Name: Search Page
*/
get_header(); ?>

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
        <h2>Search Results for &ldquo;<?php the_search_query(); ?>&rdquo;</h2>
        <hr/>
        <?php if(have_posts()): while(have_posts()): the_post();?>
        <article role="article" id="post_<?php the_ID()?>" <?php post_class()?>>
          <header>
            <h4><a href="<?php the_permalink(); ?>"><?php the_title()?></a></h4>
          </header>
          <?php the_excerpt(); ?>
          <hr/>
        </article>
        <?php endwhile; ?> 
        <?php else: ?>
        <div class="alert alert-warning">
          <i class="glyphicon glyphicon-exclamation-sign"></i> Sorry, your search yielded no results.
        </div>
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
