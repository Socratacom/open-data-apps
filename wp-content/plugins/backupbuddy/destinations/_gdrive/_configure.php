<?php
/*
	Pre-populated variables coming into this script:
		$destination_settings
		$mode
*/

global $pb_hide_save;
global $pb_hide_test;
$pb_hide_save = true;
$pb_hide_test = true;

$default_name = NULL;

set_include_path( pb_backupbuddy::plugin_path() . '/destinations/gdrive/' . PATH_SEPARATOR . get_include_path());

if ( 'add' == $mode ) {
	if ( 'auth_gdrive' != pb_backupbuddy::_POST( 'gaction' ) ) {
		?>
		
		<ol>
			<li><a href="https://code.google.com/apis/console" target="_new" class="button secondary-button" style="vertical-align: 0;">Open Google API Console in a new window</a></li>
			<li>Expand "APIs & auth" from the left menu then select "APIs" beneath it</li>
			<li>Under "Browse APIs" find "Drive API" and click to turn it on (Important: Enable "Drive <b>API</b>" <i>not</i> "Drive SDK")</li>
			<li>Below "APIs" in the left menu select "Credentials"</li>
			<li>Click the "Create new Client ID" button</li>
			<li>Select Application Type of "Installed application" with the type of "Other"</li>
			<li>Click "Create Client ID"</li>
			<li>Copy & paste the 'Client ID' & 'Client Secret' below</li>
		</ol>
		
		<br><br>
		<h3>Google Drive Client ID for native application</h3>
		<form method="post" action="<?php echo pb_backupbuddy::ajax_url( 'destination_picker' ) . '&add=gdrive&callback_data=' . pb_backupbuddy::_GET( 'callback_data' ); ?>">
			<input type="hidden" name="gaction" value="auth_gdrive">
			<table class="form-table">
				<tr>
					<th scope="row">Client ID</th>
					<td><input type="text" name="client_id" style="width: 100%; max-width: 720px;"></td>
				</tr>
				<tr>
					<th scope="row">Client Secret</th>
					<td><input type="text" name="client_secret" style="width: 100%; max-width: 720px;"></td>
				</tr>
				<tr>
					<th scope="row">&nbsp;</th>
					<td><input class="button-primary" type="submit" value="Continue"></td>
				</tr>
			</table>
		</form>
		
		<?php
		return;
	}
	if ( 'auth_gdrive' == pb_backupbuddy::_POST( 'gaction' ) ) {
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Client.php' );
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Http/MediaFileUpload.php' );
		require_once( pb_backupbuddy::plugin_path() . '/destinations/gdrive/Google/Service/Drive.php' );
		
		$client_id = trim( pb_backupbuddy::_POST( 'client_id' ) );
		$client_secret = trim( pb_backupbuddy::_POST( 'client_secret' ) );
		$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';

		$client = new Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->setAccessType('offline'); // Required so that Google will return the refresh token.
		$client->addScope("https://www.googleapis.com/auth/drive");
		$service = new Google_Service_Drive($client);
		
		$auth_code = pb_backupbuddy::_POST( 'auth_code' );
		if ( '' != $auth_code ) {
			try {
				$result = $client->authenticate( $auth_code );
			} catch (Exception $e) {
				pb_backupbuddy::alert( 'Error Authenticating: ' . $e->getMessage() . ' Please go back, check codes, and try again.' );
				return;
			}
			
			/*
			echo '<br>';
			echo 'token: ' . $client->getAccessToken();
			echo '<br><br>';
			*/
			
			$destination_settings['tokens'] = $client->getAccessToken();
			
		}
		
		
		if ( '' == $destination_settings['tokens'] ) {
			?>
			<ol>
				<li><a href="<?php echo $client->createAuthUrl(); ?>" target="_new" class="button secondary-button" style="vertical-align: 0;">Click here to authorize BackupBuddy access to your Google Drive</a></li>
				<li>Copy & paste the provided code into the box below</li>
			</ol>
			
			<br>
			<form method="post">
				<input type="hidden" name="gaction" value="auth_gdrive">
				<input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
				<input type="hidden" name="client_secret" value="<?php echo $client_secret; ?>">
				
				<table class="form-table">
					<tr>
						<th scope="row">Auth Code</th>
						<td><input type="text" name="auth_code" style="width: 100%; max-width: 720px;"></td>
					</tr>
					<tr>
						<th scope="row">&nbsp;</th>
						<td><input class="button-primary" type="submit" value="Continue"></td>
					</tr>
				</table>
				
			</form>
			
			<?php
			
			return;
		}
		
		
		
		
	}
}


// Editing or add mode authed. Show settings.
$pb_hide_test = false;
$pb_hide_save = false;


if ( 'add' == $mode ) {
	$tokens = base64_encode( $destination_settings['tokens'] );
	$default_name = 'My Google Drive';
} else {
	$tokens = NULL;
	$client_id = NULL;
	$client_secret = NULL;
}


$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'title',
	'title'		=>		__( 'Destination name', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Name of the new destination to create. This is for your convenience only.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
	'default'	=>		$default_name,
) );

$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_burst',
	'title'		=>		__( 'Send per burst', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Default 10] - This is the amount of data that will be sent per burst within a single PHP page load. Reduce if hitting PHP memory limits. Chunking time limits will only be checked between bursts. Lower burst size if timeouts occur before chunking checks trigger.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' MB',
) );

$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'max_time',
	'title'		=>		__( 'Max time per chunk', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 30] - Enter 0 for no limit (aka no chunking). This is the maximum number of seconds per chunk. If this time is exceeded when a burst finishes then the next portion will be transferred on a new page load.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' secs',
) );

$settings_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'tokens',
	'default'	=>		$tokens,
) );
$settings_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'client_id',
	'default'	=>		$client_id,
) );
$settings_form->add_setting( array(
	'type'		=>		'hidden',
	'name'		=>		'client_secret',
	'default'	=>		$client_secret,
) );


