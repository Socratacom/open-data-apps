<?php

// DO NOT CALL THIS CLASS DIRECTLY. CALL VIA: pb_backupbuddy_destination in bootstrap.php.

// As of BackupBuddy v5.0 by Dustin Bolton.
class pb_backupbuddy_destination_gdrive {
	
	const TIME_WIGGLE_ROOM = 5;								// Number of seconds to fudge up the time elapsed to give a little wiggle room so we don't accidently hit the edge and time out.
	
	public static $destination_info = array(
		'name'			=>		'Google Drive',
		'description'	=>		'Send files to Google Drive. <a href="https://drive.google.com" target="_new">Learn more here.</a>',
	);
	
	// Default settings. Should be public static for auto-merging.
	public static $default_settings = array(
		'type'				=>		'gdrive',	// MUST MATCH your destination slug.
		'title'				=>		'',			// Required destination field.
		'client_id'			=>		'',
		'client_secret'		=>		'',
		//'auth_code'			=>		'',			// User-pasted auth code
		'tokens'			=>		'',			// Empty string if not yet authed. base64 encoded json string of tokens once authed. Google stores tokens in a json encoded string.
		'directory'			=>		'',
		'archive_limit'		=>		0,
		'max_time'			=>		'30',	// Default max time in seconds to allow a send to run for. Set to 0 for no time limit. Aka no chunking.
		'max_burst'			=>		'10',	// Max size in mb of each burst within the same page load.
		'resume_point'		=>		'',		// fseek resume point (via ftell).
	);
	
	private static $_isConnected = false;
	private static $_client = '';
	private static $_drive = '';
	private static $_timeStart = 0;
	
	
	/* _normalizeSettings()
	 *
	 * Call on any incoming settings to normalize defaults, tokens format, etc.
	 *
	 */
	private static function _normalizeSettings( $settings ) {
		$settings = array_merge( self::$default_settings, $settings ); // Apply defaults.
		//echo 'TOKEN: ' .$settings['tokens'] . "\n\n\n";
		
		if ( strlen( $settings['tokens'] ) > 0 ) {
			// Currently base64 encoded. Unencode.
			if ( 0 == strpos( $settings['tokens'], '{' ) ) {
				$settings['tokens'] = base64_decode( $settings['tokens'] );
				//$settings['tokens'] = stripslashes( $settings['tokens'] );
			}
		}
		
		// If token are set but still in string format, change them into arrays.
		/*
		if ( strlen( $settings['tokens'] ) > 0 ) {
			if ( false === ( $settings['tokens'] = base64_decode( $settings['tokens'] ) ) ) {
				$settings['tokens'] = '';
				$error = 'Error #48387943: Unable to parse token data [base64_decode].';
				echo $error;
				pb_backupbuddy::status( 'error', $error );
			}
			if ( false === ( $settings['tokens'] = unserialize( $settings['tokens'] ) ) ) {
				$settings['tokens'] = '';
				$error = 'Error #32783733: Unable to parse token data [unserialize].';
				echo $error;
				pb_backupbuddy::status( 'error', $error );
			}
		}
		*/
		
		//print_r( $settings['tokens'] );
		
		return $settings;
	} // End _normalizeSettings().
	
	
	
	/* _connect()
	 *
	 * See http://stackoverflow.com/questions/15905104/automatically-refresh-token-using-google-drive-api-with-php-script
	 *
	 * @return	false|array 		false on failure to connect. Else Array of updated settings (token may be refreshed).
	 *
	 */
	private function _connect( $settings ) {
		
		if ( true === self::$_isConnected ) { // Already connected.
			return $settings;
		}
		
		set_include_path( pb_backupbuddy::plugin_path() . '/destinations/gdrive/' . PATH_SEPARATOR . get_include_path() );
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Client.php' );
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Http/MediaFileUpload.php' );
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Service/Drive.php' );
		
		$client_id = $settings['client_id'];
		$client_secret = $settings['client_secret'];
		$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
		
		self::$_client = new Google_Client();
		self::$_client->setClientId($client_id);
		self::$_client->setClientSecret($client_secret);
		self::$_client->setRedirectUri($redirect_uri);
		self::$_client->setAccessType('offline'); // Required so that Google will use the refresh token.
		self::$_client->addScope("https://www.googleapis.com/auth/drive");
		self::$_drive = new Google_Service_Drive( self::$_client );
		
		self::$_client->setAccessToken( $settings['tokens'] );
		
		/*
		try {
			$result = self::$_client->authenticate(); //  $auth_code 
		} catch (Exception $e) {
			pb_backupbuddy::alert( 'Error Authenticating: ' . $e->getMessage() . ' Please go back, check codes, and try again.' );
			return false;
		}
		*/
		
		// Update tokens in settings.
		$oldAccessTokens = json_decode( $settings['tokens']['refreshToken'], true );
		$newAccessToken = self::$_client->getAccessToken();
		//print_r( $newAccessToken );
		
		/*
		$accessTokens = json_decode( $newAccessToken, true );
		$accessTokens['refreshToken'] = $oldAccessTokens['refreshToken'];
		$settings['tokens'] = json_encode( $settings['tokens'] ); // Re-encode in JSON
		
		
		self::$_client->setAccessToken( $settings['tokens'] );
		*/
		
		$settings['tokens'] = $newAccessToken;
		
		self::$_isConnected = true;
		return $settings;
		
	} // End _connect().
	
	
	/*	send()
	 *	
	 *	Send one or more files.
	 *	
	 *	@param		array			$files		Array of one or more files to send.
	 *	@return		boolean						True on success, else false.
	 */
	public static function send( $settings = array(), $files = array(), $send_id = '', $delete_remote_after = false ) {
		self::$_timeStart = microtime( true );
		
		$settings = self::_normalizeSettings( $settings );
		if ( false === ( $settings = self::_connect( $settings ) ) ) {
			$error = 'Unable to connect with Google Drive. See log for details.';
			echo $error;
			pb_backupbuddy::status( 'error', $error );
			return false;
		}
		
		$chunkSizeBytes = $settings['max_burst'] * 1024 * 1024; // Send X mb at a time to limit memory usage.
		
		foreach( $files as $file ) {
			
			$fileSize = filesize( $file );
			$fileinfo = pathinfo( $file );
			$fileextension = $fileinfo['extension'];
			if ( 'zip' == $fileextension ) {
				$mimeType = 'application/zip';
			} elseif ( 'php' == $fileextension ) {
				$mimeType = 'application/x-httpd-php';
			} else {
				$mimeType = '';
			}
			pb_backupbuddy::status( 'details', 'About to upload file `' . $file . '` of size `' . $fileSize . '` with mimetype `' . $mimeType . '`. Internal chunk size of `' . $chunkSizeBytes . '` bytes.' );
			
			//Insert a file
			$driveFile = new Google_Service_Drive_DriveFile();
			$driveFile->setTitle( basename( $file ) );
			$driveFile->setDescription( 'BackupBuddy file' );
			$driveFile->setMimeType( $mimeType );
			
			self::$_client->setDefer( true );
			try {
				$insertRequest = self::$_drive->files->insert( $driveFile );
			} catch (Exception $e) {
				pb_backupbuddy::alert( 'Error #3232783268336: initiating upload. Details: ' . $e->getMessage() );
				return;
			}
			
			
			// See https://developers.google.com/api-client-library/php/guide/media_upload
			try {
				$media = new Google_Http_MediaFileUpload(
					self::$_client,
					$insertRequest,
					$mimeType,
					null,
					true,
					$chunkSizeBytes
				);
			} catch (Exception $e) {
				pb_backupbuddy::alert( 'Error #3893273937: initiating upload. Details: ' . $e->getMessage() );
				return;
			}
			$media->setFileSize( $fileSize );
			
			
			pb_backupbuddy::status( 'details', 'Opening file for sending in binary mode.' );
			$fs = fopen( $file , 'rb' );
			
			
			// If chunked resuming then seek to the correct place in the file.
			if ( ( '' != $settings['resume_point'] ) && ( $settings['resume_point'] > 0 ) ) { // Resuming send of a partially transferred file.
				if ( 0 !== fseek( $fs, $settings['resume_point'] ) ) { // Returns 0 on success.
					pb_backupbuddy::status( 'error', 'Error #3872733: Failed to seek file to resume point `' . $settings['resume_point'] . '` via fseek().' );
					return false;
				}
				$prevPointer = $settings['resume_point'];
			} else { // New file send.
				$prevPointer = 0;
			}
			
			$needProcessChunking = false; // Set true if we need to spawn off resuming to a new PHP page load.
			$uploadStatus = false;
			while (!$uploadStatus && !feof($fs)) {
				$chunk = fread($fs, $chunkSizeBytes);
				$uploadStatus = $media->nextChunk( $chunk );
				
				
				// Handle splitting up across multiple PHP page loads if needed.
				if ( !feof($fs) && ( 0 != $settings['max_time'] ) ) { // More data remains so see if we need to consider chunking to a new PHP process.
					// If we are within X second of reaching maximum PHP runtime then stop here so that it can be picked up in another PHP process...
					if ( ( ( microtime( true ) - self::$_timeStart ) + self::TIME_WIGGLE_ROOM ) >= $settings['max_time'] ) {
						pb_backupbuddy::status( 'message', 'Approaching limit of available PHP chunking time of `' . $settings['max_time'] . '` sec. Ran for ' . round( microtime( true ) - self::$_timeStart, 3 ) . ' sec. Proceeding to use chunking.' );
						@fclose( $fs );
						
						// Tells next chunk where to pick up.
						$settings['resume_point'] = $prevPointer;
						if ( isset( $chunksTotal ) ) {
							$settings['chunks_total'] = $chunksTotal;
						}
						
						// Schedule cron.
						$cronTime = time();
						$cronArgs = array( $settings, $files, $send_id, $delete_after = false );
						$cronHashID = md5( $cronTime . serialize( $cronArgs ) );
						$cronArgs[] = $cronHashID;
						
						$schedule_result = backupbuddy_core::schedule_single_event( $cronTime, pb_backupbuddy::cron_tag( 'destination_send' ), $cronArgs );
						if ( true === $schedule_result ) {
							pb_backupbuddy::status( 'details', 'Next Site chunk step cron event scheduled.' );
						} else {
							pb_backupbuddy::status( 'error', 'Next Site chunk step cron even FAILED to be scheduled.' );
						}
						spawn_cron( time() + 150 ); // Adds > 60 seconds to get around once per minute cron running limit.
						update_option( '_transient_doing_cron', 0 ); // Prevent cron-blocking for next item.
						
						
						return array( $prevPointer, 'Sent part ' . $settings['chunks_sent'] . ' of ~' . $settings['chunks_total'] . ' parts.' ); // filepointer location, elapsed time during the import
					} else { // End if.
						pb_backupbuddy::status( 'details', 'Not approaching time limit.' );
					}
				} else {
					pb_backupbuddy::status( 'details', 'No more data remains (eg for chunking) so finishing up.' );
				}
				
				
			}
			fclose($fs);
			
			self::$_client->setDefer( false );
			
			if ( false == $uploadStatus ) {
				global $pb_backupbuddy_destination_errors;
				$pb_backupbuddy_destination_errors[] = 'Error #84347474 sending. Details: ' . $uploadStatus;
				return false;
			} else { // Success.
				if ( true === $delete_remote_after ) {
					self::deleteFile( $settings, $uploadStatus->id );
				}
			}
			
		} // end foreach.
		
		// Made it this far then success.
		return true;
		
	} // End send().
	
	
	
	/*	test()
	 *	
	 *	Sends a text email with ImportBuddy.php zipped up and attached to it.
	 *	
	 *	@param		array			$settings	Destination settings.
	 *	@return		bool|string					True on success, string error message on failure.
	 */
	public static function test( $settings ) {
		$settings = self::_normalizeSettings( $settings );
		if ( false === ( $settings = self::_connect( $settings ) ) ) {
			$error = 'Unable to connect with Google Drive. See log for details.';
			echo $error;
			pb_backupbuddy::status( 'error', $error );
			return false;
		}
		
		
		pb_backupbuddy::status( 'details', 'Testing Google Drive destination. Sending ImportBuddy.php.' );
		pb_backupbuddy::anti_directory_browsing( backupbuddy_core::getTempDirectory(), $die = false );
		$importbuddy_temp = backupbuddy_core::getTempDirectory() . 'importbuddy_' . pb_backupbuddy::random_string( 10 ) . '.php.tmp'; // Full path & filename to temporary importbuddy
		backupbuddy_core::importbuddy( $importbuddy_temp ); // Create temporary importbuddy.
		
		$files = array( $importbuddy_temp );
		
		$results = self::send( $settings, $files, '', $delete_remote_after = true );
		
		@unlink( $importbuddy_temp );
		
		if ( true === $results ) {
			echo 'Success sending test file to Google Drive.';
			return true;
		} else {
			echo 'Failure sending test file to Google Drive.';
			return false;
		}
		
	} // End test().
	
	
	
	public static function listFiles( $settings, $query ) {
		$settings = self::_normalizeSettings( $settings );
		if ( false === ( $settings = self::_connect( $settings ) ) ) {
			$error = 'Unable to connect with Google Drive. See log for details.';
			echo $error;
			pb_backupbuddy::status( 'error', $error );
			return false;
		}
		
		$service = &self::$_drive;
		
		$result = array();
		$pageToken = NULL;

		do {
			try {
			  $parameters = array( 'q' => $query );
			  if ($pageToken) {
			    $parameters['pageToken'] = $pageToken;
			  }
			  $files = $service->files->listFiles($parameters);

			  $result = array_merge($result, $files->getItems());
			  $pageToken = $files->getNextPageToken();
			} catch (Exception $e) {
			  print "An error occurred: " . $e->getMessage();
			  $pageToken = NULL;
			}
		} while ($pageToken);
		
		return $result;
		
	} // End list().
	
	
	
	public static function deleteFile( $settings, $fileID ) {
		$settings = self::_normalizeSettings( $settings );
		if ( false === ( $settings = self::_connect( $settings ) ) ) {
			$error = 'Unable to connect with Google Drive. See log for details.';
			echo $error;
			pb_backupbuddy::status( 'error', $error );
			return false;
		}
		
		try {
			
			self::$_drive->files->delete( $fileID );
			
		} catch (Exception $e) {
			global $pb_backupbuddy_destination_errors;
			$pb_backupbuddy_destination_errors[] =  $e->getMessage();
			$error = $e->getMessage();
			echo $error;
			pb_backupbuddy::status( 'error', $error );
			return false;
		}
		
		return true;
	} // End deleteFile().
	
	
	
	
	
} // End class.

