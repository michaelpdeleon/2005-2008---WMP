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
# $Id: cc_hsbc.php,v 1.29.2.2 2006/06/26 05:41:13 max Exp $
#

$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD = "POST" && (isset($HTTP_POST_VARS['CpiResultsCode']) || isset($HTTP_GET_VARS['CpiResultsCode'])))
{
	require "./auth.php";

	$skey = $OrderId;
	require($xcart_dir."/payment/payment_ccview.php");
}
else
{

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('files');

$hsbc_storefrontid = $module_params["param01"];
$hsbc_hashkey = $module_params["param02"];
$hsbc_mode = ($module_params["testmode"]=="Y" ? "T" : "P");
$hsbc_currency = $module_params["param04"];

$ordr = $module_params["param05"].join("-", $secure_oid);

if(!$duplicate)
	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

$post_data = array(
	"CpiDirectResultUrl"	=> $https_location."/payment/cc_hsbc_result.php",
	"CpiReturnUrl"			=> $https_location."/payment/cc_hsbc.php",
	"MerchantData"			=> $ordr,
	"Mode"					=> $hsbc_mode,
	"OrderDesc"				=> "ORDER",
	"OrderId"				=> $ordr,
	"PurchaseAmount"		=> $cart["total_cost"] * (($hsbc_currency!="392")? 100 : 1),
	"PurchaseCurrency"		=> $hsbc_currency,
	"StorefrontId"			=> $hsbc_storefrontid,
	"TimeStamp"				=> (time())."000",
	"TransactionType"		=> "Capture",
	"UserId"				=> $userinfo["login"]
);

function hsbc_country2code($country) {
	global $sql_tbl;
	$code = func_query_first_cell("SELECT code_N3 FROM $sql_tbl[countries] WHERE code='".$country."'");
	return sprintf("%03d", $code);
}

$billing_info = array(
	"BillingAddress1" => $userinfo["b_address"],
	"BillingAddress2" => $userinfo["b_address_2"],
	"BillingCity" => $userinfo["b_city"],
	"BillingCountry" => hsbc_country2code($userinfo["b_country"]),
	"BillingCounty" => $userinfo["b_state"] ? $userinfo["b_state"]: "n/a",
	"BillingFirstName" => $bill_firstname,
	"BillingLastName" => $bill_lastname,
	"BillingPostal" => $userinfo["b_zipcode"],
	"ShopperEmail" => $userinfo["email"]
);

$shipping_info = array (
	"ShippingAddress1" => $userinfo["s_address"],
	"ShippingAddress2" => $userinfo["s_address_2"],
	"ShippingCity" => $userinfo["s_city"],
	"ShippingCountry" => hsbc_country2code($userinfo["s_country"]),
	"ShippingCounty" => $userinfo["s_state"] ? $userinfo["s_state"]: "n/a",
	"ShippingFirstName" => $ship_firstname,
	"ShippingLastName" => $ship_lastname,
	"ShippingPostal" => $userinfo["s_zipcode"]
);

foreach ($billing_info as $k => $v) {
	if (empty($v))
		unset($billing_info[$k]);
}
foreach ($shipping_info as $k => $v) {
	if (empty($v))
		unset($shipping_info[$k]);
}

$post = func_array_merge($post_data,$billing_info,$shipping_info);

$args = array_values($post);
array_unshift($args, $hsbc_hashkey);

if (X_DEF_OS_WINDOWS) {
	array_unshift($args, $xcart_dir.'/payment/TestHash.exe');
}
else {
	putenv("LD_LIBRARY_PATH=".getenv("LD_LIBRARY_PATH").":".$xcart_dir.'/payment');
	array_unshift($args, $xcart_dir.'/payment/TestHash.e');
}

foreach ($args as $k=>$v) {
	if (strlen($v) == 0) $v = '""';
	else $v = func_shellquote($v);

	$args[$k] = $v;
}

$errfile = func_temp_store('');
$cmdline = implode(" ", $args)." 2>".func_shellquote($errfile);

@exec($cmdline,$data);
$data = $data[0];

if(!preg_match("/^Hash value:  (.*)$/",$data,$a))
{
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Error in hash generation<hr />".$data;
}
else
{
	$post["OrderHash"] = $a[1];

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www.cpi.hsbc.com/servlet" method=POST name=process>
<?php
	if ($post)
	foreach($post as $k=>$v) {
		$v = htmlspecialchars($v);
		print "<input type=hidden name=\"$k\" value=\"$v\">\n";
	}
?>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>HSBC</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
exit;

}
}
?>
