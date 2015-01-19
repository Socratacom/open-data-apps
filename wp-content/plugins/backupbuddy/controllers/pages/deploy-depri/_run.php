<?php
print_r( $_POST );
echo '<hr>';

defined('ABSPATH') or die( '404 Not Found' );
if ( ! isset( pb_backupbuddy::$options['remote_destinations'][ pb_backupbuddy::_POST( 'destination' ) ] ) ) {
	die( 'Error #8457474: Invalid destination ID `' . htmlentities( pb_backupbuddy::_POST( 'destination' ) ) . '`.' );
}
$deployment = &pb_backupbuddy::$options['deployments'][ pb_backupbuddy::_GET( 'deployment' ) ];

$profileID = pb_backupbuddy::_POST( 'backup_profile' );
$backupSerial = pb_backupbuddy::random_string( 10 ); // Manually define serial so we can track this specific backup.
$backup_result = backupbuddy_api::runBackup( $profileID, $triggerTitle = 'Deployment', $backupMode = '', $backupSerial );
if ( true !== $backup_result ) {
	die( 'Error #85487755: Unable to start backup. Details: `' . $backup_result . '`.' );
}


print_r( backupbuddy_api::getLatestBackupStats() );


?>

<script>
	function checkDeployStatus( step ) {
		jQuery('#pb_backupbuddy_loading').show();
		if ( '' == step ) {
			step = '0';
		}
		stepCounter = 0; // How many status checks of this step have been done.
		
		jQuery.ajax({
			
			url:	'<?php echo pb_backupbuddy::ajax_url( 'deploy_status' ); ?>',
			type:	'post',
			data:	{ serial: '<?php echo $backupSerial; ?>', step: step, stepCounter: stepCounter, profileID: '<?php echo $profileID; ?>' },
			context: document.body,
			
			success: function( data ) {
				stepCounter = stepCounter + 1;
				console.log( data );
				
				jQuery('#pb_backupbuddy_loading').hide();
				isJSON = false;
				try {
					var json = jQuery.parseJSON( data );
					isJSON = true;
				} catch(e) {
					if ( data.indexOf( 'Fatal error' ) > -1 ) {
						alert( 'Fatal PHP Error: ' + data );
					} else {
						console.log( 'NOTjson:' + data );
					}
					isJSON = false;
				}
				
				if ( ( true === isJSON ) && ( null !== json ) ) {
					jQuery( '#backupbuddy_deploy_title' ).text( json.stepTitle );
					
					if ( 'undefined' != typeof json.statusStep ) {
						if ( 'backupStats' == json.statusStep ) {
							console.log( json.stats );
						}
					}
					
					if ( 'undefined' == typeof json.nextStep ) {
						setTimeout( 'checkDeployStatus( "0" )' , 2000 );
					} else if ( '-1' == json.nextStep ) {
						alert( 'Deploy done.' );
					} else {
						if ( json.nextStep != step ) { // If a NEW step, reset stepCounter.
							stepCounter = 0;
						}
						setTimeout( function(){ checkDeployStatus( json.nextStep ); }, 2000 );
						
					}
				}
				
			}, // end success.
			
			complete: function( jqXHR, status ) {
				if ( ( status != 'success' ) && ( status != 'notmodified' ) ) {
					jQuery('#pb_backupbuddy_loading').hide();
				}
			} // end complete.
			
		}); // end ajax.



	}
	
	jQuery(document).ready(function() {
		setTimeout( function(){
			checkDeployStatus( '0' );
		}, 2000);
	});
</script>

<div id="backupbuddy_deploy_title">
	Preparing to backup ...
</div>
<span id="pb_backupbuddy_loading" style="display: none; margin-left: 10px; float: left;"><img src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/loading.gif" <?php echo 'alt="', __('Loading...', 'it-l10n-backupbuddy' ),'" title="',__('Loading...', 'it-l10n-backupbuddy' ),'"';?> width="16" height="16" style="vertical-align: -3px;" /></span>


