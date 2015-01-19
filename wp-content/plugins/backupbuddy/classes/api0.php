<?php
class backupbuddy_api0 {
	
	/* getLastError() -- NOT YET IMPLEMENTED.
	 *
	 * Retrieve the last error the API encountered. Use if a method returned bool FALSE to get message.
	 *
	 */
	public static function getLastError() {
	}
	
	
	// backupbuddy_api0::getOverview()
	public static function getOverview() {
		self::_before();
		
		return array(
			'backupbuddyVersion'		=> pb_backupbuddy::settings( 'version' ),
			'localTime'					=> time(),
			'lastBackupStart'			=> pb_backupbuddy::$options['last_backup_start'],
			'lastBackupSerial'			=> pb_backupbuddy::$options['last_backup_serial'],
			'lastBackupStats'			=> pb_backupbuddy::$options['last_backup_stats'],
			'editsSinceLastBackup'		=> pb_backupbuddy::$options['edits_since_last'],
			'scheduleCount'				=> count( pb_backupbuddy::$options['schedules'] ),
			'profileCount'				=> count( pb_backupbuddy::$options['profiles'] ),
			'destinationCount'			=> count( pb_backupbuddy::$options['remote_destinations'] ),
			'notifications'				=> array(), // Array of string notification messages.
		);
	}
	
	
	// backupbuddy_api0::getSchedules()
	public static function getSchedules() {
		self::_before();
		
		$schedules = array();
		foreach( pb_backupbuddy::$options['schedules'] as $schedule_id => $schedule ) {
			$schedules[] = array(
				'title' => strip_tags( $schedule['title'] ),
				'type' => pb_backupbuddy::$options['profiles'][$schedule['profile']]['type'],
				'interval' => $schedule['interval'],
				'lastRun' => $schedule['last_run'],
				'enabled' => $schedule['on_off'],
				'profileID' => $schedule['profile'],
				'profileTitle' => strip_tags( pb_backupbuddy::$options['profiles'][$schedule['profile']]['title'] ),
				'id' => $schedule_id
			);
		}
		return $schedules;
	}
	
	
	
	private static function _before() {
	}
	
} // end class.