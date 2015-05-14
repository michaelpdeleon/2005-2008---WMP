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
# $Id: cc_secpay.php,v 1.26.2.1 2006/06/15 10:10:49 max Exp $
#

# Callback
if (!defined('XCART_START') && ($HTTP_POST_VARS["is_callback"] == "Y" || $HTTP_GET_VARS["is_callback"] == "Y")) {

	require "./auth.php";

	$tmp = func_query_first("select * from $sql_tbl[cc_pp3_data] where ref='".$trans_id."'");
	$bill_output["sessid"] = $tmp['sessionid'];
	$md5_orig = $tmp['param1'];

	# MD5 hash (returned) checked
	$is_md5 = true;
	if (!empty($md5_orig)) {
		$is_md5 = ($md5_orig == $hash);
	}

	# Error codes
	$err = array(
		"N" => "Transaction not authorised. Failure message text available to merchant",
		"C" => "Communication problem. Trying again later may well work",
		"P:A" => "Pre-bank checks. Amount not supplied or invalid",
		"P:X" => "Pre-bank checks. Not all mandatory parameters supplied",
		"P:P" => "Pre-bank checks. Same payment presented twice",
		"P:S" => "Pre-bank checks. Start date invalid",
		"P:E" => "Pre-bank checks. Expiry date invalid",
		"P:I" => "Pre-bank checks. Issue number invalid",
		"P:C" => "Pre-bank checks. Card number fails LUHN check",
		"P:T" => "Pre-bank checks. Card type invalid - i.e. does not match card number prefix",
		"P:N" => "Pre-bank checks. Customer name not supplied",
		"P:M" => "Pre-bank checks. Merchant does not exist or not registered yet",
		"P:B" => "Pre-bank checks. Merchant account for card type does not exist",
		"P:D" => "Pre-bank checks. Merchant account for this currency does not exist",
		"P:V" => "Pre-bank checks. CV2 security code mandatory and not supplied / invalid",
		"P:R" => "Pre-bank checks. Transaction timed out awaiting a virtual circuit. Merchant may not have enough virtual circuits for the volume of business.",
		"P:#" => "Pre-bank checks. No MD5 hash / token key set up against account"
	);

	# Approved
	if ($code == "A" && $is_md5) {
		$bill_output["code"] = 1;
		$bill_output["billmes"] = "TransID: ".$trans_id."; AuthCode: ".$auth_code."; ";

	# Declined
	} else {
		$res = "";
		if (!$is_md5) {
			$res = "MD5 hash is wrong; ";
		} elseif (isset($err[$code])) {
			$res = $err[$code];
			if (!empty($message))
				$res .= " ($message)";
			if (!empty($resp_code))
				$res .= "; RespCode: $resp_code";
			$res .= "; ";
		}
		$bill_output["code"] = 2;
		$bill_output["billmes"] = "Declined: ".$res."TransID: ".$trans_id."; AuthCode: ".$auth_code."; ";
	}

	if ($test_status != 'live')
		$bill_output["billmes"] .= "Test status: ".$test_status;

	# Save AVS message
	if (!empty($cv2avs))
		$bill_output["avsmes"] = $cv2avs;

	$weblink = 2;
	require($xcart_dir."/payment/payment_ccend.php");

# Request
} else {

	if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

	@set_time_limit(100);
	$pp_testmodes = array("A"=>"true", "D"=>"false", "N"=>"live");

	$pp_login = $module_params["param01"];
	$pp_test = $pp_testmodes[$module_params["testmode"]];
	$pp_reqcv2 = $module_params["param03"];
	$pp_curr = $module_params["param04"];
	$_orderids = $module_params ["param05"].join("-",$secure_oid);
	$pp_rpass = $module_params["param06"];
	$pp_digest = $module_params["param07"];

	$url = $current_location."/payment/cc_secpay.php?is_callback=Y";

	# Generate MD5 hash (returned)
	$md5_orig = "";
	if (!empty($pp_digest)) {
		$md5_orig = md5("trans_id=$_orderids&amount=".price_format($cart["total_cost"])."&callback=".$url."&".$pp_digest);
	}

	if(!$duplicate) {
		db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid, param1) VALUES ('".addslashes($_orderids)."','".$XCARTSESSID."', '$md5_orig')");
	}

	$prod = array();
	if (!empty($products)) {
		foreach ($products as $p) {
			$prod[] = "prod=".str_replace(array(",",";","\n","\r"), array("","","",""), $p['productcode']).",item_amount=".price_format($p['price'])."x".$p['amount'];
		}
	}
	$prod = implode(";", $prod);
	
?>
<html>
<head>
<title>Order Form</title>
</head>
<body onLoad="document.process.submit();">
<form action="https://www.secpay.com/java-bin/ValCard" method="POST" name="process">
<input type="hidden" name="merchant" value="<?php echo htmlspecialchars($pp_login); ?>">
<input type="hidden" name="trans_id" value="<?php echo htmlspecialchars($_orderids); ?>">
<input type="hidden" name="amount" value="<?php echo htmlspecialchars(price_format($cart["total_cost"])); ?>">
<input type="hidden" name="callback" value="<?php echo htmlspecialchars($url); ?>">
<input type="hidden" name="currency" value="<?php echo htmlspecialchars($pp_curr); ?>">
<input type="hidden" name="options" value="<?php echo htmlspecialchars("test_status=".$pp_test.",cb_post=true,req_cv2=$pp_reqcv2,dups=false,md_flds=trans_id:amount:callback"); ?>">
<input type="hidden" name="order" value="<?php echo htmlspecialchars($prod); ?>">

<?php
# Send main MD5 hash
if (!empty($pp_rpass)) {
	$md5 = md5($_orderids.price_format($cart["total_cost"]).$pp_rpass);
	echo '<input type="hidden" name="digest" value="'.htmlspecialchars($md5).'">';
}
?>

<input type="hidden" name="bill_name" value="<?php echo htmlspecialchars($bill_name); ?>">
<input type="hidden" name="bill_company" value="<?php echo htmlspecialchars($userinfo['company']); ?>">
<input type="hidden" name="bill_addr_1" value="<?php echo htmlspecialchars($userinfo['b_address']); ?>">
<input type="hidden" name="bill_addr_2" value="<?php echo htmlspecialchars($userinfo['b_address_2']); ?>">
<input type="hidden" name="bill_city" value="<?php echo htmlspecialchars($userinfo['b_city']); ?>">
<input type="hidden" name="bill_state" value="<?php echo htmlspecialchars($userinfo['b_state']); ?>">
<input type="hidden" name="bill_country" value="<?php echo htmlspecialchars($userinfo['b_country']); ?>">
<input type="hidden" name="bill_post_code" value="<?php echo htmlspecialchars($userinfo['b_zipcode']); ?>">
<input type="hidden" name="bill_tel" value="<?php echo htmlspecialchars($userinfo['phone']); ?>">
<input type="hidden" name="bill_email" value="<?php echo htmlspecialchars($userinfo['email']); ?>">
<input type="hidden" name="bill_url" value="<?php echo htmlspecialchars($userinfo['url']); ?>">

<input type="hidden" name="ship_name" value="<?php echo htmlspecialchars($userinfo['s_firstname'].(empty($userinfo['s_firstname']) ? "" : " ").$userinfo['s_lastname']); ?>">
<input type="hidden" name="ship_company" value="<?php echo htmlspecialchars($userinfo['company']); ?>">
<input type="hidden" name="ship_addr_1" value="<?php echo htmlspecialchars($userinfo['s_address']); ?>">
<input type="hidden" name="ship_addr_2" value="<?php echo htmlspecialchars($userinfo['s_address_2']); ?>">
<input type="hidden" name="ship_city" value="<?php echo htmlspecialchars($userinfo['s_city']); ?>">
<input type="hidden" name="ship_state" value="<?php echo htmlspecialchars($userinfo['s_state']); ?>">
<input type="hidden" name="ship_country" value="<?php echo htmlspecialchars($userinfo['s_country']); ?>">
<input type="hidden" name="ship_post_code" value="<?php echo htmlspecialchars($userinfo['s_zipcode']); ?>">
<input type="hidden" name="ship_tel" value="<?php echo htmlspecialchars($userinfo['phone']); ?>">
<input type="hidden" name="ship_email" value="<?php echo htmlspecialchars($userinfo['email']); ?>">
<input type="hidden" name="ship_url" value="<?php echo htmlspecialchars($userinfo['url']); ?>">

</form>
<table width=100% height=100%>
         <tr><td align=center valign=middle>Please wait while connecting to <b>SECPay</b> payment gateway...</td></tr>
</table>
</body>
</html>
<?php 
	exit;
}
?>
