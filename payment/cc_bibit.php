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
# $Id: cc_bibit.php,v 1.10.2.2 2006/07/25 05:33:31 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('http');

$x_mid = $module_params["param01"];
$x_pass = $module_params["param02"];
$posturl = ($module_params["testmode"]=="Y" ? "-test" : "");
$x_curr = $module_params["param05"];
$x_cexp = 2;if($x_curr=="HUF" || $x_curr=="IDR" || $x_curr=="JPY" || $x_curr=="KRW")$x_cexp = 0;

$first4 = 0+substr($userinfo["card_number"],0,4);
if($first4>=4000 && $first4<=4999)$userinfo["card_type"]="VISA-SSL"; # VISA
if($first4>=5100 && $first4<=5999)$userinfo["card_type"]="ECMC-SSL"; # MasterCard
if($first4>=3400 && $first4<=3499)$userinfo["card_type"]="AMEX-SSL"; # AmericanExpress
if($first4>=3700 && $first4<=3799)$userinfo["card_type"]="AMEX-SSL"; # AmericanExpress
if($first4>=3000 && $first4<=3059)$userinfo["card_type"]="DINERS-SSL"; # Diners
if($first4>=3600 && $first4<=3699)$userinfo["card_type"]="DINERS-SSL"; # Diners
if($first4>=3800 && $first4<=3889)$userinfo["card_type"]="DINERS-SSL"; # Diners
if($first4==6011)$userinfo["card_type"]="DISCOVER-SSL"; # Discover
if($first4>=3528 && $first4<=3589)$userinfo["card_type"]="JCB-SSL"; # JCB

$post = "";
$post[] = "<?xml version=\"1.0\"?>";
$post[] = "<!DOCTYPE paymentService PUBLIC \"-//Bibit/DTD Bibit PaymentService v1//EN\" \"http://dtd.bibit.com/paymentService_v1.dtd\">";
$post[] = "<paymentService version=\"1.4\" merchantCode=\"".$x_mid."\">";
$post[] = "<submit>";
$post[] = "<order orderCode=\"".$module_params["param03"].join("-",$secure_oid)."\">";
$post[] = "<description>".$config["Company"]["company_name"]."</description>";
$post[] = "<amount value=\"".(100*$cart["total_cost"])."\" currencyCode=\"".$x_curr."\" exponent=\"".$x_cexp."\"/>";
$post[] = "<orderContent>";
$post[] = "<![CDATA[";

$post[] = "<center>";
#$post[] = "<table>";
#$post[] = "<tr><td bgcolor='#CCCCCC'>Your Internet Order:</td><td colspan='2' bgcolor='#ffff00' align='right'>AY 845</td></tr>";
#$post[] = "<tr><td bgcolor='#ffff00'>Description:</td><td>14 Tulip bulbs</td><td align='right'>1,00</td></tr>";
#$post[] = "<tr><td colspan='2'>Subtotal:</td><td align='right'>14,00</td></tr>";
#$post[] = "<tr><td colspan='2'>VAT: 13%</td><td align='right'>1,82</td></tr>";
#$post[] = "<tr><td colspan='2'>Shipping and Handling:</td><td align='right'>4,00</td></tr>";
#$post[] = "<tr><td colspan='2' bgcolor='#c0c0c0'>Total cost:</td><td bgcolor='#c0c0c0' align='right'>Euro 19,82</td></tr>";
#$post[] = "<tr><td colspan='3'>&nbsp;</td></tr>";
#$post[] = "<tr><td bgcolor='#ffff00' colspan='3'>Your billing address:</td></tr>";
#$post[] = "<tr><td colspan='3'>Mr. $lastName,<br />$shopperStreet,<br />$postalCode $shopperCity,<br />Thisplace.</td></tr>";
#$post[] = "<tr><td colspan='3'>&nbsp;</td></tr>";
#$post[] = "<tr><td bgcolor='#ffff00' colspan='3'>Your shipping address:</td></tr>";
#$post[] = "<tr><td colspan='3'>Mr. $lastName,<br />$shopperStreet,<br />$postalCode $shopperCity,<br />Thisplace.</td></tr>";
#$post[] = "<tr><td colspan='3'>&nbsp;</td></tr>";
#$post[] = "<tr><td bgcolor='#ffff00' colspan='3'>Our contact information:</td></tr>";
#$post[] = "<tr><td colspan='3'>ACME Webshops Int. Inc.,<br />11 Strangewood Blv.,<br />1255 KZ Thisisit,<br />Nowhereatall.<br /><br />acmeweb@acme.inc<br />(555) 1235 456</td></tr>";
#$post[] = "<tr><td colspan='3'>&nbsp;</td></tr>";
#$post[] = "<tr><td bgcolor='#c0c0c0' colspan='3'>Billing notice:</td></tr>";
#$post[] = "<tr><td colspan='3'>Your payment will be handled by Bibit Global Payments Services<br />This name may appear on your bank statement<br />http://www.bibit.com</td></tr>";
#$post[] = "</table>";
$post[] = "</center>";

$post[] = "]]>";
$post[] = "</orderContent>";
$post[] = "<paymentDetails>";
$post[] = "<".$userinfo["card_type"].">";
$post[] = "<cardNumber>".$userinfo["card_number"]."</cardNumber>";
$post[] = "<expiryDate><date month=\"".substr($userinfo["card_expire"],0,2)."\" year=\"".(2000+substr($userinfo["card_expire"],2,2))."\" /></expiryDate>";
$post[] = "<cardHolderName>".$userinfo["card_name"]."</cardHolderName>";
$post[] = "<cvc>".$userinfo["card_cvv2"]."</cvc>";
$post[] = "<cardAddress><address>";
$post[] = "<firstName>".$bill_firstname."</firstName>";
$post[] = "<lastName>".$bill_lastname."</lastName>";
$post[] = "<street>".$userinfo["b_address"]."</street>";
$post[] = "<postalCode>".$userinfo["b_zipcode"]."</postalCode>";
$post[] = "<city>".$userinfo["b_city"]."</city>";
$post[] = "<countryCode>".$userinfo["b_country"]."</countryCode>";
$post[] = "<telephoneNumber>".$userinfo["phone"]."</telephoneNumber>";
$post[] = "</address></cardAddress>";
$post[] = "</".$userinfo["card_type"].">";
$post[] = "<session shopperIPAddress=\"".$REMOTE_ADDR."\" id=\"".$XCARTSESSID."\" />";
$post[] = "</paymentDetails>";
$post[] = "<shopper>";
$post[] = "<shopperEmailAddress>".$userinfo["email"]."</shopperEmailAddress> <authenticatedShopperID>".$userinfo["login"]."</authenticatedShopperID>";
$post[] = "</shopper>";
$post[] = "<shippingAddress>";
$post[] = "<address>";
$post[] = "<firstName>".$userinfo["s_firstname"]."</firstName>";
$post[] = "<lastName>".$userinfo["s_lastname"]."</lastName>";
$post[] = "<street>".$userinfo["s_address"]."</street>";
$post[] = "<postalCode>".$userinfo["s_zipcode"]."</postalCode>";
$post[] = "<city>".$userinfo["s_city"]."</city>";
$post[] = "<countryCode>".$userinfo["s_country"]."</countryCode>";
$post[] = "<telephoneNumber>".$userinfo["phone"]."</telephoneNumber>";
$post[] = "</address>";
$post[] = "</shippingAddress>";
$post[] = "</order>";
$post[] = "</submit>";
$post[] = "</paymentService>";

list($a,$return) = func_https_request("POST","https://".$x_mid.":".$x_pass."@secure".$posturl.".bibit.com:443/jsp/merchant/xml/paymentService.jsp",$post,"");

#print htmlentities($return);

#<!DOCTYPE paymentService PUBLIC "-//Bibit//DTD Bibit PaymentService v1//EN" "http://dtd.bibit.com/paymentService_v1.dtd">
#<paymentService version="1.4" merchantCode="SHORTCUT"><reply>
#	<orderStatus orderCode="233-8479-3544">
#	<payment>
#		<paymentMethod>VISA-SSL</paymentMethod>
#		<amount value="7916" currencyCode="EUR" exponent="2" debitCreditIndicator="credit"/>
#		<lastEvent>AUTHORISED</lastEvent>
#		<balance accountType="IN_PROCESS_AUTHORISED">
#			<amount value="7916" currencyCode="EUR" exponent="2" debitCreditIndicator="credit"/>
#		</balance>
#		<cardNumber>4242********4242</cardNumber>
#		<riskScore value="0"/>
#	</payment>
#	</orderStatus>
#</reply></paymentService>

$err = array(
	"0"  => "AUTHORISED",
	"2"  => "REFERRED",
	"4"  => "HOLD CARD",
	"5"  => "REFUSED",
	"8"  => "APPROVE AFTER IDENTIFICATION",
	"13" => "INVALID AMOUNT",
	"15" => "INVALID CARD ISSUER",
	"17" => "ANNULATION BY CLIENT",
	"28" => "ACCESS DENIED",
	"29" => "IMPOSSIBLE REFERENCE NUMBER",
	"33" => "CARD EXPIRED",
	"34" => "FRAUD SUSPICION",
	"38" => "SECURITY CODE EXPIRED",
	"41" => "LOST CARD",
	"43" => "STOLEN CARD, PICK UP",
	"51" => "LIMIT EXCEEDED",
	"55" => "INVALID SECURITY CODE",
	"56" => "UNKNOWN CARD",
	"57" => "ILLEGAL TRANSACTION",
	"62" => "RESTRICTED CARD",
	"63" => "SECURITY RULES VIOLATED",
	"75" => "SECURITY CODE INVALID",
	"76" => "CARD BLOCKED",
	"85" => "REJECTED BY CARD ISSUER",
	"91" => "CREDITCARD ISSUER TEMPORARILY NOT REACHABLE",
	"97" => "SECURITY BREACH",
	"3"  => "INVALID ACCEPTOR",
	"12" => "INVALID TRANSACTION",
	"14" => "INVALID ACCOUNT",
	"19" => "REPEAT OF LAST TRANSACTION",
	"20" => "ACQUIRER ERROR",
	"21" => "REVERSAL NOT PROCESSED, MISSING AUTHORISATION",
	"24" => "UPDATE OF FILE IMPOSSIBLE",
	"25" => "REFERENCE NUMBER CANNOT BE FOUND",
	"26" => "DUPLICATE REFERENCE NUMBER",
	"27" => "ERROR IN REFERENCE NUMBER FIELD",
	"30" => "FORMAT ERROR",
	"31" => "UNKNOWN ACQUIRER ACCOUNT CODE",
	"40" => "REQUESTED FUNCTION NOT SUPPORTED",
	"58" => "TRANSACTION NOT PERMITTED",
	"64" => "AMOUNT HIGHER THAN PREVIOUS TRANSACTION AMOUNT",
	"68" => "TRANSACTION TIMED OUT",
	"80" => "AMOUNT NO LONGER AVAILABLE, AUTHORISATION EXPIRED",
	"92" => "CREDITCARD TYPE NOT PROCESSED BY ACQUIRER",
	"94" => "DUPLICATE REQUEST"
);

$return = strtr($return,array("\n"=>""));
if(preg_match("/<reply>(.*)<\/reply>/U",$return,$o)) $return = $o[1];
if(preg_match("/<orderStatus orderCode=\"(.*)\">/U",$return,$o))$addon = "(Order code: ".$o[1].") ";

$bill_output["billmes"] = "";

if(preg_match("/<payment>(.*)<\/payment>/U",$return,$o))
{
	$return = $o[1];
	if(preg_match("/<lastEvent>(.*)<\/lastEvent>/U",$return,$o))$bill_output["billmes"] .= $o[1]." "; $typ = $o[1];
	if(preg_match("/<paymentMethod>(.*)<\/paymentMethod>/U",$return,$o))$bill_output["billmes"] .= "(Payment method: ".$o[1].") ";

	if($typ == "AUTHORISED" || $typ == "CAPTURED")
	{
		$bill_output["code"] = 1;
		if(preg_match("/<balance accountType=\"(.*)\">/U",$return,$o)) $bill_output["billmes"] .= "Balance account type: ".$o[1]."; ";
		if(preg_match("/<riskScore value=\"(.*)\"\/>/U",$return,$o)) $bill_output["billmes"] .= "Risk score: ".$o[1]."; ";
	}
	else
	{
		$bill_output["code"] = 2;
	}

}
elseif(preg_match("/(<error.*)<\/error>/U",$return,$o))
{
	$bill_output["code"] = 2; $return = $o[1];
	preg_match("/<!\[CDATA\[(.*)\]\]/U",$return,$o); $bill_output["billmes"] .= $o[1]." ";
	preg_match("/<error code=\"(.*)\">/U",$return,$o); $bill_output["billmes"] .= "(Error code ".$o[1].(($err[$o[1]]) ? " : ".$err[$o[1]] : "").") ";

} elseif (preg_match("/401\s+Authorization\s+Required/is", $a)) {
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Error: Authorization required";
}

$bill_output["billmes"] .= $addon;

if(preg_match("/<CVCResultCode description=\"(.*)\"\/>/iU",$return,$o))
	$bill_output["cvvmes"] = "CVCResultCode: ".$o[1];

if(preg_match("/<AVSResultCode description=\"(.*)\"\/>/iU",$return,$o))
	$bill_output["avsmes"] = "AVSResultCode: ".$o[1];

#print "<hr /><font color=".($bill_output["code"]!=1 ? "red" : "green").">";print_r($bill_output);
#exit;

?>
