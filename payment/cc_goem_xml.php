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
# $Id: cc_goem_xml.php,v 1.6 2006/01/11 06:56:22 mclap Exp $
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

$cvverr = array(
	"M" => "CVV2 Match ",
	"N" => "CVV2 No Match ",
	"P" => "Not Processed ",
	"S" => "Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated that the CVV2 data is not present on the card ",
	"U" => "Issuer has not certified for CVV2 or Issuer has not provided Visa with theCVV2 encryption Keys "
);

$pp_merch = $module_params["param01"]; #must be a 4 digits number
$pp_passwd= $module_params["param02"];

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="Visa"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="MasterCard"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="Amex"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="Amex"; # AmericanExpress
if($first4==6011)$userinfo["card_type"]="Discover"; # Discover

$post = array();
$post[] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$post[] = "<TRANSACTION><FIELDS>";
$post[] = "<FIELD KEY=\"merchant\">".$pp_merch."</FIELD>";
$post[] = "<FIELD KEY=\"password\">".$pp_passwd."</FIELD>";
$post[] = "<FIELD KEY=\"operation_type\">auth</FIELD>";
$post[] = "<FIELD KEY=\"order_id\">".$module_params["param03"].join("-",$secure_oid)."</FIELD>";
$post[] = "<FIELD KEY=\"total\">".$cart["total_cost"]."</FIELD>";
$post[] = "<FIELD KEY=\"card_name\">".$userinfo["card_type"]."</FIELD>";
$post[] = "<FIELD KEY=\"card_number\">".$userinfo["card_number"]."</FIELD>";
$post[] = "<FIELD KEY=\"card_exp\">".$userinfo["card_expire"]."</FIELD>";
$post[] = "<FIELD KEY=\"cvv2\">".$userinfo["card_cvv2"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_name\">".$userinfo["card_name"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_street\">".$userinfo["b_address"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_city\">".$userinfo["b_city"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_state\">".$userinfo["b_state"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_zip\">".$userinfo["b_zipcode"]."</FIELD>";
$post[] = "<FIELD KEY=\"owner_country\">".$userinfo["b_country"]."</FIELD>";
$post[] = "<FIELD KEY=\"recurring\">0</FIELD>";
$post[] = "<FIELD KEY=\"recurring_type\">Null</FIELD>";
$post[] = "</FIELDS></TRANSACTION>";

list($a,$return)=func_https_request("POST","https://www.goemerchant4.com:443/trans_center/gateway/xmlgateway.cgi",$post,"","","text/xml",$http_location."/payment/payment_cc.php");

preg_match("/<FIELD KEY=[^\w]*status[^\w]*>(.+)<\/FIELD>/i",$return,$sts);

if($sts[1]==1)
	$bill_output["code"] = 1;
else
{
	preg_match("/<FIELD KEY=[^\w]*error[^\w]*>(.+)<\/FIELD>/i",$return,$out);
	$bill_output["code"] = 2;
	$bill_output["billmes"] = ($sts==2 ? "Declined" : "Error").": ".$out[1];
}

if(preg_match("/<FIELD KEY=[^\w]*auth_response[^\w]*>(.+)<\/FIELD>/i",$return,$out))
	$bill_output["billmes"].= $out[1]." ";

if(preg_match("/<FIELD KEY=[^\w]*auth_code[^\w]*>(.+)<\/FIELD>/i",$return,$out))
	$bill_output["billmes"].= " (Auth code: ".$out[1].")";

if(preg_match("/<FIELD KEY=[^\w]*reference_number[^\w]*>(.+)<\/FIELD>/i",$return,$out))
	$bill_output["billmes"].= " (RefNumber: ".$out[1].")";

if(preg_match("/<FIELD KEY=[^\w]*avs_code[^\w]*>(.+)<\/FIELD>/i",$return,$out))
	$bill_output["avsmes"] = ($avserr[$out[1]] ? $avserr[$out[1]] : "AVSCode: ".$out[1]);

if(preg_match("/<FIELD KEY=[^\w]*cvv2_code[^\w]*>(.+)<\/FIELD>/i",$return,$out))
	$bill_output["cvvmes"] = ($cvverr[$out[1]] ? $cvverr[$out[1]] : "CVVCode: ".$out[1]);

#print_r($bill_output);
#print_r($return);
#exit;

?>
