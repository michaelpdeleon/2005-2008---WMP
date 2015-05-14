<?php 
# This code is released under the same license as PHP. 
# (http://www.php.net/license.html) 

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

function db_session_open($save_path, $session_name) {
	return true;
}

function db_session_close() {
	return true;
}

function db_session_read($SessionID) {
	global $sql_tbl;

	$SessionID = addslashes($SessionID);

	$session_data = @db_query("SELECT data FROM $sql_tbl[sessions_data] WHERE sessid='$SessionID'");

	if (db_num_rows($session_data) == 1) {
		return @db_result($session_data, 0);
	} else {
		return '';
	}
}

function db_session_write($SessionID, $val) {
	global $sql_tbl, $config;

	$SessionID = addslashes($SessionID);
	$val = addslashes($val);

	$SessionExists = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid='$SessionID'");

	$curtime = time();
	$expiry_time = $curtime + $config["Sessions"]["session_length"];

	if ($SessionExists == 0) { 
		$retval = @db_query("INSERT INTO $sql_tbl[sessions_data] (sessid, start, expiry, data) VALUES ('$SessionID', '$curtime', '$expiry_time', '$val')");
	} else {
		$retval = @db_query("UPDATE $sql_tbl[sessions_data] SET data='$val', expiry='$expiry_time' WHERE sessid='$SessionID'");
	}

	return $retval;
}

function db_session_destroy($SessionID) {
	global $sql_tbl;

	$SessionID = addslashes($SessionID); 
	$retval = @db_query("DELETE FROM $sql_tbl[sessions_data] WHERE sessid='$SessionID'");

	return $retval; 
}

function db_session_gc($maxlifetime=0) { 
	global $sql_tbl; 

	$retval = @db_query("DELETE FROM $sql_tbl[sessions_data] WHERE expiry<'".time()."'");

	return $retval;
}

session_set_save_handler (
	'db_session_open',
	'db_session_close',
	'db_session_read',
	'db_session_write',
	'db_session_destroy',
	'db_session_gc'
);
?>
