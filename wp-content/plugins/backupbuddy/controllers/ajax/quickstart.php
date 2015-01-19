<?php
if ( ! is_admin() ) { die( 'Access denied.' ); }

pb_backupbuddy::$ui->ajax_header();
pb_backupbuddy::load_style( 'thickboxed.css' );
require_once( pb_backupbuddy::plugin_path() . '/views/_quicksetup.php' );
pb_backupbuddy::$ui->ajax_footer();
die();