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
# $Id: mod_FEDEX.php,v 1.43 2006/02/10 14:08:08 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('cart');

/* todo: do something with Package Type (it seems that it doesn't affects the rates)

  envelope <= 0.5 lbs
  FedEx Packaging <= 20 lbs

*/

function func_shipper_FEDEX($weight, $userinfo, $debug, $cart) {
	global $config, $sql_tbl;
	global $products;
	global $active_modules;
	global $allowed_shipping_methods;

	$FEDEX_FOUND = false;
	if (is_array($allowed_shipping_methods)) {
		foreach ($allowed_shipping_methods as $key=>$value) {
			if ($value["code"] == "FDX") {
				$FEDEX_FOUND = true;
				break;
			}
		}
	}

	if (!$FEDEX_FOUND)
		return;

	$residential = 1.40;			# Residential delivery charge

	# Get the declared value of package
	if ($debug=="Y") {
		$decl_value = 0;
	}
	else {
		$is_admin = defined('AREA_TYPE') && (AREA_TYPE=='A' || AREA_TYPE=='P' && !empty($active_modules['Simple_Mode']));

		if ($is_admin && !empty($active_modules["Advanced_Order_Management"]) && x_session_is_registered("cart_tmp")) {
			global $cart_tmp;

			if (!isset($cart_tmp) && is_array($cart_tmp))
				$cart = $cart_tmp;
		}

		$cart2 = func_calculate($cart, $products, $userinfo["login"], $userinfo["usertype"]);
		$decl_value = $cart2["subtotal"];
	}

	# query FedEx parameters.
	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='FDX'");

	$fedex_weight=ceil(func_weight_in_grams($weight)/453.6);
	if ($fedex_weight<1) $fedex_weight=1;


	$supportHome=array('US'=>1,'CA'=>1);
	$supportGrnd=array('US'=>1,'CA'=>1,'PR'=>1);

	$ozip = $config["Company"]["location_zipcode"];
	$dzip = $userinfo["s_zipcode"];
	$s2res = ($supportHome[$userinfo["s_country"]] ? $params["param02"] : "false");
	$ctyp = ($supportGrnd[$userinfo["s_country"]] ? $params["param00"] : "Express");
	$isex = ($ctyp == "Express" ? true : false);
	$isgr = ((($ctyp == "Ground" || $ctyp == "Both") && $s2res != "true") ? true : false);

	$zp3=strtoupper(substr(ereg_replace('[ "\',_\-]',"",$dzip),0,3));

	# calculate Insurance
	$insurance = 0;
	if ($decl_value > 500)
		$insurance = ceil($decl_value/100)*0.50;
	elseif ($decl_value > 100)
		$insurance = 2.5;

	if (($zp3=="006" || $zp3=="007" || $zp3=="009") && ($userinfo["s_country"]=="PR" || $userinfo["s_state"]=="PR")) {
		# Puerto Rico
		$addon = $insurance;
		$methods = array(
			array("name" => "International Economy", "addon" => $addon),
			array("name" => "International Priority", "addon" => $addon)
		);
		func_shipper_FEDEX_AddMethods($params, $methods, $fedex_weight, "PR", $addon);
	}
	elseif ($userinfo["s_country"]=="US" && is_numeric($zp3)) {
		# Get US relative shipping zone
		$zone = func_shipper_FEDEX_get_zone($ozip, $dzip);

		# validate $zone
		if ($zone < 9)
			$zone = $zone;
		elseif ($zone > 12)
			$zone = "13-16";
		elseif ($zone > 10)
			$zone = "11-12";
		else
			$zone = "9-10";

		# select aplicable methods
		$methods = array();

		if ($ctyp == "Express" || $ctyp == "Both") {
			$rural = 1.5; # todo: define rural !!! FedEx eto vrode ne usaet, no dolzhen

			# within the PM or RM delivery areas
			$DeliveryAreaSurcharge = 1.75;


			$addon = $DeliveryAreaSurcharge + $insurance;
			# Residential Surcharge - used only with "ship to residence" option
			if ($s2res=="true")
				$addon = $addon + $residential;

			$methods[] = array("name" => "2nd Day", "addon" => $addon);
			$methods[] = array("name" => "2Day", "addon" => $addon);
			$methods[] = array("name" => "Express Saver", "addon" => $addon);
			$methods[] = array("name" => "Priority Overnight", "addon" => $addon);
			$methods[] = array("name" => "Standard Overnight", "addon" => $addon);
			$methods[] = array("name" => "First Overnight", "addon" => $addon);
		}

		if ($ctyp == "Ground" || $ctyp == "Both") {
			$decl_surcharge = ($decl_value<=100)?(0):(0.35);
			if ($isgr)
				$methods[] = array("name" => "Ground", "addon" => $decl_surcharge);

			if ($s2res=="true") {
				$hm_residential = 1.15;
				$hm_rural = 1.75; # maybe rural sometimes doesn't needed?
				$methods[] = array("name" => "Home Delivery", "addon" => $hm_residential + $hm_rural + $decl_surcharge);
			}
		}

		func_shipper_FEDEX_AddMethods($params, $methods, $fedex_weight, $zone);
		func_shipper_FEDEX_AddMethodsSpec($params, $methods, $fedex_weight, $ozip, $dzip);
	}
	else {
		if ($decl_value > 100)
			$insurance = floor($decl_value/100)*0.40;

		$addon = $insurance;

		# Get Canadian Zone if customer from Canada
		if ($userinfo["s_country"]=='CA') {
			$zone = func_shipper_FEDEX_get_zone($ozip, $dzip);
		}
		else {
			# Get International shipping zone for Country
			$res = func_query_first("SELECT fedex_zone FROM $sql_tbl[countries] WHERE code='".$userinfo["s_country"]."'");
			$zone = $res['fedex_zone'];
		}

		# Sometimes used: Extended Service Area Surcharge - Greater of $20 or $0.20 per lb.
		# "ship to residence" - doesn't affects rates
		$methods = array (
			array("name" => "International Economy", "addon" => $addon),
			array("name" => "International Priority", "addon" => $addon)
		);

		func_shipper_FEDEX_AddMethods($params, $methods, $fedex_weight, $zone, $addon);

		if ($userinfo["s_country"]=='CA') {
			$methods = array (
				array("name" => "Ground", "addon" => $addon)
			);
			$zone = func_shipper_FEDEX_get_zone($ozip, $dzip, 43);
			func_shipper_FEDEX_AddMethods($params, $methods, $fedex_weight, $zone, $addon);
		}
	}
}

/****************************************************************************
* function - Adds shipping methods into the global $intershipper_rates array
*
* params:
*
*   @methods  - array of methods
*   @weight   - weight of shipping goods
*   @zone     - zone of shipping
*   @addon    - addon to the result price
*
* returns: nothing
*
****************************************************************************/
function func_shipper_FEDEX_AddMethods($params, $methods, $weight, $zone) {
	global $intershipper_rates, $sql_tbl;

	foreach($methods as $method) {
		$rate = func_shipper_FEDEX_get_rate($weight, $method['name'], $zone);

		if (!($rate===false)) {
			# calculate fuel surcharge
			if (($method['name']=='Ground') || ($method['name']=='Home Delivery'))
				$fuel_surcharge = $params["param04"]; # Fuel surcharge (Ground & Home)
			else
				$fuel_surcharge = $params["param03"]; # Fuel surcharge (Express)

			$fuel_addon = round(($rate*$fuel_surcharge)*100)/100;

			$row = func_query_first("SELECT shippingid, subcode FROM $sql_tbl[shipping] WHERE shipping='FedEx $method[name]' AND active='Y'");
			if ($row) {
				#calculate effective rate
				$rate = $rate + $fuel_addon + $method['addon'];
				$intershipper_rates[] = array("methodid" => $row['subcode'], "rate" => $rate);
			}
		}
	}
}

#
# Add rates for non-continental US
#
function func_shipper_FEDEX_AddMethodsSpec($params, $methods, $weight, $ozip, $dzip) {
	global $sql_tbl;

	$special = array ('Ground', 'Express Saver', 'Home Delivery', 'Priority Overnight', '2nd Day', 'Standard Overnight', 'First Overnight');

	foreach ($methods as $m) {
		if (in_array($m["name"], $special)) {

			$id = func_query_first_cell("SELECT shippingid FROM $sql_tbl[shipping] WHERE shipping='FedEx ".addslashes($m["name"])."' AND active='Y'");
			if ($id === false) continue;

			$zone = func_shipper_FEDEX_get_zone($ozip, $dzip, $id, 5);
			if ($zone === false) continue;

			func_shipper_FEDEX_AddMethods($params, array($m), $weight, $zone);
		}
	}
}

/********************************************************
* function - calculates rates
*
* params:
*
*   @weight   - the weight of shipping goods
*   @method   - shipping method
*   @zone     - zone of shipping
*
* returns: the rate of shipping
*
********************************************************/
function func_shipper_FEDEX_get_rate($weight, $method, $zone) {
	global $sql_tbl;

	$res = func_query_first("SELECT * FROM $sql_tbl[shipping] WHERE shipping = 'FedEx $method'");
	$method_id = @$res["shippingid"];

	$row = func_query_first("SELECT * FROM $sql_tbl[fedex_rates] WHERE r_meth_id = '".$method_id."' AND r_zone = '".$zone."' AND r_weight = '".$weight."'");
	if ($row) {
		# exact weight match
		return $row["r_rate"];
	}
	else {
		# houndreds
		$rows = func_query("SELECT * FROM $sql_tbl[fedex_rates] WHERE r_meth_id = '".$method_id."' AND r_zone = '".$zone."' AND r_ishundreds = '1'");
		if (is_array($rows)) {
			foreach ($rows as $row) {
				if (strpos($row['r_weight'], '+')===false) {
					# from [0] to [1]
					$w_range = explode('-', $row['r_weight']);
					if (($weight>=$w_range[0]) && ($weight<=$w_range[1]))
						return $row['r_rate']*$weight;
				}
				else {
					# only from [0]
					$w_range = explode('+', $row['r_weight']);
					if ($weight>=$w_range[0])
						return $row['r_rate']*$weight;
				}
			}
		}
	}

	return false;
}

/****************************************************************************
* function - searches for shipping zone according to zip codes
*
* params:
*
*   @orig_zip  - array of methods
*   @dest_zip  - weight of shipping goods
*
* returns: string with zone
*
****************************************************************************/
function func_shipper_FEDEX_get_zone($orig_zip, $dest_zip, $methid='', $limit=3) {
	global $sql_tbl;

	$zp3 = substr($dest_zip,0,$limit);
	$row = func_query_first("SELECT *, (zip_last-zip_first) as R FROM $sql_tbl[fedex_zips] WHERE (zip_first<='$zp3') AND (IF(zip_last='', '$zp3'=zip_last, '$zp3'<=zip_last)) AND zip_meth='$methid' ORDER BY R ASC LIMIT 1");
	if ($row) {
		$zone = $row['zip_zone'];
		if (is_numeric($zone))
			$zone = (int)$zone;

		return $zone;
	}

	return false;
}

?>
