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
# $Id: func.core.php,v 1.52.2.26 2006/08/15 05:41:17 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# Use this function to load code of functions on demand (include/func/func.*.php)
#
function x_load() {
	global $xcart_dir;

	$names = func_get_args();
	foreach ($names as $n) {
		$n = str_replace("..", "", $n);
		$f = $xcart_dir."/include/func/func.$n.php";

		if (file_exists($f)) {
			require_once $f;
		}
	}
}

#
# This function replaced standard PHP function header("Location...")
#
function func_header_location($location, $keep_https = true) {
	global $XCART_SESSION_NAME, $XCARTSESSID, $HTTP_COOKIE_VARS;
	global $use_sessions_type, $is_location;
	global $HTTP_SERVER_VARS;
	global $config, $HTTPS, $REQUEST_METHOD;

	$is_location = 'Y';
	x_session_save();

	if ($use_sessions_type < 3) {
		session_write_close();
	}

	# Start deletion by Michael de Leon 12.08.06 for XC SEO
	# if (!empty($XCARTSESSID) && !isset($HTTP_COOKIE_VARS[$XCART_SESSION_NAME]) && !eregi("$XCART_SESSION_NAME=", $location)) {
	# 	$location .= ((strpos($location, '?') != false)?'&':'?')."$XCART_SESSION_NAME=".$XCARTSESSID;
	# }

	if (!empty($XCARTSESSID) && !isset($HTTP_COOKIE_VARS[$XCART_SESSION_NAME]) && !eregi("$XCART_SESSION_NAME=", $location) && !defined('IS_ROBOT')) {
		$location .= ((strpos($location, '?') != false)?'&':'?')."$XCART_SESSION_NAME=".$XCARTSESSID;
	}

	if ($keep_https && $REQUEST_METHOD == "POST" && $HTTPS && strpos($location,'keep_https=yes') === false && $config["Security"]["dont_leave_https"] == "Y") {
		$location .= ((strpos($location, '?') != false)?'&':'?')."keep_https=yes";
		# this block is necessary (in addition to https.php) to prevent appearance of secure alert in IE
	}

	# Opera 8.51 (8.x ?) notes:
	# 1. Opera requires both headers - "Location" & "Refresh". Without "Location" it displays
	#    HTML code for META redirect
	# 2. 'Refresh' header is required when ansvering on POST request

	if (!@preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE"))) {
		# Microsoft IIS handle "Location:" headers internaly and repeat request
		# so this can lead to infinite loops
		@header("Location: ".$location);
	}

	if (strpos($HTTP_SERVER_VARS["HTTP_USER_AGENT"],'Opera')!==false
	|| @preg_match("/Microsoft|WebSTAR|Xitami/", getenv("SERVER_SOFTWARE"))) {
		@header("Refresh: 0; URL=".$location);
	}

	echo "<br /><br />".func_get_langvar_by_name("txt_header_location_note", array("time" => 2, "location" => $location), false, true);
	echo "<meta http-equiv=\"Refresh\" content=\"0;URL=$location\" />";

	func_flush();
	exit();
}

#
# Get county by code
#
function func_get_county ($countyid) {
	global $sql_tbl;

	$county_name = func_query_first_cell("SELECT county FROM $sql_tbl[counties] WHERE countyid='$countyid'");

	return ($county_name ? $county_name : $countyid);
}
#
# Get state by code
#
function func_get_state ($state_code, $country_code) {
	global $sql_tbl;

	$state_name = func_query_first_cell("SELECT state FROM $sql_tbl[states] WHERE country_code='$country_code' AND code='".addslashes($state_code)."'");

	return ($state_name ? $state_name : $state_code);
}

#
# Get country by code
#
function func_get_country ($country_code, $force_code = '') {
	global $sql_tbl, $shop_language;

	$code = (empty($force_code)?$shop_language:$force_code);
	$country_name = func_query_first_cell("SELECT value as country FROM $sql_tbl[languages] WHERE name='country_$country_code' AND code = '$code'");
	return ($country_name ? $country_name : $country_code);
}

#
# Convert price to "XXXXX.XX" format
#
function price_format($price) {
	return sprintf("%.2f", round((double)$price+0.00000000001, 2));
}

#
# Return number of available products
#
function insert_productsonline() {
	global $sql_tbl;

	return func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products] WHERE forsale!='N'");
}

#
# Return number of available items
#
function insert_itemsonline() {
	global $sql_tbl;

	return func_query_first_cell("SELECT SUM(avail) FROM $sql_tbl[products] WHERE forsale!='N'");
}

#
# This function returns true if $cart is empty
#
function func_is_cart_empty($cart) {
	return empty($cart) || empty($cart["products"]) && empty($cart["giftcerts"]);
}

#
# Get value of language variable by its name and usertype
#
function func_get_langvar_by_name($lang_name, $replace_to=NULL, $force_code = false, $force_output = false, $cancel_wm=false) {
	global $sql_tbl, $current_area, $config, $shop_language;
	global $smarty, $user_agent;
	global $predefined_lng_variables;

	$language_code = $shop_language;

	if ($force_code !== false)
		$language_code = $force_code;

	if ($force_output === false) {
		$predefined_lng_variables[] = $lang_name;
		if ($force_code === false)
			$language_code = "  ";

		$tmp = "";
		if (is_array($replace_to) && !empty($replace_to)) {
			foreach($replace_to as $k => $v) {
				$tmp .= "$k>$v<<<";
			}

			$tmp = substr($tmp, 0, -3);
		}

		return "~~~~|".$lang_name."|".$language_code."|".$tmp."|~~~~";
	}

	$result = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE code='$language_code' AND name='$lang_name'");
	if (empty($result)) {
		$_language_code = ($current_area == "C" ? $config["default_customer_language"] : $config["default_admin_language"]);
		if ($_language_code != $language_code) {
			$result = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE code='$_language_code' AND name='$lang_name'");
		}
		elseif ($language_code != 'US') {
			$result = func_query_first_cell("SELECT value FROM $sql_tbl[languages] WHERE code='US' AND name='$lang_name'");
		}
	}

	if (is_array($replace_to)) {
		foreach ($replace_to as $k=>$v)
			$result = str_replace("{{".$k."}}", $v, $result);
	}

	if ($smarty->webmaster_mode && !$cancel_wm)
		$result = func_webmaster_label($user_agent, $lang_name, $result);

	return $result;
}

#
# Flush output
#
function func_flush($s = NULL) {
	if (!is_null($s))
		echo $s;

	if (preg_match("/Apache(.*)Win/S", getenv("SERVER_SOFTWARE")))
		echo str_repeat(" ", 2500);
	elseif (preg_match("/(.*)MSIE(.*)\)$/S", getenv("HTTP_USER_AGENT")))
		echo str_repeat(" ", 256);

	if (function_exists('ob_flush')) {
		# for PHP >= 4.2.0
		ob_flush();
	}
	else {
		# for PHP < 4.2.0
		if (ob_get_length() !== FALSE)
		       ob_end_flush();
	}

	flush();
}

#
# This function added the ability to redirect a user to another page using HTML meta tags
# (without using header() function or Javascript)
#
function func_html_location($url, $time=3) {
	x_session_save();

	if ($use_sessions_type < 3) {
		session_write_close();
	}

	echo "<br /><br />".func_get_langvar_by_name("txt_header_location_note", array("time" => $time, "location" => $url),false,true);
	echo "<meta http-equiv=\"Refresh\" content=\"$time;URL=$url\">";
	func_flush();

	exit;
}

#
# This function returns the language variable value by name and language code
#
function func_get_languages_alt($name, $lng_code = false, $force_get = false) {
	global $sql_tbl, $shop_language, $config, $current_area;

	if ($lng_code === false)
		$lng_code = $shop_language;

	if ($force_get) {
		# Force get language variable(s) content
		$is_array = is_array($name);
		if (!$is_array)
			$name = array($name);

		if ($current_area == 'C' || $current_area == 'B') {
			$lngs = array($lng_code, $config['default_customer_language'], $config['default_admin_language'], false);
		} else {
			$lngs = array($lng_code, $config['default_admin_language'], $config['default_customer_language'], false);
		}
		$lngs = array_unique($lngs);

		$hash = array();
		foreach ($lngs as $lng_code) {
			$where = '';
			if ($lng_code !== false)
				$where = " AND code = '$lng_code'";

			$res = func_query_hash("SELECT name, value FROM $sql_tbl[languages_alt] WHERE name IN ('".implode("','", $name)."')".$where, "name", false, true);

			if (empty($res))
				continue;

			foreach($res as $n => $l) {
				if (!isset($hash[$n])) {
					$hash[$n] = $l;
					$idx = array_search($n ,$name);
					if ($ids !== false)
						unset($name[$idx]);
				}
			}

			if (empty($name))
				break;
		}

		return !$is_array ? array_shift($hash) : $hash;
	}

	if (is_array($name)) {
		return func_query_hash("SELECT name, value FROM $sql_tbl[languages_alt] WHERE code='$lng_code' AND name IN ('".implode("','", $name)."')", "name", false, true);
	}

	return func_query_first_cell("SELECT value FROM $sql_tbl[languages_alt] WHERE code='$lng_code' AND name='$name'");
}

#
# This function quotes arguments for shell command according
# to the host operation system
#
function func_shellquote() {
	static $win_s = '!([\t \&\<\>\?]+)!S';
	static $win_r = '"\\1"';
	$result = "";
	$args = func_get_args();
	foreach ($args as $idx=>$arg)
		$args[$idx] = X_DEF_OS_WINDOWS ? (preg_replace($win_s,$win_r,$arg)) : (escapeshellarg($arg));

	return implode(' ', $args);
}

#
# This function checks the user passwords with default values
#
function func_check_default_passwords($uname=false) {
	global $sql_tbl, $active_modules;

	x_load('crypt');

	$default_accounts = array();
	$default_accounts["P"] = array("provider", "master", "root");

	if (empty($active_modules["Simple_Mode"]))
		$default_accounts["A"] = array("admin");

	$return = array();

	if (!empty($uname)) {
		#
		# Check password security for specified user name
		#
		$account = func_query_first("SELECT login, password FROM $sql_tbl[customers] WHERE login='$uname'");
		if (is_array($account) && $account["login"] == text_decrypt($account["password"]))
			$return[] = $account["login"];
	}
	else {
		#
		# Check password security for all default user names
		#
		foreach ($default_accounts as $usertype=>$accounts) {
			foreach ($accounts as $login_) {
				if (!empty($uname) && $uname != $login_)
					continue;

				$account = func_query_first("SELECT login, password FROM $sql_tbl[customers] WHERE login='$login_' AND usertype='$usertype'");
				if (empty($account) || !is_array($account))
					continue;

				if ($account["login"] == text_decrypt($account["password"]))
					$return[] = $account["login"];
			}
		}
	}

	return $return;
}

#
# Smarty->display wrapper
#
function func_display($tpl, &$templater, $to_display = true) {
	global $config;
	global $predefined_lng_variables, $override_lng_code, $shop_language, $user_agent, $__smarty_time, $__smarty_size;
	global $xcart_dir;
	global $__X_LNG;

	x_load('templater');

	__add_mark_smarty();
	if (!empty($config['Security']['compiled_tpl_check_md5']) && $config['Security']['compiled_tpl_check_md5'] == 'Y') {
		$templater->compile_check_md5 = true;
	}
	else {
		$templater->compile_check_md5 = false;
	}

	if (!empty($predefined_lng_variables)) {
		$lng_code = $override_lng_code;
		if (empty($lng_code)) {
			$lng_code = $shop_language;
		}

		if (!empty($predefined_lng_variables)) {
			$predefined_lng_variables = array_flip($predefined_lng_variables);
			$predefined_vars = array();
			func_get_lang_vars_extra($lng_code, $predefined_lng_variables, $predefined_vars);
			if ($templater->webmaster_mode)
				$result = func_webmaster_convert_labels($predefined_vars);

			$templater->_tpl_vars['lng'] = func_array_merge($templater->_tpl_vars['lng'], $predefined_vars);

			if (!isset($__X_LNG[$shop_language])) {
				$__X_LNG[$shop_language] = $predefined_vars;
			} else {
				$__X_LNG[$shop_language] = func_array_merge($__X_LNG[$shop_language], $predefined_vars);
			}

			unset($predefined_vars);
		}
		unset($predefined_lng_variables);
	}

	$templater->register_postfilter("func_tpl_add_hash");

	if (isset($templater->webmaster_mode) && $templater->webmaster_mode) {
		$templater->force_compile = true;
		$templater->register_postfilter("func_webmaster_filter");
		$templater->register_outputfilter("func_tpl_webmaster");
	}

	$templater->register_postfilter("func_tpl_postfilter");
	$templater->register_outputfilter("func_convert_lang_var");

	if($to_display == true) {
		$templater->display($tpl);
		$ret = "";
	} else {
		$ret = $templater->fetch($tpl);
	}

	__add_mark_smarty($tpl);

	if ($to_display == true) {
		# Display page content
		func_flush();

		# Update tracking statistics
		if (AREA_TYPE == 'C')
			include_once $xcart_dir."/include/atracking.php";
	}

	return $ret;
}

#
# Function for fetching language variables values for one code
#
function func_get_lang_vars($code, &$variables, &$lng) {
	global $sql_tbl;

	$labels = db_query("SELECT name, value FROM $sql_tbl[languages] WHERE code = '$code' AND name IN ('".implode("','", array_keys($variables))."')");
	if ($labels) {
		while ($v = db_fetch_array($labels)) {
			$lng[$v['name']] = $v['value'];
			unset($variables[$v['name']]);
		}

		db_free_result($labels);
	}
}

#
# Extra version of func_get_lang_vars(): try to fetch values of language variables
# using all possible language codes
#
function func_get_lang_vars_extra($prefered_lng_code, &$variables, &$lng) {
	global $current_area, $config;

	if (empty($variables))
		return;

	func_get_lang_vars($prefered_lng_code, $variables, $lng);
	if (empty($variables))
		return;

	$default_language = ($current_area == 'C' ? $config['default_customer_language'] : $config['default_admin_language']);
	if ($default_language != $prefered_lng_code) {
		func_get_lang_vars($default_language, $variables, $lng);
		if (empty($variables))
			return;
	}

	if ($default_language != 'US')
		func_get_lang_vars('US', $variables, $lng);
}

#
# Check CC processor's transaction type
#
function func_check_cc_trans ($module_name, $type, $hash = array()) {
	global $sql_tbl;

	$return = false;
	if (empty($hash) && is_array($hash))
		$hash = array("P" => "P", "C" => "C", "R" => "R");

	if (empty($type))
		$type = 'P';

	if ($type == 'P') {
		$return = $hash[$type];
	}
	elseif ($type == 'C') {
		if (func_query_first_cell("SELECT is_check FROM $sql_tbl[ccprocessors] WHERE module_name = '$module_name'"))
			$return = $hash[$type];
	}
	elseif ($type == 'R') {
		if (func_query_first_cell("SELECT is_refund FROM $sql_tbl[ccprocessors] WHERE module_name = '$module_name'"))
			$return = $hash[$type];
	}

	if (empty($return) && $return !== false)
		$return = false;

	return $return;
}

#
# Parse string to hash array like:
# x=1|y=2|z=3
# where:
#	str 	= x=1|y=2|z=3
#	delim 	= |
# convert to:
# array('x' => 1, 'y' => 2, 'z' => 3)
#
function func_parse_str($str, $delim = '&', $pair_delim = '=', $value_filter=false) {
	if (empty($str))
		return array();

	$arr = explode($delim, $str);
	$return = array();
	for ($x = 0; $x < count($arr); $x++) {
		$pos = strpos($arr[$x], $pair_delim);
		if ($pos === false) {
			$return[$arr[$x]] = false;
		}
		elseif ($pos >= 0) {
			$v = substr($arr[$x], $pos+1);
			if (!empty($value_filter))
				$v = $value_filter($v);

			$return[substr($arr[$x], 0, $pos)] = $v;
		}
	}

	return $return;
}

#
# Remove parameters from QUERY_STRING by name
#
function func_qs_remove($qs) {
    if (func_num_args() <= 1)
        return $qs;

    $args = func_get_args(); 
	array_shift($args);

	foreach ($args as $param_name) {
		if (empty($param_name))
			continue;

		$pn = preg_quote($param_name, "!");
		$qs = preg_replace("!(&?)(".$pn."(\[[^&]*\])?)=\w*!S", "", $qs);
		$qs = preg_replace("!^&!S", "", $qs);
	}
	$qs = preg_replace("/(\w+)\?$/S", "\\1", $qs);

	return $qs;
}

#
# Get default field's name
#
function func_get_default_field($name) {
	$prefix = substr($name, 0, 2);
	if ($prefix == "s_" || $prefix == "b_") {
		$name = substr($name, 2);
	}

	$name = str_replace(
		array("firstname","lastname","zipcode"),
		array("first_name","last_name","zip_code"),
		$name);

	return func_get_langvar_by_name("lbl_".$name, false, false, true);
}

#
# Get memberships list
#
function func_get_memberships($area = 'C', $as_hash = false) {
	global $sql_tbl, $shop_language;

	$query_string = "SELECT $sql_tbl[memberships].membershipid, IFNULL($sql_tbl[memberships_lng].membership, $sql_tbl[memberships].membership) as membership FROM $sql_tbl[memberships] LEFT JOIN $sql_tbl[memberships_lng] ON $sql_tbl[memberships].membershipid = $sql_tbl[memberships_lng].membershipid AND $sql_tbl[memberships_lng].code = '$shop_language' WHERE $sql_tbl[memberships].active = 'Y' AND $sql_tbl[memberships].area = '$area' ORDER BY $sql_tbl[memberships].orderby";

	if ($as_hash) {
		return func_query_hash($query_string, "membershipid", false);
	} else {
		return func_query($query_string);
	}
}

#
# Detect membershipid by membership name
#
function func_detect_membership($membership = "", $type = false) {
	global $sql_tbl;

	if (empty($membership))
		return 0;

	$where = "";
	if ($type != false)
		$where = " AND area = '$type'";

	$membership = addslashes($membership);
	$id = func_query_first_cell("SELECT membershipid FROM $sql_tbl[memberships] WHERE membership = '$membership'".$where);

	return $id ? $id : 0;
}

#
# The function is merging arrays by keys
# Ex.:
# array(5 => "y") = func_array_merge_assoc(array(5 => "x"), array(5 => "y"));
#
function func_array_merge_assoc() {
	if (!func_num_args())
		return array();

	$args = func_get_args();

	$result = array();
	foreach ($args as $val) {
		if (!is_array($val) || empty($val))
			continue;

		foreach ($val as $k => $v)
			$result[$k] = $v;
	}

	return $result;
}

function func_membership_update($type, $id, $membershipids, $field = false) {
	global $sql_tbl;

	$tbl = $sql_tbl[$type."_memberships"];
	if (empty($tbl) || empty($id))
		return false;

	if ($field === false)
		$field = $type."id";

	db_query("DELETE FROM $tbl WHERE $field = '$id'");

	if (!empty($membershipids)) {
		if (!in_array(-1, $membershipids)) {
			foreach ($membershipids as $v) {
				db_query("INSERT INTO $tbl VALUES ('$id','$v')");
			}
		}
	}

	return true;
}

function func_get_titles() {
	global $sql_tbl;

	$titles = func_query("SELECT * FROM $sql_tbl[titles] WHERE active = 'Y' ORDER BY orderby, title");
	if (!empty($titles)) {
		foreach ($titles as $k => $v) {
			$name = func_get_languages_alt("title_".$v['titleid']);
			$titles[$k]['title_orig'] = $v['title'];
			if (!empty($name)) {
				$titles[$k]['title'] = $name;
			}
		}
	}

	return $titles;
}

function func_detect_title($title) {
	global $sql_tbl;

	if (empty($title))
		return false;

	return func_query_first_cell("SELECT titleid FROM $sql_tbl[titles] WHERE title = '$title'");
}

function func_get_title($titleid, $code = false) {
	global $sql_tbl, $shop_language;

	if (empty($titleid))
		return false;

	$title = func_get_languages_alt("title_".$titleid, $code);
	if (empty($title)) {
		$title = func_query_first_cell("SELECT title FROM $sql_tbl[titles] WHERE titleid = '$titleid'");
	}

	return $title;
}

function func_detect_price($price, $cur_symbol = '$', $cur_symbol_left = true) {

	if (!is_numeric($price)) {
		$price = trim($price);
		$cur_symbol = preg_quote($cur_symbol, "/");
		if ($cur_symbol_left) {
			$price = preg_replace("/^".$cur_symbol."/S","", $price);
		} else {
			$price = preg_replace("/".$cur_symbol."$/S","", $price);
		}
		$price = func_convert_number($price);
	}

	return doubleval($price);
}

#
# Convert local number format to inner number format
#
function func_convert_number($var, $from = NULL) {
	global $config;

	if (strlen(@$var) == 0)
		return $var;

	if (empty($from))
		$from = $config['Appearance']['number_format'];

	if (empty($from))
		$from = "2.,";

	return round(func_convert_numeric($var, $from), intval(substr($from, 0, 1)));
}

#
# Convert local number format (without precision) to inner number format
#
function func_convert_numeric($var, $from = NULL) {
	global $config;

	if (strlen(@$var) == 0)
		return $var;

	$var = trim($var);
	if (preg_match("/^\d+$/S", $var))
		return doubleval($var);

	if (empty($from))
		$from = $config['Appearance']['number_format'];

	if (empty($from))
		$from = "2.,";

	return doubleval(str_replace(" ", "", str_replace(substr($from, 1, 1), ".", str_replace(substr($from, 2, 1), "", $var))));
}

#
# Format price according to 'Input and display format for floating comma numbers' option
#
function func_format_number($price, $thousand_delim = NULL, $decimal_delim = NULL, $precision = NULL) {
	global $config;

	if (strlen(@$price) == 0)
		return $price;

	$format = $config['Appearance']['number_format'];

	if (empty($format)) $format = "2.,";

	if (is_null($thousand_delim) || $thousand_delim === false)
		$thousand_delim = substr($format,2,1);

	if (is_null($decimal_delim) || $decimal_delim === false)
		$decimal_delim = substr($format,1,1);

	if (is_null($precision) || $precision === false)
		$precision = intval(substr($format,0,1));

	return number_format(round((double)$price+0.00000000001,2), $precision, $decimal_delim, $thousand_delim);
}

#
# Convert string to use in custom javascript code
#
function func_js_escape($string) {
	return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
}

#
# Generate product flags (stored in statis service array - xcart_quick_flags table)
# work for all/selected products
#
function func_build_quick_flags($id = false, $tick = 0) {
	global $sql_tbl, $active_modules;

	$where = "";
	if ($id !== false && !is_array($id)) {
		$where = " WHERE $sql_tbl[products].productid = '$id'";
		db_query("DELETE FROM $sql_tbl[quick_flags] WHERE productid = '$id'");

	} elseif (is_array($id) && !empty($id)) {
		$where = " WHERE $sql_tbl[products].productid IN ('".implode("','", $id)."')";
		db_query("DELETE FROM $sql_tbl[quick_flags] WHERE productid IN ('".implode("','", $id)."')");

	} else {
		db_query("DELETE FROM $sql_tbl[quick_flags]");
	}

	if ($tick > 0)
		func_display_service_header("lbl_rebuild_quick_flags");

	$image_fields = "$sql_tbl[images_T].image_path AS image_path_T";

	if (empty($active_modules['Product_Options'])) {
		$sd = db_query("SELECT $sql_tbl[products].productid, '' AS is_variants, '' AS is_product_options, IF($sql_tbl[product_taxes].productid IS NULL, '', 'Y') AS is_taxes, $image_fields  FROM $sql_tbl[products] LEFT JOIN $sql_tbl[product_taxes] ON $sql_tbl[product_taxes].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[images_T].id = $sql_tbl[products].productid $where GROUP BY $sql_tbl[products].productid");

	} else {
		$sd = db_query("SELECT $sql_tbl[products].productid, IF($sql_tbl[variants].variantid IS NULL, '', IF(MAX($sql_tbl[variants].avail) = 0, 'E', 'Y')) AS is_variants, IF($sql_tbl[classes].productid IS NULL, '', 'Y') AS is_product_options, IF($sql_tbl[product_taxes].productid IS NULL, '', 'Y') AS is_taxes, $image_fields FROM $sql_tbl[products] LEFT JOIN $sql_tbl[variants] ON $sql_tbl[variants].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[classes] ON $sql_tbl[classes].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[product_taxes] ON $sql_tbl[product_taxes].productid = $sql_tbl[products].productid LEFT JOIN $sql_tbl[images_T] ON $sql_tbl[images_T].id = $sql_tbl[products].productid $where GROUP BY $sql_tbl[products].productid");
	}

	$updated = 0;

	if ($sd) {
		while ($row = db_fetch_array($sd)) {

			func_array2insert("quick_flags", func_addslashes($row));

			$updated++;
			if ($tick > 0 && $updated % $tick == 0) {
				echo ". ";
				if (($updated/$tick) % 100 == 0)
					echo "\n";
				func_flush();
			}
		}

		db_free_result($sd);
	}

	return $updated;
}

#
# Generate matrix: MIN(product price) x membershipid (stored in statis service array - xcart_quick_prices table)
# (with variantid)
# work for all/selected products
#
function func_build_quick_prices($id = false, $tick = 0) {
	global $sql_tbl, $config, $active_modules;

	# Define product condition
	$where = "";
	if ($id !== false && !is_array($id)) {
		$where = " AND $sql_tbl[products].productid = '$id'";
		db_query("DELETE FROM $sql_tbl[quick_prices] WHERE productid = '$id'");
	}
	elseif (is_array($id) && !empty($id)) {
		$where = " AND $sql_tbl[products].productid IN ('".implode("','", $id)."')";
		db_query("DELETE FROM $sql_tbl[quick_prices] WHERE productid IN ('".implode("','", $id)."')");
	}
	else {
		db_query("DELETE FROM $sql_tbl[quick_prices]");
	}

	if ($tick > 0)
		func_display_service_header("lbl_rebuild_quick_prices");

	# Get common data
	if (empty($active_modules['Product_Options'])) {
		$res = db_query("SELECT $sql_tbl[products].productid, MIN(CONCAT($sql_tbl[pricing].price,'/',$sql_tbl[pricing].membershipid, '/', $sql_tbl[pricing].priceid)) as priceid, $sql_tbl[pricing].membershipid FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[pricing].productid = $sql_tbl[products].productid AND $sql_tbl[pricing].variantid = 0 AND $sql_tbl[pricing].quantity = 1 $where GROUP BY $sql_tbl[products].productid, $sql_tbl[pricing].membershipid");

	} else {
		$res = db_query("SELECT $sql_tbl[products].productid, MIN(CONCAT($sql_tbl[pricing].price,'/',$sql_tbl[pricing].membershipid, '/', $sql_tbl[pricing].priceid)) as priceid, $sql_tbl[pricing].membershipid FROM $sql_tbl[pricing], $sql_tbl[products] LEFT JOIN $sql_tbl[variants] ON $sql_tbl[products].productid = $sql_tbl[variants].productid WHERE $sql_tbl[pricing].productid = $sql_tbl[products].productid AND $sql_tbl[pricing].variantid = 0 AND $sql_tbl[pricing].quantity = 1 AND $sql_tbl[variants].productid IS NULL $where GROUP BY $sql_tbl[products].productid, $sql_tbl[pricing].membershipid");
	}
	if ($res) {
		$i = 0;
		while ($arr = db_fetch_array($res)) {
			$i++;
			list($tmp1, $arr['membershipid'], $arr['priceid']) = explode("/", $arr['priceid'], 3);
			func_array2insert("quick_prices", func_addslashes($arr));
			if ($tick > 0 && $i % $tick == 0) {
				echo ". ";
				if (($i/$tick) % 100 == 0)
					echo "\n";
				func_flush();
			}
		}

		db_free_result($res);
	}

	if (empty($active_modules['Product_Options']))
		return $i;

	# Get variants' prices
	$res = db_query("SELECT $sql_tbl[products].productid FROM $sql_tbl[products], $sql_tbl[variants] WHERE $sql_tbl[variants].productid = $sql_tbl[products].productid $where GROUP BY $sql_tbl[products].productid");
	if (!$res)
		return $i;

	while ($arr = db_fetch_array($res)) {
		$productid = $arr['productid'];
		$variantid = func_get_default_variantid($productid);
		if (empty($variantid))
			continue;

		$prices = func_query_hash("SELECT membershipid, priceid FROM $sql_tbl[pricing] WHERE variantid = '$variantid' AND quantity = 1 ORDER BY price", "membershipid", false, true);
		if (empty($prices))
			continue;

		foreach ($prices as $mid => $priceid) {
			$i++;
			$query_data = array(
				"productid" => $productid,
				"priceid" => $priceid,
				"membershipid" => $mid,
				"variantid" => $variantid
			);
			func_array2insert("quick_prices", $query_data, true);

			if ($tick > 0 && $i % $tick == 0) {
				func_flush(". ");
			}
		}
	}

	db_free_result($res);

	return $i;
}

#
# Get data cache content and regenerate cache file on demand
#
function func_data_cache_get($name, $params = array(), $force_rebuild = false) {
	global $data_caches, $var_dirs, $xcart_dir, $data_caches_no_save;

	if (!isset($data_caches[$name]) || empty($data_caches[$name]['func']) || !function_exists($data_caches[$name]['func']))
		return false;

	$path = $var_dirs["cache"]."/".$name;
	if (!empty($params)) {
		$path .= ".".implode(".", $params);
	}

	$path .= ".php";
	$no_save = defined("BLOCK_DATA_CACHE_".strtoupper($name));

	if (file_exists($path) && !$force_rebuild && defined("USE_DATA_CACHE") && constant("USE_DATA_CACHE") && !$no_save) {
		if (!@include($path))
			return false;

		return $$name;

	} else {
		$data = call_user_func_array($data_caches[$name]['func'], $params);
		if (defined("USE_DATA_CACHE") && constant("USE_DATA_CACHE") && is_writable($var_dirs["cache"]) && is_dir($var_dirs["cache"]) && !$no_save) {
			@unlink($path);
			$fp = @fopen($path, "w");
			$is_unlink = false;
			if ($fp) {
				if (@fwrite($fp, "<?php\nif (!defined('XCART_START')) { header('Location: ../../'); die('Access denied'); }\n") === false)
					$is_unlink = true;
				if (!$is_unlink && !func_data_cache_write($fp, '$'.$name, $data))
					$is_unlink = true;
				if (!$is_unlink && @fwrite($fp, "?>") === false)
					$is_unlink = true;

				fclose($fp);
			}

			if ($is_unlink)
				@unlink($path);
		}

		return $data;
	}

	return "";
}

#
# Write array to data cache file
#
function func_data_cache_write($fp, $prefix, $data) {
	if (!is_array($data)) {
		fwrite($fp, $prefix.'=');
		if (is_bool($data)) {
			if (@fwrite($fp, ($data ? "true" : "false").";\n") === false)
				return false;
		}
		elseif (is_int($data) || is_float($data)) {
			if (@fwrite($fp, $data.";\n") === false)
				return false;
		}
		else {
			if (@fwrite($fp, '"'.str_replace('"','\"',$data)."\";\n") === false)
				return false;
		}
	} else {
		foreach ($data as $key => $value) {
			if (!func_data_cache_write($fp, $prefix."['".str_replace("'", "\'", $key)."']", $value))
				return false;
		}
	}

	return true;
}

#
# Clear data cache
#
function func_data_cache_clear($name = false) {
	global $data_caches, $var_dirs, $xcart_dir;

    if ($name !== false && (!isset($data_caches[$name]) || empty($data_caches[$name]['func']) || !function_exists($data_caches[$name]['func'])))
        return false;

    $path = $var_dirs["cache"];

	$dir = opendir($path);
	if (!$dir)
		return false;

	while ($file = readdir($dir)) {
		if ($file != '.' && $file != '..' && (($name === false && preg_match("/\.php$/S", $file)) || ($name !== false && strpos($file, $name.".") === 0))) {
			@unlink($path.DIRECTORY_SEPARATOR.$file);
		}
	}

	closedir($dir);

	return true;
}

#
# Erase service array (Group editing of products functionality)
#
function func_ge_erase($geid = false) {
	global $sql_tbl, $XCARTSESSID;

	if (!empty($geid)) {
		db_query("DELETE FROM $sql_tbl[ge_products] WHERE geid = '$geid'");
	} else {
		db_query("DELETE FROM $sql_tbl[ge_products] WHERE sessid = '$XCARTSESSID'");
	}
}

#
# Store temporary data in database for some reason
#
function func_db_tmpwrite($data, $ttl=600) {
	$id = md5(microtime());

	$hash = array (
		'id' => addslashes($id),
		'data' => addslashes(serialize($data)),
		'expire' => time() + $ttl
	);

	func_array2insert('temporary_data', $hash, true);
	return $id;
}

#
# Read previously stored temporary data
#
function func_db_tmpread($id, $destroy=false) {
	global $sql_tbl;

	$tmp = func_query_first_cell("SELECT data FROM $sql_tbl[temporary_data] WHERE id='".addslashes($id)."' LIMIT 1");
	if ($tmp === false)
		return false;

	if ($destroy) {
		db_query("DELETE FROM $sql_tbl[temporary_data] WHERE id='".addslashes($id)."'");
	}

	return unserialize($tmp);
}

#
# Display service page header
#
function func_display_service_header($title = "", $as_text = false) {
	global $smarty;

	if (!defined("BENCH_BLOCK"))
		define("BENCH_BLOCK", true);

	if (!defined("SERVICE_HEADER")) {
		define("SERVICE_HEADER", true);
		set_time_limit(86400);

		func_display("main/service_header.tpl", $smarty);
		func_flush();

		if (!defined("NO_RSFUNCTION"))
			register_shutdown_function("func_display_service_footer");
	}

	if (!empty($title)) {
		if (!$as_text) {
			$title = func_get_langvar_by_name($title, null, false, true);
			if (empty($title))
				return;
		}
		func_flush($title.": ");
	}
}

#
# Display service page footer
#
function func_display_service_footer() {
	global $smarty;

	if (defined("SERVICE_HEADER")) {
		func_display("main/service_footer.tpl", $smarty);
		func_flush();
	}
}

#
# Close current window through JS-code
#
function func_close_window() {
?>
<script type="text/javascript">
<!--
window.close();
-->
</script>
<?php
	exit;
}

#
# This function check user name for belonging to anonymous customers
#
function func_is_anonymous($username) {
	global $anonymous_username_prefix;

	return !strncmp($username, $anonymous_username_prefix, strlen($anonymous_username_prefix));
}

#
# Get value from array with presence check and default value
#
function get_value($array, $index, $default=false) {
	if (isset($array[$index]))
		return $array[$index];

	return $default;
}

#
# Get default image URL
#
function func_get_default_image($type) {
	global $config, $xcart_dir, $xcart_web_dir;

	if (!isset($config['available_images'][$type]) || empty($config['setup_images'][$type]['default_image']))
		return false;

	$default_image = $config['setup_images'][$type]['default_image'];
	if (is_url($default_image)) {
		return $default_image;
	}

	$default_image = func_realpath($default_image);
	if (!strncmp($xcart_dir, $default_image, strlen($xcart_dir)) && @file_exists($default_image)) {
		$default_image = str_replace($xcart_dir, $xcart_web_dir, $default_image);
		if (X_DEF_OS_WINDOWS)
			$default_image = str_replace("\\", "/", $default_image);

		return $default_image;
	}

	return '';
}

#
# Convert EOL symbols to BR tags
# if content hasn't any tags
#
function func_eol2br($content) {
	return ($content == strip_tags($content)) ? str_replace("\n", "<br />", $content) : $content;
}

?>
