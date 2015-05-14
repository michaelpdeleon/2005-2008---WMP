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
# $Id: cc_isecure.php,v 1.31.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == "GET" && isset($HTTP_GET_VARS["ok"]) && isset($HTTP_GET_VARS["ordr"]))
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$ordr."'");
	$bill_output["code"] = (($ok=="yes") ? 1 : 2);

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$accid = $module_params ["param01"];
	$test = "";
	switch ($module_params ["testmode"]) {
		case "A": $test="{TEST}"; break;
		case "D": $test="{TESTD}"; break;
	}
	$prefix = $module_params ["param03"];
	$curr = $module_params ["param04"];
	$ordr = $prefix.join("-",$secure_oid);
	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	$p_array = array();
	$flags = "";
	if($curr == 'US')
		$flags .= "{US}";
	$flags .= $test;
	
	if($cart['products']) {
		foreach($cart['products'] as $p) {
			$p_array[] = $p['price']."::".$p['amount']."::".$p['productcode']."::".str_replace("::", " ", $p['product'])."::".$flags;
		}
	}
	if($cart['giftcerts']) {
		foreach($cart['giftcerts'] as $g) {
			$p_array[] = $g['amount']."::1::GC::GiftCertificate::".$flags;
		}
	}

	$shipping_method = func_query_first_cell("SELECT shipping FROM $sql_tbl[shipping] WHERE shippingid='$cart[shippingid]'");
	if (empty($shipping_method)) {
		 $shipping_method = 'Shipping';
	} else {
		$shipping_method = 'Shipping - '.$shipping_method;
	}

	$p_array[] = $cart['shipping_cost']."::1::::".$shipping_method."::".$flags;

	# taxes
	$taxes_cost = $cart['tax_cost'];
	if ($taxes_cost != 0)
		$p_array[] = $taxes_cost."::1::::Tax::".$flags;

	# discounts
	$p_array[] = (-$cart['coupon_discount'])."::1::::Coupon discount::".$flags;
	$p_array[] = (-$cart['discount'])."::1::::Discount::".$flags;

	# applied giftcerts
	if ($cart["applied_giftcerts"]) {
		foreach($cart["applied_giftcerts"] as $k=>$v) {
			$p_array[] = ($v['giftcert_cost']*-1)."::1::::Applied GiftCertificate #".$v['giftcert_id']."::".$flags;
		}
	}
	
?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://secure.internetsecure.com/process.cgi" method=POST name=process>
  	<input type=hidden name=MerchantNumber value="<?php echo $accid; ?>">
	<input type=hidden name=xxxName value="<?php echo htmlspecialchars($bill_name); ?>">
	<input type=hidden name=xxxCompany value="<?php echo htmlspecialchars($userinfo["company"]); ?>">
	<input type=hidden name=xxxAddress value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=xxxCity value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=xxxProvince value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
	<input type=hidden name=xxxCountry value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=xxxPostal value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=xxxEmail value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=xxxPhone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
  	<input type=hidden name=language value="English">
	<input type=hidden name=ReturnURL value="<?php echo $http_location."/payment/cc_isecure.php?ordr=".$ordr."&ok=yes"; ?>">
	<input type=hidden name=Products value="<?php echo htmlspecialchars(@implode("|", $p_array)); ?>">
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>InternetSecure</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
exit;

?>
