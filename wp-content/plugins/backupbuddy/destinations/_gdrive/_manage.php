<?php
// Settings.
if ( isset( pb_backupbuddy::$options['remote_destinations'][pb_backupbuddy::_GET('destination_id')] ) ) {
	$settings = &pb_backupbuddy::$options['remote_destinations'][pb_backupbuddy::_GET('destination_id')];
} else {
	die( 'Error #844893: Invalid destination ID.' );
}
$urlPrefix = pb_backupbuddy::ajax_url( 'remoteClient' ) . '&destination_id=' . htmlentities( pb_backupbuddy::_GET( 'destination_id' ) );


require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/init.php' );



// Handle deletion.
if ( pb_backupbuddy::_POST( 'bulk_action' ) == 'delete_backup' ) {
	pb_backupbuddy::verify_nonce();
	$deleted_files = 0;
	foreach( (array)pb_backupbuddy::_POST( 'items' ) as $item ) {
		
		$response = pb_backupbuddy_destination_gdrive::deleteFile( $settings, $item );
		if ( true === $response ) {
			$deleted_files++;
		} else {
			pb_backupbuddy::alert( 'Error: Unable to delete `' . $item . '`. Verify permissions.' );
		}
		
		
	}
	
	if ( $deleted_files > 0 ) {
		pb_backupbuddy::alert( 'Deleted ' . $deleted_files . ' file(s).' );
	}
	echo '<br>';
}
?>


<span id="backupbuddy_gdrive_loading"><h3><img src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/loading.gif" alt="' . __('Loading...', 'it-l10n-backupbuddy' ) . '" title="' . __('Loading...', 'it-l10n-backupbuddy' ) . '" width="16" height="16" style="vertical-align: -3px;"> <?php _e( 'Loading...', 'it-l10n-backupbuddy' ); ?></h3></span>


<?php
pb_backupbuddy::flush();


$files = pb_backupbuddy_destination_gdrive::listFiles( $settings, "title contains 'backup'" );
if ( false === $files ) {
	die( 'Error #834843: Error attempting to list files.' );
}
?>
<script>jQuery( '#backupbuddy_gdrive_loading' ).hide();</script>
<?php

/*
echo '<pre>';
print_r( $files );
echo '</pre>';
*/

$backup_files = array();
foreach( $files as $file ) {
	//echo 'file: ' .$file->originalFilename . '<br>';
	if ( '' == $file->originalFilename ) {
		continue;
	}
	
	$created = strtotime( $file->createdDate );
	
	$backup_files[ $file->id ] = array(
		array( $file->id, $file->originalFilename ),
		pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $created ) ) . '<br /><span class="description">(' . pb_backupbuddy::$format->time_ago( $created ) . ' ago)</span>',
		pb_backupbuddy::$format->file_size( $file->fileSize ),
		'??moose??'
	);
}



// Render table listing files.
if ( count( $backup_files ) == 0 ) {
	echo '<b>';
	_e( 'You have not completed sending any backups to Google Drive for this site yet.', 'it-l10n-backupbuddy' );
	echo '</b>';
} else {
	pb_backupbuddy::$ui->list_table(
		$backup_files,
		array(
			'action'		=>	pb_backupbuddy::ajax_url( 'remoteClient' ) . '&function=remoteClient&destination_id=' . htmlentities( pb_backupbuddy::_GET( 'destination_id' ) ) . '&remote_path=' . htmlentities( pb_backupbuddy::_GET( 'remote_path' ) ),
			'columns'		=>	array( 'Backup File', 'Uploaded <img src="' . pb_backupbuddy::plugin_url() . '/images/sort_down.png" style="vertical-align: 0px;" title="Sorted most recent first">', 'File Size', 'Type' ),
			'hover_actions'	=>	array( $urlPrefix . '&cpy_file=' => 'Copy to Local', $urlPrefix . '&downloadlink_file=' => 'Get download link' ),
			'hover_action_column_key'	=>	'0',
			'bulk_actions'	=>	array( 'delete_backup' => 'Delete' ),
			'css'			=>		'width: 100%;',
		)
	);
}