<?php
// Incoming vars:
// $serial, $specialAction = '', $initRetryCount = 0, $sqlFile = ''


$init_wait_retry_count = $initRetryCount;
$echoNotWrite = $echo;

// Forward all logging to this serial file.
pb_backupbuddy::set_status_serial( $serial );

if ( true == get_transient( 'pb_backupbuddy_stop_backup-' . $serial ) ) {
	pb_backupbuddy::status( 'message', 'Backup STOPPED. Post backup cleanup step has been scheduled to clean up any temporary files.', $serial );
	
	require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
	$fileoptions_file = backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt';
	pb_backupbuddy::status( 'details', 'Fileoptions instance #30.' );
	$backup_options = new pb_backupbuddy_fileoptions( $fileoptions_file, false, $ignore_lock = true );
	
	if ( true !== ( $result = $backup_options->is_ok() ) ) {
		pb_backupbuddy::status( 'error', 'Unable to access fileoptions file `' . $fileoptions_file . '`.', $serial );
	}
	
	// Wipe backup file.
	if ( isset( $backup_options->options['archive_file'] ) && file_exists( $backup_options->options['archive_file'] ) ) { // Final zip file.
		$unlink_result = @unlink( $backup_options->options['archive_file'] );
		if ( true === $unlink_result ) {
			pb_backupbuddy::status( 'details', 'Deleted stopped backup ZIP file.', $serial );
		} else {
			pb_backupbuddy::status( 'error', 'Unable to delete stopped backup file. You should delete it manually as it may be damaged from stopping mid-backup. File to delete: `' . $backup_options->options['archive_file'] . '`.', $serial );
		}
	} else {
		pb_backupbuddy::status( 'details', 'Archive file not found. Not deleting.', $serial );
	}
	
	// NOTE: fileoptions file will be wiped by periodic cleanup. We need to keep this for now...
	
	delete_transient( 'pb_backupbuddy_stop_backup-' . $serial );
	pb_backupbuddy::status( 'details', 'Backup stopped. Any remaining processes or files will time out and be cleaned up by scheduled housekeeping functionality.', $serial );
	pb_backupbuddy::status( 'haltScript', '', $serial ); // Halt JS on page.
}

// Make sure the serial exists.
if ( $serial != '' ) {
	require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
	$fileoptions_file = backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt';
	//pb_backupbuddy::status( 'details', 'Fileoptions instance #29.' );
	$backup_options = new pb_backupbuddy_fileoptions( $fileoptions_file, $read_only = true, $ignore_lock = true );
	$backup = &$backup_options->options;
	if ( true !== ( $result = $backup_options->is_ok() ) ) {
		if ( 0 >= $init_wait_retry_count ) {
			// Waited too long for init to complete, must be something wrong
			pb_backupbuddy::status( 'error', 'Error #8329754.  Error retrieving fileoptions file `' . $fileoptions_file . '`. Error details `' . $result . '`.', $serial );
			pb_backupbuddy::status( 'haltScript', '', $serial );
			die();
		} else {
			pb_backupbuddy::status( 'details', 'Waiting for the fileoptions initialization for serial `' . $serial . '` to complete: ' . $init_wait_retry_count, $serial );				
			pb_backupbuddy::status( 'wait_init', '', $serial );
		}
		
	}
}
if ( ( $serial == '' ) || ( !is_array( $backup ) ) ) {
	pb_backupbuddy::status( 'error', 'Error #9031. Invalid backup serial (' . htmlentities( $serial ) . '). Please check directory permissions for your wp-content/uploads/ directory recursively, your PHP error_log for any errors, and that you have enough free disk space. If seeking support please provide this full status log and PHP error log. Fatal error. Verify this fileoptions file exists `' . $fileoptions_file . '`', $serial );
	pb_backupbuddy::status( 'haltScript', '', $serial );
	die();
} else {
	
	// Verify init completed.
	if ( false === $backup['init_complete'] ) {
		if ( 0 >= $init_wait_retry_count ) {
			// Waited too long for init to complete, must be something wrong
			pb_backupbuddy::status( 'error', 'Error #9033: The pre-backup initialization for serial `' . $serial . '` was unable save pre-backup initialization options (init_complete===false) possibly because the pre-backup initialization step did not complete. If the log indicates the pre-backup procedure did indeed complete then something prevented BackupBuddy from updating the database such as an misconfigured caching plugin. Check for any errors above or in logs. Verify permissions & that there is enough server memory. See the BackupBuddy "Server Information" page to help assess your server.', $serial );
			pb_backupbuddy::status( 'haltScript', '', $serial );
		} else {
			pb_backupbuddy::status( 'details', 'Waiting for the pre-backup initialization for serial `' . $serial . '` to complete: ' . $init_wait_retry_count, $serial );				
			pb_backupbuddy::status( 'wait_init', '', $serial );
		}
	}
	
	//***** Process any specialAction methods.
	if ( 'checkSchedule' == $specialAction ) {
		
		if ( FALSE === ( $next_scheduled = wp_next_scheduled( 'pb_backupbuddy_process_backup', array( $serial ) ) ) ) {
			//pb_backupbuddy::status( 'details', print_r( pb_backupbuddy::_POST(), true ), $serial );
			pb_backupbuddy::status( 'warning', 'WordPress reports the next step is not currently scheduled. It is either in the process of running or went missing. Consider enabled advanced setting missing cron rescheduling if this persists.', $serial, null, $echoNotWrite = true );
			
			if ( '1' == pb_backupbuddy::$options['backup_cron_rescheduling'] ) {
				pb_backupbuddy::status( 'details', 'Missing cron rescheduling enabled. Attempting to add the missing schedule back in.' );
				// Schedule event.
				$cron_time = time();
				$cron_tag = pb_backupbuddy::cron_tag( 'process_backup' );
				$cron_args = array( $serial );
				pb_backupbuddy::status( 'details', 'Scheduling next step to run at `' . $cron_time . '` (localized time: ' . pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $cron_time ) ) . ') with cron tag `' . $cron_tag . '` and serial arguments `' . implode( ',', $cron_args ) . '`.' );
				$schedule_result = backupbuddy_core::schedule_single_event( $cron_time, $cron_tag, $cron_args );
				if ( $schedule_result === false ) {
					pb_backupbuddy::status( 'error', 'Unable to reschedule missing cron step. Verify that another plugin is not preventing / conflicting.' );
				} else {
					pb_backupbuddy::status( 'details', 'Next step rescheduled.' );
					pb_backupbuddy::status( 'startAction', 'cronPass' ); // Resets the time this action began so we will not attempt re-scheduling a second time for a bit.
					pb_backupbuddy::status( 'cronParams', base64_encode( json_encode( array( 'time' => $cron_time, 'tag' => $cron_tag, 'args' => $cron_args ) ) ) );
				}
			}
			
		} else {
			pb_backupbuddy::status( 'details', 'Checked cron schedule. Next run: `' . $next_scheduled . '`. ' . ( $next_scheduled - time() ) . ' seconds from now.', $serial, null, $echoNotWrite = true );
		}
		
	}
	//***** End processing any specialAction methods.
	
	
	//***** Begin outputting status of the current step.
	$zipRunTime = 0;
	//error_log( print_r( $backup['steps'], true ) );
	foreach( $backup['steps'] as $step ) {
		if ( ( $step['start_time'] != -1 ) && ( $step['start_time'] != 0 ) && ( $step['finish_time'] == 0 ) ) { // A step isnt mark to skip, has begun but has not finished. This should not happen but the WP cron is funky. Wait a while before continuing.
			$thisRunTime = ( time() - $step['start_time'] );
			
			// For database dump step output the SQL file current size.
			if ( 'backup_create_database_dump' == $step['function'] ) {
				if ( '' != $sqlFile ) {
					$sqlFilename = $sqlFile;
					/* else {
						$sqlFilename = 'db_1.sql';
					} */
					$sql_file = $backup['temp_directory'] . $sqlFilename;
					if ( file_exists( $sql_file ) ) {
						$sql_filesize = filesize( $sql_file );
					} else { // No SQL file yet.
						$sql_filesize = 0;
					}
					
					$writeSpeedText = '';
					if ( $thisRunTime > 0 ) {
						$writeSpeed = $sql_filesize / $thisRunTime;
						$writeSpeedText = '. ' . __('Approximate creation speed', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $writeSpeed ) . '/sec [' . $writeSpeed . ']';
					}
					
					pb_backupbuddy::status( 'details', 'Current database dump file (' . basename( $sql_file ) . ') size: ' . pb_backupbuddy::$format->file_size( $sql_filesize ) . ' [' . $sql_filesize . '].' . $writeSpeedText, $serial );
				}
			}
			
			if ( 'backup_zip_files' == $step['function'] ) {
				$zipRunTime = $thisRunTime;
			}
			
			pb_backupbuddy::status( 'details', 'Waiting for function `' . $step['function'] . '` to complete. Started ' . $thisRunTime . ' seconds ago.', $serial );
			if ( ( time() - $step['start_time'] ) > 300 ) {
				pb_backupbuddy::status( 'warning', 'The function `' . $step['function'] . '` is taking an abnormally long time to complete (' . $thisRunTime . ' seconds). The backup may have failed. If it does not increase in the next few minutes it most likely timed out. See the Status Log for details.', $serial );
			}
			
		} elseif ( $step['start_time'] == 0 ) { // Step that has not started yet.
			// Do nothing.
		} elseif ( $step['start_time'] == -1 ) { // Step marked for skipping (backup stop button hit).
			// Do nothing.
		} else { // Last case: Finished. Skip.
			// Do nothing.
		}
	}
	//***** End outputting status of the current step.
	
	
	//***** Begin output of temp zip file size.
	$temporary_zip_directory = backupbuddy_core::getBackupDirectory() . 'temp_zip_' . $serial . '/';
	if ( file_exists( $temporary_zip_directory ) ) { // Temp zip file.
		$directory = opendir( $temporary_zip_directory );
		while( $file = readdir( $directory ) ) {
			if ( ( $file != '.' ) && ( $file != '..' ) && ( $file != 'exclusions.txt' ) && ( !preg_match( '/.*\.txt/', $file ) ) && ( !preg_match( '/pclzip.*\.gz/', $file) ) ) {
				$stats = stat( $temporary_zip_directory . $file );
				
				$writeSpeedText = '';
				if ( $zipRunTime > 0 ) {
					$writeSpeed = $stats['size'] / $zipRunTime;
					$writeSpeedText = '. ' . __('Approximate creation speed', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $writeSpeed ) . '/sec';
				}
				pb_backupbuddy::status( 'details', __('Temporary ZIP file size', 'it-l10n-backupbuddy' ) .': ' . pb_backupbuddy::$format->file_size( $stats['size'] ) . $writeSpeedText, $serial );
				pb_backupbuddy::status( 'archiveSize', pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
			}
		}
		closedir( $directory );
		unset( $directory );
	}
	//***** End output of temp zip file size.
	
	
	// Output different stuff to the browser depending on whether backup is finished or not.
	if ( $backup['finish_time'] > 0 ) { // BACKUP FINISHED.
		
		// OUTPUT COMPLETED ZIP FINAL SIZE.
		if ( 'pull' != $backup['deployment_direction'] ) { // not a pull type deployment.
			if( file_exists( $backup['archive_file'] ) ) { // Final zip file.
				$stats = stat( $backup['archive_file'] );
				pb_backupbuddy::status( 'details', '--- ' . __( 'New PHP process.' ), $serial );
				pb_backupbuddy::status( 'details', __('Completed backup final ZIP file size', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
				pb_backupbuddy::status( 'archiveSize', pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
				$backup_finished = true;
			} else {
				$purposeful_deletion = false;
				foreach( $backup['steps'] as $step ) {
					if ( $step['function'] == 'send_remote_destination' ) {
						if ( $step['args'][1] == true ) {
							pb_backupbuddy::status( 'details', 'Option to delete local backup after successful send enabled so local file deleted.' );
							$purposeful_deletion = true;
							break;
						}
					}
				}
				if ( $purposeful_deletion !== true ) {
					pb_backupbuddy::status( 'error', __( 'Backup reports success but unable to access final ZIP file. Verify permissions and ownership. If the error persists insure that server is properly configured with suphp and proper ownership & permissions.', 'it-l10n-backupbuddy' ), $serial );
				}
			}
		}
		pb_backupbuddy::status( 'message', __('Backup successfully completed in', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::$format->time_duration( $backup['finish_time'] - $backup['start_time'] ) . ' with BackupBuddy v' . pb_backupbuddy::settings( 'version' ) . '.', $serial );
		pb_backupbuddy::status( 'milestone', 'finish_backup', $serial );
	} else { // NOT FINISHED
		//$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . "|~|0|~|0|~|ping\n";
	}
	
	
	//***** Begin getting status log information.
	if ( '' != $backup['deployment_log'] ) {
		//error_log( print_r( $backup, true ) );
		pb_backupbuddy::status( 'details', 'About to retrieve remote deployment status log from `' . $backup['deployment_log'] . '`...', $serial );
		pb_backupbuddy::status( 'details', '~~~ Begin ImportBuddy Log section', $serial );
	}
	
	// Get local status log and output it.
	$status_lines = pb_backupbuddy::get_status( $serial, true, false, true ); // Clear file, dont unlink file, supress status retrieval msg.
	echo implode( '', $status_lines );
	
	// DEPLOYMENT OUTPUT.
	if ( '' != $backup['deployment_log'] ) {
		$response = wp_remote_get(
			$backup['deployment_log'],
			array(
				'method' => 'GET',
				'timeout' => 10, // X second delay. Should not take long to get a plain txt log file.
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => null,
				'cookies' => array()
			)
		);
		if( is_wp_error( $response ) ) { // Loopback failed. Some kind of error.
			$error = $response->get_error_message();
			pb_backupbuddy::status( 'error', 'Error retrieving remote deployment log. Details: `' . $error . '`.', $serial );
		} else {
			if ( '200' == $response['response']['code'] ) {
				//error_log( print_r( $response, true ) );
				echo $response['body'];
			} elseif ( '404' == $response['response']['code'] ) {
				// do nothing
			} else {
				pb_backupbuddy::status( 'error', 'Error retrieving remote deployment log. Response code: `' . $response['response']['code'] . '`.', $serial );
			}
		}
	}
	
	// Queue up a pong for the next response.
	pb_backupbuddy::status( 'message', __( 'Pong! Server replied.', 'it-l10n-backupbuddy' ), $serial );
}


return;

