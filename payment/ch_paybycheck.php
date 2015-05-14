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
# $Id: ch_paybycheck.php,v 1.24.2.1 2006/06/15 10:10:49 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if($REQUEST_METHOD=="GET" && !empty($resp_code) && !empty($oid))
{
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$oid."'");
	$bill_output["code"] = ($resp_code=="ok" ? 1 : 2);
	if($ref)
		$bill_output["billmes"] = ($resp_code!="ok" ? "Declined: " : "")."(PayByCheck transaction reference number: ".$ref.")";

	require($xcart_dir."/payment/payment_ccend.php");
}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$pid = $module_params ["param01"];
	$ordr = $module_params ["param02"].join("-",$secure_oid);

	if(!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid) VALUES ('".addslashes($ordr)."','".$XCARTSESSID."')");

	$string = array();
	foreach ($products as $product)
		$string [] = $product["product"]." (".$product["price"]." x ".$product["amount"].")";

	if (@is_array($cart["giftcerts"]) && count($cart["giftcerts"])>0)
	foreach ($cart["giftcerts"] as $tmp_gc)
		$string [] = "GIFT CERTIFICATE (".$tmp_gc["amount"]." x 1)";

	$url = $http_location."/payment/ch_paybycheck.php?ref=REF&oid=".$ordr."&resp_code=";

?>
<html>
<body onLoad="document.process.submit();">
  <form action=https://paybycheck.com/ method=POST name=process>
  <input type=hidden name=id value="<?php echo htmlspecialchars($pid); ?>">
  <input type=hidden name=name value="<?php echo htmlspecialchars($bill_name); ?>">
  <input type=hidden name=address1 value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
  <input type=hidden name=city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
  <input type=hidden name=state value="<?php echo htmlspecialchars($userinfo["b_state"]); ?>">
  <input type=hidden name=zip value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
  <input type=hidden name=phone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
  <input type=hidden name=email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
  <input type=hidden name=a value="<?php echo $cart["total_cost"]; ?>">

  <input type=hidden name=item value="<?php echo htmlspecialchars($ordr.": ".join("; ",$string)); ?>">
  <input type=hidden name=s value="<?php print htmlspecialchars($url."ok"); ?>">
  <input type=hidden name=f value="<?php print htmlspecialchars($url."nok"); ?>">
  <input type=hidden name=lock value=true>
</form>
<table width=100% height=100%>
<tr><td align=center valign=middle>Please wait while connecting to <b>PayByCheck</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php
}
exit;

?>
