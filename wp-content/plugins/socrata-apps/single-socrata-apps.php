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

    <?php get_template_part( 'sidebar-app-nav' ); ?>

    <div class="col-sm-8 col-md-9"><!-- Content Column -->
      
<<<<<<< HEAD
<<<<<<< HEAD
      <?php do_action( 'above_primary_content' ); ?>
=======
<<<<<<< HEAD
      <?php include('filter-bar.php'); ?>
=======
      <?php do_action( 'above_primary_content' ); ?>
>>>>>>> ce52966a649cbb70448ad5b1e5438ea43d7740e9
>>>>>>> staging
=======
      <?php do_action( 'above_primary_content' ); ?>
>>>>>>> staging

      <div class="app-frame">

        <div class="row">

          <!-- Go to www.addthis.com/dashboard to customize your tools -->
          <div class="hidden-xs addthis_sharing_toolbox"></div>
          <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e590fc12e22e79e"></script>

          <div class="hidden-xs col-xs-12 col-sm-2 col-md-3 app-specs-column">
            
            <div style="margin-bottom: 20px"><?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-md', false, array('class' => 'app-icon')); ?></div>
            
            <ul class="app-buttons">
            <?php if ($meta[21]) { ?>
              <li><button class="btn btn-primary" data-toggle="modal" data-target="#getAppModal"><?php echo $get_app_cta; ?></button> </li>
            <?php } elseif ($meta[0]) { ?>
              <li><a href="<?php echo $meta[0]; ?>" class="btn btn-primary" target="_blank" style="color:#fff"><?php echo $get_app_cta; ?></a></li>
            <?php } ?>

            <?php if ($meta[22]) { ?>
              <li><a href="<?php echo $meta[22]; ?>" class="btn btn-info" target="_blank">View Demo</a></li>
            <?php } ?>

            <?php if ($meta[16]) { ?>
              <li title="This app uses the Socrata API." data-toggle="tooltip" data-placement="top" style="text-align:center; background: #ddd; padding: 10px 0 0;">
                <span style="display: inline-block; margin-bottom: 10px"><span style="font-size: 21px; vertical-align: bottom; padding-top: 0; margin-right: 8px" class="icon icon-certified"></span> Socrata Certified</span>
              </li>
            <?php } ?>
            </ul>

            <ul class="app-meta-list">

              <?php if ($meta[19]) { ?>
              <li>
                <h5 style="margin-bottom: 12px">Cost</h5>
                <?php if ($meta[19] === 'Paid App') { $paid_active = 'active'; $free_active = ''; } else { $paid_active = ''; $free_active = 'active'; } ?>
                <div class="cost-toggle"><div title="Paid apps will require some form of payment model." data-toggle="tooltip" data-placement="top"  class="side <?php echo $paid_active; ?>">Paid</div><div class="side right <?php echo $free_active; ?>">Free</div></div>
              </li>
              <?php } ?>
              
              <?php if (is_array($meta[18])) { ?>
              <li>
                <h5>Devices</h5>
                <?php foreach ($meta[18] as $device) {
                  switch ($device) {
                    case 'Web App':
                      echo "<span title=\"This is a Web App. Web apps run in a web browser such as Internet Explorer, Chrome, Firefox, Safari, etc.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-web2\"></span>";
                      break;
                    case 'Mobile App':
                      echo "<span title=\"This is a Mobile App. Mobile apps run on mobile devices such as smart phones and tablets. Check out which platform this runs below.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-mobile2\"></span>";
                      break;
                    case 'Desktop App':
                      echo "<span title=\"Desktop apps are operating system specific. Check out which platform this runs below.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-screen\"></span>";
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

          <div class="col-xs-12 col-sm-10 col-md-9 app-desc-column">

            <div class="clearfix" style="margin-bottom: 20px">

              <div class="visible-xs" style="width: 80px; float:left;">
                <?php if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-md', false, array('class' => 'img-responsive app-icon')); ?>
              </div>
            
              <div class="app-title-wrapper">
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
              </div>
            </div>

            <ul class="visible-xs app-buttons">
            <?php if ($meta[21]) { ?>
              <li><button class="btn btn-primary" data-toggle="modal" data-target="#getAppModal"><?php echo $get_app_cta; ?></button> </li>
            <?php } elseif ($meta[0]) { ?>
              <li><a href="<?php echo $meta[0]; ?>" class="btn btn-primary" target="_blank" style="color:#fff"><?php echo $get_app_cta; ?></a></li>
            <?php } ?>

            <?php if ($meta[22]) { ?>
              <li><a href="<?php echo $meta[22]; ?>" class="btn btn-info" target="_blank">View Demo</a></li>
            <?php } ?>

            <?php if ($meta[16]) { ?>
              <li style="text-align:center; background: #ddd; padding: 10px 0 0;">
                <span style="display: inline-block; margin-bottom: 10px"><span style="font-size: 21px; vertical-align: bottom; padding-top: 0; margin-right: 8px" class="icon icon-certified"></span> Socrata Certified
                <br>This app uses the Socrata API.</span>
              </li>
            <?php } ?>
            </ul>
            
            <div class="app-description">
              <?php 
              if ($meta[15]) {
                echo "<div class=\"text\">$meta[15]</div>"; 
              } ?>
            </div>

            <div class="app-screenshots">
              <div style="margin-bottom: 80px;">
              <?php if ($meta[7]) { ?>
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
              <?php } else { ?>
                <div class="single-screen"><?php echo wp_get_attachment_image($meta[6], 'screen-lg', false, array('class' => 'img-responsive')); ?></div>
              <?php } ?>
              </div>
            </div>

            <?php if ($show_review) { ?>
            <h2 class="title">Reviews</h2>
            <div id="review-content" style="margin-bottom: 10px">
              <?php comments_template(); ?>
            </div>
            <?php } ?>


            <ul class="visible-xs app-meta-list" style="margin-top: 30px">

              <?php if ($meta[19]) { ?>
              <li>
                <h5 style="margin-bottom: 12px">Cost</h5>
                <?php if ($meta[19] === 'Paid App') { $paid_active = 'active'; $free_active = ''; } else { $paid_active = ''; $free_active = 'active'; } ?>
                <div class="cost-toggle"><div title="Paid apps will require some form of payment model." data-toggle="tooltip" data-placement="top"  class="side <?php echo $paid_active; ?>">Paid</div><div class="side right <?php echo $free_active; ?>">Free</div></div>
              </li>
              <?php } ?>
              
              <?php if (is_array($meta[18])) { ?>
              <li>
                <h5>Devices</h5>
                <?php foreach ($meta[18] as $device) {
                  switch ($device) {
                    case 'Web App':
                      echo "<span title=\"This is a Web App. Web apps run in a web browser such as Internet Explorer, Chrome, Firefox, Safari, etc.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-web2\"></span>";
                      break;
                    case 'Mobile App':
                      echo "<span title=\"This is a Mobile App. Mobile apps run on mobile devices such as smart phones and tablets. Check out which platform this runs below.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-mobile2\"></span>";
                      break;
                    case 'Desktop App':
                      echo "<span title=\"Desktop apps are operating system specific. Check out which platform this runs below.\" data-toggle=\"tooltip\" data-placement=\"top\" class=\"icon icon-screen\"></span>";
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
        
        </div>
      </div>
    </div>

  </div>
</div><!-- End Container -->

<!-- Get App Modal -->
<div class="modal fade" id="getAppModal" tabindex="-1" role="dialog" aria-labelledby="getAppModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Get the App</h4>
      </div>
      <div class="modal-body">
        <p>Please complete the following form and a Socrata representative will contact you to answer any questions and discuss next steps.</p>
        <?php $meta = get_socrata_apps_meta(); if ($meta[21]) { ?>
          <script src="//app-abk.marketo.com/js/forms2/js/forms2.js"></script>
          <form id="mktoForm_1723"></form>
          <script>MktoForms2.loadForm("//app-abk.marketo.com", "851-SII-641", 1723);</script>
          <?php
        } ?> 
      </div>
    </div>
  </div>
</div>

<!-- Legend Modal -->
<div class="modal fade getLegendModal" tabindex="-1" role="dialog" aria-labelledby="getLegendModal" aria-hidden="true" id="getLegendModal" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">App Special Features</h4>
      </div>
      <div class="modal-body">
        <div id="legendIcons" class="legendIcons" style="padding-top:30px;">
          <?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<dl class='clearfix'><dt><span class='icon50'>Socrata Certified</span></dt><dd><strong>This is a Socrata Certified App</strong>. </dd></dl>" ; ?>
          <?php $meta = get_socrata_apps_meta(); if ($meta[19]) echo "<dl class='clearfix'><dt><span class='icon50'>$meta[19]</span></dt><dd><strong>This is a $meta[19]</strong>. </dd></dl>" ; ?>
          <?php $meta = get_socrata_apps_meta(); if ($meta[18])              
            foreach ($meta[18] as $string) {
              echo "<dl class='clearfix'><dt><span class='icon50'>".$string."</span></dt><dd><strong>This is a ".$string."</strong>. </dd></dl>";
              }; ?>
        </div>
        <script>
          $('#legendIcons span:contains(Web App)').addClass('icon-web');
          $('#legendIcons dd:contains(Web App)').append('<p>Web apps run in a web browser such as Internet Explorer, Chrome, Firefox, Safari, etc.</p>');
          $('#legendIcons span:contains(Mobile App)').addClass('icon-mobile');
          $('#legendIcons dd:contains(Mobile App)').append('<p>Mobile apps run on mobile devices such as smart phones and tablets. Check out which platform this runs on under the Details tab.</p>');
          $('#legendIcons span:contains(Desktop App)').addClass('icon-desktop');
          $('#legendIcons dd:contains(Desktop App)').append('<p>Desktop apps are operating system specific. Check out which platform this runs on under the Details tab.</p>');
          $('#legendIcons span:contains(Socrata Certified)').addClass('icon-certified');
          $('#legendIcons dd:contains(Socrata Certified)').append('<p>This app uses the Socrata API.</p>');
          $('#legendIcons span:contains(Free App)').addClass('icon-free');
          $('#legendIcons dd:contains(Free App)').append('<p>Free Apps are just that, free.</p>');
          $('#legendIcons span:contains(Paid App)').addClass('icon-paid');
          $('#legendIcons dd:contains(Paid App)').append('<p>Paid apps will require some form of payment model.</p>');
        </script>
      </div>
    </div>
  </div>
</div>

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
