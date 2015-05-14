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
# $Id: mod_UPS.php,v 1.42.2.4 2006/08/03 08:14:55 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }


function func_shipper_UPS($weight, $userinfo, $debug, $cart) {
	global $config, $sql_tbl, $smarty, $active_modules;
	global $ups_services, $origin_code, $dest_code;
	global $mod_UPS_tags, $mod_UPS_service;
	global $mod_UPS_errorcode, $mod_UPS_errordesc;
	global $mod_UPS_convrate;
	global $ups_services;
	global $UPS_url;
	global $show_XML;
	global $allowed_shipping_methods, $intershipper_rates;
	global $shipping_calc_service, $intershipper_error;

	if (empty($active_modules["UPS_OnLine_Tools"]))
		return;

	x_load('crypt','http');
	
	$UPS_username = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_username"]));
	$UPS_password = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_password"]));
	$UPS_accesskey = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_accesskey"]));

	if (empty($UPS_username) || empty($UPS_password) || empty($UPS_accesskey))
		return;

	$UPS_FOUND = false;
	foreach ($allowed_shipping_methods as $key=>$value) {
		if ($value["code"] == "UPS")
			$UPS_FOUND = true;
	}

	if (!$UPS_FOUND)
		return;


	#
	# Need to display UPS OnLine Tools trademarks
	#
	$smarty->assign("display_ups_trademarks", 1);

	#
	# Default UPS shipping options (if it wasn't defined yet by admin)
	#
	$ups_parameters_default = array(
		"account_type" => "01",
		#"customer_classification_code" => "03",
		#"pickup_type" => "01",
		"packaging_type" => "02",#01
		"length" => 0,#02
		"width" => 0, #03
		"height" => 0,#04
		"upsoptions" => array(), #05
		"codvalue" => 0.00,#06
		"cod_currency" => "USD",
		"cod_funds_code" => 0,
		"iv_amount" => 0.00,#07
		"iv_currency" => "USD", #07
		"delivery_conf" => 0,
		"conversion_rate" => 1,
		"av_status" => "Y",
		"av_quality" => "close"
	);

	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

	$ups_parameters = unserialize($params["param00"]);
	if (!is_array($ups_parameters)) {
		$ups_parameters = $ups_parameters_default;
	}

	switch ($ups_parameters["account_type"]) {
	case "01":
		$ups_parameters["customer_classification_code"] = "01";
		$ups_parameters["pickup_type"] = "01";
		break;
	case "02":
		$ups_parameters["customer_classification_code"] = "03";
		$ups_parameters["pickup_type"] = "03";
		break;
	case "03":
	default:
		$ups_parameters["customer_classification_code"] = "04";
		$ups_parameters["pickup_type"] = "11";
	}

	#
	# The origin address - from Company options
	# (suppose that ShipperAddress and ShipFrom is equal)
	#
	$src_country_code = $config["Company"]["location_country"];
	$src_city = func_ups_xml_quote($config["Company"]["location_city"]);
	$src_zipcode = $config["Company"]["location_zipcode"];

	#
	# The destination address - from user's profile
	#
	$dest_code = $dst_country_code = $userinfo["s_country"];
	$dst_city = func_ups_xml_quote($userinfo["s_city"]);
	$dst_zipcode = $userinfo["s_zipcode"];

	if ($src_country_code == "US" && !empty($ups_parameters["customer_classification_code"])) {
		#
		# CustomerClassification section is valid for origin country = 'US' only
		#
		$customer_classification_code = $ups_parameters["customer_classification_code"];
		$customer_classification_query=<<<EOT
	<CustomerClassification>
		<Code>$customer_classification_code</Code>
	</CustomerClassification>
EOT;
	}

	#
	# Pickup Type and Packaging Type
	#
	$pickup_type = $ups_parameters["pickup_type"];
	$packaging_type = $ups_parameters["packaging_type"];

	#
	# Weight of a package
	#
	if (in_array($src_country_code, array("DO","PR","US"))) {
		$UPS_wunit = "LBS";
		$UPS_dunit = "IN";
		$dim_koefficient = 1;
	}
	else {
		$UPS_wunit = "KGS";
		$UPS_dunit = "CM";
		$dim_koefficient = 1;
	}

	if ($packaging_type=="01")
		$UPS_weight = "0.0"; # UPS Letter
	else
		$UPS_weight = max(0.1,round(func_weight_in_grams($weight)/($UPS_wunit=="LBS"?453.6:1000),1));

	#
	# Dimensions of a package
	#
	$UPS_length = round(doubleval($ups_parameters["length"]) / $dim_koefficient, 2);
	$UPS_width = round(doubleval($ups_parameters["width"]) / $dim_koefficient, 2);
	$UPS_height = round(doubleval($ups_parameters["height"]) / $dim_koefficient, 2);
	
	if ($UPS_length + $UPS_width + $UPS_height > 0) {
		#
		# Insert the Dimensions section
		#
		$dimensions_query=<<<DIM
			<Dimensions>
				<UnitOfMeasurement>
					<Code>$UPS_dunit</Code>
				</UnitOfMeasurement>
				<Length>$UPS_length</Length>
				<Width>$UPS_width</Width>
				<Height>$UPS_height</Height>
			</Dimensions>

DIM;

		if (in_array($ups_parameters["oversize"], array("1", "2", "3"))) {
			$dimensions_query .=<<<DIM
			<OversizePackage>$ups_parameters[oversize]</OversizePackage>
DIM;
		}
	}

	$insvalue = round(doubleval($ups_parameters["iv_amount"]),2);
	$pkgopt = array();
	if ($insvalue > 0.1) {
		$pkgopt[] =<<<EOT
				<InsuredValue>
					<CurrencyCode>$ups_parameters[iv_currency]</CurrencyCode>
					<MonetaryValue>$insvalue</MonetaryValue>
				</InsuredValue>

EOT;
	}
	
	$delivery_conf = intval($ups_parameters["delivery_conf"]);
	if ($delivery_conf > 0 && $delivery_conf < 4) {
		$pkgopt[] =<<<EOT
				<DeliveryConfirmation>
					<DCISType>$delivery_conf</DCISType>
				</DeliveryConfirmation>

EOT;
	}

	$codvalue = round(doubleval($ups_parameters["codvalue"]),2);
	$cod_is_allowed = false;
	$cod_is_allowed |= (($src_country_code == "US" || $src_country_code == "PR") && ($dst_country_code == "US" || $dst_country_code == "PR"));
	$cod_is_allowed |= ($src_country_code == "CA" && (($dst_country_code == "US" || $dst_country_code == "CA")));
	if ($cod_is_allowed && $codvalue > 0.1) {
		$pkgopt[] =<<<EOT
				<COD>
					<CODCode>3</CODCode>
					<CODFundsCode>$ups_parameters[cod_funds_code]</CODFundsCode>
					<CODAmount>
						<CurrencyCode>$ups_parameters[cod_currency]</CurrencyCode>
						<MonetaryValue>$codvalue</MonetaryValue>
					</CODAmount>
				</COD>

EOT;
	}

	$pkgparams = (count($pkgopt) > 0)?"\t\t\t<PackageServiceOptions>\n".join("",$pkgopt)."\t\t\t</PackageServiceOptions>\n":"";

	$srvopts = array();
	foreach (explode("|",$ups_parameters["upsoptions"]) as $opt) {
		switch($opt) {
			case "AH": $pkgparams .= "\t\t\t<AdditionalHandling/>"; break;
			case "SP": $srvopts[] = "\t\t\t<SaturdayPickupIndicator/>\n"; break;
			case "SD": $srvopts[] = "\t\t\t<SaturdayDeliveryIndicator/>\n"; break;
		}
	}

	if (!empty($ups_parameters['shipper_number'])) {
		$shipper_number_xml=<<<EOT
			<ShipperNumber>$ups_parameters[shipper_number]</ShipperNumber>
EOT;
	}
	else
		$shipper_number_xml = "";

	# Residential / commercial address indicator
	if ($ups_parameters["residential"] == "Y")
		$residental_flag = "\t\t\t<ResidentialAddressIndicator/>";
	else
		$residental_flag="";

	if (count($srvopts)>0)
		$shipment_options_xml .= "\t\t<ShipmentServiceOptions>\n".join("", $srvopts)."\t\t</ShipmentServiceOptions>";

	$query=<<<EOT
<?xml version='1.0'?>
<AccessRequest xml:lang='en-US'>
	<AccessLicenseNumber>$UPS_accesskey</AccessLicenseNumber>
	<UserId>$UPS_username</UserId>
	<Password>$UPS_password</Password>
</AccessRequest>
<?xml version='1.0'?>
<RatingServiceSelectionRequest xml:lang='en-US'>
	<Request>
		<TransactionReference>
			<CustomerContext>Rating and Service</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption>shop</RequestOption>
	</Request>
	<PickupType>
		<Code>$pickup_type</Code>
	</PickupType>
$customer_classification_query
	<Shipment>
		<Shipper>
$shipper_number_xml
			<Address>
				<City>$src_city</City>
				<PostalCode>$src_zipcode</PostalCode>
				<CountryCode>$src_country_code</CountryCode>
			</Address>
		</Shipper>
		<ShipFrom>
			<Address>
				<City>$src_city</City>
				<PostalCode>$src_zipcode</PostalCode>
				<CountryCode>$src_country_code</CountryCode>
			</Address>
		</ShipFrom>
		<ShipTo>
			<Address>
				<City>$dst_city</City>
				<PostalCode>$dst_zipcode</PostalCode>
				<CountryCode>$dst_country_code</CountryCode>
$residental_flag
			</Address>
		</ShipTo>
		<Package>
			<PackagingType>
				<Code>$packaging_type</Code>
			</PackagingType>
			<PackageWeight>
				<UnitOfMeasurement>
					<Code>$UPS_wunit</Code>
				</UnitOfMeasurement>
				<Weight>$UPS_weight</Weight>
			</PackageWeight>
$dimensions_query
$pkgparams
		</Package>
$shipment_options_xml		
	</Shipment>
</RatingServiceSelectionRequest>
EOT;

	$post=explode("\n",$query);

	#
	# Perform the XML request
	#
	if ($show_XML) $debug = "Y";

	list ($a,$result) = func_https_request("POST",$UPS_url."Rate",$post,"","","text/xml");

	$mod_UPS_tags = array();
	$mod_UPS_service = "";
	$mod_UPS_errorcode = "";

	if ((float)$ups_parameters["conversion_rate"] != 0)
		$mod_UPS_convrate = (float)$ups_parameters["conversion_rate"];
	else
		$mod_UPS_convrate = 1.0;

	if (in_array($src_country_code, array("US", "CA", "PR", "MX"))) {
		#
		# Origin is US, Canada, Puerto Rico or Mexico
		#
		$origin_code = $src_country_code;
	}
	else {
		if (in_array($src_country_code, array("AT","BE","DK","FI","FR","DE","GR","IE","IT","LU","NL","PT","ES","SE","GB"))) {
			#
			# Origin is European Union
			#
			$origin_code = "EU";
		}
		else {
			#
			# Origin is other countries
			#
			$origin_code = "OTHER_ORIGINS";
		}
	}

	$xml_parser = xml_parser_create("ISO-8859-1");
	xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
	xml_set_element_handler($xml_parser, "UPS_startElement", "UPS_endElement");
	xml_set_character_data_handler($xml_parser, "UPS_characterData");
	xml_parse($xml_parser, $result);
	xml_parser_free($xml_parser);

	if (!empty($mod_UPS_errordesc)) {
		$shipping_calc_service = "UPS";
		$intershipper_error = $mod_UPS_errordesc." (errorcode: ".$mod_UPS_errorcode.")";
	}
	elseif (!empty($intershipper_rates)) {
		$_intershipper_rates = array();
		foreach ($intershipper_rates as $k=>$v) {
			if (empty($v["shipping_time"])) {
				foreach ($allowed_shipping_methods as $_method_data) {
					if ($_method_data["subcode"] == $v["methodid"]) {
						$v["shipping_time"] = $_method_data["shipping_time"];
						break;
					}
				}
			}

			if (!empty($v["methodid"]))
				$_intershipper_rates[] = $v;
		}

		$intershipper_rates = $_intershipper_rates;
	}

	if (!empty($intershipper_rates)) {
		if ($ups_parameters["currency_code"] != $intershipper_rates[0]["currency"]) {
			$ups_parameters["currency_code"] = $intershipper_rates[0]["currency"];
			db_query("UPDATE $sql_tbl[shipping_options] SET param00='".addslashes(serialize($ups_parameters))."' WHERE carrier='UPS'");
		}
	}

	if ($debug=="Y") {
		print "<h1>UPS Debug Information</h1>";
		if ($query) {
			$query=preg_replace("|<AccessLicenseNumber>.*</AccessLicenseNumber>|i","<AccessLicenseNumber>xxx</AccessLicenseNumber>",$query);
			$query=preg_replace("|<UserId>.*</UserId>|i","<UserId>xxx</UserId>",$query);
			$query=preg_replace("|<Password>.*</Password>|i","<Password>xxx</Password>",$query);

			print "<h2>UPS Request</h2>";
			print "<pre>".htmlspecialchars($query)."</pre>";
			print "<h2>UPS Response</h2>";
			$result = preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $result);
			$result = preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $result);
			print "<pre>".htmlspecialchars($result)."</pre>";
			if ($intershipper_error != "") {
				print "<h1>Error processing request at UPS</h1>";
				print $intershipper_error;
			}
		}
		else {
			print "Before Rates & Service Selection Tool will be enabled you need to go through licensing and registering with UPS.";
		}
	}

	$query = null;
	$result = null;
}

#
# Functions to parse XML-response
#
function UPS_startElement($parser, $name, $attrs) {
	global $mod_UPS_tags;

	array_push($mod_UPS_tags,$name);
}

function UPS_characterData($parser, $data) {
	global $mod_UPS_tags, $mod_UPS_service;
	global $allowed_shipping_methods, $intershipper_rates;
	global $mod_UPS_errorcode, $mod_UPS_errordesc;
	global $mod_UPS_currency, $mod_UPS_convrate;
	global $ups_services, $origin_code, $dest_code;
	global $RatedShipmentWarning;
	global $rate_added_flag;

	if ($mod_UPS_tags[2] == "GUARANTEEDDAYSTODELIVERY" && $rate_added_flag) {
		$_current_index = count($intershipper_rates)-1;
		
		if (in_array($intershipper_rates[$_current_index]["service_code"], $ups_services[$mod_UPS_service])) {
			$intershipper_rates[$_current_index]["shipping_time"] = $data;
			$rate_added_flag = false;
		}
	}

	if ($mod_UPS_tags[2] == "RATEDSHIPMENTWARNING")
		$RatedShipmentWarning = $data;

	if (count($mod_UPS_tags)==4) {
		if ($mod_UPS_tags[2]=="SERVICE" && $mod_UPS_tags[3]=="CODE") {
			$mod_UPS_service=$data;
		}
		elseif ($mod_UPS_tags[2]=="TOTALCHARGES" && $mod_UPS_tags[3]=="CURRENCYCODE") {
			$mod_UPS_currency = $data;
		}
		elseif ($mod_UPS_tags[2]=="TOTALCHARGES" && $mod_UPS_tags[3]=="MONETARYVALUE") {
			$orig_rate = $data;
			$data = round($mod_UPS_convrate * $data, 2);
			$is_found = false;
			foreach ($allowed_shipping_methods as $sk=>$sv) {
				if ($sv["code"]!="UPS")
					continue;

				if ($sv["service_code"]==$ups_services[$mod_UPS_service][$origin_code]) {
					$subcode = (($sv["service_code"]=="14" && $dest_code == "CA") ? 110 : $sv["subcode"]);
					$intershipper_rates[] = array(
						"methodid"=>$subcode,
						"rate"=>$data,
						"currency"=>$mod_UPS_currency,
						"orig_rate"=>$orig_rate,
						"warning"=>$RatedShipmentWarning,
						"service_code"=>$sv["service_code"]
					);
					$RatedShipmentWarning = "";
					$rate_added_flag = true;
					$is_found = true;
				}
			}

			if (!empty($mod_UPS_service) && !$is_found) {
				func_add_new_smethod("UPS #".$mod_UPS_service, "UPS", array("service_code" => $mod_UPS_service));
			}
		}

		if ($mod_UPS_tags[3] == "ERRORCODE")
			$mod_UPS_errorcode = $data;

		if ($mod_UPS_tags[3] == "ERRORDESCRIPTION")
			$mod_UPS_errordesc = $data;
	}
}

function UPS_endElement($parser, $name) {
	global $mod_UPS_tags;

	array_pop($mod_UPS_tags);
}

?>
