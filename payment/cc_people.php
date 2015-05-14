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
# $Id: cc_people.php,v 1.11.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

if (!function_exists('ppl_encode')) {
	function ppl_nMod($x,$y) {
		return $x - (intval($x/$y)*$y);
	}

	function ppl_mult($x,$pg,$m) {
		$y = 1;
		while ($pg>0) {
			while (($pg/2)==intval($pg/2)) {
				$x = ppl_nMod(($x*$x),$m);
				$pg /= 2;
			}

			$y = ppl_nMod(($x*$y),$m);
			$pg--;
		}

		return $y;
	}

	function ppl_encode($tIp,$KeyEnc,$KeyMod) {
		if (empty($tIp) || empty($KeyMod) || empty($KeyEnc))
			return;

		for ($z=0;$z<strlen($tIp);$z++) {
			$iAsci = ord(substr($tIp,$z,1));
			$encSt.=  sprintf("%08x",ppl_mult($iAsci,$KeyEnc,$KeyMod));
		}

		return $encSt;
	}
}

$avserr = array(
	"A" => "Address (Street) matches, ZIP does not",
	"E" => "AVS error",
	"N" => "No Match on Address (Street) or ZIP",
	"P" => "AVS not applicable for this transaction",
	"R" => "Retry. System unavailable or timed out",
	"S" => "Service not supported by issuer",
	"U" => "Address information is unavailable",
	"W" => "9 digit ZIP matches, Address (Street) does not",
	"X" => "Exact AVS Match",
	"Y" => "Address (Street) and 5 digit ZIP match",
	"Z" => "5 digit ZIP matches, Address (Street) does not"
);

$cvverr = array(
	"M" => "Match",
	"N" => "No Match",
	"P" => "Not Processed",
	"S" => "Should have been present",
	"U" => "Issuer unable to process request"
);

$an_login = $module_params["param01"];
$an_key_enc = $module_params["param02"];
$an_key_mod = $module_params["param03"];
$an_prefix = $module_params["param04"];

$first4 = 0+substr($userinfo["card_number"],0,4);
$userinfo["card_type"]="";
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="V"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="M"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="A"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="A"; # AmericanExpress
if($first4==6011)$userinfo["card_type"]="D"; # Discover

if (empty($userinfo["card_type"])) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Unknown card type. Only VISA, MasterCard, American Express or Discover card is accept.";

	return;
}


$msg_ = rand(100,999)."^".$an_login."^".date("m/d/Y h:i:s A")."^".$cart["total_cost"];
$enc = ppl_encode(strval($msg_),strval($an_key_enc),strval($an_key_mod));

$post[] = "F_LOGIN=".$an_login;
$post[] = "F_MESSAGE=".$msg_;
$post[] = "F_CRYPT_MESSAGE=".$enc;
$post[] = "F_SHOW_FORM=TRUE";
$post[] = "F_tran_id=".$an_prefix.join("-",$secure_oid);
$post[] = "F_AMOUNT=".$cart["total_cost"];
$post[] = "F_CC_TYPE=".$userinfo["card_type"];
$post[] = "F_CARD_NUM=".$userinfo["card_number"];
$post[] = "F_CARD_CODE=".$userinfo["card_cvv2"];
$post[] = "F_EXP_DATE=".substr($userinfo["card_expire"],0,2)."/".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "F_FIRST_NAME=".$bill_firstname;
$post[] = "F_LAST_NAME=".$bill_lastname;
$post[] = "F_ADDRESS=".$userinfo["b_address"];
$post[] = "F_CITY=".$userinfo["b_city"];
$post[] = "F_STATE=".(($userinfo["b_state"]!=$userinfo["b_statename"])? $userinfo["b_state"] : "OO");
$post[] = "F_COUNTRY=".$userinfo["b_country"];
$post[] = "F_ZIP=".$userinfo["b_zipcode"];
$post[] = "F_PHONE=".preg_replace("/[^0-9]/","",$userinfo["phone"]);
$post[] = "F_EMAIL=".$userinfo["email"];
$post[] = "F_SHIP_TO_FIRST_NAME=".$userinfo["s_firstname"];
$post[] = "F_SHIP_TO_LAST_NAME=".$userinfo["s_lastname"];
$post[] = "F_SHIP_TO_ADDRESS=".$userinfo["s_address"];
$post[] = "F_SHIP_TO_CITY=".$userinfo["s_city"];
$post[] = "F_SHIP_TO_STATE=".(($userinfo["s_state"]!=$userinfo["s_statename"])? $userinfo["s_state"] : "OO");
$post[] = "F_SHIP_TO_COUNTRY=".$userinfo["s_country"];
$post[] = "F_SHIP_TO_ZIP=".$userinfo["s_zipcode"];

$string = array($an_prefix.join("-",$secure_oid));
foreach ($products as $product) {
	if (!empty($active_modules['Product_Options'])) {
		$string[] = " - ".$product["product"]." (".str_replace("\n","; ",func_serialize_options($product['options']))."; ".$product["price"]." x ".$product["amount"].")";
	}
	else {
		$string[] = " - ".$product["product"]." (".$product["price"]." x ".$product["amount"].")";
	}
}

if (is_set($cart["giftcerts"]) && is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0) {
	foreach ($cart["giftcerts"] as $tmp_gc) {
		$string [] = " - GIFT CERTIFICATE (".$tmp_gc["amount"]." x 1)";
	}
}

$post[] = "F_ORDER_STRING=".join("<br />",$string);

list($a,$return) = func_https_request("POST","https://gateway.smartpeople.com:443/scripts/securegp.asp",$post);
$m = split("\",\"","\",".$return.",\"");
if (is_array($m)) {
	foreach ($m as $v) {
		if($v){
			list($a,$b) = split("=",trim($v));
			$mass[$a] = $b;
		}
	}
}

$bill_output["code"] = (($mass["x_response_code"]==1) ? 1 : 2);
$bill_output["billmes"] = $mass["x_response_reason_text"];

if(!empty($mass["x_auth_code"]))
	$bill_output["billmes"].= " (AuthCode: ".$mass["x_auth_code"].")";

if(!empty($mass["F_invoice_num"]))
	$bill_output["billmes"].= " (TransID: ".$mass["F_invoice_num"].")";

if(!empty($mass["F_cust_id"]))
	$bill_output["billmes"].= " (Invoice No: ".$mass["F_cust_id"].")";

if(!empty($mass["x_response_reason_code"]))
	$bill_output["billmes"].= " (reasoncode: ".$mass["x_response_reason_code"].")";

if(!empty($mass["x_avs_code"]))
	$bill_output["avsmes"] = (empty($avserr[$mass["x_avs_code"]]) ? "Code: ".$mass["x_avs_code"] : $avserr[$mass["x_avs_code"]]);

if(!empty($mass["x_cvv2_resp_code"]))
	$bill_output["cvvmes"] = (empty($cvverr[$mass["x_cvv2_resp_code"]]) ? "Code: ".$mass["x_cvv2_resp_code"] : $cvverr[$mass["x_cvv2_resp_code"]]);

?>
