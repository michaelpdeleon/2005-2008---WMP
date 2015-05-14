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
# $Id: cc_goem.php,v 1.6.2.1 2006/05/18 07:46:56 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$avserr = array(
    "A" => "Address matches - Zip Code does not ",
    "B" => "Street address match, Postal code in wrong format. (international issuer) ",
    "C" => "Street address and postal code in wrong formats ",
    "D" => "Street address and postal code match (international issuer) ",
    "E" => "AVS Error ",
    "G" => "Service not supported by non-US issuer ",
    "I" => "Address information not verified by international issuer. ",
    "M" => "Street Address and Postal code match (international issuer) ",
    "N" => "No match on address or Zip Code ",
    "O" => "No Response sent ",
    "P" => "Postal codes match, Street address not verified due to incompatible formats. ",
    "R" => "Retry - system is unavailable or timed out ",
    "S" => "Service not supported by issuer ",
    "U" => "Address information is unavailable ",
    "W" => "9-digit Zip Code matches - address does not ",
    "X" => "Exact match ",
    "Y" => "Address and 5-digit Zip Code match ",
    "Z" => "5-digit zip matches - address does not ",
    "0" => "No Response sent "
);


$pp_merch = $module_params["param01"];
$pp_passwd= $module_params["param02"];

if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="Visa"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MasterCard"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="Amex"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="Amex"; # AmericanExpress
if($first4==6011)$userinfo["card_type"]="Discover"; # Discover

$post = array();
$post[] = "merchant=".$pp_merch;
$post[] = "password=".$pp_passwd;
$post[] = "operation_type=auth";
$post[] = "orderid=".$module_params["param03"].join("-",$secure_oid);
$post[] = "total=".$cart["total_cost"];
$post[] = "cardname=".$userinfo["card_name"];
$post[] = "cardnum1=".substr($userinfo["card_number"],0,4);
$post[] = "cardnum2=".substr($userinfo["card_number"],4,4);
$post[] = "cardnum3=".substr($userinfo["card_number"],8,4);
$post[] = "cardnum4=".substr($userinfo["card_number"],12);
$post[] = "cardexpm=".substr($userinfo["card_expire"],0,2);
$post[] = "cardexpy=".substr($userinfo["card_expire"],2,2);
$post[] = "CVV2=".$userinfo["card_cvv2"];
$post[] = "nameoncard=".$userinfo["card_name"];
$post[] = "cardstreet=".$userinfo["b_address"];
$post[] = "cardcity=".$userinfo["b_city"];
$post[] = "cardstate=".$userinfo["b_state"];
$post[] = "cardzip=".$userinfo["b_zipcode"];
$post[] = "cardcountry=".$userinfo["b_country"];

list($a,$return)=func_https_request("POST","https://www.goemerchant7.com:443/cgi-bin/gateway/gateway.cgi",$post,"&","","application/x-www-form-urlencoded",$http_location."/payment/payment_cc.php");
$ret = split("\|",$return);
# 0             1                  2                      3        4
# Success Value|Authorization Code|Authorization Response|AVS Code|Order ID

$bill_output["code"] = ($ret[0]=="1") ? 1 : 2;
$bill_output["billmes"] = $ret[2];

if($ret[1])
	$bill_output["billmes"].= " (AuthCode: ".$ret[1].")";

if($ret[3])
	$bill_output["avsmes"] = ($avserr[$ret[3]] ? $avserr[$ret[3]] : "AVSCode: ".$ret[3]);

#print_r($bill_output);
#print_r($return);
#exit;

?>
