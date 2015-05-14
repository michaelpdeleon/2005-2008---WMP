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
# $Id: cc_usight.php,v 1.8.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$staerr = array(
	"1" => "Failure",
	"2" => "Bad data",
	"3" => "Internal error"
);

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
	"I" => "Invalid",
	"M" => "CVV2 Match.",
	"N" => "CVV2 No Match.",
	"P" => "Not Processed.",
	"S" => "Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated data is not present on the card.",
	"U" => "Issuer has not certified for CVV2 or Issuer has not provided Visa with the CVV2 encryption keys."
);

$pp_merch = $module_params["param01"];

$post = "";
$post[] = "GWUsername=".$pp_merch;
$post[] = "GWBillingFirstName=".$bill_firstname;
$post[] = "GWBillingLastName=".$bill_lastname;
$post[] = "GWNameOnCard=".$userinfo["card_name"];
$post[] = "GWBillingAddress=".$userinfo["b_address"];
$post[] = "GWBillingCity=".$userinfo["b_city"];
$post[] = "GWBillingState=".($userinfo["b_state"] ? $userinfo["b_state"] : "NL");
$post[] = "GWBillingZip=".$userinfo["b_zipcode"];
$post[] = "GWBillingCountry=".$userinfo["b_country"];
$post[] = "GWCardNumber=".$userinfo["card_number"];
$post[] = "GWCardExpMonth=".(0+substr($userinfo["card_expire"],0,2));
$post[] = "GWCardExpYear=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "GWAmount=".$cart["total_cost"];
$post[] = "Phone=".$userinfo["phone"];
$post[] = "Email=".$userinfo["email"];
$post[] = "CVV2=".$userinfo["card_cvv2"];
$post[] = "InvoiceNo=".$module_params["param03"].join("-",$secure_oid);

list($a,$return)=func_https_request("POST","https://gateway.usight.com:443/postauth.secure",$post);
parse_str($return,$ret);

if($ret["ResponseCode"]==="0")
{
	$bill_output["code"] = 1;
	if($ret["TransactionID"])
		$bill_output["billmes"].= " (TxnID: ".$ret["TransactionID"].")";
	if($ret["AuthCode"])
		$bill_output["billmes"].= " (AuthCode: ".$ret["AuthCode"].")";

}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $ret["ResponseMessage"];
}


if($ret["AVSResponse"])
	$bill_output["avsmes"].= (empty($avserr[$ret["AVSResponse"]]) ? "Code: ".$ret["AVSResponse"] : $avserr[$ret["AVSResponse"]]);
if($ret["CVV2Response"])
	$bill_output["cvvmes"].= (empty($cvverr[$ret["CVV2Response"]]) ? "Code: ".$ret["CVV2Response"] : $cvverr[$ret["CVV2Response"]]);

#print_r($bill_output);
#print_r($ret);
#exit;

?>
