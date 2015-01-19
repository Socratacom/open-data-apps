<?php
if ( ! is_admin() ) { die( 'Access denied.' ); }

/* getMainLog()
 *
 * Dump out contents of the main log file.
 *
 */
$log_file = backupbuddy_core::getLogDirectory() . 'log-' . pb_backupbuddy::$options['log_serial'] . '.txt';
if ( file_exists( $log_file ) ) {
	readfile( $log_file );
} else {
	echo __('Nothing has been logged.', 'it-l10n-backupbuddy' );
}
die();