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
# $Id: cc_tt.php,v 1.6 2006/01/11 06:56:23 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(180);

x_load('http');

$staerr = array(
	"A" => "Approval: ",
	"E" => "Error: ",
	"W" => "Wait: ",
	"D" => "Declined: "
);

$avserr = array(
	"A" => "Address (Street) matches, Zip does not",
	"N" => "No Match on Address (Street) or Zip",
	"Y" => "Address (Street) and 5 digit Zip match"
);

$cvverr = array(
	"M" => "CVV2 Match.",
	"N" => "CVV2 No Match.",
	"P" => "Not Processed."
);

$pp_merch = $module_params["param01"];
$pp_pass  = $module_params["param02"];
$pp_curr  = $module_params["param03"];

if(is_visa($userinfo["card_number"]))$userinfo["card_type"]="VISA"; # VISA
if(is_mc($userinfo["card_number"]))$userinfo["card_type"]="MASTERCARD"; # MasterCard
if(is_amex($userinfo["card_number"]))$userinfo["card_type"]="AMEX"; # AmericanExpress
if(is_diners($userinfo["card_number"]))$userinfo["card_type"]="DINERS"; # Diners
if(is_jcb($userinfo["card_number"]))$userinfo["card_type"]="JCB"; # JCB

$post = "";
$post[] = "username=".$pp_merch;
$post[] = "password=".$pp_pass;
$post[] = "transtype=sale";
$post[] = "cardholdername=".$userinfo["card_name"];
$post[] = "cardnumber=".$userinfo["card_number"];
$post[] = "cvv=".$userinfo["card_cvv2"];
$post[] = "cardtype=".$userinfo["card_type"];
$post[] = "expyear=".substr($userinfo["card_expire"],2,2);
$post[] = "expmonth=".substr($userinfo["card_expire"],0,2);
$post[] = "amount=".$cart["total_cost"];
$post[] = "currency=".$pp_curr;
$post[] = "address=".$userinfo["b_address"];
$post[] = "city=".$userinfo["b_city"];
$post[] = "state=".$userinfo["b_state"];
$post[] = "country=".$userinfo["b_country"];
$post[] = "zip=".$userinfo["b_zipcode"];
$post[] = "phone=".$userinfo["phone"];
$post[] = "email=".$userinfo["email"];
$post[] = "reference=".$module_params["param04"].join("-",$secure_oid);
$post[] = "ipaddress=".$REMOTE_ADDR;

list($a,$return)=func_https_request("POST","https://secure.totaltrans.net:443/tt/transaction.php",$post);
# Result1, ResponseCode2, ResponseText3, TransID4, CVV5, AVS6, PCS7
#"A","000","Approved","3241515"

$ret = split("\",\"","\",".$return.",\"");

$bill_output["code"] = (($ret[1]=="A") ? 1 : (($ret[1]=="W" ? 3 : 2)));
$bill_output["billmes"].= $staerr[$ret[1]].$ret[3];

if($ret[4])
	$bill_output["billmes"].= " (TxnID: ".$ret[4].")";
if($ret[2])
	$bill_output["billmes"].= " (ResponseCode: ".$ret[2].")";

if($ret[6])
	$bill_output["avsmes"].= (empty($avserr[$ret[6]]) ? "Code: ".$ret[6] : $avserr[$ret[6]]);
if($ret[5])
	$bill_output["cvvmes"].= (empty($cvverr[$ret[5]]) ? "Code: ".$ret[5] : $cvverr[$ret[5]]);
if($ret[7])
	$bill_output["billmes"].= " (Profile Check Scores: ".$ret[7].")";

?>
