<?php
/* Incoming vars from _manage.php:
 *
 *	$activePluginsA, $activePluginsB, $activeThemeBInfo
 *
 */
?>


<?php _e( "This will <b>pull</b> data from the other site to make this site match the source site's database, media, etc. Contents of this site will be overwritten as needed. Verify the details below to make sure this is the correct deployment you wish to commence. You will be given the opportunity to test the changes and undo the database changes before making them permanent.", 'it-l10n-backupbuddy' ); ?>
<br><br>


<?php
$headFoot = array( __( '<b>Pulling</b> from (source)', 'it-l10n-backupbuddy' ), __( 'To this site (destination)', 'it-l10n-backupbuddy' ) );
$pushRows = array(
	'Site URL' => array( $deployData['remoteInfo']['siteurl'], $localInfo['siteurl'] ),
	'Home URL' => array( $deployData['remoteInfo']['homeurl'], $localInfo['homeurl'] ),
	'Max Execution Time' => array( $deployData['remoteInfo']['php']['max_execution_time'] . ' sec', $localInfo['php']['max_execution_time'] . ' sec' ),
	'Max Upload File Size' => array( $deployData['remoteInfo']['php']['upload_max_filesize'] . ' MB', $localInfo['php']['upload_max_filesize'] . ' MB' ),
	'Memory Limit' => array( $deployData['remoteInfo']['php']['memory_limit'] . ' MB', $localInfo['php']['memory_limit'] . ' MB' ),
	//'PHP Upload Limit' => array( $localInfo['php']['upload_max_filesize'], $deployData['remoteInfo']['php']['upload_max_filesize'] ),
	'WordPress Version' => array( $deployData['remoteInfo']['wordpressVersion'], $localInfo['wordpressVersion'] ),
	'BackupBuddy Version' => array( $deployData['remoteInfo']['backupbuddyVersion'], $localInfo['backupbuddyVersion'] ),
	'Active Plugins' => array( $activePluginsB, $activePluginsA ),
	'Active Theme' => array( $deployData['remoteInfo']['activeTheme'] . ' ' . $activeThemeBInfo, $localInfo['activeTheme'] ),
	'Media / Attachments' => array( $deployData['remoteInfo']['mediaCount'], $localInfo['mediaCount'] . ' (' . count( $deployData['pullMediaFiles'] ) . ' files to get)' ),
);
?>



<?php
$deployDirection = 'pull';
require( '_pushpull_foot.php' );
?>

