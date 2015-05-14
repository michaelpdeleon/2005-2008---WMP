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
# $Id: cc_protxdir.php,v 1.28 2006/01/13 08:38:21 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$pp_merch = $module_params["param01"];
$pp_curr = $module_params["param03"];
$pp_test = ($module_params["testmode"]!="N") ? "https://ukvpstest.protx.com:443/VPSDirectAuth/PaymentGateway.asp" : "https://ukvps.protx.com:443/VPSDirectAuth/PaymentGateway.asp";
$pp_shift = $module_params["param05"];
$_orderids = join("-",$secure_oid);

if(is_visa($userinfo["card_number"]) && $userinfo["card_type"] != 'UKE')$userinfo["card_type"]="VISA";
if(is_mc($userinfo["card_number"]))$userinfo["card_type"]="MC"; # MasterCard
if(is_amex($userinfo["card_number"]))$userinfo["card_type"]="AMEX"; # AmericanExpress
if(is_switch($userinfo["card_number"]))$userinfo["card_type"]="SWITCH";
if(is_solo($userinfo["card_number"]))$userinfo["card_type"]="SOLO";
if(is_delta($userinfo["card_number"]))$userinfo["card_type"]="DELTA";
if(is_dc($userinfo["card_number"]))$userinfo["card_type"]="DC";
if(is_jcb($userinfo["card_number"]))$userinfo["card_type"]="JCB";

$post = array();
$post[] = "VPSProtocol=2.22";
$post[] = "TxType=PAYMENT";
$post[] = "Vendor=".$pp_merch;
$post[] = "VendorTxCode=".$pp_shift.$_orderids;
$post[] = "Amount=".$cart["total_cost"];
$post[] = "Currency=".$pp_curr;
$post[] = "Description=Your Cart";
$post[] = "CardHolder=".$userinfo["card_name"];
$post[] = "CardNumber=".$userinfo["card_number"];
$post[] = "ExpiryDate=".$userinfo["card_expire"];
$post[] = "CV2=".$userinfo["card_cvv2"];
$post[] = "CardType=".$userinfo["card_type"];

$billing_address = array();
$billing_address[] = $userinfo["b_address"];
if (!empty($userinfo["b_address_2"]))
	$billing_address[] = $userinfo["b_address_2"];
$billing_address[] = $userinfo["b_city"];
if (!empty($userinfo["b_countyname"]))
	$billing_address[] = $userinfo["b_countyname"];
$billing_address[] = $userinfo["b_statename"].(($userinfo["b_statename"] == $userinfo["b_state"]) ? "" : " (".$userinfo["b_state"].")");
$billing_address[] = $userinfo["b_countryname"].(($userinfo["b_countryname"] == $userinfo["b_country"]) ? "" : " (".$userinfo["b_country"].")");

$post[] = "BillingAddress=".implode(" ", $billing_address);
$post[] = "BillingPostCode=".$userinfo["b_zipcode"];

$ship_address = array();
$ship_address[] = $userinfo["s_address"];
if (!empty($userinfo["s_address_2"]))
	$ship_address[] = $userinfo["s_address_2"];
$ship_address[] = $userinfo["s_city"];
if (!empty($userinfo["s_countyname"]))
	$ship_address[] = $userinfo["s_countyname"];
$ship_address[] = $userinfo["s_statename"].(($userinfo["s_statename"] == $userinfo["s_state"]) ? "" : " (".$userinfo["s_state"].")");
$ship_address[] = $userinfo["s_countryname"].(($userinfo["s_countryname"] == $userinfo["s_country"]) ? "" : " (".$userinfo["s_country"].")");

$post[] = "DeliveryAddress=".implode(" ", $ship_address);
$post[] = "DeliveryPostCode=".$userinfo["s_zipcode"];

$post[] = "CustomerName=".substr($userinfo["firstname"]." ".$userinfo["lastname"], 0, 100);
$post[] = "ContactNumber=".substr(str_replace(array(" ","-"), array("",""), $userinfo["phone"]), 0, 20);
$post[] = "ContactFax=".substr(str_replace(array(" ","-"), array("",""), $userinfo["fax"]), 0, 20);
$post[] = "CustomerEMail=".$userinfo["email"];

$cnt = 0;
if (is_array($products))
	$cnt += count($products);
if (is_array($cart['giftcerts']))
	$cnt += count($cart['giftcerts']);
if ($cart['shipping_cost'] > 0)
	$cnt++;
$c = (string)$cnt;

if (!empty($products)) {
	foreach ($products as $v) {
		$c .= ":".str_replace(":", " ", $v['product']).":".$v['amount'].":".price_format($v['price']).":::".price_format($v['amount']*$v['price']);
	}
}
if (!empty($cart['giftcerts'])) {
	foreach ($cart['giftcerts'] as $v) {
		$c .= ":GIFT CERTIFICATE:1:".price_format($v['amount']).":::".price_format($v['amount']);
	}
}
if ($cart['shipping_cost'] > 0)
	$c .= ":Shipping cost:1:".price_format($cart['shipping_cost']).":::".price_format($cart['shipping_cost']);

$post[] = "Basket=".$c;
$post[] = "ClientIPAddress=".$REMOTE_ADDR;

if(isset($cmpi_result)) {
	$post[] = "ECI=".intval($cmpi_result['EciFlag']);
	$post[] = "CAVV=".$cmpi_result['Cavv'];
	$post[] = "XID=".$cmpi_result['Xid'];
	$post[] = "3DSecureStatus=";
}

if ($userinfo["card_type"] == "SOLO" || $userinfo["card_type"] == 'SWITCH') {
	$userinfo['card_issue_no'] = (empty($userinfo['card_issue_no'])) ? "" : trim($userinfo['card_issue_no']);
	$post[] = "IssueNumber=" . $userinfo['card_issue_no'];
}

list($a,$return)=func_https_request("POST",$pp_test,$post);
$ret = str_replace("\n","&",$return);

preg_match("/Status=(.+)&/U",$ret,$a);
if(trim($a[1]) == "OK") {
	$bill_output["code"]=1;
	preg_match("/TxAuthNo=(.+)&/U",$ret,$authno);
	$bill_output["billmes"] = "AuthNo: ".$authno[1];
	preg_match("/SecurityKey=(.+)&/U",$ret,$authno);
	$bill_output["billmes"].= "SecurityKey: ".$authno[1];
} else {
	$bill_output["code"]=2;
	preg_match("/StatusDetail=(.+)&/U",$ret,$stat);
	$bill_output["billmes"] = "Status: ".trim($stat[1])." (".trim($a[1]).") ";
}

preg_match("/VPSTxID={(.+)}/U",$ret,$txid);
if(!empty($txid[1]))
	$bill_output["billmes"].= " (TxID: {".trim($txid[1])."})";
preg_match("/AVSCV2=(.*)&/U",$ret,$avs);
if(!empty($avs[1]))
	$bill_output["billmes"].= " (AVS/CVV2: {".trim($avs[1])."})";

?>
