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
# $Id: shipping.php,v 1.41.2.3 2006/07/06 07:35:33 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart');

function func_weight_in_grams($weight) {
	global $config;

	return $weight*$config["General"]["weight_symbol_grams"];
}

#
# This function creates the shipping methods/rates list
#
function func_get_shipping_methods_list($cart, $products, $userinfo) {
	global $sql_tbl, $config, $active_modules, $single_mode, $smarty;
	global $xcart_dir;
	global $intershipper_recalc, $intershipper_rates;
	global $intershipper_error;
	global $shipping_calc_service;
	global $current_carrier;
	global $login;
	global $arb_account_used;

	if (empty($products))
		return;

	if (empty($login) && $config["General"]["apply_default_country"] != "Y" && $config["Shipping"]["enable_all_shippings"] == "Y") {
		$enable_all_shippings = true;
		$smarty->assign("force_delivery_dropdown_box", "Y");
	}

	#
	# If $enable_shipping then calculate shipping rates
	#
	$enable_shipping = ((!empty($userinfo) && !empty($login)) || $config["General"]["apply_default_country"] == "Y");

	#
	# Get the total products weight
	#
	$total_weight_shipping = func_weight_shipping_products($products);

	$cart_subtotal = $cart["subtotal"];

	if ($config["Shipping"]["realtime_shipping"] == "Y" && $enable_shipping && $intershipper_recalc != "N") {
		x_load('http');

		#
		# Get the real time shipping rates
		#
		if ($config["Shipping"]["use_intershipper"] == "Y") {
			include_once $xcart_dir."/shipping/intershipper.php";
		}
		else {
			include_once $xcart_dir."/shipping/myshipper.php";
		}

		func_https_ctl('IGNORE');

		$intershipper_rates = func_shipper($total_weight_shipping, $userinfo ,'N', $cart);

		func_https_ctl('STORE');

		if (!empty($intershipper_error)){
			$smarty->assign("shipping_calc_service",$shipping_calc_service?$shipping_calc_service:"Intershipper");
			$smarty->assign("shipping_calc_error",$intershipper_error);

			$msg  = "Service: ".($shipping_calc_service?$shipping_calc_service:"Intershipper")."\n";
			$msg .= "Error: ".$intershipper_error."\n";
			$msg .= "Login: ".$login."\n";
			$msg .= "Shipping address: ".$userinfo['s_address']." ".$userinfo['s_address_2']."\n";
			$msg .= "Shipping city: ".$userinfo['s_city']."\n";
			$msg .= "Shipping state: ".$userinfo['s_statename']."\n";
			$msg .= "Shipping country: ".$userinfo['s_countryname']."\n";
			$msg .= "Shipping zipcode: ".$userinfo['s_zipcode'];
			x_log_add('SHIPPING', $msg);
		}

		$intershipper_recalc = "N";
	}

	#
	# The preparing to search the allowable shipping methods
	#
	$weight_condition = " AND weight_min<='$total_weight_shipping' AND (weight_limit='0' OR weight_limit>='$total_weight_shipping')";

	if (!empty($active_modules["UPS_OnLine_Tools"]) && $config["Shipping"]["use_intershipper"] != "Y") {

		$condition = "";

		if ($enable_all_shippings) {
			global $ups_services;
			include "modules/UPS_OnLine_Tools/ups_shipping_methods.php";
		}

		$ups_condition = $condition;

		if ($config["Shipping"]["realtime_shipping"] == "Y" && $current_carrier == "UPS") {
			$ups_condition .= " AND $sql_tbl[shipping].code='UPS' AND $sql_tbl[shipping].service_code!=''";
		}

		$weight_condition .= $ups_condition;
	}

	if (!empty($active_modules["UPS_OnLine_Tools"]) && $config["Shipping"]["realtime_shipping"] == "Y" && $config["Shipping"]["use_intershipper"] != "Y") {
		$_carriers["UPS"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code='UPS' AND service_code!='' AND weight_min<='$total_weight_shipping' AND (weight_limit='0' OR weight_limit>='$total_weight_shipping') AND active='Y'");
		$_carriers["other"] = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code<>'UPS' AND weight_min<='$total_weight_shipping' AND (weight_limit='0' OR weight_limit>='$total_weight_shipping') AND active='Y'");
		if ($_carriers["UPS"] == 0 || $_carriers["other"] == 0) {
			$current_carrier = ($_carriers["UPS"] == 0 ? "" : "UPS");
			x_session_save("current_carrier");
		}
		else {
			$smarty->assign("show_carriers_selector", "Y");
		}
	}

	if ($enable_shipping || $config["Shipping"]["enable_all_shippings"] != "Y") {
		$destination_condition = " AND destination=".(!empty($userinfo) && $userinfo["s_country"] == $config["Company"]["location_country"] ? "'L'" : "'I'");
	}

	if (!$enable_shipping || $config["Shipping"]["realtime_shipping"] != "Y") {
		#
		# Get ALL shipping methods according to the conditions (W/O real time)
		#
		$shipping = func_query("SELECT * FROM $sql_tbl[shipping] WHERE active='Y' $destination_condition $weight_condition ORDER BY orderby");
	}
	else {
		#
		# Gathering the defined shipping methods
		#
		$shipping = func_query ("SELECT * FROM $sql_tbl[shipping] WHERE code='' AND active='Y' $destination_condition $weight_condition ORDER BY orderby");

		if ($intershipper_rates) {
			#
			# Gathering the shipping methods from $intershipper_rates
			#
			foreach ($intershipper_rates as $intershipper_rate) {
				$ship_time = "";
				if (!empty($intershipper_rate["shipping_time"])) {
					if (is_numeric($intershipper_rate["shipping_time"]))
						$ship_time = $intershipper_rate["shipping_time"]." ".func_get_langvar_by_name("lbl_day_s");
					else
						$ship_time = $intershipper_rate["shipping_time"];
				}

				if ($ship_time != "")
					$ship_time_column = "'".$ship_time."' AS shipping_time";
				else
					$ship_time_column = "shipping_time";

				$result = func_query_first("SELECT *, '$intershipper_rate[rate]' AS rate, '$intershipper_rate[warning]' AS warning, $ship_time_column FROM $sql_tbl[shipping] WHERE subcode='$intershipper_rate[methodid]' AND active='Y' $weight_condition ORDER BY orderby");
				if ($result) {
					$shipping[] = $result;
				}
			}
		}

		if (is_array($shipping))
			usort($shipping, "usort_array_cmp_orderby");
	}

	if (!empty($shipping)) {
		#
		# Final preparing the shipping methods list
		#
		$tmp_shipping = array();
		$providers_count = count(func_get_products_providers($products));
		foreach ($shipping as $k=>$v) {
			if (($config["Shipping"]["realtime_shipping"]=="Y" && $v["code"]=="") || $config["Shipping"]["realtime_shipping"]!="Y") {
				#
				# Check accessibility only for defined shipping methods
				#

				if (!func_is_shipping_method_allowable($v["shippingid"], $userinfo, $products, $total_weight_shipping, $cart_subtotal))
					continue;
			}

			$tmp_shipping[] = $v;
		}

		$shipping = $tmp_shipping;
		unset($tmp_shipping);

		if (is_array($shipping)) {
			$tmp_cart = $cart;
			foreach ($shipping as $k=>$v) {
				#
				# Fetch shipping rate if it wasn't defined
				#
				$tmp_cart["shippingid"] = $v["shippingid"];
				$calc_result = func_calculate($tmp_cart, $products, $userinfo["login"], $userinfo["usertype"]);
				$shipping[$k]["rate"] = $calc_result["display_shipping_cost"];
			}

			unset($tmp_cart);
		}

	}

	if ($arb_account_used && is_array($shipping)) {
		foreach ($shipping as $v) {
			if ($v["code"] == "ARB" && $v["shippingid"] == $cart["shippingid"]) {
				$smarty->assign("arb_account_used", true);
				break;
			}
		}
	}

	if ($shipping)
		return $shipping;
	else
		return;
}

#
# This function checks if shipping method have defined shipping rates
#
function func_is_shipping_method_allowable($shippingid, $customer_info, $products, $weight=0, $subtotal=0) {
	global $sql_tbl, $config, $single_mode;
	global $login;

	if (empty($login)) {
		if ($config["Shipping"]["enable_all_shippings"] == "Y")
			return true;

		if ($config["General"]["apply_default_country"] != "Y")
			return false;
	}

	foreach ($products as $product) {
		if (!$single_mode) {
			#
			# Get the provider info (only for PRO, single_mode=false)
			#
			$provider = func_query_first_cell("SELECT provider FROM $sql_tbl[products] WHERE productid='$product[productid]'");
			$provider_condition = "AND provider='$provider'";
		}
		else {
			$provider = $provider_condition = "";
		}

		#
		# Get the customer's shipping zone
		#
		$customer_zone = func_get_customer_zone_ship($customer_info, $provider, "D");

		#
		# Find existing shipping rates for $customer_zone
		#
		$shipping = func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping_rates] WHERE shippingid='$shippingid' AND minweight<='$weight' AND maxweight>='$weight' AND mintotal<='$subtotal' AND maxtotal>='$subtotal' $provider_condition AND zoneid='$customer_zone' AND type='D'");

		if ($shipping)
			return true;
	}

	return false;
}

function func_use_arb_account($params = false) {
	global $sql_tbl;

	if (!is_array($params))
		$params = func_query_first("SELECT param07 FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");

	if (isset($params["param07"])) {
		$tmp = explode(',',$params["param07"]);
		return (isset($tmp[1]) && $tmp[1] == "Y");
	}

	return false;
}

#
# Add new realtime shipping method
#
function func_add_new_smethod($method, $code, $added = array()) {
	global $sql_tbl;

	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code = '".addslashes($code)."'") == 0)
		return false;

	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE shipping = '".addslashes($method)."' AND code = '".addslashes($code)."'") > 0)
		return false;

	if (isset($added['service_code'])) {
		if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[shipping] WHERE code = '".addslashes($code)."' AND service_code = '".addslashes($added['service_code'])."'") > 0)
			return false;
	}

	$max_subcode = func_query_first_cell("SELECT MAX(subcode+0) FROM $sql_tbl[shipping]")+1;
	$data = array(
		"shipping"	=> addslashes($method),
		"subcode"	=> $max_subcode,
		"active"	=> "N",
		"is_new"	=> "Y",
		"code"		=> $code);

	if (!empty($added) && is_array($added))
		$data = func_array_merge($data, $added);

	$id = func_array2insert("shipping", $data);
	if (empty($id))
		return false;

	return $id;
}

function func_weight_shipping_products ($products) {
	global $active_modules, $config;

	$total_weight = 0;

	foreach ($products as $product) {
		if (@$product["deleted"]) continue; # for Advanced_Order_Management module

		if ($product["free_shipping"] == "Y" || ($active_modules["Egoods"] && $product["distribution"] != "") || ($config["Shipping"]["replace_shipping_with_freight"] == "Y" && $product["shipping_freight"] > 0)) {
			continue;
		}

		$total_weight += $product["weight"] * $product["amount"];
	}

	return $total_weight;
}

#
# Sort shipping list by the 'orderby' field
#
function usort_array_cmp_orderby($a, $b) {
	return $a["orderby"] - $b["orderby"];
}

function func_shipper_show_rates($rates_list) {
	global $config, $sql_tbl;

	echo "<h1>Shipping Rates</h1>";

	if (empty($rates_list)) {
		echo "No rates";
		return;
	}

	$l_search = array("##SM##","##R##");
	$l_replace = array("<sup>SM</sup>","&#174;");

	foreach ($rates_list as $rate) {
		$method = func_query_first("SELECT shipping FROM $sql_tbl[shipping] WHERE subcode='$rate[methodid]'");
		if (!empty($rate["currency"]))
			$_currency_symbol = $rate["currency"];
		else
			$_currency_symbol = $config["General"]["currency_symbol"];

		echo "<p>".str_replace($l_search,$l_replace,$method["shipping"])." ($_currency_symbol ".price_format($rate["rate"]).")";
	}
}

?>
