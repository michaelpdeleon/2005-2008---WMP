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
# $Id: cc_fire.php,v 1.7 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$pp_merch = $module_params["param01"];
$pp_account =  $module_params["param02"];
$pp_passwd =  $module_params["param03"];

$server = ($module_params["testmode"]=="Y") ? "https://realtime.test.firepay.com:443/servlet/DPServlet" : "https://realtime.firepay.com:443/servlet/DPServlet";

#FP = FirePay
#SO = Solo
#SW = Switch

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="VI"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MC"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="AM"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="AM"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="DI"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="DI"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="DI"; # Diners
if($first4==6011)$userinfo["card_type"]="DI"; # Discover

$post = "";
$post[] = "account=".$pp_account;
$post[] = "merchantId=".$pp_merch;
$post[] = "merchantPwd=".$pp_passwd;
$post[] = "clientVersion=1.1";
$post[] = "operation=P";
$post[] = "cardType=".$userinfo["card_type"];
$post[] = "cardNumber=".$userinfo["card_number"];
$post[] = "cardExp=".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2);
$post[] = "amount=".(100*$cart["total_cost"]);
$post[] = "merchantTxn=".$module_params["param04"].join("-",$secure_oid);
$post[] = "cvdIndicator=1";
$post[] = "cvdValue=".$userinfo["card_cvv2"];
$post[] = "custName1=".$userinfo["card_name"];
$post[] = "streetAddr=".$userinfo["b_address"];
$post[] = "city=".$userinfo["b_city"];
$post[] = "province=".$userinfo["b_state"];
$post[] = "zip=".$userinfo["b_zipcode"];
$post[] = "country=".$userinfo["b_country"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "email=".$userinfo["email"];

list($a,$return)=func_https_request("POST",$server,$post);
parse_str($return,$ret);

if($ret["status"]=="SP")
{
	$bill_output["code"] = 1;
	if($ret["txnNumber"])
		$bill_output["billmes"].= " (TxnID: ".$ret["txnNumber"].")";
	if($ret["settleNumber"])
		$bill_output["billmes"].= " (SettleNumber: ".$ret["settleNumber"].")";

	if($ret["authCode"])
		$bill_output["billmes"].= " (AuthCode: ".$ret["authCode"].")";

}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $ret["errString"].$ret["subErrorString"]." (Code: ".$ret["errCode"].")";
}


$avserr = array(
	"X" => "Exact. Nine-digit zip code and address match.",
	"Y" => "Yes. Five-digit zip code and address match.",
	"A" => "Address matches, but zip code does not.",
	"W" => "Nine-digit zip code matches, but address does not.",
	"Z" => "Five-digit zip code matches, but address does not.",
	"N" => "No part of the address matches.",
	"U" => "Address information is unavailable.",
	"R" => "Retry. System unable to process.",
	"S" => "AVS not supported.",
	"E" => "AVS not supported for this industry.",
	"B" => "AVS not performed.",
	"Q" => "Unknown response from issuer/banknet switch."
);

$cvverr = array(
	"M" => "The CVD value provided matches the CVD value associated with the card.",
	"N" => "The CVD value provided does not match the CVD value associated with the card.",
	"P" => "The CVD value was not processed.",
	"S" => "Merchant indicated that CVV2 was not present on card.",
	"U" => "Issuer is not certified and/or has not provided Visa encryption keys."
);

if($ret["avsInfo"])
{
	$ret["avs"] = $ret["avsInfo"][0];
	$bill_output["avsmes"] = (empty($avserr[$ret["avs"]]) ? "Code: ".$ret["avs"] : $avserr[$ret["avs"]])." + ".$ret["avsInfo"];	
}
if($ret["cvdInfo"])
	$bill_output["cvvmes"].= (empty($cvverr[$ret["cvdInfo"]]) ? "Code: ".$ret["cvdInfo"] : $cvverr[$ret["cvdInfo"]]);

#print_r($bill_output);
#print_r($ret);
#exit;

?>
