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
# $Id: cc_efs.php,v 1.16.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_id = $module_params["param01"];
$pp_key = $module_params["param02"];
$host = $module_params["testmode"]=="Y"?"test":"";

$prm="";
$prm[]="Method=CreditCardCharge";
$prm[]="StoreID=".$pp_id;
$prm[]="StoreKey=".$pp_key;
$prm[]="ApplicationID=X-Cart EFSNet payment module";
$prm[]="TransactionAmount=".$cart["total_cost"];
$prm[]="AccountNumber=".$userinfo["card_number"];
$prm[]="ReferenceNumber=".$module_params["param04"].join("-",$secure_oid);
$prm[]="ExpirationMonth=".substr($userinfo["card_expire"],0,2);
$prm[]="ExpirationYear=".substr($userinfo["card_expire"],2,2);
$prm[]="CardVerificationValue=".$userinfo["card_cvv2"];
#$prm[]="Currency=".$module_params["param05"];
$prm[]="BillingName=".addslashes($bill_name);
$prm[]="BillingAddress=".$userinfo["b_address"];
$prm[]="BillingCity=".$userinfo["b_city"];
$prm[]="BillingState=".$userinfo["b_state"];
$prm[]="BillingPostalCode=".$userinfo["b_zipcode"];
$prm[]="BillingCountry=".$userinfo["b_country"];
$prm[]="BillingPhone=".$useringo["phone"];
$prm[]="BillingEmail=".$userinfo["email"];
$prm[]="ShippingAddress=".$userinfo["s_address"];
$prm[]="ShippingCity=".$userinfo["s_city"];
$prm[]="ShippingState=".$userinfo["s_state"];
$prm[]="ShippingPostalCode=".$userinfo["s_zipcode"];
$prm[]="ShippingCountry=".$userinfo["s_country"];

list($a,$return)=func_https_request("POST","https://".$host."efsnet.concordebiz.com:443/efsnet.dll",$prm);$return=$return."&";

#ResponseCode=1032&ResultCode=999&ResultMessage=INVALID+STOREKEY&TransactionID=&AVSResponseCode=Z&CVVResponseCode=N&ApprovalNumber=&AuthorizationNumber=&TransactionDate=&TransactionTime=&ReferenceNumber=&AccountNumber=&TransactionAmount=&
# ResponseCode=1025&ResultCode=999&ResultMessage=INVALID+REQUEST&
preg_match("/ResponseCode=(.*)&/U",$return,$out);

if($out[1]=="0")
{
	$bill_output["code"] = 1;
	preg_match("/ApprovalNumber=(.*)&/U",$return,$out);
	$bill_output["billmes"] = "(ApprovalNumber=".$out[1].")";
}
else
{
$err = array(
	"1" => "Transaction previously approved",
	"128" => "Authentication required",
	"256" => "Transaction declined",
	"257" => "Insufficient funds",
	"258" => "Invalid card number, MICR numberor routing number",
	"259" => "Card expired",
	"260" => "Contact financial institution",
	"261" => "Authentication failed",
	"262" => "Unable to authenticate",
	"1023" => "Generic processor error",
	"1024" => "General error",
	"1026" => "Communications error, try again",
	"1027" => "Communications failure",
	"1028" => "Duplicate ReferenceNumber",
	"1029" => "Invalid merchant",
	"1030" => "Invalid request",
	"1031" => "Invalid StoreID",
	"1032" => "Invalid StoreKey",
	"1033" => "Invalid processor",
	"1034" => "Invalid transaction",
	"1035" => "Transaction not permitted",
	"1036" => "A query is already in progress",
	"8191" => "Unknown error"
);

	$bill_output["code"] = 2;
	$b = "[".$out[1]; if($err[$out[1]]) $b.= ": ".$err[$out[1]]; $b.= "] ";
	preg_match("/ResultMessage=(.*)&/U",$return,$out);
	$bill_output["billmes"].= urldecode($out[1]);
	$bill_output["billmes"].= $b;
}

if(preg_match("/TransactionID=(.*)&/U",$return,$out))
	$bill_output["billmes"].= " (TransactionID=".($out[1] ? $out[1] : "Empty").")";

if(preg_match("/AVSResponseCode=(.*)&/U",$return,$out))
{	$avserr = array(
	"A" => "Address matches, ZIP code does not match",
	"E" => "Ineligible transaction OR message contains content error",
	"N" => "Neither address nor ZIP code matches",
	"R" => "System unavailable or timed out. Unable to process",
	"S" => "Card issuer does not support address verification system",
	"U" => "Address information is unavailable",
	"W" => "9-digit ZIP code matches, address does not match",
	"X" => "Address and 9-digit ZIP code match",
	"Y" => "Address and 5-digit ZIP code match",
	"Z" => "5-digit ZIP code matches, address does not"
	);
	$bill_output["avsmes"] = (empty($avserr[$out[1]]) ? "AVS Code: ".$out[1] : $avserr[$out[1]]);
}
else $bill_output["avsmes"] = "Not present";

if(preg_match("/CVVResponseCode=(.*)&/U",$return,$out))
{	$cvverr = array(
	"M" => "CardVerificationValue matches",
	"N" => "CardVerificationValue does not match or is invalid",
	"P" => "CardVerificationValue not processed",
	"U" => "Issuer not registered"
	);
	$bill_output["cvvmes"].= (empty($cvverr[$out[1]]) ? "CVV Code: ".$out[1] : $cvverr[$out[1]]);
}
else $bill_output["cvvmes"].= "Not present";

?>
