<?php get_header(); ?>
<div class="container">
  <div class="row">

    <div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
      <?php get_template_part( 'sidebar-app-nav' ); ?>
    </div>

    <div class="col-sm-8 col-md-9"><!-- Content Column -->
      <!-- <div style="background: #e9e9e9; margin-bottom: 30px; height: 38px; line-height: 38px; padding:0 10px; border-radius: 0px">
        <div style="float: right">

          <label style="display:inline-block; margin: 0 10px"><input type="checkbox"> Free</label>

          <label style="display:inline-block; margin: 0 10px"><input type="checkbox"> Socrata Certified</label>
          
          <select id="example-getting-started" style="display:inline-block; margin: 0 0 0 10px">
            <option value="cheese">Device</option>
            <option value="tomatoes">Tomatoes</option>
            <option value="mozarella">Mozzarella</option>
            <option value="mushrooms">Mushrooms</option>
            <option value="pepperoni">Pepperoni</option>
            <option value="onions">Onions</option>
          </select>

          <select id="example-getting-started" style="display:inline-block; margin: 0 0 0 10px">
            <option value="cheese">Platform</option>
            <option value="tomatoes">Tomatoes</option>
            <option value="mozarella">Mozzarella</option>
            <option value="mushrooms">Mushrooms</option>
            <option value="pepperoni">Pepperoni</option>
            <option value="onions">Onions</option>
          </select>

        </div>
      </div> -->
      <div style="background: #E9E9E9; padding: 30px">

        <div class="row">

          <div class="col-md-3 app-specs-column">
            
            <div style=" margin-bottom: 20px">
            <?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-md', false, array('class' => 'img-responsive app-icon')); ?>
            </div>
            
            <ul class="app-buttons">
              <?php $meta = get_socrata_apps_meta(); if ($meta[21]) { ?>
              <li>
                <button class="btn btn-primary" data-toggle="modal" data-target="#getAppModal">Get the App</button>
              </li>
              <?php } elseif ($meta[0]) { ?>
                <li>
                  <a href="<?php echo $meta[0]; ?>" class="btn btn-primary" target="_blank" style="color:#fff">Get the App</a>
                </li>
              <?php } ?>
              <?php $meta = get_socrata_apps_meta(); if ($meta[22]) { ?>
                <li>
                  <a href="<?php echo $meta[22]; ?>" class="btn btn-info" target="_blank">View Demo</a>
                </li>
              <?php } ?>
            </ul>

            <ul class="app-meta-list">

              <li>
                <h5 style="margin-bottom: 12px">Cost</h5>
                <?php $meta = get_socrata_apps_meta(); if ($meta[19]) { ?>
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

                <?php } //  ?>
              </li>
              
              <li>
                <h5>Devices</h5>
                <?php $meta = get_socrata_apps_meta(); if (is_array($meta[18])) {
                  foreach ($meta[18] as $platform) {
                    switch ($platform) {
                      case 'Web App':
                        echo "<span title=\"Web App\" class=\"icon icon-screen\"></span>";
                        break;
                      case 'Mobile App':
                        echo "<span title=\"Mobile App\" class=\"icon icon-mobile2\"></span>";
                        break;
                    }
                  }
                } ?>
              </li>

              <li>
                <h5>Platforms</h5>
                <?php $meta = get_socrata_apps_meta(); if (is_array($meta[17])) {

                  foreach ($meta[17] as $platform) {
                    switch ($platform) {
                      case 'Web':
                        echo "<span title=\"Web\" class=\"icon icon-uniE60B\"></span>";
                        break;
                      case 'iOS':
                        echo "<span title=\"iOS\" class=\"icon icon-uniE60C\"></span>";
                        break;
                      case 'Android':
                        echo "<span title=\"Android\" class=\"icon icon-uniE60D\"></span>";
                        break;
                      case 'Windows Phone':
                        echo "<span title=\"Windows Phone\" class=\"icon icon-uniE60E\"></span>";
                        break;
                    }
                  }
                } ?>
              </li>

              <li>
                <h5>Updated</h5>          
                <?php $meta = get_socrata_apps_meta(); echo ($meta[1] ? $meta[1] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>

              <li>
                <h5>Version</h5>
                <?php $meta = get_socrata_apps_meta(); echo ($meta[2] ? $meta[2] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>

              <li>
                <h5>Size</h5>
                <?php $meta = get_socrata_apps_meta(); echo ($meta[3] ? $meta[3] : '<span style="color:#bbb; font-size: 12px">n/a</span>'); ?>
              </li>

              <li>
                <h5>Contact Developer</h5>
                <ul>
                  <?php $meta = get_socrata_apps_meta(); if ($meta[11]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[11]" ?>" target="_blank">Visit Company Site</a></li>
                  <?php
                  } ?>            
                  <?php $meta = get_socrata_apps_meta(); if ($meta[12]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[12]" ?>" target="_blank">Visit Developer Site</a></li>
                  <?php
                  } ?>            
                  <?php $meta = get_socrata_apps_meta(); if ($meta[13]) { ?>
                    <li><a href="<?php get_socrata_apps_meta(); echo "$meta[13]" ?>" target="_blank">Visit Support Site</a></li>
                  <?php
                  } ?>
                </ul>
              </li>

            </ul>

          </div>





          <div class="col-md-9 app-desc-column">
          
            <h1 class="app-title" style="margin-top:0"><?php the_title()?></h1>
          
            <?php $meta = get_socrata_apps_meta(); if ($meta[9] && $meta[10]) { ?>
              <ul class="app-meta">
                <li><?php echo $meta[9]; ?></li>
                <li><span class="divider"></span></li>
                <li><?php echo $meta[10]; ?></li>
              </ul>
            <?php } elseif ($meta[10]) { ?>
              <div class="app-meta"><?php echo $meta[10]; ?></div>
            <?php } else { ?>
              <div class="app-meta"><?php echo $meta[9]; ?></div>
            <?php } ?>
            
            <div class="app-description">
              <?php $meta = get_socrata_apps_meta(); 
              if ($meta[15]) {
                echo "<div class=\"text\">$meta[15]</div>"; 
              } ?>
            </div>

            <div class="app-screenshots">

              <div style="margin-bottom: 80px;">

              <?php
                    $meta = get_socrata_apps_meta();
                    if ($meta[7]) { ?>
                      <div id="screenCarousel" class="carousel slide" data-interval="5000" data-ride="carousel">      
                        <!-- Carousel indicators -->
                        <ol class="carousel-indicators">
                          <?php $meta = get_socrata_apps_meta(); if ($meta[6]) echo "<li data-target='#screenCarousel' data-slide-to='0' class='active'></li>"; ?>
                          <?php $meta = get_socrata_apps_meta(); if ($meta[7]) echo "<li data-target='#screenCarousel' data-slide-to='1' class=''></li>"; ?>
                          <?php $meta = get_socrata_apps_meta(); if ($meta[8]) echo "<li data-target='#screenCarousel' data-slide-to='2' class=''></li>"; ?>
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                          <?php $meta = get_socrata_apps_meta(); if ($meta[6]) { ?>        
                              <div class="item active">                
                                <?php echo wp_get_attachment_image($meta[6], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                              </div>
                          <?php } ?>
                          <?php $meta = get_socrata_apps_meta(); if ($meta[7]) { ?>        
                              <div class="item">                
                                <?php echo wp_get_attachment_image($meta[7], 'screen-lg', false, array('class' => 'img-responsive')); ?>
                              </div>
                          <?php } ?>
                          <?php $meta = get_socrata_apps_meta(); if ($meta[8]) { ?>        
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

            <h2 class="title">Reviews</h2>

              <div id="review-content" style="margin-bottom: 10px">
                <?php comments_template(); ?>
              </div>

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
});
</script>


<?php get_footer(); ?>
