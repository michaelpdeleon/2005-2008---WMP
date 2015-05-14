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
# $Id: mod_CPC.php,v 1.21.2.1 2006/06/26 07:14:40 max Exp $
#
# Canada Post
# (only from Canada)
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('xml','http');

function func_shipper_CPC($weight, $userinfo, $debug, $cart) {
	global $config, $sql_tbl;
	global $allowed_shipping_methods;
	global $shipping_calc_service, $intershipper_error, $intershipper_rates;

	if ($config["Company"]["location_country"] != "CA" || empty($config["Shipping"]["CPC_merchant_id"]))
		return;

	$cpc_methods = array();
	foreach ($allowed_shipping_methods as $v) {
		if ($v["code"]=="CPC"){
			$cpc_methods[] = $v;
		}
	}

	if (empty($cpc_methods)) return;

	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='CPC'");

	$cp_merchant=$config["Shipping"]["CPC_merchant_id"];
	$cp_language="en";
	$cp_qnty="1";
	$cp_packed = true;

	$cp_weight=round(func_weight_in_grams($weight)/1000,3);
	if ($cp_weight<0.1) $cp_weight=0.1;

	$cp_description = $params["param00"];
	$cp_length = $params["param01"];
	$cp_width = $params["param02"];
	$cp_height = $params["param03"];

	$cp_currency_rate = $params["param05"];
	$cp_insured_value = $params["param04"];

	$cp_dest_country = $userinfo["s_country"];
	$cp_dest_city = $userinfo["s_city"];
	$cp_dest_zip = $userinfo["s_zipcode"];
	$cp_dest_state = empty($userinfo["s_state"]) ? "NA" : $userinfo["s_state"];

	$cp_orig_zip=$config["Company"]["location_zipcode"];

	if ($config["Shipping"]["CPC_test_mode"] == "Y")
		$cp_host = "206.191.4.228";
	else
		$cp_host = "216.191.36.73";

	if (isset($cart['discounted_subtotal']))
		$itemsPrice = "<itemsPrice>$cart[discounted_subtotal]</itemsPrice>";
	elseif (!empty($cp_insured_value))
		$itemsPrice = "<itemsPrice>$cp_insured_value</itemsPrice>";
	else
		$itemsPrice = "";

	$cp_request =
		"<?xml version=\"1.0\" ?>\n".
		"<eparcel>".
		"<language>$cp_language</language>\n".
		"<ratesAndServicesRequest>\n".
		"  <merchantCPCID>$cp_merchant</merchantCPCID>\n".
		"  <fromPostalCode>$cp_orig_zip</fromPostalCode>\n".
		"  $itemsPrice\n".
		//"  <turnAroundTime> 24 </turnAroundTime>\n".
		"  <lineItems>\n".
		"    <item>\n".
		"      <quantity>$cp_qnty</quantity>\n".
		"      <weight>$cp_weight</weight>\n".
		"      <length>$cp_length</length>\n".
		"      <width>$cp_width</width>\n".
		"      <height>$cp_height</height>\n".
		"      <description>$cp_description</description>\n".
		($cp_packed?"      <readyToShip/>\n":"").
		"    </item>\n".
		"  </lineItems>\n".
		"  <city>$cp_dest_city</city>\n".
		"  <provOrState>$cp_dest_state</provOrState>\n".
		"  <country>$cp_dest_country</country>\n".
		"  <postalCode>$cp_dest_zip</postalCode>\n".
		"</ratesAndServicesRequest>\n".
		"</eparcel>";

	list($a,$result) = func_http_post_request($cp_host.":30000","/",$cp_request);

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = func_xml_parse($result, $parse_errors, $options);

	$products =& func_array_path($parsed, 'EPARCEL/RATESANDSERVICESRESPONSE/PRODUCT');

	if (is_array($products)) {
		foreach ($products as $product) {
			$pid = $product['@']['ID'];
			$rate = func_array_path($product,'RATE/0/#');
			if ($pid === false || $rate === false) continue;

			$is_found = false;
			foreach ($cpc_methods as $v) {
				if ($v["service_code"] == $pid){
					$intershipper_rates[] = array("methodid"=>$v["subcode"], "rate"=>$rate*$cp_currency_rate);
					$is_found = true;
					break;
				}
			}

			if (!empty($pid) && !$is_found) {
				$tmp_name = func_array_path($product,"NAME/0/#");
				func_add_new_smethod($tmp_name, "CPC", array("service_code" => $pid));
			}
		}
	}

	$error_code = func_array_path($parsed, 'EPARCEL/ERROR/STATUSCODE/0/#');
	if ($error_code !== false) {
		$error_msg  = func_array_path($parsed, 'EPARCEL/ERROR/STATUSMESSAGE/0/#');
		$shipping_calc_service = "Canada Post";
		$intershipper_error = $error_msg;
	}

	if ($debug=="Y") {
		print "<h1>CPC Debug Information</h1>";
		if ($cp_request) {
			$query = preg_replace("|<merchantCPCID>.*</merchantCPCID>|i","<merchantCPCID>xxx</merchantCPCID>",$cp_request);

			print "<h2>CPC Request</h2>";
			print "<pre>".htmlspecialchars($query)."</pre>";
			print "<h2>CPC Response</h2>";
			print "<pre>".htmlspecialchars($result)."</pre>";
		}
		else {
			print "It seems, you have forgotten to fill in an CPC account information, or destination information (City, State, Country or ZipCode). Please check it, and try again.";
		}

		if ($intershipper_error) {
			print "<h2>CPC Error Information</h2>";
			print $intershipper_error;
		}
	}
}

?>
