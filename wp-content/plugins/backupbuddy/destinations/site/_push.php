<?php
/* Incoming vars from _manage.php:
 *
 *	$activePluginsA, $activePluginsB, $activeThemeBInfo
 *
 */
?>


<?php _e( "This will <b>push</b> to the destination site to make it match this site's database, media, etc. Contents of the destination site will be overwritten as needed. Verify the details below to make sure this is the correct deployment you wish to commence. You will be given the opportunity to test the changes and undo the database changes before making them permanent.", 'it-l10n-backupbuddy' ); ?>
<br><br>


<?php
$headFoot = array( __( 'From this site (source)', 'it-l10n-backupbuddy' ), __( '<b>Pushing</b> to (destination)', 'it-l10n-backupbuddy' ) );
$pushRows = array(
	'Site URL' => array( $localInfo['siteurl'], $deployData['remoteInfo']['siteurl'] ),
	'Home URL' => array( $localInfo['homeurl'], $deployData['remoteInfo']['homeurl'] ),
	'Max Execution Time' => array( $localInfo['php']['max_execution_time'] . ' sec', $deployData['remoteInfo']['php']['max_execution_time'] . ' sec' ),
	'Max Upload File Size' => array( $localInfo['php']['upload_max_filesize'] . ' MB', $deployData['remoteInfo']['php']['upload_max_filesize'] . ' MB' ),
	'Memory Limit' => array( $localInfo['php']['memory_limit'] . ' MB', $deployData['remoteInfo']['php']['memory_limit'] . ' MB' ),
	//'PHP Upload Limit' => array( $localInfo['php']['upload_max_filesize'], $deployData['remoteInfo']['php']['upload_max_filesize'] ),
	'WordPress Version' => array( $localInfo['wordpressVersion'], $deployData['remoteInfo']['wordpressVersion'] ),
	'BackupBuddy Version' => array( $localInfo['backupbuddyVersion'], $deployData['remoteInfo']['backupbuddyVersion'] ),
	'Active Plugins' => array( $activePluginsA, $activePluginsB ),
	'Active Theme' => array( $localInfo['activeTheme'], $deployData['remoteInfo']['activeTheme'] . ' ' . $activeThemeBInfo ),
	'Media / Attachments' => array( $localInfo['mediaCount'], $deployData['remoteInfo']['mediaCount'] . ' (' . count( $deployData['pushMediaFiles'] ) . ' files to send)' ),
);
?>



<?php
$deployDirection = 'push';
require( '_pushpull_foot.php' );
?>

