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
# $Id: ps_gcheckout_callback.php,v 1.1.2.4 2006/08/01 15:02:46 max Exp $
#
# Google checkout (callback)
#

require "./auth.php";

x_session_register('login');

if (empty($HTTP_RAW_POST_DATA)) {
	x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with no data passed to it.", true);
	exit;
}

$module_params = func_query_first("SELECT * FROM $sql_tbl[ccprocessors] WHERE processor = 'ps_gcheckout.php'");

if ($PHP_AUTH_USER != $module_params['param01'] || $PHP_AUTH_PW != $module_params['param02']) {
	header('WWW-Authenticate: Basic');
	header('HTTP/1.0 401 Unauthorized');
	x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called without authorization, or caller authorization failed.", true);
	exit;
}

x_load("payment", "xml");

$xml_url = $module_params['testmode'] == 'Y' ? "https://sandbox.google.com:443/cws/v2/Merchant/".$module_params['param01']."/request" : "https://checkout.google.com:443/cws/v2/Merchant/".$module_params['param01']."/request";

$module_params['param03'] = "USD";

$parse_errors = false;
$options = array(
	'XML_OPTION_CASE_FOLDING' => 1,
	'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
);

$HTTP_RAW_POST_DATA = str_replace("\r","", $HTTP_RAW_POST_DATA);
$parsed = func_xml_parse($HTTP_RAW_POST_DATA, $parse_errors, $options);

if (empty($parsed)) {
	x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Received data could not be identified correctly.", true);
	exit;
}

$type = key($parsed);

$avs_info = array(
	"Y" => "Full AVS match (address and postal code)",
	"P" => "Partial AVS match (postal code only)",
	"A" => "Partial AVS match (address only)",
	"N" => "No AVS match",
	"U" => "AVS not supported by issuer"
);

$cvv_info = array(
	"M" => "CVN match",
	"N" => "No CVN match",
	"U" => "CVN not available",
	"E" => "CVN error"
);

$goid = func_array_path($parsed, $type."/GOOGLE-ORDER-NUMBER/0/#");

$is_exit = false;
$skey = false;

if ($type == 'NEW-ORDER-NOTIFICATION') {

	# new-order-notification message
	# Step 1: callback from Google

	$skey = func_array_path($parsed, $type."/SHOPPING-CART/MERCHANT-PRIVATE-DATA/MERCHANT-NOTE/0/#");
	$financial_os = func_array_path($parsed, $type."/FINANCIAL-ORDER-STATE/0/#");
	$total_cost = func_array_path($parsed, $type."/ORDER-TOTAL/0/#");

	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'") == 0) {
		x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with a wrong Google order id.", true);
		exit;
	}

	$trstat = func_query_first_cell("SELECT trstat FROM $sql_tbl[cc_pp3_data] WHERE ref = '$skey'");
	if ($financial_os != 'REVIEWING' || !preg_match("/^GO|/", $trstat)) {
		$bill_output['code'] = 2;
		$bill_output['mess'] = "Inner error";

		$is_exit = true;

	} else {
		# Create duplicate record in cc_pp3_data table with reference id = google order number id
		# 'trstat' field	- current google order status
		func_array2insert(
			"cc_pp3_data",
			array(
				"ref" => $goid,
				"sessionid" => $XCARTSESSID,
				"trstat" => $financial_os,
				"param1" => $skey,
				"param2" => $total_cost
			),
			true
		);

	}

} elseif ($type == 'ORDER-STATE-CHANGE-NOTIFICATION') {

	# order-state-change-notification
	# Step 2: callback from Google (status = CHARGEABLE)
	# Step 5: callback from Google (status = CHARGING)
	# Step 6: callback from Google (status = CHARGED)
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'") == 0) {
		x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with a wrong Google order id.", true);
		exit;
	}

	$new_financial_os = func_array_path($parsed, $type."/NEW-FINANCIAL-ORDER-STATE/0/#");
	$prev_financial_os = func_array_path($parsed, $type."/PREVIOUS-FINANCIAL-ORDER-STATE/0/#");

	if (!in_array($new_financial_os, array('CHARGEABLE', 'CHARGING', 'CHARGED'))) {
		$bill_output['code'] = 2;
		$bill_output['mess'] = "Declined. Transaction status: $new_financial_os; Google order id: $goid; Previous transaction status: $prev_financial_os; Reason: ".func_array_path($parsed, $type."/REASON/0/#");

		$is_exit = true;

	} else {

		# Save new status
		func_array2update(
			"cc_pp3_data",
			array(
				"trstat" => $new_financial_os
			),
			"ref = '$goid'"
		);
	}

} elseif ($type == 'RISK-INFORMATION-NOTIFICATION') {

	# risk-information-notification
	# Step 3: callback from Google
    if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'") == 0) {
		x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with a wrong Google order id.", true);
        exit;
	}

	$avs = func_array_path($parsed, $type."/RISK-INFORMATION/AVS-RESPONSE/0/#");
	$cvn = func_array_path($parsed, $type."/RISK-INFORMATION/CVN-RESPONSE/0/#");
	$prot = func_array_path($parsed, $type."/RISK-INFORMATION/ELIGIBLE-FOR-PROTECTION/0/#") == 'true';

	func_array2update(
		"cc_pp3_data",
		array(
			"param3" => $avs,
			"param4" => $cvn,
			"param5" => ($prot ? "Y" : "")
		),
		"ref = '$goid'"
	);

	# Step 4: send charge-order request

	$total_cost = price_format(func_query_first_cell("SELECT param2 FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'"));
	if (empty($total_cost))
		exit;

	$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="$goid">
	<amount currency="$module_params[param03]">$total_cost</amount>
</charge-order>
XML;

	$h = array( 
		"Authorization" => "Basic ".base64_encode($module_params['param01'].":".$module_params['param02']),
		"Accept" => "application/xml"
	);  

	x_load("http");

	list($a, $return) = func_https_request("POST", $xml_url, array($xml), "", "", "application/xml", "", "", "", $h);

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = func_xml_parse($return, $parse_errors, $options);

	if (empty($parsed)) {
		$bill_output['code'] = 2;
		$bill_output['mess'] = "Error: Empty server response";

		$is_exit = true;

	} elseif ($error = func_array_path($parsed, "ERROR/ERROR-MESSAGE/0/#")) {
		$bill_output['code'] = 2;
		$bill_output['mess'] = "Error: ".$error;

		$is_exit = true;
	}

	
} elseif ($type == 'CHARGE-AMOUNT-NOTIFICATION') {

	# charge-amount-notification
	# Step 7: callback from Google and process order
	if (func_query_first_cell("SELECT COUNT(*) FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'") == 0) {
		x_log_flag('log_payment_processing_errors', 'PAYMENTS', "Google checkout payment module: Script called with a wrong Google order id.", true);
		exit;
	}

	$ret = func_query_first("SELECT trstat, param3, param4, param5 FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");
	$bill_output['code'] = 1;
	$bill_output['mess'] = "Accepted. Transaction status: $ret[trstat]; Google order id: $goid";

	if ($ret['param3'])
		$bill_output['avsmes'] = "Code ".$ret['param3'].($avs_info[$ret['param3']] ? ": ".$avs_info[$ret['param3']] : "");

	if ($ret['param4'])
		$bill_output['cvvmes'] = "Code ".$ret['param4'].($cvv_info[$ret['param4']] ? ": ".$cvv_info[$ret['param4']] : "");

	$is_exit = true;

}

# Send response data
?><xml version="1.0" encoding="UTF-8"?>
<notification-acknowledgment xmlns="http://checkout.google.com/schema/2"/>
<?php

if ($is_exit) {

	# Get original reference id
	if (empty($skey))
		$skey = func_query_first_cell("SELECT param1 FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

    # Delete manual-created record in cc_pp3_data table
    db_query("DELETE FROM $sql_tbl[cc_pp3_data] WHERE ref = '$goid'");

	require $xcart_dir."/payment/payment_ccmid.php";
	require $xcart_dir."/payment/payment_ccwebset.php";
}

exit;
?>
