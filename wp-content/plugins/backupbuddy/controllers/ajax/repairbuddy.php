<?php
if ( ! is_admin() ) { die( 'Access denied.' ); }

/* repairbuddy()
 *
 * Compile RepairBuddy and stream download to browser.
 *
 */


backupbuddy_core::repairbuddy(); // Outputs repairbuddy to browser for download.

die();