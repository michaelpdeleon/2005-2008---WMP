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
# $Id: ups_av.php,v 1.8.2.3 2006/06/29 13:31:45 svowl Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

x_load('crypt');

#
# Input variables:
# =========================================
# $UPS_accesskey
# $UPS_username
# $UPS_password
# $userinfo: {s_address, s_city, s_state}
# $required_quality
#

#
# Output variables:
# =========================================
# $quality
# $address_is_valid
# $UPS_password
# $userinfo: {s_address, s_city, s_state}
# $required_quality
#

if ($REQUEST_METHOD == "POST" && !empty($ups_av)) {

	if ($suggest == "yes" && isset($rank)) {
		x_session_register("av_result");
		$s_city = $av_result[$rank]["city"];
		$s_state = $av_result[$rank]["state"];
		$s_zipcode = $av_result[$rank]["zipcode"];
		if ($ship2diff != 'Y') {
			$b_city = $s_city;
			$b_state = $s_state;
			$b_zipcode = $s_zipcode;
		}
	}
	elseif ($suggest == "no") {
		$av_recheck = 1;
		$av_error = 1;
		$error = 1;
	}
	$process_result = 1;
}

x_session_unregister("av_result");

if ($process_result)
	return;

#
# Get the UPS OnLine Tools module settings
#
$params = func_query_first ("SELECT * FROM $sql_tbl[shipping_options] WHERE carrier='UPS'");

$ups_parameters = unserialize($params["param00"]);
if (!is_array($ups_parameters)) {
	$ups_parameters["av_status"] = "N";
}

if ($ups_parameters["av_status"] == "Y" && $s_country == "US") {

	include $xcart_dir."/modules/UPS_OnLine_Tools/ups_states.php";

	if (!func_array_key_exists($s_state, $ups_states))
		$s_state = "";

	$required_quality = $ups_parameters["av_quality"];
	$av_error = 0;
}
else {
	return;
}

$UPS_username = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_username"]));
$UPS_password = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_password"]));
$UPS_accesskey = text_decrypt(trim($config["UPS_OnLine_Tools"]["UPS_accesskey"]));

#
# Prepare the Address Validation request
#
$request =<<<EOT
<?xml version="1.0"?>
<AccessRequest>
	<AccessLicenseNumber>$UPS_accesskey</AccessLicenseNumber>
	<UserId>$UPS_username</UserId>
	<Password>$UPS_password</Password>
</AccessRequest>
<?xml version="1.0"?>
<AddressValidationRequest xml:lang="en-US">
	<Request>
		<TransactionReference>
			<CustomerContext>Address validation request</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>AV</RequestAction>
	</Request>
	<Address>
		<City>$s_city</City>
		<StateProvinceCode>$s_state</StateProvinceCode>
		<PostalCode>$s_zipcode</PostalCode>
	</Address>
</AddressValidationRequest>
EOT;

#
# This function Forms the folowing variables:
#  $ps["RANK"]
#  $ps["QUALITY"]
#  $ps["ADDRESS.CITY"]
#  $ps["ADDRESS.STATEPROVINCECODE"]
#  $ps["ADDRESS.POSTALCODE"]
#  $ps["PostalCodeLowEnd"]
#  $ps["PostalCodeHighEnd"]
#
u_process($request, "u_elem_data_av","AV");

if (@$ps["statuscode"] != "1") {
	$error = $av_recheck = 1;
	return;
}

$quality_factors = array (
	"exact"		 => array("min" => 1.00, "max" => 1.00, "rank" => 5),
	"very_close" => array("min" => 0.95, "max" => 0.99, "rank" => 4),
	"close" 	 => array("min" => 0.90, "max" => 0.94, "rank" => 3),
	"possible"   => array("min" => 0.70, "max" => 0.89, "rank" => 2),
	"poor" 		 => array("min" => 0.00, "max" => 0.69, "rank" => 1)
);

foreach ($quality_factors as $k=>$v) {
	if ($ps[1]["QUALITY"] >= $v["min"] && $ps[1]["QUALITY"] <= $v["max"]) {
		$quality = $k;
		break;
	}
}

$address_is_valid = ($quality_factors[$quality]["rank"] >= $quality_factors[$required_quality]["rank"]);


if (!$address_is_valid) {
	$index = 0;
	foreach ($ps as $k=>$v) {
		if (!is_numeric($k))
			continue;
		if ($v["POSTALCODELOWEND"] != $v["POSTALCODEHIGHEND"]) {
			$max = intval($v["POSTALCODEHIGHEND"]) - ($v["POSTALCODELOWEND"]);
			for ($i = 0; $i<=$max; $i++) {
				$av_result[$index]["city"] = $v["ADDRESS"]["CITY"];
				$av_result[$index]["state"] = $v["ADDRESS"]["STATEPROVINCECODE"];
				$av_result[$index]["zipcode"] = $v["POSTALCODELOWEND"] + $i;
				$index++;
			}

		}
		else {
			$av_result[$index]["city"] = $v["ADDRESS"]["CITY"];
			$av_result[$index]["state"] = $v["ADDRESS"]["STATEPROVINCECODE"];
			$av_result[$index]["zipcode"] = $v["POSTALCODELOWEND"];
			$index++;
		}
	}

	x_session_register("av_result", $av_result);
	x_session_save("av_result");

	$error = $av_error = 1;
	$s_statename = func_get_state($s_state, $s_country);
	$smarty->assign("post_vars", $HTTP_POST_VARS);
	$smarty->assign("get_vars", $HTTP_GET_VARS);
	$smarty->assign("s_statename", $s_statename);
	$smarty->assign("av_result", $av_result);
	$smarty->assign("av_error", 1);
}
else
	$error = $error;

?>
