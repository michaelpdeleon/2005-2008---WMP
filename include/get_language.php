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
# $Id: get_language.php,v 1.70.2.3 2006/08/07 07:18:11 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

define("GET_LANGUAGE", 1);

x_session_register("old_lng");

if (!empty($edit_lng) && !empty($HTTP_GET_VARS['old_lng'])) {
	$asl = $edit_lng;
	$HTTP_POST_VARS['asl'] = $asl;
	$old_lng = $HTTP_GET_VARS['old_lng'];
	$QUERY_STRING = func_qs_remove($QUERY_STRING,"edit_lng");
	$QUERY_STRING = func_qs_remove($QUERY_STRING,"old_lng");
	$HTTP_REFERER = preg_replace("/[&\?]edit_lng=\w{2}/S", "", $HTTP_REFERER);
	$HTTP_REFERER = preg_replace("/[&\?]old_lng=\w{2}/S", "", $HTTP_REFERER);
}

if (!empty($old_lng) && !defined("IS_MULTILANGUAGE")) {
	if($config['Appearance']['restore_language_interface'] == 'Y') {
		$asl = $old_lng;
		$HTTP_POST_VARS['asl'] = $asl;
		$HTTP_REFERER = $PHP_SELF."?".$QUERY_STRING;
	}
	$old_lng = "";
}

$e_langs = func_data_cache_get("charsets");

$d_langs = explode ("|", $config["disabled_languages"]);
if ($d_langs) {
	$d_langs = func_array_map("trim", $d_langs);
	foreach ($d_langs as $v) {
		if (isset($e_langs[$v])) {
			unset($e_langs[$v]);
		}
	}
}

if (!isset($e_langs[$config["default_customer_language"]]) && !empty($e_langs) && is_array($e_langs))
	$config["default_customer_language"] = key($e_langs);
if (!isset($e_langs[$config["default_admin_language"]]) && !empty($e_langs) && is_array($e_langs))
	$config["default_admin_language"] = key($e_langs);

# Define redirect URL
if ($is_https_redirect == 'Y') {

	# Redirect from HTTP
	$l_redirect = func_qs_remove($PHP_SELF."?".$QUERY_STRING, "is_https_redirect", "sl", $XCART_SESSION_NAME);

} elseif (empty($HTTP_REFERER) || strstr($HTTP_REFERER, "error=disabled_cookies")) {

	# First request or redirect from Disabled cookies error page
	$l_redirect = func_qs_remove($PHP_SELF."?".$QUERY_STRING, "sl", $XCART_SESSION_NAME);

} else {
	$l_redirect = func_qs_remove($HTTP_REFERER, "sl", $XCART_SESSION_NAME);
}

if ($smarty->webmaster_mode || $smarty->debugging)
	$predefined_lng_variables = array("lbl_xcart_debugging_console", "lbl_included_templates_config_files");
else
	$predefined_lng_variables = array();

if ($login) unset($store_language);

if (!empty($HTTP_GET_VARS["sl"]))
	$store_language = $HTTP_GET_VARS["sl"];

$shop_language = '';
if (empty($current_area) || @$current_area == "C" || @$current_area == "B") {
	if (empty($store_language) && !empty($login)) {
		$store_language = func_query_first_cell ("SELECT $sql_tbl[customers].language FROM $sql_tbl[customers], $sql_tbl[languages] WHERE $sql_tbl[customers].login='$login' AND $sql_tbl[customers].language = $sql_tbl[languages].code LIMIT 1");
	}

	if (!empty($store_language)) {
		if (!isset($e_langs[$store_language])) {
			$store_language = "";
		}
	}

	if (empty($store_language))
		$store_language = $config["default_customer_language"];

	if (!isset($e_langs[$store_language])) {
		if (!isset($e_langs[$config["default_customer_language"]]) && !empty($e_langs) && is_array($e_langs)) {
			$store_language = key($e_langs);
		} else {
			$store_language = $config["default_customer_language"];
		}
	}

	$shop_language = $store_language;
}
else {
	x_session_register("current_language");
	if (@$HTTP_POST_VARS["asl"] && $login) {
		$res = func_query_first ("SELECT charset FROM $sql_tbl[countries] WHERE code='".$HTTP_POST_VARS["asl"]."'");
		if ($res) {
			$current_language = $HTTP_POST_VARS["asl"];
		}
		func_header_location($l_redirect);
	}

	if (!isset($current_language) || empty($current_language))
		$current_language = $config["default_admin_language"];

	if (!isset($e_langs[$current_language])) {
		if (!isset($e_langs[$config["default_admin_language"]])) {
			$current_language = key($e_langs);
			reset($e_langs);
		} else {
			$current_language = $config["default_admin_language"];
		}
	}

	$smarty->assign ('current_language', $current_language);
	$shop_language = $current_language;
}
$smarty->assign ('default_charset', $e_langs[$shop_language]);

x_session_register("editor_mode");

if ($login)
	db_query ("UPDATE $sql_tbl[customers] SET language='$shop_language' WHERE login='$login'");

if (@$current_area == "C" || @$current_area == "B") {
	#
	# Set cookies
	#
	if ($store_language != @$HTTP_COOKIE_VARS["store_language"] && !defined('NOCOOKIE')) {
		setcookie ("store_language", "", time()-31536000);
		setcookie ("store_language", $store_language, time()+31536000); # for one year
		if ($xcart_http_host != $xcart_https_host) {
			#
			# Set cookies for HTTPS host
			#
			setcookie ("store_language", "", time()-31536000, "/", $xcart_https_host, 1);
			setcookie ("store_language", $store_language, time()+31536000, "/", $xcart_https_host, 1); # for one year
		}
	}
}

$all_languages = func_data_cache_get("languages", array($shop_language));

if (empty($all_languages)) {
	$def_language = ($current_area == 'C' ? $config["default_customer_language"] : $config["default_admin_language"]);
	$all_languages = func_data_cache_get("languages", array($def_language));
	if (empty($all_languages)) {
		$all_languages = func_data_cache_get("languages", array(key($e_langs)));
		reset($e_langs);
	}
}

$n_langs = array ();

if ($all_languages) {
	$avail_languages = $all_languages;
	foreach ($all_languages as $value) {
		if (!in_array($value["code"], $d_langs))
			$n_langs [] = $value;
	}
}

# Start deletion by Michael de Leon 12.08.06 for XC SEO
# if (
# 	($current_area == "C" || $current_area == "B") &&
# 	!empty($HTTP_GET_VARS["sl"]) &&
# 	!defined('IS_ROBOT') &&
# 	!preg_match('/\.html?($|\?)|\/$/s', $l_redirect)
# ) {
# 	func_header_location($l_redirect);
# }

if (($current_area == "C" || $current_area == "B") && !empty($HTTP_GET_VARS["sl"]) && !defined('IS_ROBOT')) {

	############################################################
	# X-CART-SEO Mod :: http://code.google.com/p/x-cart-seo/
	############################################################
	if (!empty($active_modules["XC_SEO"])) {
		func_header_location($l_redirect);
	}else{
		if (!preg_match('!(\.htm(l)?|/$)!',$_tmp["path"])) {
			func_header_location($l_redirect);
		}
	}
	# End SEO
}

$all_languages = $n_langs;

$smarty->assign ("all_languages", $all_languages);
$smarty->assign ("store_language", @$store_language);
$smarty->assign ("shop_language", @$shop_language);
$smarty->assign ("all_languages_cnt", sizeof($all_languages));

$config["Company"]["location_country_name"] = func_get_country($config["Company"]["location_country"]);
$config["Company"]["location_state_name"] = func_get_state($config["Company"]["location_state"], $config["Company"]["location_country"]);
$smarty->assign("config",$config);
$mail_smarty->assign("config",$config);

if (!empty($config['r2l_languages'][$shop_language]))
	$smarty->assign('reading_direction_tag', ' dir="RTL"');
else
	$smarty->assign('reading_direction_tag', '');

?>
