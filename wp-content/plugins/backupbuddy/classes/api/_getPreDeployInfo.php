<?php
$upload_max_filesize = str_ireplace( 'M', '', @ini_get( 'upload_max_filesize' ) );
if ( ( ! is_numeric( $upload_max_filesize ) ) || ( 0 == $upload_max_filesize ) ) {
	$upload_max_filesize = 1;
}

$max_execution_time = str_ireplace( 's', '', @ini_get( 'max_execution_time' ) );
if ( ( ! is_numeric( $max_execution_time ) ) || ( 0 == $max_execution_time ) ) {
	$max_execution_time = 30;
}

$memory_limit = str_ireplace( 'M', '', @ini_get( 'memory_limit' ) );
if ( ( ! is_numeric( $memory_limit ) ) || ( 0 == $memory_limit ) ) {
	$memory_limit = 32;
}

$max_post_size = str_ireplace( 'M', '', @ini_get( 'post_max_size' ) );
if ( ( ! is_numeric( $max_post_size ) ) || ( 0 == $max_post_size ) ) {
	$max_post_size = 8;
}


$dbTables = array();
global $wpdb;
$rows = $wpdb->get_results( "SHOW TABLE STATUS", ARRAY_A );
foreach( $rows as $row ) {
	$dbTables[] = $row['Name'];
}



function backupbuddy_hashGlob( $root ) {
	$generate_sha1 = true;
	$files = (array) pb_backupbuddy::$filesystem->deepglob( $root );
	$root_len = strlen( $root );
	$hashedFiles = array();
	foreach( $files as $file_id => &$file ) {
		$stat = stat( $file );
		if ( FALSE === $stat ) { pb_backupbuddy::status( 'error', 'Unable to read file `' . $file . '` stat.' ); }
		$new_file = substr( $file, $root_len );
		
		$sha1 = '';
		if ( ( true === $generate_sha1 ) && ( $stat['size'] < 1073741824 ) ) { // < 100mb
			$sha1 = sha1_file( $file );
		}
		
		$hashedFiles[$new_file] = array(
			'size'		=> $stat['size'],
			'modified'	=> $stat['mtime'],
			'sha1'		=> $sha1,
		);
		unset( $files[$file_id] ); // Better to free memory or leave out for performance?
		
	}
	unset( $files );
	
	return $hashedFiles;
}


// List
function backupbuddy_dbMediaSince() {
	global $wpdb;
	$wpdb->show_errors(); // Turn on error display.
	
	$mediaFiles = array();
	
	$sql = "select " . $wpdb->prefix . "postmeta.meta_value as file," . $wpdb->prefix . "posts.post_modified as file_modified from " . $wpdb->prefix . "postmeta," . $wpdb->prefix . "posts WHERE meta_key='_wp_attached_file' AND " . $wpdb->prefix . "postmeta.post_id = " . $wpdb->prefix . "posts.id";
	$results = $wpdb->get_results( $sql, ARRAY_A );
	if ( ( null === $results ) || ( false === $results ) ) {
		pb_backupbuddy::status( 'error', 'Error #238933: Unable to calculate media with query `' . $sql . '`. Check database permissions or contact host.' );
	}
	foreach( (array)$results as $result ) {
		$mediaFiles[ $result['file'] ] = array(
			'modified'	=> $result['file_modified']
		);
	}
	unset( $results );
	return $mediaFiles;
}

$mediaSignatures = backupbuddy_dbMediaSince();

global $wp_version;
return array(
	'backupbuddyVersion'		=> pb_backupbuddy::settings( 'version' ),
	'wordpressVersion'			=> $wp_version,
	'localTime'					=> time(),
	'php'						=> array(
									'upload_max_filesize' => $upload_max_filesize,
									'max_execution_time' => $max_execution_time,
									'memory_limit' => $memory_limit,
									'max_post_size' => $max_post_size,
									),
	'abspath'					=> ABSPATH,
	'siteurl'					=> site_url(),
	'homeurl'					=> home_url(),
	'tables'					=> $dbTables,
	'dbPrefix'					=> $wpdb->prefix,
	'activePlugins'				=> backupbuddy_api::getActivePlugins(),
	'activeTheme'				=> get_template(),
	'themeSignatures'			=> backupbuddy_hashGlob( get_template_directory() ),
	'mediaSignatures'			=> $mediaSignatures,
	'mediaCount'				=> count( $mediaSignatures ),
	'notifications'				=> array(), // Array of string notification messages.
);

unset( $mediaSignatures );