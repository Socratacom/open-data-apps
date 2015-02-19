<?php get_header(); ?>

<?php 

$meta = get_socrata_apps_meta(); 

$get_app_cta = 'Get the App';
$show_review = true;

$term_list = wp_get_post_terms($post->ID, 'socrata_apps_resources', array("fields" => "all"));
if ($term_list[0]->taxonomy === 'socrata_apps_resources') {
  $meta[18] = null;
  $get_app_cta = 'View Template';
  $show_review = false;
}

?>

<div class="container">
  <div class="row">

    <div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
      <?php get_template_part( 'sidebar-app-nav' ); ?>
    </div>

    <div class="col-sm-8 col-md-9"><!-- Content Column -->
      
      <?php include('filter-bar.php'); ?>

      <div style="background: #E9E9E9; padding: 30px">

        <div class="row">

          <div class="col-md-3 app-specs-column">
            
            <div style=" margin-bottom: 20px">
            <?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-md', false, array('class' => 'img-responsive app-icon')); ?>
            </div>
            
            <ul class="app-buttons">
              <?php if ($meta[21]) { ?>
              <li>
                <button class="btn btn-primary" data-toggle="modal" data-target="#getAppModal"><?php echo $get_app_cta; ?></button>
              </li>
              <?php } elseif ($meta[0]) { ?>
                <li>
                  <a href="<?php echo $meta[0]; ?>" class="btn btn-primary" target="_blank" style="color:#fff"><?php echo $get_app_cta; ?></a>
                </li>
              <?php } ?>
              <?php if ($meta[22]) { ?>
                <li>
                  <a href="<?php echo $meta[22]; ?>" class="btn btn-info" target="_blank">View Demo</a>
                </li>
              <?php } ?>
            </ul>

            <ul class="app-meta-list">

              <?php  if ($meta[19]) { ?>
              <li>
                <h5 style="margin-bottom: 12px">Cost</h5>
                <?php if ($meta[19] === 'Paid App') { ?>
                <div class="cost-toggle">
                  <div class="side active">Paid</div>
                  <div class="side right">Free</div>
                </div>
                <?php } else { ?>
                <div class="cost-toggle">
                  <div class="side">Paid</div>
                  <div class="side right active">Free</div>
                </div>
                <?php } ?>
              </li>
              <?php } ?>
              
              
              <?php if (is_array($meta[18])) { ?>
              <li>
                <h5>Devices</h5>
                <?php foreach ($meta[18] as $device) {
                  switch ($device) {
                    case 'Web App':
                      echo "<span title=\"Desktop\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-screen\"></span>";
                      break;
                    case 'Mobile App':
                      echo "<span title=\"Mobile\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-mobile2\"></span>";
                      break;
                  }
                } ?>
              </li>
              <?php } ?>
                
              <?php if (is_array($meta[17])) { ?>
              <li>
                <h5>Platforms</h5>
                <?php foreach ($meta[17] as $platform) {
                  switch ($platform) {
                    case 'Web':
                      echo "<span title=\"Web\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-cloud\"></span>";
                      break;
                    case 'iOS':
                      echo "<span title=\"iOS\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-apple\"></span>";
                      break;
                    case 'Android':
                      echo "<span title=\"Android\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-android\"></span>";
                      break;
                    case 'Windows Phone':
                      echo "<span title=\"Windows Phone\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-windows\"></span>";
                      break;
                  }
                } ?>
                </li>
                <?php } ?>
              
              <?php if ($meta[1]) { ?>
              <li>
                <h5>Updated</h5>          
                <?php echo ($meta[1] ? $meta[1] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>
              <?php } ?>

              <?php if ($meta[2]) { ?>
              <li>
                <h5>Version</h5>
                <?php echo ($meta[2] ? $meta[2] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>
              <?php } ?>

              <?php if ($meta[3]) { ?>
              <li>
                <h5>Size</h5>
                <?php echo ($meta[3] ? $meta[3] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>
              <?php } ?>

              <?php if ($meta[11] || $meta[12] || $meta[13]) { ?>
              <li>
                <h5>Contact Developer</h5>
                <ul>
                  <?php if ($meta[11]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[11]" ?>" target="_blank">Visit Company Site</a></li>
                  <?php
                  } ?>            
                  <?php if ($meta[12]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[12]" ?>" target="_blank">Visit Developer Site</a></li>
                  <?php
                  } ?>            
                  <?php if ($meta[13]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[13]" ?>" target="_blank">Visit Support Site</a></li>
                  <?php
                  } ?>
                </ul>
              </li>
              <?php } ?>

            </ul>

          </div>

          <div class="col-md-9 app-desc-column">
          
            <h1 class="app-title" style="margin-top:0; margin-bottom:10px"><?php the_title()?></h1>

            <?php if ($meta[9] && $meta[10]) { ?>
              <ul class="app-meta">
                <li><?php echo $meta[9]; ?></li>
                <li><span class="divider"></span></li>
                <li><?php echo $meta[10]; ?></li>
              </ul>
            <?php } elseif ($meta[10]) { ?>
              <div class="app-meta"><?php echo $meta[10]; ?></div>
            <?php } elseif ($meta[9]) { ?>
              <div class="app-meta"><?php echo $meta[9]; ?></div>
            <?php } ?>
            
            <div class="app-description">
              <?php 
              if ($meta[15]) {
                echo "<div class=\"text\">$meta[15]</div>"; 
              } ?>
            </div>

            <div class="app-screenshots">

              <div style="margin-bottom: 80px;">

              <?php
                    
                    if ($meta[7]) { ?>
                      <div id="screenCarousel" class="carousel slide" data-interval="5000" data-ride="carousel">      
                        <!-- Carousel indicators -->
                        <ol class="carousel-indicators">
                          <?php if ($meta[6]) echo "<li data-target='#screenCarousel' data-slide-to='0' class='active'></li>"; ?>
                          <?php if ($meta[7]) echo "<li data-target='#screenCarousel' data-slide-to='1' class=''></li>"; ?>
                          <?php if ($meta[8]) echo "<li data-target='#screenCarousel' data-slide-to='2' class=''></li>"; ?>
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                          <?php if ($meta[6]) { ?>        
                              <div class="item active">                
                                <?php echo wp_get_attachment_image($meta[6], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                              </div>
                          <?php } ?>
                          <?php if ($meta[7]) { ?>        
                              <div class="item">                
                                <?php echo wp_get_attachment_image($meta[7], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                              </div>
                          <?php } ?>
                          <?php if ($meta[8]) { ?>        
                              <div class="item">                
                                <?php echo wp_get_attachment_image($meta[8], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                              </div>
                          <?php } ?>
                        </div>
                      </div>
                    <?php }
                    else { ?>
                      <div class="single-screen"><?php echo wp_get_attachment_image($meta[6], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                      </div>
                    <?php
                  }
                  ?>
                </div>

            </div>

            <?php if ($show_review) { ?>
            <h2 class="title">Reviews</h2>
            <div id="review-content" style="margin-bottom: 10px">
              <?php comments_template(); ?>
            </div>
            <?php } ?>

          </div>
        
        </div>
      </div>
    </div>

  </div>
</div><!-- End Container -->

<script>
$(function(){
  $('.app-description .text').readmore({
    collapsedHeight: 250,
    moreLink: '<a href="#">Show more</a>',
    lessLink: '<a href="#">Show less</a>'
  });
  $('[data-toggle="tooltip"]').tooltip()
});
</script>


<?php get_footer(); ?>
