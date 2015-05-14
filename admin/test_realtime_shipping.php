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
# $Id: test_realtime_shipping.php,v 1.20 2006/01/11 06:55:58 mclap Exp $
#

require "./auth.php";
require $xcart_dir."/include/security.php";
include $xcart_dir."/shipping/shipping.php";

x_load('http');

$show_arb_account_field = func_use_arb_account();
$smarty->assign("show_arb_account_field", $show_arb_account_field);

if (!isset($weight)) {
	$weight=1;
} else {
	$weight = (float)$weight;
	if ($weight<=0)
		$weight = 1;
}

if (!empty($active_modules["UPS_OnLine_Tools"]) and $config["Shipping"]["realtime_shipping"] == "Y" and $config["Shipping"]["use_intershipper"] != "Y") {
	if (isset($selected_carrier))
		$current_carrier = $selected_carrier;
	else
		$current_carrier = "UPS";
	$smarty->assign("current_carrier", $current_carrier);
}

if ($config["Shipping"]["use_intershipper"] == "Y")
	include $xcart_dir."/shipping/intershipper.php";
else
	include $xcart_dir."/shipping/myshipper.php";

require $xcart_dir."/include/countries.php";
require $xcart_dir."/include/states.php";

$userinfo = array();
if (isset($s_country)) $userinfo["s_country"] = $s_country;
if (isset($s_state)) $userinfo["s_state"] = $s_state;
if (isset($s_zipcode)) $userinfo["s_zipcode"] = $s_zipcode;
if (isset($s_city)) $userinfo["s_city"] = $s_city;

if (empty($userinfo)) {
	$userinfo["s_country"] = $config["General"]["default_country"];
	$userinfo["s_state"] = $config["General"]["default_state"];
	$userinfo["s_zipcode"] = $config["General"]["default_zipcode"];
	$userinfo["s_city"] = $config["General"]["default_city"];
}

if (!empty($origin)) {
	$config['Company']['location_city'] = $origin['city'];
	$config['Company']['location_state'] = $origin['state'];
	$config['Company']['location_country'] = $origin['country'];
	$config['Company']['location_zipcode'] = $origin['zipcode'];
	$config["Company"]["location_country_name"] = func_get_country($config["Company"]["location_country"]);
	$config["Company"]["location_state_name"] = func_get_state($config["Company"]["location_state"], $config["Company"]["location_country"]);
	$smarty->assign("config", $config);
}


x_session_register("airborne_account");
if ($show_arb_account_field) {
	$airborne_account = @$s_arb_account;
}
else {
	$airborne_account = "";
}

$smarty->assign("userinfo", $userinfo);
$smarty->assign("airborne_account", $airborne_account);

func_https_ctl('IGNORE');

ob_start();
$intershipper_rates = func_shipper($weight,$userinfo,"Y");
$content = ob_get_contents();
ob_end_clean();

func_https_ctl('STORE');

$content = "<font>$content</font><br /><br />";

if (!empty($intershipper_error)) {
	$content .= "Service: $shipping_calc_service<br />Error: ".$intershipper_error;
}

$smarty->assign("content", $content);
$smarty->assign("weight", $weight);
func_display("admin/main/test_shippings.tpl",$smarty);

?>
