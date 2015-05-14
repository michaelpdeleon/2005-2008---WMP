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
# $Id: cc_csrc.php,v 1.17.2.1 2006/06/15 10:10:49 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

$vs_mid = $module_params["param01"];
$vs_path = $module_params["param02"];
$vs_host = $module_params["param03"];
$vs_port = $module_params["param04"];
$vs_curr = $module_params["param05"];
$vs_prx = $module_params["param06"];

if (file_exists($xcart_dir."/payment/ics/bin/ics")) {
	$ics_cmd = func_shellquote($xcart_dir."/payment/ics/bin/ics");
}
else {
	$perl = func_find_executable("perl",$config["General"]["perl_binary"]);
	if (empty($perl)) {
		func_header_location($current_location.DIR_CUSTOMER."/error_message.php?error_ccprocessor_notfound");
	}
	$ics_cmd = func_shellquote($perl)." ".func_shellquote($xcart_dir."/payment/csrc.pl")." process ";
}

$avserr = array(
	"A" => "Street address matches, but both 5-digit ZIP code and 9-digit ZIP code do not match. ",
	"B" => "Street address matches, but postal code not verified. Returned only for non-U.S.-issued Visa cards. ",
	"C" => "Street address and postal code not verified. Returned only for non-U.S.-issued Visa cards. ",
	"D" => "Street address and postal code both match. Returned only for non-U.S.-issued Visa cards. ",
	"E" => "AVS data is invalid. ",
	"G" => "Non-U.S. issuing bank does not support AVS. ",
	"I" => "Address information not verified. Returned only for non-U.S.-issued Visa cards. ",
	"J" => "Card member name, billing address, and postal code all match. Ship-to information verified and chargeback protection guaranteed through the Fraud Protection Program. This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor. ",
	"K" => "Card member's name matches. Both billing address and billing postal code do not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
	"L" => "Card member's name matches. Billing postal code matches, but billing address does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
	"M" => "Street address and postal code both match. Returned only for non-U.S.-issued Visa cards. ",
	"N" => "Street address, 5-digit ZIP code, and 9-digit ZIP code all do not match. ",
	"O" => "Card member name matches. Billing address matches, but billing postal code does not match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
	"P" => "Postal code matches, but street address not verified. Returned only for non-U.S.-issued Visa cards. ",
	"Q" => "Card member name, billing address, and postal code all match. Ship-to information verified but chargeback protection not guaranteed (Standard program). This code is returned only if you are signed up to use AAV+ with the American Express Phoenix processor. ",
	"R" => "System unavailable. ",
	"S" => "U.S. issuing bank does not support AVS. ",
	"U" => "Address information unavailable. Returned if non-U.S. AVS is not available or if the AVS in a U.S. bank is not functioning properly. ",
	"V" => "Card member name matches. Both billing address and billing postal code match. This code is returned only if you are signed up to use Enhanced AVS or AAV+ with the American Express Phoenix processor. ",
	"W" => "Street address does not match, but 9-digit ZIP code matches. ",
	"X" => "Exact match. Street address and 9-digit ZIP code both match. ",
	"Y" => "Street address and 5-digit ZIP code both match. ",
	"Z" => "Street address does not match, but 5-digit ZIP code matches. ",
	"1" => "CyberSource AVS code. AVS is not supported for this processor or card type. ",
	"2" => "CyberSource AVS code. The processor returned an unrecognized value for the AVS response. "
);

$cvverr = array(
	"I" => "Card verification number failed processor's data validation check.",
	"M" => "Card verification number matched. ",
	"N" => "Card verification number not matched.",
	"P" => "Card verification number not processed. ",
	"S" => "Card verification number is on the card but was not included in the request. ",
	"U" => "Card verification is not supported by the issuing bank. ",
	"X" => "Card verification is not supported by the card association.",
	" " => "Deprecated. Ignore this value. ",
	"1" => "CyberSource does not support card verification for this processor or card type. ",
	"2" => "The processor returned an unrecognized value for the card verification response.",
	"3" => "The processor did not return a card verification result code."
);

$factor = array(
	"F"=>"Hotlist match. The credit card number, street address, email address, or IP address for this order appears on the hotlist.",
	"G"=>"The customer's geolocation data (phone number) and other factors do not correlate.",
	"N"=>"Nonsensical input in the customer name or address fields.",
	"O"=>"Obscenities in the order form.",
	"P"=>"The bank processor declined the credit card.",
	"U"=>"Unverifiable billing or shipping address.",
	"W"=>"Warning for partial match of address to hotlist."
);

$post = "";
$post[] = "ics_path=".$vs_path;
$post[] = "server_host=".$vs_host;
$post[] = "server_port=".$vs_port;
$post[] = "ics_applications=ics_auth,ics_bill";
$post[] = "merchant_id=".$vs_mid;
$post[] = "customer_firstname=".$bill_firstname;
$post[] = "customer_lastname=".$bill_lastname;
$post[] = "customer_email=".$userinfo["email"];
$post[] = "customer_phone=".$userinfo["phone"];
$post[] = "bill_address1=".$userinfo["b_address"];
$post[] = "bill_city=".$userinfo["b_city"];
$post[] = "bill_state=".$userinfo["b_state"];
$post[] = "bill_zip=".$userinfo["b_zipcode"];
$post[] = "bill_country=".$userinfo["b_country"];
$post[] = "customer_cc_number=".$userinfo["card_number"];
$post[] = "customer_cc_expmo=".substr($userinfo["card_expire"],0,2);
$post[] = "customer_cc_expyr=".(2000+substr($userinfo["card_expire"],2,2));
$post[] = "merchant_ref_number=".$vs_prx.join("-",$secure_oid);
$post[] = "currency=".$vs_curr;

$post[] = "avs_level=Standard"; #Enhanced/AAVPlus
$post[] = "customer_cc_cv_indicator=1"; #0: CV number service not requested.  1: CV number service requested and supported.  2: CV number on credit card is illegible.  9: CV number was not imprinted on credit card.
$post[] = "customer_cc_cv_number=".$userinfo["card_cvv2"];
$post[] = "customer_ipaddress=".$REMOTE_ADDR;
$post[] = "e_commerce_indicator=internet";
$post[] = "ignore_avs=no"; #yes: Ignore the results of AVS checking and run the ics_bill application. no (default): If the authorization receives an AVS decline, do not run the ics_bill application
$post[] = "ignore_bad_cv=no"; #Yes: If the value of auth_cv_result is N, allow ics_bill to run.  No (default): If the value of auth_cv_result is N, return the auth_rflag DCV and do not allow ics_bill to run.


$i=0;
foreach ($products as $product)
	$post[] = "offer".$i."=offerid:".($i++)."^product_name:".strtr($product["product"],"^:\n\r","    ")."^merchant_product_sku:".strtr($product["productcode"],"^:","  ")."^product_code:^amount:".$product['discounted_price']."^quantity:".$product["amount"];

if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
foreach ($cart["giftcerts"] as $tmp_gc)
	$post[] = "offer".$i."=offerid:".($i++)."^product_name:Gift_Certificate^merchant_product_sku:^product_code:^amount:".$tmp_gc["amount"]."^quantity:1";

if($cart["total_cost"]>$cart["discounted_subtotal"])
	$post[] = "offer".$i."=offerid:".($i++)."^product_name:Shipping_etc^merchant_product_sku:^product_code:^amount:".($cart["total_cost"]-$cart["discounted_subtotal"])."^quantity:1";


putenv('ICSPATH='.$vs_path);

# Execute ICS
$tmpfile = func_temp_store('');
$errfile = func_temp_store('');
$execline = $ics_cmd." > ".func_shellquote($tmpfile)." 2>".func_shellquote($errfile);
$fp = popen($execline, "w");
fputs($fp,join("\n",$post));
pclose($fp);
$return = explode("\n", func_temp_read($tmpfile, true));
@unlink($errfile);

if($return)
foreach($return as $v)
{ list($a,$b) = split("=",$v,2); $ret[$a] = trim($b); }

if($ret["ics_rcode"] == "1")
{
	$bill_output["code"] = 1;

	$bill_output["billmes"] = $ret["ics_rmsg"];
	if($ret["auth_auth_code"])
		$bill_output["billmes"].= " (AuthCode: ".$ret["auth_auth_code"].")";
	if($ret["bill_trans_ref_no"])
		$bill_output["billmes"].= " (RefNo: ".$ret["bill_trans_ref_no"].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $ret["ics_rmsg"];
}

if($ret["auth_avs_raw"])$bill_output["avsmes"] = "Auth AVS raw: ".$ret["auth_avs_raw"].($avserr[$ret["auth_auth_avs"]] ? " (".$avserr[$ret["auth_auth_avs"]].")" : "");
if($ret["auth_cv_result_raw"])$bill_output["cvvmes"] = "Auth CVV raw: ".$ret["auth_cv_result_raw"].($cvverr[$ret["auth_cv_result"]] ? " (".$cvverr[$ret["auth_cv_result"]].")" : "");

if($ret["auth_factor_code"])$bill_output["billmes"].= " (".($factor[$ret["auth_factor_code"]] ? "Risk Factor: ".$factor[$ret["auth_factor_code"]] : "RiskFactorCode: ".$ret["auth_factor_code"]).")";

#print "<pre>";
#print $return."<hr />";
#print_r($bill_output);
#print_r($ret);
#exit;


?>
