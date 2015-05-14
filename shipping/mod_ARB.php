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
# $Id: mod_ARB.php,v 1.14.2.1 2006/07/06 07:35:33 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

function func_shipper_ARB($weight, $userinfo, $debug, $cart) {
	global $config, $sql_tbl;
	global $arb_account_used, $airborne_account;
	global $intershipper_rates, $intershipper_error;
	global $allowed_shipping_methods;

	x_session_register("arb_account_used");
	x_session_register("airborne_account");

	$arb_account_used = false;

	$ARB_FOUND = false;
	if (is_array($allowed_shipping_methods)) {
		foreach ($allowed_shipping_methods as $key=>$value) {
			if ($value["code"] == "ARB") {
				$ARB_FOUND = true;
				break;
			}
		}
	}

	if (!$ARB_FOUND)
		return;

	x_load('http','xml');

	$ab_id = $config["Shipping"]["ARB_id"];
	$ab_password = $config["Shipping"]["ARB_password"];
	$ab_ship_accnum = $config["Shipping"]["ARB_account"];
	$ab_testmode = $config["Shipping"]["ARB_testmode"];

	#
	# Currently shipping only from US is supported
	#
	if (empty($ab_id) || empty($ab_password) || empty($ab_ship_accnum) || $userinfo["s_country"]!="US" || $config["Company"]["location_country"] != "US")
		return;

	if ($ab_testmode == 'Y')
		$ab_url = "https://ecommerce.airborne.com:443/ApiLandingTest.asp";
	else
		$ab_url = "https://ecommerce.airborne.com:443/ApiLanding.asp";

	$ab_ship_key = ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $config["Company"]["location_zipcode"], $ab_testmode);
	if (empty($ab_ship_key)) {
		if ($debug == "Y") ab_show_faults();

		ab_conv_faults();
		return;
	}

	$ship_weight = max(1,round(func_weight_in_grams($weight)/453.6,0));
	$ship_weight_oz = round(func_weight_in_grams($weight)/28.3,0);

	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='ARB'");

	$ab_packaging = $params["param00"];
	$ab_ship_length = $params["param02"];
	$ab_ship_width = $params["param03"];
	$ab_ship_height = $params["param04"];
	$ab_ship_prot_code = $params["param05"];
	$ab_ship_prot_value = $params["param06"];
	$ab_ship_codpmt = $params["param08"];
	$ab_ship_codval = (float)$params["param09"];
	# options
	list($ab_ship_haz,$ab_ship_own_account) = explode(',',$params["param07"]);

	$_ship_date = date("Y-m-d", time() + $params["param01"]*86400);

	global $mod_AB_ship_flags;
	$mod_AB_ship_flags = array (
		109 => array('code'=>'G', 'sub'=>''),		# Airborne Ground
		31  => array('code'=>'S', 'sub'=>''),		# Airborne Second Day Service
		33  => array('code'=>'N', 'sub'=>''),		# Airborne Next Afternoon
		32  => array('code'=>'E', 'sub'=>''),		# Airborne Express
		124 => array('code'=>'E', 'sub'=>'1030'),	# Airborne Express 10:30 AM
		125 => array('code'=>'E', 'sub'=>'SAT')		# Airborne Express Saturday
	);

	if (func_use_arb_account($params) && isset($airborne_account) && trim($airborne_account) != "") {
		$_party_code = "R";
		$_party_account = "<AccountNbr>".trim($airborne_account)."</AccountNbr>";
		$arb_account_used = true;
	}
	else {
		$_party_code = "S";
		$_party_account = "";
	}

	$shipments = ""; $cnt = 0;
	$ship_reqs = array();
	foreach ($allowed_shipping_methods as $method) {
		if ($method["code"] == "ARB" && ($ship_weight < $method["weight_limit"] || $method["weight_limit"] == 0.00) && isset($mod_AB_ship_flags[$method["subcode"]])) {
			$_ship_srv_key = $mod_AB_ship_flags[$method["subcode"]]["code"];
			$_ship_srv_sub = $mod_AB_ship_flags[$method["subcode"]]["sub"];

			if ($_ship_srv_key == "G" && $ab_packaging == 'L') {
				# Letter express is not allowed with Ground Shipments. (Code=4119)
				continue;
			}

			if ($_ship_srv_key == "G" && $_ship_srv_sub == "SAT") {
				# Saturday pickup service is not available for Ground shipments. (Code=4105).
				continue;
			}

			$_shipproc_instr = "";
			$_secial_express = "";
			if ($_ship_srv_key == 'E') {
				# Express Saturday & Express 10:30AM services are not compatible within "Hazardous Materials"
				if ($ab_ship_haz == "Y" && $_ship_srv_sub != "")
					continue;

				if ($_ship_srv_sub == "SAT") {
					$_shipproc_instr = "<ShipmentProcessingInstructions><Overrides><Override><Code>ES</Code></Override></Overrides></ShipmentProcessingInstructions>";
					$_secial_express = "<SpecialServices><SpecialService><Code>SAT</Code></SpecialService></SpecialServices>";
				}
				elseif ($_ship_srv_sub == "1030") {
					$_secial_express = "<SpecialServices><SpecialService><Code>1030</Code></SpecialService></SpecialServices>";
				}
			}

			$_additional_protection = '';
			if ($ab_ship_prot_code == 'AP') {
				$_additional_protection = "<AdditionalProtection><Code>$ab_ship_prot_code</Code><Value>$ab_ship_prot_value</Value></AdditionalProtection>";
			}

			$_secial_haz = "";
			if ($ab_ship_haz == "Y") {
				$_secial_haz = "<SpecialServices><SpecialService><Code>HAZ</Code></SpecialService></SpecialServices>";
			}

			$_cod_payment = "";
			if ($ab_ship_codval > 0 && $_party_code == "S") {
				# When using COD service freight charges must be billed to sender. (Code=4116)
				$_cod_payment = "<CODPayment><Code>$ab_ship_codpmt</Code><Value>$ab_ship_codval</Value></CODPayment>";
			}

			$_dimensions = '';
			if ($ab_packaging == 'P') {
				$_dimensions = "<Weight>$ship_weight</Weight><Dimensions><Width>$ab_ship_width</Width><Height>$ab_ship_height</Height><Length>$ab_ship_length</Length></Dimensions>";
			}
			else {
				if ($ship_weight_oz > 8) {
					# Shipment exceeds allowable weight for Letter. (Code=4118)
					# Letter Express packages must be in Letter Express envelopes and weigh 8 ounces or less.
					continue;
				}
			}

			$shipment =<<<EOT
	<Shipment action='RateEstimate' version='1.0'>
		<ShippingCredentials>
			<ShippingKey>$ab_ship_key</ShippingKey>
			<AccountNbr>$ab_ship_accnum</AccountNbr>
		</ShippingCredentials>
		<ShipmentDetail>
			<ShipDate>$_ship_date</ShipDate>
			<Service>
				<Code>$_ship_srv_key</Code>
			</Service>
			<ShipmentType>
				<Code>$ab_packaging</Code>
			</ShipmentType>
			$_secial_express
			$_secial_haz
			$_dimensions
			$_additional_protection
		</ShipmentDetail>
		<Billing>
			$_cod_payment
			<Party>
				<Code>$_party_code</Code>
			</Party>
			$_party_account
		</Billing>
		<Receiver>
			<Address>
				<City>$userinfo[s_city]</City>
				<State>$userinfo[s_state]</State>
				<Country>$userinfo[s_country]</Country>
				<PostalCode>$userinfo[s_zipcode]</PostalCode>
			</Address>
		</Receiver>
		$_shipproc_instr
	</Shipment>
EOT;
			$shipments .= $shipment;
			$cnt++;
			if ($cnt >= 5) {
				$cnt = 0;
				if ($shipments != "") $ship_reqs[] = $shipments;
				$shipments = "";
			}
		}

	}

	if ($shipments != "") $ship_reqs[] = $shipments;

	if (count($ship_reqs)>0) {
		$ab_request = "";

		$intershipper_error = "";
		foreach ($ship_reqs as $req)
			ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req);
	}
}

function ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req) {
	global $mod_AB_faults;

	$ab_request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
	<Requestor>
		<ID>$ab_id</ID>
		<Password>$ab_password</Password>
	</Requestor>
	$req
</eCommerce>
EOT;

	$post=explode("\n",$ab_request);
	list ($a, $ab_response) = func_https_request("POST", $ab_url, $post, "","","text/xml");

	ab_parse_response($ab_response);

	if ($debug == "Y") {
		print "<h1>DHL/Airborne Debug Information</h1>";
		$query=preg_replace("|<ID>.*</ID>|iUS","<ID>xxx</ID>",$ab_request);
		$query=preg_replace("|<Password>.*</Password>|iUS","<Password>xxx</Password>",$query);
		$query=preg_replace("|<ShippingKey>.*</ShippingKey>|iUS","<ShippingKey>xxx</ShippingKey>",$query);
		$query=preg_replace("|<AccountNbr>.*</AccountNbr>|iUS","<AccountNbr>xxx</AccountNbr>",$query);
		print "<h2>DHL/Airborne Request</h2>";
		print "<pre>".htmlspecialchars($query)."</pre>";
		print "<h2>DHL/Airborne Response</h2>";
		print "<pre>".htmlspecialchars($ab_response)."</pre>";
	}

	if (!empty($mod_AB_faults)) {
		if ($debug == "Y") ab_show_faults();

		ab_conv_faults();
	}
}

function ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $zipcode, $ab_testmode) {
	global $config, $sql_tbl;
	global $mod_AB_faults;
	global $mod_AB_shipkey;

	if (!empty($config["Shipping"]["ARB_shipping_key"]))
		return $config["Shipping"]["ARB_shipping_key"];

	#
	# Request new Shipping Key
	#

	$request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
	<Requestor>
		<ID>$ab_id</ID>
		<Password>$ab_password</Password>
	</Requestor>
	<Register action='ShippingKey' version='1.0'>
		<AccountNbr>$ab_ship_accnum</AccountNbr>
		<PostalCode>$zipcode</PostalCode>
	</Register>
</eCommerce>
EOT;

	$post=explode("\n",$request);
	list ($a, $result) = func_https_request("POST", $ab_url, $post, "","","text/xml");

	ab_parse_response($result);
	if (!empty($mod_AB_faults)) return "";

	$config["Shipping"]["ARB_shipping_key"] = $mod_AB_shipkey;

	db_query("UPDATE $sql_tbl[config] SET value='".addslashes($mod_AB_shipkey)."' WHERE name='ARB_shipping_key'");

	return $mod_AB_shipkey;
}

function ab_show_faults() {
	global $mod_AB_faults;

	echo "<h1>DHL/Airborne request faults</h1>";
	$code = array();
	foreach ($mod_AB_faults as $fault) {
		if (isset($code[$fault["CODE"]])) continue;

		echo $fault["DESC"]." (Code=".$fault["CODE"].") <br />";
		$code[$fault["CODE"]] = true;
	}
}

function ab_conv_faults() {
	global $mod_AB_faults;
	global $intershipper_error, $shipping_calc_service;
	static $code = array();

	$str = "";
	foreach ($mod_AB_faults as $fault) {
		if (isset($code[$fault["CODE"]])) continue;

		$str .= $fault["DESC"]." (Code=".$fault["CODE"]."). ";
		$code[$fault["CODE"]] = true;
	}

	if ($str != "") {
		$shipping_calc_service = "DHL/Airborne";
		$intershipper_error .= $str;
	}
}

#
# Functions to parse XML-response
#
function ab_parse_response($result) {
	global $allowed_shipping_methods;
	global $intershipper_rates;
	global $mod_AB_ship_flags;
	global $mod_AB_shipkey;

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = func_xml_parse($result, $parse_errors, $options);


	$r = func_array_path($parsed, 'ECOMMERCE/REGISTER/SHIPPINGKEY/0/#');
	if ($r !== false) {
		$mod_AB_shipkey = $r;
	}

	ab_add_faults($parsed, 'ECOMMERCE/FAULTS/FAULT');
	ab_add_faults($parsed, 'ECOMMERCE/REGISTER/FAULTS/FAULT');

	$shipments =& func_array_path($parsed, 'ECOMMERCE/SHIPMENT');

	if (is_array($shipments)) {
		foreach ($shipments as $shipment) {
			ab_add_faults($shipment, 'FAULTS/FAULT');

			$mod_AB_SRVCODE = func_array_path($shipment,'ESTIMATEDETAIL/SERVICE/CODE/0/#');
			$mod_AB_SRVSUBCODE = "";

			$desc = func_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#');
			if (!empty($desc) && $mod_AB_SRVCODE == 'E') {
				if (stristr($desc,"Saturday")!==false)
					$mod_AB_SRVSUBCODE = 'SAT';
				elseif (strstr($desc,"10:30")!==false)
					$mod_AB_SRVSUBCODE = '1030';
			}

			$rate = func_array_path($shipment, 'ESTIMATEDETAIL/RATEESTIMATE/TOTALCHARGEESTIMATE/0/#');
			if ($rate === false || (float)trim($rate) < 0.001)
				continue;

			$shipping_time = func_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC');

			foreach ($allowed_shipping_methods as $method) {
				if ($method['code'] != 'ARB' || empty($mod_AB_ship_flags[$method['subcode']]))
					continue;

				$method_flags = $mod_AB_ship_flags[$method['subcode']];

				if ($method_flags['code'] == $mod_AB_SRVCODE && $method_flags['sub'] == $mod_AB_SRVSUBCODE) {
					$current_rate = array (
						'methodid' => $method['subcode'],
						'rate' => trim($rate)
					);
					if ($shipping_time !== false) {
						if (is_array($shipping_time))
							$current_rate['shipping_time'] = array_pop(array_pop($shipping_time));
						else
							$current_rate['shipping_time'] = $shipping_time;
					}

					$intershipper_rates[] = $current_rate;
					break;
				}
			}
		}
	}
}

function ab_add_faults($parsed, $path) {
	global $mod_AB_faults;

	$faults = func_array_path($parsed, $path);
	if (!is_array($faults))
		return;

	foreach ($faults as $fault) {
		$desc = func_array_path($fault,'DESC/0/#');
		if ($desc === false) {
			$mod_AB_faults[] = array (
				'CODE' => func_array_path($fault,'CODE/0/#'),
				'DESCRIPTION' => func_array_path($fault,'DESC/0/#'),
				'CONTEXT' => func_array_path($fault,'CONTEXT/0/#')
			);
		}
		else {
			$mod_AB_faults[] = array (
				'CODE' => func_array_path($fault,'CODE/0/#'),
				'DESC' => $desc,
				'SOURCE' => func_array_path($fault,'SOURCE/0/#')
			);
		}
	}
}

?>
