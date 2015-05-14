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
# $Id: cc_bank.php,v 1.25.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

require $xcart_dir."/include/rc4.php";

x_load('http');

$_ordr = $module_params["param03"].join("-",$secure_oid);

$post = array();
$post[] = "ioc_merchant_id=".$module_params ["param01"];
$post[] = "ioc_merchant_shopper_id=".$userinfo["login"];
$post[] = "ioc_merchant_order_id=".$_ordr;
if($module_params["param04"]) {
	$tmp = unpack("H*", func_rc4_crypt($cart["total_cost"].$_ordr.$module_params ["param01"], $module_params["param04"]));
	$post[] = "ioc_order_data=".strtoupper(array_pop($tmp));
}
$post[] = "ioc_order_total_amount=".$cart["total_cost"];

$post[] = "ecom_billto_postal_name_first=".$bill_firstname;
$post[] = "ecom_billto_postal_name_last=".$bill_lastname;
$post[] = "ecom_billto_postal_street_line1=".$userinfo["b_address"];
$post[] = "ecom_billto_postal_city=".$userinfo["b_city"];
$post[] = "ecom_billto_postal_stateprov=".$userinfo["b_state"];
$post[] = "ecom_billto_postal_postalcode=".$userinfo["b_zipcode"];
$post[] = "ecom_billto_postal_countrycode=".$userinfo["b_country"];
$post[] = "ecom_billto_telecom_phone_number=".$userinfo["phone"];
$post[] = "ecom_billto_online_email=".$userinfo["email"];

$post[] = "ecom_shipto_postal_name_first=".$userinfo["s_firstname"];
$post[] = "ecom_shipto_postal_name_last=".$userinfo["s_lastname"];
$post[] = "ecom_shipto_postal_street_line1=".$userinfo["s_address"];
$post[] = "ecom_shipto_postal_city=".$userinfo["s_city"];
$post[] = "ecom_shipto_postal_stateprov=".$userinfo["s_state"];
$post[] = "ecom_shipto_postal_postalcode=".$userinfo["s_zipcode"];
$post[] = "ecom_shipto_postal_countrycode=".$userinfo["s_country"];

$post[] = "ecom_payment_card_name=".$userinfo["card_name"];
$post[] = "ecom_payment_card_number=".$userinfo["card_number"];
$post[] = "ecom_payment_card_expdate_month=".(0+substr($userinfo["card_expire"],0,2));
$post[] = "ecom_payment_card_expdate_year=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "ioc_cvv_indicator=".$module_params ["param02"];;
$post[] = "ecom_payment_card_verification=".$userinfo["card_cvv2"];
$post[] = "ioc_auto_settle_flag=Y";

list($a,$return)=func_https_request("POST","https://cart.bamart.com:443/payment.mart",$post,"&","","application/x-www-form-urlencoded",$http_location."/payment/cc_bank.php");
#$return="m=psagroup<br />IOC_merchant_order_id=xcart576<br />IOC_merchant_shopper_id=<br />IOC_shopper_id=521JMN4GW9D18JNCW549XKB4URWX2BG8<br />IOC_response_code=8<br />Ecom_transaction_complete=TRUE<br />IOC_pcard_response=N<br />Ecom_Payment_Card_Verification_RC=P<br />IOC_order_total_amount=41.95<br />IOC_reject_description=Unable to obtain a valid credit card authorization at this time.<br />IOC_AVS_result=0<br />ioc_order_shopper_id=sdg<br />";

$m = split("<br />",$return);
foreach($m as $k => $v)
{
	list($a,$b) = split("=",trim($v),2);
	if($a)$ret[strtolower($a)] = $b;
}

$staerr = array(
	"-1" => "Authorization system not responding. Order accepted in Faith mode.",
	"1" => "Authorization system not responding. Please retry transaction.",
	"2" => "Authorization declined. Please retry with different credit card.",
	"3" => "No response from issuing institution. Order not accepted. Please retry.",
	"4" => "Authorization declined. Invalid credit card. Please retry with different credit card.",
	"5" => "Authorization declined. Invalid amount. Please retry.",
	"6" => "Authorization declined. Expired credit card. Please retry with different credit card.",
	"7" => "Authorization declined. Invalid transaction. Please retry with different credit card.",
	"8" => "Received unexpected reply. Order not accepted. Please retry.",
	"9" => "Authorization declined. Duplicate transaction.",
	"10" => "Other issue. Order not accepted. Please retry."
);

$avserr = array(
	"0" => "No data",
	"1" => "No match",
	"2" => "Address match only",
	"3" => "Zip code match only",
	"4" => "Exact match"
);

$cvverr = array(
	"M" => "CVV Matched.",
	"N" => "CVV No Match.",
	"P" => "Not Processed.",
	"S" => "CVV is on the card, but the shopper has indicated that CVV is not present.",
	"U" => "Issuer is not VISA certified for CVV and has not provided Visa encryption keys or both."
);

if (isset($ret["ioc_response_code"]) && $ret["ioc_response_code"]=="0")
{
	$bill_output["code"] = 1;
	if(!empty($ret["ioc_invoice_number"]) && !empty($ret["ioc_settlement_amount"]))
		$bill_output["billmes"] = "(Authorization Code: ".$ret["ioc_authorization_code"]."; OrderID: ".$ret["ioc_order_id"]."; IOC_invoice_number: ".$ret["ioc_invoice_number"].")";
	else
		$bill_output["billmes"] = "(Authorization Code: ".$ret["ioc_authorization_code"]."; OrderID: ".$ret["ioc_order_id"].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = ($staerr[$ret["ioc_response_code"]]) ? $staerr[$ret["ioc_response_code"]] : $ret["ioc_reject_description"]." (".$ret["ioc_response_code"].")";
}

if(isset($ret["ioc_avs_result"]))
	$bill_output["avsmes"] = empty($avserr[$ret["ioc_avs_result"]]) ? "AVS Response code: ".$ret["ioc_avs_result"] : $avserr[$ret["ioc_avs_result"]];
if(!empty($ret["ecom_payment_card_verification_rc"]))
	$bill_output["cvvmes"] = empty($cvverr[$ret["ecom_payment_card_verification_rc"]]) ? "CVV Response code: ".$ret["ecom_payment_card_verification_rc"] : $cvverr[$ret["ecom_payment_card_verification_rc"]];

#print_r($bill_output);
#print_r($ret);
#print_r($return);
#exit;

?>
