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
# $Id: cc_payepay.php,v 1.22.2.1 2006/06/15 10:10:49 max Exp $
#

require "./auth.php";

if ($REQUEST_METHOD=="POST" && $HTTP_POST_VARS["option1"] && $HTTP_POST_VARS["cc_status"])
{

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$option1."'");

	$bill_output["code"] = (($cc_status=="pass") ? 1 : 2);
	if($cc_status!="pass")
		$bill_output["billmes"] = "Declined";
	if(!$orderid)
		$bill_output["billoutput"].= " (OrderID: ".$orderid.")";
	if(!$sku)
		$bill_output["billoutput"].= " (SKU: ".$sku.")";

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pp_companyid = $module_params["param01"];
	$pp_payepaylink = $module_params["param03"];
	$ordr = $module_params["param02"];
	if(!$duplicate)
		db_query("replace into $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	$string = "";
	foreach ($products as $product)
		$string [] = " - ".$product["product"]." (".$product["price"]." x ".$product["amount"].")";

	if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
	foreach ($cart["giftcerts"] as $tmp_gc)
		$string [] = " - GIFT CERTIFICATE (".$tmp_gc["amount"]." x 1)";

	$returnurl=$http_location."/payment/cc_payepay.php";
 
?>
<html>
<body onLoad="document.proces.submit();">
  <form action="<?php echo $pp_payepaylink; ?>" method=POST name=process>
    <input type=hidden name=companyid value="<?php echo $pp_companyid; ?>">
    <input type=hidden name=tr_type value="203B7C">
    <input type=hidden name=total value="<?php echo $cart["total_cost"]; ?>">
    <input type=hidden name=product1 value="<?php echo $string; ?>">
	<input type=hidden name=b_firstname value="<?php echo $bill_firstname; ?>">
	<input type=hidden name=s_firstname value="<?php echo $userinfo["s_firstname"]; ?>">
	<input type=hidden name=b_middlename value="">
	<input type=hidden name=s_middlename value="">
	<input type=hidden name=b_lastname value="<?php echo $bill_lastname; ?>">
	<input type=hidden name=s_lastname value="<?php echo $userinfo["s_lastname"]; ?>">
	<input type=hidden name=email value="<?php echo $userinfo["email"]; ?>">
	<input type=hidden name=b_address value="<?php echo $userinfo["b_address"]; ?>">
	<input type=hidden name=s_address value="<?php echo $userinfo["s_address"]; ?>">
	<input type=hidden name=b_city value="<?php echo $userinfo["b_city"]; ?>">
	<input type=hidden name=s_city value="<?php echo $userinfo["s_city"]; ?>">
	<input type=hidden name=b_country value="<?php echo $userinfo["b_country"]; ?>">
	<input type=hidden name=s_country value="<?php echo $userinfo["s_country"]; ?>">
	<input type=hidden name=b_zip value="<?php echo $userinfo["b_zipcode"]; ?>">
	<input type=hidden name=s_zip value="<?php echo $userinfo["s_zipcode"]; ?>">
	<input type=hidden name=b_state value="<?php echo $userinfo["b_state"]; ?>">
	<input type=hidden name=s_state value="<?php echo $userinfo["s_state"]; ?>">
	<input type=hidden name=b_tel value="<?php echo $userinfo["phone"]; ?>">
	<input type=hidden name=delivery value="N">
	<input type=hidden name=formget value="N">
	<input type=hidden name=option1 value="<?php echo $ordr; ?>">
	<input type=hidden name=redirect value="<?php echo $returnurl; ?>">
	<input type=hidden name=redirectfail value="<?php echo $returnurl; ?>">
  </form>
  <table width=100% height=100%>
	<tr><td align=center valign=middle>Please wait while connecting to <b>PayEPay</b> payment gateway...</td></tr>
  </table>
 </body>
</html>
<?php
}
exit;

?>
