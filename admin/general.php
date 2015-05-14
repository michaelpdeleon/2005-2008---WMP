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
# $Id: general.php,v 1.75 2006/04/03 07:07:47 svowl Exp $
#

#
# This script provide admin the general information about online store
# and allows him to ckear redundant data from the database
# (this need to be performed before shop go to live)
#

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files','http','tests');

$anchors = array(
	"License" => "lbl_license_info",
	"General" => "lbl_general_info",
	"PaymentMethods" => "lbl_payments_methods_info",
	"Environment" => "lbl_environment_info");

foreach ($anchors as $anchor=>$anchor_label)
	$dialog_tools_data["left"][] = array("link" => "#".$anchor, "title" => func_get_langvar_by_name($anchor_label));

$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/tools.php", "title" => func_get_langvar_by_name("lbl_tools"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/snapshots.php", "title" => func_get_langvar_by_name("lbl_snapshots"));
$dialog_tools_data["right"][] = array("link" => $xcart_web_dir.DIR_ADMIN."/logs.php", "title" => func_get_langvar_by_name("lbl_shop_logs"));

if (@$HTTP_GET_VARS["mode"] == "phpinfo") {
    phpinfo();
    exit;
}
if (@$HTTP_GET_VARS["mode"] == "perlinfo") {
    echo test_perl(true);
    exit;
}

require $xcart_dir."/include/install.php";

x_session_register("previous_login_date");

$https_modules = array(
	"ssleay" => "Net::SSLeay",
	"libcurl" => "libCURL",
	"curl" => "CURL executable",
	"openssl" => "OpenSSL executable",
	"httpscli" => "HTTPS-cli executable");

if (!empty($mode) && $mode == "test_https_module") {
	$url = isset($url) ? trim(stripslashes($url)) : '';
	if (!zerolen($url)) {
		list($headers, $data) = func_https_request("GET", $url);
		$smarty->assign("headers_data", $headers);
		$smarty->assign("response_data", $data);
	}

	$smarty->assign("url", $url);
	$smarty->assign("template_name", "admin/main/test_https_module.tpl");
	$active_bouncer = test_active_bouncer();
	$smarty->assign("popup_title", func_get_langvar_by_name("lbl_test_of_https_module", array("name" => $https_modules[$active_bouncer])));
	func_display("help/popup_info.tpl",$smarty);
	exit;
}

$location[] = array(func_get_langvar_by_name("lbl_summary"), "");

#
# Function to get OS type
#
function test_os() {
    list($os_type, $tmp) = split(" ", php_uname());

    return $os_type;
}

function test_dirs_rights() {
	global $xcart_dir, $var_dirs;

	$directories = array(
		$xcart_dir."/.pgp",
		$xcart_dir."/files",
		$xcart_dir."/catalog",
		$var_dirs["templates_c"],
		$var_dirs["log"],
		$var_dirs["tmp"],
		$var_dirs["upgrade"],
		$var_dirs["cache"]
	);

	sort($directories);
	$rc = "";
	$root_dir = func_normalize_path($xcart_dir);
	foreach ($directories as $dir) {
		$testdir = func_normalize_path($dir);
		if (func_pathcmp($root_dir.DIRECTORY_SEPARATOR, $test_dir, 1)) {
			$testdir = substr($testdir, strlen($root_dir)+1);
		}

		$rc[] = array (
			"directory" => $testdir,
			"exists" => is_dir($dir),
			"writable" => is_writable($dir));
	}

	return $rc;
}

function test_webserver() {
	global $HTTP_SERVER_VARS;
	if( isset($HTTP_SERVER_VARS["SERVER_SOFTWARE"]) ) return $HTTP_SERVER_VARS["SERVER_SOFTWARE"];
	return "";
}

#
# This function collects knowledge about X-Cart environment
#
function test_environment() {
	global $version, $xcart_dir;
	global $https_modules;

	$env = array();
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_software_version"),
		"data" => $version,
		"default" => "unknown"
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_software_directory"),
		"data" => $xcart_dir,
		"default" => "unknown"
	);
	$env[] = array(
		"item" => "PHP",
		"data" => phpversion(),
		"details" => "javascript: window.open('general.php?mode=phpinfo','phpinfo')",
		"default" => "unknown"
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_mysql_server"),
		"default" => mysql_get_server_info(),
	);

	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_mysql_client"),
		"data" => mysql_get_client_info()
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_web_server"),
		"data" => test_webserver(),
		"default" => "unknown"
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_os"),
		"data" => test_os(),
		"default" => "unknown"
	);
	$env[] = array(
		"item" => "Perl",
		"data" => test_perl(),
		"details" => "javascript: window.open('general.php?mode=perlinfo','perlinfo')"
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_xml_parser"),
		"data" => test_expat(),
		"warning" => true
	);

	# HTTPS modules
	$env[] = array(
		"row_txt" => func_get_langvar_by_name("lbl_https_modules")
	);
	$details_txt = func_get_langvar_by_name("lbl_active");

	$details = "javascript: window.open('general.php?mode=test_https_module','HTTPSTEST','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');";

	$active_bouncer = test_active_bouncer();

	foreach ($https_modules as $_bouncer => $_bouncer_name) {
		$_test_func = "test_$_bouncer";
		$_bouncer_details = array(
			"item" => $_bouncer_name,
			"data" => $_test_func(),
			"details_txt" => "",
			"details" => "");

		if ($_bouncer == $active_bouncer) {
			$_bouncer_details["details_txt"] = $details_txt;
			$_bouncer_details["details"] = $details;
		}

		$env[] = $_bouncer_details;
	}

	# Modules for the payment methods
	$env[] = array(
		"row_txt" => func_get_langvar_by_name("lbl_modules_for_payment_methods")
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_verisign"),
		"data" => test_payflow()
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_paybox"),
		"data" => func_is_executable($xcart_dir."/payment/bin/paybox.cgi")
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_saferpay"),
		"data" => test_saferpay()
	);
	$env[] = array(
		"item" => func_get_langvar_by_name("lbl_env_trustcommerce"),
		"data" => test_trustcommerce()
	);

	foreach($env as $idx => $item) {
	
		if ($item["item"] == "Perl" && empty($item["data"]))
			$env[$idx]["details"] = ""; # Do not show link if Perl not found
			
		if (!isset($item["default"]))
			$env[$idx]["default"] = func_get_langvar_by_name("lbl_not_found");
	}

	return $env;
}

#
# Get the orders info
#
$curtime = time() + $config["General"]["timezone_offset"];

$start_dates[] = $previous_login_date + $config["General"]["timezone_offset"];  # Since last login
$start_dates[] = mktime(0,0,0,date("m",$curtime),date("d",$curtime),date("Y",$curtime)); # Today
$start_week = $curtime - (date("w",$curtime))*24*3600; # Week starts since Sunday
$start_dates[] = mktime(0,0,0,date("m",$start_week),date("d",$start_week),date("Y",$start_week)); # Current week
$start_dates[] = mktime(0,0,0,date("m",$curtime),1,date("Y",$curtime)); # Current month

foreach($start_dates as $start_date) {

    $date_condition = "AND date>='$start_date' AND date<='$curtime'";

    $orders["P"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='P' $date_condition");
    $orders["F"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE (status='F' OR status='D') $date_condition");
    $orders["I"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='I' $date_condition");
    $orders["Q"][] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[orders] WHERE status='Q' $date_condition");

}

#
# Get the shipping methods info
#
$shipping_methods_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y'");
$shipping_mod_enabled = func_query("SELECT code, COUNT(*) as count FROM $sql_tbl[shipping] WHERE active='Y' GROUP BY code ORDER BY code");

if ($active_modules["UPS_OnLine_Tools"] and $config["Shipping"]["use_intershipper"] != "Y") {
	$condition = "";
	$ups_only = true;
	include $xcart_dir."/modules/UPS_OnLine_Tools/ups_shipping_methods.php";
	$ups_shipping_methods_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE active='Y' $condition");
	for ($i = 0; $i < count($shipping_mod_enabled); $i++) {
		if ($shipping_mod_enabled[$i]["code"] == "UPS") {
			$shipping_methods_count -= ($shipping_mod_enabled[$i]["count"] - $ups_shipping_methods_count);
			$shipping_mod_enabled[$i]["count"] = $ups_shipping_methods_count;
			break;
		}
	}
}

#
# Get the shipping rates info
#
$shipping_rates_count = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping_rates]");
$shipping_rates_enabled = func_query("SELECT $sql_tbl[shipping].code, COUNT(*) as count FROM $sql_tbl[shipping], $sql_tbl[shipping_rates] WHERE $sql_tbl[shipping].shippingid=$sql_tbl[shipping_rates].shippingid GROUP BY $sql_tbl[shipping].code ORDER BY $sql_tbl[shipping].code");

#
# Get the X-Cart version
#
$version = func_query_first_cell("SELECT value FROM $sql_tbl[config] WHERE name='version'");

#
# Get the products critical properties
#
$empty_prices = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[products], $sql_tbl[pricing] WHERE $sql_tbl[products].productid=$sql_tbl[pricing].productid AND $sql_tbl[pricing].quantity='1' AND $sql_tbl[pricing].price='0.00' AND $sql_tbl[pricing].variantid = 0");

#
# Testing payment methods and CC processors
#
$payment_methods=func_query("select pm.*,cc.module_name,cc.processor,cc.type from $sql_tbl[payment_methods] as pm left join $sql_tbl[ccprocessors] as cc on pm.paymentid=cc.paymentid order by pm.orderby");
$payment_methods = test_payment_methods($payment_methods);

if ($config["active_subscriptions_processor"]) {
	$active_sb_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor='$config[active_subscriptions_processor]'");
	$active_sb = test_ccprocessor($active_sb_params);
}
else
	$active_sb = array("status"=>1);

#
# Set up the smarty templates variables
#
$smarty->assign("single_mode", $single_mode);
$smarty->assign("orders", $orders);
$smarty->assign("shipping_methods_count", $shipping_methods_count);
$smarty->assign("shipping_mod_enabled", $shipping_mod_enabled);
$smarty->assign("shipping_rates_count", $shipping_rates_count);
$smarty->assign("shipping_rates_enabled", $shipping_rates_enabled);
$smarty->assign("empty_prices", $empty_prices);
$smarty->assign("test_dirs_rights", test_dirs_rights());
$smarty->assign("environment_info", test_environment());
$smarty->assign("payment_methods",$payment_methods);
$smarty->assign("active_sb", $active_sb);
$smarty->assign("active_sb_params", $active_sb_params);
$smarty->assign("auth_code", $installation_auth_code);

$smarty->assign("main","general_info");

# Assign the current location line
$smarty->assign("location", $location);

# Assign the section navigation data
$smarty->assign("dialog_tools_data", $dialog_tools_data);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
