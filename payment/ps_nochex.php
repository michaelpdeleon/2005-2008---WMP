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
# $Id: ps_nochex.php,v 1.23.2.1 2006/06/15 10:10:49 max Exp $
#

$REQUEST_METHOD = $HTTP_SERVER_VARS["REQUEST_METHOD"];

if ($REQUEST_METHOD == 'POST' && $HTTP_GET_VARS['mode'] == 'responder' && $HTTP_POST_VARS && $HTTP_GET_VARS['orderids']) {
	require "./auth.php";

	x_load('http');

    $bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$orderids."'");

	# APC system responder
    foreach($HTTP_POST_VARS as $k => $v)
		$advinfo[] = "$k: $v";

	if ($to_email != func_query_first_cell("SELECT param01 FROM $sql_tbl[ccprocessors] WHERE processor='ps_nochex.php'")) {
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Invalid NOCHEX account!";
	}
	else {
		# Request transaction result code
		$pos = array();
		foreach ($HTTP_POST_VARS as $k => $v)
			$post[] = "$k=$v";

		list($a,$return) = func_https_request("POST", "https://www.nochex.com:443/nochex.dll/apc/apc", $post);
		$return = trim($return);
		if (preg_match('/AUTHORISED/', $return)) {
			$bill_output["code"] = 1;
			$bill_output["billmes"] = $return;
		}
		else {
			$bill_output["code"] = 2;
			$bill_output["billmes"] = "Reason: Rejected by NOCHEX server (APC system)!";
		}
	}

	$skey = $HTTP_GET_VARS['orderids'];
	require $xcart_dir."/payment/payment_ccmid.php";
	require $xcart_dir."/payment/payment_ccwebset.php";
}
elseif($HTTP_GET_VARS['mode'] == 'complete' && $orderids) {
	#
	# Handling for "returnurl" field
	#
	require "./auth.php";

	$weblink = 1;
	$skey = $HTTP_GET_VARS["orderids"];
	require($xcart_dir."/payment/payment_ccview.php");
}
elseif($HTTP_GET_VARS['mode'] == 'cancel' && $HTTP_GET_VARS["orderids"]) {
	#
	# Handling for "cancelurl" field
	#
	require "./auth.php";

	$bill_output["sessid"] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$HTTP_GET_VARS["orderids"]."'");
	$bill_output["code"] = 2;
	$bill_output["billmes"] = "Canceled by customer";

	$skey = $HTTP_GET_VARS["orderids"];
	require($xcart_dir."/payment/payment_ccend.php");
}
elseif ($HTTP_POST_VARS["action"] == 'place_order' && $REQUEST_METHOD == 'POST') {
	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	$_orderids = urlencode($module_params['param04'].join("-",$secure_oid));
	if (!$duplicate)
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".$_orderids."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

?>
<html>
<body onload="document.process.submit();">
  <form action="https://www.nochex.com/nochex.dll/checkout" method="POST" name="process">
	<input type="hidden" name=email value="<?php echo htmlspecialchars($module_params['param01']); ?>">
	<input type="hidden" name=amount value="<?php echo htmlspecialchars($cart["total_cost"]); ?>">
    <input type="hidden" name="order_number" value="<?php echo $_orderids; ?>">
<?php if($module_params['param02']) { ?><input type="hidden" name="logo" value="<?php echo htmlspecialchars($module_params['param02']); ?>"><?php } ?>
<?php if($module_params['testmode'] == 'Y') { ?><input type="hidden" name="status" value="test"><?php } ?>
    <input type="hidden" name="returnurl" value="<?php echo $current_location."/payment/ps_nochex.php?mode=complete&orderids=".$_orderids; ?>">
    <input type="hidden" name="cancelurl" value="<?php echo $current_location."/payment/ps_nochex.php?mode=cancel&orderids=".$_orderids; ?>">
    <input type="hidden" name="firstname" value="<?php echo htmlspecialchars($bill_firstname); ?>">
    <input type="hidden" name="lastname" value="<?php echo htmlspecialchars($bill_lastname); ?>">
    <input type="hidden" name="firstline" value="<?php echo htmlspecialchars($userinfo["b_address"]); ?>">
    <input type="hidden" name="town" value="<?php echo htmlspecialchars($userinfo["b_city"]); ?>">
    <input type="hidden" name="county" value="<?php echo htmlspecialchars($userinfo["b_statename"]); ?>">
    <input type="hidden" name="postcode" value="<?php echo htmlspecialchars($userinfo["b_zipcode"]); ?>">
    <input type="hidden" name="email_address_sender" value="<?php echo htmlspecialchars($userinfo['email']); ?>">
    <input type="hidden" name="responderurl" value="<?php echo $current_location."/payment/ps_nochex.php?mode=responder&orderids=".$_orderids; ?>">
	</form>
	<table width="100%" height="100%">
	 <tr><td align="center" valign="middle">Please wait while connecting to <b>NOCHEX</b> payment gateway...</td></tr>
	</table>
 </body>
</html>
<?php
exit;
}

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }
exit;
?>
