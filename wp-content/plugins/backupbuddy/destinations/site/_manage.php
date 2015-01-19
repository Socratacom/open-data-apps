<?php
// Incoming vars: $destination, $destination_id

//pb_backupbuddy::$ui->title( 'Deployment' );
include( pb_backupbuddy::plugin_path() . '/classes/remote_api.php' );

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
$deployment = backupbuddy_remote_api::key_to_array( $destination['api_key'] );



require_once( pb_backupbuddy::plugin_path() . '/classes/deploy.php' );
$deploy = new backupbuddy_deploy( $destination['api_key'] );
?>


<style>
	.deploy-push-text {
		//font-size: 1.4em;
		padding: 7px;
		
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}
	.deploy-pull-text {
		//font-size: 1.4em;
		padding: 7px;
		
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}
	.deploy-status-up {
		display: inline-block;
		background: #0074a2;
		font-size: 1.4em;
		padding: 14px;
		color: #FFF;
		
		opacity: 0.5;
		
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}
	.deploy-pushpull-wrap {
		font-size: 1.4em !important;
	}
	.deploy-sites-table td {
		padding: 30px;
		vertical-align: middle;
	}
	.deploy-type-selected {
		font-weight: bold;
		background: #efefef;
	}
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
		
		/*
		jQuery( '#pb_backupbuddy_deploy_form' ).submit( function(e){
			e.preventDefault();
			window.location.href = '<?php echo pb_backupbuddy::ajax_url( 'deploy' ); ?>&step=run&deployment=<?php echo $destination_id; ?>&backup_profile=' + jQuery( '#deploy_profile_selected' ).val();
			return false;
		});
		*/

	});
</script>


<table class="widefat deploy-sites-table">
	<tr>
		<td><span class="deploy-status-up">Site UP</span></td>
		<td><?php echo $deploy->_state['destination']['siteurl']; ?></td>
		<td class="deploy-pushpull-wrap">
			<a href="#" class="deploy-push-text" onClick="jQuery( '.deploy-type-selected' ).removeClass( 'deploy-type-selected' ); jQuery(this).addClass( 'deploy-type-selected' ); jQuery('#deploy-pull-wrap').hide(); jQuery('#deploy-push-wrap').slideDown(); jQuery('#backupbuddy_deploy_direction').attr('data-direction','push' ); jQuery( '.database_contents_shortcuts-prefix' ).click();">Push to</a>
			&nbsp;|&nbsp;
			<a href="#"class="deploy-pull-text" onClick="jQuery( '.deploy-type-selected' ).removeClass( 'deploy-type-selected' ); jQuery(this).addClass( 'deploy-type-selected' ); jQuery('#deploy-push-wrap').hide(); jQuery('#deploy-pull-wrap').slideDown(); jQuery('#backupbuddy_deploy_direction').attr('data-direction','pull' ); jQuery( '.database_contents_shortcuts-prefix' ).click();">Pull from</a>
		</td>
	</tr>
</table>




<?php
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











$activePluginsA = '';
$i = 0; $x = count( $localInfo['activePlugins'] );
foreach( (array)$localInfo['activePlugins'] as $localPlugin ) {
	$i++;
	$activePluginsA .= $localPlugin['name'] . ' v' . $localPlugin['version'];
	if ( $x > $i ) {
		$activePluginsA .= ', ';
	}
}


$activePluginsB = '';
$i = 0; $x = count( $deployData['remoteInfo']['activePlugins'] );
foreach( (array)$deployData['remoteInfo']['activePlugins'] as $remotePlugin ) {
	$i++;
	$activePluginsB .= $remotePlugin['name'] . ' v' . $remotePlugin['version'];
	if ( $x > $i ) {
		$activePluginsB .= ', ';
	}
}
$activePluginsB .= ' (' . count( $deployData['pushPluginFiles'] ) . ' files to send)';



if ( $deployData['remoteInfo']['activeTheme'] == $localInfo['activeTheme'] ) {
	$activeThemeBInfo = ' ' . count( $deployData['pushThemeFiles'] ) . ' files to send';
} else {
	$activeThemeBInfo = ' (' . __( 'Active theme differs so not updating.', 'it-l10n-backupbuddy' ) . ')';
}

?>
<br><br>

<span id="backupbuddy_deploy_direction" data-direction=""></span>
<span id="backupbuddy_deploy_prefixA" data-prefix="<?php echo $localInfo['dbPrefix']; ?>"></span>
<span id="backupbuddy_deploy_prefixB" data-prefix="<?php echo $deployData['remoteInfo']['dbPrefix']; ?>"></span>

<div id="deploy-push-wrap" style="display: none;">
	<?php require_once( '_push.php' ); ?>
</div>


<div id="deploy-pull-wrap" style="display: none;">
	<?php require_once( '_pull.php' ); ?>
</div>


<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
