<?php

$default_name = NULL;
if ( 'add' == $mode ) {
	$default_name = 'My Deployment Site';
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
	'type'		=>		'textarea',
	'name'		=>		'api_key',
	'title'		=>		__( 'Remote API Key', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: foo] - Username to use when connecting to the FTP server.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[0-1000]',
	'css'		=>		'width: 100%;',
) );