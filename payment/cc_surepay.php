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
# $Id: cc_surepay.php,v 1.16.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$surepay_login = $module_params["param01"];
$surepay_pass = $module_params["param02"];
$surepay_server = $module_params["testmode"]!="N" ? "test." : "";
$surepay_ordr = $module_params["param03"].join("-",$secure_oid);
$surepay_curr = "USD";
$surepay_date = date("m/d/Y");

foreach($products as $product)
{
	$addon.= "<pp.lineitem sku=\"".(empty($product["productcode"]) ? "id".$product["productid"] : $product["productcode"])."\" quantity=\"".$product["amount"]."\" unitprice=\"".$product["price"].$surepay_curr."\" description=\"".$product["product"]."\" taxrate=\"0\"/>";
	$diff+=$product["amount"]*$product["price"];
}

if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
foreach ($cart["giftcerts"] as $tmp_gc) {
	$addon.= "<pp.lineitem sku=\"GC".$tmp_gc["amount"]."\" quantity=\"1\" unitprice=\"".$tmp_gc["amount"].$surepay_curr."\" description=\"GIFT CERTIFICATE\" taxrate=\"0\"/>";
	$diff+=$tmp_gc["amount"];
}

#for testing:APPROVAL
#$diff = 90;
#$cart["total_cost"]=92;
#$addon = "<pp.lineitem sku=\"123\" quantity=\"3\" unitprice=\"30.00USD\" description=\"sdg was here\" taxrate=\"0\"/>";
#$userinfo["card_number"] = "4012000033330026";
#$userinfo["card_expire"] = "1205";
#$userinfo["card_cvv2"] = "999";

$diff = sprintf("%.2f",$cart["total_cost"] - $diff);

$post = array();
$post[] = "<!DOCTYPE pp.request PUBLIC \"-//IMALL//DTD PUREPAYMENTS 1.0//EN\" \"http://www.purepayments.com/dtd/purepayments.dtd\">";
$post[] = "<pp.request merchant=\"".$surepay_login."\" password=\"".$surepay_pass."\">";
$post[] = "<pp.auth ordernumber=\"".array_pop($secure_oid)."\" ipaddress=\"".$REMOTE_ADDR."\" ponumber=\"".$surepay_ordr."\" ecommerce=\"true\" ecommercecode=\"05\" shippingcost=\"".$diff.$surepay_curr."\">";
$post[] = "<pp.creditcard number=\"".$userinfo["card_number"]."\" expiration=\"".substr($userinfo["card_expire"],0,2)."/".substr($userinfo["card_expire"],2,2)."\" cvv2=\"".$userinfo["card_cvv2"]."\" cvv2status=\"1\">";
$post[] = "<pp.address type=\"billing\" state=\"".$userinfo["b_state"]."\" city=\"".$userinfo["b_city"]."\" phone=\"".$userinfo["phone"]."\" zip=\"".$userinfo["b_zipcode"]."\" country=\"".$userinfo["b_country"]."\" firstname=\"".$bill_firstname."\" lastname=\"".$bill_lastname."\" address1=\"".$userinfo["b_address"]."\" email=\"".$userinfo["email"]."\"/>";
$post[] = "</pp.creditcard>";
$post[] = "<pp.address type=\"shipping\" state=\"".$userinfo["s_state"]."\" city=\"".$userinfo["s_city"]."\" zip=\"".$userinfo["s_zipcode"]."\" country=\"".$userinfo["s_country"]."\" address1=\"".$userinfo["s_address"]."\" fullname=\"".$userinfo['s_firstname']." ".$userinfo['s_lastname']."\"/>";

$post[] = $addon;

$post[] = "<pp.ordertext type=\"description\">Order #".$surepay_ordr."</pp.ordertext>";
$post[] = "</pp.auth>";
$post[] = "</pp.request>";

$pst = "xml=".join("",$post);

list($a,$return) = func_https_request("POST","https://xml.".$surepay_server."surepay.com:443/",array($pst));

#<pp.response>
#<pp.authresponse merchant="1001" ordernumber="2" authstatus="DCL" transactionid="CC_FU2FFEACA27C" failure="true">Card declined</pp.authresponse>
#<pp.authresponse merchant="1001" ordernumber="560" transactionid="CC_FU2FFEACA294" authcode="OK6596" avs="YYY" cvv2result="M" authstatus="AUTH"/>
#</pp.response>

$cvverr = array(
	"M" => "Match",
	"N" => "No match",
	"P" => "Not processed",
	"S" => "Should be on the card, but is not supplied",
	"U" => "The user is not certified or has not been provided an encryption key"
);

$avserr = array(
	"YYY" => "Address & five-digit ZIP match",
	"YYA" => "Address & five-digit ZIP match",
	"NYZ" => "Five-digit ZIP match only",
	"YNA" => "Address match only",
	"YNY" => "Address match only",
	"NNN" => "Neither Address nor ZIP match",
	"YY"  => "Address and nine-digit ZIP match",
	"YYX" => "Address and nine-digit ZIP match",
	"NY"  => "Nine-digit ZIP match only",
	"NYW" => "Nine-digit ZIP match only",
	"XXW" => "Card number not on file",
	"XXU" => "Address Verification Unavailable",
	"XXR" => "Retry/System unavailable",
	"XXS" => "Service Not Supported",
	"XXE" => "Address Verification not allowed for card type",
	"XX" => "Address Verification requested/ Not received"
);

preg_match("/<pp.authresponse.*authstatus=\"(.*)\"/U",$return,$out);$resp = $out[1];
preg_match("/<pp.authresponse.*transactionid=\"(.*)\"/U",$return,$out);$trid = $out[1];

if($resp == "AUTH")
{
	$bill_output["code"] = 1;
	preg_match("/<pp.authresponse.*authcode=\"(.*)\"/U",$return,$out);
	if($out[1])$bill_output["billmes"] = " (AuthCode: ".$out[1].")";

	preg_match("/<pp.authresponse.*cvv2result=\"(.*)\"/U",$return,$out);
	if($out[1])$bill_output["cvvmes"] = (empty($cvverr[$out[1]]) ? "CVV Code: ".$out[1] : $cvverr[$out[1]]);

	preg_match("/<pp.authresponse.*avs=\"(.*)\"/U",$return,$out);
	if($out[1])$bill_output["avsmes"] = (empty($avserr[$out[1]]) ? "AVS Code: ".$out[1] : $avserr[$out[1]]);
}
else
{
	$bill_output["code"] = 2;
	preg_match("/<pp.authresponse.*>(.*)<\/pp.authresponse>/U",$return,$out);
	$bill_output["billmes"] = $out[1];
	if($trid)$bill_output["billmes"].= " (TransactionID: ".$trid.")";
}

#print_r($bill_output);
#exit;

?>
