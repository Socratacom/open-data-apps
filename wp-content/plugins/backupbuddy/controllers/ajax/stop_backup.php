<?php
if ( ! is_admin() ) { die( 'Access denied.' ); }

// Button to stop backup.

$serial = pb_backupbuddy::_POST( 'serial' );
set_transient( 'pb_backupbuddy_stop_backup-' . $serial, true, ( 60*60*24 ) );

die( '1' );