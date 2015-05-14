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
# $Id: cc_2conew.php,v 1.27.2.6 2006/06/16 06:56:11 max Exp $
#

if (!isset($REQUEST_METHOD))
	$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if (!empty($HTTP_POST_VARS["cart_order_id"]) || !empty($HTTP_GET_VARS["cart_order_id"])) {
	require "./auth.php";

	$tmp = func_query_first("SELECT sessionid,param1 FROM $sql_tbl[cc_pp3_data] WHERE ref='$cart_order_id'");
	$bill_output["sessid"] = $tmp["sessionid"];
	$s = func_query_first("select param01,param03,testmode from $sql_tbl[ccprocessors] where processor='cc_2conew.php'");

	$bill_output["code"] = ($credit_card_processed=="Y" ? 1 : ($credit_card_processed=="K" ? 3 : 2));
	$order_ = ($s["testmode"]=="Y") ? 1 : $order_number;
	if(($total!=$tmp["param1"] && $amount!=$tmp["param1"]) || strtoupper(md5($s["param03"].$s["param01"].$order_.$tmp["param1"]))!=$key) {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "MD5 HASH is invalid!";
	} else {
		$bill_output["billmes"] = "";
	}

	if(!empty($order_number))
		$bill_output["billmes"].= " (Order number: ".$order_number.")";
	if(!empty($tcoid))
		$bill_output["billmes"].= " (TransID: ".$tcoid.") ";

	# Make IE happy with 2Checkout response for some server configurations
	echo str_repeat(" ", 600);
	
	# Save the full response in the order details
	$response_vars = "HTTP_".$REQUEST_METHOD."_VARS";
	if (is_array($$response_vars)) {
		foreach ($$response_vars as $k=>$v) {
			$full_response[] = "$k=>$v";
		}
		$bill_output["billmes"] .= "\n-------------------------\nFull response:\n";
		$bill_output["billmes"] .= implode("\n", $full_response);
		$bill_output["billmes"] .= "\n-------------------------\n";
	}

	# Force redirecting back to the site
	$weblink=2;

	define ('NOCOOKIE', 1);

	require($xcart_dir."/payment/payment_ccend.php");

}
else
{
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$merchant = $module_params ["param01"];
	$_orderids = $module_params ["param02"].join("-",$secure_oid);
	if (!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,param1) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."','".price_format($cart["total_cost"])."')");

	$b_state = $userinfo["b_state"];
	if (empty($b_state)) {
		$b_state = "n/a";
	} elseif (!in_array($userinfo['b_country'], array("US","CA"))) {
		$b_state = "XX";
	}
	$s_state = $userinfo["s_state"];
	if (empty($s_state)) {
		$s_state = "n/a";
	} elseif (!in_array($userinfo['s_country'], array("US","CA"))) {
		$s_state = "XX";
	}

?>
<html>
<body onLoad="document.process.submit();">
  <form action="https://www2.2checkout.com/2co/buyer/purchase" method="POST" name="process">
    <input type=hidden name=sid value="<?php echo htmlspecialchars($merchant); ?>">
	<input type=hidden name=total value="<?php echo price_format($cart["total_cost"]); ?>">
    <input type=hidden name=cart_order_id value="<?php echo htmlspecialchars($_orderids); ?>">
	<input type=hidden name=card_holder_name value="<?php echo htmlspecialchars($bill_name); ?>">
	<input type=hidden name=street_address value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
	<input type=hidden name=city value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
	<input type=hidden name=state value="<?php echo htmlspecialchars($b_state); ?>">
	<input type=hidden name=zip value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
	<input type=hidden name=country value="<?php echo htmlspecialchars($userinfo["b_country"]); ?>">
	<input type=hidden name=email value="<?php echo htmlspecialchars($userinfo["email"]); ?>">
	<input type=hidden name=phone value="<?php echo htmlspecialchars($userinfo["phone"]); ?>">
	<input type=hidden name=ship_name value="<?php echo htmlspecialchars($userinfo["s_firstname"]." ".$userinfo["s_lastname"]); ?>">
	<input type=hidden name=ship_street_address value="<?php echo htmlspecialchars($userinfo["s_address"]); ?>">
	<input type=hidden name=ship_city value="<?php echo htmlspecialchars($userinfo["s_city"]); ?>">
	<input type=hidden name=ship_state value="<?php echo htmlspecialchars($s_state); ?>">
	<input type=hidden name=ship_zip value="<?php echo htmlspecialchars($userinfo["s_zipcode"]); ?>">
	<input type=hidden name=ship_country value="<?php echo htmlspecialchars($userinfo["s_country"]); ?>">
	<input type=hidden name=fixed value="Y">
	<input type=hidden name=id_type value="1">
	<input type=hidden name=sh_cost value="<?php echo price_format($cart["shipping_cost"]); ?>">
	<?php if($module_params ["testmode"]=="Y") echo "<input type=hidden name=demo value=\"Y\">"; ?>
<?php
$i = -1;
if (!empty($products)) {
	foreach ($products as $v) {
		$i++;
		if (!empty($v['descr']))
			$v['descr'] = func_query_first_cell("SELECT descr FROM $sql_tbl[products] WHERE productid = '$productid'");
		if (empty($v['descr']))
			$v['descr'] = $v['product'];

if ($i == 0) {
?>
	<input type="hidden" name="c_prod" value="<?php echo htmlspecialchars($v['productid'].",".$v['amount']); ?>">
	<input type="hidden" name="c_name" value="<?php echo htmlspecialchars(substr($v['product'], 0, 127)); ?>">
	<input type="hidden" name="c_price" value="<?php echo price_format($v['price']); ?>">
	<input type="hidden" name="c_description" value="<?php echo htmlspecialchars(substr($v['descr'], 0, 254)); ?>">
	<input type="hidden" name="c_tangible" value="<?php echo (empty($v['distribution'])?"Y":"N"); ?>">
<?php
} else {
?>
	<input type="hidden" name="c_prod_<?php echo $i; ?>" value="<?php echo htmlspecialchars($v['productid'].",".$v['amount']); ?>">
	<input type="hidden" name="c_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars(substr($v['product'], 0, 127)); ?>">
	<input type="hidden" name="c_price_<?php echo $i; ?>" value="<?php echo price_format($v['price']); ?>">
	<input type="hidden" name="c_description_<?php echo $i; ?>" value="<?php echo htmlspecialchars(substr($v['descr'], 0, 254)); ?>">
	<input type="hidden" name="c_tangible_<?php echo $i; ?>" value="<?php echo (empty($v['distribution'])?"Y":"N"); ?>">
<?php
}
	}
}

if (!empty($cart["giftcerts"])) {
	foreach ($cart["giftcerts"] as $v) {
		$i++;
?>
	<input type="hidden" name="c_prod_<?php echo $i; ?>" value="<?php echo htmlspecialchars($i.",1"); ?>">
	<input type="hidden" name="c_name_<?php echo $i; ?>" value="Gift certificate">
	<input type="hidden" name="c_price_<?php echo $i; ?>" value="<?php echo price_format($v['amount']); ?>">
	<input type="hidden" name="c_description_<?php echo $i; ?>" value="Gift certificate">
	<input type="hidden" name="c_tangible_<?php echo $i; ?>" value="N">
<?php
	}
}

?>
	</form>
	<table width=100% height=100%>
	 <tr><td align=center valign=middle>Please wait while connecting to <b>2checkout.com</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
}
	exit;
?>
