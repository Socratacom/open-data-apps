<?php get_header(); ?>
<div class="container">
  <div class="row">
    <div class="col-sm-4 col-md-3 hidden-xs"><!-- Left Nav -->
      <?php get_template_part( 'sidebar-app-nav' ); ?>
    </div>
    <div class="col-sm-8 col-md-9"><!-- Content Column -->      
      
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
