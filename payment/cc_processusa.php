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
# $Id: cc_processusa.php,v 1.40.2.1 2006/06/15 10:10:49 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('cart','http');

@set_time_limit(100);

$avserr = array(
	"A" => "Address match. Zip does not match.",
	"E" => "AVS error.",
	"G" => "Global non-participant. AVS not available for international customers.",
	"I" => "International address not verified.",
	"N" => "No match. Neither address nor ZIP match.",
	"O" => "No response to AVS request.",
	"U" => "AVS unavailable.",
	"Y" => "Address and 5-digit ZIP Code match.",
	"Z" => "ZIP Code match. Address does not match."
);

$cvverr = array(
	"M" => "Match",
	"N" => "No Match",
	"P" => "Not Processed",
	"S" => "Issuer indicates CVV2 should be present. Merchant indicates not present",
	"U" => "Issuer has not certified for CVV or has not provided CVV encryption keys"
);


$pp_login = $module_params["param01"];
$pp_pass = $module_params["param02"];
$pp_home = $module_params["param03"];

$post = "";
$post[] = "<?xml version=\"1.0\"?>";
$post[] = "<SaleRequest>";
$post[] = "<CustomerData>";
$post[] = "<Email>".$userinfo["email"]."</Email>";
$post[] = "<BillingAddress>";
$post[] = "<FirstName>".$bill_firstname."</FirstName>";
$post[] = "<LastName>".$bill_lastname."</LastName>";
$post[] = "<Address1>".$userinfo["b_address"]."</Address1>";
$post[] = "<City>".$userinfo["b_city"]."</City>";
$post[] = "<State>".(empty($userinfo["b_state"])?"NONE":$userinfo["b_state"])."</State>";
$post[] = "<Zip>".$userinfo["b_zipcode"]."</Zip>";
$post[] = "<Country>".$userinfo["b_country"]."</Country>";
$post[] = "<Phone>".(empty($userinfo["phone"])?"NONE":$userinfo["phone"])."</Phone>";
$post[] = "</BillingAddress>";
#$post[] = "<ShippingAddress>";
#$post[] = "<Address1>".$userinfo["s_address"]."</Address1>";
#$post[] = "<City>".$userinfo["s_city"]."</City>";
#$post[] = "<State>".$userinfo["s_state"]."</State>";
#$post[] = "<Zip>".$userinfo["s_zipcode"]."</Zip>";
#$post[] = "<Country>".$userinfo["s_country"]."</Country>";
#$post[] = "</ShippingAddress>";
$post[] = "<AccountInfo>";
$post[] = "<CardInfo>";
$post[] = "<CCNum>".$userinfo["card_number"]."</CCNum>";
$post[] = "<CCMo>".substr($userinfo["card_expire"],0,2)."</CCMo>";
$post[] = "<CCYr>".(2000+substr($userinfo["card_expire"],2,2))."</CCYr>";
$post[] = "<CVV2Number>".$userinfo["card_cvv2"]."</CVV2Number>";
$post[] = "</CardInfo>";
$post[] = "</AccountInfo>";
$post[] = "</CustomerData>";
$post[] = "<TransactionData>";
$post[] = "<VendorId>".$pp_login."</VendorId>";
$post[] = "<VendorPassword>".$pp_pass."</VendorPassword>";
$post[] = "<HomePage>".$pp_home."</HomePage>";
$post[] = "<OrderItems>";

/*
$prods = func_products_in_cart ($cart, $userinfo["membership"]);
foreach($prods as $product)
{
	$post[] = "<Item>";
	$post[] = "<Description>".$product["product"]."</Description>";
	$post[] = "<Cost>".$product["price"]."</Cost>";
	$post[] = "<Qty>".$product["amount"]."</Qty>";
	$post[] = "</Item>";
}

if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
foreach ($cart["giftcerts"] as $tmp_gc) {
	$post[] = "<Item>";
	$post[] = "<Description>GIFT CERTIFICATE</Description>";
	$post[] = "<Cost>".$tmp_gc["amount"]."</Cost>";
	$post[] = "<Qty>1</Qty>";
	$post[] = "</Item>";
}

$post[] = "<Item>";
$post[] = "<Description>Additional charge</Description>";
$post[] = "<Cost>".($cart["total_cost"]-$cart["subtotal"])."</Cost>";
$post[] = "<Qty>1</Qty>";
$post[] = "</Item>";
*/

$post[] = "<Item>";
$post[] = "<Description>Your Cart</Description>";
$post[] = "<Cost>".$cart["total_cost"]."</Cost>";
$post[] = "<Qty>1</Qty>";
$post[] = "</Item>";

$post[] = "</OrderItems>";
$post[] = "</TransactionData>";
$post[] = "</SaleRequest>";

$pst = array("xml=".strtr(join("",$post),array("&"=>"&amp;")));

list($a,$return)=func_https_request("POST","https://secure.paymentclearing.com:443/cgi-bin/rc/xmltrans.cgi",$pst);

#<xml ....>
#
#<SaleResponse>
# ....
#<TransactionData>
#	<Status>OK</Status>
#	<ErrorCategory></ErrorCategory>
#	<ErrorMessage></ErrorMessage>
#	<WarningMessage></WarningMessage>
#	<AuthCode>000000</AuthCode>
#	<AVSCategory></AVSCategory>
#	<AVSResponse></AVSResponse>
#	<CVV2Response></CVV2Response>
#	<TimeStamp>20030421015308</TimeStamp>
#	<TestMode>1</TestMode>
#	<Total>31.32</Total>
#	<XID>0</XID>
#	<RecurringData>
#		<RecurRecipe></RecurRecipe>
#		<RecurReps></RecurReps>
#		<RecurTotal></RecurTotal>
#		<RecurDesc></RecurDesc>
#	</RecurringData>
#</TransactionData>
#</SaleResponse>

preg_match("/<TransactionData>(.*)<\/TransactionData>/",$return,$out);$return =$out[1];
preg_match("/<Status>(.*)<\/Status>/",$return,$status);

if($status[1] == "OK")
{
	$bill_output["code"] = 1;
	preg_match("/<AuthCode>(.*)<\/AuthCode>/",$return,$out);
	$bill_output["billmes"] = "AuthCode: ".$out[1];
}
else
{
	$bill_output["code"] = 2;
	preg_match("/<ErrorCategory>(.*)<\/ErrorCategory>/",$return,$out);
	$bill_output["billmes"] = $status[1]." : ".$out[1]." : ";
	preg_match("/<ErrorMessage>(.*)<\/ErrorMessage>/",$return,$out);
	$bill_output["billmes"].= $out[1];
}

preg_match("/<AVSResponse>(.*)<\/AVSResponse>/",$return,$out);
if(!empty($out[1]))
	$bill_output["avsmes"] = empty($avserr[$out[1]]) ? "AVSResponse: ".$out[1] : $avserr[$out[1]];
preg_match("/<AVSCategory>(.*)<\/AVSCategory>/",$return,$out);
if(!empty($out[1]))
	$bill_output["avsmes"].= " (".$out[1].")";

preg_match("/<CVV2Response>(.*)<\/CVV2Response>/",$return,$out);
if(!empty($out[1]))
	$bill_output["cvvmes"] = empty($cvverr[$out[1]]) ? "CVV2Response: ".$out[1] : $cvverr[$out[1]];

preg_match("/<XID>(.*)<\/XID>/",$return,$out);
if(!empty($out[1]))
	$bill_output["billmes"].= " (XID: ".$out[1].")";

?>
