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
# $Id: cc_seci.php,v 1.8.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$avserr = array(
	"A" => "Address (Street) matches, Zip does not",
	"B" => "Partial match. Street address matches, but postal code not verified. Returned only for non-U.S.-issued Visa cards.",
	"C" => "No match. Street address and postal code not verified. Returned only for non-U.S.-issued Visa cards.",
	"D" => "Match. Street address and postal code both match. Returned only for non-U.S.-issued Visa cards.",
	"E" => "AVS Error",
	"G" => "Global (international) non-avs participant",
	"I" => "No match. Address information not verified. Returned only for non-U.S.-issued Visa cards.",
	"J" => "Match. Card member name, billing address, and postal code all match. Ship-to information verified and chargeback protection guaranteed through the Fraud Protection Program. This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor.",
	"K" => "Partial match. Card member's name matches. Both billing address and billing postal code do not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor.",
	"L" => "Partial match. Card member's name matches. Billing postal code matches, but billing address does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor.",
	"M" => "Match. Street address and postal code both match. Returned only for non-U.S.-issued Visa cards.",
	"N" => "No Match on Address (Street) or Zip",
	"O" => "Partial match. Card member name matches. Billing address matches, but billing postal code does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor.",
	"P" => "Partial match. Postal code matches, but street address not verified. Returned only for non-U.S.-issued Visa cards.",
	"Q" => "Match. Card member name, billing address, and postal code all match. Ship-to information verified but chargeback protection not guaranteed (Standard program). This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor.",
	"R" => "Retry, System unavailable or Timed out",
	"S" => "Service not supported by issuer",
	"U" => "Address information is unavailable",
	"V" => "Match. Card member name matches. Both billing address and billing postal code match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor.",
	"W" => "9 digit Zip matches, Address (Street) does not",
	"X" => "Exact AVS Match",
	"Y" => "Address (Street) and 5 digit Zip match",
	"Z" => "5 digit Zip matches, Address (Street) does not"
);

$cvverr = array(
	"M" => "CVV2 Match.",
	"N" => "CVV2 No Match.",
	"P" => "Not Processed."
);

$pp_rid = $module_params["param01"];
$pp_key  = $module_params["param02"];
$pp_curr  = $module_params["param03"];
#$userinfo["card_name"] = "cardtest";

$post = "";
$post[] = "rid=".$pp_rid;
$post[] = "ridKey=".$pp_key;
$post[] = "transtype=sale";
$post[] = "customer-first-name=".$bill_firstname;
$post[] = "customer-last-name=".$bill_lastname;
$post[] = "card-number=".$userinfo["card_number"];
$post[] = "card-amount=".$cart["total_cost"];
$post[] = "card-name=".$userinfo["card_name"];
$post[] = "card-address1=".$userinfo["b_address"];
$post[] = "card-city=".$userinfo["b_city"];
$post[] = "card-state=".$userinfo["b_state"];
$post[] = "card-country=".$userinfo["b_country"];
$post[] = "card-zip=".$userinfo["b_zipcode"];
$post[] = "card-exp=".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2);
$post[] = "card-cvv=".$userinfo["card_cvv2"];
$post[] = "shipname=".$userinfo["s_firstname"]." ".$userinfo["s_lastname"];
#address1
#city,state,country,shipping,zip
$post[] = "email=".$userinfo["email"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "ipaddress=".$REMOTE_ADDR;
$post[] = "order-id=".$module_params["param04"].join("-",$secure_oid);
#$post[] = "currency=".$pp_curr;

$i=0;
foreach ($products as $product)
{
	$post[] = "pid[".(++$i)."]=".$product["proructid"];
	$post[] = "price[".$i."]=".$product["price"];
	$post[] = "quantity[".$i."]=".$product["amount"];
	$post[] = "description[".$i."]=".$product["product"];
}

$a=func_https_request("POST","https://38.113.128.211/remote.cgi",$post);
parse_str($a[1],$ret);

$bill_output["code"] = (($ret["FinalStatus"]=="success") ? 1 : 2);
$bill_output["billmes"] = $ret["FinalStatus"].": ".$ret["MErrMsg"];

if($ret["orderID"])
	$bill_output["billmes"].= " (orderID: ".$ret["orderID"].")";
#$bill_output["billmes"].= " (auth-code: ".$ret["auth-code"]."; auth-msg: ".$ret["auth-msg"]."; authtype: ".$ret["authtype"].")";

if($ret["avs-code"])
	$bill_output["avsmes"].= (empty($avserr[$ret["avs-code"]]) ? "Code: ".$ret["avs-code"] : $avserr[$ret["avs-code"]]);
if($ret["cvvresp"])
	$bill_output["cvvmes"].= (empty($cvverr[$ret["cvvresp"]]) ? "Code: ".$ret["cvvresp"] : $cvverr[$ret["cvvresp"]]);

?>
