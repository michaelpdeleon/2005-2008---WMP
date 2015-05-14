<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2006 Ruslan R. Fazliev <rrf@rrf.ru>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazliev             |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2006           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: sessions.php,v 1.62.2.2 2006/06/08 10:43:27 svowl Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
# DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
# YOU REALLY KNOW WHAT ARE YOU DOING
# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
#

if (defined('XCART_SESSION_START'))
	return;

define("XCART_SESSION_START", 1);

if ($use_sessions_type == 2)
	include $xcart_dir."/include/mysql_sessions.php";

#
# PHP build-in sessions tuning (for type "1" & "2")
#

# PHP 4.3.0 and higher allow to turn off trans-sid using this command:
ini_set("url_rewriter.tags","");
# Let's garbage collection will occurs more frequently
ini_set("session.gc_probability",90);
ini_set("session.gc_divisor",100); # for PHP >= 4.3.0
ini_set("session.use_cookies", false);

#
# Anti cache block
#

if (defined("SET_EXPIRE")) {
	header("Expires: ".gmdate("D, d M Y H:i:s", SET_EXPIRE)." GMT");
} else {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
}
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

if (defined("SET_EXPIRE")) {
	header("Cache-Control: public");
}
elseif ($HTTPS) {
	header("Cache-Control: private, must-revalidate");
}
else {
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
}

if (isset($HTTP_POST_VARS[$XCART_SESSION_NAME]))
	$XCARTSESSID = $HTTP_POST_VARS[$XCART_SESSION_NAME];
elseif (isset($HTTP_GET_VARS[$XCART_SESSION_NAME]))
	$XCARTSESSID = $HTTP_GET_VARS[$XCART_SESSION_NAME];
elseif (isset($HTTP_COOKIE_VARS[$XCART_SESSION_NAME]))
	$XCARTSESSID = $HTTP_COOKIE_VARS[$XCART_SESSION_NAME];
else {
	$XCARTSESSID = false;
}

x_session_start($XCARTSESSID);
register_shutdown_function("x_session_save");
setcookie($XCART_SESSION_NAME, $XCARTSESSID, 0, "/", $xcart_https_host, 0);
setcookie($XCART_SESSION_NAME, $XCARTSESSID, 0, "/", $xcart_http_host, 0);

$smarty->assign("XCARTSESSNAME", $XCART_SESSION_NAME);
$smarty->assign("XCARTSESSID", $XCARTSESSID);



####################################################################
#   FUNCTIONS
####################################################################

#
# Start session
#
function x_session_start($sessid) {
	global $XCART_SESSION_VARS, $XCART_SESSION_NAME, $XCARTSESSID;
	global $sql_tbl, $config, $use_sessions_type;
	global $HTTP_SERVER_VARS;

	# $sessid should contain only '0'..'9' or 'a'..'z' or 'A'..'Z'
	if (strlen($sessid) > 32 || !empty($sessid) && !preg_match('!^[0-9a-zA-Z]+$!S', $sessid)) {
		$sessid = "";
	}

	$XCART_SESSION_VARS = array();
	#
	# For new sessions always generate unique id
	#

	$l = 0;
	if (isset($HTTP_SERVER_VARS["REMOTE_PORT"]))
		$l = $HTTP_SERVER_VARS["REMOTE_PORT"];

	list($usec, $sec) = explode(' ', microtime());
	srand((float) $sec + ((float) $usec * 1000000) + (float)$l);

	$sessid_is_empty = empty($sessid);
	if (!$sessid_is_empty && ($use_sessions_type == 2 || $use_sessions_type == 3)) {
		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'") == 0)
			$sessid = "";
	}

	if (empty($sessid)) {
		do {
			$sessid = md5(uniqid(rand()));
			$already_exists = false;
			if ($use_sessions_type==2 || $use_sessions_type==3) {
				$data = func_query_first_cell("SELECT count(*) FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");
				$already_exists = $data>0;
			}
		} while ($already_exists);
	}

	if ($use_sessions_type < 3) {
		if ($use_sessions_type == 2) {
			# restore handler for mysql sessions
			session_set_save_handler (
				'db_session_open',
				'db_session_close',
				'db_session_read',
				'db_session_write',
				'db_session_destroy',
				'db_session_gc'
				);
		}

		#
		# Using standard PHP sessions
		#
		session_cache_limiter('none');
		
		session_name($XCART_SESSION_NAME);
		if ($sessid)
			session_id($sessid);

		session_start();

		if (strlen(session_encode()) == 0) {
			define("NEW_SESSION", true);
			if (!$sessid_is_empty && $use_sessions_type == 1) {
				$sessid = md5(uniqid(rand()));
				session_id($sessid);
			}
		}

		$XCARTSESSID = session_id();

		return;
	}
	
	if (empty($config["Sessions"]["session_length"]))
		$config["Sessions"]["session_length"] = 30;

	$curtime = time();
	$expiry_time = $curtime + $config["Sessions"]["session_length"];

	db_query("DELETE FROM $sql_tbl[sessions_data] WHERE expiry<'".time()."'");

	# Erase old service array (Group editing of products functionality)
	if (defined("AREA_TYPE")) {
		if (constant("AREA_TYPE") == 'A' || constant("AREA_TYPE") == 'P') {
			$res = db_query("SELECT $sql_tbl[ge_products].geid FROM $sql_tbl[ge_products] LEFT JOIN $sql_tbl[sessions_data] ON $sql_tbl[ge_products].sessid = $sql_tbl[sessions_data].sessid WHERE $sql_tbl[sessions_data].sessid IS NULL");
			if ($res) {
				while ($row = db_fetch_row($res)) {
					func_ge_erase($row[0]);
				}

				db_free_result($res);
			}
		}
	}
	
	$sess_data = func_query_first("SELECT * FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");
	
	if ($sess_data) {
		$XCART_SESSION_VARS = unserialize($sess_data["data"]);
		db_query("UPDATE $sql_tbl[sessions_data] SET expiry='$expiry_time' WHERE sessid='$sessid'");
	}
	else {
		define("NEW_SESSION", true);
		db_query("REPLACE INTO $sql_tbl[sessions_data] (sessid, start, expiry, data) VALUES('$sessid', '$curtime', '$expiry_time', '')");
	}

	$XCARTSESSID = $sessid;
	setcookie($XCART_SESSION_NAME, $XCARTSESSID, 0, "/", "", 0);
}

#
# Change current session to session with specified ID
#
function x_session_id($sessid="") {
	global $sql_tbl, $use_sessions_type, $XCART_SESSION_VARS, $XCARTSESSID, $XCART_SESSION_UNPACKED_VARS;

	$XCART_SESSION_VARS = array();
	if ($use_sessions_type < 3) {
		#
		# Using standard PHP sessions
		#
		if ($sessid) {
			session_write_close();
			x_session_start($sessid);
			return;
		}

		$XCARTSESSID = session_id();

		return $XCARTSESSID;
	}

	if ($sessid) {
		$sess_data = func_query_first("SELECT * FROM $sql_tbl[sessions_data] WHERE sessid='$sessid'");
		$XCARTSESSID = $sessid;
		if ($sess_data) {
			$XCART_SESSION_VARS = unserialize($sess_data["data"]);
			if (!empty($XCART_SESSION_UNPACKED_VARS)) {
				foreach ($XCART_SESSION_UNPACKED_VARS as $var => $v) {
					if (isset($GLOBALS[$var]))
						unset($GLOBALS[$var]);

					unset($XCART_SESSION_UNPACKED_VARS[$var]);
				}
			}
		}
		else {
			x_session_start($sessid);
		}
	}
	else {
		$sessid = $XCARTSESSID;
	}

	return $sessid;
}

#
# Cut off variable if it is come from _GET, _POST or _COOKIES
#
function check_session_var($varname) {
	global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS;

	if (isset($HTTP_GET_VARS[$varname]) || isset($HTTP_POST_VARS[$varname]) || isset($HTTP_COOKIE_VARS[$varname]))
		return false;

	return true;
}

#
# Register variable XCART_SESSION_VARS array from the database
#
function x_session_register($varname, $default="") {
	global $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS;
	global $use_sessions_type;
	global $HTTP_SESSION_VARS;
	
	if (empty($varname))
		return false;

	if ($use_sessions_type < 3) {
		#
		# Using standard PHP sessions
		#
		if (!session_is_registered($varname) && check_session_var($varname)) {
			$HTTP_SESSION_VARS[$varname] = $default;
		}

		session_register($varname);

		#
		# Register global variable
		#
		$GLOBALS[$varname] =& $HTTP_SESSION_VARS[$varname];
		return;
	}

	#
	# Register variable $varname in $XCART_SESSION_VARS array
	#
	if (!isset($XCART_SESSION_VARS[$varname])) {
		if (isset($GLOBALS[$varname]) && check_session_var($varname)) {
			$XCART_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
		else {
			$XCART_SESSION_VARS[$varname] = $default;
		}
	}
	else {
		if (isset($GLOBALS[$varname]) && check_session_var($varname)) {
			$XCART_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}

	#
	# Unpack variable $varname from $XCART_SESSION_VARS array
	#
	$XCART_SESSION_UNPACKED_VARS[$varname] = $XCART_SESSION_VARS[$varname];
	$GLOBALS[$varname] = $XCART_SESSION_VARS[$varname];
}

#
# Save the XCART_SESSION_VARS array in the database
#
function x_session_save() {
	global $XCARTSESSID;
	global $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS;
	global $sql_tbl, $use_sessions_type, $bench_max_session;

	if ($use_sessions_type < 3) {
		#
		# Using standard PHP sessions
		#
		return;
	}

	$varnames = func_get_args();
	if (!empty($varnames)) {
		foreach ($varnames as $varname) {
			if (isset($GLOBALS[$varname]))
				$XCART_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}
	elseif (is_array($XCART_SESSION_UNPACKED_VARS)) {
		foreach ($XCART_SESSION_UNPACKED_VARS as $varname=>$value) {
			if (isset($GLOBALS[$varname]))
				$XCART_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}

	#
	# Save session variables in the database
	#
	if (defined("BENCH") && constant("BENCH")) {
		$len = strlen(serialize($XCART_SESSION_VARS));
		if ($bench_max_session < $len)
			$bench_max_session = $len;
	}

	db_query("UPDATE $sql_tbl[sessions_data] SET data='".addslashes(serialize($XCART_SESSION_VARS))."' WHERE sessid='$XCARTSESSID'");
}

#
# Unregister variable $varname from $XCART_SESSION_VARS array
#
function x_session_unregister($varname, $unset_global=false) {
	global $XCART_SESSION_VARS, $XCART_SESSION_UNPACKED_VARS;
	global $use_sessions_type;

	if (empty($varname))
		return false;

	if ($use_sessions_type < 3) {
		#
		# Using standard PHP sessions
		#
		session_unregister($varname);
		return;
	}

	func_unset($XCART_SESSION_VARS, $varname);
	func_unset($XCART_SESSION_UNPACKED_VARS, $varname);

	if ($unset_global) {
		func_unset($GLOBALS, $varname);
	}
}

#
# Find out whether a global variable $varname is registered in 
# $XCART_SESSION_VARS array
#
function x_session_is_registered($varname) {
	global $XCART_SESSION_VARS;
	global $use_sessions_type;

	if (empty($varname))
		return false;

	if ($use_sessions_type < 3) {
		#
		# Using standard PHP sessions
		#
		return session_is_registered($varname);
	}

	return isset($XCART_SESSION_VARS[$varname]);
}

?>
