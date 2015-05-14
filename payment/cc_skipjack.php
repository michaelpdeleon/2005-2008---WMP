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
# $Id: cc_skipjack.php,v 1.23.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$staerr = array(
	"-35" => "Error invalid credit card number",
	"-37" => "Error failed communication",
	"-39" => "Error length serial number",
	"-51" => "Error length zip code",
	"-52" => "Error length shipto zip code",
	"-53" => "Error length expiration date",
	"-54" => "Error length account number date",
	"-55" => "Error length street address",
	"-56" => "Error length shipto street address",
	"-57" => "Error length transaction amount",
	"-58" => "Error length name",
	"-59" => "Error length location",
	"-60" => "Error length state",
	"-61" => "Error length shipto state",
	"-62" => "Error length order string",
	"-64" => "Error invalid phone number",
	"-65" => "Error empty name",
	"-66" => "Error empty email",
	"-67" => "Error empty street address",
	"-68" => "Error empty city",
	"-69" => "Error empty state",
	"-79" => "Error length customer name",
	"-80" => "Error length shipto customer name",
	"-81" => "Error length customer location",
	"-82" => "Error length customer state",
	"-83" => "Error length shipto phone",
	"-84" => "Pos error duplicate ordernumber",
	"-91" => "Pos_error_CVV2",
	"-92" => "Pos_error_Error_Approval_Code",
	"-93" => "Pos_error_Blind_Credits_Not_Allowed",
	"-94" => "Pos_error_Blind_Credits_Failed",
	"-95" => "Pos_error_Voice_Authorizations_Not_Allowed "
);

$avserr = array(
 "X" => "Exact match, 9 digit zip",
 "Y" => "Exact match, 5 digit zip",
 "A" => "Address match only",
 "W" => "9 digit match only",
 "Z" => "5 digit match only",
 "N" => "No address or zip match",
 "U" => "Address unavailable",
 "R" => "Issuer system unavailable",
 "E" => "Not a mail/phone order",
 "S" => "Service not supported"
);

$test = ($module_params["testmode"]!="N" ? "developer" : "www");
$ordr = $module_params["param03"].join("-",$secure_oid);

$os = "";
foreach ($products as $product) {
	$os.= substr(preg_replace("/[^\d\w ]/Ss","",$product["productcode"]), 0, 20)."~".substr(preg_replace("/[^\d\w ]/Ss","",$product["product"]), 0, 120)."~".price_format($product["price"])."~".$product["amount"]."~N~||";
}

if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0) {
	foreach ($cart["giftcerts"] as $k => $tmp_gc) {
		$os.= "GC".$k."~GIFT CERTIFICATE~".price_format($tmp_gc["amount"])."~1~N~||";
	}
}

$sj_name = trim($userinfo['card_name']);
if (empty($sj_name))
	$sj_name = "sjname=".$bill_name;

$post = "";
$post[]="sjname=".$sj_name;
$post[]="Email=".$userinfo["email"];
$post[]="Streetaddress=".$userinfo["b_address"];
$post[]="City=".$userinfo["b_city"];
$post[]="State=".$userinfo["b_state"];
$post[]="Zipcode=".$userinfo["b_zipcode"];
$post[]="Ordernumber=".$ordr;
$post[]="Accountnumber=".$userinfo["card_number"];
$post[]="cvv2=".$userinfo["card_cvv2"];
$post[]="Month=".substr($userinfo["card_expire"],0,2);
$post[]="Year=".substr($userinfo["card_expire"],2,2);
$post[]="Serialnumber=".$module_params["param01"];
$post[]="Transactionamount=".$cart["total_cost"];
$post[]="Shiptophone=".$userinfo["phone"];
$post[]="Phone=".$userinfo["phone"];
$post[]="Country=".$userinfo["b_country"];
$post[]="Fax=".$userinfo["fax"];
$post[]="Shiptostreetaddress=".$userinfo["s_address"];
$post[]="Shiptocity=".$userinfo["s_city"];
$post[]="Shiptostate=".$userinfo["s_state"];
$post[]="Shiptozipcode=".$userinfo["s_zipcode"];
$post[]="Shiptocountry=".$userinfo["s_country"];
$post[]="Orderstring=".$os;

list($a,$return) = func_https_request("POST","https://".$test.".skipjackic.com:443/scripts/EvolvCC.dll?AuthorizeAPI",$post);

#"AUTHCODE","szSerialNumber","szTransactionAmount","szAuthorizationDeclinedMessage","szAVSResponseCode","szAVSResponseMessage","szOrderNumber","szAuthorizationResponseCode","szIsApproved","szCVV2ResponseCode","szCVV2ResponseMessage","szReturnCode","szTransactionFileName"
#"VITAL5","000882895356","145","","Y","Card authorized, exact address match with 5 digit zip code.","1","VITAL5","1","","","1","9802851296723.DEV"

list($a,$b) = split("\n",$return);
$a = split("\",\"","\",".$a.",\"");
$b = split("\",\"","\",".$b.",\"");

$res = "";
foreach($a as $i => $j) {
	if($j!="")
		$res[$j] = $b[$i];
}

#[AUTHCODE] => EMPTY
#[szSerialNumber] => 000154051399
#[szTransactionAmount] => 41.95
#[szAuthorizationDeclinedMessage] =>
#[szAVSResponseCode] =>
#[szAVSResponseMessage] =>
#[szOrderNumber] => xcart521
#[szAuthorizationResponseCode] =>
#[szIsApproved] => 0
#[szCVV2ResponseCode] =>
#[szCVV2ResponseMessage] =>
#[szReturnCode] => -66
#[szTransactionFileName] =>

if($res["szIsApproved"]==1)
{
	$bill_output["code"] = 1;
	$bill_output["billmes"] = "(TransactionFileName: ".$res["szTransactionFileName"]."/ AUTHCODE: ".$res["AUTHCODE"].")";
}
else
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = (empty($res["szAuthorizationDeclinedMessage"]) ? $staerr[$res["szReturnCode"]] : $res["szAuthorizationDeclinedMessage"])." (ReturnCode: ".$res["szReturnCode"].")";
}

if(!empty($res["szAVSResponseCode"]) || !empty($res["szAVSResponseMessage"]))
	$bill_output["avsmes"] = (empty($res["szAVSResponseMessage"]) ? $avserr[$res["szAVSResponseCode"]] : $res["szAVSResponseMessage"])." (".$res["szAVSResponseCode"].")";

if(!empty($res["szCVV2ResponseCode"]) || !empty($res["szCVV2ResponseMessage"]))
	$bill_output["cvvmes"].= $res["szCVV2ResponseMessage"]." (".$res["szCVV2ResponseCode"].")";

if(!empty($res["szSerialNumber"]))
	$bill_output["billmes"].= " (SerialNumber: ".$res["szSerialNumber"].")";

?>
