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
# $Id: ps_paypal.php,v 1.27.2.2 2006/06/30 07:35:46 max Exp $
#
# PayPal CC processing module
#

# $custom variable exists in data POSTed by PayPal:
# 1) callback (POST)
# 2) return from PayPal (GET)
# it contains order_secureid

#
# Successful return from PayPal
#
if ($HTTP_GET_VARS['mode'] == 'success' || $HTTP_POST_VARS['mode'] == 'success') {
	require "./auth.php";

	$skey = $HTTP_GET_VARS["secureid"];
	require($xcart_dir."/payment/payment_ccview.php");
}
#
# Callback by PayPal
#
elseif ($HTTP_SERVER_VARS['REQUEST_METHOD'] == "POST" && !empty($HTTP_POST_VARS['payment_type'])) {
	require "./auth.php";

	x_load('http');

	if (!strcasecmp($payment_status,"Refunded")) {
		exit; # do nothing, ignore
	}

	$skey = $HTTP_POST_VARS["custom"];
	$bill_output['sessid'] = func_query_first_cell("SELECT sessionid FROM $sql_tbl[cc_pp3_data] WHERE ref='".$skey."'");

	if (!empty($HTTP_GET_VARS['notify_from']) && $HTTP_GET_VARS['notify_from'] == 'pro') {
		$_processor = 'ps_paypal_pro.php';
	}
	else {
		$_processor = 'ps_paypal.php';
	}

	$testmode = func_query_first_cell("SELECT testmode FROM $sql_tbl[ccprocessors] WHERE processor='$_processor'");

	$pp_host = ($testmode == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");

	if (!strcasecmp($payment_status,"Completed")) {
		## do PayPal (IPN) background request...
		$post = "";
		$post_vars = $HTTP_POST_VARS;
		$post_vars["cmd"] = "_notify-validate";
		foreach ($post_vars as $key => $val)
			$post[] = "$key=".stripslashes($val);

		list($a,$result) = func_https_request("POST","https://$pp_host:443/cgi-bin/webscr", $post);

		$bill_output["code"] = 2;
 		if (preg_match("/VERIFIED/i", $result)) {
			$bill_output["code"] = 1;
			$bill_message = "Accepted";
		} elseif (preg_match("/INVALID/i", $result)) {
			$bill_message = "Declined (invalid request)";
		} else {
			$bill_message = "Declined (processor error)";
		}

		## end of request
	} else if (!strcasecmp($payment_status,"Pending")) {
		$bill_message = "Queued";
		$bill_output["code"] = 3;
	} else {
		$bill_message = "Declined";
		$bill_output["code"] = 2;
	}

	$bill_output["billmes"] = "$bill_message Status: $payment_status (TransID #$txn_id)";
	if (!empty($pending_reason))
		$bill_output["billmes"] .= " Reason: $pending_reason";

	require $xcart_dir."/payment/payment_ccmid.php";
	require $xcart_dir."/payment/payment_ccwebset.php";
}
#
# Checkout
#
else {

	if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

	$pp_supported_charsets = array (
		"Big5", "EUC-JP", "EUC-KR", "EUC-TW", "gb2312", "gbk", "HZ-GB-2312",
		"ibm-862", "ISO-2022-CN", "ISO-2022-JP", "ISO-2022-KR", "ISO-8859-1",
		"ISO-8859-2", "ISO-8859-3", "ISO-8859-4", "ISO-8859-5", "ISO-8859-6",
		"ISO-8859-7", "ISO-8859-8", "ISO-8859-9", "ISO-8859-13", "ISO-8859-15",
		"KOI8-R", "Shift_JIS", "UTF-7", "UTF-8", "UTF-16", "UTF-16BE",
		"UTF-16LE", "UTF-32", "UTF-32BE", "UTF-32LE", "US-ASCII",
		"windows-1250", "windows-1251", "windows-1252", "windows-1253",
		"windows-1254", "windows-1255", "windows-1256", "windows-1257",
		"windows-1258", "windows-874", "windows-949", "x-mac-greek",
		"x-mac-turkish", "x-maccentraleurroman", "x-mac-cyrillic",
		"ebcdic-cp-us", "ibm-1047");

	$pp_charset = func_query_first_cell("SELECT charset FROM $sql_tbl[countries] WHERE code='$shop_language'");
	if (!in_array($pp_charset, $pp_supported_charsets)) {
		$pp_charset = "ISO-8859-1";
	}

	$pp_acc = $module_params['param01'];
	$pp_for = $module_params['param02'];
	$pp_curr = $module_params['param03'];
	$pp_prefix = preg_replace("/[ '\"]+/","",$module_params['param04']);
	$pp_ordr = $pp_prefix.join("-",$secure_oid);

	$pp_host = ($module_params['testmode'] == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com");

	db_query("REPLACE INTO $sql_tbl[cc_pp3_data] (ref,sessionid,trstat) VALUES ('".addslashes($order_secureid)."','".$XCARTSESSID."','GO|".implode('|',$secure_oid)."')");

	# Filling $_location variable depending protocol value
	$res = func_query_first("SELECT protocol FROM $sql_tbl[payment_methods] WHERE paymentid='".$paymentid."'");
	$_location = ($res["protocol"] == "https") ? $https_location : $http_location;

	if ($userinfo["b_country"] == "US") $_customer_state = $userinfo["b_state"];
	else $_customer_state = $userinfo["b_statename"];

	$u_phone = preg_replace('![^\d]+!', '', $userinfo["phone"]);

	$fields = array(
		"charset" => $pp_charset,
		"cmd" => "_ext-enter",
		"custom" => $order_secureid,
		"invoice" => $pp_ordr,
		"redirect_cmd" => "_xclick",
		"mrb" => "R-2JR83330TB370181P",
		"pal" => "RDGQCFJTT6Y6A",
		"rm" => "2",
		"email" => $userinfo["email"],
		"first_name" => $bill_firstname,
		"last_name" => $bill_lastname,
		"country" => $userinfo["b_country"],
		"state" => $_customer_state,
		"day_phone_a" => substr($u_phone, -10, -7),
		"day_phone_b" => substr($u_phone, -7, -4),
		"day_phone_c" => substr($u_phone, -4),
		"night_phone_a" => substr($u_phone, -10, -7),
		"night_phone_b" => substr($u_phone, -7, -4),
		"night_phone_c" => substr($u_phone, -4),
		"business" => $pp_acc,
		"item_name" => $pp_for,
		"amount" => sprintf("%0.2f", $cart["total_cost"]),
		"currency_code" => $pp_curr,
		"return" => $_location."/payment/ps_paypal.php?mode=success&secureid=$order_secureid",
		"cancel_return" => $_location.DIR_CUSTOMER."/cart.php",
		"notify_url" => $_location."/payment/ps_paypal.php",
		"bn" => "x-cart"
	);

	if (!empty($userinfo["b_address"]))
		$fields['address1'] = $userinfo["b_address"];
	if (!empty($userinfo["b_address_2"]))
		$fields['address2'] = $userinfo["b_address_2"];
    if (!empty($userinfo["b_city"]))
        $fields['city'] = $userinfo["b_city"];
    if (!empty($userinfo["b_zipcode"]))
        $fields['zip'] = $userinfo["b_zipcode"];

	func_create_payment_form("https://$pp_host/cgi-bin/webscr", $fields, "PayPal");
}
exit;

?>
