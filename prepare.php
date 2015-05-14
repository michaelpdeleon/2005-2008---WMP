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
# $Id: prepare.php,v 1.62.2.4 2006/08/14 06:05:08 max Exp $
#
# This module provides compatibility with different hostings and versions of PHP.
#

if ( !defined('XCART_START') ) { header("Location: index.php"); die("Access denied"); }

@include $xcart_dir."/check_requirements.php";

#
#
# DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
# YOU REALLY KNOW WHAT ARE YOU DOING
#
#

set_magic_quotes_runtime(0);
ini_set("magic_quotes_sybase",0);
ini_set("session.bug_compat_42",1);
ini_set("session.bug_compat_warn",0);

$__quotes_qpc = get_magic_quotes_gpc();

function func_microtime() {
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
}

function func_unset(&$array) {
	$keys = func_get_args();
	array_shift($keys);
	if (!empty($keys) && !empty($array) && is_array($array)) {
		foreach ($keys as $key) {
			if (@isset($array[$key]))
				unset($array[$key]);
		}
	}
}

# responsible version of empty()
function zerolen() {
	foreach (func_get_args() as $arg) {
		if (strlen($arg) == 0) return true;
	}

	return false;
}

function func_array_map($func, $var) {
	if (!is_array($var)) return $var;

	foreach($var as $k=>$v)
		$var[$k] = call_user_func($func,$v);

	return $var;
}

function func_array_merge() {
	$vars = func_get_args();

	$result = array();
	if (!is_array($vars) || empty($vars)) {
		return $result;
	}

	foreach($vars as $v) {
		if (is_array($v) && !empty($v)) {
			$result = array_merge($result, $v);
		}
	}

	return $result;
}

function func_addslashes($var) {
	return is_array($var) ? func_array_map('func_addslashes', $var) : addslashes($var);
}

function func_stripslashes($var) {
	return is_array($var) ? func_array_map('func_stripslashes', $var) : stripslashes($var);
}

function func_array_key_exists($key, $search) {
	if (function_exists("array_key_exists")) {
		return array_key_exists($key, $search);

	} elseif (!isset($search[$key])) {
		foreach ($search as $k => $v) {
			if ($k === $key)
				return true;
		}

		return false;
	}

	return true;
}

function func_strip_tags($var) {
	return is_array($var) ? func_array_map('func_strip_tags', $var) : strip_tags($var);
}

function func_have_script_tag($var) {
	if (!is_array($var)) {
		return (stristr($var, '<script') !== false);
	}
	foreach ($var as $item) {
		if (!is_array($var)) {
			if (stristr($var, '<script') !== false) return true;
		}
		elseif (func_have_script_tag($item)) return true;
	}
	return false;
}

function func_allowed_var($name) {
	global $reject;
	if (in_array($name,$reject) && !defined('ADMIN_UNALLOWED_VAR_FLAG')) {
		define('ADMIN_UNALLOWED_VAR_FLAG',1);
	}
	return !in_array($name,$reject);
}

#
# Wrapper for version_compare() function
#
function func_version_compare($ver1, $ver2) {
	if (function_exists("version_compare"))
		return version_compare($ver1, $ver2);

	$ver1 = str_replace("..", ".", preg_replace("/([^\d\.]+)/S", ".\\1.", str_replace(array("_", "-", "+"), array(".", ".", "."), $ver1)));
	$ver2 = str_replace("..", ".", preg_replace("/([^\d\.]+)/S", ".\\1.", str_replace(array("_", "-", "+"), array(".", ".", "."), $ver2)));

	$ratings = array(
		"/^dev$/i" => -100,
		"/^alpha$/i" => -90,
		"/^a$/i" => -90,
		"/^beta$/i" => -80,
		"/^b$/i" => -80,
		"/^RC$/i" => -70,
		"/^pl$/i" => -60
	);
	foreach ($ver1 as $k => $v) {
		if (!is_numeric($v))
			$v = preg_replace(array_keys($ratings), array_values($ratings), $v);

		if (!is_numeric($ver2[$k]))
			$ver2[$k] = preg_replace(array_keys($ratings), array_values($ratings), $ver2[$k]);

		$r = strcmp($v, $ver2[$k]);
		if ($r != 0)
			return $r;
	}

	return 0;
}

if (!defined("XCART_EXT_ENV")) {

if (isset($HTTP_COOKIE_VARS["is_robot"]) && $HTTP_COOKIE_VARS["is_robot"])
	define('IS_ROBOT', 1);

# strong validation for the SERVER variables
foreach ($HTTP_SERVER_VARS as $__var => $__res) {
	$HTTP_SERVER_VARS[$__var] = func_strip_tags($__res);
}

# simple validation for the GET variables
foreach ($HTTP_GET_VARS as $__var => $__res) {
	if (defined('USE_TRUSTED_GET_VARS') && in_array($__var, explode(",",USE_TRUSTED_GET_VARS))) continue;

	$HTTP_GET_VARS[$__var] = func_strip_tags($__res);
}
# simple validation for the COOKIE variables
foreach ($HTTP_COOKIE_VARS as $__var => $__res) $HTTP_COOKIE_VARS[$__var] = func_strip_tags($__res);

# validation for the POST variables: strip html tags from untrusted variables
foreach ($HTTP_POST_VARS as $__var => $__res) {
	if (defined("USE_TRUSTED_POST_VARIABLES") && in_array($__var, $trusted_post_variables)) {
		# ignore trusted variables: these variables used in product/category modify etc

		if (!defined("USE_TRUSTED_SCRIPT_VARS") && func_have_script_tag($__res)) {
			unset($$__var);
			unset($HTTP_POST_VARS[$__var]);
			if (isset($_POST))
				unset($_POST[$__var]);
		}

		continue;
	}
	else
		$HTTP_POST_VARS[$__var] = func_strip_tags($__res);
}

if (!$__quotes_qpc) {
	foreach(array("GET","POST","COOKIE") as $__avar)
		foreach (${"HTTP_".$__avar."_VARS"} as $__var => $__res) ${"HTTP_".$__avar."_VARS"}[$__var] = func_addslashes($__res);
}
unset($__avar, $__var, $__res);

$reject = array_keys(get_defined_vars());
$reject[] = 'reject';
$reject[] = "__name";
$reject[] = "__avar";
$reject[] = "GLOBALS";
$reject[] = "HTTP_GET_VARS";
$reject[] = "HTTP_POST_VARS";
$reject[] = "HTTP_SERVER_VARS";
$reject[] = "HTTP_ENV_VARS";
$reject[] = "HTTP_COOKIE_VARS";
$reject[] = "HTTP_POST_FILES";

# register allowed global variables from request
foreach(array("GET","POST","COOKIE","SERVER") as $__avar) {
	foreach (${"HTTP_".$__avar."_VARS"} as $__var => $__res) {
		if (func_allowed_var($__var))
			$$__var = $__res;
		else
			func_unset(${"HTTP_".$__avar."_VARS"}, $__var);
	}

	reset(${"HTTP_".$__avar."_VARS"});
}

foreach ($HTTP_POST_FILES as $__name => $__value) {
	if (!func_allowed_var($__name)) continue;
	$$__name = $__value["tmp_name"];
	foreach($__value as $__k=>$__v) {
		$__varname_ = $__name."_".$__k;
		if (!func_allowed_var($__varname_)) continue;
		$$__varname_ = $__v;
	}
}
unset($reject, $__avar, $__var, $__res);

}

#
# OS detection
#
define('X_DEF_OS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

if (!defined('PATH_SEPARATOR')) {
	if (X_DEF_OS_WINDOWS)
		define('PATH_SEPARATOR', ';');
	else
		define('PATH_SEPARATOR', ':');
}

if (ini_get("open_basedir") != "")
	ini_set("include_path", ".".PATH_SEPARATOR.$xcart_dir.DIRECTORY_SEPARATOR."Smarty-2.6.3");
 
if (empty($REQUEST_URI))
	$REQUEST_URI = $PHP_SELF.($QUERY_STRING?"?$QUERY_STRING":"");

@include $xcart_dir."/include/https_detect.php";

#
# HTTP_REFERER override
#
if($HTTP_GET_VARS['iframe_referer'])
	$HTTP_REFERER = urldecode($HTTP_GET_VARS['iframe_referer']);

if (!empty($HTTP_REFERER) && strncasecmp($HTTP_REFERER,'http://', 7) && strncasecmp($HTTP_REFERER,'https://', 8)) {
	$HTTP_REFERER = "";
	if (!empty($HTTP_SERVER_VARS['HTTP_REFERER'])) {
		unset($HTTP_SERVER_VARS['HTTP_REFERER']);
	}
	if (!empty($HTTP_GET_VARS['iframe_referer'])) {
		unset($HTTP_GET_VARS['iframe_referer']);
	}
}

#
# Proxy IP
#
$PROXY_IP = '';
if (!empty($HTTP_X_FORWARDED_FOR)) {
	$PROXY_IP = $HTTP_X_FORWARDED_FOR;
} elseif (!empty($HTTP_X_FORWARDED)) {
	$PROXY_IP = $HTTP_X_FORWARDED;
} elseif (!empty($HTTP_FORWARDED_FOR)) {
	$PROXY_IP = $HTTP_FORWARDED_FOR;
} elseif (!empty($HTTP_FORWARDED)) {
	$PROXY_IP = $HTTP_FORWARDED;
} elseif (!empty($HTTP_CLIENT_IP)) {
	$PROXY_IP = $HTTP_CLIENT_IP;
} elseif (!empty($HTTP_X_COMING_FROM)) {
	$PROXY_IP = $HTTP_X_COMING_FROM;
} elseif (!empty($HTTP_COMING_FROM)) {
	$PROXY_IP = $HTTP_COMING_FROM;
}

if(!empty($PROXY_IP)) {
	$CLIENT_IP = $PROXY_IP;
	$PROXY_IP = $REMOTE_ADDR;
} else {
	$CLIENT_IP = $REMOTE_ADDR;
}

if(isset($HTTP_GET_VARS['benchmark']) || isset($HTTP_POST_VARS['benchmark'])) {
	define("START_TIME", func_microtime());
}

#
# Miscellaneous constants
#

define('SECONDS_PER_DAY', 86400); # 60 * 60 * 24
define('SECONDS_PER_WEEK', 604800); # 60 * 60 * 24 * 7

#
# Aloow displaying content in functions, registered in register_shutdown_function()
#
$zlib_oc = ini_get("zlib.output_compression");
if (!empty($zlib_oc) || func_version_compare(phpversion(), "4.0.6") <= 0)
	define("NO_RSFUNCTION", true);

unset($zlib_oc);

?>
