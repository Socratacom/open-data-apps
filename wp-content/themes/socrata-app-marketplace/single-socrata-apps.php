<?php get_header(); ?>
<div class="container">
  <div class="row">
    <div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
      <?php get_template_part( 'sidebar-app-nav' ); ?>
    </div>
    <div class="col-sm-8 col-md-9"><!-- Content Column -->      
      <!--<?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<div class='ribbon-wrapper hidden-xs'><div class='ribbon' ><span>Socrata Certified</span></div></div>" ;?>-->
      <div class="panel panel-default app-title-panel">
        <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e590fc12e22e79e"></script>
        <div class="row">                
          <div class="col-sm-3">
            <?php $meta = get_socrata_apps_meta(); if ($meta[4]) echo wp_get_attachment_image($meta[4], 'square-md', false, array('class' => 'img-responsive app-icon')); ?>
          </div>
          <div class="col-sm-9">
            <h1 class="app-title"><?php the_title()?></h1>
            <?php $meta = get_socrata_apps_meta();
              if ($meta[9] && $meta[10]) { ?>
                <ul class="app-meta">
                  <li><?php echo $meta[9]; ?></li>
                  <li><span class="divider"></span></li>
                  <li><?php echo $meta[10]; ?></li>
                </ul>
              <?php }
              elseif ($meta[10]) { ?>
                <div class="app-meta"><?php echo $meta[10]; ?></div>
              <?php  
              }
              else { ?>
                <div class="app-meta"><?php echo $meta[9]; ?></div>
              <?php
              }
            ?>
            <ul class="app-buttons">
              <?php $meta = get_socrata_apps_meta();
                if ($meta[21]) { ?>
                <li>
                  <button class="btn btn-primary" data-toggle="modal" data-target="#getAppModal">Get the App</button>
                </li>
                <?php }
                elseif ($meta[0]) { ?>
                  <li><a href="<?php echo $meta[0]; ?>" class="btn btn-primary" target="_blank" style="color:#fff">Get the App</a></li>
                <?php
                }
              ?>
              <?php $meta = get_socrata_apps_meta();
                if ($meta[22]) { ?>
                <li>
                  <a href="<?php echo $meta[22]; ?>" class="btn btn-info" target="_blank">View Demo</a>
                </li>
                <?php
                }
              ?>
            </ul>
            <ul id="featureIcons" class="app-specs">
              <?php $meta = get_socrata_apps_meta(); if ($meta[16]) echo "<li><a href='#' data-toggle='modal' data-target='#getLegendModal'><span class='appSpecToolTip icon32' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='Socrata Certified'>Socrata Certified</span></a></li>" ; ?>
              <?php $meta = get_socrata_apps_meta(); if ($meta[19]) echo "<li><a href='#' data-toggle='modal' data-target='#getLegendModal'><span class='appSpecToolTip icon32' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='$meta[19]'>$meta[19]</span></a></li>" ; ?>
              <?php $meta = get_socrata_apps_meta(); if ($meta[18])              
              foreach ($meta[18] as $string) {
                  echo "<li style='padding-right:4px;'><a href='#' data-toggle='modal' data-target='#getLegendModal'><span class='appSpecToolTip icon32' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='$string'>".$string."</span></a></li>";
              }; ?>
            </ul>
            <script>
              $('#featureIcons span:contains(Web App)').addClass('icon-web');
              $('#featureIcons span:contains(Mobile App)').addClass('icon-mobile');
              $('#featureIcons span:contains(Desktop App)').addClass('icon-desktop');
              $('#featureIcons span:contains(Socrata Certified)').addClass('icon-certified');
              $('#featureIcons span:contains(Free App)').addClass('icon-free');
              $('#featureIcons span:contains(Paid App)').addClass('icon-paid');
              $('document').ready(function(){
                $('.appSpecToolTip').tooltip();
              });
            </script>
            <div class="app-terms">
              <?php echo get_the_term_list( $post->ID, array('socrata_apps_persona', 'socrata_apps_industry'), 'FOUND IN: ', ', ', '' ); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">          
          <ul class="nav nav-tabs" role="tablist" id="appTabs">
            <li class="active"><a href="#overview" role="tab" data-toggle="tab"><span class="tab-label">Overview</span><span class="glyphicon glyphicon-picture"></span></a></li>
            <li><a href="#details" role="tab" data-toggle="tab"><span class="tab-label">Details</span><span class="glyphicon glyphicon-info-sign"></span></a></li>
            <?php $meta = get_socrata_apps_meta(); if ($meta[20]) echo "<li><a href='#schema' role='tab' data-toggle='tab'><span class='tab-label'>Data Schema</span><span class='glyphicon glyphicon-wrench'></span></a></li>"; ?>
            <li><a href="#reviews" role="tab" data-toggle="tab"><span class="tab-label">Reviews</span><span class="glyphicon glyphicon-comment"></span></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="overview">
              <div class="row">
                <div class="col-md-8">
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
                <div class="col-md-4">
                  <h2>Description</h2>
                  <?php $meta = get_socrata_apps_meta(); if ($meta[14]) echo "<div>$meta[14]</div>"; ?>
                </div>
              </div>
            </div><!-- End Tab Pane -->
            <div class="tab-pane fade" id="details">
              <div class="row">
                <div class="col-sm-8 col-md-9">
                  <h2>App Details</h2>
                  <?php $meta = get_socrata_apps_meta(); if ($meta[15]) echo "<div>$meta[15]</div>"; ?>
                </div>
                <div class="col-sm-4 col-md-3">
                  <h2>Platform</h2>
                  <ul id="platformIcons">                  
                    <?php $meta = get_socrata_apps_meta(); if ($meta[17])              
                    foreach ($meta[17] as $string) {
                        echo "<li><span>$string</span>".$string."</li>";
                    }; ?>
                  </ul>
                  <script>
                    $('#platformIcons span:contains(Web)').addClass('icon-cloud');
                    $('#platformIcons span:contains(iOS)').addClass('icon-apple');
                    $('#platformIcons span:contains(Android)').addClass('icon-android');
                    $('#platformIcons span:contains(Windows Phone)').addClass('icon-windows');
                    $('#platformIcons span:contains(Mac OS)').addClass('icon-apple');
                    $('#platformIcons span:contains(Linux)').addClass('icon-tux');
                    $('#platformIcons span:contains(Windows)').addClass('icon-windows');
                  </script>
                </div>
              </div>
              <hr/>
              <h2>Additional Information</h2>
              <div class="row">
                <div class="col-sm-6 col-md-3">
                  <h5>Updated</h5>          
                  <?php $meta = get_socrata_apps_meta(); if ($meta[1]) echo "$meta[1]"; ?>
                </div>
                <div class="col-sm-6 col-md-3">
                  <h5>Version</h5>
                  <?php $meta = get_socrata_apps_meta(); if ($meta[2]) echo "$meta[2]"; ?>
                </div>
                <div class="col-sm-6 col-md-3">
                  <h5>Size</h5>
                  <?php $meta = get_socrata_apps_meta(); if ($meta[3]) echo "$meta[3]"; ?>
                </div>
                <div class="col-sm-6 col-md-3 contact-links">
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
                </div>
              </div>
            </div><!-- End Tab Pane -->
            <?php $meta = get_socrata_apps_meta(); if ($meta[20]) { ?>
            <div class="tab-pane fade" id="schema">
              <h2>Data Schema</h2>
              <p>This app has made their Data Schema available.</p>
              <p><a href="<?php get_socrata_apps_meta(); echo "$meta[20]" ?>" target="_blank" class="btn btn-lg btn-primary">View the Data Schema</a></p>
            </div><!-- End Tab Pane -->
            <?php
            } ?>  
            <div class="tab-pane fade" id="reviews">
              <div id="review-content">
                <?php comments_template(); ?>
              </div>
            </div><!-- End Tab Pane -->            
          </div><!-- End Tab Content -->
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
  $(function () {
    $('#appTabs a:first').tab('show')
  })
</script>

<?php get_footer(); ?>
