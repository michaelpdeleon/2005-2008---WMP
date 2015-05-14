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
# $Id: auth.php,v 1.30.2.2 2006/07/19 10:19:28 max Exp $
#

define('AREA_TYPE', 'C');

@include_once "./top.inc.php";
@include_once "../top.inc.php";
@include_once "../../top.inc.php";
if (!defined('DIR_CUSTOMER')) die("ERROR: Can not initiate application! Please check configuration.");

include_once $xcart_dir."/init.php";

$current_area="C";

x_load('files');

x_session_register("logout_user");
x_session_register("session_failed_transaction");
x_session_register("add_to_cart_time");

x_session_register("always_allow_shop");
if (!empty($HTTP_GET_VARS["shopkey"])) $always_allow_shop = (!empty($config["General"]["shop_closed_key"]) && $HTTP_GET_VARS["shopkey"] == $config["General"]["shop_closed_key"]);

if ($config["General"]["shop_closed"] == "Y" && !$always_allow_shop){
	#
	# Close store front
	# Thanks to rubyaryat for the Shop Closed mod
	#
	if (!func_readfile($xcart_dir.DIRECTORY_SEPARATOR.$shop_closed_file, true))
		echo func_get_langvar_by_name("txt_shop_temporarily_unaccessible",false,false,true);
	exit();
}

require $xcart_dir."/include/nocookie_warning.php";

if (!defined('HTTPS_CHECK_SKIP')) {
	@include $xcart_dir.DIR_CUSTOMER."/https.php";
}

if (!empty($active_modules['Users_online'])) {
	x_session_register("current_url_page");
	x_session_register("current_date");
	x_session_register("session_create_date");
	$current_url_page = $php_url['url'].($php_url['query_string']?"?".$php_url['query_string']:"");
	if (empty($session_create_date))
		$session_create_date = time();

	$current_date = time();
}

#
# Display
#
x_session_register("wlid");
if (isset($HTTP_GET_VARS["wlid"]) and $HTTP_GET_VARS["wlid"])
	$wlid = $HTTP_GET_VARS["wlid"];

$smarty->assign("wlid", $wlid);

#
# Browser have disabled/enabled javasript switching
#
x_session_register("js_enabled", "Y");

if (!isset($js_enabled)) $js_enabled="Y";

if (isset($HTTP_GET_VARS["js"])) {
	if ($HTTP_GET_VARS["js"]=="y") {
		$js_enabled = "Y";
		$config['Adaptives']['isJS'] = "Y";
		$adaptives['isJS'] = "Y";
	}
	elseif ($HTTP_GET_VARS["js"]=="n") {
		$js_enabled = "";
	}
}

if ($js_enabled == "Y") {
	$qry_string = ereg_replace("(&*)js=y", "", $QUERY_STRING);
	$js_update_link = $PHP_SELF."?".($qry_string?"$qry_string&":"")."js=n";
}
else {
	$qry_string = ereg_replace("(&*)js=n", "", $QUERY_STRING);
	$js_update_link = $PHP_SELF."?".($qry_string?"$qry_string&":"")."js=y";
}

$smarty->assign("js_update_link", $js_update_link);
$smarty->assign("js_enabled", $js_enabled);

x_session_register("top_message");
if (!empty($top_message)) {
	$smarty->assign("top_message", $top_message);
	if ($config['Adaptives']['is_first_start'] != 'Y')
		$top_message = "";

	x_session_save("top_message");
}

$cat = intval(@$cat);
$page = intval(@$page);

if (!empty($active_modules['XAffiliate'])) {
	include $xcart_dir."/include/partner_info.php";
	include $xcart_dir."/include/adv_info.php";
}

include $xcart_dir.DIR_CUSTOMER."/referer.php";

############################################################
# X-CART-SEO Mod :: http://code.google.com/p/x-cart-seo/
# Added by Michael de Leon 12.08.06
############################################################
if (!empty($active_modules["XC_SEO"])) {
	include $xcart_dir."/modules/XC_SEO/loader.seo.php";
}
# END SEO

include $xcart_dir."/include/check_useraccount.php";

include $xcart_dir."/include/get_language.php";

$lbl_site_name = func_get_langvar_by_name("lbl_site_title", "", false, true);
$location = array();
$location[] = array((!empty($lbl_site_name) ? $lbl_site_name : $config["Company"]["company_name"]), "home.php");

include $xcart_dir.DIR_CUSTOMER."/minicart.php";

if (!empty($active_modules["Interneka"])) {
	include $xcart_dir."/modules/Interneka/interneka.php";
}

if (!empty($active_modules["Subscriptions"])) {
    if ($login) {
        include $xcart_dir."/modules/Subscriptions/get_subscription_info.php";
        $smarty->assign("user_subscription", is_user_subscribed($login));
    }
}

$pages_menu = func_query("SELECT * FROM $sql_tbl[pages] WHERE language='$store_language' AND active='Y' AND level='E' ORDER BY orderby, title");
$smarty->assign("pages_menu", $pages_menu);

$speed_bar = unserialize($config["speed_bar"]);
if (!empty($speed_bar)) {
	$tmp_labels = array();
	foreach ($speed_bar as $k => $v) {
		$speed_bar[$k] = func_array_map("stripslashes", $v);
		$tmp_labels[] = "speed_bar_".$v['id'];
	}

	$tmp = func_get_languages_alt($tmp_labels);
	foreach ($speed_bar as $k => $v) {
		if (isset($tmp['speed_bar_'.$v['id']]))
			$speed_bar[$k]['title'] = $tmp['speed_bar_'.$v['id']];

		$speed_bar[$k]['link'] = str_replace("&", "&amp;", $v['link']);
	}

	$smarty->assign("speed_bar", $speed_bar);
}

unset($speed_bar);

$smarty->assign("redirect","customer");

if (!empty($active_modules["News_Management"]))
	include $xcart_dir."/modules/News_Management/news_last.php";

if (!empty($active_modules["Feature_Comparison"]) && $config['Feature_Comparison']['fcomparison_show_product_list'] == 'Y') {
	$comparison_list = func_get_comparison_list();
	$smarty->assign("comparison_list",$comparison_list);
}

if (!empty($active_modules["Survey"])) {
	include_once $xcart_dir."/modules/Survey/surveys_list.php";
}

$smarty->assign("printable", $printable);
$smarty->assign("logout_user", $logout_user);

?>
