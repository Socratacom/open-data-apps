<?php
// Loaded from: controllers/pages/deploy.php iframe
defined('ABSPATH') or die( '404 Not Found' );

wp_enqueue_script( 'thickbox' );
wp_print_scripts( 'thickbox' );
wp_print_styles( 'thickbox' );
?>

<style>
	.database_restore_table_select {
		background: #FFF;
		padding: 8px;
		max-height: 200px;
		overflow: scroll;
		border: 1px solid #ececec;
	}
	.database_restore_table_select::-webkit-scrollbar {
		-webkit-appearance: none;
		width: 11px;
		height: 11px;
	}
	.database_restore_table_select::-webkit-scrollbar-thumb {
		border-radius: 8px;
		border: 2px solid white; /* should match background, can't be transparent */
		background-color: rgba(0, 0, 0, .1);
	}
</style>

<?php
// Incoming vars: $backupFile, $step
if ( ! current_user_can( pb_backupbuddy::$options['role_access'] ) ) {
	die( 'Error #473623. Access Denied.' );
}



$deployment_id = pb_backupbuddy::_GET( 'deployment' );
if ( ! isset( pb_backupbuddy::$options['deployments'][ $deployment_id ] ) ) {
	die( 'Error #8457474: Invalid deployment ID.' );
}
$deployment = pb_backupbuddy::$options['deployments'][ $deployment_id ];


// TODO: hardcoded for dev.
$deployment = array(
	'siteurl' => 'http://backupbuddy2/',
);



require_once( pb_backupbuddy::plugin_path() . '/classes/deploy.php' );
$deploy = new backupbuddy_deploy( $deployment['siteurl'] );

$localInfo = backupbuddy_api::getPreDeployInfo();
$status = $deploy->start( $localInfo );
if ( false === $status ) {
	$errors = $deploy->getErrors();
	if ( count( $errors ) > 0 ) {
		pb_backupbuddy::alert( 'Errors were encountered: ' . implode( ', ', $errors ) . ' If seeking support please click to Show Advanced Details above and provide a copy of the log.' );
	}
	return;
}



$deployData = $deploy->getState();
$deployDataJson = json_encode( $deployData );
echo '<script>console.log("deployData (len: ' . strlen( $deployDataJson ) . '):"); console.dir(' . $deployDataJson . ');</script>';





?>


<?php _e( "This will synchronize the destination site to match this site's database, media, etc. Contents of the destination site will be overwritten as needed. Verify the details below to make sure this is the correct deployment you wish to commence. You will be given the opportunity to test the changes and undo them before making them permanent.", 'it-l10n-backupbuddy' ); ?>
<br><br>




<style>
	.tdhead {
		font-weight: bold;
	}
</style>
<script>
jQuery(document).ready(function() {
	jQuery( '#deploy_profile_settings' ).click( function(e){
		e.preventDefault();
		tb_show( 'BackupBuddy', '<?php echo pb_backupbuddy::ajax_url( 'profile_settings' ); ?>&profile=' + jQuery( '#deploy_profile_selected' ).val() + '&callback_data=&TB_iframe=1&width=640&height=455', null );
	});
	
	jQuery( '#pb_backupbuddy_deploy_form' ).submit( function(e){
		e.preventDefault();
		window.location.href = '<?php echo pb_backupbuddy::ajax_url( 'deploy' ); ?>&step=run&deployment=<?php echo $deployment_id; ?>&backup_profile=' + jQuery( '#deploy_profile_selected' ).val();
		return false;
	});
});
</script>

<table class="widefat">
	<thead>
		<tr class="thead">
			<th>&nbsp;</th><th>This Site (source)</th><th>Deploying To (destination)</th>
		</tr>
	</thead>
	<tfoot>
		<tr class="thead">
			<th>&nbsp;</th><th>This Site (source)</th><th>Deploying To (destination)</th>
		</tr>
	</tfoot>
	<tbody>
		<tr class="entry-row alternate">
			<td class="tdhead">Site URL</td>
			<td><?php echo $localInfo['siteurl']; ?></td>
			<td><?php echo $deployData['remoteInfo']['siteurl']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Home URL</td>
			<td><?php echo $localInfo['homeurl']; ?></td>
			<td><?php echo $deployData['remoteInfo']['homeurl']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Max Execution Time</td>
			<td><?php echo $localInfo['php']['max_execution_time']; ?> sec</td>
			<td><?php echo $deployData['remoteInfo']['php']['max_execution_time']; ?> sec</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Max Upload File Size</td>
			<td><?php echo $localInfo['php']['upload_max_filesize']; ?> MB</td>
			<td><?php echo $deployData['remoteInfo']['php']['upload_max_filesize']; ?> MB</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Memory Limit</td>
			<td><?php echo $localInfo['php']['memory_limit']; ?> MB</td>
			<td><?php echo $deployData['remoteInfo']['php']['memory_limit']; ?> MB</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">PHP Upload Limit</td>
			<td><?php echo $localInfo['php']['upload_max_filesize']; ?> MB</td>
			<td><?php echo $deployData['remoteInfo']['php']['upload_max_filesize']; ?> MB</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">WordPress Version</td>
			<td><?php echo $localInfo['wordpressVersion']; ?></td>
			<td><?php echo $deployData['remoteInfo']['wordpressVersion']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">BackupBuddy Version</td>
			<td><?php echo $localInfo['backupbuddyVersion']; ?></td>
			<td><?php echo $deployData['remoteInfo']['backupbuddyVersion']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Active Plugins</td>
			<td><?php foreach( (array)$localInfo['activePlugins'] as $localPlugin ) { echo $localPlugin['name'] . ' v' . $localPlugin['version']; } ?></td>
			<td><?php foreach( (array)$deployData['remoteInfo']['activePlugins'] as $remotePlugin ) { echo $remotePlugin['name'] . ' v' . $remotePlugin['version']; } ?> (<?php echo count( $deployData['sendPlugins'] ); ?> plugins to update)</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Active Theme</td>
			<td><?php echo $localInfo['activeTheme']; ?></td>
			<td><?php print_r( $deployData['remoteInfo']['activeTheme'] ); ?> (<?php echo count( $deployData['sendThemeFiles'] ); ?> files to update)</td>
		</tr>
</tbody>
</table>








<h3>Database Find & Replace</h3>
The site URL (www and domain) and paths will be updated. Serialized data will be accounted for.<br>
<input type="text" value="<?php echo $localInfo['siteurl']; ?>" disabled> &rarr; <input type="text" value="<?php echo $deployData['remoteInfo']['siteurl']; ?>" disabled><br>
<input type="text" value="<?php echo $localInfo['abspath']; ?>" disabled> &rarr; <input type="text" value="<?php echo $deployData['remoteInfo']['abspath']; ?>" disabled><br>
<!-- <input type="text"> -&gt; <input type="text"> - +<br> -->
<br>


<h3>Database Backup Profile</h3>
<select name="backup_profile" id="deploy_profile_selected">
<?php
foreach( pb_backupbuddy::$options['profiles'] as $profile_id => $profile ) {
	if ( $profile['type'] == 'defaults' ) { continue; } // Skip showing defaults here...
	if ( $profile['type'] != 'db' ) { continue; } // Currently only support DB backup deployments...
	
	echo '<option value="' . $profile_id . '">' . htmlentities( $profile['title'] ) . '</option>';
}
?>
</select> &nbsp; <a href="javascript:void(0);" id="deploy_profile_settings"><img src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/dest_gear.png" height="14"></a>


<br><br>
<form id="pb_backupbuddy_deploy_form" method="post" action="?action=pb_backupbuddy_backupbuddy&function=rollback&step=1">
	<?php pb_backupbuddy::nonce(); ?>
	<input type="hidden" name="deployData" value="<?php echo base64_encode( serialize( $deployData ) ); ?>">
	<input type="submit" name="submitForm" class="button button-primary" value="<?php echo __('Begin Deployment') . ' &raquo;'; ?>">
	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	
	<a class="button button-secondary" onclick="jQuery('#pb_backupbuddy_advanced').toggle();">Advanced Options</a>
	<span id="pb_backupbuddy_advanced" style="display: none; margin-left: 15px;">
		<label><input type="checkbox" name="autoAdvance" value="1" checked="checked"> Auto Advance</label>
		&nbsp;&nbsp;&nbsp;
		<label>Source chunk time limit: <input size="5" maxlength="5" type="text" name="maxExecutionTime" value="<?php echo $localInfo['php']['max_execution_time']; ?>"> sec</label>
		&nbsp;&nbsp;&nbsp;
		<label>Destination chunk time limit: <input size="5" maxlength="5" type="text" name="maxExecutionTime" value="<?php echo $deployData['remoteInfo']['php']['max_execution_time']; ?>"> sec</label>
	</span>
	
</form>


<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
