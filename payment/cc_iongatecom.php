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
# $Id: cc_iongatecom.php,v 1.13 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$iongatecom_login = $module_params["param01"];
$iongatecom_prefix = $module_params["param02"];

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="VISA"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MASTERCARD"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="AMEX"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="AMEX"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="DINERS"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="DINERS"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="DINERS"; # Diners
if($first4==6011)$userinfo["card_type"]="DISCOVER"; # Discover
if($first4>=3528 && $first4<=3589)$userinfo["card_type"]="JCB"; # JCB

$post = "";
$post[] = "LOGIN=".$iongatecom_login;
$post[] = "AMOUNT=".$cart["total_cost"];
$post[] = "CARDTYPE=".$userinfo["card_type"];
$post[] = "CARDNUM=".$userinfo["card_number"];
$post[] = "EXPIRES=".$userinfo["card_expire"];
$post[] = "CVV2NUM=".$userinfo["card_cvv2"];
$post[] = "CVVIND=1";
$post[] = "CARDNAME=".$userinfo["card_name"];
$post[] = "ADDRESS=".$userinfo["b_address"];
$post[] = "CITY=".$userinfo["b_city"];
$post[] = "STATE=".$userinfo["b_state"];
$post[] = "ZIP=".$userinfo["b_zipcode"];
$post[] = "COUNTRY=".$userinfo["b_country"];
$post[] = "PHONE=".$userinfo["phone"];
$post[] = "EMAIL=".$userinfo["email"];
$post[] = "INVOICENO=".$iongatecom_prefix.join("-",$secure_oid);
$post[] = "RECEIPTURL=DISPLAY";

list($a,$return) = func_https_request("POST","https://secure.iongate.com:443/iongate.asp",$post);

$cvverr = array(
	"M" => "the CVV value entered on your order form matched the CVV on the back of the card",
	"N" => "the CVV entered DID NOT match",
	"P" => "the CVV was not processed",
	"S" => "the issuer (bank) indicates that there should be a CVV, but merchant or customer has indicated no value on card",
	"U" => "issuer has not be certified for CVV (very rare)"
);

$avserr = array(
	"Y" => "Exact Match",
	"N" => "No Match",
	"A" => "Address Match Only",
	"Z" => "Zip Code Match Only",
	"S" => "Service Not Supported",
	"U" => "Address Information Not Available",
	"X" => "Address Information Not Available",
	"R" => "Retry - System Unavailable or Timed Out",
	"W" => "Zip Code OK",
	"E" => "Error Response for Merchant Service Category Code",
	"G" => "Country of customer not supported by AVS"
);

$ret1 = preg_replace("/<br />/i","###",$return);
$ret2 = split("###",$ret1);

foreach($ret2 as $v) {list($a,$b)=split("=",$v);if(!empty($a))$resp[$a] = trim($b); }

#print_r($resp);

if($resp["RESPONSECODE"]=="AA" && $resp["REPLYCODE"]=="000")
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = " (Approval Code: ".$resp["APPROVALCODE"].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = $resp["AUTHRESPONSE"]." (RespCode: ".$resp["RESPONSECODE"]."; ReplCode: ".$resp["REPLYCODE"].")";
}

if(!empty($resp["AVSRESPONSE"]))
	$bill_output["avsmes"] = empty($avserr[$resp["AVSRESPONSE"]]) ? "AVS Code: ".$resp["AVSRESPONSE"] : $avserr[$resp["AVSRESPONSE"]];

if(!empty($resp["CVV2RESPONSE"]))
	$bill_output["cvvmes"] = empty($cvverr[$resp["CVV2RESPONSE"]]) ? "CVV Code: ".$resp["CVV2RESPONSE"] : $cvverr[$resp["CVV2RESPONSE"]];


#print_r($bill_output);
#exit;

?>
