<?php
if ( ! is_admin() ) { die( 'Access denied.' ); }
/* destination_picker()
*
* iframe remote destination selector page.
*
*/

pb_backupbuddy::load();

pb_backupbuddy::$ui->ajax_header();

$mode = 'destination';
require_once( '_destination_picker.php' );

pb_backupbuddy::$ui->ajax_footer();
die();

