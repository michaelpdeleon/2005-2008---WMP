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
# $Id: myshipper.php,v 1.38 2006/01/11 06:56:25 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

#
# This function calculates shipping rates from my own shipper module
#



function func_shipper ($weight, $userinfo, $debug="N", $cart=false) {
	global $allowed_shipping_methods,$intershipper_rates;
	global $shipping_calc_service, $intershipper_error;
	global $sql_tbl;
	global $config;
	global $active_modules;
	global $xcart_dir;
	global $current_carrier;

	if (empty($userinfo) && ($config["General"]["apply_default_country"]=="Y" || $debug=="Y")) {
		$userinfo["s_country"] = $config["General"]["default_country"];
		$userinfo["s_state"] = $config["General"]["default_state"];
		$userinfo["s_zipcode"] = $config["General"]["default_zipcode"];
		$userinfo["s_city"] = $config["General"]["default_city"];
	}
	elseif (empty($userinfo)) {
		return array();
	}

	$allowed_shipping_methods = func_query ("SELECT * FROM $sql_tbl[shipping] WHERE active='Y'");

	$intershipper_rates = array ();

	if (!empty($active_modules["UPS_OnLine_Tools"]) && $current_carrier == "UPS")
		$ups_rates_only = true;
	else
		$ups_rates_only = false;

	$ship_mods = array ();

	if (!$ups_rates_only) {
		$ship_mods[] = "FEDEX";
		$ship_mods[] = "AP";
	}

	x_load('tests');

	#
	# Shipping modules depend on XML parser (EXPAT extension)
	#
	if (test_expat() != "") {
		if ($ups_rates_only) {
			$ship_mods[] = "UPS";
		}
		else {
			$ship_mods[] = "USPS";
			$ship_mods[] = "CPC";
			$ship_mods[] = "ARB";
			$ship_mods[] = "DHL";
		}
	}

	foreach ($ship_mods as $ship_mod) {
		include_once $xcart_dir."/shipping/mod_".$ship_mod.".php";
		$func_ship = "func_shipper_".$ship_mod;
		$func_ship($weight, $userinfo, $debug, $cart);
	}

	if ($debug=="Y") {
		func_shipper_show_rates($intershipper_rates);
	}

	return $intershipper_rates;
}

?>
