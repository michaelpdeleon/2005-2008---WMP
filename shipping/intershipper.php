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
# $Id: intershipper.php,v 1.54.2.1 2006/06/19 08:35:40 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('xml','http','tests');

function func_shipper($weight, $userinfo, $debug="N", $cart=false) {
	global $allowed_shipping_methods, $intershipper_rates;
	global $sql_tbl;
	global $config;

	$__intershipper_userinfo = $userinfo;

	$intershipper_rates = array ();

	$intershipper_countries = array (
		'IE' => 'IR',	# IRELAND
		'VA' => 'IT',	# ITALY AND VATICAN CITY STATE
		'FX' => 'FR',	# FRANCE
		'PR' => 'US'	# PUERTO RICO
	);

	#
	# Intershipper depends on XML parser (EXPAT extension)
	#
	if (test_expat() == "")
		return;

	if (empty($userinfo) && ($config["General"]["apply_default_country"]=="Y" || $debug=="Y")) {
		$__intershipper_userinfo["s_country"] = $config["General"]["default_country"];
		$__intershipper_userinfo["s_state"] = $config["General"]["default_state"];
		$__intershipper_userinfo["s_zipcode"] = $config["General"]["default_zipcode"];
		$__intershipper_userinfo["s_city"] = $config["General"]["default_city"];
	}
	elseif (empty($userinfo)) {
		return array();
	}

	$pounds=func_weight_in_grams($weight)/453;
	$pounds=sprintf("%.2f",round((double)$pounds+0.00000000001,2));
	if($pounds<0.1) $pounds=0.1;

	$servername="www.intershipper.com";
	$scriptname="/Shipping/Intershipper/XML/v2.0/HTTP.jsp";

	$username=$config["Shipping"]["intershipper_username"];
	$password=$config["Shipping"]["intershipper_password"];

	$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='INTERSHIPPER'");

	$delivery=$params["param00"];
	$shipmethod=$params["param01"];

	$CO=$config["Company"]["location_country"];
	$ZO=urlencode($config["Company"]["location_zipcode"]);

	$CD=$__intershipper_userinfo["s_country"];
	$ZD=urlencode($__intershipper_userinfo["s_zipcode"]);

	if (!empty($intershipper_countries[$CD])) $CD = $intershipper_countries[$CD];
	if (!empty($intershipper_countries[$CO])) $CO = $intershipper_countries[$CO];

	$__intershipper_userinfo["s_country"] = $CD;
	$config["Company"]["location_country"] = $CO;

	$length=(double)$params["param02"];
	$width=(double)$params["param03"];
	$height=(double)$params["param04"];
	$dunit=$params["param05"];

	$packaging=$params["param06"];
	$contents=$params["param07"];

	$codvalue=(double)$params["param08"];
	$insvalue=(double)$params["param09"];
	$queryid=substr(uniqid(rand()),0,15);
	$wunit=strtoupper(trim($config["General"]["weight_symbol"]));
	if (strlen($wunit) > 2) $wunit = substr($wunit,0,2);

	$allowed_shipping_methods = func_query ("SELECT * FROM $sql_tbl[shipping] WHERE active='Y'");

	$carriers = func_query_column("SELECT DISTINCT(code) FROM $sql_tbl[shipping] WHERE code<>'' AND intershipper_code!='' AND active='Y'");

	if (!$carriers || !$username || !$password)
		return array();

	$post[] = "Version=2.0.0.0";
	$post[] = "ShipmentID=1";
	$post[] = "QueryID=1";
	$post[] = "Username=$username";
	$post[] = "Password=$password";
	$post[] = "TotalClasses=4";
	$post[] = "ClassCode1=GND";
	$post[] = "ClassCode2=1DY";
	$post[] = "ClassCode3=2DY";
	$post[] = "ClassCode4=3DY";
	$post[] = "DeliveryType=$delivery";
	$post[] = "ShipMethod=$shipmethod";
	$post[] = "OriginationPostal=$ZO";
	$post[] = "OriginationCountry=$CO";
	$post[] = "DestinationPostal=$ZD";
	$post[] = "DestinationCountry=$CD";
	$post[] = "Currency=USD";				// Currently, supported only "USD". maxlen=3
	$post[] = "TotalPackages=1";
	$post[] = "BoxID1=box1";
	$post[] = "Weight1=$pounds";
	$post[] = "WeightUnit1=LB";
	$post[] = "Length1=$length";
	$post[] = "Width1=$width";
	$post[] = "Height1=$height";
	$post[] = "DimensionalUnit1=$dunit";	// DimensionalUnit	::= CM | IN
	$post[] = "Packaging1=$packaging";		// Packaging		::= BOX | ENV | LTR | TUB
	$post[] = "Contents1=$contents";
	$post[] = "Cod1=$codvalue";
	$post[] = "Insurance1=$insvalue";
	$post[] = "TotalCarriers=".count($carriers);

	foreach ($carriers as $k => $v) {
		if ($v == 'CPC')
			$v = 'CAN';
		$post[] = "CarrierCode".($k+1)."=".$v;
	}

	$query = join('&', $post);
	list($header, $result) = func_http_get_request($servername, $scriptname, $query);

	$result = preg_replace("/^<\?xml\s+[^>]+>/s", "", trim($result));

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = func_xml_parse($result, $parse_errors, $options);

	$destination = ($__intershipper_userinfo["s_country"]==$config["Company"]["location_country"])?"L":"I";

	$packages =& func_array_path($parsed, 'SHIPMENT/PACKAGE');
	if (is_array($packages)) {
		$rates = array();
		foreach ($packages as $pkginfo) {
			if (empty($pkginfo['#']) || !is_array($pkginfo['#']))
				continue;

			foreach ($pkginfo['#']['QUOTE'] as $quote) {
				$carrier = func_array_path($quote, 'CARRIER/CODE/0/#');
				if ($carrier == 'USP')
					$carrier = 'USPS';

				$service = func_array_path($quote, 'SERVICE/NAME/0/#');
				$sn = func_array_path($quote, 'SERVICE/CODE/0/#');
				$rate = func_array_path($quote, 'RATE/AMOUNT/0/#') / 100.0;

				if (!$carrier || !($service || $sn) || !$rate) {
					continue;
				}

				$saved = -1;

				foreach ($allowed_shipping_methods as $sk=>$sv) {
					if ($sv["code"] != $carrier || $sv["destination"] != $destination)
						continue;

					if ($sv["intershipper_code"] == 'CPC')
						$sv["intershipper_code"] = 'CAN';

					if ((!$sn || $sv["intershipper_code"] != $sn) && (!$service || !stristr($sv["shipping"],$service)))
						continue;

					# Suppressing duplicates
					if ($saved < 0 || strlen($allowed_shipping_methods[$saved]["shipping"]) > strlen($sv["shipping"]))
						$saved = $sk;
				}

				if ($saved >= 0)
					$rates[$allowed_shipping_methods[$saved]["subcode"]] = $rate;
			}
		}

		if (!empty($rates)) {
			foreach ($rates as $k=>$v) {
				$intershipper_rates[]= array ("methodid"=>$k, "rate"=>$v);
			}
		}
	}

	if ($debug=="Y") {
		print "<table width=800><tr><td width=800>";
		print "<h1>InterShipper Debug Information</h1>";
		if ($query) {
			$query=preg_replace("/([&?])(Username[=][^&]*)/i","\\1Username=xxx",$query);
			$query=preg_replace("/([&?])(Password[=][^&]*)/i","\\1Password=xxx",$query);
			print "<h2>InterShipper Request</h2>";
			print "<pre><font>".htmlspecialchars($query)."</font></pre>";
			print "<h2>InterShipper Response</h2>";
			$out = $result;
			$out = preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $out);
			$out = preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $out);
			print "<pre><font>".htmlspecialchars($out)."</font></pre>";
			if ($intershipper_error != ""){
				print "<h1>Error processing request at Intershipper</h1>";
				print $intershipper_error;
			}
			else {
				func_shipper_show_rates($intershipper_rates);
			}
		}
		else {
			print "It seems, you have forgotten to fill in an InterShipper account information.";
		}

		print "</td></tr></table>";
	}

	return $intershipper_rates;
}

?>
