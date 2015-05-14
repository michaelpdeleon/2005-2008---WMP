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
# $Id: cc_payzip.php,v 1.19 2006/01/11 06:56:22 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_login = $module_params["param01"];
$pp_pass = $module_params["param02"];
$test = $module_params["testmode"]!="N"?"testapi/":"";
$oid = $module_params["param05"].join("-",$secure_oid);

$post = "";
$post[] = "<PAYZIP_XML><REQUEST>";
$post[] = "<Pin>".$pp_pass."</Pin>";
$post[] = "<AccID>".$pp_login."</AccID>";
$post[] = "<OrderID>".$oid."</OrderID>";
$post[] = "<Function>INSERT_ORDER</Function>";
$post[] = "<PRODUCTS>";
$post[] = "<MailNotify>0</MailNotify>";
$post[] = "<EMailAddress>".$userinfo["email"]."</EMailAddress>";
$post[] = "<Description>Your Cart</Description>";
$post[] = "<Currency>".$module_params["param04"]."</Currency>";
$post[] = "<AmountTotal>".(100*$cart["total_cost"])."</AmountTotal>";
$post[] = "<VATRateTotal>0</VATRateTotal>";
$post[] = "<ProductTotal>".(100*$cart["total_cost"])."</ProductTotal>";

foreach($products as $product)
	$post[] = "<PRODUCT><Description>".urlencode($product["product"])."</Description><Price>".(100*$product["price"])."</Price><Quantity>".$product["amount"]."</Quantity><VATRate>0</VATRate><SubTotal>".(100*$product["price"]*$product["amount"])."</SubTotal></PRODUCT>";

$post[] = "</PRODUCTS>";
$post[] = "<DELIVERY><ShippingAddress>";
$post[] = "<Standard>1</Standard>";
$post[] = "<Name>".$userinfo["s_firstname"]." ".$userinfo["s_lastname"]."</Name>";
$post[] = "<SAddress1>".$userinfo["s_address"]."></SAddress1>";
$post[] = "<SAddress2></SAddress2>";
$post[] = "<SAddress3>".$userinfo["s_city"]." ".$userinfo["s_state"]."</SAddress3>";
$post[] = "<SAddress4>".$userinfo["s_zipcode"]."</SAddress4>";
$post[] = "<SAddress5>".$userinfo["s_country"]."</SAddress5>";
$post[] = "</ShippingAddress></DELIVERY>";
$post[] = "</REQUEST></PAYZIP_XML>";

list($a,$return)=func_https_request("POST","https://www.payzip.net:443/".$test."api/apixml.asp",$post,"");

#<PAYZIP_XML>
#   <RESPONSE>
#        <Function>INSERT_ORDER</Function>
#        <Result>OK</Result>
#        <ResultCode>0</ResultCode>
#        <Message>Transaction successful</Message>
#        <AccID>973008</AccID>
#        <OrderID>20030521110404</OrderID>
#        <OrderStatus>1</OrderStatus>
#        <StatusDate>20030521110400</StatusDate>
#        <Reference>911</Reference>
#   </RESPONSE>
#</PAYZIP_XML>

if(preg_match("/<Result>OK<\/Result>/",$return))
{
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($oid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

	func_header_location("https://www.payzip.net/".$test."w2w/default.asp?AccID=".$pp_login."&OrderID=".$oid."&URL=".$current_location."/payment/cc_payzip_result.php");
	exit;
}
else
{
	# Step 1 failed.
	$bill_output["code"] = 2;
	preg_match("/<Message>(.*)<\/Message>/",$return,$out);
	$bill_output["billmes"] = $out[1];

	preg_match("/<ResultCode>(.*)<\/ResultCode>/",$return,$out);
	if($out[1])$bill_output["billmes"] .= " (ResultCode: ".$out[1].")";

	preg_match("/<Reference>(.*)<\/Reference>/",$return,$out);
	if($out[1])$bill_output["billmes"] .= " (Reference: ".$out[1].")";
}

?>
