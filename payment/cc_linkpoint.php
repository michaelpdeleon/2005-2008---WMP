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
# $Id: cc_linkpoint.php,v 1.36.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_login = $module_params["param01"];
$sert = $module_params["param02"];
$host =  $module_params["param06"];
$port = $module_params["param07"];
$lp_test = "LIVE";
switch($module_params["testmode"]) {
	case "A": $lp_test = "GOOD"; break;
	case "D": $lp_test = "DECLINE"; break;
}

$addrnum = preg_replace("/[^\d]/","",$userinfo["b_address"]);

$post = array();
$post[] = "<order>";

$post[] = "<orderoptions>";
$post[] = "<ordertype>PREAUTH</ordertype>";
$post[] = "<result>".$lp_test."</result>";
$post[] = "</orderoptions>";

$post[] = "<creditcard>";
$post[] = "<cardnumber>".$userinfo["card_number"]."</cardnumber>";
$post[] = "<cardexpmonth>".substr($userinfo["card_expire"],0,2)."</cardexpmonth>";
$post[] = "<cardexpyear>".substr($userinfo["card_expire"],2,2)."</cardexpyear>";
$post[] = "<cvmvalue>".$userinfo["card_cvv2"]."</cvmvalue>";
$post[] = "<cvmindicator>".$module_params["param04"]."</cvmindicator>";
$post[] = "</creditcard>";

$post[] = "<merchantinfo>";
$post[] = "<configfile>".$pp_login."</configfile>";
$post[] = "<keyfile>".$sert."</keyfile>";
$post[] = "<host>".$host."</host><port>".$port."</port>";
$post[] = "</merchantinfo>";

$post[] = "<payment>";
$post[] = "<chargetotal>".price_format($cart["total_cost"])."</chargetotal>";
$post[] = "</payment>";

$post[] = "<billing>";
$post[] = "<name>".htmlspecialchars($bill_name)."</name>";
$post[] = "<address1>".htmlspecialchars($userinfo["b_address"])."</address1>";
$post[] = "<company>".htmlspecialchars($userinfo["company"])."</company>";
$post[] = "<address2>".htmlspecialchars($userinfo["b_address_2"])."</address2>";
$post[] = "<addrnum>".$addrnum."</addrnum>";
$post[] = "<city>".htmlspecialchars($userinfo["b_city"])."</city>";
$post[] = "<state>".htmlspecialchars($userinfo["b_state"])."</state>";
$post[] = "<zip>".htmlspecialchars($userinfo["b_zipcode"])."</zip>";
$post[] = "<country>".htmlspecialchars($userinfo["b_country"])."</country>";
$post[] = "<phone>".htmlspecialchars($userinfo["phone"])."</phone>";
$post[] = "<fax>".htmlspecialchars($userinfo["fax"])."</fax>";
$post[] = "<email>".htmlspecialchars($userinfo["email"])."</email>";
$post[] = "</billing>";

$cnt = 0;
$weight = 0;
if (!empty($products)) {
	foreach ($products as $v) {
		$cnt += $v['amount'];
		$weight += $v['weight']*$v['amount'];
	}
}

$post[] = "<shipping>";
$post[] = "<name>".htmlspecialchars($userinfo["s_firstname"]." ".$userinfo["s_lastname"])."</name>";
$post[] = "<address1>".htmlspecialchars($userinfo["s_address"])."</address1>";
$post[] = "<address2>".htmlspecialchars($userinfo["s_address_2"])."</address2>";
$post[] = "<city>".htmlspecialchars($userinfo["s_city"])."</city>";
$post[] = "<state>".htmlspecialchars($userinfo["s_state"])."</state>";
$post[] = "<zip>".htmlspecialchars($userinfo["s_zipcode"])."</zip>";
$post[] = "<country>".htmlspecialchars($userinfo["s_country"])."</country>";
$post[] = "<weight>".$weight."</weight>";
$post[] = "<items>".$cnt."</items>";
$post[] = "<total>".price_format($cart['total_cost']-$cart['shipping_cost'])."</total>";
$post[] = "</shipping>";

$post[] = "<transactiondetails>";
$post[] = "<oid>".$module_params["param05"].join("-",$secure_oid)."</oid>";
$post[] = "<ip>".$REMOTE_ADDR."</ip>";
$post[] = "</transactiondetails>";

/*
$post[] = "<items>";
if (!empty($products)) {
	foreach ($products as $v) {
		$post[] = "<item><id>$v[productid]</id><description>".htmlspecialchars($v['product'])."</description><price>$v[price]</price><quantity>$v[amount]</quantity><serial>".htmlspecialchars($v['productcode'])."</serial></item>";
	}
}
if (!empty($cart['giftcerts'])) {
	foreach($cart['giftcerts'] as $v) {
		$post[] = "<item><id>$v[gcid]</id><description>GEFT CERTIFICATE</description><price>$v[amount]</price><quantity>1</quantity><serial>$v[gcid]</serial></item>";
	}
}
$post[] = "</items>";
*/

$post[] = "</order>";
list($a,$return)=func_https_request("POST","https://$host:$port/LSGSXML",$post,"","","application/x-www-form-urlencoded","",$sert,$sert);

#<r_time>Mon May 26 23:39:46 2003</r_time>
#	<r_ref>12345678</r_ref>
#	<r_approved>APPROVED</r_approved>
#	<r_code>0123456789123456:YNAM:01234567890123412345678:</r_code>
#	<r_error></r_error>
#	<r_ordernum>xcart596</r_ordernum>
#	<r_authresponse></r_authresponse>
#	<r_message></r_message>
#	<r_tdate>1054017586</r_tdate>

#<r_time>Mon May 26 23:45:51 2003</r_time>
#	<r_ref></r_ref>
#	<r_approved>FRAUD</r_approved>
#	<r_code></r_code>
#	<r_error>This credit card appears to have expired.</r_error>
#	<r_ordernum>xcart597</r_ordernum>
#	<r_authresponse></r_authresponse>
#	<r_message></r_message>
#	<r_tdate>1054017951</r_tdate>

$avserr = array(
	"YY" => "Address matches, zip code matches",
	"YN" => "Address matches, zip code does not match",
	"YX" => "Address matches, zip code comparison not available",
	"NY" => "Address does not match, zip code matches",
	"XY" => "Address comparison not available, zip code matches",
	"NN" => "Address comparison does not match, zip code does not match",
	"NX" => "Address does not match, zip code comparison not available",
	"XN" => "Address comparison not available, zip code does not match",
	"XX" => "Address comparisons not available, zip code comparison not available",
);

$cvverr = array(
	"M" => "Card Code Match",
	"N" => "Card code does not match",
	"P" => "Not processed",
	"S" => "Merchant has indicated that the card code is not present on the card",
	"U" => "Issuer is not certified and/or has not provided encryption keys"
);

$bill_output["avsmes"] = $bill_output["cvvmes"] = "";
$bill_output["code"] = 2;
preg_match("/<r_approved>(.*)<\/r_approved>/",$return, $status);

$bill_output["billmes"] = "";
if (preg_match("/<r_code>(.*)<\/r_code>/", $return, $out)) {
	$bill_output["billmes"] .= "Code [".$out[1]."] :: ";
	if (preg_match("/(\d{6})(\d{10}):(\w{2})(\w)([ \w]):(.*):$/", trim($out[1]), $pars)) {
		if (($pars[3] == 'NN' || $pars[4] == 'N' || $pars[5] == 'N') && $status[1] == "APPROVED") {
			$status[1] = "FRAUD";
		}

		if ($cvverr[$pars[5]])
			$bill_output["cvvmes"] = $cvverr[$pars[5]];
		if ($pars[5])
			$bill_output["cvvmes"].= " (CVV code: ".$pars[5].")";

		if ($avserr[$pars[3]])
			$bill_output["avsmes"] = $avserr[$pars[3]];
		if ($pars[3])
			$bill_output["avsmes"].= " (AVS code: ".$pars[3].$pars[4].")";
	}
}

if ($status[1] == "APPROVED") {

	$post[2] = "<ordertype>POSTAUTH</ordertype>";
	list($a,$return) = func_https_request("POST","https://$host:$port/LSGSXML",$post,"","","application/x-www-form-urlencoded","",$sert,$sert);

	if (preg_match("/<r_approved>APPROVED<\/r_approved>/",$return)) {
		$bill_output["code"] = 1;
	} else {
		preg_match("/<r_approved>(.*)<\/r_approved>/",$return, $status);
	}
}

if ($bill_output["code"] == 2) {
	preg_match("/<r_error>(.*)<\/r_error>/",$return,$out);
	$bill_output["billmes"] = "[".$status[1]."] ".$out[1].$bill_output["billmes"];
}

if (preg_match("/<r_authresponse>(.+)<\/r_authresponse>/",$return,$out))
	$bill_output["billmes"] .= " (AuthResponce: ".$out[1].")";

if (preg_match("/<r_message>(.+)<\/r_message>/",$return,$out))
	$bill_output["billmes"] .= " (Message: ".$out[1].")";

?>
