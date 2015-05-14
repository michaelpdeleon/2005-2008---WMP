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
# $Id: cc_plugnpaycom.php,v 1.18 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$avserr = array(
	"A" => "Address matches, ZIP code does not. ",
	"B" => "Street address match for international transaction; postal code not verified. ",
	"C" => "Street & postal code not verified for international transaction. ",
	"D" => "Street & Postal codes match for international transaction. Both the five-digit postal zip code as well as the first five numerical characters contained in the address match for the international transaction. ",
	"E" => "Transaction is ineligible for address verification. ",
	"F" => "Street address & postal codes match for international transaction. (UK Only) ",
	"G" => "AVS not performed because the international issuer does not support AVS. ",
	"I" => "Address information not verified for international transaction. ",
	"M" => "Street address & postal codes match for international transaction. ",
	"N" => "Neither the ZIP nor the address matches. ",
	"P" => "Postal codes match for international transaction; street address not verified. ",
	"S" => "AVS not supported at this time. ",
	"R" => "Issuer's authorization system is unavailable, try again later. ",
	"U" => "Unable to perform address verification because either address information is unavailable or the Issuer does not support AVS. ",
	"W" => "Nine-digit zip match, address does not. The nine-digit postal zip code matches that stored at the VIC or card issuer's center. However, the first five numerical characters contained in the address do not match. ",
	"X" => "Exact match (nine-digit zip and address). Both the nine-digit postal zip code as well as the first five numerical characters contained in the address match. ",
	"Y" => "Address & 5-digit or 9-digit ZIP match. ",
	"Z" => "Either 5-digit or 9-digit ZIP matches, address does not. ",
	"0" => "Service Not Allowed. Generally associated with credit cards that are either not allowed to be used for any online transactions or are not allowed to be used for a specific classification of company. "
);

$cvverr = array(
	"M" => "Match ",
	"N" => "No Match ",
	"P" => "Not Processed ",
	"X" => "Cannot Verify (also used as a test response by some processors) ",
	"U" => "Unable To Verify ",
	"S" => "Unavailable For Verification "
);


@set_time_limit(100);

$pp_publisher = $module_params["param01"];
$pp_host = $module_params["param03"];
$ordr = $secure_oid[0]; #NUMBER only...
#$ordr = $module_params["param04"].join("-",$secure_oid);

$post = "";
$post[] = "publisher-name=".$pp_publisher;
$post[] = "publisher-email=".$config["Company"]["orders_department"];
$post[] = "authtype=authpostauth";
#$post[] = "dontsndmail=yes";
$post[] = "card-amount=".$cart["total_cost"];
$post[] = "card-name=".$userinfo["card_name"];
$post[] = "card-address1=".$userinfo["b_address"];
$post[] = "card-city=".$userinfo["b_city"];
$post[] = "card-state=".($userinfo["b_country"]=="US" ? $userinfo["b_state"] : "ZZ");
if($userinfo["b_country"]!="US")$post[] = "card-prov=".$userinfo["b_statename"];
$post[] = "card-zip=".$userinfo["b_zipcode"];
$post[] = "card-country=".$userinfo["b_country"];
$post[] = "card-number=".$userinfo["card_number"];
$post[] = "card-exp=".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2);
$post[] = "card-cvv=".$userinfo["card_cvv2"];;
$post[] = "email=".$userinfo["email"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "address1=".$userinfo["s_address"];
$post[] = "city=".$userinfo["s_city"];
$post[] = "state=".($userinfo["s_country"]=="US" ? $userinfo["s_state"] : "ZZ");
if($userinfo["s_country"]!="US")$post[] = "province=".$userinfo["s_statename"];
$post[] = "country=".$userinfo["s_country"];
$post[] = "orderID=".$ordr;
$post[] = "app-level=".$module_params["param05"];


list($a,$return)=func_https_request("POST","https://".$pp_host.":443/payment/pnpremote.cgi",$post);
$return = "&".urldecode($return)."&";

#FinalStatus=badcard
#IPaddress=193.124.127.165
#MStatus=success
#acct_code4=AVS failure.:pnpremote.cgi:193.124.127.165
#app-level=5
#auth-code=TSTAUT
#auth-msg= Sorry, the billing address you entered does not match the address on record for this credit card or your address information is unavailable for verification.
#avs-code=M
#currency=usd
#cvvresp=M
#orderID=2003041511294812737
#resp-code=P01
#sresp=E
#state=sdg
#MErrMsg=Sorry, the billing address you entered does not match the address on record for this credit card or your address information is unavailable for verification.

preg_match("/&FinalStatus=(.*)&/U",$return,$a);$resp = $a[1];

if($resp=="success")
{
	$bill_output["code"] = 1;
	preg_match("/&auth-code=(.*)&/U",$return,$a);
	$bill_output["billmes"] = "(AuthCode: ".$a[1].")";
}
else
{
	$bill_output["code"] = 2;
	preg_match("/&MErrMsg=(.*)&/U",$return,$err);
	preg_match("/&resp-code=(.*)&/U",$return,$cd);
	$bill_output["billmes"] = $err[1]." (".$resp."/".$cd[1].")";
}

preg_match("/&orderID=(.*)&/U",$return,$a);
if(!empty($a[1]))
    $bill_output["billmes"].= " (OrderID: ".$a[1].")";

preg_match("/&cvvresp=(.*)&/U",$return,$a);$cvvresp = $a[1];
if(!empty($cvvresp))
	$bill_output["cvvmes"] = (empty($cvverr[$cvvresp]) ? "CVV Code: ".$cvvresp : $cvverr[$cvvresp]);

preg_match("/&avs-code=(.*)&/U",$return,$a);$avscode = $a[1];
if(!empty($avscode))
	$bill_output["avsmes"] = (empty($avserr[$avscode]) ? "AVS Code: ".$avscode : $avserr[$avscode]);

preg_match("/&IPaddress=(.*)&/U",$return,$a);
if(!empty($a[1]))
	$bill_output["billmes"].= " (IP: ".$a[1].")";

#print_r($secure_oid);
#print_r($bill_output);
#exit;

?>
